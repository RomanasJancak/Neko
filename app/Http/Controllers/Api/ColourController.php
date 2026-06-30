<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColourRequest;
use App\Http\Requests\UpdateColourRequest;
use App\Models\Colour;
use Illuminate\Http\JsonResponse;

class ColourController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Colour::with('taskable')
                ->orderBy('taskable_type')
                ->orderBy('taskable_id')
                ->orderBy('type')
                ->get(),
        ]);
    }

    public function store(StoreColourRequest $request): JsonResponse
    {
        $colour = Colour::create($request->validatedPayload());

        return response()->json([
            'message' => 'Colour created successfully.',
            'data' => $colour->load('taskable'),
        ], 201);
    }

    public function show(Colour $colour): JsonResponse
    {
        return response()->json([
            'data' => $colour->load('taskable'),
        ]);
    }

    public function update(UpdateColourRequest $request, Colour $colour): JsonResponse
    {
        $colour->update($request->validatedPayload());

        return response()->json([
            'message' => 'Colour updated successfully.',
            'data' => $colour->fresh()->load('taskable'),
        ]);
    }

    public function destroy(Colour $colour): JsonResponse
    {
        $colour->delete();

        return response()->json([
            'message' => 'Colour removed successfully.',
        ]);
    }
}