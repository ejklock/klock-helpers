<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CreateBladeCardCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'utils:blade-card';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade card component';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Blade card component';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . "/../stubs/blade-card.stub";
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return 'card';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {

        return base_path() . '/resources/views/components/utils/card.blade.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\resources\views\components\utils';
    }
}
