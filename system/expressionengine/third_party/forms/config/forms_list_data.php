<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$cf_lists = array();

//----------------------------------------
// Countries
//----------------------------------------
$cf_lists['countries']['name'] = 'Countries';
$cf_lists['countries']['list'] = array (
'US' => 'United States',
'GB' => 'United Kingdom',
'AF' => 'Afghanistan',
'AL' => 'Albania',
'DZ' => 'Algeria',
'AS' => 'American Samoa',
'AD' => 'Andorra',
'AO' => 'Angola',
'AI' => 'Anguilla',
'AQ' => 'Antarctica',
'AG' => 'Antigua And Barbuda',
'AR' => 'Argentina',
'AM' => 'Armenia',
'AW' => 'Aruba',
'AU' => 'Australia',
'AT' => 'Austria',
'AZ' => 'Azerbaijan',
'BS' => 'Bahamas',
'BH' => 'Bahrain',
'BD' => 'Bangladesh',
'BB' => 'Barbados',
'BY' => 'Belarus',
'BE' => 'Belgium',
'BZ' => 'Belize',
'BJ' => 'Benin',
'BM' => 'Bermuda',
'BT' => 'Bhutan',
'BO' => 'Bolivia',
'BA' => 'Bosnia And Herzegowina',
'BW' => 'Botswana',
'BV' => 'Bouvet Island',
'BR' => 'Brazil',
'IO' => 'British Indian Ocean Territory',
'BN' => 'Brunei Darussalam',
'BG' => 'Bulgaria',
'BF' => 'Burkina Faso',
'BI' => 'Burundi',
'KH' => 'Cambodia',
'CM' => 'Cameroon',
'CA' => 'Canada',
'CV' => 'Cape Verde',
'KY' => 'Cayman Islands',
'CF' => 'Central African Republic',
'TD' => 'Chad',
'CL' => 'Chile',
'CN' => 'China',
'CX' => 'Christmas Island',
'CC' => 'Cocos (Keeling) Islands',
'CO' => 'Colombia',
'KM' => 'Comoros',
'CG' => 'Congo',
'CD' => 'Congo, The Democratic Republic Of The',
'CK' => 'Cook Islands',
'CR' => 'Costa Rica',
'CI' => "Cote D'Ivoire",
'HR' => 'Croatia (Local Name: Hrvatska)',
'CU' => 'Cuba',
'CY' => 'Cyprus',
'CZ' => 'Czech Republic',
'DK' => 'Denmark',
'DJ' => 'Djibouti',
'DM' => 'Dominica',
'DO' => 'Dominican Republic',
'TP' => 'East Timor',
'EC' => 'Ecuador',
'EG' => 'Egypt',
'SV' => 'El Salvador',
'GQ' => 'Equatorial Guinea',
'ER' => 'Eritrea',
'EE' => 'Estonia',
'ET' => 'Ethiopia',
'FK' => 'Falkland Islands (Malvinas)',
'FO' => 'Faroe Islands',
'FJ' => 'Fiji',
'FI' => 'Finland',
'FR' => 'France',
'FX' => 'France, Metropolitan',
'GF' => 'French Guiana',
'PF' => 'French Polynesia',
'TF' => 'French Southern Territories',
'GA' => 'Gabon',
'GM' => 'Gambia',
'GE' => 'Georgia',
'DE' => 'Germany',
'GH' => 'Ghana',
'GI' => 'Gibraltar',
'GR' => 'Greece',
'GL' => 'Greenland',
'GD' => 'Grenada',
'GP' => 'Guadeloupe',
'GU' => 'Guam',
'GT' => 'Guatemala',
'GN' => 'Guinea',
'GW' => 'Guinea-Bissau',
'GY' => 'Guyana',
'HT' => 'Haiti',
'HM' => 'Heard And Mc Donald Islands',
'VA' => 'Holy See (Vatican City State)',
'HN' => 'Honduras',
'HK' => 'Hong Kong',
'HU' => 'Hungary',
'IS' => 'Iceland',
'IN' => 'India',
'ID' => 'Indonesia',
'IR' => 'Iran (Islamic Republic Of)',
'IQ' => 'Iraq',
'IE' => 'Ireland',
'IL' => 'Israel',
'IT' => 'Italy',
'JM' => 'Jamaica',
'JP' => 'Japan',
'JO' => 'Jordan',
'KZ' => 'Kazakhstan',
'KE' => 'Kenya',
'KI' => 'Kiribati',
'KP' => "Korea, Democratic People's Republic Of",
'KR' => 'Korea, Republic Of',
'KW' => 'Kuwait',
'KG' => 'Kyrgyzstan',
'LA' => "Lao People's Democratic Republic",
'LV' => 'Latvia',
'LB' => 'Lebanon',
'LS' => 'Lesotho',
'LR' => 'Liberia',
'LY' => 'Libyan Arab Jamahiriya',
'LI' => 'Liechtenstein',
'LT' => 'Lithuania',
'LU' => 'Luxembourg',
'MO' => 'Macau',
'MK' => 'Macedonia, Former Yugoslav Republic Of',
'MG' => 'Madagascar',
'MW' => 'Malawi',
'MY' => 'Malaysia',
'MV' => 'Maldives',
'ML' => 'Mali',
'MT' => 'Malta',
'MH' => 'Marshall Islands',
'MQ' => 'Martinique',
'MR' => 'Mauritania',
'MU' => 'Mauritius',
'YT' => 'Mayotte',
'MX' => 'Mexico',
'FM' => 'Micronesia, Federated States Of',
'MD' => 'Moldova, Republic Of',
'MC' => 'Monaco',
'MN' => 'Mongolia',
'MS' => 'Montserrat',
'MA' => 'Morocco',
'MZ' => 'Mozambique',
'MM' => 'Myanmar',
'NA' => 'Namibia',
'NR' => 'Nauru',
'NP' => 'Nepal',
'NL' => 'Netherlands',
'AN' => 'Netherlands Antilles',
'NC' => 'New Caledonia',
'NZ' => 'New Zealand',
'NI' => 'Nicaragua',
'NE' => 'Niger',
'NG' => 'Nigeria',
'NU' => 'Niue',
'NF' => 'Norfolk Island',
'MP' => 'Northern Mariana Islands',
'NO' => 'Norway',
'OM' => 'Oman',
'PK' => 'Pakistan',
'PW' => 'Palau',
'PA' => 'Panama',
'PG' => 'Papua New Guinea',
'PY' => 'Paraguay',
'PE' => 'Peru',
'PH' => 'Philippines',
'PN' => 'Pitcairn',
'PL' => 'Poland',
'PT' => 'Portugal',
'PR' => 'Puerto Rico',
'QA' => 'Qatar',
'RE' => 'Reunion',
'RO' => 'Romania',
'RU' => 'Russian Federation',
'RW' => 'Rwanda',
'KN' => 'Saint Kitts And Nevis',
'LC' => 'Saint Lucia',
'VC' => 'Saint Vincent And The Grenadines',
'WS' => 'Samoa',
'SM' => 'San Marino',
'ST' => 'Sao Tome And Principe',
'SA' => 'Saudi Arabia',
'SN' => 'Senegal',
'SC' => 'Seychelles',
'SL' => 'Sierra Leone',
'SG' => 'Singapore',
'SK' => 'Slovakia (Slovak Republic)',
'SI' => 'Slovenia',
'SB' => 'Solomon Islands',
'SO' => 'Somalia',
'ZA' => 'South Africa',
'GS' => 'South Georgia, South Sandwich Islands',
'ES' => 'Spain',
'LK' => 'Sri Lanka',
'SH' => 'St. Helena',
'PM' => 'St. Pierre And Miquelon',
'SD' => 'Sudan',
'SR' => 'Suriname',
'SJ' => 'Svalbard And Jan Mayen Islands',
'SZ' => 'Swaziland',
'SE' => 'Sweden',
'CH' => 'Switzerland',
'SY' => 'Syrian Arab Republic',
'TW' => 'Taiwan',
'TJ' => 'Tajikistan',
'TZ' => 'Tanzania, United Republic Of',
'TH' => 'Thailand',
'TG' => 'Togo',
'TK' => 'Tokelau',
'TO' => 'Tonga',
'TT' => 'Trinidad And Tobago',
'TN' => 'Tunisia',
'TR' => 'Turkey',
'TM' => 'Turkmenistan',
'TC' => 'Turks And Caicos Islands',
'TV' => 'Tuvalu',
'UG' => 'Uganda',
'UA' => 'Ukraine',
'AE' => 'United Arab Emirates',
'UM' => 'United States Minor Outlying Islands',
'UY' => 'Uruguay',
'UZ' => 'Uzbekistan',
'VU' => 'Vanuatu',
'VE' => 'Venezuela',
'VN' => 'Viet Nam',
'VG' => 'Virgin Islands (British)',
'VI' => 'Virgin Islands (U.S.)',
'WF' => 'Wallis And Futuna Islands',
'EH' => 'Western Sahara',
'YE' => 'Yemen',
'YU' => 'Yugoslavia',
'ZM' => 'Zambia',
'ZW' => 'Zimbabwe',
);

