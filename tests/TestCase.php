<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
    protected function browserContextOptions(): array {
        return ['ignoreHTTPSErrors' => true];
    }
}
