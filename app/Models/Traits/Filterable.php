<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Tự động filter dữ liệu từ array đầu vào (thường từ Request).
     * Yêu cầu Model cần define array $filterable = ['field1' => 'operator', ...];
     * Ví dụ: $filterable = ['name' => 'like', 'status' => '='];
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        if (!property_exists($this, 'filterable')) {
            return $query;
        }

        foreach ($this->filterable as $field => $operator) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $value = $filters[$field];

                if (strtolower($operator) === 'like') {
                    $query->where($field, 'like', "%{$value}%");
                } else {
                    $query->where($field, $operator, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Tự động sort dữ liệu
     */
    public function scopeSort(Builder $query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        $allowedSorts = property_exists($this, 'sortable') ? $this->sortable : ['created_at', 'updated_at', 'id'];

        if (in_array($sortBy, $allowedSorts)) {
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
            return $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }
}
