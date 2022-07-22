<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;


class BaseGeneratorCommand extends GeneratorCommand
{

    protected function getStub()
    {

        return '';
    }

    /**
     * Normaliza o path do arquivo "app/Domain/@NOME@/@TIPO@/@NOME_TIPO@
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);

        return $this->laravel['path'] . '/Domains/' . $this->getNameTable() . $this->path . $this->getNameTable() . $this->endFile;
    }

    protected function getNameTable()
    {
        return \Str::singular(\Str::ucfirst(\Str::camel($this->argument('name'))));
    }

    /**
     * Normaliza o nome passado
     */
    protected function getNameInput()
    {
        return $this->getNameTable();
    }

    /**
     * Normaliza o nome passado
     */
    protected function getCamelName()
    {
        return \Str::singular(\Str::camel($this->argument('name')));
    }

    /**
     * Normaliza o Namespace
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Domains\\' . $this->getNameTable();
    }

    /**
     * Classe do Model
     */
    public function getModelClass()
    {
        $model = 'App\Domains\\' . $this->getNameTable() . '\Models\\' . $this->getNameTable() . 'Model';
        return  $model;
    }

    public function getRelationshipsFields($modelClass)
    {
        $model = new $modelClass;

        $relationships = [];

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__
            ) {
                continue;
            }

            $relationships[] = $method->getName();
            $relationships[] = $method->getName() . 'id';
            $relationships[] = $method->getName() . 'Id';
            $relationships[] = \Str::plural($method->getName()) . 'Id';
            $relationships[] = \Str::plural($method->getName()) . '_id';
            $relationships[] = \Str::snake($method->getName()) . '_id';
        }

        return $relationships;
    }
    /**
     *
     * Reflection no Model p/ extrair os relantionships.
     *
     * Todos Models relacionados precisam estar criados p/ este método funcionar
     */
    public function getRelationships()
    {
        $modelClass = $this->getModelClass();

        $model = new $modelClass;

        $relationships = [];

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__
            ) {
                continue;
            }

            try {
                $return = $method->invoke($model);

                // dd(get_class_methods($return));
                if ($return instanceof Relation) {
                    $reflection = new ReflectionClass($return);

                    $related = $return->getRelated();
                    $relatedAttributes = $this->getAttributes($related);

                    $firstColumnString = "name";
                    foreach ($relatedAttributes as $k => $v) {
                        $type = \Schema::getColumnType($related->getTable(), $v);
                        if ($type == 'string') {
                            $firstColumnString = $v;
                            break;
                        }
                    }

                    $relationships[$return->getForeignKeyName()] = [ //nome da coluna do banco
                        'methodName' => $method->getName(),
                        'foreignKey' => $return->getForeignKeyName(),
                        'ownerKey' => $return->getOwnerKeyName(),
                        'relationName' => $return->getRelationName(),
                        'related' => $return->getRelated(),
                        'relatedClass' => get_class($return->getRelated()),
                        'relatedFirstColumnString' => $firstColumnString,
                        'type' => $reflection->getShortName(),
                        'model' => $reflection->getName()
                    ];

                    // dd($relationships);
                }
            } catch (\Throwable $e) {
                // echo 'Foi encontrado um relacionamento que ainda não foi criado. Você precisa executar antes o comando: base:model ' . $method->getName();
            }
        }

        return $relationships;
    }

    /**
     *  Label pt-BR
     */
    public function translateLabel($label)
    {
        $translate = file_get_contents(__DIR__ . '/../stubs/translate.txt');

        $translate = explode("\n", $translate);
        foreach ($translate as $v) {
            $n = explode("|", $v);

            if (@$n[0] == Str::lower($label)) {
                return trim(ucfirst($n[1]));
            }
        }

        $label = str_replace(array("_", "-"), " ", \Str::snake($label));

        return \Str::ucfirst($label);
    }


    /**
     * Retorno da primary key
     */
    public function getPrimaryKey()
    {
        return 'id';
    }

    /**
     * Atributos do model default da tabela passada
     */
    public function getAttributesModel()
    {
        $model = $this->getModelClass();

        if (!class_exists($model)) {
            return [];
        }

        return $this->getAttributes($model);
    }

    protected function getAttributes($model)
    {
        $attributes = [];
        $model = new $model;

        if (!count($model->getFillable())) {
            $attributes = $model->getFillable();
        } else {
            $attributes = \Schema::getColumnListing($model->getTable());
        }

        return $attributes;
    }

    /**
     * Replace default p/ dummys gerais dos .stub
     */
    protected function replaceNameStrings(&$stub, $name)
    {
        $table = $this->argument('name');
        $name =  $this->getNameTable();

        $stub = str_replace('dummy_route', $table, $stub);
        $stub = str_replace('dummy_report_route', $table . '_report', $stub);
        $stub = str_replace('dummy_table', $table, $stub);

        $stub = str_replace('dummy_title', $name, $stub);

        $stub = str_replace('DummyModel', $name . 'Model', $stub);
        $stub = str_replace('DummyRequest', $name . 'Request', $stub);
        $stub = str_replace('DummyCreateRequest', $name . 'CreateRequest', $stub);
        $stub = str_replace('DummyUpdateRequest', $name . 'UpdateRequest', $stub);
        $stub = str_replace('DummyPolicy', $name . 'Policy', $stub);
        $stub = str_replace('DummyRepository', $name . 'Repository', $stub);
        $stub = str_replace('DummyDefinition', $name . 'Definition', $stub);
        $stub = str_replace('DummyController', $name . 'Controller', $stub);

        $stub = str_replace('DummyReportController', $name . 'ReportController', $stub);
        $stub = str_replace('DummyReportDefinition', $name . 'ReportDefinition', $stub);

        $stub = str_replace('$model', '$' . $this->getCamelName(), $stub);
        $stub = str_replace('$thismodel', '$this->' . $this->getCamelName(), $stub);
        $stub = str_replace("'model'", "'" . $this->getCamelName() . "'", $stub);

        foreach ($this->injectParams as $k => $v) {
            $stub = str_replace($k, $v, $stub);
        }

        return $this;
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $this->replaceNamespace($stub, $name)
            ->replaceNameStrings($stub, $name);

        return $stub;
    }

    protected function getOptions()
    {
        return [];
    }
}
