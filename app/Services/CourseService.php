<?php

namespace App\Services;

use App\Models\Course;

class CourseService
{
    // public function getAllCourses()
    // {
    //     return Course::all();
    // }

    public function createCourse(array $data)
    {
        return Course::create($data);
    }

    public function getCourseById($id)
    {
        return Course::findOrFail($id);
    }

    public function updateCourse($id, array $data)
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return true;
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return true;
    }



    public function getAllCourses($categoryId = null)
    {
        // If category ID is provided, filter courses by category
        if ($categoryId) {
            return Course::where('category_id', $categoryId)->get();
        }
        
        // If no category ID, return all courses
        return Course::all();
    }
}
