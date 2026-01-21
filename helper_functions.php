<?php
/* helper_functions.php
  This file ment for common functions 
  This file should not depends on any other files.
  Use only PHP 8 Core Variable, functions, constants.
  Do not use any functions from functions.php. 
  Do not use any CONSTANT from config.php
  Do not Implement the Redirect here
  Do not create Global Variable
  Do not Include any files
  Do not include a prfix as taoh_  use custom_ instead
*/


function slugify2($string)
{
    return strtolower(custom_trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

function isjson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function custom_urldecode($text)
{
    $return = $text;
    if (urldecode($text) != $text) $return = urldecode($text);
    return $return;
}

function cleanFontstyle($text)
{
    $text = str_replace('font-size', 'font-size-clean', $text);
    $text = str_replace('font-style', 'font-style-clean', $text);
    $text = str_replace('font-family', 'font-family-clean', $text);
    $text = str_replace('h2', 'span', $text);
    $text = str_replace('h3', 'span', $text);

    $text = str_replace('h4', 'span', $text);
    $text = str_replace('h5', 'span', $text);
    $text = str_replace('h6', 'span', $text);
    $text = str_replace('h1', 'span', $text);


    $text = str_replace('<div', '<span', $text);
    $text = str_replace('div', 'span', $text);
    //$text = str_replace('font-style', 'font-family-clean', $text);
    return $text;
}

function html_content($description)
{
    $description = html_entity_decode($description);
    return str_replace(['\"', '\/', '&quot;', '\n'], ['"', '/', '\'', "\n"], $description);
}

function is_stream_link($url)
{
    //https://www.youtube.com/watch?v=8NmYpJm27aQ
    //https://www.youtube.com/embed/
    //https://youtu.be/zMzsKUmgmgg
    $stream = false;
    if (preg_match('(youtube.com|youtu.be)', $url) === 1) {
        $stream = true;
    }
    return $stream;
}

function play_url($url)
{
    if (preg_match('(youtube.com/watch)', $url) === 1) {
        $id = explode('v=', $url);
        if (count($id) == 2) {
            return "https://www.youtube.com/embed/" . $id[1];
        }
    }
    if (preg_match('(youtu.be/)', $url) === 1) {
        $id = explode('.be/', $url);
        if (count($id) == 2) {
            return "https://www.youtube.com/embed/" . $id[1];
        }
    }

    if (preg_match('(youtube.com/embed/)', $url) === 1) {
        return $url;
    }
}

function custom_trim($data)
{
    //Supporting php 8
    return $data ? trim($data) : '';
}

function is_wp()
{
    if (function_exists('wp_title')) {
        return true;
    }
    return false;
}

function get_explode_names($array)
{
    $keys = array();
    foreach ($array as $key => $value) {
        list ($pre, $post) = explode(':>', $value);
        $keys[] = $post;
    }
    return $keys;
}

function displayTaohFormatted($string)
{
    $taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
    $user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

    $valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);

    if ($valid_user) {
        $country_get = explode(', ', $user_info_obj->full_location);
        $country = array_pop($country_get);
        $company = get_explode_names($user_info_obj?->company ?? [])[0] ?? '';
        $title = get_explode_names($user_info_obj?->title ?? [])[0] ?? '';
        $skill = get_explode_names($user_info_obj?->skill ?? []);
        usort($skill, function ($a, $b) {
            return strcasecmp($a, $b);
        });
        $skill = implode(', ', $skill);
    } else {
        $country = TAOH_DEFAULT_COUNTRY;
        $company = TAOH_DEFAULT_COMPANY;
        $title = TAOH_DEFAULT_TITLE;
        $skill = TAOH_DEFAULT_SKILL;
    }

    $sq_logo = TAOH_SITE_FAVICON;
    $logo = TAOH_SITE_LOGO;
    // Define the placeholders and their replacements
    $find = array('[sitenameslug]', '[sitelogosqurl]', '[sitelogourl]', '[sitemytitle]', '[sitemyskill]', '[sitemycompany]', '[sitemycountry]');
    $replace = array(TAOH_SERVER_NAME, $sq_logo, $logo, $title, $skill, $company, $country);

    return str_replace($find, $replace, $string);
}

function taoh_get_currency_symbol($code, $all = false)
{
    $currencies = [
        ['code' => 'USD', 'countries' => ['United States'], 'name' => 'United States dollar', 'symbol' => '&#36;'],
        ['code' => 'AFN', 'countries' => ['Afghanistan'], 'name' => 'Afghanistan Afghani', 'symbol' => '&#1547;'],
        ['code' => 'ARS', 'countries' => ['Argentina'], 'name' => 'Argentine Peso', 'symbol' => '&#36;'],
        ['code' => 'AWG', 'countries' => ['Aruba'], 'name' => 'Aruban florin', 'symbol' => '&#402;'],
        ['code' => 'AUD', 'countries' => ['Australia'], 'name' => 'Australian Dollar', 'symbol' => '&#65;&#36;'],
        ['code' => 'AZN', 'countries' => ['Azerbaijan'], 'name' => 'Azerbaijani Manat', 'symbol' => '&#8380;'],
        ['code' => 'BSD', 'countries' => ['The Bahamas'], 'name' => 'Bahamas Dollar', 'symbol' => '&#66;&#36;'],
        ['code' => 'BBD', 'countries' => ['Barbados'], 'name' => 'Barbados Dollar', 'symbol' => '&#66;&#100;&#115;&#36;'],
        ['code' => 'BDT', 'countries' => ['People\'s Republic of Bangladesh'], 'name' => 'Bangladeshi taka', 'symbol' => '&#2547;'],
        ['code' => 'BYN', 'countries' => ['Belarus'], 'name' => 'Belarus Ruble', 'symbol' => '&#66;&#114;'],
        ['code' => 'BZD', 'countries' => ['Belize'], 'name' => 'Belize Dollar', 'symbol' => '&#66;&#90;&#36;'],
        ['code' => 'BMD', 'countries' => ['British Overseas Territory of Bermuda'], 'name' => 'Bermudian Dollar', 'symbol' => '&#66;&#68;&#36;'],
        ['code' => 'BOP', 'countries' => ['Bolivia'], 'name' => 'Boliviano', 'symbol' => '&#66;&#115;'],
        ['code' => 'BAM', 'countries' => ['Bosnia', 'Herzegovina'], 'name' => 'Bosnia-Herzegovina Convertible Marka', 'symbol' => '&#75;&#77;'],
        ['code' => 'BWP', 'countries' => ['Botswana'], 'name' => 'Botswana pula', 'symbol' => '&#80;'],
        ['code' => 'BGN', 'countries' => ['Bulgaria'], 'name' => 'Bulgarian lev', 'symbol' => '&#1083;&#1074;'],
        ['code' => 'BRL', 'countries' => ['Brazil'], 'name' => 'Brazilian real', 'symbol' => '&#82;&#36;'],
        ['code' => 'BND', 'countries' => ['Sultanate of Brunei'], 'name' => 'Brunei dollar', 'symbol' => '&#66;&#36;'],
        ['code' => 'KHR', 'countries' => ['Cambodia'], 'name' => 'Cambodian riel', 'symbol' => '&#6107;'],
        ['code' => 'CAD', 'countries' => ['Canada'], 'name' => 'Canadian dollar', 'symbol' => '&#67;&#36;'],
        ['code' => 'KYD', 'countries' => ['Cayman Islands'], 'name' => 'Cayman Islands dollar', 'symbol' => '&#36;'],
        ['code' => 'CLP', 'countries' => ['Chile'], 'name' => 'Chilean peso', 'symbol' => '&#36;'],
        ['code' => 'CNY', 'countries' => ['China'], 'name' => 'Chinese Yuan Renminbi', 'symbol' => '&#165;'],
        ['code' => 'COP', 'countries' => ['Colombia'], 'name' => 'Colombian peso', 'symbol' => '&#36;'],
        ['code' => 'CRC', 'countries' => ['Costa Rica'], 'name' => 'Costa Rican colón', 'symbol' => '&#8353;'],
        ['code' => 'HRK', 'countries' => ['Croatia'], 'name' => 'Croatian kuna', 'symbol' => '&#107;&#110;'],
        ['code' => 'CUP', 'countries' => ['Cuba'], 'name' => 'Cuban peso', 'symbol' => '&#8369;'],
        ['code' => 'CZK', 'countries' => ['Czech Republic'], 'name' => 'Czech koruna', 'symbol' => '&#75;&#269;'],
        ['code' => 'DKK', 'countries' => ['Denmark', 'Greenland', 'The Faroe Islands'], 'name' => 'Danish krone', 'symbol' => '&#107;&#114;'],
        ['code' => 'DOP', 'countries' => ['Dominican Republic'], 'name' => 'Dominican peso', 'symbol' => '&#82;&#68;&#36;'],
        ['code' => 'XCD', 'countries' => ['Antigua and Barbuda', 'Commonwealth of Dominica', 'Grenada', 'Montserrat', 'St. Kitts and Nevis', 'Saint Lucia and St. Vincent', 'The Grenadines'], 'name' => 'Eastern Caribbean dollar', 'symbol' => '&#36;'],
        ['code' => 'EGP', 'countries' => ['Egypt'], 'name' => 'Egyptian pound', 'symbol' => '&#163;'],
        ['code' => 'SVC', 'countries' => ['El Salvador'], 'name' => 'Salvadoran colón', 'symbol' => '&#36;'],
        ['code' => 'EEK', 'countries' => ['Estonia'], 'name' => 'Estonian kroon', 'symbol' => '&#75;&#114;'],
        ['code' => 'EUR', 'countries' => ['European Union', 'Italy', 'Belgium', 'Bulgaria', 'Croatia', 'Cyprus', 'Czechia', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden'], 'name' => 'Euro', 'symbol' => '&#8364;'],
        ['code' => 'FKP', 'countries' => ['Falkland Islands'], 'name' => 'Falkland Islands (Malvinas) Pound', 'symbol' => '&#70;&#75;&#163;'],
        ['code' => 'FJD', 'countries' => ['Fiji'], 'name' => 'Fijian dollar', 'symbol' => '&#70;&#74;&#36;'],
        ['code' => 'GHC', 'countries' => ['Ghana'], 'name' => 'Ghanaian cedi', 'symbol' => '&#71;&#72;&#162;'],
        ['code' => 'GIP', 'countries' => ['Gibraltar'], 'name' => 'Gibraltar pound', 'symbol' => '&#163;'],
        ['code' => 'GTQ', 'countries' => ['Guatemala'], 'name' => 'Guatemalan quetzal', 'symbol' => '&#81;'],
        ['code' => 'GGP', 'countries' => ['Guernsey'], 'name' => 'Guernsey pound', 'symbol' => '&#81;'],
        ['code' => 'GYD', 'countries' => ['Guyana'], 'name' => 'Guyanese dollar', 'symbol' => '&#71;&#89;&#36;'],
        ['code' => 'HNL', 'countries' => ['Honduras'], 'name' => 'Honduran lempira', 'symbol' => '&#76;'],
        ['code' => 'HKD', 'countries' => ['Hong Kong'], 'name' => 'Hong Kong dollar', 'symbol' => '&#72;&#75;&#36;'],
        ['code' => 'HUF', 'countries' => ['Hungary'], 'name' => 'Hungarian forint', 'symbol' => '&#70;&#116;'],
        ['code' => 'ISK', 'countries' => ['Iceland'], 'name' => 'Icelandic króna', 'symbol' => '&#237;&#107;&#114;'],
        ['code' => 'INR', 'countries' => ['India'], 'name' => 'Indian rupee', 'symbol' => '&#8377;'],
        ['code' => 'IDR', 'countries' => ['Indonesia'], 'name' => 'Indonesian rupiah', 'symbol' => '&#82;&#112;'],
        ['code' => 'IRR', 'countries' => ['Iran'], 'name' => 'Iranian rial', 'symbol' => '&#65020;'],
        ['code' => 'IMP', 'countries' => ['Isle of Man'], 'name' => 'Manx pound', 'symbol' => '&#163;'],
        ['code' => 'ILS', 'countries' => ['Israel', 'Palestinian territories of the West Bank', 'The Gaza Strip'], 'name' => 'Israeli Shekel', 'symbol' => '&#8362;'],
        ['code' => 'JMD', 'countries' => ['Jamaica'], 'name' => 'Jamaican dollar', 'symbol' => '&#74;&#36;'],
        ['code' => 'JPY', 'countries' => ['Japan'], 'name' => 'Japanese yen', 'symbol' => '&#165;'],
        ['code' => 'JEP', 'countries' => ['Jersey'], 'name' => 'Jersey pound', 'symbol' => '&#163;'],
        ['code' => 'KZT', 'countries' => ['Kazakhstan'], 'name' => 'Kazakhstani tenge', 'symbol' => '&#8376;'],
        ['code' => 'KPW', 'countries' => ['North Korea'], 'name' => 'North Korean won', 'symbol' => '&#8361;'],
        ['code' => 'KPW', 'countries' => ['South Korea'], 'name' => 'South Korean won', 'symbol' => '&#8361;'],
        ['code' => 'KGS', 'countries' => ['Kyrgyz Republic'], 'name' => 'Kyrgyzstani som', 'symbol' => '&#1083;&#1074;'],
        ['code' => 'LAK', 'countries' => ['Laos'], 'name' => 'Lao kip', 'symbol' => '&#8365;'],
        ['code' => 'LAK', 'countries' => ['Laos'], 'name' => 'Latvian lats', 'symbol' => '&#8364;'],
        ['code' => 'LVL', 'countries' => ['Laos'], 'name' => 'Latvian lats', 'symbol' => '&#8364;'],
        ['code' => 'LBP', 'countries' => ['Lebanon'], 'name' => 'Lebanese pound', 'symbol' => '&#76;&#163;'],
        ['code' => 'LRD', 'countries' => ['Liberia'], 'name' => 'Liberian dollar', 'symbol' => '&#76;&#68;&#36;'],
        ['code' => 'LTL', 'countries' => ['Lithuania'], 'name' => 'Lithuanian litas', 'symbol' => '&#8364;'],
        ['code' => 'MKD', 'countries' => ['North Macedonia'], 'name' => 'Macedonian denar', 'symbol' => '&#1076;&#1077;&#1085;'],
        ['code' => 'MYR', 'countries' => ['Malaysia'], 'name' => 'Malaysian ringgit', 'symbol' => '&#82;&#77;'],
        ['code' => 'MUR', 'countries' => ['Mauritius'], 'name' => 'Mauritian rupee', 'symbol' => '&#82;&#115;'],
        ['code' => 'MXN', 'countries' => ['Mexico'], 'name' => 'Mexican peso', 'symbol' => '&#77;&#101;&#120;&#36;'],
        ['code' => 'MNT', 'countries' => ['Mongolia'], 'name' => 'Mongolian tögrög', 'symbol' => '&#8366;'],
        ['code' => 'MZN', 'countries' => ['Mozambique'], 'name' => 'Mozambican metical', 'symbol' => '&#77;&#84;'],
        ['code' => 'NAD', 'countries' => ['Namibia'], 'name' => 'Namibian dollar', 'symbol' => '&#78;&#36;'],
        ['code' => 'NPR', 'countries' => ['Federal Democratic Republic of Nepal'], 'name' => 'Nepalese rupee', 'symbol' => '&#82;&#115;&#46;'],
        ['code' => 'ANG', 'countries' => ['Curaçao', 'Sint Maarten'], 'name' => 'Netherlands Antillean guilder', 'symbol' => '&#402;'],
        ['code' => 'NZD', 'countries' => ['New Zealand', 'The Cook Islands', 'Niue', 'The Ross Dependency', 'Tokelau', 'The Pitcairn Islands'], 'name' => 'New Zealand Dollar', 'symbol' => '&#36;'],
        ['code' => 'NIO', 'countries' => ['Nicaragua'], 'name' => 'Nicaraguan córdoba', 'symbol' => '&#67;&#36;'],
        ['code' => 'NGN', 'countries' => ['Nigeria'], 'name' => 'Nigerian Naira', 'symbol' => '&#8358;'],
        ['code' => 'NOK', 'countries' => ['Norway and its dependent territories'], 'name' => 'Norwegian krone', 'symbol' => '&#107;&#114;'],
        ['code' => 'OMR', 'countries' => ['Oman'], 'name' => 'Omani rial', 'symbol' => '&#65020;'],
        ['code' => 'PKR', 'countries' => ['Pakistan'], 'name' => 'Pakistani rupee', 'symbol' => '&#82;&#115;'],
        ['code' => 'PAB', 'countries' => ['Panama'], 'name' => 'Panamanian balboa', 'symbol' => '&#66;&#47;&#46;'],
        ['code' => 'PYG', 'countries' => ['Paraguay'], 'name' => 'Paraguayan Guaraní', 'symbol' => '&#8370;'],
        ['code' => 'PEN', 'countries' => ['Peru'], 'name' => 'Sol', 'symbol' => '&#83;&#47;&#46;'],
        ['code' => 'PHP', 'countries' => ['Philippines'], 'name' => 'Philippine peso', 'symbol' => '&#8369;'],
        ['code' => 'PLN', 'countries' => ['Poland'], 'name' => 'Polish złoty', 'symbol' => '&#122;&#322;'],
        ['code' => 'QAR', 'countries' => ['State of Qatar'], 'name' => 'Qatari Riyal', 'symbol' => '&#65020;'],
        ['code' => 'RON', 'countries' => ['Romania'], 'name' => 'Romanian leu (Leu românesc)', 'symbol' => '&#76;'],
        ['code' => 'RUB', 'countries' => ['Russian Federation', 'Abkhazia and South Ossetia', 'Donetsk and Luhansk'], 'name' => 'Russian ruble', 'symbol' => '&#8381;'],
        ['code' => 'SHP', 'countries' => ['Saint Helena', 'Ascension', 'Tristan da Cunha'], 'name' => 'Saint Helena pound', 'symbol' => '&#163;'],
        ['code' => 'SAR', 'countries' => ['Saudi Arabia'], 'name' => 'Saudi riyal', 'symbol' => '&#65020;'],
        ['code' => 'RSD', 'countries' => ['Serbia'], 'name' => 'Serbian dinar', 'symbol' => '&#100;&#105;&#110;'],
        ['code' => 'SCR', 'countries' => ['Seychelles'], 'name' => 'Seychellois rupee', 'symbol' => '&#82;&#115;'],
        ['code' => 'SGD', 'countries' => ['Singapore'], 'name' => 'Singapore dollar', 'symbol' => '&#83;&#36;'],
        ['code' => 'SBD', 'countries' => ['Solomon Islands'], 'name' => 'Solomon Islands dollar', 'symbol' => '&#83;&#73;&#36;'],
        ['code' => 'SOS', 'countries' => ['Somalia'], 'name' => 'Somali shilling', 'symbol' => '&#83;&#104;&#46;&#83;&#111;'],
        ['code' => 'ZAR', 'countries' => ['South Africa'], 'name' => 'South African rand', 'symbol' => '&#82;'],
        ['code' => 'LKR', 'countries' => ['Sri Lanka'], 'name' => 'Sri Lankan rupee', 'symbol' => '&#82;&#115;'],
        ['code' => 'SEK', 'countries' => ['Sweden'], 'name' => 'Swedish krona', 'symbol' => '&#107;&#114;'],
        ['code' => 'CHF', 'countries' => ['Switzerland'], 'name' => 'Swiss franc', 'symbol' => '&#67;&#72;&#102;'],
        ['code' => 'SRD', 'countries' => ['Suriname'], 'name' => 'Suriname Dollar', 'symbol' => '&#83;&#114;&#36;'],
        ['code' => 'SYP', 'countries' => ['Syria'], 'name' => 'Syrian pound', 'symbol' => '&#163;&#83;'],
        ['code' => 'TWD', 'countries' => ['Taiwan'], 'name' => 'New Taiwan dollar', 'symbol' => '&#78;&#84;&#36;'],
        ['code' => 'THB', 'countries' => ['Thailand'], 'name' => 'Thai baht', 'symbol' => '&#3647;'],
        ['code' => 'TTD', 'countries' => ['Trinidad', 'Tobago'], 'name' => 'Trinidad and Tobago dollar', 'symbol' => '&#84;&#84;&#36;'],
        ['code' => 'TRY', 'countries' => ['Turkey', 'Turkish Republic of Northern Cyprus'], 'name' => 'Turkey Lira', 'symbol' => '&#8378;'],
        ['code' => 'TVD', 'countries' => ['Tuvalu'], 'name' => 'Tuvaluan dollar', 'symbol' => '&#84;&#86;&#36;'],
        ['code' => 'UAH', 'countries' => ['Ukraine'], 'name' => 'Ukrainian hryvnia', 'symbol' => '&#8372;'],
        ['code' => 'GBP', 'countries' => ['United Kingdom', 'Jersey', 'Guernsey', 'The Isle of Man', 'Gibraltar', 'South Georgia', 'The South Sandwich Islands', 'The British Antarctic', 'Territory', 'Tristan da Cunha'], 'name' => 'Pound sterling', 'symbol' => '&#163;'],
        ['code' => 'UGX', 'countries' => ['Uganda'], 'name' => 'Ugandan shilling', 'symbol' => '&#85;&#83;&#104;'],
        ['code' => 'UYU', 'countries' => ['Uruguayan'], 'name' => 'Peso Uruguayolar', 'symbol' => '&#36;&#85;'],
        ['code' => 'UZS', 'countries' => ['Uzbekistan'], 'name' => 'Uzbekistani soʻm', 'symbol' => '&#1083;&#1074;'],
        ['code' => 'VEF', 'countries' => ['Venezuela'], 'name' => 'Venezuelan bolívar', 'symbol' => '&#66;&#115;'],
        ['code' => 'VND', 'countries' => ['Vietnam'], 'name' => 'Vietnamese dong (Đồng)', 'symbol' => '&#8363;'],
        ['code' => 'VND', 'countries' => ['Yemen'], 'name' => 'Yemeni rial', 'symbol' => '&#65020;'],
        ['code' => 'ZWD', 'countries' => ['Zimbabwe'], 'name' => 'Zimbabwean dollar', 'symbol' => '&#90;&#36;'],
    ];

    if ($all) {
        return $currencies;
    }
    return $currencies[$code]['symbol'];
}

function timetotimestamp($time)
{
    // Create a DateTime object from the provided time string
    $date = new DateTime($time);

    // Format the date as 'YYYYMMDDHHMMSS'
    $formattedDateTime = $date->format('YmdHis');

    return $formattedDateTime;
}

function renderJobType($placeType)
{
    // Define the mapping for place types
    $placeTypeMap = [
        "ons" => "Onsite",
        "rem" => "Remote",
        "hyb" => "Hybrid"
    ];

    // Return the job type based on the placeType key
    // If the key does not exist, it will return null (or you can set a default value)
    return isset($placeTypeMap[$placeType]) ? $placeTypeMap[$placeType] : null;
}

function renderRoleType($roletypes = '')
{
    if ($roletypes == '') {
        return '';
    }
    // Define the mapping for role types
    $roleTypeMap = [
        "remo" => "Remote Work",
        "full" => "Full Time",
        "part" => "Part Time",
        "temp" => "Temporary",
        "free" => "Freelance",
        "cont" => "Contract",
        "pdin" => "Paid Internship",
        "unin" => "Unpaid Internship",
        "voln" => "Volunteer"
    ];

    // Map the role types from the input array to their corresponding labels
    $roleItems = array_map(function ($type) use ($roleTypeMap) {
        return isset($roleTypeMap[$type]) ? $roleTypeMap[$type] : $type;
    }, $roletypes);

    // Join the role items with commas
    $roleTypeHtml = implode(', ', $roleItems);

    return $roleTypeHtml;
}

function newgenerateLocationHTML($locations = '')
{
    if ($locations == '') {
        return '';
    }
    return (string)$locations;
}

function displayTitleName($title_data)
{
    // Define the base site URL
    if ($title_data == '') {
        return '';
    }

    foreach ($title_data as $k => $role) {
        return '<span class="role_directory cursor-pointer underline-on-hover" data-roleid="' . $k . '" data-roleslug="' . $role['slug'] . '">' . ucfirst($role['value']) . '</span>';
    }
}

function displayCompanyName($company_data)
{
    // Define the base site URL
    if ($company_data == '') {
        return '';
    }

    foreach ($company_data as $k => $company) {
        return '<span class="company_directory cursor-pointer underline-on-hover" data-companyid="' . $company['id'] . '" data-companyslug="' . $company['slug'] . '">' . ucfirst($company['value']) . '</span>';
    }
}

function displaySkillHTML($skills = '')
{
    if ($skills == '') {
        return '';
    }
    $skillLinks = [];
    foreach ($skills as $k => $skill) {
        $skillLinks[] = '<li><span style="margin-right:5px;background-color:#797f871a; font-size:16px;" class="skill-list skill_list cursor-pointer skill_directory" data-skillid="' . $k . '">' . htmlspecialchars($skill['value']) . '</span></li>';
//      $skillLinks[]  =  '<li><a style="margin-right:5px;background-color:#797f871a; font-size:16px;" class="btn btn-sm skill_list" target="_blank" href="'.TAOH_SITE_URL_ROOT.'/asks/chat/Skill/'.$skill['slug'].'/'.$k.'">
//                  <span title="Join the skill chat for '.$skill['slug'].'">'.$skill['value'].'</span>
//              </a></li>';
    }

    return implode('', $skillLinks);
}

function newgenerateCompanyHTML($companies = '', $cmp_name = false)
{
    if ($companies == '') {
        return '';
    }
    $companyLinks = [];
    foreach ($companies as $k => $company) {
//        $companyId = $company['id'];
        $companyName = $cmp_name ? $company['title'] : $company['name'];
        $companyLinks[] = '<span class="company_directory cursor-pointer underline-on-hover" data-companyid="' . $company['id'] . '" data-companyslug="' . $company['slug'] . '">' . htmlspecialchars($companyName) . '</span>';
//      $companyLinks[]  =  '<a target="_BLANK" href="'.TAOH_SITE_URL_ROOT.'/asks/chat/Org/'.$companyName.'/'.$companyId.'">
//                  <span title="Join the role chat for '.$companyName.'">'.ucfirst($companyName).'</span>
//              </a>';
    }

    return implode('', $companyLinks);
}

function newgenerateSkillHTML($skills = '')
{
    if ($skills == '') {
        return '';
    }
    // Map over the skills array to generate the links
    $skillLinks = [];
    foreach ($skills as $k => $skill) {
        $skillLinks[] = '<li><span class="skill-list skill_list cursor-pointer skill_directory" data-skillid="' . $skill['id'] . '" data-skillslug="' . $skill['slug'] . '">' . htmlspecialchars($skill['title']) . '</span></li>';
//        $skillLinks[] = '<li><a target="_blank" href="'.TAOH_SITE_URL_ROOT.'/asks/chat/Skill/'.$skill['title'].'/'.$skill['id'].'">
//                  <span title="Join the skill chat for '.$skill['title'].'">'.$skill['title'].'</span>
//              </a></li>';
    }

    // Join the generated links into a single string
    return implode('', $skillLinks);
}


function formatDate($dateString)
{
    // Create a DateTime object from the provided date string
    $date = new DateTime($dateString);

    // Format the date to "Month Day, Year" (e.g., "August 28, 2024")
    return $date->format('F j, Y');
}

function taohFullyearConvert($timestamp, $convert = false)
{

    $timezone = 'Asia/Kolkata';// specify your timezone
    if (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->local_timezone)) {
        $timezone = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->local_timezone;
    }
    //$timezone = 'America/New_york';// specify your timezone

    // Create a DateTime object with the specified timezone
    $date = DateTime::createFromFormat('YmdHis', $timestamp, new DateTimeZone($timezone));

    //echo "<br><pre>";print_r($date);
    if ($date === false) {
        echo "few seconds ago";
        exit;
    } else {
        $seconds = $date->getTimestamp();
        //echo "Seconds since Unix epoch: " . $seconds;
    }

    $gmtTime = $date->format('Y-m-d H:i:s');
    // Create a DateTime object in GMT
    $gmtdateTime = new DateTime($gmtTime, new DateTimeZone('GMT'));

// Specify the target timezone (e.g., 'America/New_York')
    $targetTimezone = new DateTimeZone($timezone);

// Convert the time to the target timezone
    $gmtdateTime->setTimezone($targetTimezone);
    /*echo "<br><pre>";print_r($gmtdateTime);
    // Output the result
    echo "GMT Time: " . $gmtTime . "\n";
    echo "Converted Time: " . $gmtdateTime->format('Y-m-d H:i:s') . "\n";
    */
// Create a DateTime object with the desired timezone
    $dateTime = new DateTime('now', new DateTimeZone($timezone));

// Output the current time in the specified format
//echo "Current Time in $timezone: " . $dateTime->format('Y-m-d H:i:s') . "\n";


// Calculate the difference
    $interval = $dateTime->diff($gmtdateTime);

// Output the difference
//echo "Time difference: " . $interval->format('%y years, %m months, %d days, %h hours, %i minutes, %s seconds') . "\n";


    /*



      echo "===========".$timestamp;
      $postedTime = DateTime::createFromFormat('YmdHis', $timestamp, new DateTimeZone($timezone));
      echo "<br><pre>";print_r($postedTime);
      $currentTime = new DateTime('now', new DateTimeZone($timezone));
      echo "<br><pre>";print_r($currentTime);
      $interval = $postedTime->diff($currentTime);

      // Format the difference
     // echo "Difference: " . $interval->format('%y years, %m months, %d days, %h hours, %i minutes, %s seconds');


      echo "====";print_R($interval);die();
    */

    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->d;
    $hours = $interval->h;
    $minutes = $interval->m;
    $seconds = $interval->s;

    // Determine the appropriate time unit to return
    if ($years > 0) {
        return $years . ' year ago';
    } elseif ($months > 0) {
        return $months . ' month ago';
    } elseif ($days > 0) {
        return $days . ' days ago';
    } elseif ($hours > 0) {
        return $hours . ' hours ago';
    } elseif ($minutes > 0) {
        return $minutes . ' minutes ago';
    } else {
        return $seconds . ' seconds ago';
    }
}

// Function to combine strings
function combineStrings($array, $current = '', $index = 0)
{
    // If we've reached the end of the array, print the current combination
    if ($index === count($array)) {
        echo $current . "\n";
        return;
    }

    // Loop through each string in the current index of the array
    combineStrings($array, $current . ' - ' . $array[$index], $index + 1);

    // Optionally include the case where you skip the current string
    combineStrings($array, $current, $index + 1);
}

function getCombinations($array, $current = '', $index = 0): array
{
    if ($index === count($array)) {
        return $current !== '' ? [$current] : []; // Return array with non-empty combination
    }

    // Include the current word and skip the current word in one return statement
    return array_merge(
        getCombinations($array, $current . ($current ? ', ' : '') . $array[$index], $index + 1),
        getCombinations($array, $current, $index + 1)
    );
}


function taoh_title_desc_encode(?string $t): string
{
    if (empty($t)) return '';
    return urlencode(htmlspecialchars($t, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}

function taoh_title_desc_decode(?string $t): string
{
    if (empty($t)) return '';
    $t = html_entity_decode((function_exists('custom_urldecode') ? custom_urldecode($t) : urldecode($t)), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $t = function_exists('cleanFontstyle') ? cleanFontstyle($t) : $t;
    return (string)(function_exists('displayTaohFormatted') ? displayTaohFormatted($t) : $t);
}


/*function taoh_title_desc_encode($text){
    return urlEncode(htmlspecialchars($text));
}

function taoh_title_desc_decode($text){
    return displayTaohFormatted(cleanFontstyle(html_entity_decode(custom_urldecode($text))));
}*/

function taoh_blog_desc_decode($text)
{
    return displayTaohFormatted(cleanFontstyle(html_entity_decode(html_entity_decode(custom_urldecode($text)))));
}

