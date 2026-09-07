<?php

namespace App\Traits;

trait HasAutoIncrementId
{
    /**
     * Boot the trait and auto-assign incremental ID on creating.
     */
    protected static function bootHasAutoIncrementId(): void
    {
        static::creating(function ($model) {
            if ($model->getKeyType() === 'int' && $model->getIncrementing() && empty($model->getKey())) {
                try {
                    $keyName = $model->getKeyName();
                    $maxId = static::max($keyName) ?? 0;
                    $model->setAttribute($keyName, (int)$maxId + 1);
                } catch (\Throwable $e) {
                    // Ignore and proceed
                }
            }
        });
    }
}
