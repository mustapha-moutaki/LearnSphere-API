<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        // Student Badges
        $studentBadges = [
            [
                'name' => 'Course Starter',
                'description' => 'Complete your first course',
                'type' => 'student',
                'icon' => 'course_starter.png',
                'rules' => [
                    [
                        'rule_type' => 'course',
                        'condition' => 'total_completed_courses',
                        'operator' => '>=',
                        'threshold_value' => '1',
                    ]
                ]
            ],
            [
                'name' => 'Learning Enthusiast',
                'description' => 'Complete 10 different courses',
                'type' => 'student',
                'icon' => 'learning_enthusiast.png',
                'rules' => [
                    [
                        'rule_type' => 'course',
                        'condition' => 'unique_courses_completed',
                        'operator' => '>=',
                        'threshold_value' => '10',
                    ]
                ]
            ],
            [
                'name' => 'Platform Veteran',
                'description' => 'Active on the platform for 6 months',
                'type' => 'student',
                'icon' => 'platform_veteran.png',
                'rules' => [
                    [
                        'rule_type' => 'platform',
                        'condition' => 'total_active_months',
                        'operator' => '>=',
                        'threshold_value' => '6',
                    ]
                ]
            ]
        ];

        // Mentor Badges
        $mentorBadges = [
            [
                'name' => 'Content Creator',
                'description' => 'Publish 5 courses',
                'type' => 'mentor',
                'icon' => 'content_creator.png',
                'rules' => [
                    [
                        'rule_type' => 'course',
                        'condition' => 'total_published_courses',
                        'operator' => '>=',
                        'threshold_value' => '5',
                    ]
                ]
            ],
            [
                'name' => 'Mentor Influencer',
                'description' => 'Have 50 students enrolled',
                'type' => 'mentor',
                'icon' => 'mentor_influencer.png',
                'rules' => [
                    [
                        'rule_type' => 'students',
                        'condition' => 'total_enrolled_students',
                        'operator' => '>=',
                        'threshold_value' => '50',
                    ]
                ]
            ]
        ];

        // Combine badges
        $allBadges = array_merge($studentBadges, $mentorBadges);

        foreach ($allBadges as $badgeData) {
            // Insert badge
            $existingBadge = DB::table('badges')->where('name', $badgeData['name'])->first();
            
            if (!$existingBadge) {
                $badgeId = DB::table('badges')->insertGetId([
                    'name' => $badgeData['name'],
                    'description' => $badgeData['description'],
                    'type' => $badgeData['type'],
                    'icon' => $badgeData['icon'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $badgeId = $existingBadge->id;
            }

            // Insert rules for the badge
            if (isset($badgeData['rules'])) {
                foreach ($badgeData['rules'] as $rule) {
                    $existingRule = DB::table('badge_rules')
                        ->where('badge_id', $badgeId)
                        ->where('condition', $rule['condition'])
                        ->first();

                    if (!$existingRule) {
                        DB::table('badge_rules')->insert([
                            'badge_id' => $badgeId,
                            'rule_type' => $rule['rule_type'],
                            'condition' => $rule['condition'],
                            'operator' => $rule['operator'],
                            'threshold_value' => $rule['threshold_value'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }
}