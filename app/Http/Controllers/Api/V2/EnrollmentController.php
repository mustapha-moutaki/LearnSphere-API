<?php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends Controller
{
    /**
     * Enroll a user in a course
     */
    public function enroll(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if the user is already enrolled
        $existingEnrollment = Enrollment::where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($existingEnrollment) {
            return response()->json(['message' => 'User already enrolled in this course'], 400);
        }

        // Create the enrollment
        $enrollment = Enrollment::create([
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
        ]);

        return response()->json(['message' => 'Enrollment successful', 'enrollment' => $enrollment], 201);
    }

    /**
     * Get all enrollments
     */
    public function index(): JsonResponse
    {
        $enrollments = Enrollment::with(['user', 'course'])->get();
        return response()->json($enrollments);
    }

    /**
     * Get enrollments of a specific user
     */
    public function userEnrollments($userId): JsonResponse
    {
        $enrollments = Enrollment::where('user_id', $userId)->with('course')->get();
        return response()->json($enrollments);
    }

    /**
     * Remove an enrollment
     */
    public function unenroll($id): JsonResponse
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        $enrollment->delete();
        return response()->json(['message' => 'User unenrolled successfully']);
    }
}
