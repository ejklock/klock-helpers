<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class CreateBladeFormCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'utils:blade-forms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new blade form components';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Blade form components';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $formTypes = ['delete', 'patch', 'post'];

        foreach ($formTypes as $formType) {
            $this->generateForm($formType);
        }

        $this->info('Blade form components created successfully.');
    }

    /**
     * Generate a form component.
     *
     * @param string $formType
     * @return void
     */
    protected function generateForm(string $formType)
    {
        $name = mb_strtolower($formType);
        $path = $this->getPath($name);

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->info("$name form component created successfully.");
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return "";
    }

    /**
     * Get the stub file for the generator based on form type.
     *
     * @param string $formType
     * @return string
     */
    protected function getFormStub(string $formType)
    {
        return __DIR__ . "/../stubs/blade-form-{$formType}.blade.stub";
    }

    protected function getPath($name)
    {
        return base_path() . "/resources/views/components/utils/forms/" . $name . ".blade.php";
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getFormStub(strtolower($name)));

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }
}