//----------------------------------------
// States: U.S.A
//----------------------------------------
$cf_lists['us_states']['name'] = 'States: U.S.A';
$cf_lists['us_states']['list'] = array (
'AL' => 'Alabama',
'AK' => 'Alaska',
'AZ' => 'Arizona',
'AR' => 'Arkansas',
'CA' => 'California',
'CO' => 'Colorado',
'CT' => 'Connecticut',
'DE' => 'Delaware',
'FL' => 'Florida',
'GA' => 'Georgia',
'HI' => 'Hawaii',
'ID' => 'Idaho',
'IL' => 'Illinois',
'IN' => 'Indiana',
'IA' => 'Iowa',
'KS' => 'Kansas',
'KY' => 'Kentucky',
'LA' => 'Louisiana',
'ME' => 'Maine',
'MD' => 'Maryland',
'MA' => 'Massachusetts',
'MI' => 'Michigan',
'MN' => 'Minnesota',
'MS' => 'Mississippi',
'MO' => 'Missouri',
'MT' => 'Montana',
'NE' => 'Nebraska',
'NV' => 'Nevada',
'NH' => 'New Hampshire',
'NJ' => 'New Jersey',
'NM' => 'New Mexico',
'NY' => 'New York',
'NC' => 'North Carolina',
'ND' => 'North Dakota',
'OH' => 'Ohio',
'OK' => 'Oklahoma',
'OR' => 'Oregon',
'PA' => 'Pennsylvania',
'RI' => 'Rhode Island',
'SC' => 'South Carolina',
'SD' => 'South Dakota',
'TN' => 'Tennessee',
'TX' => 'Texas',
'UT' => 'Utah',
'VT' => 'Vermont',
'VA' => 'Virginia',
'WA' => 'Washington',
'WV' => 'West Virginia',
'WI' => 'Wisconsin',
'WY' => 'Wyoming',
);

