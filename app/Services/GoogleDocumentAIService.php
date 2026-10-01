<?php

namespace App\Services;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Google\Cloud\DocumentAI\V1\RawDocument;

class GoogleDocumentAIService
{
    public function processInvoice($filePath, $mimeType)
    {
        putenv(
            'GOOGLE_APPLICATION_CREDENTIALS=' .
            base_path(env('GOOGLE_APPLICATION_CREDENTIALS'))
        );

        $client = new DocumentProcessorServiceClient();

        $name = $client->processorName(
            env('DOCUMENT_AI_PROJECT_ID'),
            env('DOCUMENT_AI_LOCATION'),
            env('DOCUMENT_AI_PROCESSOR_ID')
        );

        $content = file_get_contents($filePath);

        $rawDocument = new RawDocument([
            'content' => $content,
            'mime_type' => $mimeType,
        ]);

        $request = new ProcessRequest([
            'name' => $name,
            'raw_document' => $rawDocument,
        ]);

        $result = $client->processDocument($request);

        return $result->getDocument();
    }
}