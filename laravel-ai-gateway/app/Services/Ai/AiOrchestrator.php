<?php

namespace App\Services\Ai;

interface AiOrchestrator
{
    public function generate(
        string $tenantId,
        string $message,
        string $requestId
    ): array;
}