//----------------------------------------
// States: Canada
//----------------------------------------
$cf_lists['ca_states']['name'] = 'States: Canada';
$cf_lists['ca_states']['list'] = array (
'AB' => 'Alberta',
'BC' => 'British Columbia',
'MB' => 'Manitoba',
'NB' => 'New Brunswick',
'NL' => 'Newfoundland & Labrador',
'NT' => 'Northwest Territories',
'NS' => 'Nova Scotia',
'NU' => 'Nunavut',
'ON' => 'Ontario',
'PE' => 'Prince Edward Island',
'QC' => 'Quebec',
'SK' => 'Saskatchewan',
'YT' => 'Yukon',
);

//----------------------------------------
// States: Netherlands
//----------------------------------------
$cf_lists['nl_states']['name'] = 'States: Netherlands';
$cf_lists['nl_states']['list'] = array (
'Drenthe'       => 'Drenthe',
'Flevoland'     => 'Flevoland',
'Friesland'     => 'Friesland',
'Gelderland'    => 'Gelderland',
'Groningen'     => 'Groningen',
'Limburg'       => 'Limburg',
'Noord-Brabant' => 'Noord-Brabant',
'Noord-Holland' => 'Noord-Holland',
'Overijssel'    => 'Overijssel',
'Zuid-Holland'  => 'Zuid-Holland',
'Utrecht'       => 'Utrecht',
'Zeeland'       => 'Zeeland',
);

