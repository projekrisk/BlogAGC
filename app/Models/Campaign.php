<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}