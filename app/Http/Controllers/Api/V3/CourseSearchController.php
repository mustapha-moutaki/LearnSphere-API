<?php
namespace App\Http\Controllers\Api\V3;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseSearchController extends Controller
{

     /**
     * @OA\Get(
     *     path="/api/V3/courses/search",
     *     summary="Search for courses by title or description",
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Course"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No courses found"
     *     ),
     * )
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $courses = Course::where('title', 'LIKE', "%{$keyword}%")
                          ->orWhere('description', 'LIKE', "%{$keyword}%")
                          ->get();

        return response()->json($courses);
    }
}