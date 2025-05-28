<?php
namespace App\Settings;

class UserSettingDefinition
{
    public static function all(): array
    {
      return [
            'global' => [
                'theme' => [
                    'label' => 'Theme',
                    'type' => 'select',
                    'options' => ['light' => 'Light', 'dark' => 'Dark'],
                    'rules' => ['required', 'in:light,dark'],
                    'default' => 'dark',
                ],
            ],
            'models' => [
                'user' => [
                    'sort_column' => [
                        'label' => 'Sort Column',
                        'type' => 'text',
                        'rules' => ['required', 'string'],
                        'default' => 'created_at',
                    ],
                    'sort_order' => [
                        'label' => 'Sort Order',
                        'type' => 'select',
                        'options' => ['asc' => 'Ascending', 'desc' => 'Descending'],
                        'rules' => ['required', 'in:asc,desc'],
                        'default' => 'desc',
                    ],
                ],
                'post' => [
                    'sort_column' => [
                        'label' => 'Sort Column',
                        'type' => 'text',
                        'rules' => ['required', 'string'],
                        'default' => 'published_at',
                    ],
                    'sort_order' => [
                        'label' => 'Sort Order',
                        'type' => 'select',
                        'options' => ['asc' => 'Ascending', 'desc' => 'Descending'],
                        'rules' => ['required', 'in:asc,desc'],
                        'default' => 'asc',
                    ],
                ],
            ],
            'views' =>  [
              'job' =>  [
                'index' => [
                    'sort_column' => [
                        'label' => 'Sort Column',
                        'type' => 'text',
                        'rules' => ['required', 'string'],
                        'default' => 'id',
                    ],
                    'sort_order' => [
                        'label' => 'Sort Order',
                        'type' => 'select',
                        'options' => ['asc' => 'Ascending', 'desc' => 'Descending'],
                        'rules' => ['required', 'in:asc,desc'],
                        'default' => 'asc',
                    ],
                ],
              ]
            ],
        ];
    }
    public static function defaultValues(): array
    {
        $flattened = [];

        foreach (self::all() as $group => $settings) {
            foreach ($settings as $context => $items) {
                // global has no inner group
                if ($group === 'global') {
                    foreach ($items as $key => $setting) {
                        $flattened["global.$key"] = $setting['default'];
                    }
                } else {
                    foreach ($items as $key => $setting) {
                        $flattened["$group.$context.$key"] = $setting['default'];
                    }
                }
            }
        }

        return $flattened;
    }
}
