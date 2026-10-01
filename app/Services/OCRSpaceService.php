<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class OCRSpaceService
{
    public function processInvoice($filePath, $originalName = null)
    {
        $apiKey = env('OCR_SPACE_API_KEY');

        if (!$apiKey) {
            throw new Exception('OCR_SPACE_API_KEY is missing from .env');
        }

        $originalName = $originalName ?: basename($filePath);

        $response = Http::timeout(120)
            ->withHeaders([
                'apikey' => $apiKey,
            ])
            ->attach(
                'file',
                file_get_contents($filePath),
                $originalName
            )
            ->post('https://api.ocr.space/parse/image', [
                'language' => 'eng',
                'isTable' => 'true',
                'detectOrientation' => 'true',
                'scale' => 'true',
                'OCREngine' => '2',
            ]);

        if (!$response->successful()) {
            throw new Exception(
                'OCR.space HTTP error: ' . $response->body()
            );
        }

        $result = $response->json();

        if (($result['IsErroredOnProcessing'] ?? false) === true) {
            $errorMessage = $result['ErrorMessage']
                ?? $result['ErrorDetails']
                ?? 'Unknown OCR.space error';

            if (is_array($errorMessage)) {
                $errorMessage = implode(', ', $errorMessage);
            }

            throw new Exception(
                'OCR.space processing failed: ' . $errorMessage
            );
        }

        $text = '';

        foreach ($result['ParsedResults'] ?? [] as $page) {
            $text .= ($page['ParsedText'] ?? '') . "\n";
        }

        $text = trim($text);

        if ($text === '') {
            throw new Exception(
                'OCR.space returned no text. Please upload a clearer bill.'
            );
        }

        return [
            'text' => $text,
            'raw' => $result,
        ];
    }
}