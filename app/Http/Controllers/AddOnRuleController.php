<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AddOnRule;
use App\Models\ClientAddOnRule;
use App\Models\Client;

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
        $modelTypes = ['App\Models\Job','App\Models\PackageType'];
        $addOnRules = AddOnRule::latest()->paginate(10);
        $clients = Client::orderBy('name', 'asc')->get();

        return view('addonrule.index', compact('addOnRules','clients','modelTypes'));
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
            $addOnRule = new AddOnRule();
            $addOnRule->begin_date = $request->input('begin_date');
            $addOnRule->end_date = $request->input('end_date');
            $addOnRule->name = $request->input('name');
            $addOnRule->display_name = $request->input('display_name');
            $addOnRule->price = intval(str_replace('.', '', $request->input('price')));
            $addOnRule->save();
            foreach($request->selected_clients as $selected_client ){
                $addOnRule->clients()->attach($selected_client
            );
            }
            $addOnRule->save();
            return response()->json($addOnRule);
            return response()->json($request->input('priceField'));
        // }
    

    
        
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
                'clients' => $addonrule->clients->map(function ($client) {
                    return [
                        'id'    => $client->id,
                        'name' => $client->name,
                    ];
                }), 
                ]);
        }

        return response()->json(['error' => 'Client not found'], 404);
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
        $addOnRule->name= $request->name;
        $addOnRule->display_name= $request->display_name;
        $addOnRule->clients()->detach();
        foreach($request->selected_clients as $selected_client ){
            $addOnRule->clients()->attach($selected_client);
        }
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
        //return response()->json($addOnRule);
        $addOnRule->clients()->detach();
        $addOnRule->delete();
        return response()->json('Worked');
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
        //======================================================================================
        $clientAddOnRules = ClientAddOnRule::all();
        $columns = Schema::getColumnListing('client_add_on_rules'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($clientAddOnRules as $clientAddOnRule) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $clientAddOnRule->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/ClientAddOnRule/clientaddonrule.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);
        //======================================================================================
        return redirect()->back()->with('succeses', 'Pricing Rule backup created successfully.');
    }
    public function getRulesForDate($date){
        return response()->json(AddOnRule::getAllThatAreApplicableToThisDate($date));
    }
    public function getRulesForDateAndClient($date,$clientId){
        return response()->json(AddOnRule::getAllThatAreApplicableToThisDateForSpecificClient($date,$clientId));
    }
    public function getDistancePriceForDateAndClient($date,$clientId){
        $rulePattern = 'job-distance';
        return response()->json(AddOnRule::getAllThatAreApplicableToThisDateForSpecificClientByPatern($date,$clientId,$rulePattern));
    }
    public function getPriceForDistance($date,$clientId,$distanceMeters,$unitOfMeasurment = 'mile'){
        $distance = $distanceMeters;
        $rulePattern = 'job-distance';
        $rules = AddOnRule::getAllThatAreApplicableToThisDateForSpecificClientByPatern($date,$clientId,$rulePattern);
        $highestTresholdDistance    =   0;
        $highestTresholdPrice       =   0;
        $extraDistancePrice          =   0;
        foreach($rules as $rule){
            $parts = explode("-", $rule->name);
            if($parts[2] == 'lessthan' ){
                $value = $parts[3];
                if($distance < $value){
                    return $rule->price;
                }
                if($highestTresholdDistance < $value){
                    $highestTresholdDistance = $value;
                    $highestTresholdPrice = $rule->price;
                }
            }elseif($parts[2] == 'eachafter' ){
                $extraDistancePrice = $rule->price;
            }
        }
        $extraDistance = $distance - $highestTresholdDistance;
        $price = $highestTresholdPrice + ceil($extraDistance)*$extraDistancePrice;
        return $price;
    }
}
