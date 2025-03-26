<?php

namespace App\Http\Controllers\Api;

use id;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use OpenAPI\Annotations as OA;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * @OA\Get(
     *     path="/api/courses",
     *     summary="Get a list of courses",
     *     tags={"Course"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $categoryId = $request->input('category');
        return response()->json($this->courseService->getAllCourses($categoryId));
    }

    /**
     * @OA\Post(
     *     path="/api/courses",
     *     summary="Store a new course",
     *     tags={"Course"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id"},
     *             @OA\Property(property="name", type="string", example="Web Development 101"),
     *             @OA\Property(property="category_id", type="integer", example="1"),
     *             @OA\Property(property="description", type="string", example="An introductory course to web development")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Course created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request): JsonResponse
    {
       $course = $request->validate([
            'title' => 'required|string|unique:courses,title',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',

            'price' => 'nullable|numeric',
        ]);

        // dd($course);
        $course = $this->courseService->createCourse($request->all());
        return response()->json($course, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/courses/{id}",
     *     summary="Get course details",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Course ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */

     public function show($id): JsonResponse
{
    $course = $this->courseService->getCourseById($id);
    
    // Optional: Add logic to check if user is enrolled
    $isEnrolled = false;
    if (auth()->check()) {
        $isEnrolled = Enrollment::where([
            'user_id' => auth()->id(),
            'course_id' => $id,
            'status' => 'active'
        ])->exists();
    }

    return response()->json([
        'course' => $course,
        'is_enrolled' => $isEnrolled
    ]);
}
    // public function show($id): JsonResponse
    // {
    //     return response()->json($this->courseService->getCourseById($id));
    // }

    /**
     * @OA\Put(
     *     path="/api/courses/{id}",
     *     summary="Update a course",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Course ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Advanced Web Development"),
     *             @OA\Property(property="description", type="string", example="Learn advanced web development techniques")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Course updated"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|unique:courses,name,' . $id,
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',

            'price' => 'nullable|float',
        ]);

        $success = $this->courseService->updateCourse($id, $request->all());
        return response()->json(['success' => $success]);
    }

    /**
     * @OA\Delete(
     *     path="/api/courses/{id}",
     *     summary="Delete a course",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Course ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Course deleted"),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $success = $this->courseService->deleteCourse($id);
        return response()->json(['success' => $success]);
    }




    public function getCourseContent($id): JsonResponse
{
    // Verify enrollment
    $enrollment = Enrollment::where([
        'user_id' => auth()->id(),
        'course_id' => $id,
        'status' => 'active'
    ])->first();

    if (!$enrollment) {
        return response()->json([
            'message' => 'by course first',
            'can_access' => false
        ], 403);
    }

    $course = $this->courseService->getCourseById($id);
    
    return response()->json([
        'course' => $course,
        'can_access' => true,
        'content' => $course->content 
    ]);
}
}
