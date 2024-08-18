<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [];
        if($this->input('isJobCreationFromIndexPage')){
            $rules = [
                'status_id'                     =>  'required',
                'common_date'                   =>  'required',
                'billingClientId'               =>  'required',
            ];
        }else{        
            $rules = [


                'pickup_time_begin'             =>  'required',
                'pickup_time_end'               =>  'required',

                'pickupclientname'              =>  'required',
                'pickupclientaddressline'       =>  'required',
                'pickupclientpostalcode'        =>  'required',
                'pickupclientcity'              =>  'required',
                'pickupclientcountry'           =>  'required',

                'packagedropoffname.*'          =>  'required',
                'packagedropooffaddressline.*'  =>  'required',
                'packagedropoffpostalcode.*'    =>  'required',
                'packagedropoffcity.*'          =>  'required',
                'packagedropoffcountry.*'       =>  'required',
                'packageType.*'                 =>  'required',
                'packageType'                   =>  'required',
                'packagedropoffquantity'        =>  'required|array',
                'packagedropoffquantity.*'      =>  ['required', 'integer', 'min:1'],
                'packagedropofftimebegin.*'     =>  'required',
                'packagedropofftimebegin'       =>  'required',
                'packagedropofftimeend.*'       =>  'required',
                'packagedropofftimeend'         =>  'required',
                'jobcheckboxaddon'              =>  'nullable|array',
                'packagecheckboxaddon'          =>  'nullable|array',
                //'generalnotes'                  =>  'required',
                'manager_id'                     =>  'required',
            ];
        }
            $courierId = $this->input('courrier_id');

            // Check if 'courier_id' is equal to 0 and remove the field from the input data
            if ($courierId == 0) {
                unset($this->request->all()['courrier_id']);
            } else {
                $rules['courrier_id'] = [
                    'required', 
                    'integer',
                    Rule::exists('model_has_roles', 'model_id')->where(function ($query) use ($courierId) {
                        $query->where('model_id', $courierId)->where('model_type','App\Models\User')
                            ->where('role_id', function ($subquery) {
                                $subquery->select('id')
                                    ->from('roles')
                                    ->where('name', 'courier');
                            });
                    }),
                ];
            }
            $rulesForCustom = [];
            $isItCustomJob = $this->input('isItCustomJob');
            if ($isItCustomJob === 'true') {
                return $rulesForCustom;
            }else{
                return $rules;
            }
        
    }
    public function messages()
    {
        return [
            'status_id.required' => 'The status is required.',
            'common_date.required' => 'The date is required.',
        ];
    }
}
