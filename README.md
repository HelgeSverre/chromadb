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

## Example: Semantic Search with ChromaDB and OpenAI Embeddings

This example demonstrates how to perform a semantic search in ChromaDB using embeddings generated from OpenAI.

### Prepare Your Data

First, create an array of data you wish to index. In this example, we'll use blog posts with titles, summaries, and
tags.

```php
$blogPosts = [
    [
        'title' => 'Exploring Laravel',
        'summary' => 'A deep dive into Laravel frameworks...',
        'tags' => ['PHP', 'Laravel', 'Web Development']
    ],
    [
        'title' => 'Introduction to React',
        'summary' => 'Understanding the basics of React and how it revolutionizes frontend development.',
        'tags' => ['JavaScript', 'React', 'Frontend']
    ],
    // ... more blog posts
];
```

### Generate Embeddings

Use OpenAI's embeddings API to convert the summaries of your blog posts into vector embeddings.

```php
// Assuming you have a function to interact with OpenAI's API
$embeddings = getOpenAIEmbeddings($blogPosts);

foreach ($embeddings as $index => $embedding) {
    $blogPosts[$index]['embedding'] = $embedding;
}
```

### Create ChromaDB Collection

Create a collection in ChromaDB to store your blog post embeddings.

```php
$chromadb->collections()->create(
    name: 'blog_posts',
    metadata: ['embedding_dimension' => 1536] // Assuming OpenAI embeddings have 1536 dimensions
);
```

### Insert into ChromaDB

Insert these embeddings, along with other blog post data, into your ChromaDB collection.

```php
foreach ($blogPosts as $post) {
    $chromadb->items()->add(
        collectionId: 'blog_posts',
        ids: [$post['title']],
        embeddings: [$post['embedding']],
        metadatas: [$post]
    );
}
```

### Creating a Search Vector with OpenAI

Generate a search vector for your query, akin to how you processed the blog posts.

```php
$searchEmbedding = getOpenAIEmbedding('laravel framework');
```

### Searching using the Embedding in ChromaDB

Use the ChromaDB client to perform a search with the generated embedding.

```php
$searchResponse = $chromadb->items()->query(
    collectionId: 'blog_posts',
    queryEmbeddings: [$searchEmbedding],
    nResults: 3,
    include: ['metadatas']
);

// Output the search results
foreach ($searchResponse->json('results') as $result) {
    echo "Title: " . $result['metadatas']['title'] . "\n";
    echo "Summary: " . $result['metadatas']['summary'] . "\n";
    echo "Tags: " . implode(', ', $result['metadatas']['tags']) . "\n\n";
}
```

## Running ChromaDB in Docker
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

To quickly get started with ChromaDB, you can run it in Docker

```bash
# Download the docker-compose.yml file
wget https://raw.githubusercontent.com/chroma-core/chroma/47447b6f9846fb63cc17d3f458df405387f46127/docker-compose.yml

# Start ChromaDB
docker compose up -d
```

To stop ChromaDB, run `docker compose down`, to wipe all the data, run `docker compose down -v`.

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
