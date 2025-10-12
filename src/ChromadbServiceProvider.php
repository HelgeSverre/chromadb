<?php

namespace HelgeSverre\Chromadb;

use InvalidArgumentException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChromadbServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('chromadb')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        // Bind the default embedding function from config (when available)
        $this->app->singleton(Embeddings\EmbeddingFunction::class, function () {
            try {
                return Embeddings\Embeddings::fromConfig();
            } catch (InvalidArgumentException) {
                // No embedding config available - return null
                // This allows tests and non-embedding use cases to work
                return null;
            }
        });

        $this->app->bind(Chromadb::class, function ($app) {
            return new Chromadb(
                token: config('chromadb.token'),
                host: config('chromadb.host'),
                port: config('chromadb.port'),
                tenant: config('chromadb.tenant'),
                database: config('chromadb.database'),
                embeddingFunction: $app->make(Embeddings\EmbeddingFunction::class),
            );
        });
    }
}
