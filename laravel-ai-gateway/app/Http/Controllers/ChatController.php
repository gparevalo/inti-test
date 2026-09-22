<?php

namespace App\Http\Controllers;

use App\Exceptions\AiServiceException;
use App\Http\Requests\ChatRequest;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService
    ) {}

    public function __invoke(ChatRequest $request): JsonResponse
    {
        $requestId = (string) Str::uuid();

        try {
            $response = $this->chatService->handle(
                tenantId: $request->string('tenant_id')->toString(),
                message: $request->string('message')->toString(),
                requestId: $requestId,
            );

            return response()->json($response);

        } catch (AiServiceException $exception) {
            return response()->json([
                'request_id' => $requestId,
                'error' => 'ai_service_unavailable',
                'message' => 'No fue posible procesar tu solicitud en este momento.',
            ], 503);
        }
    }
}