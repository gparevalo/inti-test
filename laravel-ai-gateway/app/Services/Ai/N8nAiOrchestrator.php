<?php

namespace App\Services\Ai;

use App\Exceptions\AiServiceException;
use Illuminate\Support\Facades\Http;
use Throwable;

class N8nAiOrchestrator implements AiOrchestrator
{
    public function generate(
        string $tenantId,
        string $message,
        string $requestId
    ): array {
        try {
            $response = Http::timeout(15)
                ->retry(2, 200, throw: false)
                ->post(config('services.n8n.webhook_url'), [
                    'request_id' => $requestId,
                    'tenant_id' => $tenantId,
                    'message' => $message,
                ]);

            if ($response->failed()) {
                throw new AiServiceException(
                    'AI provider returned an error.'
                );
            }

            return $response->json();

        } catch (AiServiceException $exception) {
            throw $exception;

        } catch (Throwable $exception) {
            report($exception);

            throw new AiServiceException(
                'AI provider is unavailable.',
                previous: $exception
            );
        }
    }
}