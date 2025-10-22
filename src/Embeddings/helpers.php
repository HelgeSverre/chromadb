<?php

namespace HelgeSverre\Chromadb\Embeddings;

/**
 * Generate embeddings using OpenAI's API.
 *
 * @param  array<string>  $texts  The texts to embed
 * @param  string  $apiKey  Your OpenAI API key
 * @param  string  $model  The embedding model to use
 * @param  int|null  $dimensions  Optional dimension reduction (text-embedding-3 models only)
 * @return array<array<float>> The embedding vectors
 *
 * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
 */
function embeddings_openai(
    array $texts,
    string $apiKey,
    string $model = 'text-embedding-3-small',
    ?int $dimensions = null
): array {
    return (new OpenAIEmbeddings($apiKey, $model, $dimensions))->generate($texts);
}

/**
 * Generate embeddings using VoyageAI's API.
 *
 * @param  array<string>  $texts  The texts to embed
 * @param  string  $apiKey  Your VoyageAI API key
 * @param  string  $model  The embedding model to use
 * @param  string  $inputType  Input type: 'document' or 'query'
 * @return array<array<float>> The embedding vectors
 *
 * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
 */
function embeddings_voyage(
    array $texts,
    string $apiKey,
    string $model = 'voyage-3.5',
    string $inputType = 'document'
): array {
    return (new VoyageAIEmbeddings($apiKey, $model, $inputType))->generate($texts);
}

/**
 * Generate embeddings using Mistral's API.
 *
 * @param  array<string>  $texts  The texts to embed
 * @param  string  $apiKey  Your Mistral API key
 * @param  string  $model  The embedding model to use
 * @return array<array<float>> The embedding vectors
 *
 * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
 */
function embeddings_mistral(
    array $texts,
    string $apiKey,
    string $model = 'mistral-embed'
): array {
    return (new MistralEmbeddings($apiKey, $model))->generate($texts);
}

/**
 * Generate embeddings using Jina AI's API.
 *
 * @param  array<string>  $texts  The texts to embed
 * @param  string  $apiKey  Your Jina AI API key
 * @param  string  $model  The embedding model to use
 * @param  string|null  $task  Optional task type (e.g., 'retrieval.query', 'retrieval.passage')
 * @return array<array<float>> The embedding vectors
 *
 * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
 */
function embeddings_jina(
    array $texts,
    string $apiKey,
    string $model = 'jina-embeddings-v3',
    ?string $task = null
): array {
    return (new JinaEmbeddings($apiKey, $model, $task))->generate($texts);
}

/**
 * Generate embeddings using Ollama (local).
 *
 * @param  array<string>  $texts  The texts to embed
 * @param  string  $model  The embedding model to use
 * @param  string  $baseUrl  The Ollama API base URL
 * @return array<array<float>> The embedding vectors
 *
 * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
 */
function embeddings_ollama(
    array $texts,
    string $model = 'all-minilm',
    string $baseUrl = 'http://localhost:11434'
): array {
    return (new OllamaEmbeddings($model, $baseUrl))->generate($texts);
}
