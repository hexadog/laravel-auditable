<?php

namespace Hexadog\Auditable\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Our root directory for this package to make traversal easier.
     */
    public const PACKAGE_DIR = __DIR__ . '/../../';

    /**
     * Name for this package to publish assets.
     */
    public const PACKAGE_NAME = 'auditable';

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->register(EloquentServiceProvider::class);
    }

    /**
     * Get Package absolute path.
     *
     * @param  string  $path
     */
    protected function getPath($path = '')
    {
        // We get the child class
        $rc = new \ReflectionClass(get_class($this));

        return dirname($rc->getFileName()) . '/../../' . $path;
    }

    /**
     * Get Module normalized namespace.
     *
     * @param  mixed  $prefix
     */
    protected function getNormalizedNamespace($prefix = '')
    {
        return Str::start(Str::lower(self::PACKAGE_NAME), $prefix);
    }
}
