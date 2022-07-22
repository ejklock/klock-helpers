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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->model = $this->argument('name');
        $this->baseNamespace = 'App/Domains/' . $this->argument('name');

        if ($this->option('model')) {
            $this->model = $this->option('model');
        }

        if (!$this->option('no-model')) {
            $this->createModel();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('controller') || $this->option('resource')) {
            $this->createController();
        }

        return 0;
    }

    protected function createModel()
    {
        $modelNamespace = $this->baseNamespace . '/Models/';

        $this->call('make:model', [
            'name' => $modelNamespace . $this->model
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

    protected function createController()
    {
        $controllerNamespace = $this->baseNamespace . '/Http/Controllers/';
        $controller = $controllerNamespace . 'Backend/' . Str::studly(class_basename($this->model));

        $modelName = 'App/Domains/' . $this->argument('name') . '/Models/' . $this->model;

        $this->call('make:controller', array_filter([
            'name' => "{$controller}Controller",
            '--model' => $this->option('resource') && $this->alreadyExists($modelName) ? $modelName : null,
            '--resource' => $this->option('resource') && !$this->alreadyExists($modelName) ? true : null,
        ]));

        if ($this->option('frontend')) {
            $frontendController = $controllerNamespace . 'Frontend/' . Str::studly(class_basename($this->model));

            $this->call('make:controller', array_filter([
                'name' => "{$frontendController}Controller",
                '--model' => $this->option('resource') && $this->alreadyExists($modelName) ? $modelName : null,
                '--resource' => $this->option('resource') && !$this->alreadyExists($modelName) ? true : null,
            ]));
        }
    }

    protected function getStub()
    {
        return $this->option('pivot')
            ? $this->resolveStubPath('/stubs/model.pivot.stub')
            : $this->resolveStubPath('/stubs/model.stub');
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Models')) ? $rootNamespace . '\\Models' : $rootNamespace;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
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
