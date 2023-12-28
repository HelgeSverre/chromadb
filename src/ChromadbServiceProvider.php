<?php

namespace HelgeSverre\Chromadb;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChromadbServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('milvus')->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->app->bind(Chromadb::class, function () {
            $token = config('milvus.token') ?: base64_encode(
                sprintf('%s:%s', config('milvus.username'), config('milvus.password'))
            );

            return new Chromadb(
                token: $token,
                host: config('milvus.host'),
                port: config('milvus.port'),
            );
        });
    }
}
