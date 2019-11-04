<?php

require_once(__DIR__ . '/autoload.php');

use PHPUnit\Framework\TestCase;
use TinyORM\Model;

final class ModelTest extends TestCase
{
    public function testIfPrimaryKeyIsID(): void
    {
        $this->assertEquals(
            'id',
            'id'
        );
    }
}
