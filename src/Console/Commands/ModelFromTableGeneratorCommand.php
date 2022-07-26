<?php

namespace KlockTecnologia\KlockHelpers\Console\Commands;

use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

class ModelFromTableGeneratorCommand extends BaseGeneratorCommand
{
    protected $name = 'domain:model-from-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Model from table';

    public function stub()
    {

        return '';
    }

    protected function getStub()
    {
        return null;
    }
    public function handle()
    {
        $normalizeName = \Str::singular(\Str::ucfirst(\Str::camel($this->argument('name'))));

        $separator = DIRECTORY_SEPARATOR === '\\' ? DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR : DIRECTORY_SEPARATOR; //Fix Windows

        $outputPath =
            (DIRECTORY_SEPARATOR === '\\' ? '' : '.') .
            $separator .
            'Domains' .
            $separator .
            $normalizeName .
            $separator .
            'Models' .
            $separator;

        $modelName = $normalizeName . 'Model';

        $pathModel =
            public_path() .
            DIRECTORY_SEPARATOR .
            '..' .
            DIRECTORY_SEPARATOR .
            'app' .
            DIRECTORY_SEPARATOR .
            'Domains' .
            DIRECTORY_SEPARATOR .
            $normalizeName .
            DIRECTORY_SEPARATOR .
            'Models' .
            DIRECTORY_SEPARATOR .
            $modelName .
            '.php';

        //Se já existe, não cria (skip)
        if (file_exists($pathModel) && !$this->option('force')) {
            echo 'Model already exist!';
            return;
        }

        try {
            \Artisan::call(
                'krlove:generate:model' .
                    ' ' .
                    $modelName .
                    ' --table-name=' .
                    $this->argument('name') .
                    ' --output-path=' .
                    $outputPath .
                    ' --no-timestamps',
            );
            echo \Artisan::output();

            $modelClass = 'App\Domains\\' . $normalizeName . '\Models\\' . $modelName;

            $this->normalizeMakeModels($pathModel, $normalizeName);

            include_once $pathModel;

            $this->setDeletedAt($pathModel, $modelClass)
                ->setTimestamp($pathModel, $modelClass)
                ->setOrderable($pathModel, $modelClass)
                ->setHasFactory($pathModel);

            $this->artisanModelRelationship($modelClass);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function artisanModelRelationship($modelClass)
    {
        $model = new $modelClass();

        $relationshipsNotfound = [];

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
                if ($return instanceof Relation) {
                    $reflection = new ReflectionClass($return);
                }
            } catch (\Throwable $e) {
                $relationshipsNotfound[] = $method->getName();
            }
        }

        foreach ($relationshipsNotfound as $k => $v) {
            \Artisan::call('base:model ' . \Str::snake($v));
            \Artisan::call('base:model ' . \Str::plural(\Str::snake($v)));
            \Artisan::call('base:model ' . \Str::plural($v));
            \Artisan::call('base:model ' . $v);
            echo \Artisan::output();
        }
    }

    /**
     * Normaliza os Models p/ padrão de Domínios
     */
    private function normalizeMakeModels($fullpath, $name)
    {
        $contentFileModel = file_get_contents($fullpath);

        $regexp = '/\'App\\\\(\w*)/';
        $matches = [];
        preg_match_all($regexp, $contentFileModel, $matches, PREG_SET_ORDER, 0);

        $separator = '\\';
        $namespace = 'App' . $separator . 'Domains' . $separator . $name . $separator . 'Models';

        foreach ($matches as $v) {
            $model = array_pop($v);

            $namespaceCustom = 'App' . $separator . 'Domains' . $separator . $model . $separator . 'Models';
            $contentFileModel = str_replace(
                "'App\\" . $model . "'",
                "'" . $namespaceCustom . $separator . $model . "Model'",
                $contentFileModel,
            );
        }

        //Namespace do topo
        $contentFileModel = str_replace('namespace App;', 'namespace ' . $namespace . ';', $contentFileModel);

        file_put_contents($fullpath, $contentFileModel);

        return $this;
    }

    private function setTimestamp($fullpath, $modelClass)
    {
        $model = new $modelClass();

        $list = ['created_at', 'createdAt', 'criadoEm', 'criado_em'];

        $found = false;
        foreach ($model->getFillable() as $k => $v) {
            if (array_search($v, $list) !== false) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $content = file_get_contents($fullpath);
            $content = str_replace('public $timestamps = false;', 'public $timestamps = true;', $content);
            file_put_contents($fullpath, $content);
        }

        return $this;
    }

    private function setDeletedAt($fullpath, $modelClass)
    {
        $model = new $modelClass();

        $list = ['deleted_at', 'deletedAt', 'removidoEm', 'removido_em'];

        $found = false;
        foreach ($model->getFillable() as $k => $v) {
            if (array_search($v, $list) !== false) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $deletedImport = "
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;";

            $useTrait = "{
    use SoftDeletes;
";
            $content = file_get_contents($fullpath);

            $content = str_replace('use Illuminate\Database\Eloquent\Model;', $deletedImport, $content);
            $content = str_replace_once('{', $useTrait, $content);

            file_put_contents($fullpath, $content);
        }

        return $this;
    }

    private function setHasFactory($fullpath)
    {
        $import = "use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;";

        $useTrait = "{
    use HasFactory;
        ";

        $content = file_get_contents($fullpath);
        $content = str_replace('use Illuminate\Database\Eloquent\Model;', $import, $content);
        $content = str_replace_once('{', $useTrait, $content);

        file_put_contents($fullpath, $content);
    }

    private function setOrderable($fullpath, $modelClass)
    {
        $model = new $modelClass();

        $list = ['order', 'ordem'];

        $found = false;
        foreach ($model->getFillable() as $k => $v) {
            if (array_search($v, $list) !== false) {
                $found = true;
                break;
            }
        }

        if ($found) {
            $sorterableImport = "
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use BaseCms\Models\BaseModelTrait;";

            $extendImplements = 'extends Model implements Sortable';

            $useTrait = "{
    use SortableTrait;
    use BaseModelTrait;
";

            $content = file_get_contents($fullpath);

            $content = str_replace('use Illuminate\Database\Eloquent\Model;', $sorterableImport, $content);
            $content = str_replace('extends Model', $extendImplements, $content);
            $content = str_replace_once('{', $useTrait, $content);

            file_put_contents($fullpath, $content);
        }

        return $this;
    }


    public function str_replace_once($str_pattern, $str_replacement, $string)
    {
        if (strpos($string, $str_pattern) !== false) {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }

        return $string;
    }
}
