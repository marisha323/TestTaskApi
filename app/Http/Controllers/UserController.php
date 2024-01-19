<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
class UserController extends Controller
{
    public function showForm()
    {
        return view('index');
    }

    public function getHtmlFromUrl($url)
    {
        $client = new Client();
        try {
            $response = $client->get($url);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return null;
        }
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
        $logFilePath = storage_path("logs/{$validatedData['name']}_api.log");
        if (!file_exists($logFilePath)) {
            file_put_contents($logFilePath, '');
        }
        if ($response->successful()) {
            $apiData = $response->json();
            if (!empty($apiData)) {
                $logContent = "API Response for {$validatedData['name']}: " . json_encode($apiData) . PHP_EOL;
                file_put_contents($logFilePath, $logContent, FILE_APPEND);
                Log::channel('api')->info($logContent);
            } else {
                $logContent = "API Response for {$validatedData['name']} is empty." . PHP_EOL;
                file_put_contents($logFilePath, $logContent, FILE_APPEND);
                Log::channel('api')->warning($logContent);
            }
        } else {
            $errorLog = "Error while sending data to API. HTTP Status: {$response->status()}" . PHP_EOL;
            file_put_contents($logFilePath, $errorLog, FILE_APPEND);
            Log::error($errorLog);
        }
        $logData = File::exists($logFilePath) ? File::get($logFilePath) : null;
        $welcomeMessage = $this->getWelcomeMessage($validatedData);
        $ageCategory = $this->getAgeCategory($validatedData['age']);
        $html = $this->getHtmlFromUrl($validatedData['audio_url']);
        if ($html) {
            $crawler = new Crawler($html);
            $track = $crawler->filter('.loadbtnjs')->first()->attr('data-file-track');
            if ($track) {
                $url = 'https://muzflix.net/' . $track;
            } else {
                echo "Трек не знайдено";
            }
        } else {
            return redirect()->back()->withErrors(['url' => 'Не вдалося отримати HTML-код сторінки']);
        }
        $audioFileName=$this->downloadAndSaveAudio($url, $validatedData['name']);;
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
        $response = $client->get($audioUrl);
        $filename ='';
        if (!$filename) {
            $filename = $name .'_' . uniqid() .'.mp3';
        } else {
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.mp3';
        }
        $audioDirectory = 'public\audio';
        try {
            $filePath = $audioDirectory . '/' . $filename;
            Storage::put($filePath, $response->getBody()->getContents());
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error saving file: ' . $e->getMessage());
            return false;
        }
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
