<?php

namespace KlockTecnologia\KlockHelpers\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use KlockTecnologia\KlockHelpers\Tests\TestCase;

class CommandGenertorTest extends TestCase
{
    public function testDryModelGeneration()
    {

        $fooClass = app_path('Domains/Dummy/Models/Dummy.php');

        if (File::exists($fooClass)) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        $this->artisan('domain:model', ['name' => 'dummy']);

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(__DIR__ . '/DummyClasses/Dummy.php');

        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }

    public function testServiceGeneration()
    {

        $fooClass = app_path('Domains/Dummy/Services/DummyService.php');

        if (File::exists($fooClass)) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        $this->artisan('domain:service', ['name' => 'dummy']);

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(__DIR__ . '/DummyClasses/DummyService.php');

        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }

    public function testControllerGeneration()
    {

        $fooClass = app_path('Domains/Dummy/Controllers/DummyController.php');

        if (File::exists($fooClass)) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        $this->artisan('domain:controller', ['name' => 'dummy']);

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(__DIR__ . '/DummyClasses/DummyController.php');



        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }
}
