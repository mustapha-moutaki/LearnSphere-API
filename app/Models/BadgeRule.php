<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeRule extends Model
{
    protected $fillable = [
        'badge_id',
        'rule_type',
        'condition',
        'operator',
        'threshold_value',
        'is_mandatory'
    ];

    /**
     * Relationship with badge
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}