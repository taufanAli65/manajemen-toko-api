<?php

namespace App\Traits;

trait HasAuditFields
{
    /**
     * Boot the HasAuditFields trait.
     */
    protected static function bootHasAuditFields()
    {
        // Automatically set created_by when creating
        static::creating(function ($model) {
            if (!$model->created_by) {
                $model->created_by = auth()->user()?->user_id ?? 'system';
            }
        });

        // Automatically set updated_by when updating
        static::updating(function ($model) {
            $model->updated_by = auth()->user()?->user_id ?? 'system';
        });

        // Automatically set deleted_by when soft deleting
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }
            $model->deleted_by = auth()->user()?->user_id ?? 'system';
            $model->save();
        });
    }
}
