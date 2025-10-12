# ChromaDB Embeddings - Usage Guide (UPDATED!)

## 🎉 NOW WITH AUTOMATIC EMBEDDINGS!

As of this version, embeddings are **automatically injected** from your config. Just configure once and forget about it!

## Current Setup (Your Config)

```php
// config/chromadb.php
'embeddings' => [
    'default' => 'openai', // Configure your default provider
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => 'text-embedding-3-small',
        ],
    ],
],
```

##  NEW: Automatic Usage (Recommended)

```php
use HelgeSverre\Chromadb\Facades\Chromadb;

$client = Chromadb::client();
$collection = $client->collections()->create(name: 'documents');

// ✨ Embeddings are automatic now! No need to pass anything!
$client->items()->addWithEmbeddings(
    collectionId: $collection->json('id'),
    documents: ['My document text', 'Another document'],
);

// ✨ Same for queries!
$results = $client->items()->queryWithText(
    collectionId: $collection->json('id'),
    queryText: 'Find documents about machine learning',
    nResults: 5
);
```

**That's it!** The configured embedding function is automatically used.

---

## Override When Needed

You can still override the default when you need a different provider:

```php
use HelgeSverre\Chromadb\Embeddings\Embeddings;

// Use Jina instead of your configured OpenAI
$client->items()->addWithEmbeddings(
    collectionId: $collection->json('id'),
    documents: ['My document'],
    embeddingFunction: Embeddings::jina(env('JINA_API_KEY')) // Override
);

// Or use a named config
$client->items()->queryWithText(
    collectionId: $collection->json('id'),
    queryText: 'Search query',
    embeddingFunction: Embeddings::fromConfig('jina') // Use Jina from config
);
```

---

## Runtime Switching with `withEmbeddings()`

Change the embedding provider for a specific operation:

```php
// Override embedding function for this client instance
$jinaClient = Chromadb::client()->withEmbeddings(
    Embeddings::jina(env('JINA_API_KEY'))
);

// Now all operations use Jina
$jinaClient->items()->addWithEmbeddings(
    collectionId: $id,
    documents: $docs
); // Uses Jina!

$jinaClient->items()->queryWithText(
    collectionId: $id,
    queryText: 'query'
); // Also uses Jina!
```

---

## Multi-Tenant Example

```php
class TenantDocumentService
{
    public function addDocuments(Organization $org, array $documents)
    {
        $client = Chromadb::client()
            ->withTenant("tenant_{$org->id}")
            ->withDatabase('production');
        // Embedding function is already configured!

        $collection = $client->collections()->create(
            name: "documents_{$org->id}"
        );

        // Just works! Uses configured embedding provider
        $client->items()->addWithEmbeddings(
            collectionId: $collection->json('id'),
            documents: $documents
        );
    }
}
```

---

## Different Providers for Different Use Cases

```php
class SearchService
{
    public function indexDocuments(array $documents)
    {
        $client = Chromadb::client()->withEmbeddings(
            // Use Jina with document optimization
            Embeddings::jina(
                apiKey: env('JINA_API_KEY'),
                task: 'retrieval.passage' // Optimized for indexing
            )
        );

        $client->items()->addWithEmbeddings(
            collectionId: $collectionId,
            documents: $documents
        );
    }

    public function search(string $query)
    {
        $client = Chromadb::client()->withEmbeddings(
            // Use Jina with query optimization
            Embeddings::jina(
                apiKey: env('JINA_API_KEY'),
                task: 'retrieval.query' // Optimized for queries
            )
        );

        return $client->items()->queryWithText(
            collectionId: $collectionId,
            queryText: $query,
            nResults: 10
        );
    }
}
```

---

## Environment-Based Configuration

```php
// .env.production
CHROMADB_EMBEDDING_PROVIDER=openai
OPENAI_API_KEY=sk-...

// .env.local
CHROMADB_EMBEDDING_PROVIDER=ollama
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_EMBEDDING_MODEL=all-minilm

// Usage - same code works in both environments!
$client->items()->addWithEmbeddings(
    collectionId: $id,
    documents: $docs
); // Automatically uses the right provider!
```

No code changes needed - just environment configuration!

---

## Full Example: Blog Search

```php
class BlogPostService
{
    public function __construct(
        private Chromadb $chromadb
    ) {}

    public function indexPosts(Collection $posts)
    {
        $collection = $this->chromadb->collections()->create(
            name: 'blog_posts',
            metadata: ['type' => 'content']
        );

        $documents = $posts->pluck('content')->toArray();
        $metadatas = $posts->map(fn($post) => [
            'title' => $post->title,
            'author' => $post->author,
            'published_at' => $post->published_at->toISOString(),
        ])->toArray();

        // ✨ No embedding function needed!
        $this->chromadb->items()->addWithEmbeddings(
            collectionId: $collection->json('id'),
            documents: $documents,
            metadatas: $metadatas
        );
    }

    public function search(string $query, ?string $author = null)
    {
        $where = $author ? ['author' => $author] : null;

        // ✨ Still no embedding function needed!
        return $this->chromadb->items()->queryWithText(
            collectionId: $this->getCollectionId(),
            queryText: $query,
            where: $where,
            nResults: 10,
            include: ['documents', 'metadatas', 'distances']
        );
    }
}
```

---

## Error Handling

If you forget to configure an embedding provider:

```php
// If config/chromadb.php has no embedding config
$client->items()->addWithEmbeddings(
    collectionId: $id,
    documents: $docs
);

// Throws RuntimeException:
// "No embedding function configured. Either pass one explicitly or configure a default in config/chromadb.php"
```

Clear error message tells you exactly what to do!

---

## Migration Guide (If you have existing code)

### Before (Manual)
```php
use HelgeSverre\Chromadb\Embeddings\Embeddings;

$client->items()->addWithEmbeddings(
    collectionId: $id,
    documents: $docs,
    embeddingFunction: Embeddings::fromConfig() // Had to pass this every time
);
```

### After (Automatic)
```php
// Just remove the embeddingFunction parameter!
$client->items()->addWithEmbeddings(
    collectionId: $id,
    documents: $docs
    // embeddingFunction is now optional!
);
```

**Backward Compatible:** Existing code still works if you keep passing the embedding function explicitly.

---

## Summary of Changes

| Feature | Before | After |
|---------|--------|-------|
| **Basic usage** | Manual `embeddingFunction` parameter | Automatic from config |
| **Override** | Pass different function | Still works same way |
| **Runtime switch** | Not possible | `->withEmbeddings()` |
| **Configuration** | Config + manual passing | Config only |
| **Error handling** | Silent failures | Clear error messages |

---

## Best Practices

1. **Configure once** in `config/chromadb.php` for your default provider
2. **Use automatic** injection for 90% of cases
3. **Override explicitly** for special cases (different providers, different models)
4. **Use `withEmbeddings()`** for consistent overrides across multiple operations
5. **Keep it simple** - less code, fewer bugs!

---

## Next Steps

1. ✅ Embeddings are now automatic
2. ✅ Configure your default provider in `config/chromadb.php`
3. ✅ Remove `embeddingFunction` parameters from your existing code (optional, but cleaner)
4. ✅ Enjoy the simpler API!
