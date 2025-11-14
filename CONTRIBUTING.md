# Contributing

Thanks for your interest in contributing! We welcome contributions of all kinds and aim to make the process straightforward.

## Quick Start

```bash
# Fork and clone the repository
git clone https://github.com/YOUR-USERNAME/chromadb.git
cd chromadb

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Start ChromaDB with Docker
docker compose up -d

# Run tests
composer test
```

## Development Tools

We use modern PHP tooling to maintain code quality:

```bash
composer test              # Run PHPUnit tests
composer test:coverage     # Run tests with coverage report
composer analyse           # Run PHPStan static analysis
```

## Code Standards

- **PHP 8.2+** features encouraged
- **PSR-12** coding standard
- **PHPStan** for type safety
- **Tests required** for all changes

## Automated Checks

We run automated checks on every PR via GitHub Actions:

- **Tests**: Full test suite runs on PHP 8.2 and 8.3 against a real ChromaDB instance
- **Static Analysis**: PHPStan checks for type safety and potential bugs
- **Code Formatting**: Laravel Pint verifies PSR-12 compliance

All checks must pass before a PR can be merged. You can run these locally before pushing:

```bash
composer test      # Run tests
composer analyse   # Run PHPStan
vendor/bin/pint    # Auto-fix code formatting
```

## Making Changes

1. Create a feature branch: `git checkout -b feature/my-feature`
2. Write your code
3. Add tests covering your changes
4. Ensure all tests pass: `composer test`
5. Verify code quality: `composer analyse`
6. Commit with clear messages
7. Push and open a pull request

## Using AI Tools

We encourage the use of AI development tools (Claude Code, GitHub Copilot, etc.) as part of your workflow. When using AI assistance:

- **Verify correctness**: Ensure generated code works as intended
- **Include tests**: Test coverage proves the code behaves correctly
- **Check the spec**: For API client changes, verify against the [ChromaDB v2 API docs](https://docs.trychroma.com/)
- **Review output**: Understand what the AI generated before submitting

If the tests pass and the code is correct, we don't care how you wrote it.

## Pull Request Guidelines

- One feature or fix per PR
- Include tests demonstrating the change works
- Update documentation if needed (README, docblocks)
- Reference related issues with `Fixes #123`
- Keep commits clean and meaningful

We typically review PRs within a few days.

## Testing Philosophy

We value meaningful tests that verify behavior:

- Test the happy path and edge cases
- Make tests readable and maintainable
- Use descriptive test names that explain what's being tested
- Don't test framework code, test your logic

## What We're Looking For

Good contributions to consider:

- Bug fixes with tests proving the fix
- New features with documentation and tests
- Documentation improvements
- Examples and usage patterns
- Performance improvements with benchmarks

## Need Help?

- **Questions**: Open an issue labeled "question"
- **Bugs**: Create an issue with reproduction steps
- **Feature ideas**: Open an issue to discuss before implementing
- **Security**: Email the maintainer privately (don't open public issues)

## API Changes

When adding or modifying API client methods:

1. Check the [ChromaDB v2 API specification](https://docs.trychroma.com/)
2. Match parameter names and types to the official API
3. Include examples in docblocks
4. Add integration tests that run against a real ChromaDB instance

### Accessing the OpenAPI Specification

The OpenAPI specification is served by the running ChromaDB Docker container. After starting the container with `docker compose up -d`, you can access:

- **Raw JSON spec**: `http://localhost:8000/openapi.json`
- **Interactive Swagger UI**: `http://localhost:8000/docs/`

```bash
# Fetch the latest OpenAPI spec
curl http://localhost:8000/openapi.json | jq . > chromadb-latest.json

# Or just view it in your browser
open http://localhost:8000/docs/
```

The spec is also stored in this repository as `chromadb.v2.json` for reference.

## License

By contributing, your work becomes part of this project under the MIT License.

---

Happy coding! We appreciate your contribution to making this ChromaDB client better.
