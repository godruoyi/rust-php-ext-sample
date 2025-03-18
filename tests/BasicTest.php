<?php

it('basic test', function () {
    if (! class_exists('Parallel\Parallel')) {
        $this->markTestSkipped('This test requires the parallel extension');
    }

    $parallel = new Parallel\Parallel;

    $this->assertInstanceof(Parallel\Parallel::class, $parallel);
    $this->assertObjectHasProperty('test', $parallel);
    $this->assertEquals('test', $parallel->test);

    $parallel->test = 'test2';
    $this->assertEquals('test2', $parallel->test);

    $result = $parallel->run(1);

    $this->assertIsArray($result);
});
