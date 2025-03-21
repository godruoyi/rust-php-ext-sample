<?php

namespace Godruoyi\Composer;

function currentPHPVersion(): string
{
    return phpversion();
}

function currentOS(): string
{
    return PHP_OS;
}
