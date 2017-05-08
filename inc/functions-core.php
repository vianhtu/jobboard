<?php
/**
 * JobBoard Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		FOX
 * @category 	Core
 * @package 	JobBoard/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include( 'functions-conditional.php' );
include( 'functions-page.php' );

function jb_get_option($key, $default = ''){
    global $jobboard_options;

    if(empty($jobboard_options))
        $GLOBALS['jobboard_options'] = get_option('jobboard_options');

    if(!empty($jobboard_options[$key])){
        return $jobboard_options[$key];
    } else {
        return $default;
    }
}

function jb_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args );
    }

    $located = jb_get_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
        return;
    }

    $located = apply_filters( 'jobboard_template_part', $located, $template_name, $args, $template_path, $default_path );

    do_action( 'jobboard_before_template', $template_name, $template_path, $located, $args );

    include( $located );

    do_action( 'jobboard_after_template', $template_name, $template_path, $located, $args );
}

function jb_get_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    if ( ! $template_path ) {
        $template_path = JB()->template_path();
    }

    if ( ! $default_path ) {
        $default_path = JB()->plugin_directory . 'templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            $template_name
        )
    );

    // Get default template/
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return apply_filters( 'jobboard_locate_template', $template, $template_name, $template_path );
}

function jb_get_template_part( $slug, $name = '' ) {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/jobboard/slug-name.php
    if ( $name ) {
        $template = locate_template( array( "{$slug}-{$name}.php", JB()->template_path() . "{$slug}-{$name}.php" ) );
    }

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( JB()->plugin_directory . "templates/{$slug}-{$name}.php" ) ) {
        $template = JB()->plugin_directory . "templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/jobboard/slug.php
    if ( ! $template ) {
        $template = locate_template( array( "{$slug}.php", JB()->template_path() . "{$slug}.php" ) );
    }

    if ( ! $template ) {
        $template = JB()->plugin_directory . "templates/{$slug}.php";
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters( 'jb/core/template/part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false );
    }
}

function jb_the_placeholder_image($size = '50x50', $alt = ''){
    echo '<img src="'.esc_url(jb_get_placeholder_image($size)).'" alt="'.esc_attr($alt).'">';
}

function jb_get_placeholder_image($size = '50x50'){

    $image_url = JB()->plugin_directory_uri . "assets/images/placeholder.png";
    $image_dir = JB()->plugin_directory . "assets/images/placeholder{$size}.png";

    if(file_exists($image_dir)) {
        $image_url = JB()->plugin_directory_uri . "assets/images/placeholder{$size}.png";
    }

    return apply_filters('jb/core/placeholder/image', $image_url, $size);
}

function jb_get_current_user_role($id = ''){

    $roles = array();

    if(!$id){
        $id = get_current_user_id();
    }

    if(!$id){
        return $roles;
    }

    $user = get_user_by('id', $id);

    if(!$user){
        return $roles;
    }

    return $user->roles;
}

function jb_get_timeago( $ptime ){

    $estimate_time = time() - $ptime;

    if( $estimate_time < 1 )
    {
        return esc_html__('1 Second Ago', 'jobboard');
    }

    $condition = array(
        12 * 30 * 24 * 60 * 60  =>  esc_html__('year', 'jobboard'),
        30 * 24 * 60 * 60       =>  esc_html__('month', 'jobboard'),
        24 * 60 * 60            =>  esc_html__('day', 'jobboard'),
        60 * 60                 =>  esc_html__('hour', 'jobboard'),
        60                      =>  esc_html__('minute', 'jobboard'),
        1                       =>  esc_html__('second', 'jobboard')
    );

    foreach( $condition as $secs => $str )
    {
        $d = $estimate_time / $secs;

        if( $d >= 1 )
        {
            $r = round( $d );
            return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ' . esc_html__('ago', 'jobboard');
        }
    }
}

function jb_get_file_icon($type = ''){

    $icon = 'fa fa-file-o';

    if(strpos($type, 'image') !== false){
        $icon = 'fa fa-file-image-o';
    } elseif ($type == 'application/pdf'){
        $icon = 'fa fa-file-pdf-o';
    } elseif (strpos($type, 'ms-excel') !== false || strpos($type, 'spreadsheetml') !== false){
        $icon = 'fa fa-file-excel-o';
    } elseif (strpos($type, 'ms-word') !== false || strpos($type, 'wordprocessingml') !== false){
        $icon = 'fa fa-file-word-o';
    } elseif (strpos($type, 'ms-powerpoint') !== false || strpos($type, 'presentationml') !== false){
        $icon = 'fa fa-file-powerpoint-o';
    } elseif (strpos($type, 'video') !== false){
        $icon = 'fa fa-file-video-o';
    } elseif (strpos($type, 'audio') !== false){
        $icon = 'fa fa-file-audio-o';
    } elseif (strpos($type, 'text') !== false){
        $icon = 'fa fa-file-text-o';
    } elseif ($type == 'application/zip'){
        $icon = 'fa fa-file-archive-o';
    }

    return apply_filters('jb/core/file/icon', $icon, $type);
}

function jb_get_currencies() {
    return array_unique(
        apply_filters( 'jb/core/currencies',
            array(
                'AED' => __( 'United Arab Emirates dirham', 'jobboard-package' ),
                'AFN' => __( 'Afghan afghani', 'jobboard-package' ),
                'ALL' => __( 'Albanian lek', 'jobboard-package' ),
                'AMD' => __( 'Armenian dram', 'jobboard-package' ),
                'ANG' => __( 'Netherlands Antillean guilder', 'jobboard-package' ),
                'AOA' => __( 'Angolan kwanza', 'jobboard-package' ),
                'ARS' => __( 'Argentine peso', 'jobboard-package' ),
                'AUD' => __( 'Australian dollar', 'jobboard-package' ),
                'AWG' => __( 'Aruban florin', 'jobboard-package' ),
                'AZN' => __( 'Azerbaijani manat', 'jobboard-package' ),
                'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'jobboard-package' ),
                'BBD' => __( 'Barbadian dollar', 'jobboard-package' ),
                'BDT' => __( 'Bangladeshi taka', 'jobboard-package' ),
                'BGN' => __( 'Bulgarian lev', 'jobboard-package' ),
                'BHD' => __( 'Bahraini dinar', 'jobboard-package' ),
                'BIF' => __( 'Burundian franc', 'jobboard-package' ),
                'BMD' => __( 'Bermudian dollar', 'jobboard-package' ),
                'BND' => __( 'Brunei dollar', 'jobboard-package' ),
                'BOB' => __( 'Bolivian boliviano', 'jobboard-package' ),
                'BRL' => __( 'Brazilian real', 'jobboard-package' ),
                'BSD' => __( 'Bahamian dollar', 'jobboard-package' ),
                'BTC' => __( 'Bitcoin', 'jobboard-package' ),
                'BTN' => __( 'Bhutanese ngultrum', 'jobboard-package' ),
                'BWP' => __( 'Botswana pula', 'jobboard-package' ),
                'BYR' => __( 'Belarusian ruble', 'jobboard-package' ),
                'BZD' => __( 'Belize dollar', 'jobboard-package' ),
                'CAD' => __( 'Canadian dollar', 'jobboard-package' ),
                'CDF' => __( 'Congolese franc', 'jobboard-package' ),
                'CHF' => __( 'Swiss franc', 'jobboard-package' ),
                'CLP' => __( 'Chilean peso', 'jobboard-package' ),
                'CNY' => __( 'Chinese yuan', 'jobboard-package' ),
                'COP' => __( 'Colombian peso', 'jobboard-package' ),
                'CRC' => __( 'Costa Rican col&oacute;n', 'jobboard-package' ),
                'CUC' => __( 'Cuban convertible peso', 'jobboard-package' ),
                'CUP' => __( 'Cuban peso', 'jobboard-package' ),
                'CVE' => __( 'Cape Verdean escudo', 'jobboard-package' ),
                'CZK' => __( 'Czech koruna', 'jobboard-package' ),
                'DJF' => __( 'Djiboutian franc', 'jobboard-package' ),
                'DKK' => __( 'Danish krone', 'jobboard-package' ),
                'DOP' => __( 'Dominican peso', 'jobboard-package' ),
                'DZD' => __( 'Algerian dinar', 'jobboard-package' ),
                'EGP' => __( 'Egyptian pound', 'jobboard-package' ),
                'ERN' => __( 'Eritrean nakfa', 'jobboard-package' ),
                'ETB' => __( 'Ethiopian birr', 'jobboard-package' ),
                'EUR' => __( 'Euro', 'jobboard-package' ),
                'FJD' => __( 'Fijian dollar', 'jobboard-package' ),
                'FKP' => __( 'Falkland Islands pound', 'jobboard-package' ),
                'GBP' => __( 'Pound sterling', 'jobboard-package' ),
                'GEL' => __( 'Georgian lari', 'jobboard-package' ),
                'GGP' => __( 'Guernsey pound', 'jobboard-package' ),
                'GHS' => __( 'Ghana cedi', 'jobboard-package' ),
                'GIP' => __( 'Gibraltar pound', 'jobboard-package' ),
                'GMD' => __( 'Gambian dalasi', 'jobboard-package' ),
                'GNF' => __( 'Guinean franc', 'jobboard-package' ),
                'GTQ' => __( 'Guatemalan quetzal', 'jobboard-package' ),
                'GYD' => __( 'Guyanese dollar', 'jobboard-package' ),
                'HKD' => __( 'Hong Kong dollar', 'jobboard-package' ),
                'HNL' => __( 'Honduran lempira', 'jobboard-package' ),
                'HRK' => __( 'Croatian kuna', 'jobboard-package' ),
                'HTG' => __( 'Haitian gourde', 'jobboard-package' ),
                'HUF' => __( 'Hungarian forint', 'jobboard-package' ),
                'IDR' => __( 'Indonesian rupiah', 'jobboard-package' ),
                'ILS' => __( 'Israeli new shekel', 'jobboard-package' ),
                'IMP' => __( 'Manx pound', 'jobboard-package' ),
                'INR' => __( 'Indian rupee', 'jobboard-package' ),
                'IQD' => __( 'Iraqi dinar', 'jobboard-package' ),
                'IRR' => __( 'Iranian rial', 'jobboard-package' ),
                'ISK' => __( 'Icelandic kr&oacute;na', 'jobboard-package' ),
                'JEP' => __( 'Jersey pound', 'jobboard-package' ),
                'JMD' => __( 'Jamaican dollar', 'jobboard-package' ),
                'JOD' => __( 'Jordanian dinar', 'jobboard-package' ),
                'JPY' => __( 'Japanese yen', 'jobboard-package' ),
                'KES' => __( 'Kenyan shilling', 'jobboard-package' ),
                'KGS' => __( 'Kyrgyzstani som', 'jobboard-package' ),
                'KHR' => __( 'Cambodian riel', 'jobboard-package' ),
                'KMF' => __( 'Comorian franc', 'jobboard-package' ),
                'KPW' => __( 'North Korean won', 'jobboard-package' ),
                'KRW' => __( 'South Korean won', 'jobboard-package' ),
                'KWD' => __( 'Kuwaiti dinar', 'jobboard-package' ),
                'KYD' => __( 'Cayman Islands dollar', 'jobboard-package' ),
                'KZT' => __( 'Kazakhstani tenge', 'jobboard-package' ),
                'LAK' => __( 'Lao kip', 'jobboard-package' ),
                'LBP' => __( 'Lebanese pound', 'jobboard-package' ),
                'LKR' => __( 'Sri Lankan rupee', 'jobboard-package' ),
                'LRD' => __( 'Liberian dollar', 'jobboard-package' ),
                'LSL' => __( 'Lesotho loti', 'jobboard-package' ),
                'LYD' => __( 'Libyan dinar', 'jobboard-package' ),
                'MAD' => __( 'Moroccan dirham', 'jobboard-package' ),
                'MDL' => __( 'Moldovan leu', 'jobboard-package' ),
                'MGA' => __( 'Malagasy ariary', 'jobboard-package' ),
                'MKD' => __( 'Macedonian denar', 'jobboard-package' ),
                'MMK' => __( 'Burmese kyat', 'jobboard-package' ),
                'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'jobboard-package' ),
                'MOP' => __( 'Macanese pataca', 'jobboard-package' ),
                'MRO' => __( 'Mauritanian ouguiya', 'jobboard-package' ),
                'MUR' => __( 'Mauritian rupee', 'jobboard-package' ),
                'MVR' => __( 'Maldivian rufiyaa', 'jobboard-package' ),
                'MWK' => __( 'Malawian kwacha', 'jobboard-package' ),
                'MXN' => __( 'Mexican peso', 'jobboard-package' ),
                'MYR' => __( 'Malaysian ringgit', 'jobboard-package' ),
                'MZN' => __( 'Mozambican metical', 'jobboard-package' ),
                'NAD' => __( 'Namibian dollar', 'jobboard-package' ),
                'NGN' => __( 'Nigerian naira', 'jobboard-package' ),
                'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'jobboard-package' ),
                'NOK' => __( 'Norwegian krone', 'jobboard-package' ),
                'NPR' => __( 'Nepalese rupee', 'jobboard-package' ),
                'NZD' => __( 'New Zealand dollar', 'jobboard-package' ),
                'OMR' => __( 'Omani rial', 'jobboard-package' ),
                'PAB' => __( 'Panamanian balboa', 'jobboard-package' ),
                'PEN' => __( 'Peruvian nuevo sol', 'jobboard-package' ),
                'PGK' => __( 'Papua New Guinean kina', 'jobboard-package' ),
                'PHP' => __( 'Philippine peso', 'jobboard-package' ),
                'PKR' => __( 'Pakistani rupee', 'jobboard-package' ),
                'PLN' => __( 'Polish z&#x142;oty', 'jobboard-package' ),
                'PRB' => __( 'Transnistrian ruble', 'jobboard-package' ),
                'PYG' => __( 'Paraguayan guaran&iacute;', 'jobboard-package' ),
                'QAR' => __( 'Qatari riyal', 'jobboard-package' ),
                'RON' => __( 'Romanian leu', 'jobboard-package' ),
                'RSD' => __( 'Serbian dinar', 'jobboard-package' ),
                'RUB' => __( 'Russian ruble', 'jobboard-package' ),
                'RWF' => __( 'Rwandan franc', 'jobboard-package' ),
                'SAR' => __( 'Saudi riyal', 'jobboard-package' ),
                'SBD' => __( 'Solomon Islands dollar', 'jobboard-package' ),
                'SCR' => __( 'Seychellois rupee', 'jobboard-package' ),
                'SDG' => __( 'Sudanese pound', 'jobboard-package' ),
                'SEK' => __( 'Swedish krona', 'jobboard-package' ),
                'SGD' => __( 'Singapore dollar', 'jobboard-package' ),
                'SHP' => __( 'Saint Helena pound', 'jobboard-package' ),
                'SLL' => __( 'Sierra Leonean leone', 'jobboard-package' ),
                'SOS' => __( 'Somali shilling', 'jobboard-package' ),
                'SRD' => __( 'Surinamese dollar', 'jobboard-package' ),
                'SSP' => __( 'South Sudanese pound', 'jobboard-package' ),
                'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'jobboard-package' ),
                'SYP' => __( 'Syrian pound', 'jobboard-package' ),
                'SZL' => __( 'Swazi lilangeni', 'jobboard-package' ),
                'THB' => __( 'Thai baht', 'jobboard-package' ),
                'TJS' => __( 'Tajikistani somoni', 'jobboard-package' ),
                'TMT' => __( 'Turkmenistan manat', 'jobboard-package' ),
                'TND' => __( 'Tunisian dinar', 'jobboard-package' ),
                'TOP' => __( 'Tongan pa&#x2bb;anga', 'jobboard-package' ),
                'TRY' => __( 'Turkish lira', 'jobboard-package' ),
                'TTD' => __( 'Trinidad and Tobago dollar', 'jobboard-package' ),
                'TWD' => __( 'New Taiwan dollar', 'jobboard-package' ),
                'TZS' => __( 'Tanzanian shilling', 'jobboard-package' ),
                'UAH' => __( 'Ukrainian hryvnia', 'jobboard-package' ),
                'UGX' => __( 'Ugandan shilling', 'jobboard-package' ),
                'USD' => __( 'United States dollar', 'jobboard-package' ),
                'UYU' => __( 'Uruguayan peso', 'jobboard-package' ),
                'UZS' => __( 'Uzbekistani som', 'jobboard-package' ),
                'VEF' => __( 'Venezuelan bol&iacute;var', 'jobboard-package' ),
                'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'jobboard-package' ),
                'VUV' => __( 'Vanuatu vatu', 'jobboard-package' ),
                'WST' => __( 'Samoan t&#x101;l&#x101;', 'jobboard-package' ),
                'XAF' => __( 'Central African CFA franc', 'jobboard-package' ),
                'XCD' => __( 'East Caribbean dollar', 'jobboard-package' ),
                'XOF' => __( 'West African CFA franc', 'jobboard-package' ),
                'XPF' => __( 'CFP franc', 'jobboard-package' ),
                'YER' => __( 'Yemeni rial', 'jobboard-package' ),
                'ZAR' => __( 'South African rand', 'jobboard-package' ),
                'ZMW' => __( 'Zambian kwacha', 'jobboard-package' ),
            )
        )
    );
}

function jb_get_currency_symbol( $currency = 'USD' ) {
    $symbols = apply_filters( 'jb/core/currency/symbols', array(
        'AED' => '&#x62f;.&#x625;',
        'AFN' => '&#x60b;',
        'ALL' => 'L',
        'AMD' => 'AMD',
        'ANG' => '&fnof;',
        'AOA' => 'Kz',
        'ARS' => '&#36;',
        'AUD' => '&#36;',
        'AWG' => '&fnof;',
        'AZN' => 'AZN',
        'BAM' => 'KM',
        'BBD' => '&#36;',
        'BDT' => '&#2547;&nbsp;',
        'BGN' => '&#1083;&#1074;.',
        'BHD' => '.&#x62f;.&#x628;',
        'BIF' => 'Fr',
        'BMD' => '&#36;',
        'BND' => '&#36;',
        'BOB' => 'Bs.',
        'BRL' => '&#82;&#36;',
        'BSD' => '&#36;',
        'BTC' => '&#3647;',
        'BTN' => 'Nu.',
        'BWP' => 'P',
        'BYR' => 'Br',
        'BZD' => '&#36;',
        'CAD' => '&#36;',
        'CDF' => 'Fr',
        'CHF' => '&#67;&#72;&#70;',
        'CLP' => '&#36;',
        'CNY' => '&yen;',
        'COP' => '&#36;',
        'CRC' => '&#x20a1;',
        'CUC' => '&#36;',
        'CUP' => '&#36;',
        'CVE' => '&#36;',
        'CZK' => '&#75;&#269;',
        'DJF' => 'Fr',
        'DKK' => 'DKK',
        'DOP' => 'RD&#36;',
        'DZD' => '&#x62f;.&#x62c;',
        'EGP' => 'EGP',
        'ERN' => 'Nfk',
        'ETB' => 'Br',
        'EUR' => '&euro;',
        'FJD' => '&#36;',
        'FKP' => '&pound;',
        'GBP' => '&pound;',
        'GEL' => '&#x10da;',
        'GGP' => '&pound;',
        'GHS' => '&#x20b5;',
        'GIP' => '&pound;',
        'GMD' => 'D',
        'GNF' => 'Fr',
        'GTQ' => 'Q',
        'GYD' => '&#36;',
        'HKD' => '&#36;',
        'HNL' => 'L',
        'HRK' => 'Kn',
        'HTG' => 'G',
        'HUF' => '&#70;&#116;',
        'IDR' => 'Rp',
        'ILS' => '&#8362;',
        'IMP' => '&pound;',
        'INR' => '&#8377;',
        'IQD' => '&#x639;.&#x62f;',
        'IRR' => '&#xfdfc;',
        'ISK' => 'kr.',
        'JEP' => '&pound;',
        'JMD' => '&#36;',
        'JOD' => '&#x62f;.&#x627;',
        'JPY' => '&yen;',
        'KES' => 'KSh',
        'KGS' => '&#x441;&#x43e;&#x43c;',
        'KHR' => '&#x17db;',
        'KMF' => 'Fr',
        'KPW' => '&#x20a9;',
        'KRW' => '&#8361;',
        'KWD' => '&#x62f;.&#x643;',
        'KYD' => '&#36;',
        'KZT' => 'KZT',
        'LAK' => '&#8365;',
        'LBP' => '&#x644;.&#x644;',
        'LKR' => '&#xdbb;&#xdd4;',
        'LRD' => '&#36;',
        'LSL' => 'L',
        'LYD' => '&#x644;.&#x62f;',
        'MAD' => '&#x62f;. &#x645;.',
        'MAD' => '&#x62f;.&#x645;.',
        'MDL' => 'L',
        'MGA' => 'Ar',
        'MKD' => '&#x434;&#x435;&#x43d;',
        'MMK' => 'Ks',
        'MNT' => '&#x20ae;',
        'MOP' => 'P',
        'MRO' => 'UM',
        'MUR' => '&#x20a8;',
        'MVR' => '.&#x783;',
        'MWK' => 'MK',
        'MXN' => '&#36;',
        'MYR' => '&#82;&#77;',
        'MZN' => 'MT',
        'NAD' => '&#36;',
        'NGN' => '&#8358;',
        'NIO' => 'C&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'NZD' => '&#36;',
        'OMR' => '&#x631;.&#x639;.',
        'PAB' => 'B/.',
        'PEN' => 'S/.',
        'PGK' => 'K',
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PRB' => '&#x440;.',
        'PYG' => '&#8370;',
        'QAR' => '&#x631;.&#x642;',
        'RMB' => '&yen;',
        'RON' => 'lei',
        'RSD' => '&#x434;&#x438;&#x43d;.',
        'RUB' => '&#8381;',
        'RWF' => 'Fr',
        'SAR' => '&#x631;.&#x633;',
        'SBD' => '&#36;',
        'SCR' => '&#x20a8;',
        'SDG' => '&#x62c;.&#x633;.',
        'SEK' => '&#107;&#114;',
        'SGD' => '&#36;',
        'SHP' => '&pound;',
        'SLL' => 'Le',
        'SOS' => 'Sh',
        'SRD' => '&#36;',
        'SSP' => '&pound;',
        'STD' => 'Db',
        'SYP' => '&#x644;.&#x633;',
        'SZL' => 'L',
        'THB' => '&#3647;',
        'TJS' => '&#x405;&#x41c;',
        'TMT' => 'm',
        'TND' => '&#x62f;.&#x62a;',
        'TOP' => 'T&#36;',
        'TRY' => '&#8378;',
        'TTD' => '&#36;',
        'TWD' => '&#78;&#84;&#36;',
        'TZS' => 'Sh',
        'UAH' => '&#8372;',
        'UGX' => 'UGX',
        'USD' => '&#36;',
        'UYU' => '&#36;',
        'UZS' => 'UZS',
        'VEF' => 'Bs F',
        'VND' => '&#8363;',
        'VUV' => 'Vt',
        'WST' => 'T',
        'XAF' => 'Fr',
        'XCD' => '&#36;',
        'XOF' => 'Fr',
        'XPF' => 'Fr',
        'YER' => '&#xfdfc;',
        'ZAR' => '&#82;',
        'ZMW' => 'ZK',
    ) );

    $currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

    return apply_filters( 'jb/core/currency/symbol', $currency_symbol, $currency );
}

function jb_get_salary_currency($salary, $currency = '$', $position = ''){

    if(!$position){
        $position = jb_get_option('currency-position', 'left');
    }

    switch ($position){
        case 'right':
            $salary_currency = $salary . $currency;
            break;
        case 'right_space':
            $salary_currency = $salary . ' ' . $currency;
            break;
        case 'left_space':
            $salary_currency = $currency . ' ' . $salary;
            break;
        default:
            $salary_currency = $currency . $salary;
            break;
    }

    return $salary_currency;
}

function jb_get_currencies_options(){

    $currencies = jb_get_currencies();

    foreach ($currencies as $key => $currency){
        $currencies[$key] = $currency . ' ('.jb_get_currency_symbol($key).')';
    }

    return $currencies;
}

function jb_get_type_options(){
    return jb_get_taxonomy_options(array('taxonomy' => 'jobboard-tax-types', 'hide_empty' => false));
}

function jb_get_specialism_options(){
    return jb_get_taxonomy_options(array('taxonomy' => 'jobboard-tax-specialisms', 'hide_empty' => false));
}

function jb_get_taxonomy_options($args = array()){

    $options    = array();
    $terms      = get_terms($args);

    if(is_wp_error($terms)){
        return $options;
    }

    foreach ($terms as $term){
        $options[$term->term_id] = $term->name;
    }

    return $options;
}

function jb_get_page_options(){
    $options    = array();
    $pages      = get_pages();

    if(!$pages){
        return $options;
    }

    foreach ($pages as $page){
        $options[$page->ID] = $page->post_title;
    }

    return $options;
}

/**
 * build class form array.
 *
 * @param array $class
 * @param bool $echo
 * @return array|string|void
 */
