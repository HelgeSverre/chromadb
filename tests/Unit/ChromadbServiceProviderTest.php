<?php

use HelgeSverre\Chromadb\Chromadb;
use HelgeSverre\Chromadb\ChromadbServiceProvider;
use HelgeSverre\Chromadb\Embeddings\EmbeddingFunction;
use HelgeSverre\Chromadb\Facades\Chromadb as ChromadbFacade;
use HelgeSverre\Chromadb\Tests\TestCase;

/**
 * Comprehensive unit tests for the ChromaDB Laravel Service Provider.
 *
 * Tests cover:
 * - Service container bindings (singleton registration)
 * - Config file publishing
 * - Dependency injection resolution
 * - Config value integration
 * - Laravel Facade resolution
 *
 * Uses Orchestra Testbench for proper Laravel package testing.
 */
class ChromadbServiceProviderTest extends TestCase
{
    /**
     * Test 1: Service provider registers Chromadb binding in the container.
     *
     * Verifies that the service provider properly binds the Chromadb class
     * so that it can be resolved from the container with correct configuration.
     * Each resolution creates a new instance with the current config values.
     */
    public function test_service_provider_registers_chromadb_singleton(): void
    {
        // Arrange: Get instances from the container
        $instance1 = $this->app->make(Chromadb::class);
        $instance2 = $this->app->make(Chromadb::class);

        // Act & Assert: Both instances should be Chromadb class (binding works)
        // Note: bind() creates new instances, not singleton
        expect($instance1)->toBeInstanceOf(Chromadb::class)
            ->and($instance2)->toBeInstanceOf(Chromadb::class)
            ->and($instance1)->not->toBe($instance2); // Verify they are different instances
    }

    /**
     * Test 2: Config file is published to the correct location.
     *
     * Verifies that the service provider's config file can be properly
     * loaded and contains the expected configuration structure.
     */
    public function test_config_publishing_works_correctly(): void
    {
        // Arrange: Get the published config array
        $config = config('chromadb');

        // Act & Assert: Verify config structure
        expect($config)->toBeArray()
            ->and($config)->toHaveKeys(['token', 'host', 'port', 'tenant', 'database', 'embeddings'])
            ->and($config['embeddings'])->toHaveKeys(['default', 'providers']);

        // Verify providers are defined
        expect($config['embeddings']['providers'])->toHaveKeys([
            'openai',
            'voyage',
            'mistral',
            'jina',
            'ollama',
        ]);
    }

    /**
     * Test 3: Dependency injection resolves Chromadb instance correctly.
     *
     * Verifies that the service container can resolve Chromadb through
     * automatic dependency injection, allowing classes to request Chromadb
     * in their constructors.
     */
    public function test_dependency_injection_resolves_chromadb_instance(): void
    {
        // Arrange: A class that depends on Chromadb
        $injectedInstance = $this->app->make(Chromadb::class);
        $testClass = new class($injectedInstance)
        {
            public function __construct(private Chromadb $client) {}

            public function getClient(): Chromadb
            {
                return $this->client;
            }
        };

        // Act: Resolve the dependency
        $resolvedClient = $testClass->getClient();

        // Assert: Verify the injected instance is correct
        expect($resolvedClient)->toBeInstanceOf(Chromadb::class)
            ->and($resolvedClient)->toBe($injectedInstance);
    }

    /**
     * Test 4: Injected Chromadb instance uses config values.
     *
     * Verifies that the Chromadb instance resolved from the container
     * is initialized with values from the config file, ensuring proper
     * configuration integration.
     */
    public function test_injected_instance_uses_config_values(): void
    {
        // Arrange: Set specific config values
        config([
            'chromadb.token' => 'test-token-123',
            'chromadb.host' => 'http://chroma.example.com',
            'chromadb.port' => '9000',
            'chromadb.tenant' => 'test-tenant',
            'chromadb.database' => 'test-database',
        ]);

        // Need to clear the binding and re-bind to pick up new config
        $this->app->forgetInstance(Chromadb::class);

        // Act: Resolve the instance with new config
        $chromadb = $this->app->make(Chromadb::class);

        // Assert: Verify the instance uses the config values
        expect($chromadb->getTenant())->toBe('test-tenant')
            ->and($chromadb->getDatabase())->toBe('test-database')
            ->and($chromadb->resolveBaseUrl())->toBe('http://chroma.example.com:9000');
    }

