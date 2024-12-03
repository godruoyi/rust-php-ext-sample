#

## Demo

```php
use Parallel\Parallel;
use GuzzleHttp\Psr7\Request;

$responses = Parallel::run([
    new Request('GET', 'https://httpbin.org/delay/1'),
    new Request('GET', 'https://httpbin.org/delay/2'),
    new Request('GET', 'https://httpbin.org/delay/3'),
]);
```