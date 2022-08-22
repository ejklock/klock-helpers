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

        $this->argument('dm') ? $this->createModel() : $this->createModelFromTable();
        $this->createController();
        $this->createService();
    }

    protected function getDomainNamespace()
    {
        return "App\\Domains\\{$this->getCamelName()}\\Models";
    }

    protected function createModelFromTable()
    {

        $this->call('krlove:generate:model', [
            'class-name' => $this->getCamelName(),
            'tn' => $this->argument('name'),
            'ns' => $this->getDomainNamespace()

        ]);
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
            ['dry-model', 'dm', InputOption::VALUE_NONE, 'Create a new Domain with drymodel (not from table)'],
        ];
    }
}
