<?php

namespace App\Support;

/** Indian states and union territories with their GST state codes. */
class GstStates
{
    public const ALL = [
        '01' => 'Jammu and Kashmir', '02' => 'Himachal Pradesh', '03' => 'Punjab', '04' => 'Chandigarh',
        '05' => 'Uttarakhand', '06' => 'Haryana', '07' => 'Delhi', '08' => 'Rajasthan', '09' => 'Uttar Pradesh',
        '10' => 'Bihar', '11' => 'Sikkim', '12' => 'Arunachal Pradesh', '13' => 'Nagaland', '14' => 'Manipur',
        '15' => 'Mizoram', '16' => 'Tripura', '17' => 'Meghalaya', '18' => 'Assam', '19' => 'West Bengal',
        '20' => 'Jharkhand', '21' => 'Odisha', '22' => 'Chhattisgarh', '23' => 'Madhya Pradesh', '24' => 'Gujarat',
        '26' => 'Dadra and Nagar Haveli and Daman and Diu', '27' => 'Maharashtra', '29' => 'Karnataka', '30' => 'Goa',
        '31' => 'Lakshadweep', '32' => 'Kerala', '33' => 'Tamil Nadu', '34' => 'Puducherry',
        '35' => 'Andaman and Nicobar Islands', '36' => 'Telangana', '37' => 'Andhra Pradesh', '38' => 'Ladakh',
    ];

    /** 15 characters: state code, PAN, entity number, Z, check character. */
    public const GSTIN_PATTERN = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/';

    public static function names(): array
    {
        $names = array_values(self::ALL);
        sort($names);

        return $names;
    }

    public static function codeFor(?string $state): ?string
    {
        $code = array_search($state, self::ALL, true);

        return $code === false ? null : $code;
    }

    /** The state a GSTIN is registered in, from its first two digits. */
    public static function stateFromGstin(?string $gstin): ?string
    {
        return $gstin ? (self::ALL[substr($gstin, 0, 2)] ?? null) : null;
    }
}
