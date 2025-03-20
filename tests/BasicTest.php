<?php

it('basic test', function () {
    $hello = rust_php_ext_sample('World');

    expect($hello)->toBe('Hello, World!');
});
