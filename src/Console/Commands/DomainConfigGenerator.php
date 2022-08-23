<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;


class DomainConfigGenerator extends GeneratorCommand
{
    protected $name = 'domain:config';

    protected $description = 'Create config file on App/Domain/<model>/Config';

    protected $type = 'Config';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/domain-config.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return "App\\Domains\\{$this->getCamelName()}\\Config";
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    protected function getLowerCaseSingularName()
    {
        return Str::lower(Str::singular($this->argument('name')));
    }

    public function handle()
    {
        parent::handle();
    }

    protected function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }


    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $name = $this->str_lreplace($this->getCamelName(), $this->getLowerCaseSingularName(), $name);

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

        return  str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
    }
}