//----------------------------------------
// States: Germany
//----------------------------------------
$cf_lists['de_states']['name'] = 'States: Germany';
$cf_lists['de_states']['list'] = array (
'BW' => 'Baden-Württemberg',
'BY' => 'Bayern',
'BE' => 'Berlin',
'BB' => 'Brandenburg',
'HB' => 'Bremen',
'HH' => 'Hamburg',
'HE' => 'Hessen',
'MV' => 'Mecklenburg-Vorpommern',
'NI' => 'Niedersachsen',
'NW' => 'Nordrhein-Westfalen',
'RP' => 'Rheinland-Pfalz',
'SL' => 'Saarland',
'SN' => 'Sachsen',
'ST' => 'Sachsen-Anhalt',
'SH' => 'Schleswig-Holstein',
'TH' => 'Thüringen',
);

//----------------------------------------
// Continents
//----------------------------------------
$cf_lists['continents']['name'] = 'Continents';
$cf_lists['continents']['list'] = array (
'Africa'        => 'Africa',
'Antarctica'    => 'Antarctica',
'Asia'          => 'Asia',
'Australia'     => 'Australia',
'Europe'        => 'Europe',
'North America' => 'North America',
'South America' => 'South America',
);

//----------------------------------------
// Gender
//----------------------------------------
$cf_lists['gender']['name'] = 'Gender';
$cf_lists['gender']['list'] = array (
'Male'   => 'Male',
'Female' => 'Female',
'Prefer Not to Answer' => 'Prefer Not to Answer',
);

//----------------------------------------
// Age
//----------------------------------------
$cf_lists['age']['name'] = 'Age';
$cf_lists['age']['list'] = array (
'Under 18' => 'Under 18',
'18-24'    => '18-24',
'25-34'    => '25-34',
'35-44'    => '35-44',
'45-54'    => '45-54',
'55-64'    => '55-64',
'65 or Above' => '65 or Above',
'Prefer Not to Answer' => 'Prefer Not to Answer',
);

//----------------------------------------
// Marital Status
//----------------------------------------
$cf_lists['marital_status']['name'] = 'Marital Status';
$cf_lists['marital_status']['list'] = array (
'Single'   => 'Single',
'Married'  => 'Married',
'Divorced' => 'Divorced',
'Widowed'  => 'Widowed',
);

//----------------------------------------
// Employment
//----------------------------------------
$cf_lists['employment']['name'] = 'Employment';
$cf_lists['employment']['list'] = array (
'Employed Full-Time' => 'Employed Full-Time',
'Employed Part-Time' => 'Employed Part-Time',
'Self-employed'      => 'Self-employed',
'Not employed but looking for work' => 'Not employed but looking for work',
'Not employed and not looking for work' => 'Not employed and not looking for work',
'Homemaker' => 'Homemaker',
'Retired'   => 'Retired',
'Student'   => 'Student',
'Prefer Not to Answer' => 'Prefer Not to Answer',
);

//----------------------------------------
// Job Type
//----------------------------------------
$cf_lists['job_type']['name'] = 'Job Type';
$cf_lists['job_type']['list'] = array (
'Full-Time' => 'Full-Time',
'Part-Time' => 'Part-Time',
'Per Diem'  => 'Per Diem',
'Employee'  => 'Employee',
'Temporary' => 'Temporary',
'Contract'  => 'Contract',
'Intern'    => 'Intern',
'Seasonal'  => 'Seasonal',
);

