<?php

namespace KlockTecnologia\KlockHelpers\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use KlockTecnologia\KlockHelpers\Tests\TestCase;

class CommandGenertorTest extends TestCase
{

    const DUMMY_CLASS_MODEL_PATH = __DIR__ . '/DummyClasses/Dummy.php';
    const DUMMY_CLASS_CONTROLLER_PATH = __DIR__ . '/DummyClasses/DummyController.php';
    const DUMMY_CLASS_SERVICE_PATH = __DIR__ . '/DummyClasses/DummyService.php';

    const GENERATED_DOMAIN_MODEL_PATH = 'Domains/Dummy/Models/Dummy.php';
    const GENERATED_DOMAIN_CONTROLLER_PATH = 'Domains/Dummy/Controllers/DummyController.php';
    const GENERATED_DOMAIN_SERVICE_PATH = 'Domains/Dummy/Services/DummyService.php';

    public function testDryModelGeneration($runningFromAnotherTest = false)
    {

        $fooClass = app_path(self::DUMMY_CLASS_MODEL_PATH);


        if (File::exists($fooClass) && !$runningFromAnotherTest) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        if (!$runningFromAnotherTest) {
            $this->artisan('domain:model', ['name' => 'dummy']);
        }

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(self::GENERATED_DOMAIN_MODEL_PATH);

        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }

    @skip
    public function testServiceGeneration($runningFromAnotherTest = false)
    {

        $fooClass = app_path(self::DUMMY_CLASS_SERVICE_PATH);

        if (File::exists($fooClass) && $runningFromAnotherTest) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        if (!$runningFromAnotherTest) {

            $this->artisan('domain:service', ['name' => 'dummy']);
        }

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(self::GENERATED_DOMAIN_SERVICE_PATH);

        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }


    public function testControllerGeneration($runningFromAnotherTest = false)
    {

        $fooClass = app_path(self::DUMMY_CLASS_CONTROLLER_PATH);

        if (File::exists($fooClass) && $runningFromAnotherTest) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        if (!$runningFromAnotherTest) {
            $this->artisan('domain:controller', ['name' => 'dummy']);
        }

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(self::GENERATED_DOMAIN_CONTROLLER_PATH);

        dd($expectedContents, file_get_contents($fooClass));


        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }

    public function testGenerateCompleteDomain()
    {

        // $this->artisan('domain:make', ['name' => 'dummy']);
        // $this->testDryModelGeneration(true);
        // $this->testControllerGeneration(true);
        // $this->testServiceGeneration(true);
    }
}
