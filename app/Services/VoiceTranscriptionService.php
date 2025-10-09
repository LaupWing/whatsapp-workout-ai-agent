<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI;

class VoiceTranscriptionService
{
    private $client;
    private string $model;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.api_key'));
        $this->model = config('services.openai.whisper_model');
    }

    /**
     * Transcribe audio file to text using OpenAI Whisper
     *
     * @param string $audioFilePath Path to the audio file
     * @return string|null Transcribed text or null on failure
     */
    public function transcribe(string $audioFilePath): ?string
    {
        try {
            if (!file_exists($audioFilePath)) {
                Log::error('Voice transcription: file not found', [
                    'path' => $audioFilePath,
                ]);
                return null;
            }

            Log::info('Starting voice transcription', [
                'file' => $audioFilePath,
                'size' => filesize($audioFilePath),
            ]);

            $response = $this->client->audio()->transcribe([
                'model' => $this->model,
                'file' => fopen($audioFilePath, 'r'),
                'response_format' => 'json',
                'language' => 'en', // You can make this configurable or auto-detect
            ]);

            $transcribedText = $response->text ?? null;

            Log::info('Voice transcription completed', [
                'success' => !empty($transcribedText),
                'text_length' => strlen($transcribedText ?? ''),
            ]);

            // Clean up the temporary file
            $this->cleanupTempFile($audioFilePath);

            return $transcribedText;
        } catch (\Exception $e) {
            Log::error('Voice transcription error', [
                'file' => $audioFilePath,
                'error' => $e->getMessage(),
            ]);

            // Clean up even on error
            $this->cleanupTempFile($audioFilePath);

            return null;
        }
    }

    /**
     * Delete temporary audio file
     */
    private function cleanupTempFile(string $filePath): void
    {
        try {
            if (file_exists($filePath)) {
                unlink($filePath);
                Log::info('Cleaned up temp audio file', ['path' => $filePath]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup temp file', [
                'path' => $filePath,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
