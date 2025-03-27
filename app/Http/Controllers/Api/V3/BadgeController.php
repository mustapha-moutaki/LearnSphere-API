<?php

namespace App\Http\Controllers\Api\V3;

use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BadgeController extends Controller
{
    /**
     * List badges for a specific student
     */
    public function studentBadges($studentId)
    {
        $user = User::findOrFail($studentId);
        return response()->json($user->badges);
    }

    /**
     * Create a new badge (admin only)
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $badge = Badge::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return response()->json($badge, 201);
    }

    /**
     * Update an existing badge (admin only)
     */
    public function update(Request $request, $id)
    {
        $badge = Badge::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $updateData = $request->only(['name', 'description']);
        $badge->update($updateData);

        return response()->json($badge);
    }

    /**
     * Delete a badge (admin only)
     */
    public function delete($id)
    {
        $badge = Badge::findOrFail($id);
        $badge->delete();

        return response()->json(null, 204);
    }

    /**
     * Get all badges
     */
    public function index()
    {
        $badges = Badge::all();
        return response()->json($badges);
    }
}
