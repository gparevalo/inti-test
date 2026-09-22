<?php

namespace App\Services;

use App\Services\Ai\AiOrchestrator;

class ChatService
{
    public function __construct(
        private readonly AiOrchestrator $aiOrchestrator
    ) {}

    public function handle(
        string $tenantId,
        string $message,
        string $requestId
    ): array {
        return $this->aiOrchestrator->generate(
            tenantId: $tenantId,
            message: $message,
            requestId: $requestId,
        );
    }
}