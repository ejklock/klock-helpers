<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ControllerGeneratorCommand extends GeneratorCommand
{
    protected $name = 'domain:controller';

    protected $description = 'Create service  on App/Domain/<model>/Controllers';

    protected $type = 'controller';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/controller.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return "App\\Domains\\{$this->getCamelName()}\\Controllers";
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    protected function replaceServiceClass($stub)
    {
        $dummyServiceClassType = $this->getCamelName() . "Service";

        return str_replace(['{{dummyServiceClassType}}', '{{ dummyServiceClassType}}'], $dummyServiceClassType, $stub);
    }

    protected function replaceDomainViewPath($stub)
    {
        $dummyDomainViewPath = Str::lower($this->getCamelName());

        return str_replace(['{{dummyDomainViewPath}}', '{{ dummyDomainViewPath}}'], $dummyDomainViewPath, $stub);
    }

    protected function relaceClassInstance($stub)
    {
        $classInstance = Str::lower($this->getCamelName());

        return str_replace(['{{classInstance}}', '{{ classInstance}}'], $classInstance, $stub);
    }

    protected function replaceServiceClassInstance($stub)
    {

        $dummyServiceInstance = Str::camel($this->getCamelName() . "Service");

        return str_replace(['{{dummyServiceInstance}}', '{{ dummyServiceInstance}}'], $dummyServiceInstance, $stub);
    }

    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');

            return false;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())
        ) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $file = $this->sortImports($this->buildClass($name));
        $file = $this->replaceServiceClassInstance($file);
        $file = $this->replaceServiceClass($file);
        $file = $this->replaceDomainViewPath($file);
        $file = $this->relaceClassInstance($file);

        $this->files->put($path, $file);

        $this->info($this->type . ' created successfully.');

        if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
            $this->handleTestCreation($path);
        }
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name) . "Controller";
        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function getNameInput()
    {
        return $this->getCamelName();
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    protected function replaceClass($stub, $name)
    {
        $class = $this->getCamelName();

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }
}
