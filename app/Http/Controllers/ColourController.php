<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColourRequest;
use App\Http\Requests\UpdateColourRequest;
use App\Models\Colour;

class ColourController extends Controller
{
    public function index()
    {
        $colours = Colour::with('taskable')
            ->orderBy('taskable_type')
            ->orderBy('taskable_id')
            ->orderBy('type')
            ->get();

        return view('colours.index', [
            'colours' => $colours,
            'taskableTypes' => Colour::taskableTypeOptions(),
        ]);
    }

    public function store(StoreColourRequest $request)
    {
        Colour::create($request->validatedPayload());

        return redirect()->route('colours.index')->with('success', 'Colour created successfully.');
    }

    public function update(UpdateColourRequest $request, Colour $colour)
    {
        $colour->update($request->validatedPayload());

        return redirect()->route('colours.index')->with('success', 'Colour updated successfully.');
    }

    public function destroy(Colour $colour)
    {
        $colour->delete();

        return redirect()->route('colours.index')->with('success', 'Colour removed successfully.');
    }
}