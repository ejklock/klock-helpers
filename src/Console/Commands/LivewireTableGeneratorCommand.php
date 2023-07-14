<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class LivewireTableGeneratorCommand extends GeneratorCommand
{
    protected $name = 'domain:livewire-table';

    protected $description = 'Create a Livewire DataTableComponent for a domain';

    protected $type = 'LivewireTable';

    protected function getStub()
    {
        return __DIR__ . '/../stubs/livewire-table.stub';
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $namespace = $this->getNamespace($name);

        $stub = str_replace('DummyNamespace', $namespace, $stub);

        return $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return "App\\Http\\Livewire";
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

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $name = Str::replaceLast($this->getCamelName(), $this->getCamelName() . 'Table', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function getNameInput()
    {
        return $this->getCamelName() . 'Table';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name);

        $stub = $this->replaceModel($stub, $this->getCamelName());

        $stub = $this->replaceClass($stub, $this->getCamelName() . 'Table');

        $modelClassName = "App\\Domains\\" . $this->getCamelName() . "\\Models\\" . $this->getCamelName();
        $columns = $this->getPropertiesFromModel($modelClassName);
        $stub = $this->replaceColumns($stub, $columns);

        $stub = $this->replaceRowView($stub, $this->getLowerCaseSingularName());

        return $stub;
    }

    protected function replaceModel($stub, $model)
    {
        return str_replace('DummyModel', $model, $stub);
    }

    protected function replaceClass($stub, $name)
    {
        return str_replace(['DummyModelTable', 'DummyClass', '{{ class }}', '{{class}}'], $name, $stub);
    }

    protected function replaceRowView($stub, $name)
    {
        return str_replace('dummyclass_singular', $name, $stub);
    }

    protected function replaceColumns($stub, $columns)
    {
        return str_replace('{{columns}}', $columns, $stub);
    }

    protected function getPropertiesFromModel($modelClassName)
    {
        $model = new $modelClassName;
        $fillableProperties = $model->getFillable();

        $columns = [];
        foreach ($fillableProperties as $property) {
            $columns[] = "Column::make('{$property}')->sortable()->searchable()";
        }

        // Add two tab indentations to each line
        $indentedColumns = array_map(function ($column) {
            return "\t\t" . $column;
        }, $columns);

        return implode(",\n", $indentedColumns);
    }
}
