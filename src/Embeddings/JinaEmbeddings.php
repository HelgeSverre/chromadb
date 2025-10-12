<?php

namespace HelgeSverre\Chromadb\Embeddings;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;
use SensitiveParameter;

/**
 * Jina AI Embeddings provider.
 *
 * Supports Jina AI's embedding models including:
 * - jina-embeddings-v3 (570M, 1024 dims, multilingual)
 * - jina-embeddings-v2-base-en (137M, 768 dims, English)
 * - jina-embeddings-v2-small-en (33M, 512 dims, English)
 * - jina-clip-v2 (885M, 1024 dims, multimodal)
 *
 * @see https://jina.ai/embeddings/
 * @see https://docs.jina.ai/
 */
class JinaEmbeddings implements EmbeddingFunction
{
    private Client $client;

    /**
     * Create a new Jina AI embeddings instance.
     *
     * @param  string  $apiKey  Your Jina AI API key
     * @param  string  $model  The embedding model to use (default: jina-embeddings-v3)
     * @param  string|null  $task  Optional task type (e.g., 'retrieval.query', 'retrieval.passage', 'text-matching')
     * @param  int|null  $dimensions  Optional dimension reduction (for supported models)
     */
    public function __construct(
        #[SensitiveParameter]
        private readonly string $apiKey,
        private readonly string $model = 'jina-embeddings-v3',
        private readonly ?string $task = null,
        private readonly ?int $dimensions = null
    ) {
        $this->client = HttpClient::create([
            'base_uri' => 'https://api.jina.ai/v1/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Generate embeddings for the given texts using Jina AI's API.
     *
     * @param  array<string>  $texts  The texts to embed
     * @return array<array<float>> The embedding vectors
     *
     * @throws EmbeddingException
     */
    public function generate(array $texts): array
    {
        if (empty($texts)) {
            throw EmbeddingException::emptyInput();
        }

        try {
            $body = [
                'model' => $this->model,
                'input' => $texts,
            ];

            if ($this->task !== null) {
                $body['task'] = $this->task;
            }

            if ($this->dimensions !== null) {
                $body['dimensions'] = $this->dimensions;
            }

            $response = $this->client->post('embeddings', [
                'json' => $body,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (! isset($result['data']) || ! is_array($result['data'])) {
                throw EmbeddingException::invalidResponse('Jina AI', 'Missing or invalid "data" field');
            }

            $embeddings = $result['data'];

            // Sort by index to maintain original order
            usort($embeddings, fn ($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));

            // Extract just the embedding arrays
            return array_map(fn ($item) => $item['embedding'], $embeddings);
        } catch (GuzzleException $e) {
            // Check for specific HTTP status codes
            if ($e->getCode() === 401 || $e->getCode() === 403) {
                throw EmbeddingException::authenticationFailed('Jina AI');
            }

            if ($e->getCode() === 429) {
                throw EmbeddingException::rateLimitExceeded('Jina AI');
            }

            throw EmbeddingException::apiError('Jina AI', $e->getMessage(), $e);
        }
    }
}
