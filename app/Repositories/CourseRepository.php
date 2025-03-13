<?php
namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class CourseRepository implements CourseRepositoryInterface {
    public function getAll(): Collection {
        try {
            return Course::all();
        } catch (Exception $e) {
            throw new Exception('Error fetching all courses: ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Course {
        try {
            return Course::find($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Course not found with ID: ' . $id);
        } catch (Exception $e) {
            throw new Exception('Error fetching course: ' . $e->getMessage());
        }
    }

    public function create(array $data): Course {
        try {
            return Course::create($data);
        } catch (QueryException $e) {
            throw new QueryException('Error creating course: ' . $e->getMessage(), $e->getBindings(), $e->getCode());
        } catch (Exception $e) {
            throw new Exception('Error creating course: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): bool {
        try {
            $course = $this->findById($id);
            return $course ? $course->update($data) : false;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Course not found with ID: ' . $id);
        } catch (QueryException $e) {
            throw new QueryException('Error updating course: ' . $e->getMessage(), $e->getBindings(), $e->getCode());
        } catch (Exception $e) {
            throw new Exception('Error updating course: ' . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $course = $this->findById($id);
            return $course ? $course->delete() : false;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Course not found with ID: ' . $id);
        } catch (QueryException $e) {
            throw new QueryException('Error deleting course: ' . $e->getMessage(), $e->getBindings(), $e->getCode());
        } catch (Exception $e) {
            throw new Exception('Error deleting course: ' . $e->getMessage());
        }
    }
}
