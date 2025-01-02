<?php

namespace App\Http\Controllers;

use App\Models\ExtraTypes;
use App\Http\Requests\StoreExtraTypesRequest;
use App\Http\Requests\UpdateExtraTypesRequest;
use Illuminate\Http\Request;

class ExtraTypesController extends Controller
{


public function index()
{
    $extraTypes = ExtraTypes::paginate(10);
    return view('extratypes.index', compact('extraTypes'));
}

public function create()
{
    return view('extratypes.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    ExtraTypes::create($request->all());

    return redirect()->route('extratypes.index')->with('success', 'Extra Type created successfully.');
}

public function show(ExtraTypes $extraType)
{
    return view('extratypes.show', compact('extraType'));
}

public function edit(ExtraTypes $extraType)
{
    return view('extratypes.edit', compact('extraType'));
}

public function update(Request $request, ExtraTypes $extraType)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $extraType->update($request->all());

    return redirect()->route('extratypes.index')->with('success', 'Extra Type updated successfully.');
}

public function destroy(ExtraTypes $extraType)
{
    $extraType->delete();

    return redirect()->route('extratypes.index')->with('success', 'Extra Type deleted successfully.');
}

public function fetch(Request $request)
{
    $query = ExtraTypes::query();

    if ($request->has('id')) {
        $query->where('id', $request->id);
    }

    if ($request->has('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->has('description')) {
        $query->where('description', 'like', '%' . $request->description . '%');
    }

    $extraTypes = $query->paginate(10);

    return response()->json([
        'extraTypes' => $extraTypes,
        'links' => (string) $extraTypes->links(),
    ]);
}
}