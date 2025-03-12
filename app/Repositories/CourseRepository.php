<?php
namespace App\Repositories;


use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository implements CourseRepositoryInterface{
    public function getAll():collection{
        return Course::all();
    }

    public function findById(int $id): ?Course{
        return Course::find($id);
    }

    public function create(array $data):Course{
        return Course::create($data);
    }

    public function update(int $id, array $data): bool{
        $category = $this->findById($id);
        return  $category ? $category->update($data): false;
    }

    public function delete(int $id): bool{
        $category = $this->findById($id);
        return $category ?$category->delete(): false;
    }




}
