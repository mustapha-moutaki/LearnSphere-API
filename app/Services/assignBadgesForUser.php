<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class BadgeAssignmentService
{
    /**
     * Automatically check and assign badges for a user
     */
    public function assignBadgesForUser(User $user)
    {
        // Get all badges
        $badges = Badge::where('is_active', true)->get();

        foreach ($badges as $badge) {
            $badge->assignToUser($user);
        }
    }

    /**
     * Bulk assignment for all users
     */
    public function assignBadgesForAllUsers()
    {
        $users = User::all();

        foreach ($users as $user) {
            $this->assignBadgesForUser($user);
        }
    }
}