function jb_build_class($class = array(), $echo = true){

    if(empty($class)){
        return;
    }

    $class = implode(' ', $class);

    if($echo){
        echo esc_attr($class);
    } else {
        return $class;
    }
}

/**
 * return endpoint title.
 *
 * @param $endpoint
 * @return mixed
 */
function jb_endpoint_title($endpoint){
    return JB()->query->get_endpoint_title($endpoint);
}

function jb_class($class = array()){
    $class[] = 'jobboard-content';
    if(is_jb_jobs()){
        $class[] = 'jobboard-jobs';
    } elseif (is_jb_job()) {
        $class[] = 'jobboard-job';
    } elseif (is_jb_account_listing()){
        $class[] = 'jobboard-users';
    } elseif (is_jb_profile()){
        $class[] = 'jobboard-profile';
    } elseif(is_jb_dashboard()){
        $class[] = 'jobboard-dashboard';
    }

    echo esc_attr(jb_build_class(apply_filters('jobboard_class', $class)));
}

function jb_upload_files($file, $post = array()){

    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    $upload                     = wp_handle_upload($file, array( 'test_form' => false ));

    if(isset($upload['error'])){
        return false;
    }

    $file_name                  = sanitize_file_name(basename($upload['file']));

    $post['post_title']         = $file_name;
    $post['post_mime_type']     = $upload['type'];
    $id                         = wp_insert_attachment($post, $upload['file']);

    if(!$id){
        unlink($upload['file']);
        return false;
    }

    if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
    }

    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));

    return $id;
}

