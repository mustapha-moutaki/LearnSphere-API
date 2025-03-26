<?php
namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseSearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $courses = Course::where('title', 'LIKE', "%{$keyword}%")
                          ->orWhere('description', 'LIKE', "%{$keyword}%")
                          ->get();

        return response()->json($courses);
    }
}