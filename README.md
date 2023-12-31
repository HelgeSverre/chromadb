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

```php
$chromadb = new \HelgeSverre\Chromadb\Chromadb(
    token: 'test-token-chroma-local-dev',
    host: 'http://localhost',
    port: '8000'
);


// Create a new collection with optional metadata
$chromadb->collections()->create(
    name: 'my_collection',
     'generic']
);

// Count the number of collections
$chromadb->collections()->count();

// Retrieve a specific collection by name
$chromadb->collections()->get(
    collectionName: 'my_collection'
);

// Delete a collection by name
$chromadb->collections()->delete(
    collectionName: 'my_collection'
);

// Update a collection's name and/or metadata
$chromadb->collections()->update(
    collectionId: 'collection_id',
    newName: 'new_collection_name',
     'updated']
);

// Add items to a collection with optional embeddings, metadata, and documents
$chromadb->items()->add(
    collectionId: 'collection_id',
    ids: ['item1', 'item2'],
    embeddings: ['embedding1', 'embedding2'],
     'value']],
    documents: ['doc1', 'doc2']
);

// Update items in a collection with new embeddings, metadata, and documents
$chromadb->items()->update(
    collectionId: 'collection_id',
    ids: ['item1', 'item2'],
    embeddings: ['new_embedding1', 'new_embedding2'],
     'new_value']],
    documents: ['new_doc1', 'new_doc2']
);

// Upsert items in a collection (insert if not exist, update if exist)
$chromadb->items()->upsert(
    collectionId: 'collection_id',
    ids: ['item'],
    metadatas: [['title' => 'metadata']],
    documents: ['document']
);

// Retrieve specific items from a collection by their IDs
$chromadb->items()->get(
    collectionId: 'collection_id',
    ids: ['item1', 'item2']
);

// Delete specific items from a collection by their IDs
$chromadb->items()->delete(
    collectionId: 'collection_id',
    ids: ['item1', 'item2']
);

// Count the number of items in a collection
$chromadb->items()->count('collection_id');

// Query items in a collection based on embeddings, texts, and other filters
$chromadb->items()->query(
    collectionId: $collectionId,
    queryEmbeddings: [createTestVector(0.8)],
    include: ['documents', 'metadatas', 'distances'],
    nResults: 5
);
```

## Running ChromaDB in Docker

To quickly get started with Chromadb, you can run it in Docker

```bash
# Download the docker-compose.yml file
wget https://raw.githubusercontent.com/chroma-core/chroma/47447b6f9846fb63cc17d3f458df405387f46127/docker-compose.yml

# Start Chroma
docker compose up -d
```

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
