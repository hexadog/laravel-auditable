<?php

declare(strict_types=1);

namespace Hexadog\Auditable\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait Auditable
{
    protected static function bootAuditable()
    {
        self::creating(function ($model) {
            $model->setUpdatedByAttribute();
        });

        self::updating(function ($model) {
            $model->setUpdatedByAttribute();
        });

        self::deleting(function ($model) {
            $model->setDeletedByAttribute();
        });

        if (collect(class_uses_recursive(static::class))->contains(SoftDeletes::class)) {
            self::restoring(function ($model) {
                $model->unsetDeletedByAttribute();
            });
        }

        self::saved(function ($model) {
            $model->assertUpdatedByAttribute();
        });
    }

    /**
     * Get user model who created the record.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo($this->getUserClass(), $this->getCreatedByColumn(), 'id');
    }

    /**
     * Get user model who updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo($this->getUserClass(), $this->getUpdatedByColumn(), 'id');
    }

    /**
     * Get user model who deleted the record.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo($this->getUserClass(), $this->getDeletedByColumn(), 'id');
    }

    /**
     * Get column name for created by.
     */
    public function getCreatedByColumn(): string
    {
        return property_exists($this, 'CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    /**
     * Get column name for updated by.
     */
    public function getUpdatedByColumn(): string
    {
        return property_exists($this, 'UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    /**
     * Get column name for deleted by.
     */
    public function getDeletedByColumn(): string
    {
        return property_exists($this, 'DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    public function scopeOnlyCreatedBy(Builder $builder, int $userId)
    {
        return $builder->where($this->getCreatedByColumn(), $userId);
    }

    public function scopeOnlyDeletedBy(Builder $builder, int $userId)
    {
        return $builder->where($this->getDeletedByColumn(), $userId);
    }

    public function scopeOnlyUpdatedBy(Builder $builder, int $userId)
    {
        return $builder->where($this->getUpdatedByColumn(), $userId);
    }

    /**
     * Set the value of the "created by" attribute.
     */
    public function setCreatedByAttribute()
    {
        if (Schema::hasColumn($this->getTable(), 'created_by')) {
            $userId = $this->getAuthenticatedUserId();

            if (! is_null($userId)) {
                $this->attributes['created_by'] = $userId;
            }
        }

        return $this;
    }

    /**
     * Set the value of the "updated by" attribute.
     */
    public function setUpdatedByAttribute()
    {
        $updatedBy = $this->getUpdatedByColumn();

        if (Schema::hasColumn($this->getTable(), $updatedBy)) {
            $userId = $this->getAuthenticatedUserId();

            if (! is_null($userId) && ! $this->isDirty($updatedBy)) {
                $this->attributes[$updatedBy] = $userId;
            }
        }

        return $this;
    }

    /**
     * Set the value of the "deleted by" attribute.
     */
    public function setDeletedByAttribute()
    {
        $deletedBy = $this->getDeletedByColumn();

        if (Schema::hasColumn($this->getTable(), $deletedBy)) {
            $events = $this->getEventDispatcher();
            $userId = $this->getAuthenticatedUserId();

            $this->unsetEventDispatcher();

            if (! is_null($userId)) {
                $this->attributes[$deletedBy] = $userId;

                $this->save();
            }

            $this->setEventDispatcher($events);
        }

        return $this;
    }

    /**
     * Model's restoring event hook
     */
    public function unsetDeletedByAttribute()
    {
        $deletedBy = $this->getDeletedByColumn();

        $this->$deletedBy = null;
    }

    /**
     * Set updatedBy column on save if value is not the same.
     */
    public function assertUpdatedByAttribute()
    {
        $updatedBy = $this->getUpdatedByColumn();

        if ($this->getAuthenticatedUserId() && $this->getAuthenticatedUserId() != $this->$updatedBy) {
            $this->$updatedBy = $this->getAuthenticatedUserId();

            $this->save();
        }
    }

    /**
     * Get authenticated user id depending on model's auth guard.
     *
     * @return int
     */
    protected function getAuthenticatedUserId()
    {
        return auth()->check() ? auth()->id() : null;
    }

    /**
     * Get user class.
     *
     * @return string
     */
    protected function getUserClass()
    {
        if (property_exists($this, 'auditUser')) {
            return $this->auditUser;
        }

        return config('auth.providers.users.model', User::class);
    }
}
