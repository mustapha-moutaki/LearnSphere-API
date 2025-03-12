<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @OA\Get(
     *     path="/api/tags",
     *     summary="Get a list of all tags",
     *     tags={"Tag"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        return response()->json($this->tagService->getAllTags());
    }

    /**
     * @OA\Post(
     *     path="/api/tags",
     *     summary="Store a new tag",
     *     tags={"Tag"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "parent_id"},
     *             @OA\Property(property="name", type="string", example="Web Development"),
     *             @OA\Property(property="parent_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tag created"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:tags',
            'parent_id' => 'required|integer|exists:categories,id'
        ]);

        return response()->json($this->tagService->createTag($data), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{id}",
     *     summary="Get tag details",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Tag not found")
     * )
     */
    public function show($id)
    {
        return response()->json($this->tagService->getTagById($id));
    }

    /**
     * @OA\Put(
     *     path="/api/tags/{id}",
     *     summary="Update a tag",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "parent_id"},
     *             @OA\Property(property="name", type="string", example="Mobile Development"),
     *             @OA\Property(property="parent_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tag updated"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:tags,name,' . $id,
            'parent_id' => 'required|integer|exists:categories,id'
        ]);

        return response()->json($this->tagService->updateTag($id, $data));
    }

    /**
     * @OA\Delete(
     *     path="/api/tags/{id}",
     *     summary="Delete a tag",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Tag deleted"),
     *     @OA\Response(response=404, description="Tag not found")
     * )
     */
    public function destroy($id)
    {
        $this->tagService->deleteTag($id);
        return response()->json(['message' => 'Tag deleted'], 200);
    }
}
