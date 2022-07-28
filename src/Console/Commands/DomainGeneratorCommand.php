<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Support\Str;

use Symfony\Component\Console\Input\InputOption;

class DomainGeneratorCommand extends BaseGeneratorCommand
{
    private $model, $baseNamespace;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'domain:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Domain Model/Controller/Migration';

    public function stub()
    {
        return '';
    }

    protected function getCamelName()
    {
        return ucwords(Str::singular(Str::camel($this->argument('name'))));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createModel();
        $this->createController();
        $this->createService();
    }

    protected function createModel()
    {
        $this->call('domain:model', [
            'name' => $this->getCamelName()
        ]);
    }

    protected function createController()
    {
        $this->call('domain:controller', [
            'name' => $this->getCamelName()
        ]);
    }

    protected function createService()
    {
        $this->call('domain:service', [
            'name' => $this->getCamelName()
        ]);
    }

    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->model)));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    protected function getOptions()
    {
        return [
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['no-model', 'N', InputOption::VALUE_NONE, 'Create a domain without a model'],
            ['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],
            ['frontend', 'f', InputOption::VALUE_NONE, 'Indicates if the generated controller should create a frontend controller'],
            ['model', 'M', InputOption::VALUE_OPTIONAL, 'Create a new model on domain'],
        ];
    }
}
