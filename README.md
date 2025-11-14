<p align="center"><img src="./art/header.png"></p>

# ChromaDB PHP API Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/helgesverre/chromadb.svg?style=flat-square)](https://packagist.org/packages/helgesverre/chromadb)
[![Total Downloads](https://img.shields.io/packagist/dt/helgesverre/chromadb.svg?style=flat-square)](https://packagist.org/packages/helgesverre/chromadb)
[![ChromaDB v2 API](https://img.shields.io/badge/ChromaDB-v2%20API-blue.svg)](https://docs.trychroma.com/)

A **framework-agnostic PHP client** for the [ChromaDB](https://github.com/chroma-core/chroma) v2 API. Works with any PHP 8.2+ project, with **optional Laravel integration** for enhanced developer experience.

> **Use Anywhere:** This package works in vanilla PHP, Symfony, WordPress, or any PHP framework. <br>
> Laravel features (config, facades, service provider) are completely optional.

> **Looking for an alternative?** Check out [CodeWithKyrian/chromadb-php](https://github.com/CodeWithKyrian/chromadb-php) for another ChromaDB PHP client.

## Documentation

## Version Compatibility

This package supports **ChromaDB v2 API**. The v1 API has been deprecated by ChromaDB.

- **ChromaDB Server:** 1.0.0 - 1.1.x
- **PHP:** 8.2+
- **Laravel:** 10.x, 11.x, 12.x (optional)

## Installation

Install the package via Composer:

```bash
composer require helgesverre/chromadb
```

## Quick Start

### Basic Usage (Any PHP Project)

```php
use HelgeSverre\Chromadb\Chromadb;

// Initialize client with direct instantiation
$chromadb = new Chromadb(
    token: 'test-token-chroma-local-dev',
    host: 'http://localhost',
    port: '8000'
);

// Create a collection
$response = $chromadb->collections()->create('my_collection');
$collectionId = $response->json('id');

// Add documents with embeddings
$chromadb->items()->add(
    collectionId: $collectionId,
    ids: ['doc1', 'doc2'],
    embeddings: [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]],
    documents: ['Document 1', 'Document 2']
);

// Query the collection
$results = $chromadb->items()->query(
    collectionId: $collectionId,
    queryEmbeddings: [[0.1, 0.2, 0.3]],
    nResults: 10
);
```

### Using with Laravel (Optional)

If you're using Laravel, you can leverage the Facade and config-based setup:

```php
use HelgeSverre\Chromadb\Facades\Chromadb;

// Using the Facade (configuration loaded from config/chromadb.php)
$response = Chromadb::client()->collections()->create('my_collection');
```

See the [Laravel Integration](#laravel-integration-optional) section below for full setup details, including dependency injection.

## Usage

### Collections

```php
// Create a new collection with optional metadata
$chromadb->collections()->create(
    name: 'my_collection',
);

// Create a collection with metadata and configuration
$chromadb->collections()->create(
    name: 'my_collection',
    metadata: ['description' => 'My collection'],
    configuration: [
        'hnsw:space' => 'cosine',
        'hnsw:construction_ef' => 100,
        'hnsw:M' => 16
    ]
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
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    newName: 'new_collection_name',
    newMetadata: ['updated' => 'true']
);

// List all collections
$collections = $chromadb->collections()->list();

// List collections with pagination
$collections = $chromadb->collections()->list(
    limit: 10,
    offset: 0
);

// Fork a collection (create a copy)
$chromadb->collections()->fork(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    newName: 'my_collection_copy'
);

// Get collection by CRN (Collection Resource Name)
$chromadb->collections()->getByCrn(
    crn: 'crn:chroma:collection:default_tenant:default_database:3ea5a914-e2ab-47cb-b285-8e585c9af4f3'
);
```

### Items

```php
// Add items to a collection with embeddings, metadata, and documents
$chromadb->items()->add(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    ids: ['item1', 'item2'],
    embeddings: [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]],
    documents: ['doc1', 'doc2']
);

// Update items in a collection with new embeddings, metadata, and documents
$chromadb->items()->update(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    ids: ['item1', 'item2'],
    embeddings: [[0.7, 0.8, 0.9], [1.0, 1.1, 1.2]],
    documents: ['new_doc1', 'new_doc2']
);

// Upsert items in a collection (insert if not exist, update if exist)
$chromadb->items()->upsert(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    ids: ['item'],
    embeddings: [[0.5, 0.6, 0.7]],
    metadatas: [['title' => 'metadata']],
    documents: ['document']
);

// Retrieve specific items from a collection by their IDs
$chromadb->items()->get(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    ids: ['item1', 'item2']
);

// Delete specific items from a collection by their IDs
$chromadb->items()->delete(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    ids: ['item1', 'item2']
);

// Count the number of items in a collection
$chromadb->items()->count(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3'
);

// Query items in a collection based on embeddings, texts, and other filters
$chromadb->items()->query(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    queryEmbeddings: [[0.8, 0.8, 0.8]],
    include: ['documents', 'metadatas', 'distances'],
    nResults: 5
);

// Hybrid search with multiple search strategies
// Note: search() requires Chroma Cloud - not available in local ChromaDB
$chromadb->items()->search(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    searches: [
        [
            'filter' => [
                'query_ids' => ['id1', 'id2'],
                'where_clause' => ['category' => 'technology']
            ],
            'limit' => ['limit' => 10, 'offset' => 0],
            'rank' => ['type' => 'bm25'],
            'select' => ['keys' => ['title', 'content']]
        ]
    ]
);

// Filtering with where (metadata filters) and whereDocument (content filters)

// Get items by metadata filters
$chromadb->items()->get(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    where: [
        'category' => 'technology',
        'year' => ['$gte' => 2020] // Greater than or equal to 2020
    ]
);

// Delete items matching metadata filters
$chromadb->items()->delete(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    where: [
        'status' => 'archived',
        'priority' => ['$lt' => 3] // Less than 3
    ]
);

// Delete items matching document content filters
$chromadb->items()->delete(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    whereDocument: ['$contains' => 'obsolete']
);

// Combine metadata and document filters
$chromadb->items()->delete(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    where: ['category' => 'temporary'],
    whereDocument: ['$contains' => 'draft']
);

// Query with filters
$chromadb->items()->query(
    collectionId: '3ea5a914-e2ab-47cb-b285-8e585c9af4f3',
    queryEmbeddings: [[0.8, 0.8, 0.8]],
    where: ['category' => 'articles'],
    whereDocument: ['$contains' => 'machine learning'],
    nResults: 10
);

// Supported filter operators:
// $eq, $ne, $gt, $gte, $lt, $lte, $in, $nin, $and, $or, $not, $contains
```

### Database Management

ChromaDB v2 supports multiple databases within a tenant for logical data separation:

```php
// Create a new database
$chromadb->database()->create(name: 'production');

// Get database information
$response = $chromadb->database()->get(database: 'production');

// List all databases in the current tenant
$databases = $chromadb->database()->list();

// List databases with pagination
$databases = $chromadb->database()->list(
    limit: 10,
    offset: 0
);

// Delete a database
$chromadb->database()->delete(database: 'old_database');

// Work with a specific database
$productionDb = $chromadb->withDatabase('production');
$stagingDb = $chromadb->withDatabase('staging');
```

### Tenant Management

Manage tenants for multi-tenancy isolation:

```php
// Create a new tenant
$chromadb->tenant()->create(name: 'customer_acme');

// Get tenant information
$response = $chromadb->tenant()->get(tenant: 'customer_acme');

// Update tenant configuration
$chromadb->tenant()->update(
    tenantName: 'customer_acme',
    resourceName: 'acme-corp-resource'
);
```

### Server API

Monitor and manage the ChromaDB server:

```php
// Check server health
$response = $chromadb->server()->healthcheck();

// Get heartbeat (nanosecond timestamp)
$response = $chromadb->server()->heartbeat();

// Get server version
$version = $chromadb->server()->version();

// Get pre-flight checks information
$response = $chromadb->server()->preFlightChecks();

// Get current user identity and permissions
$response = $chromadb->server()->identity();
$userId = $response->json('user_id');
$tenant = $response->json('tenant');
$databases = $response->json('databases');

// Reset the server (⚠️ WARNING: Deletes all data)
$success = $chromadb->server()->reset();
```

### Multi-Tenancy

ChromaDB v2 provides native multi-tenancy support:

```php
// Create tenant-specific clients
$tenant1Client = $chromadb->withTenant('customer_a');
$tenant2Client = $chromadb->withTenant('customer_b');

// Operations are automatically isolated
$tenant1Collections = $tenant1Client->collections()->list();
$tenant2Collections = $tenant2Client->collections()->list();

// Switch database context
$productionClient = $chromadb
    ->withTenant('my_org')
    ->withDatabase('production');
```

## Laravel Integration (Optional)

If you're using Laravel, this package provides additional features for enhanced developer experience through the service provider, config file, and Facade.

### Publishing Configuration

Publish the config file with:

```bash
php artisan vendor:publish --tag="chromadb-config"
```

This creates `config/chromadb.php` with the following structure:

```php
return [
    'token' => env('CHROMADB_TOKEN'),
    'host' => env('CHROMADB_HOST', 'http://localhost'),
    'port' => env('CHROMADB_PORT', '8000'),

    'tenant' => env('CHROMADB_TENANT', 'default_tenant'),
    'database' => env('CHROMADB_DATABASE', 'default_database'),

    'embeddings' => [
        'default' => env('CHROMADB_EMBEDDING_PROVIDER', 'openai'),
        'providers' => [
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
                'dimensions' => env('OPENAI_EMBEDDING_DIMENSIONS', null), // Optional: reduce dimensions
                'organization' => env('OPENAI_ORGANIZATION', null), // Optional: organization ID
            ],
            'voyage' => [
                'api_key' => env('VOYAGE_API_KEY'),
                'model' => env('VOYAGE_EMBEDDING_MODEL', 'voyage-3.5'),
                'input_type' => env('VOYAGE_INPUT_TYPE', 'document'), // 'document' or 'query'
            ],
            'mistral' => [
                'api_key' => env('MISTRAL_API_KEY'),
                'model' => env('MISTRAL_EMBEDDING_MODEL', 'mistral-embed'),
            ],
            'jina' => [
                'api_key' => env('JINA_API_KEY'),
                'model' => env('JINA_EMBEDDING_MODEL', 'jina-embeddings-v3'),
                'task' => env('JINA_TASK', null), // Optional: 'retrieval.query', 'retrieval.passage', 'text-matching'
                'dimensions' => env('JINA_EMBEDDING_DIMENSIONS', null), // Optional: reduce dimensions
            ],
            'ollama' => [
                'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
                'model' => env('OLLAMA_EMBEDDING_MODEL', 'all-minilm'),
            ],
        ],
    ],
];
```

### Environment Variables

Add these variables to your `.env` file:

```env
# ChromaDB Connection
CHROMADB_TOKEN=test-token-chroma-local-dev
CHROMADB_HOST=http://localhost
CHROMADB_PORT=8000

# Multi-tenancy (optional)
CHROMADB_TENANT=default_tenant
CHROMADB_DATABASE=default_database

# Embedding Provider (optional)
CHROMADB_EMBEDDING_PROVIDER=openai

# OpenAI Configuration (if using OpenAI embeddings)
OPENAI_API_KEY=your-openai-api-key
OPENAI_EMBEDDING_MODEL=text-embedding-3-small

# Voyage AI Configuration (if using Voyage embeddings)
VOYAGE_API_KEY=your-voyage-api-key
VOYAGE_EMBEDDING_MODEL=voyage-3.5

# Mistral AI Configuration (if using Mistral embeddings)
MISTRAL_API_KEY=your-mistral-api-key
MISTRAL_EMBEDDING_MODEL=mistral-embed

# Jina AI Configuration (if using Jina embeddings)
JINA_API_KEY=your-jina-api-key
JINA_EMBEDDING_MODEL=jina-embeddings-v3

# Ollama Configuration (if using local Ollama embeddings)
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_EMBEDDING_MODEL=all-minilm
```

### Using the Facade

The package automatically registers a Facade for convenient static access:

```php
use HelgeSverre\Chromadb\Facades\Chromadb;

// Access through Facade (uses config automatically)
$collections = Chromadb::client()->collections()->list();
$response = Chromadb::client()->collections()->create('my_collection');
```

### Dependency Injection

The service provider automatically binds the client to the container:

```php
use HelgeSverre\Chromadb\Chromadb;

class MyController
{
    public function __construct(private Chromadb $chromadb)
    {
        // $chromadb is automatically configured from config/chromadb.php
    }

    public function index()
    {
        $collections = $this->chromadb->collections()->list();
        return view('collections.index', compact('collections'));
    }
}
```

### Using Config-Based Embeddings (Laravel Only)

> **Note:** The `Embeddings::fromConfig()` method requires Laravel and reads from your config file.

```php
use HelgeSverre\Chromadb\Embeddings\Embeddings;

// Create embedder from Laravel config
$embedder = Embeddings::fromConfig('openai');

// Or use the default provider from config
$embedder = Embeddings::fromConfig(); // Uses CHROMADB_EMBEDDING_PROVIDER
```

## Embedding Providers

This package includes built-in support for automatic embedding generation using various providers. All embedding classes work in any PHP project - Laravel is not required.

### Supported Providers

- **OpenAI** - `text-embedding-3-small`, `text-embedding-3-large`, `text-embedding-ada-002`
- **Voyage AI** - `voyage-3.5` (default), `voyage-3-large`, `voyage-code-3`, `voyage-finance-2`, `voyage-law-2`
- **Mistral AI** - `mistral-embed`
- **Jina AI** - `jina-embeddings-v3`, `jina-embeddings-v2-base-en`, `jina-embeddings-v2-small-en`, `jina-clip-v2`
- **Ollama** - Local embeddings with any Ollama model

### Direct Usage (Any PHP Project)

Create embedding providers directly without any framework dependencies:

```php
use HelgeSverre\Chromadb\Embeddings\Embeddings;

// Create embedder with direct instantiation
$embedder = Embeddings::openai(
    apiKey: 'sk-your-api-key',
    model: 'text-embedding-3-small'
);

// Generate embeddings
$documents = [
    'The quick brown fox jumps over the lazy dog',
    'Vector databases enable semantic search',
];

$embeddings = $embedder->generate($documents);

// Add to ChromaDB
$chromadb->items()->add(
    collectionId: $collectionId,
    ids: ['doc1', 'doc2'],
    embeddings: $embeddings,
    documents: $documents
);
```

### With Laravel Config (Laravel Only)

If using Laravel, you can configure embeddings in your config file and load them with `fromConfig()`:

```php
use HelgeSverre\Chromadb\Embeddings\Embeddings;

// Create embedder from Laravel config (requires config/chromadb.php)
$embedder = Embeddings::fromConfig('openai');

// Or use default provider from config
$embedder = Embeddings::fromConfig(); // Uses CHROMADB_EMBEDDING_PROVIDER

$embeddings = $embedder->generate($documents);
```

### Automatic Embedding Generation

#### Using withEmbeddings()

Configure a client instance with an embedding function for automatic embedding generation:

```php
// Create an embedding function
$embedder = new OpenAIEmbeddings(apiKey: 'sk-...');

// Configure client with embedder
$chromadb = $chromadb->withEmbeddings($embedder);

// Add documents with automatic embeddings
$chromadb->items()->addWithEmbeddings(
    collectionId: $collectionId,
    documents: ['Document 1', 'Document 2'],
    embeddingFunction: null,  // Optional: override default embedding function
    ids: ['doc1', 'doc2'],    // Optional: auto-generated if null
    metadatas: [['type' => 'article'], ['type' => 'blog']]  // Optional
);

// Query with text (automatic embedding) - basic usage
$results = $chromadb->items()->queryWithText(
    collectionId: $collectionId,
    queryText: 'search query',
    nResults: 10
);

// Query with text - advanced usage with override and filters
$mistralEmbedder = new MistralEmbeddings(
    apiKey: 'your-mistral-key',
    model: 'mistral-embed'
);

$results = $chromadb->items()->queryWithText(
    collectionId: $collectionId,
    queryText: 'machine learning',
    embeddingFunction: $mistralEmbedder,  // Override default embedder
    nResults: 10,
    include: ['documents', 'metadatas', 'distances'],
    where: ['category' => 'tech'],
    whereDocument: ['$contains' => 'AI']
);
```

#### Available Embedding Providers

All providers implement the `EmbeddingFunction` interface and work in any PHP 8.2+ project (Laravel not required). Each provider can be instantiated directly or using the `Embeddings` factory class.

##### 1. OpenAI Embeddings

```php
use HelgeSverre\Chromadb\Embeddings\OpenAIEmbeddings;
use HelgeSverre\Chromadb\Embeddings\Embeddings;

// Direct instantiation
$embedder = new OpenAIEmbeddings(
    apiKey: 'sk-your-api-key',
    model: 'text-embedding-3-small',  // Default
    dimensions: 1536,                 // Optional: dimension reduction
    organization: 'org-123'           // Optional: organization ID
);

// Or using factory
$embedder = Embeddings::openai(
    apiKey: 'sk-your-api-key',
    model: 'text-embedding-3-small'
);

// Supported models:
// - text-embedding-3-small (1536 dims, efficient)
// - text-embedding-3-large (3072 dims, best quality)
// - text-embedding-ada-002 (1536 dims, legacy)
```

##### 2. Voyage AI Embeddings

```php
use HelgeSverre\Chromadb\Embeddings\VoyageAIEmbeddings;

$embedder = new VoyageAIEmbeddings(
    apiKey: 'your-voyage-api-key',
    model: 'voyage-3.5',      // Default
    inputType: 'document'     // 'document' or 'query'
);

// Or using factory
$embedder = Embeddings::voyage(
    apiKey: 'your-voyage-api-key',
    model: 'voyage-3.5'
);

// Supported models:
// - voyage-3.5 (efficient, 8M TPM)
// - voyage-3-large (best quality)
// - voyage-code-3 (optimized for code)
// - voyage-finance-2 (financial domain)
// - voyage-law-2 (legal domain)
```

##### 3. Mistral AI Embeddings

```php
use HelgeSverre\Chromadb\Embeddings\MistralEmbeddings;

$embedder = new MistralEmbeddings(
    apiKey: 'your-mistral-api-key',
    model: 'mistral-embed'  // Default (1024 dimensions)
);

// Or using factory
$embedder = Embeddings::mistral(
    apiKey: 'your-mistral-api-key',
    model: 'mistral-embed'
);
```

##### 4. Jina AI Embeddings

```php
use HelgeSverre\Chromadb\Embeddings\JinaEmbeddings;

$embedder = new JinaEmbeddings(
    apiKey: 'your-jina-api-key',
    model: 'jina-embeddings-v3',           // Default
    task: 'retrieval.query',               // Optional: task type
    dimensions: 1024                       // Optional: dimension reduction
);

// Or using factory
$embedder = Embeddings::jina(
    apiKey: 'your-jina-api-key',
    model: 'jina-embeddings-v3'
);

// Supported models:
// - jina-embeddings-v3 (570M, 1024 dims, multilingual)
// - jina-embeddings-v2-base-en (137M, 768 dims, English)
// - jina-embeddings-v2-small-en (33M, 512 dims, English)
// - jina-clip-v2 (885M, 1024 dims, multimodal)
//
// Optional task types:
// - retrieval.query
// - retrieval.passage
// - text-matching
```

##### 5. Ollama Embeddings (Local)

```php
use HelgeSverre\Chromadb\Embeddings\OllamaEmbeddings;

$embedder = new OllamaEmbeddings(
    model: 'all-minilm',                    // Default
    baseUrl: 'http://localhost:11434',     // Default
    timeout: 60                             // Default timeout in seconds
);

// Or using factory
$embedder = Embeddings::ollama(
    model: 'all-minilm',
    baseUrl: 'http://localhost:11434'
);

// Supported models (any Ollama model):
// - all-minilm (lightweight, default)
// - nomic-embed-text (high quality)
// - mxbai-embed-large (larger model)
// - snowflake-arctic-embed (specialized)
```

#### Using Helper Functions

For a more functional programming style, the package provides convenient helper functions:

```php
use function HelgeSverre\Chromadb\Embeddings\embeddings_openai;
use function HelgeSverre\Chromadb\Embeddings\embeddings_voyage;
use function HelgeSverre\Chromadb\Embeddings\embeddings_mistral;
use function HelgeSverre\Chromadb\Embeddings\embeddings_jina;
use function HelgeSverre\Chromadb\Embeddings\embeddings_ollama;

// Generate embeddings using helper functions
$embeddings = embeddings_openai(
    texts: ['Document 1', 'Document 2'],
    apiKey: 'sk-your-api-key',
    model: 'text-embedding-3-small'
);

$embeddings = embeddings_voyage(
    texts: ['Document 1', 'Document 2'],
    apiKey: 'your-voyage-key',
    model: 'voyage-3.5'
);

$embeddings = embeddings_mistral(
    texts: ['Document 1', 'Document 2'],
    apiKey: 'your-mistral-key'
);

$embeddings = embeddings_jina(
    texts: ['Document 1', 'Document 2'],
    apiKey: 'your-jina-key',
    model: 'jina-embeddings-v3'
);

$embeddings = embeddings_ollama(
    texts: ['Document 1', 'Document 2'],
    model: 'all-minilm',
    baseUrl: 'http://localhost:11434'
);

// Use directly with ChromaDB
$chromadb->items()->add(
    collectionId: $collectionId,
    ids: ['doc1', 'doc2'],
    embeddings: embeddings_openai(
        texts: ['Document 1', 'Document 2'],
        apiKey: 'sk-your-api-key'
    ),
    documents: ['Document 1', 'Document 2']
);
```

#### Creating Custom Embedding Functions

You can create your own embedding provider by implementing the `EmbeddingFunction` interface. This works in any PHP project:

```php
use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;

class MyCustomEmbeddings implements EmbeddingFunction
{
    public function __construct(
        private string $apiKey,
        private string $model = 'my-model'
    ) {}

    /**
     * Generate embeddings for the given texts.
     *
     * @param array<string> $texts Array of text strings to embed
     * @return array<array<float>> 2D array where each element is an embedding vector
     */
    public function generate(array $texts): array
    {
        $embeddings = [];

        foreach ($texts as $text) {
            // Your custom embedding logic here
            $vector = $this->callYourEmbeddingAPI($text);
            $embeddings[] = $vector;
        }

        return $embeddings;
    }

    private function callYourEmbeddingAPI(string $text): array
    {
        // Call your embedding service
        // Return array of floats, e.g., [0.1, 0.2, 0.3, ...]
        return [];
    }
}

// Use your custom embedder
$embedder = new MyCustomEmbeddings(apiKey: 'your-key');
$chromadb = $chromadb->withEmbeddings($embedder);
```

#### Interface Definition

```php
namespace HelgeSverre\Chromadb\Embeddings;

interface EmbeddingFunction
{
    /**
     * Generate embeddings for the given texts.
     *
     * @param array<string> $texts An array of text strings to embed
     * @return array<array<float>> A 2D array where each element is an embedding vector
     * @throws \HelgeSverre\Chromadb\Exceptions\EmbeddingException
     */
    public function generate(array $texts): array;
}
```

## Example: Semantic Search with ChromaDB and OpenAI Embeddings

This example demonstrates how to perform a semantic search in ChromaDB using embeddings generated from OpenAI. This works in any PHP project.

Full code available in [SemanticSearchTest.php](./tests/Feature/SemanticSearchTest.php).

> **Note:** This example uses the OpenAI PHP client. You can use any method to generate embeddings - this package's embedding providers, direct API calls, or any other embedding service.

### Prepare Your Data

First, create an array of data you wish to index. In this example, we'll use blog posts with titles, summaries, and
tags.

```php
$blogPosts = [
    [
        'title' => 'Exploring Laravel',
        'summary' => 'A deep dive into Laravel frameworks...',
        'tags' => 'PHP, Laravel, Web Development'
    ],
    [
        'title' => 'Introduction to React',
        'summary' => 'Understanding the basics of React and how it revolutionizes frontend development.',
        'tags' => 'JavaScript, React, Frontend'
    ],
];
```

### Generate Embeddings

Use OpenAI's embeddings API to convert the summaries of your blog posts into vector embeddings.

```php
$summaries = array_column($blogPosts, 'summary');
$embeddingsResponse = OpenAI::client('sk-your-openai-api-key')
    ->embeddings()
    ->create([
        'model' => 'text-embedding-ada-002',
        'input' => $summaries,
    ]);

foreach ($embeddingsResponse->embeddings as $embedding) {
    $blogPosts[$embedding->index]['vector'] = $embedding->embedding;
}
```

### Create ChromaDB Collection

Create a collection in ChromaDB to store your blog post embeddings.

```php
$createCollectionResponse = $chromadb->collections()->create(
    name: 'blog_posts',
);

$collectionId = $createCollectionResponse->json('id');
```

### Insert into ChromaDB

Insert these embeddings, along with other blog post data, into your ChromaDB collection.

```php
foreach ($blogPosts as $post) {
    // Extract embedding from post (v2 API metadata cannot contain arrays)
    $embedding = $post['vector'];
    unset($post['vector']); // Remove from metadata

    $chromadb->items()->add(
        collectionId: $collectionId,
        ids: [$post['title']],
        embeddings: [$embedding],
        metadatas: [$post]
    );
}
```

### Creating a Search Vector with OpenAI

Generate a search vector for your query, akin to how you processed the blog posts.

```php
$searchResponse = OpenAI::client('sk-your-openai-api-key')
    ->embeddings()
    ->create([
        'model' => 'text-embedding-ada-002',
        'input' => 'laravel framework',
    ]);

$searchEmbedding = $searchResponse->embeddings[0]->embedding;
```

### Searching using the Embedding in ChromaDB

Use the ChromaDB client to perform a search with the generated embedding.

```php
$searchResponse = $chromadb->items()->query(
    collectionId: $collectionId,
    queryEmbeddings: [$searchEmbedding],
    nResults: 3,
    include: ['metadatas']
);

// Output the search results
$metadatas = $searchResponse->json('metadatas.0');
$ids = $searchResponse->json('ids.0');

foreach ($metadatas as $index => $metadata) {
    echo "Title: " . $metadata['title'] . "\n";
    echo "Summary: " . $metadata['summary'] . "\n";
    echo "Tags: " . $metadata['tags'] . "\n\n";
}
```

## Running ChromaDB in Docker

To quickly get started with ChromaDB (works with any PHP setup), you can run it in Docker:

```bash
# Download the docker-compose.yml file
wget https://raw.githubusercontent.com/HelgeSverre/chromadb/refs/heads/main/docker-compose.yml

# Start ChromaDB
docker compose up -d
```

The auth token is set to `test-token-chroma-local-dev` by default.

You can change this in the `docker-compose.yml` file by changing the `CHROMA_SERVER_AUTH_CREDENTIALS` environment
variable

To stop ChromaDB, run `docker compose down`, to wipe all the data, run `docker compose down -v`.

> **NOTE**
>
> The `docker-compose.yml` file in this repo is provided only as an example and should not be used in
> production.
>
> Go to the ChromaDB [deployment documentation](https://docs.trychroma.com/deployment) for more information on deploying
> Chroma in production.

## Testing

```bash
cp .env.example .env

docker compose up -d

composer test
composer analyse src
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email the maintainer instead of using the issue tracker.

## Credits

- [Helge Sverre](https://github.com/HelgeSverre)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
