<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ChromaDB Connection Settings
    |--------------------------------------------------------------------------
    */

    'token' => env('CHROMADB_TOKEN'),
    'host' => env('CHROMADB_HOST', 'http://localhost'),
    'port' => env('CHROMADB_PORT', '8000'),

    /*
    |--------------------------------------------------------------------------
    | Tenant & Database Configuration
    |--------------------------------------------------------------------------
    |
    | ChromaDB supports multi-tenancy through tenants and databases:
    |
    | TENANT: Top-level isolation boundary. Use cases:
    |   - Multi-tenant SaaS: Each customer gets their own tenant
    |   - Complete data isolation between organizations
    |   - Different access control policies per tenant
    |
    | DATABASE: Logical grouping within a tenant. Use cases:
    |   - Environment separation: 'production', 'staging', 'development'
    |   - Project organization: Different databases for different projects
    |   - Feature-based separation: 'main', 'experimental', 'archive'
    |
    | Default values work for most single-tenant applications.
    | For multi-tenant apps, use Chromadb::client()->withTenant($tenant)
    | For multiple databases, use Chromadb::client()->withDatabase($database)
    |
    */

    'tenant' => env('CHROMADB_TENANT', 'default_tenant'),
    'database' => env('CHROMADB_DATABASE', 'default_database'),

    /*
    |--------------------------------------------------------------------------
    | Embedding Configuration
    |--------------------------------------------------------------------------
    |
    | Configure embedding function providers for automatic embedding generation.
    | Supported providers: openai, voyage, mistral, jina, ollama
    |
    */

    'embeddings' => [
        'default' => env('CHROMADB_EMBEDDING_PROVIDER', 'openai'),

        'providers' => [
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
                'dimensions' => env('OPENAI_EMBEDDING_DIMENSIONS', null),
                'organization' => env('OPENAI_ORGANIZATION', null),
            ],

            'voyage' => [
                'api_key' => env('VOYAGE_API_KEY'),
                'model' => env('VOYAGE_EMBEDDING_MODEL', 'voyage-3.5'),
                'input_type' => env('VOYAGE_INPUT_TYPE', 'document'),
            ],

            'mistral' => [
                'api_key' => env('MISTRAL_API_KEY'),
                'model' => env('MISTRAL_EMBEDDING_MODEL', 'mistral-embed'),
            ],

            'jina' => [
                'api_key' => env('JINA_API_KEY'),
                'model' => env('JINA_EMBEDDING_MODEL', 'jina-embeddings-v3'),
                'task' => env('JINA_TASK', null), // e.g., 'retrieval.query', 'retrieval.passage', 'text-matching'
                'dimensions' => env('JINA_EMBEDDING_DIMENSIONS', null),
            ],

            'ollama' => [
                'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
                'model' => env('OLLAMA_EMBEDDING_MODEL', 'all-minilm'),
            ],
        ],
    ],
];
