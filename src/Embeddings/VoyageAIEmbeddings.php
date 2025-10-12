<?php

namespace HelgeSverre\Chromadb\Embeddings;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HelgeSverre\Chromadb\Exceptions\EmbeddingException;
use SensitiveParameter;

/**
 * VoyageAI Embeddings provider.
 *
 * Supports VoyageAI's embedding models including:
 * - voyage-3.5 (efficient, 8M TPM)
 * - voyage-3-large (best quality)
 * - voyage-code-3 (optimized for code)
 * - voyage-finance-2 (financial domain)
 * - voyage-law-2 (legal domain)
 *
 * @see https://docs.voyageai.com/docs/embeddings
 */
class VoyageAIEmbeddings implements EmbeddingFunction
{
    private Client $client;

    /**
     * Create a new VoyageAI embeddings instance.
     *
     * @param  string  $apiKey  Your VoyageAI API key
     * @param  string  $model  The embedding model to use (default: voyage-3.5)
     * @param  string  $inputType  Input type: 'document' or 'query' (default: document)
     */
    public function __construct(
        #[SensitiveParameter]
        private readonly string $apiKey,
        private readonly string $model = 'voyage-3.5',
        private readonly string $inputType = 'document'
    ) {
        $this->client = HttpClient::create([
            'base_uri' => 'https://api.voyageai.com/v1/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Generate embeddings for the given texts using VoyageAI's API.
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
                    'input_type' => $this->inputType,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (! isset($result['data']) || ! is_array($result['data'])) {
                throw EmbeddingException::invalidResponse('VoyageAI', 'Missing or invalid "data" field');
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
                $errorDetail = $json['detail'] ?? null;
            }

            // Check for specific HTTP status codes
            if ($e->getCode() === 401 || $e->getCode() === 403) {
                throw EmbeddingException::authenticationFailed('VoyageAI');
            }

            if ($e->getCode() === 429) {
                throw EmbeddingException::rateLimitExceeded('VoyageAI', 0, $errorDetail);
            }

            throw EmbeddingException::apiError('VoyageAI', $errorDetail ?? $e->getMessage(), $e);
        }
    }
}
