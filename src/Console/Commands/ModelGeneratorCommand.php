<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;

use Illuminate\Support\Str;

class ModelGeneratorCommand extends GeneratorCommand
{

    private $model, $baseNamespace;

    protected $name = 'domain:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/model.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Models')) ? $rootNamespace . '\\Models' : $rootNamespace;
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    protected function getNamespace($name)
    {
        return trim("App\\Domains\\{$this->getCamelName()}\\Models");
    }


    public function handle()
    {
        dd($this->getNamespace(''));
    }
}
