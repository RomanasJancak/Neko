<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AddOnRule;
use App\Http\Requests\StoreAddOnRuleRequest;
use App\Http\Requests\UpdateAddOnRuleRequest;

use Carbon\Carbon;

class AddOnRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addOnRules = AddOnRule::latest()->paginate(10);

        return view('addonrule.index', compact('addOnRules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lastCreated = AddOnRule::latest()->first();

        return view('addonrule.create', compact('lastCreated'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddOnRuleRequest $request)
    {
        //dd($request->distancerule_1_name);
        $validatedData = $request->validate([
            'begin_date' => 'required|date',
            'end_date' => 'required|date|after:begin_date',
            'baseprice' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric'],
            'distancerule_1_name' => 'required','distancerule_1_value' => 'required',
            'distancerule_2_name' => 'required','distancerule_2_value' => 'required',
            'extradistancerule_name' => 'required','extradistancerule_value' => 'required',
            'rule_1_name' => 'required','rule_1_value' => 'required',
            'rule_2_name' => 'required','rule_2_value' => 'required',
            'rule_3_name' => 'required','rule_3_value' => 'required',
            'rule_4_name' => 'required','rule_4_value' => 'required',
            'rule_5_name' => 'required','rule_5_value' => 'required',
            'rule_6_name' => 'required','rule_6_value' => 'required',
            'rule_7_name' => 'required','rule_7_value' => 'required',
            'rule_8_name' => 'required','rule_8_value' => 'required',
            'rule_9_name' => 'required','rule_9_value' => 'required',
            'rule_10_name' => 'required','rule_10_value' => 'required',
            'rule_11_name' => 'required','rule_11_value' => 'required',
            'rule_12_name' => 'required','rule_12_value' => 'required',
            'rule_13_name' => 'required','rule_13_value' => 'required',
            'rule_14_name' => 'required','rule_14_value' => 'required',      
        ],[
            'end_date.after' => 'The End Date must be a date after the Begin Date.',
            'baseprice.regex' => 'The Base Price must be a valid number with up to 2 decimal places.',
        ]);
        AddOnRule::create($validatedData);
        return redirect()->back()->with('Succses', 'Addon Rule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AddOnRule $addOnRule)
    {
        //
    }
    public function findAddOnRule(Request $request)
    {
        $formattedDatetime = Carbon::parse($day = $request->input('datetime'));

        $rule = AddOnRule::where('begin_date', '<=', $formattedDatetime)
            ->where('end_date', '>=', $formattedDatetime)
            ->first();
    
        return response()->json($rule);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddOnRule $addOnRule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddOnRuleRequest $request, AddOnRule $addOnRule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddOnRule $addOnRule)
    {
        //
    }
}
