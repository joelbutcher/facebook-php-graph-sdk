# Facebook PHP Graph SDK - AI Agent Guide

This document provides comprehensive information for AI coding assistants working with this codebase.

## Project Overview

The Facebook PHP Graph SDK is a PHP library for interacting with the Facebook Graph API. This is version 8.x, which requires PHP 8.4+ and follows modern PHP best practices.

## Architecture

### Core Components

- **`Facebook`**: Main entry point class that orchestrates all SDK functionality
- **`Client`**: HTTP client implementation for making requests to the Graph API
- **`Request`**: Represents a single API request
- **`Response`**: Represents a single API response
- **`Application`**: Represents a Facebook application (app ID and secret)
- **`AccessToken`**: Represents an OAuth access token

### Key Directories

- `src/Authentication/`: OAuth 2.0 authentication logic
- `src/GraphNode/`: Graph API response data models
- `src/Helper/`: Helper classes for common scenarios (login, canvas, etc.)
- `src/FileUpload/`: File and video upload handling
- `src/Http/`: HTTP request/response body implementations
- `src/PersistentData/`: Session/persistent data handling
- `src/Url/`: URL manipulation utilities
- `src/Exception/`: Custom exception classes

## Coding Standards

### PHP Version
- **Minimum**: PHP 8.4
- Use modern PHP 8.4+ features (readonly properties, union types, etc.)

### Code Style
- Follow **PSR-12** coding standards
- Use **strict types**: `declare(strict_types=1);` in all production files
- Use **PSL functions** over native PHP functions where possible:
  - `Psl\Dict\merge()` instead of `array_merge()`
  - `Psl\Str\format()` instead of `sprintf()`
  - `Psl\Str\join()` instead of `implode()`
  - `Psl\Str\split()` instead of `explode()`
  - `Psl\Iter\count()` instead of `count()` for iterables
  - `Psl\Env\get_var()` instead of `getenv()`
  - `Psl\Str\uppercase()` instead of `strtoupper()`

### Type System
- Add return type declarations to all methods
- Add parameter type declarations
- Use union types (`|`) and intersection types (`&`) where appropriate
- Use `readonly` properties where applicable
- Prefer type declarations over docblock types

### SOLID Principles
- **Depend on interfaces, not implementations**
- Key interfaces:
  - `Facebook\Http\ClientInterface`: HTTP client abstraction
  - `Facebook\PersistentData\PersistentDataInterface`: Persistent data storage
  - `Facebook\Url\UrlDetectionInterface`: URL detection logic
  - `Facebook\Http\RequestBodyInterface`: Request body implementations

### Testing
- **Testing Pyramid**: More unit tests than feature tests
- Run tests with: `composer test:unit`
- Run static analysis with: `composer test:types`
- Use PHPUnit 12.x conventions
- Test methods must be `public` and return `void`
- Data providers must be `public static` methods

## Customization Points

### HTTP Client
Implement `Facebook\Http\ClientInterface` to use a custom HTTP client:

```php
use Facebook\Http\ClientInterface;
use Facebook\Request;
use Facebook\Response;
use Facebook\BatchRequest;

class MyCustomClient implements ClientInterface
{
    public function prepareRequestMessage(Request $request): array { /* ... */ }
    public function sendRequest(Request $request): Response { /* ... */ }
    public function sendBatchRequest(BatchRequest $request): \Facebook\BatchResponse { /* ... */ }
}
```

### Persistent Data Handler
Implement `Facebook\PersistentData\PersistentDataInterface` for custom session handling:

```php
use Facebook\PersistentData\PersistentDataInterface;

class MyPersistentDataHandler implements PersistentDataInterface
{
    public function get($key) { /* ... */ }
    public function set($key, $value): void { /* ... */ }
}
```

### URL Detection
Implement `Facebook\Url\UrlDetectionInterface` for custom URL detection:

```php
use Facebook\Url\UrlDetectionInterface;

class MyUrlDetectionHandler implements UrlDetectionInterface
{
    public function getCurrentUrl(): string { /* ... */ }
}
```

## Common Patterns

### Creating a Facebook Instance

```php
use Facebook\Facebook;

$fb = new Facebook([
    'app_id' => 'your-app-id',
    'app_secret' => 'your-app-secret',
    'default_graph_version' => 'v18.0',
    'http_client' => new MyCustomClient(), // Optional
    'persistent_data_handler' => new MyPersistentDataHandler(), // Optional
    'url_detection_handler' => new MyUrlDetectionHandler(), // Optional
]);
```

### Making API Requests

```php
// GET request
$response = $fb->get('/me', $accessToken);

// POST request
$response = $fb->post('/me/feed', ['message' => 'Hello!'], $accessToken);

// DELETE request
$response = $fb->delete('/me/permissions', [], $accessToken);
```

### Working with Graph Nodes

```php
$user = $response->getGraphNode('Facebook\GraphNode\GraphUser');
$name = $user->getName();
$email = $user->getEmail();
```

### Pagination

```php
$edge = $response->getGraphEdge();
$nextPage = $fb->next($edge);
$previousPage = $fb->previous($edge);
```

## File Structure

```
src/
├── Application.php              # Facebook app entity
├── Client.php                   # HTTP client implementation
├── Facebook.php                 # Main SDK class
├── Request.php                  # API request entity
├── Response.php                 # API response entity
├── BatchRequest.php             # Batch request entity
├── BatchResponse.php            # Batch response entity
├── Authentication/              # OAuth 2.0 authentication
├── Exception/                   # Custom exceptions
├── FileUpload/                  # File/video upload handling
├── GraphNode/                   # Graph API data models
├── Helper/                      # Helper classes
├── Http/                        # HTTP implementations
├── PersistentData/              # Session/data persistence
└── Url/                         # URL utilities
```

## Development Workflow

1. **Make changes** to source files
2. **Run tests**: `composer test:unit`
3. **Check types**: `composer test:types`
4. **Check code quality**: `composer rector:check`
5. **Commit** with descriptive messages

## Key Files to Understand

- `src/Facebook.php`: Main entry point, orchestrates all functionality
- `src/Client.php`: HTTP client implementation
- `src/Request.php`: Request building and validation
- `src/Response.php`: Response parsing and error handling
- `src/Authentication/OAuth2Client.php`: OAuth 2.0 flow implementation

## Extension Points

When adding new features:
1. **Create interfaces first** for customizability
2. **Inject dependencies** via constructors
3. **Use dependency injection** throughout
4. **Follow SOLID principles**
5. **Add comprehensive tests**

## Notes for AI Agents

- Always check existing interfaces before creating new abstractions
- Prefer composition over inheritance
- Use PSL functions for better type safety and error handling
- Maintain backward compatibility considerations (though v8.0 is a major version)
- Test thoroughly after any changes
- Follow the commit strategy: commit logical groups of changes regularly

