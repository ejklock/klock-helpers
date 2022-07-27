<?php

namespace KlockTecnologia\KlockHelpers\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use KlockTecnologia\KlockHelpers\Tests\TestCase;

class CommandGenertorTest extends TestCase
{
    public function testDryModelGeneration()
    {

        $fooClass = app_path('Domains/CourseCategory/Models/CourseCategory.php');

        if (File::exists($fooClass)) {
            unlink($fooClass);
        }

        $this->assertFalse(File::exists($fooClass));

        $this->artisan('domain:model', ['name' => 'course_categories']);

        $this->assertTrue(File::exists($fooClass));

        $expectedContents = file_get_contents(__DIR__ . '/CourseCategory.php');

        $this->assertEquals($expectedContents, file_get_contents($fooClass));
    }
}
