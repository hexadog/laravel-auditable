<?php

namespace Hexadog\Auditable\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
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
     * @param mixed $value
     * @param mixed $user
     *
     * @return $this
     */
    public function setDeletedByAttribute($user)
    {
        if (Schema::hasColumn($this->getTable(), 'deleted_by')) {
            $events = $this->getEventDispatcher();

            $userClass = config('auditable.models.user');
            $userId = $user instanceof $userClass ? $user->getKey() : $user;

            tap($this)->unsetEventDispatcher()
                ->forceFill(
                    ['deleted_by' => $userId]
                )->save();

            $this->setEventDispatcher($events);
        }

        return $this;
    }

    public function scopeOnlyDeletedBy(Builder $builder, $user)
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
