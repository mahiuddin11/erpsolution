<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'sub_module',
        'record_id',
        'record_type',
        'description',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'status',
    ];

    protected $casts = [
        'old_values'      => 'array',
        'new_values'      => 'array',
        'changed_fields'  => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // ==================== Relationships ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo('record_type', 'record_id');
    }

    // ==================== Scopes (Fixed with Type Hint) ====================

    public function scopeSuccess(Builder $query): Builder
    {
        return $query->where('status', 'success');
    }

    public function scopeOfModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    public function scopeOfAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    public function scopeLastDays(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // আরও সুবিধাজনক Scope
    public function scopeRecent(Builder $query, int $limit = 50): Builder
    {
        return $query->latest()->limit($limit);
    }
}
