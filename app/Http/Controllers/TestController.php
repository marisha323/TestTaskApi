<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function handler(Request $request)
    {
        //dd($request->all());
        $postData = $request->input('post', []);
        $name = $postData['name'];
        $gender = $postData['gender'];
        $age = $postData['age'];
        $audioUrl = $postData['audio_url'];

        $audioFileName = $this->downloadAndSaveAudio($audioUrl, $name);
        $logFilePath = storage_path("logs/{$name}.log");
        if (!file_exists($logFilePath)) {
            file_put_contents($logFilePath, '');
        }
        $logContent = "Name: {$name} Gender: {$gender} Age: {$age} AudioUrl: {$audioUrl} AudioPath: {$audioFileName}". PHP_EOL;
        file_put_contents($logFilePath, $logContent, FILE_APPEND);
        Log::channel('api')->info($logContent);
        $logData = File::exists($logFilePath) ? File::get($logFilePath) : null;
        return view('result', [
            'audioFileName' => $audioFileName,
            'logData'=>$logData
        ]);
    }

    private function downloadAndSaveAudio($audioUrl, $name)
    {
        $client = new Client();
        $response = $client->get($audioUrl);
        $filename = '';
        if (!$filename) {
            $filename = $name . '_' . uniqid() . '.mp3';
        } else {
            $filename = pathinfo($filename, PATHINFO_FILENAME) . '.mp3';
        }
        $audioDirectory = 'public/audio';
        try {
            $filePath = $audioDirectory . '/' . $filename;
            Storage::put($filePath, $response->getBody()->getContents());
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error saving file: ' . $e->getMessage());
            return false;
        }
    }
}
