<?php
namespace App\Settings;

class UserSettingDefinition
{
    public static function all(): array
    {
      return [
            'global' => [
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
