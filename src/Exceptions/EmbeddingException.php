<?php

namespace HelgeSverre\Chromadb\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Exception thrown when an embedding operation fails.
 *
 * This exception provides context about which embedding provider failed
 * and what the underlying error was.
 */
class EmbeddingException extends RuntimeException
{
    /**
     * Create an exception for an API error from an embedding provider.
     *
     * @param  string  $provider  The name of the embedding provider (e.g., 'OpenAI', 'VoyageAI')
     * @param  string  $message  The error message from the provider
     * @param  Throwable|null  $previous  The previous exception for chaining
     * @return static
     */
    public static function apiError(string $provider, string $message, ?Throwable $previous = null): self
    {
        return new self("Error calling {$provider} API: {$message}", 0, $previous);
    }

    /**
     * Create an exception for an invalid API response.
     *
     * @param  string  $provider  The name of the embedding provider
     * @param  string|null  $details  Additional details about the invalid response
     * @return static
     */
    public static function invalidResponse(string $provider, ?string $details = null): self
    {
        $message = "Invalid response from {$provider} API";

        if ($details) {
            $message .= ": {$details}";
        }

        return new self($message);
    }

    /**
     * Create an exception for authentication failures.
     *
     * @param  string  $provider  The name of the embedding provider
     * @return static
     */
    public static function authenticationFailed(string $provider): self
    {
        return new self("Authentication failed for {$provider} API. Please check your API key.");
    }

    /**
     * Create an exception for rate limit errors.
     *
     * @param  string  $provider  The name of the embedding provider
     * @param  int|null  $retryAfter  Number of seconds to wait before retrying (if known)
     * @param  string|null  $details  Additional details about the rate limit error
     * @return static
     */
    public static function rateLimitExceeded(string $provider, ?int $retryAfter = null, ?string $details = null): self
    {
        $message = "Rate limit exceeded for {$provider} API";

        if ($retryAfter !== null && $retryAfter > 0) {
            $message .= ". Retry after {$retryAfter} seconds";
        }

        if ($details) {
            $message .= ": {$details}";
        }

        return new self($message);
    }

    /**
     * Create an exception for empty input arrays.
     *
     * @return static
     */
    public static function emptyInput(): self
    {
        return new self('Cannot generate embeddings for an empty array of texts');
    }
}
