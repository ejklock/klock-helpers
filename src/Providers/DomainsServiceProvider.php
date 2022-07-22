<?php

namespace KlockTecnologia\KlockHelpers\Providers;

use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DomainsServiceProvider extends ServiceProvider
{
    protected $defer = false;
    public function boot()
    {
        $rdi = new RecursiveDirectoryIterator(app_path('Domains' . DIRECTORY_SEPARATOR));
        $it = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST);
        while ($it->valid()) {

            if (
                !$it->isDot() &&
                $it->isFile() &&
                $it->isReadable() &&
                strpos($it->current()->getPath(), 'Providers') &&
                $it->current()->getExtension() === 'php' &&
                strpos($it->current()->getFilename(), 'Provider')
            ) {
                require $it->key();

                $providerPath = str_replace('/', '\\', $it->getSubpath()) . str_replace($it->getFileName(), '.php', '') . '\\' . preg_replace('/\\.[^.\\s]{3,4}$/', '', $it->getFileName());

                $this->app->register("App\\Domains\\{$providerPath}");
            }

            $it->next();
        }
    }
}
