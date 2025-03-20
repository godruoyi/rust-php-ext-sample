<?php

it('basic test', function () {
    if (! extension_loaded('rust_php_ext_sample')) {
        $this->markTestSkipped('The rust_php_ext_sample extension is not available.');
    }

    $hello = rust_php_ext_sample('World');

    expect($hello)->toBe('Hello World');
});
