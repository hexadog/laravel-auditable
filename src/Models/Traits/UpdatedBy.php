<?php

namespace Hexadog\Auditable\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait UpdatedBy
{
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(config('auditable.models.user'), 'updated_by', 'id');
    }

    /**
     * Set the value of the "updated by" attribute.
     *
     * @param mixed $user
     *
     * @return $this
     */
    public function setUpdatedByAttribute($user)
    {
        if (Schema::hasColumn($this->getTable(), 'updated_by')) {
            $userClass = config('auditable.models.user');
            $userId = $user instanceof $userClass ? $user->getKey() : $user;

            if (!is_null($userId)) {
                $this->attributes['updated_by'] = $userId;
            }
        }

        return $this;
    }

    public function scopeOnlyUpdatedBy(Builder $builder, $user)
    {
        $userClass = config('auditable.models.user');
        $userId = $user instanceof $userClass ? $user->getKey() : $user;

        return $builder->where('updated_by', $userId);
    }

    protected static function bootUpdatedBy()
    {
        self::creating(function ($model) {
            $model->setUpdatedByAttribute(auth()->user());
        });

        self::updating(function ($model) {
            $model->setUpdatedByAttribute(auth()->user());
        });
    }
}