function jb_user_keys(){
    return apply_filters('jobboard_user_keys', array(
        'user_login',
        'user_nicename',
        'user_email',
        'display_name'
    ));
}

function jb_job_keys(){
    return apply_filters('jobboard_job_keys', array(
        'post_title',
        'post_content'
    ));
}

function jb_job_tax_keys(){
    return apply_filters('jobboard_job_tax_keys', array(
        'tags'          => 'names',
        'types'         => 'ids',
        'specialisms'   => 'ids',
        'locations'     => 'ids',
    ));
}

function jb_array_to_attributes($attributes = array()){

    if(empty($attributes)){
        return '';
    }

    $html = array();

    foreach ($attributes as $k => $v){
        $html[] = sprintf('%s="%s"', $k, $v);
    }

    return implode(' ', $html);
}

function jb_parse_custom_fields($field){

    if(!isset($field['type'])){
        return $field;
    }

    $default = array(
        'id'                => '',
        'name'              => $field['id'],
        'title'             => esc_html__('Title', 'jobboard'),
        'subtitle'          => '',
        'placeholder'       => '',
        'desc'              => '',
        'col'               => 12,
        'require'           => 0,
        'notice'            => esc_attr__('This is a required field.', 'jobboard'),
        'value'             => '',
        'class'             => ''
    );

    switch ($field['type']){
        case 'text':
            $field = wp_parse_args($field, array_merge($default, array(
                'input'         => 'text',
                'default'       => ''
            )));
            break;
        case 'media':
            $field = wp_parse_args($field, array_merge($default, array(
                'input'             => 'file',
                'button'            => esc_html__('Select File', 'jobboard'),
                'require-types'     => '',
                'require-dimension' => 1000
            )));
            break;
        case 'select':
            $field = wp_parse_args($field, array_merge($default, array(
                'multi'             => false,
                'options'           => array()
            )));
            break;
        case 'radio':
            $field = wp_parse_args($field, array_merge($default, array(
                'options'           => array()
            )));
            break;
        default :
            $field = wp_parse_args($field, $default);
            break;
    }

    return $field;
}

