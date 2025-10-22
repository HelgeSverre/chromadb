<?php

namespace HelgeSverre\Chromadb\Embeddings;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;
use SensitiveParameter;

/**
 * OpenAI Embeddings provider.
 *
 * Supports all OpenAI embedding models including:
 * - text-embedding-3-small (1536 dims, efficient)
 * - text-embedding-3-large (3072 dims, best quality)
 * - text-embedding-ada-002 (1536 dims, legacy)
 *
 * @see https://platform.openai.com/docs/api-reference/embeddings
 */
class OpenAIEmbeddings implements EmbeddingFunction
{
    private Client $client;

    /**
     * Create a new OpenAI embeddings instance.
     *
     * @param  string  $apiKey  Your OpenAI API key
     * @param  string  $model  The embedding model to use (default: text-embedding-3-small)
     * @param  int|null  $dimensions  Optional dimension reduction (text-embedding-3 models only)
     * @param  string|null  $organization  Optional OpenAI organization ID
     */
    public function __construct(
        #[SensitiveParameter]
        private readonly string $apiKey,
        private readonly string $model = 'text-embedding-3-small',
        private readonly ?int $dimensions = null,
        private readonly ?string $organization = null
    ) {
        $headers = [
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ];

        if ($this->organization) {
            $headers['OpenAI-Organization'] = $this->organization;
        }

        $this->client = HttpClient::create([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => $headers,
            'timeout' => 30,
        ]);
    }

    /**
     * Generate embeddings for the given texts using OpenAI's API.
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

            if ($this->dimensions !== null) {
                $body['dimensions'] = $this->dimensions;
            }

            $response = $this->client->post('embeddings', [
                'json' => $body,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (! isset($result['data']) || ! is_array($result['data'])) {
                throw EmbeddingException::invalidResponse('OpenAI', 'Missing or invalid "data" field');
            }

            $embeddings = $result['data'];

            // Sort by index to maintain original order
            usort($embeddings, fn ($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));

            // Extract just the embedding arrays
            return array_map(fn ($item) => $item['embedding'], $embeddings);
        } catch (GuzzleException $e) {
            // Extract detailed error message from response body if available
            $errorDetail = null;
            if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $json = json_decode($body, true);
                // OpenAI uses 'error.message' structure
                $errorDetail = $json['error']['message'] ?? $json['detail'] ?? null;
            }

            // Check for specific HTTP status codes
            if ($e->getCode() === 401 || $e->getCode() === 403) {
                throw EmbeddingException::authenticationFailed('OpenAI');
            }

            if ($e->getCode() === 429) {
                throw EmbeddingException::rateLimitExceeded('OpenAI', 0, $errorDetail);
            }

            throw EmbeddingException::apiError('OpenAI', $errorDetail ?? $e->getMessage(), $e);
        }
    }
}
