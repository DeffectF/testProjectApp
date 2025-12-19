<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->has('tenant')) {
                $tenant = app('tenant');
                $builder->where('tenant_id', $tenant->id);
            }
        });
        static::creating(function ($model) {
            if (app()->has('tenant')) {
                $model->tenant_id = app('tenant')->id;
            }
        });
    }

}
