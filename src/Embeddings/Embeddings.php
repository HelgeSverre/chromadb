<?php

namespace HelgeSverre\Chromadb\Embeddings;

use InvalidArgumentException;

/**
 * Factory class for creating embedding function instances.
 *
 * Provides convenient static methods for instantiating embedding providers
 * and supports Laravel configuration integration.
 */
class Embeddings
{
    /**
     * Create an OpenAI embeddings instance.
     *
     * @param  string  $apiKey  Your OpenAI API key
     * @param  string  $model  The embedding model to use
     * @param  int|null  $dimensions  Optional dimension reduction
     * @param  string|null  $organization  Optional OpenAI organization ID
     */
    public static function openai(
        string $apiKey,
        string $model = 'text-embedding-3-small',
        ?int $dimensions = null,
        ?string $organization = null
    ): OpenAIEmbeddings {
        return new OpenAIEmbeddings($apiKey, $model, $dimensions, $organization);
    }

    /**
     * Create a VoyageAI embeddings instance.
     *
     * @param  string  $apiKey  Your VoyageAI API key
     * @param  string  $model  The embedding model to use
     * @param  string  $inputType  Input type: 'document' or 'query'
     */
    public static function voyage(
        string $apiKey,
        string $model = 'voyage-3.5',
        string $inputType = 'document'
    ): VoyageAIEmbeddings {
        return new VoyageAIEmbeddings($apiKey, $model, $inputType);
    }

    /**
     * Create a Mistral embeddings instance.
     *
     * @param  string  $apiKey  Your Mistral API key
     * @param  string  $model  The embedding model to use
     */
    public static function mistral(
        string $apiKey,
        string $model = 'mistral-embed'
    ): MistralEmbeddings {
        return new MistralEmbeddings($apiKey, $model);
    }

    /**
     * Create an Ollama embeddings instance (local).
     *
     * @param  string  $model  The embedding model to use
     * @param  string  $baseUrl  The Ollama API base URL
     */
    public static function ollama(
        string $model = 'all-minilm',
        string $baseUrl = 'http://localhost:11434'
    ): OllamaEmbeddings {
        return new OllamaEmbeddings($model, $baseUrl);
    }

    /**
     * Create a Jina AI embeddings instance.
     *
     * @param  string  $apiKey  Your Jina AI API key
     * @param  string  $model  The embedding model to use
     * @param  string|null  $task  Optional task type (e.g., 'retrieval.query', 'retrieval.passage')
     * @param  int|null  $dimensions  Optional dimension reduction
     */
    public static function jina(
        string $apiKey,
        string $model = 'jina-embeddings-v3',
        ?string $task = null,
        ?int $dimensions = null
    ): JinaEmbeddings {
        return new JinaEmbeddings($apiKey, $model, $task, $dimensions);
    }

    /**
     * Create an embedding function from Laravel configuration.
     *
     * Requires the chromadb.embeddings config to be set up.
     *
     * @param  string|null  $provider  The provider name (openai, voyage, mistral, jina, ollama) or null for default
     *
     * @TODO:  maybe bane thius factory()?
     *
     * @throws InvalidArgumentException
     */
    public static function fromConfig(?string $provider = null): EmbeddingFunction
    {
        // Check if running in Laravel context
        if (! function_exists('config')) {
            throw new InvalidArgumentException(
                'fromConfig() requires Laravel. Use explicit constructor methods instead.'
            );
        }

        $provider = $provider ?? config('chromadb.embeddings.default', 'openai');

        $config = config("chromadb.embeddings.providers.{$provider}");

        if (! $config) {
            throw new InvalidArgumentException("No configuration found for embedding provider: {$provider}");
        }

        return match ($provider) {
            'openai' => new OpenAIEmbeddings(
                apiKey: $config['api_key'] ?? throw new InvalidArgumentException('OpenAI API key not configured'),
                model: $config['model'] ?? 'text-embedding-3-small',
                dimensions: $config['dimensions'] ?? null,
                organization: $config['organization'] ?? null
            ),
            'voyage' => new VoyageAIEmbeddings(
                apiKey: $config['api_key'] ?? throw new InvalidArgumentException('VoyageAI API key not configured'),
                model: $config['model'] ?? 'voyage-3.5',
                inputType: $config['input_type'] ?? 'document'
            ),
            'mistral' => new MistralEmbeddings(
                apiKey: $config['api_key'] ?? throw new InvalidArgumentException('Mistral API key not configured'),
                model: $config['model'] ?? 'mistral-embed'
            ),
            'jina' => new JinaEmbeddings(
                apiKey: $config['api_key'] ?? throw new InvalidArgumentException('Jina AI API key not configured'),
                model: $config['model'] ?? 'jina-embeddings-v3',
                task: $config['task'] ?? null,
                dimensions: $config['dimensions'] ?? null
            ),
            'ollama' => new OllamaEmbeddings(
                model: $config['model'] ?? 'all-minilm',
                baseUrl: $config['base_url'] ?? 'http://localhost:11434'
            ),
            default => throw new InvalidArgumentException("Unknown embedding provider: {$provider}")
        };
    }
}
