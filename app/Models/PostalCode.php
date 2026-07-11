<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'postal_code',
        'outward_code',
        'inward_code',
        'area',
        'district',
        'sector',
        'unit',
    ];

    /**
     * Constructor for the PostalCode model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (isset($attributes['postal_code'])) {
            $this->fillPostalCodeAttributes($attributes['postal_code']);
        }
    }
    public static function parse(string $postalCode): array
    {
        $postalCode = strtoupper(trim($postalCode));
        $pattern = '/^([A-Z]{1,2}) ?(\d{1,2}[A-Z]?)? ?(\d)? ?([A-Z]{2})?$/';

        if (preg_match($pattern, $postalCode, $matches)) {
            return [
                'area'     => $matches[1] ?? null,
                'district' => $matches[2] ?? null,
                'sector'   => $matches[3] ?? null,
                'unit'     => $matches[4] ?? null
            ];
        }

        return [
            'area' => null, 'district' => null, 'sector' => null, 'unit' => null
        ];
    }
    /**
     * Fill the postal code attributes based on the full postal code.
     *
     * @param string $postalCode
     */
    protected function fillPostalCodeAttributes(string $postalCode)
    {
        $this->postal_code = $postalCode;

        // Assuming UK postal code format for example purposes
        if (preg_match('/^([A-Z]{1,2}\d[A-Z\d]?)\s*(\d[A-Z]{2})$/i', $postalCode, $matches)) {
            $this->outward_code = $matches[1];
            $this->inward_code = $matches[2];

            // Further split the outward code into area and district
            if (preg_match('/^([A-Z]{1,2})(\d[A-Z\d]?)$/i', $this->outward_code, $outwardMatches)) {
                $this->area = $outwardMatches[1];
                $this->district = $outwardMatches[2];
            }

            // Split the inward code into sector and unit
            if (preg_match('/^(\d)([A-Z]{2})$/i', $this->inward_code, $inwardMatches)) {
                $this->sector = $inwardMatches[1];
                $this->unit = $inwardMatches[2];
            }
        }
    }
}
