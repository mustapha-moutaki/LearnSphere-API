<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'icon', 
        'type', 
        'is_active'
    ];

    /**
     * Relationship with badge rules
     */
    public function rules(): HasMany
    {
        return $this->hasMany(BadgeRule::class);
    }

    /**
     * Relationship with users
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('status', 'achievement_date', 'progress_percentage');
    }

    /**
     * Check if a user is eligible for this badge
     */
    public function checkEligibility(User $user)
    {
        // Collect all rules for this badge
        $rules = $this->rules;

        // Track rule compliance
        $allRulesMet = true;

        foreach ($rules as $rule) {
            $ruleMet = $this->checkSingleRule($user, $rule);
            
            // If any mandatory rule is not met, the badge is not eligible
            if (!$ruleMet && $rule->is_mandatory) {
                $allRulesMet = false;
                break;
            }
        }

        return $allRulesMet;
    }

    /**
     * Check a single rule for badge eligibility
     */
    private function checkSingleRule(User $user, BadgeRule $rule)
    {
        // Mapping between rule conditions and user attributes
        $conditionMap = [
            'total_completed_courses' => 'total_courses_completed',
            'unique_courses_completed' => 'unique_courses_taken',
            'total_active_months' => 'total_active_months',
            'total_published_courses' => 'total_published_courses',
            'total_enrolled_students' => 'total_enrolled_students'
        ];

        // Get the corresponding user attribute
        $userAttribute = $conditionMap[$rule->condition] ?? null;

        if (!$userAttribute) {
            return false;
        }

        $userValue = $user->{$userAttribute} ?? 0;
        $thresholdValue = (int)$rule->threshold_value;

        // Perform comparison based on operator
        switch ($rule->operator) {
            case '>=':
                return $userValue >= $thresholdValue;
            case '<=':
                return $userValue <= $thresholdValue;
            case '==':
                return $userValue == $thresholdValue;
            case '>':
                return $userValue > $thresholdValue;
            case '<':
                return $userValue < $thresholdValue;
            default:
                return false;
        }
    }

    /**
     * Automatically assign badge to user if eligible
     */
    public function assignToUser(User $user)
    {
        if ($this->checkEligibility($user)) {
            // Attach badge to user if not already earned
            if (!$user->badges()->where('badges.id', $this->id)->exists()) {
                $user->badges()->attach($this->id, [
                    'status' => 'earned',
                    'achievement_date' => now(),
                    'progress_percentage' => 100,
                    'achievement_details' => json_encode([
                        'method' => 'automatic_assignment'
                    ])
                ]);

                return true;
            }
        }

        return false;
    }
}