//----------------------------------------
// Industry
//----------------------------------------
$cf_lists['industry']['name'] = 'Industry';
$cf_lists['industry']['list'] = array (
'Accounting/Finance'            => 'Accounting/Finance',
'Advertising/Public Relations'  => 'Advertising/Public Relations',
'Aerospace/Aviation'            => 'Aerospace/Aviation',
'Arts/Entertainment/Publishing' => 'Arts/Entertainment/Publishing',
'Automotive'                    => 'Automotive',
'Banking/Mortgage'              => 'Banking/Mortgage',
'Business Development'          => 'Business Development',
'Business Opportunity'          => 'Business Opportunity',
'Clerical/Administrative'       => 'Clerical/Administrative',
'Construction/Facilities'       => 'Construction/Facilities',
'Consumer Goods'                => 'Consumer Goods',
'Customer Service'              => 'Customer Service',
'Education/Training'            => 'Education/Training',
'Energy/Utilities'              => 'Energy/Utilities',
'Engineering'                   => 'Engineering',
'Government/Military'           => 'Government/Military',
'Green'                         => 'Green',
'Healthcare'                    => 'Healthcare',
'Hospitality/Travel'            => 'Hospitality/Travel',
'Human Resources'               => 'Human Resources',
'Installation/Maintenance'      => 'Installation/Maintenance',
'Insurance'                     => 'Insurance',
'Internet'                      => 'Internet',
'Job Search Aids'               => 'Job Search Aids',
'Law Enforcement/Security'      => 'Law Enforcement/Security',
'Legal'                         => 'Legal',
'Management/Executive'          => 'Management/Executive',
'Manufacturing/Operations'      => 'Manufacturing/Operations',
'Marketing'                     => 'Marketing',
'Non-Profit/Volunteer'          => 'Non-Profit/Volunteer',
'Pharmaceutical/Biotech'        => 'Pharmaceutical/Biotech',
'Professional Services'         => 'Professional Services',
'QA/Quality Control'            => 'QA/Quality Control',
'Real Estate'                   => 'Real Estate',
'Restaurant/Food Service'       => 'Restaurant/Food Service',
'Retail'                        => 'Retail',
'Sales'                         => 'Sales',
'Science/Research'              => 'Science/Research',
'Skilled Labor'                 => 'Skilled Labor',
'Technology'                    => 'Technology',
'Telecommunications'            => 'Telecommunications',
'Transportation/Logistics'      => 'Transportation/Logistics',
'Other'                         => 'Other',
);

//----------------------------------------
// Income
//----------------------------------------
$cf_lists['income']['name'] = 'Income';
$cf_lists['income']['list'] = array (
'Under $20,000'        => 'Under $20,000',
'$20,000 - $30,000'    => '$20,000 - $30,000',
'$30,000 - $40,000'    => '$30,000 - $40,000',
'$40,000 - $50,000'    => '$40,000 - $50,000',
'$50,000 - $75,000'    => '$50,000 - $75,000',
'$75,000 - $100,000'   => '$75,000 - $100,000',
'$100,000 - $150,000'  => '$100,000 - $150,000',
'$150,000 or more'     => '$150,000 or more',
'Prefer Not to Answer' => 'Prefer Not to Answer',
);

//----------------------------------------
// Education
//----------------------------------------
$cf_lists['education']['name'] = 'Education';
$cf_lists['education']['list'] = array (
'High School'                     => 'High School',
'Associate Degree'                => 'Associate Degree',
'Bachelor\'s Degree'              => 'Bachelor\'s Degree',
'Graduate of Professional Degree' => 'Graduate of Professional Degree',
'Some College'                    => 'Some College',
'Other'                           => 'Other',
'Prefer Not to Answer'            => 'Prefer Not to Answer',
);

