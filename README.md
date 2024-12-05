#

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
```