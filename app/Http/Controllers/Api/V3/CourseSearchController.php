<?php
namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CourseSearchController extends Controller
{
//is not working 




    public function search(Request $request)
    {
        try {
            $keyword = $request->input('keyword');
 
            // Validate keyword
            if (empty($keyword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keyword is required'
                ], 400);
            }
 
            $courses = Course::where('title', 'LIKE', "%{$keyword}%")
                              ->orWhere('description', 'LIKE', "%{$keyword}%")
                              ->get();
 
            return response()->json([
                'success' => true,
                'courses' => $courses,
                'total' => $courses->count()
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Course Search Error', [
                'keyword' => $keyword,
                'message' => $e->getMessage()
            ]);
 
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ], 400);
        }
    }
}