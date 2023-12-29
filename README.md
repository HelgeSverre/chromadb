<p align="center"><img src="./art/header.png"></p>

# ChromaDB PHP API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/helgesverre/chromadb.svg?style=flat-square)](https://packagist.org/packages/helgesverre/chromadb)
[![Total Downloads](https://img.shields.io/packagist/dt/helgesverre/chromadb.svg?style=flat-square)](https://packagist.org/packages/helgesverre/chromadb)

[ChromaDB](https://github.com/chroma-core/chroma) is an open-source vector database

## Installation

You can install the package via composer:

```bash
composer require helgesverre/chromadb
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="chromadb-config"
```

This is the contents of the published `config/chromadb.php` file:

```php
return [
    'token' => env('CHROMADB_TOKEN'),
    'host' => env('CHROMADB_HOST', 'localhost'),
    'port' => env('CHROMADB_PORT', '19530'),
];
```

## Usage

todo: write usage docs

## Running ChromaDB in Docker

To quickly get started with Chromadb, you can run it in Docker

```bash
# Download the docker-compose.yml file
wget https://raw.githubusercontent.com/chroma-core/chroma/47447b6f9846fb63cc17d3f458df405387f46127/docker-compose.yml

# Start Milvus
docker compose up -d
```

A healthcheck endpoint will now be available on `http://localhost:9091/healthz`, and the Milvus API will be available
on `http://localhost:19530`.

To stop Chromadb, run `docker compose down`, to wipe all the data, run `docker compose down -v`.

Go to the ChromaDB [deployment documentation](https://docs.trychroma.com/deployment) for more information.

## Testing

```bash
cp .env.example .env

docker compose up -d
 
composer test
composer analyse src
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
