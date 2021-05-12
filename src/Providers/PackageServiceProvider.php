<?php

namespace Hexadog\Auditable\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier.
     */
    public const PACKAGE_DIR = __DIR__.'/../../';

    /**
     * Name for this package to publish assets.
     */
    public const PACKAGE_NAME = 'auditable';

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->loadViewsFrom($this->getPath('resources/views'), $this->getNormalizedNamespace());

        $this->strapPublishers();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->registerConfigs();

        $this->app->register(EloquentServiceProvider::class);
    }

    /**
     * Get Package absolute path.
     *
     * @param string $path
     */
    protected function getPath($path = '')
    {
        // We get the child class
        $rc = new ReflectionClass(get_class($this));

        return dirname($rc->getFileName()).'/../../'.$path;
    }

    /**
     * Get Module normalized namespace.
     *
     * @param mixed $prefix
     */
    protected function getNormalizedNamespace($prefix = '')
    {
        return Str::start(Str::lower(self::PACKAGE_NAME), $prefix);
    }

    /**
     * Register our Configs.
     */
    protected function registerConfigs(): void
    {
        $configPath = $this->getPath('config');

        $this->mergeConfigFrom(
            "{$configPath}/config.php",
            $this->getNormalizedNamespace()
        );
    }

    /**
     * Bootstrap our Publishers.
     */
    protected function strapPublishers(): void
    {
        $configPath = $this->getPath('config');

        $this->publishes([
            "{$configPath}/config.php" => config_path($this->getNormalizedNamespace().'.php'),
        ], $this->getNormalizedNamespace().'-config');
    }
}
