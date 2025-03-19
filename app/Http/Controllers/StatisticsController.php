<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Tag;

class StatisticsController extends Controller
{
    public function getStatistics(){
        $totalUsers = User::count();

        $totalCourses = Course::count();

        
        $totalCategories = Category::count();

        $totalTags = Tag::count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_courses' => $totalCourses,
            'total_categories' => $totalCategories,
            'total_tags' => $totalTags,
        ], 200);
    }
}
