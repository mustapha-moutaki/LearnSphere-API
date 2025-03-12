<?php
namespace App\Services;

use App\Models\Subcategory;
use App\Models\Category;
use App\Repositories\SubcategoryRepository;

class SubcategoryService
{
    protected $subcategoryRepository;

    public function __construct(SubcategoryRepository $subcategoryRepository)
    {
        $this->subcategoryRepository = $subcategoryRepository;
    }

    public function getSubcategories($parentId)
    {
        return $this->subcategoryRepository->getSubcategories($parentId);
    }

    public function createSubcategory(array $data)
    {
        return Subcategory::create($data);
    }

    public function getSubcategoryById($id)
    {
        return Subcategory::findOrFail($id);
    }

    public function updateSubcategory($id, array $data)
    {
        // Find the subcategory by ID and update it
        $subcategory = Subcategory::findOrFail($id);
        return $subcategory->update($data);
    }

    public function deleteSubcategory($id)
    {
        // Find the subcategory by ID and delete it
        $subcategory = Subcategory::findOrFail($id);
        return $subcategory->delete();
    }
}
