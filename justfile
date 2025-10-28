# ChromaDB PHP Client - Development Commands
# https://just.systems/man/en/

# Load environment variables from .env if present
set dotenv-load := true

# Show available commands by default (first recipe is default)
help:
    @just --list

# === Setup ===

[group('setup')]
[doc('Install PHP dependencies')]
install:
    @echo "Installing dependencies..."
    composer install

# === Docker ===

[group('docker')]
[doc('Start ChromaDB in Docker')]
dev:
    @echo "Starting ChromaDB in Docker..."
    docker compose up -d

[group('docker')]
[doc('Stop Docker and remove volumes')]
clean:
    @echo "Stopping Docker and cleaning up..."
    docker compose down -v
    @rm -rf wip
    @echo "Cleanup complete!"

# === Testing ===

[group('test')]
[doc('Run all tests')]
test:
    @echo "Running tests..."
    composer test

[group('test')]
[doc('Test Laravel integration across versions 10, 11, 12')]
test-laravel:
    @echo "Testing Laravel integration..."
    ./test-laravel-install.sh

# === Development Workflows ===

[group('workflow')]
[doc('Full development cycle: start docker, install deps, run tests')]
workflow: dev install test
    @echo "Development workflow complete!"

[group('workflow')]
[doc('Quick test cycle: ensure docker is running and run tests')]
quick: dev test
    @echo "Quick test cycle complete!"