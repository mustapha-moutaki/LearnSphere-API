<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubcategoryService;
use Illuminate\Http\JsonResponse;
use OpenAPI\Annotations as OA;

class SubcategoryController extends Controller
{
    protected $subcategoryService;

    public function __construct(SubcategoryService $subcategoryService)
    {
        $this->subcategoryService = $subcategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/subcategories/{parentId}",
     *     summary="Get a list of subcategories for a parent category",
     *     tags={"Subcategory"},
     *     @OA\Parameter(
     *         name="parentId",
     *         in="path",
     *         required=true,
     *         description="Parent Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index($parentId): JsonResponse
    {
        return response()->json($this->subcategoryService->getSubcategories($parentId));
    }

    /**
     * @OA\Post(
     *     path="/api/subcategories",
     *     summary="Store a new subcategory",
     *     tags={"Subcategory"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "parent_id"},
     *             @OA\Property(property="name", type="string", example="Web Development"),
     *             @OA\Property(property="parent_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Subcategory created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'required|integer|exists:categories,id', // Ensure the parent exists in the categories table
        ]);

        $subcategory = $this->subcategoryService->createSubcategory($request->all());
        return response()->json($subcategory, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/subcategories/{id}",
     *     summary="Get subcategory details",
     *     tags={"Subcategory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Subcategory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Subcategory not found")
     * )
     */
    public function show($id): JsonResponse
    {
        return response()->json($this->subcategoryService->getSubcategoryById($id));
    }

    /**
     * @OA\Put(
     *     path="/api/subcategories/{id}",
     *     summary="Update a subcategory",
     *     tags={"Subcategory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Subcategory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "parent_id"},
     *             @OA\Property(property="name", type="string", example="Mobile Development"),
     *             @OA\Property(property="parent_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Subcategory updated"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'parent_id' => 'sometimes|required|integer|exists:categories,id',
        ]);

        $success = $this->subcategoryService->updateSubcategory($id, $request->all());
        return response()->json(['success' => $success]);
    }

    /**
     * @OA\Delete(
     *     path="/api/subcategories/{id}",
     *     summary="Delete a subcategory",
     *     tags={"Subcategory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Subcategory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Subcategory deleted"),
     *     @OA\Response(response=404, description="Subcategory not found")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $success = $this->subcategoryService->deleteSubcategory($id);
        return response()->json(['success' => $success]);
    }
}
