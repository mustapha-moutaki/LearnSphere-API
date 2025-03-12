<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->categoryService->getAllCategories());
    }

    public function show($id): JsonResponse
    {
        return response()->json($this->categoryService->getCategoryById($id));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = $this->categoryService->createCategory($request->all());
        return response()->json($category, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $success = $this->categoryService->updateCategory($id, $request->all());
        return response()->json(['success' => $success]);
    }

    public function destroy($id): JsonResponse
    {
        $success = $this->categoryService->deleteCategory($id);
        return response()->json(['success' => $success]);
    }
}
