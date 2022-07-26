<?php

namespace KlockTecnologia\KlockHelpers\Tests\Unit\Commands;


use KlockTecnologia\KlockHelpers\Tests\TestCase;

class CommandGenertorTest extends TestCase
{


    public function testDoma()
    {

        $this->artisan('domain:model', ['name' => 'course_categories']);
    }
}
