<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'name',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->team_id = auth()->user()->team_id;
        });

        static::addGlobalScope('teams_tasks', function (Builder $builder) {
            $builder->where('team_id', auth()->user()->team_id);
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
