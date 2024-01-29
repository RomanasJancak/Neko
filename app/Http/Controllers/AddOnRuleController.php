<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AddOnRule;
use App\Http\Requests\StoreAddOnRuleRequest;
use App\Http\Requests\UpdateAddOnRuleRequest;

use Carbon\Carbon;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'begin_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:begin_date',
            'name' => 'required',
            'display_name' => 'required',
            'price' => 'required',
            'client_id' => 'required',
        ], [
            'end_date.after' => 'The End Date must be a date after the Begin Date.',
        ]);
        //return response()->json($validator->errors());
        //return response()->json($validator->fails());
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ]);
        }else{
            $validatedData = $validator->validated();
            $addOnRule = AddOnRule::create($validatedData);
            return response()->json($addOnRule);
        }
    

    
        
    }

    /**
     * Display the specified resource.
     */
    public function show(AddOnRule $addOnRule)
    {
        //
    }
    public function getAddOnRuleInfo($addOnRuleId)
    {
        
        // Fetch the client's information based on the $clientId
        $addonrule = AddOnRule::find($addOnRuleId);
        if ($addonrule) {
            return response()->json([
                'begin_date'    => $addonrule->begin_date,
                'end_date'      => $addonrule->end_date,
                'name'                  => $addonrule->name,
                'display_name'  => $addonrule->display_name,
                'price'         => $addonrule->price,
                'clientName'    => $addonrule->client->name,
                'clientId'    => $addonrule->client->id,
                ]);
        }

        return response()->json(['error' => 'Client not found'], 404);
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
        //return response()->json($request);
        $addOnRule = AddOnRule::findOrFail($request->addonruleid);
        $addOnRule->begin_date = $request->begin_date;
        $addOnRule->end_date= $request->end_date;
        $addOnRule->price= $request->price;
        $addOnRule->client_id = $request->client_id;
        $addOnRule->name= $request->name;
        $addOnRule->display_name= $request->display_name;
        $addOnRule->save();
        return response()->json($addOnRule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,AddOnRule $addOnRule)
    {
        //return response()->json($request);
        $addOnRule = AddOnRule::findOrFail($request->addonruleid);
        $addOnRule->delete();
        return response()->json($addOnRule);
    }
    public function createBackup()
    {
        $addOnRules = AddOnRule::all();        
        $columns = Schema::getColumnListing('add_on_rules'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($addOnRules as $client) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $client->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/AddOnRule/addonrule.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);
        return redirect()->back()->with('succeses', 'Pricing Rule backup created successfully.');
    }
    public function getRulesForDate($date){
        return response()->json(AddOnRule::getAllThatAreApplicableToThisDate($date));
    }
}
