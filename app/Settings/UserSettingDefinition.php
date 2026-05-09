<?php
namespace App\Settings;

class UserSettingDefinition
{
    public static function all(): array
    {
      return [
            'global' => [
                'vatRate' => [
                    'label' => 'VAT rate',
                    'type' => 'number',
                    'rules' => ['required', 'numeric', 'min:0', 'max:1'],
                    'default' => 0.2,
                ],
                'invoiceLockDays' => [
                    'label' => 'Invoice lock age (days)',
                    'type' => 'number',
                    'rules' => ['required', 'integer', 'min:0', 'max:3650'],
                    'default' => 1,
                ],
            ],
            'models' => [
                'job' => [
                    'view' => [
                        'index' => [
                            'sortColumn' => [
                                'label' => 'Job list Sort Column',
                                'type' => 'select',
                                'options' => [
                                    'clientName' => 'Client name',
                                    'id'         => 'Job ID'],
                                    'date'       =>  'Date',
                                'rules' => ['required', 'in:clientName,id,date'],
                                'default' => 'id',
                            ],
                            'sortOrder' => [
                                'label' => 'Job list Sort Order',
                                'type' => 'select',
                                'options' => ['asc' => 'Ascending', 'desc' => 'Descending'],
                                'rules' => ['required', 'in:asc,desc'],
                                'default' => 'asc',
                            ],
                            'dropOffSearchFields' => [
                                'label' => 'Drop-off Search Fields',
                                'type' => 'select-muiltiple',
                                'options' => [
                                    'dropoff_adress_line' => 'Address Line',
                                    'dropoff_city' => 'City',
                                    'dropoff_postal_code' => 'Postal Code',
                                    'dropoff_country' => 'Country',
                                    'dropoff_name' => 'Name',
                                    'packageType_id' => 'Package Type',
                                ],
                                'rules' => ['array'],
                                'default' => ['dropoff_adress_line', 'dropoff_city', 'dropoff_postal_code', 'dropoff_country'],
                            ],
                        ]
                    ]
                ],
            ],
        ];
    }
public static function defaultValues(): array
{
    $flattened = [];

    $flatten = function (array $settings, string $prefix = '') use (&$flattened, &$flatten) {
        foreach ($settings as $key => $value) {
            $path = $prefix === '' ? $key : "$prefix.$key";
            if (is_array($value) && isset($value['default'])) {
                $flattened[$path] = $value['default'];
            } elseif (is_array($value)) {
                $flatten($value, $path);
            }
        }
    };

    $flatten(self::all());

    return $flattened;
}

public static function validationRules(): array
{
    $rules = [];

    $flatten = function (array $settings, string $prefix = '') use (&$rules, &$flatten) {
        foreach ($settings as $key => $value) {
            $path = $prefix === '' ? $key : "$prefix.$key";
            if (is_array($value) && isset($value['rules'])) {
                $rules[$path] = $value['rules'];
            } elseif (is_array($value)) {
                $flatten($value, $path);
            }
        }
    };

    $flatten(self::all());

    return $rules;
}


}
