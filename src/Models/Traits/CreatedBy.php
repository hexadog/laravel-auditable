<?php

namespace Hexadog\Auditable\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait CreatedBy
{
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(config('auditable.models.user'), 'created_by', 'id');
    }

    /**
     * Set the value of the "created by" attribute.
     *
     * @param mixed $value
     * @param mixed $user
     *
     * @return $this
     */
    public function setCreatedByAttribute(Model|string|int|null $user)
    {
        if (!is_null($user) && Schema::hasColumn($this->getTable(), 'created_by')) {
            $userClass = config('auditable.models.user', User::class);
            $userId = $user instanceof $userClass ? $user->getKey() : $user;

            if (!is_null($userId)) {
                $this->attributes['created_by'] = $userId;
            }
        }

        return $this;
    }

    public function scopeOnlyCreatedBy(Builder $builder, Model|string|int $user)
    {
        $userClass = config('auditable.models.user');
        $userId = $user instanceof $userClass ? $user->getKey() : $user;

        return $builder->where('created_by', $userId);
    }

    protected static function bootCreatedBy()
    {
        self::creating(function ($model) {
            $model->setCreatedByAttribute(auth()->user());
        });
    }
}
