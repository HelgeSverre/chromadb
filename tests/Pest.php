<?php

use HelgeSverre\Chromadb\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function createTestVector(float $value = 0.1, int $dimension = 128): array
{
    return array_fill(0, $dimension, $value);
}

// Mock response helpers for embedding tests
function mockOpenAIResponse(int $count = 1, int $dimensions = 1536): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.1 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'text-embedding-3-small',
        'usage' => [
            'prompt_tokens' => 10 * $count,
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockVoyageResponse(int $count = 1, int $dimensions = 1024): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.2 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'voyage-3.5',
        'usage' => [
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockMistralResponse(int $count = 1, int $dimensions = 1024): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.3 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'mistral-embed',
        'usage' => [
            'prompt_tokens' => 10 * $count,
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockJinaResponse(int $count = 1, int $dimensions = 1024): array
{
    $data = [];
    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'embedding' => createTestVector(0.5 + ($i * 0.01), $dimensions),
            'index' => $i,
        ];
    }

    return [
        'data' => $data,
        'model' => 'jina-embeddings-v3',
        'usage' => [
            'prompt_tokens' => 10 * $count,
            'total_tokens' => 10 * $count,
        ],
    ];
}

function mockOllamaResponse(int $dimensions = 384): array
{
    return [
        'model' => 'all-minilm',
        'embeddings' => [
            createTestVector(0.4, $dimensions),
        ],
    ];
}
