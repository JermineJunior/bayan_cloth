<?php

namespace App\Models\Concerns;

trait VisibleToOwner
{
    public function scopeVisible($query)
    {
        $user = auth()->user();
        if (! $user || $user->can('عرض كل الفواتير')) return $query;
        $table = $this->getTable();
        return $query->where(function ($q) use ($user, $table) {
            $q->where($table . '.user_id', $user->id);
            if ($user->representative_id && \Schema::hasColumn($table, 'representative_id')) {
                $q->orWhere($table . '.representative_id', $user->representative_id);
            }
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->newQuery()->visible()->where($field ?? $this->getRouteKeyName(), $value)->first();
    }
}
