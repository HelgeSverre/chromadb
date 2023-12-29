<?php

namespace HelgeSverre\Chromadb;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChromadbServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('chromadb')->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->app->bind(Chromadb::class, function () {
            return new Chromadb(
                token: config('chromadb.token'),
                host: config('chromadb.host'),
                port: config('chromadb.port'),
            );
        });
    }
}
