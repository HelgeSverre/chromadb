<?php

namespace HelgeSverre\Chromadb\Embeddings;

use GuzzleHttp\BodySummarizer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
 * Simple HTTP client factory for embedding providers.
 * Provides Guzzle clients with enhanced error message truncation.
 */
class HttpClient
{
    /**
     * Create a Guzzle client with enhanced error handling.
     *
     * Uses larger error body truncation (50k vs default 120 bytes)
     * for better debugging when APIs return detailed error messages.
     *
     * @param  array  $config  Guzzle client configuration (base_uri, headers, timeout, etc.)
     * @param  int  $truncateAt  Maximum characters to include in error messages
     */
    public static function create(array $config, int $truncateAt = 50_000): Client
    {
        $stack = HandlerStack::create();
        $stack->remove('http_errors');
        $stack->unshift(
            Middleware::httpErrors(new BodySummarizer($truncateAt)),
            'http_errors'
        );

        return new Client(array_merge(['handler' => $stack], $config));
    }
}