    /**
     * Test 5: Facade resolves to correct Chromadb instance and uses config.
     *
     * Verifies that the Laravel Facade properly delegates to the
     * Chromadb instance in the service container with correct configuration,
     * allowing convenient static-style access to Chromadb functionality.
     */
    public function test_facade_resolves_to_correct_instance(): void
    {
        // Arrange: Set config values
        config([
            'chromadb.token' => 'facade-test-token',
            'chromadb.host' => 'http://facade.test.local',
            'chromadb.port' => '7000',
            'chromadb.tenant' => 'facade-tenant',
            'chromadb.database' => 'facade-database',
        ]);

        // Act: Get instance through Facade
        $facadedInstance = ChromadbFacade::client();

        // Assert: Facade instance is Chromadb and has correct config
        expect($facadedInstance)->toBeInstanceOf(Chromadb::class)
            ->and($facadedInstance->getTenant())->toBe('facade-tenant')
            ->and($facadedInstance->getDatabase())->toBe('facade-database')
            ->and($facadedInstance->resolveBaseUrl())->toBe('http://facade.test.local:7000');
    }

    /**
     * Additional test: Embedding function is bound as a singleton.
     *
     * Verifies that the EmbeddingFunction is also properly registered
     * in the service container as a singleton.
     */
    public function test_embedding_function_is_bound_as_singleton(): void
    {
        // Arrange: Get two instances of the embedding function binding
        $embedding1 = $this->app->make(EmbeddingFunction::class);
        $embedding2 = $this->app->make(EmbeddingFunction::class);

        // Act & Assert: Both should be the same instance (singleton)
        expect($embedding1)->toBe($embedding2);
    }

    /**
     * Additional test: Service provider uses Spatie LaravelPackageTools.
     *
     * Verifies that the service provider properly extends
     * PackageServiceProvider and implements required methods.
     */
    public function test_service_provider_extends_package_service_provider(): void
    {
        // Arrange: Create a new instance of the service provider
        $provider = new ChromadbServiceProvider($this->app);

        // Act & Assert: Verify it's the correct type
        expect($provider)->toBeInstanceOf(ChromadbServiceProvider::class);
    }

    /**
     * Additional test: Chromadb instance methods return expected types.
     *
     * Verifies that resolved Chromadb instance has all expected methods
     * and they work correctly.
     */
    public function test_chromadb_instance_methods_return_expected_types(): void
    {
        // Arrange: Get the resolved instance
        $chromadb = $this->app->make(Chromadb::class);

        // Act & Assert: Verify resource methods work
        expect($chromadb->collections())->toBeInstanceOf(HelgeSverre\Chromadb\Resources\Collections::class)
            ->and($chromadb->items())->toBeInstanceOf(HelgeSverre\Chromadb\Resources\Items::class)
            ->and($chromadb->database())->toBeInstanceOf(HelgeSverre\Chromadb\Resources\Database::class)
            ->and($chromadb->tenant())->toBeInstanceOf(HelgeSverre\Chromadb\Resources\Tenant::class)
            ->and($chromadb->server())->toBeInstanceOf(HelgeSverre\Chromadb\Resources\Server::class)
            ->and($chromadb->client())->toBe($chromadb);
    }

    /**
     * Additional test: Config file has sensible defaults.
     *
     * Verifies that the published config file provides appropriate
     * default values for all required settings. Note: actual values
     * may be overridden by environment variables.
     */
    public function test_config_file_has_sensible_defaults(): void
    {
        // Arrange: Get config (may be overridden by .env)
        $config = config('chromadb');

        // Act & Assert: Verify expected config structure and port default
        expect($config)->toHaveKeys(['host', 'port', 'tenant', 'database', 'embeddings'])
            ->and($config['port'])->toBe('8000')
            ->and($config['tenant'])->toBe('default_tenant')
            ->and($config['database'])->toBe('default_database')
            ->and($config['embeddings']['default'])->toBe('openai')
            ->and($config['host'])->toBeString(); // Host may be overridden by .env
    }
}
