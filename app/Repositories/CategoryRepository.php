<?php
namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class CategoryRepository implements CategoryRepositoryInterface {
    public function getAll(): Collection {
        try {
            return Category::all();
        } catch (Exception $e) {
            throw new Exception('Error fetching all categories: ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Category {
        try {
            return Category::find($id);
        } catch (ModelNotFoundException $e) {
          
            throw new ModelNotFoundException('Category not found with ID: ' . $id);
        } catch (Exception $e) {
          
            throw new Exception('Error fetching category: ' . $e->getMessage());
        }
    }

    public function create(array $data): Category {
        try {
            return Category::create($data);
        } catch (QueryException $e) {
          
            throw new QueryException('Error creating category: ' . $e->getMessage(), $e->getBindings(), $e->getCode());
        } catch (Exception $e) {
            
            throw new Exception('Error creating category: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): bool {
        try {
            $category = $this->findById($id);
            return $category ? $category->update($data) : false;
        } catch (ModelNotFoundException $e) {
           
            throw new ModelNotFoundException('Category not found with ID: ' . $id);
        } catch (QueryException $e) {
        
            throw new QueryException('Error updating category: ' . $e->getMessage(), $e->getBindings(), $e->getCode());
        } catch (Exception $e) {
            
            throw new Exception('Error updating category: ' . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $category = $this->findById($id);
            return $category ? $category->delete() : false;
        } catch (ModelNotFoundException $e) {
           
            throw new ModelNotFoundException('Category not found with ID: ' . $id);
        } catch (QueryException $e) {
         
            throw new QueryException('Error deleting category: ' . $e->getMessage(), $e->getBindings(), $e->getCode());
        } catch (Exception $e) {
        
            throw new Exception('Error deleting category: ' . $e->getMessage());
        }
    }
}