//----------------------------------------
// Days of the Week
//----------------------------------------
$cf_lists['days_week']['name'] = 'Days of the Week';
$cf_lists['days_week']['list'] = array (
'Sunday'    => 'Sunday',
'Monday'    => 'Monday',
'Tuesday'   => 'Tuesday',
'Wednesday' => 'Wednesday',
'Thursday'  => 'Thursday',
'Friday'    => 'Friday',
'Saturday'  => 'Saturday',
);

//----------------------------------------
// Months of the Year
//----------------------------------------
$cf_lists['months_year']['name'] = 'Months of the Year';
$cf_lists['months_year']['list'] = array (
'January'   => 'January',
'February'  => 'February',
'March'     => 'March',
'April'     => 'April',
'May'       => 'May',
'June'      => 'June',
'July'      => 'July',
'August'    => 'August',
'September' => 'September',
'October'   => 'October',
'November'  => 'November',
'December'  => 'December',
);

//----------------------------------------
// How Often
//----------------------------------------
$cf_lists['how_often']['name'] = 'How Often';
$cf_lists['how_often']['list'] = array (
'Everyday'               => 'Everyday',
'Once a week'            => 'Once a week',
'2 to 3 times a week'    => '2 to 3 times a week',
'Once a month'           => 'Once a month',
' 2 to 3 times a month'  => ' 2 to 3 times a month',
'Less than once a month' => 'Less than once a month',
);

//----------------------------------------
// How Long
//----------------------------------------
$cf_lists['how_long']['name'] = 'How Long';
$cf_lists['how_long']['list'] = array (
'Less than a month' => 'Less than a month',
'1-6 months'        => '1-6 months',
'1-3 years'         => '1-3 years',
'Over 3 Years'      => 'Over 3 Years',
'Never used'        => 'Never used',
);

//----------------------------------------
// Satisfaction
//----------------------------------------
$cf_lists['satisfaction']['name'] = 'Satisfaction';
$cf_lists['satisfaction']['list'] = array (
'Very Satisfied'   => 'Very Satisfied',
'Satisfied'        => 'Satisfied',
'Neutral'          => 'Neutral',
'Unsatisfied'      => 'Unsatisfied',
'Very Unsatisfied' => 'Very Unsatisfied',
);

//----------------------------------------
// Importance
//----------------------------------------
$cf_lists['importance']['name'] = 'Importance';
$cf_lists['importance']['list'] = array (
'Very Important'     => 'Very Important',
'Important'          => 'Important',
'Somewhat Important' => 'Somewhat Important',
'Not Important'      => 'Not Important',
);

//----------------------------------------
// Agreement
//----------------------------------------
$cf_lists['agreement']['name'] = 'Agreement';
$cf_lists['agreement']['list'] = array (
'Strongly Agree'    => 'Strongly Agree',
'Agree'             => 'Agree',
'Disagree'          => 'Disagree',
'Strongly Disagree' => 'Strongly Disagree',
);

//----------------------------------------
// Comparison
//----------------------------------------
$cf_lists['comparison']['name'] = 'Comparison';
$cf_lists['comparison']['list'] = array (
'Much Better'     => 'Much Better',
'Somewhat Better' => 'Somewhat Better',
'About the Same'  => 'About the Same',
'Somewhat Worse'  => 'Somewhat Worse',
'Much Worse'      => 'Much Worse',
);

//----------------------------------------
// Would You
//----------------------------------------
$cf_lists['would_you']['name'] = 'Would You';
$cf_lists['would_you']['list'] = array (
'Definitely'     => 'Definitely',
'Probably'       => 'Probably',
'Not Sure'       => 'Not Sure',
'Probably Not'   => 'Probably Not',
'Definitely Not' => 'Definitely Not',
);

//----------------------------------------
// Size
//----------------------------------------
$cf_lists['size']['name'] = 'Size';
$cf_lists['size']['list'] = array (
'XS'   => 'Extra Small',
'S'    => 'Small',
'M'    => 'Medium',
'L'    => 'Large',
'XL'   => 'Extra Large',
'XXL'  => '2 Extra Large',
'XXXL' => '3 Extra Large',
);

