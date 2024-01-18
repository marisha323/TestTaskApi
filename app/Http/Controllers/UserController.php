<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
class UserController extends Controller
{
    public function showForm()
    {
        return view('index');
    }


    public function processForm(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'gender' => 'required|in:male,female',
            'age' => 'required|integer|min:0',
            'audio_url' => 'required|url',
        ]);

        $apiUrl = "https://neuron-systems.com/api/test";
        //$apiUrl = "http://127.0.0.1:8000/api/test";
        $response = Http::post($apiUrl,$validatedData);

        // Путь к файлу лога
        $logFilePath = storage_path("logs/{$validatedData['name']}_api.log");

        // Создать файл лога, если он не существует
        if (!file_exists($logFilePath)) {
            file_put_contents($logFilePath, '');
        }



        // Обработка ответа от API
        if ($response->successful()) {
            $apiData = $response->json();

            // Проверка на наличие данных в ответе
            if (!empty($apiData)) {
                // Успешно полученные данные записать в лог в отдельный файл
                $logContent = "API Response for {$validatedData['name']}: " . json_encode($apiData) . PHP_EOL;

                // Добавить данные в файл лога
                file_put_contents($logFilePath, $logContent, FILE_APPEND);

                Log::channel('api')->info($logContent);
            } else {
                // Обработка случая, когда ответ пустой
                $logContent = "API Response for {$validatedData['name']} is empty." . PHP_EOL;

                // Добавить информацию в файл лога
                file_put_contents($logFilePath, $logContent, FILE_APPEND);

                Log::channel('api')->warning($logContent);
            }
        } else {
            // Обработка ошибок при взаимодействии с API
            $errorLog = "Error while sending data to API. HTTP Status: {$response->status()}" . PHP_EOL;

            // Добавить ошибку в файл лога
            file_put_contents($logFilePath, $errorLog, FILE_APPEND);

            Log::error($errorLog);
        }
        // Получить данные из файла лога
        $logData = File::exists($logFilePath) ? File::get($logFilePath) : null;
        $welcomeMessage = $this->getWelcomeMessage($validatedData);
        $ageCategory = $this->getAgeCategory($validatedData['age']);
        $audioFileName = $this->downloadAndSaveAudio($validatedData['audio_url'], $validatedData['name']);
        return view('result', [
            'welcomeMessage' => $welcomeMessage,
            'ageCategory' => $ageCategory,
            'gender' => $validatedData['gender'],
            'age' => $validatedData['age'],
            'logData' => $logData,
            'audioFileName' => $audioFileName,
        ]);
    }
    private function downloadAndSaveAudio($audioUrl, $name)
    {
        $client = new Client();

        // Отримайте аудіозапис та збережіть його
        $response = $client->get($audioUrl);

        // Отримайте інформацію про файл із заголовка відповіді
        $filename = $response->getHeader('Content-Disposition')[0] ?? null;

        // Якщо ім'я файлу відсутнє, використовуйте унікальне ім'я на основі ім'я користувача
        if (!$filename) {
            $extension = pathinfo($audioUrl, PATHINFO_EXTENSION);
            $filename = $name . '_' . uniqid() . '.' . $extension;
        }

        // Визначте каталог для збереження аудіозаписів у сховищі Laravel
        $audioDirectory = 'audio';

        // Отримайте реальний шлях до потоку
        $streamMeta = stream_get_meta_data($response->getBody()->detach());
        $streamPath = $streamMeta['uri'];

        // Збережіть аудіозапис в сховище Laravel
        $path = Storage::disk('public')->putFileAs($audioDirectory, $streamPath, $filename);

        // Поверніть ім'я файлу або null у випадку невдачі
        return  $filename;
    }
    private function getWelcomeMessage($data)
    {
        $genderSalutation = ($data['gender'] == 'male') ? 'Mr.' : 'Ms.';
        return "Hello, $genderSalutation {$data['name']}!";
    }

    private function getAgeCategory($age)
    {
        if ($age < 18) {
            return 'You are in the Young category.';
        } elseif ($age >= 18 && $age < 40) {
            return 'You are in the Adult category.';
        } else {
            return 'You are in the Senior category.';
        }
    }
}
