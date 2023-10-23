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
        Blueprint::macro('auditable', function () {
            switch (config('auditable.primary_key_type', 'id')) {
                case 'id':
                default:
                    $primaryType = 'unsignedBigInteger';
                    break;
                case 'uuid':
                    $primaryType = 'uuid';
                    break;
                case 'ulid':
                    $primaryType = 'ulid';
                    break;
            }

            $this->$primaryType('created_by')->nullable()->index();
            $this->timestamp('created_at')->nullable()->index();
            $this->$primaryType('updated_by')->nullable()->index();
            $this->timestamp('updated_at')->nullable()->index();
            $this->$primaryType('deleted_by')->nullable()->index();
            $this->timestamp('deleted_at')->nullable()->index();
        });

        Blueprint::macro('dropAuditable', function () {
            $this->dropColumn(['created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at']);
        });
    }
}
