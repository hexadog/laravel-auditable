<?php

namespace Hexadog\Auditable\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait DeletedBy
{
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(config('auditable.models.user'), 'deleted_by', 'id');
    }

    /**
     * Set the value of the "deleted by" attribute.
     *
     * @param mixed $user
     *
     * @return $this
     */
    public function setDeletedByAttribute(Model|string|int $user)
    {
        if (Schema::hasColumn($this->getTable(), 'deleted_by')) {
            $events = $this->getEventDispatcher();

            $userClass = config('auditable.models.user', User::class);
            $userId = $user instanceof $userClass ? $user->getKey() : $user;

            $this->unsetEventDispatcher();

            if (!is_null($userId)) {
                $this->attributes['deleted_by'] = $userId;
                $this->save();
            }

            $this->setEventDispatcher($events);
        }

        return $this;
    }

    public function scopeOnlyDeletedBy(Builder $builder, Model|string|int $user)
    {
        $userClass = config('auditable.models.user');
        $userId = $user instanceof $userClass ? $user->getKey() : $user;

        return $builder->where('deleted_by', $userId);
    }

    protected static function bootDeletedBy()
    {
        self::deleting(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->setDeletedByAttribute(auth()->user());
            }
        });
    }
}
