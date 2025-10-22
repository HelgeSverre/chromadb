<?php

namespace HelgeSverre\Chromadb\Embeddings;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;
use SensitiveParameter;

/**
 * Mistral AI Embeddings provider.
 *
 * Supports Mistral's embedding model:
 * - mistral-embed (1024 dimensions)
 *
 * @see https://docs.mistral.ai/capabilities/embeddings/
 */
class MistralEmbeddings implements EmbeddingFunction
{
    private Client $client;

    /**
     * Create a new Mistral embeddings instance.
     *
     * @param  string  $apiKey  Your Mistral API key
     * @param  string  $model  The embedding model to use (default: mistral-embed)
     */
    public function __construct(
        #[SensitiveParameter]
        private readonly string $apiKey,
        private readonly string $model = 'mistral-embed'
    ) {
        $this->client = HttpClient::create([
            'base_uri' => 'https://api.mistral.ai/v1/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Generate embeddings for the given texts using Mistral's API.
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
            $response = $this->client->post('embeddings', [
                'json' => [
                    'model' => $this->model,
                    'input' => $texts,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (! isset($result['data']) || ! is_array($result['data'])) {
                throw EmbeddingException::invalidResponse('Mistral', 'Missing or invalid "data" field');
            }

            $embeddings = $result['data'];

            // Sort by index to maintain original order
            usort($embeddings, fn ($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));

            // Extract just the embedding arrays
            return array_map(fn ($item) => $item['embedding'], $embeddings);
        } catch (GuzzleException $e) {
            // Check for specific HTTP status codes
            if ($e->getCode() === 401 || $e->getCode() === 403) {
                throw EmbeddingException::authenticationFailed('Mistral');
            }

            if ($e->getCode() === 429) {
                throw EmbeddingException::rateLimitExceeded('Mistral');
            }

            throw EmbeddingException::apiError('Mistral', $e->getMessage(), $e);
        }
    }
}
