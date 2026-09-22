<?php

namespace Tests\Feature;

use App\Services\Ai\AiOrchestrator;
use Tests\TestCase;
use App\Exceptions\AiServiceException;

class ChatTest extends TestCase
{
    public function test_chat_returns_ai_response(): void
    {
        $this->mock(AiOrchestrator::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->withArgs(function (string $tenantId, string $message, string $requestId) {
                    return $tenantId === 'nova'
                        && $message === '¿Cuánto cuesta el Plan Fitness Pro?'
                        && !empty($requestId);
                })
                ->andReturn([
                    'request_id' => 'test-request-id',
                    'tenant_id' => 'nova',
                    'answer' => 'El Plan Fitness Pro cuesta $29 al mes.',
                    'provider' => 'n8n',
                    'fallback' => false,
                ]);
        });

        $response = $this->postJson('/api/v1/chat', [
            'tenant_id' => 'nova',
            'message' => '¿Cuánto cuesta el Plan Fitness Pro?',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'tenant_id' => 'nova',
                'answer' => 'El Plan Fitness Pro cuesta $29 al mes.',
                'provider' => 'n8n',
                'fallback' => false,
            ])
            ->assertJsonStructure([
                'request_id',
                'tenant_id',
                'answer',
                'provider',
                'fallback',
            ]);
    }

    public function test_tenant_id_is_required(): void
    {
        $response = $this->postJson('/api/v1/chat', [
            'message' => '¿Cuánto cuesta el Plan Fitness Pro?',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['tenant_id']);
    }

    public function test_message_cannot_exceed_500_characters(): void
    {
        $response = $this->postJson('/api/v1/chat', [
            'tenant_id' => 'nova',
            'message' => str_repeat('a', 501),
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_ai_service_failure_returns_503(): void
    {
        $this->mock(AiOrchestrator::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andThrow(new AiServiceException('AI provider is unavailable.'));
        });

        $response = $this->postJson('/api/v1/chat', [
            'tenant_id' => 'nova',
            'message' => '¿Cuánto cuesta el Plan Fitness Pro?',
        ]);

        $response
            ->assertStatus(503)
            ->assertJson([
                'error' => 'ai_service_unavailable',
                'message' => 'No fue posible procesar tu solicitud en este momento.',
            ])
            ->assertJsonStructure([
                'request_id',
                'error',
                'message',
            ]);
    }

    public function test_chat_is_rate_limited(): void
    {
        $this->mock(AiOrchestrator::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->times(20)
                ->andReturn([
                    'request_id' => 'test-request-id',
                    'tenant_id' => 'nova',
                    'answer' => 'Respuesta de prueba.',
                    'provider' => 'n8n',
                    'fallback' => false,
                ]);
        });

        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/api/v1/chat', [
                'tenant_id' => 'nova',
                'message' => 'Mensaje de prueba',
            ])->assertStatus(200);
        }

        $this->postJson('/api/v1/chat', [
            'tenant_id' => 'nova',
            'message' => 'Mensaje número 21',
        ])->assertStatus(429);
    }

    public function test_chat_uses_the_requested_tenant(): void
    {
        $this->mock(AiOrchestrator::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->withArgs(function (string $tenantId, string $message, string $requestId) {
                    return $tenantId === 'acme'
                        && $message === '¿Cuánto demora el envío?'
                        && !empty($requestId);
                })
                ->andReturn([
                    'request_id' => 'test-request-id',
                    'tenant_id' => 'acme',
                    'answer' => 'Los pedidos se entregan entre 2 y 3 días laborables.',
                    'provider' => 'n8n',
                    'fallback' => false,
                ]);
        });

        $response = $this->postJson('/api/v1/chat', [
            'tenant_id' => 'acme',
            'message' => '¿Cuánto demora el envío?',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'tenant_id' => 'acme',
            ]);
    }
}