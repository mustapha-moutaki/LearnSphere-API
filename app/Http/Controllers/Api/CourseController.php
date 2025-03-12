<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use OpenAPI\Annotations as OA;

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
    public function index(): JsonResponse
    {
        return response()->json($this->courseService->getAllCourses());
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
        $request->validate([
            'name' => 'required|string|unique:courses,name',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'nullable|string',
        ]);

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
        return response()->json($this->courseService->getCourseById($id));
    }

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
}
