<?php

namespace HelgeSverre\Chromadb\Embeddings;

/**
 * Base interface for all embedding function implementations.
 *
 * Embedding functions convert text strings into numerical vector representations
 * that can be used for semantic similarity search in ChromaDB.
 */
interface EmbeddingFunction
{
    /**
     * Generate embeddings for the given texts.
     *
     * @param  array<string>  $texts  An array of text strings to embed
     * @return array<array<float>> A 2D array where each element is an embedding vector
     *
     * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
     */
    public function generate(array $texts): array;
}