function jb_selected($selected, $current, $echo = true){
    if((is_array($selected) && in_array($current, $selected)) || $selected == $current){
        $result = " selected='selected'";
    } else {
        $result = '';
    }

    if($echo){
        echo $result;
    }

    return $result;
}

function jb_multiple($multiple = true, $echo = true){
    if($multiple){
        $result = " multiple='multiple'";
    } else {
        $result = '';
    }

    if($echo){
        echo $result;
    }

    return $result;
}

function jb_sort_terms(Array &$terms,Array &$into, $parent = 0)
{
    foreach ($terms as $i => $term) {
        if ($term->parent == $parent) {
            $into[$term->term_id] = $term;
            $parent = $term->term_id;
            unset($terms[$i]);
        }
    }
    if(count($terms) > 0){
        jb_sort_terms($terms, $into, $parent);
    }
}

function jb_checked($checked, $current, $echo = true){
    if(is_array($checked)){
        $result = in_array($current, $checked) ? " checked='checked'" : "";
    } elseif ($checked == $current){
        $result = " checked='checked'";
    } else {
        $result = "";
    }

    if($echo){
        echo $result;
    }

    return $result;
}

function jb_get_country(){
    return jb_get_terms(array(
        'taxonomy'      => 'jobboard-tax-locations',
        'hide_empty'    => false,
        'parent'        => 0
    ));
}

function jb_get_city($parent){
    return jb_get_terms(array(
        'taxonomy'      => 'jobboard-tax-locations',
        'hide_empty'    => false,
        'parent'        => $parent
    ));
}

function jb_get_district($parent){
    return jb_get_terms(array(
        'taxonomy'      => 'jobboard-tax-locations',
        'hide_empty'    => false,
        'parent'        => $parent
    ));
}

function jb_get_terms($args = array()){

    $_terms = array();

    if(!is_wp_error($terms = get_terms($args))){
        foreach ($terms as $term){
            $_terms[$term->term_id] = $term->name;
        }
    }

    return $_terms;
}

function jb_the_current_url(){
    echo jb_get_current_url();
}

function jb_get_current_url(){
    global $wp;
    return add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
}