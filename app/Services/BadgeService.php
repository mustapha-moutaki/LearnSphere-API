<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\Course;
use App\Models\Enrollment;

class BadgeService
{

    public function checkStudentBadges(User $user)
    {
        $completedCourses = Enrollment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $uniqueCoursesCount = Enrollment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->distinct('course_id')
            ->count('course_id');

        $badges = Badge::where('category', 'student')->get();

        foreach ($badges as $badge) {
            $criteria = json_decode($badge->criteria, true);
            
            $badgeEarned = false;
            
            // Check different badge criteria
            if (isset($criteria['completed_courses']) && $completedCourses >= $criteria['completed_courses']) {
                $badgeEarned = true;
            }
            
            if (isset($criteria['unique_courses']) && $uniqueCoursesCount >= $criteria['unique_courses']) {
                $badgeEarned = true;
            }

            // Add badge to user if criteria met and not already owned
            if ($badgeEarned && !$user->badges->contains($badge->id)) {
                $user->badges()->attach($badge->id);
            }
        }
    }

    /**
     * Check and award badges for a mentor
     */
    public function checkMentorBadges(User $user)
    {
        $publishedCoursesCount = Course::where('user_id', $user->id)->count();
        $totalEnrollments = Course::where('user_id', $user->id)
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->count();

        $badges = Badge::where('category', 'mentor')->get();

        foreach ($badges as $badge) {
            $criteria = json_decode($badge->criteria, true);
            
            $badgeEarned = false;
            
            // Check different badge criteria
            if (isset($criteria['published_courses']) && $publishedCoursesCount >= $criteria['published_courses']) {
                $badgeEarned = true;
            }
            
            if (isset($criteria['total_enrollments']) && $totalEnrollments >= $criteria['total_enrollments']) {
                $badgeEarned = true;
            }

            // Add badge to user if criteria met and not already owned
            if ($badgeEarned && !$user->badges->contains($badge->id)) {
                $user->badges()->attach($badge->id);
            }
        }
    }
}