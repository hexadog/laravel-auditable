<?php

namespace Hexadog\Auditable\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class EloquentServiceProvider extends ServiceProvider
{
    /**
     * Register any misc. blade extensions.
     */
    public function boot()
    {
        Blueprint::macro('auditable', function ($table) {
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
            $table->timestamp('deleted_at')->nullable()->index();
        });

        Blueprint::macro('dropAuditable', function ($table) {
            $table->dropColumn(['created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at']);
        });
    }
}
