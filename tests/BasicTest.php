<?php

test('Parallel\Parallel Class Exists', function () {
    expect(class_exists('Parallel\Parallel'))->toBeTrue();
});

test('Parallel\Parallel Class is Instantiable', function () {
    $parallel = new Parallel\Parallel;
    expect($parallel)->toBeInstanceOf('Parallel\Parallel');
});

test('Parallel\Parallel Class has a run method', function () {
    $parallel = new Parallel\Parallel;
    expect(method_exists($parallel, 'run'))->toBeTrue();
});

test('Parallel\Parallel Run', function () {
    $parallel = new Parallel\Parallel;

    $result = $parallel->run([
        fn () => 'Hello',
        fn () => 'Hello1',
        fn () => 'Hello2',
        fn () => 'Hello3',
        fn () => 'Hello4',
    ]);

    expect($result)->toBeArray()->dump();
});
