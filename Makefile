.PHONY: help install dev test test-laravel clean

# Colors
GREEN  := \033[0;32m
YELLOW := \033[0;33m
RESET  := \033[0m

help: ## Show this help message
	@echo "$(GREEN)Available commands:$(RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-12s$(RESET) %s\n", $$1, $$2}'

install: ## Install dependencies
	@composer install

dev: ## Start ChromaDB in Docker
	@docker compose up -d

test: ## Run tests
	@composer test

test-laravel: ## Test Laravel integration across versions 10, 11, 12
	@./test-laravel-install.sh

clean: ## Stop Docker, remove volumes and test directories
	@docker compose down -v
	@rm -rf wip
