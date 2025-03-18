# Run PHP code in parallel with the powerful Rust

This package allows you to run PHP code in parallel using the powerful Rust language.

**Note:** This package is still in development and not ready for production use.

## Demo

```php
use Parallel\Parallel;

$responses = Parallel::run([
    function () {
        return file_get_contents('https://www.google.com');
    },
    function () {
        return file_get_contents('https://www.bing.com');
    },
    function () {
        return file_get_contents('https://www.yahoo.com');
    },
]);

// or 

$results = Parallel::run([
    function () {
        // do something 1
        return 'done 1';
    },
    function () {
        // do something 2
        return 'done 2';
    },
]);

assert($responses[0] === 'done 1');
```