<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable;

    const PENDING = 0;
    const ACTIVE = 1;
    const INACTIVE = 2;

    public static $status = [
        0 => 'Pending',
        1 => 'Active',
        2 => 'Deactive',
    ];
    public static $statulevels = [
        0 => 'Silver',
        1 => 'Gold',
        2 => 'Platinum',
        3 => 'Diamond',
    ];
    public static $userPercentage = [
        0 => '5.00',
        1 => '4.00',
        2 => '3.00',
        3 => '2.50',
    ];
    public static $role = [
        1 => 'Admin',
        2 => 'Dealer',
        3 => 'Customer',
    ];
    public static $roleType = [
        2 => 'Dealer',
        3 => 'Customer',
    ];

    public static $gender = [
        1 => 'Male',
        2 => 'Female',
        3 => 'Other',
    ];

    public static $countries = [
        0 => "Afghanistan",
        1 => "Albania",
        2 => "Algeria",
        3 => "American Samoa",
        4 => "Andorra",
        5 => "Angola",
        6 => "Anguilla",
        7 => "Antarctica",
        8 => "Antigua and Barbuda",
        9 => "Argentina",
        10 => "Armenia",
        11 => "Aruba",
        12 => "Australia",
        13 => "Austria",
        14 => "Azerbaijan",
        15 => "Bahamas",
        16 => "Bahrain",
        17 => "Bangladesh",
        18 => "Barbados",
        19 => "Belarus",
        20 => "Belgium",
        21 => "Belize",
        22 => "Benin",
        23 => "Bermuda",
        24 => "Bhutan",
        25 => "Bolivia",
        26 => "Bosnia and Herzegowina",
        27 => "Botswana",
        28 => "Bouvet Island",
        29 => "Brazil",
        30 => "British Indian Ocean Territory",
        31 => "Brunei Darussalam",
        32 => "Bulgaria",
        33 => "Burkina Faso",
        34 => "Burundi",
        35 => "Cambodia",
        36 => "Cameroon",
        37 => "Canada",
        38 => "Cape Verde",
        39 => "Cayman Islands",
        40 => "Central African Republic",
        41 => "Chad",
        42 => "Chile",
        43 => "China",
        44 => "Christmas Island",
        45 => "Cocos (Keeling) Islands",
        46 => "Colombia",
        47 => "Comoros",
        48 => "Congo",
        49 => "Congo",
        50 => "the Democratic Republic of the",
        51 => "Cook Islands",
        52 => "Costa Rica",
        53 => "Cote d'Ivoire",
        54 => "Croatia (Hrvatska)",
        55 => "Cuba",
        56 => "Cyprus",
        57 => "Czech Republic",
        58 => "Denmark",
        59 => "Djibouti",
        60 => "Dominica",
        61 => "Dominican Republic",
        62 => "East Timor",
        63 => "Ecuador",
        64 => "Egypt",
        65 => "El Salvador",
        66 => "Equatorial Guinea",
        67 => "Eritrea",
        68 => "Estonia",
        69 => "Ethiopia",
        70 => "Falkland Islands (Malvinas)",
        71 => "Faroe Islands",
        72 => "Fiji",
        73 => "Finland",
        74 => "France",
        75 => "France",
        76 => "Metropolitan",
        77 => "French Guiana",
        78 => "French Polynesia",
        79 => "French Southern Territories",
        80 => "Gabon",
        81 => "Gambia",
        82 => "Georgia",
        83 => "Germany",
        84 => "Ghana",
        85 => "Gibraltar",
        86 => "Greece",
        87 => "Greenland",
        88 => "Grenada",
        89 => "Guadeloupe",
        90 => "Guam",
        91 => "Guatemala",
        92 => "Guinea",
        93 => "Guinea-Bissau",
        94 => "Guyana",
        95 => "Haiti",
        96 => "Heard and Mc Donald Islands",
        97 => "Holy See (Vatican City State)",
        98 => "Honduras",
        99 => "Hong Kong",
        100 => "Hungary",
        101 => "Iceland",
        102 => "India",
        103 => "Indonesia",
        104 => "Iran (Islamic Republic of)",
        105 => "Iraq",
        106 => "Ireland",
        107 => "Israel",
        108 => "Italy",
        109 => "Jamaica",
        110 => "Japan",
        111 => "Jordan",
        112 => "Kazakhstan",
        113 => "Kenya",
        114 => "Kiribati",
        115 => "Korea",
        116 => "Democratic People's Republic of",
        117 => "Korea",
        118 => "Republic of",
        119 => "Kuwait",
        120 => "Kyrgyzstan",
        121 => "Lao People's Democratic Republic",
        122 => "Latvia",
        123 => "Lebanon",
        124 => "Lesotho",
        125 => "Liberia",
        126 => "Libyan Arab Jamahiriya",
        127 => "Liechtenstein",
        128 => "Lithuania",
        129 => "Luxembourg",
        130 => "Macau",
        131 => "Macedonia",
        132 => "The Former Yugoslav Republic of",
        133 => "Madagascar",
        134 => "Malawi",
        135 => "Malaysia",
        136 => "Maldives",
        137 => "Mali",
        138 => "Malta",
        139 => "Marshall Islands",
        140 => "Martinique",
        141 => "Mauritania",
        142 => "Mauritius",
        143 => "Mayotte",
        144 => "Mexico",
        145 => "Micronesia",
        146 => "Federated States of",
        147 => "Moldova",
        148 => "Republic of",
        149 => "Monaco",
        150 => "Mongolia",
        151 => "Montserrat",
        152 => "Morocco",
        153 => "Mozambique",
        154 => "Myanmar",
        155 => "Namibia",
        156 => "Nauru",
        157 => "Nepal",
        158 => "Netherlands",
        159 => "Netherlands Antilles",
        160 => "New Caledonia",
        161 => "New Zealand",
        162 => "Nicaragua",
        163 => "Niger",
        164 => "Nigeria",
        165 => "Niue",
        166 => "Norfolk Island",
        167 => "Northern Mariana Islands",
        168 => "Norway",
        169 => "Oman",
        170 => "Pakistan",
        171 => "Palau",
        172 => "Panama",
        173 => "Papua New Guinea",
        174 => "Paraguay",
        175 => "Peru",
        176 => "Philippines",
        177 => "Pitcairn",
        178 => "Poland",
        179 => "Portugal",
        180 => "Puerto Rico",
        181 => "Qatar",
        182 => "Reunion",
        183 => "Romania",
        184 => "Russian Federation",
        185 => "Rwanda",
        186 => "Saint Kitts and Nevis",
        187 => "Saint LUCIA",
        188 => "Saint Vincent and the Grenadines",
        189 => "Samoa",
        190 => "San Marino",
        191 => "Sao Tome and Principe",
        192 => "Saudi Arabia",
        193 => "Senegal",
        194 => "Seychelles",
        195 => "Sierra Leone",
        196 => "Singapore",
        197 => "Slovakia (Slovak Republic)",
        198 => "Slovenia",
        199 => "Solomon Islands",
        200 => "Somalia",
        201 => "South Africa",
        202 => "South Georgia and the South Sandwich Islands",
        203 => "Spain",
        204 => "Sri Lanka",
        205 => "St. Helena",
        206 => "St. Pierre and Miquelon",
        207 => "Sudan",
        208 => "Suriname",
        209 => "Svalbard and Jan Mayen Islands",
        210 => "Swaziland",
        211 => "Sweden",
        212 => "Switzerland",
        213 => "Syrian Arab Republic",
        214 => "Taiwan",
        215 => "Province of China",
        216 => "Tajikistan",
        217 => "Tanzania",
        218 => "United Republic of",
        219 => "Thailand",
        220 => "Togo",
        221 => "Tokelau",
        222 => "Tonga",
        223 => "Trinidad and Tobago",
        224 => "Tunisia",
        225 => "Turkey",
        226 => "Turkmenistan",
        227 => "Turks and Caicos Islands",
        228 => "Tuvalu",
        229 => "Uganda",
        230 => "Ukraine",
        231 => "United Arab Emirates",
        232 => "United Kingdom",
        233 => "United States",
        234 => "United States Minor Outlying Islands",
        235 => "Uruguay",
        236 => "Uzbekistan",
        237 => "Vanuatu",
        238 => "Venezuela",
        239 => "Viet Nam",
        240 => "Virgin Islands (British)",
        241 => "Virgin Islands (U.S.)",
        242 => "Wallis and Futuna Islands",
        243 => "Western Sahara",
        244 => "Yemen",
        245 => "Serbia",
        246 => "Zambia",
        247 => "Zimbabwe"
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'display_id',
        'company_name',
        'vat_number',
        'street',
        'house_number',
        'zipcode',
        'city',
        'country',
        'bank_name',
        'iban',
        'bic',
        'shop_address',
        'role_type',
        'phone',
        'first_name',
        'last_name',
        'email',
        'password',
        'document_file',
        'company_logo',
        'status',
        'role_type',
        'shop_start_time',
        'shop_end_time',
        'email_verified_at',
        'contract_startdate',
        'contract_enddate',
        'contract_canceldate',
        'birth_date',
        'salutation',
        'gender',
        'vat'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getDocumentFileUrl($document_file = "")
    {
        $oldfileexists = storage_path('app/public/document_file/') . $document_file;
        if ($document_file != "" && file_exists($oldfileexists)) {
            return asset('/storage/document_file/' . $document_file);
        } else {
            return "";
        }
    }
    public static function getCompanyLogoUrl($company_logo = "")
    {
        $oldfileexists = storage_path('app/public/company_logo/') . $company_logo;
        if ($company_logo != "" && file_exists($oldfileexists)) {
            return asset('/storage/company_logo/' . $company_logo);
        } else {
            return "";
        }
    }
}
