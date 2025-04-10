<?php
return [
    'site_charge_rate'=> env('SITE_CHARGE_RATE'),
    'commision_rate'=>[
        'BASIC'=>[
            '0' => 5, // silver level
            '1' => 4, // gold level
            '2' => 3, // platinum level
            '3' => 2.5, //diamond level
        ],
        'PREMIUM' => [
            '0' => 5, // silver level
            '1' => 4, // gold level
            '2' => 3, // platinum level
            '3' => 2.5, //diamond level
        ],
        'SUPREME'=>[
            '0' => 5, // silver level
            '1' => 4, // gold level
            '2' => 3, // platinum level
            '3' => 2.5, //diamond level
        ]
    ],
    'commision_rate_for_distributer' => [
        'BASIC' => [
            '0' => 25, // silver level
            '1' => 20, // gold level
            '2' => 15, // platinum level
            '3' => 10, //diamond level
        ],
        'PREMIUM' => [
            '0' => 25, // silver level
            '1' => 20, // gold level
            '2' => 15, // platinum level
            '3' => 10, //diamond level
        ],
        'SUPREME' => [
            '0' => 25, // silver level
            '1' => 20, // gold level
            '2' => 15, // platinum level
            '3' => 10, //diamond level
        ]
    ],
    'is_exclude_vat_from_offer'=>1,
    'commision_unit' => 0.01,
    'german_country'=>[
        "Austria" => "Austria",
        "Belgium" => "Belgium",
        "Croatia" => "Croatia",
        "Cyprus" => "Cyprus",
        "Estonia" => "Estonia",
        "Finland" => "Finland",
        "France" => "France",
        "Germany" => "Germany",
        "Greece" => "Greece",
        "Ireland" => "Ireland",
        "Italy" => "Italy",
        "Latvia" => "Latvia",
        "Lithuania" => "Lithuania",
        "Luxembourg" => "Luxembourg",
        "Malta" => "Malta",
        "Netherlands" => "Netherlands",
        "Portugal" => "Portugal",
        "Slovakia" => "Slovakia",
        "Slovenia" => "Slovenia",
        "Spain" => "Spain",
    ]
];
