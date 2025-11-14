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
    @rm -rf volumes
    @rm -rf .phpunit.cache
    @rm -rf build
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

[group('test')]
[doc('Test GitHub Actions workflow locally with act (requires act tool)')]
act-pr:
    @echo "Running GitHub Actions CI workflow locally..."
    act pull_request --job tests -P ubuntu-latest=catthehacker/ubuntu:act-latest --matrix php:8.3

[group('test')]
[doc('Test GitHub Actions code quality checks locally with act')]
act-quality:
    @echo "Running code quality checks locally..."
    act pull_request --job code-quality -P ubuntu-latest=catthehacker/ubuntu:act-latest

[group('test')]
[doc('Test all GitHub Actions workflows locally')]
act-all:
    @echo "Running all GitHub Actions workflows locally..."
    act pull_request -P ubuntu-latest=catthehacker/ubuntu:act-latest

# === Development Workflows ===

[group('workflow')]
[doc('Full development cycle: start docker, install deps, run tests')]
workflow: dev install test
    @echo "Development workflow complete!"

[group('workflow')]
[doc('Quick test cycle: ensure docker is running and run tests')]
quick: dev test
    @echo "Quick test cycle complete!"