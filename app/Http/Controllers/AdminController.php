<?php

namespace App\Http\Controllers;

use App\airline;
use App\AllCurrency;
use App\Booking;
use App\BookingDetail;
use App\BookingDetailLog;
use App\BookingLog;
use App\BookingMethod;
use App\booking_email;
use App\Category;
use App\code;
use App\Currency;
use App\CurrencyConversions;
use App\FinanceBookingDetail;
use App\FinanceBookingDetailLog;
use App\old_booking;
use App\payment;
use App\Product;
use App\Qoute;
use App\QouteDetail;
use App\QouteDetailLog;
use App\QouteLog;
use App\role;
use App\season;
use App\supervisor;
use App\Supplier;
use App\supplier_category;
use App\supplier_product;
use App\Template;
use App\User;
use App\ZohoCredential;
use Cache;
use Carbon\Carbon;
use Config;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Input;
use Redirect;
use Response;
use Session;
use Spatie\GoogleCalendar\Event;
use Validator;


class AdminController extends Controller
{
    public $cacheTimeOut;
    public function __construct(Request $request)
    {
        $this->cacheTimeOut = 1800;
    }

    public function index()
    {
        return view('admin.index');
    }

    public function checkReference(Request $request)
    {
        $ref_no = $request->id;
        $response = false;
        if (Qoute::where('ref_no', $request->id)->exists()) {
            $response = true;
        }
        return response()->json($response);
    }

    public function logout()
    {
        if (Auth::check()) {
            $id = Auth::user()->id;
            Auth::logout();
            user::where('id', '=', $id)->update(array('is_login' => 0));
            session()->flush();
            return Redirect::route('login')->with('success_message', 'Your session has been ended!');
        } else {
            return Redirect::route('admin');
        }
    }
    public function get_chapter(Request $request)
    {
        $matchThese = ['book_id' => $request->input('id')];
        $item_rec = DB::table('chapters')->where($matchThese)->select('id', 'title')->get();
        if ($request->ajax()) {
            return response()->json([
                'item_rec' => $item_rec,
            ]);
        }
    }

    public function currency_data(Request $request)
    {

        // $xes = [
        //     "USD" => [
        //         "name" => "US Dollar",
        //         "relatedTerms" => [
        //             "United States Dollar",
        //             "America",
        //             "American Samoa",
        //             "American Virgin Islands",
        //             "British Indian Ocean Territory",
        //             "British Virgin Islands",
        //             "Ecuador",
        //             "El Salvador",
        //             "Guam",
        //             "Haiti",
        //             "Micronesia",
        //             "Northern Mariana Islands",
        //             "Palau",
        //             "Panama",
        //             "Puerto Rico",
        //             "Turks and Caicos Islands",
        //             "United States Minor Outlying Islands",
        //             "Wake Island",
        //             "East Timor",
        //         ],
        //         "name_plural" => "US Dollars",
        //     ],
        //     "EUR" => [
        //         "name" => "Euro",
        //         "relatedTerms" => [
        //             "Euro Member Countries",
        //             "Andorra",
        //             "Austria",
        //             "Azores",
        //             "Baleares (Balearic Islands)",
        //             "Belgium",
        //             "Canary Islands",
        //             "Cyprus",
        //             "Finland",
        //             "France",
        //             "French Guiana",
        //             "French Southern Territories",
        //             "Germany",
        //             "Greece",
        //             "Guadeloupe",
        //             "Holland (Netherlands)",
        //             "Holy See (Vatican City)",
        //             "Ireland (Eire)",
        //             "Italy",
        //             "Luxembourg",
        //             "Madeira Islands",
        //             "Malta",
        //             "Monaco",
        //             "Montenegro",
        //             "Netherlands",
        //             "Portugal",
        //             "Réunion",
        //             "Saint Pierre and Miquelon",
        //             "Saint-Martin",
        //             "San Marino",
        //             "Slovakia",
        //             "Slovenia",
        //             "Spain",
        //             "Vatican City (The Holy See)",
        //             "Estonia",
        //             "Lithuania",
        //             "Latvia",
        //         ],
        //         "name_plural" => "Euros",
        //     ],
        //     "GBP" => [
        //         "name" => "British Pound",
        //         "relatedTerms" => [
        //             "United Kingdom Pound",
        //             "UK",
        //             "England",
        //             "Northern Ireland",
        //             "Scotland",
        //             "Wales",
        //             "Falkland Islands",
        //             "Gibraltar",
        //             "Guernsey",
        //             "Isle of Man",
        //             "Jersey",
        //             "Saint Helena and Ascension",
        //             "South Georgia and the South Sandwich Islands",
        //             "Tristan da Cunha",
        //         ],
        //         "name_plural" => "British Pounds",
        //     ],
        //     "INR" => [
        //         "name" => "Indian Rupee",
        //         "relatedTerms" => [
        //             "India Rupee",
        //             "Bhutan",
        //             "Nepal",
        //         ],
        //         "name_plural" => "Indian Rupees",
        //     ],
        //     "AUD" => [
        //         "name" => "Australian Dollar",
        //         "relatedTerms" => [
        //             "Australia Dollar",
        //             "Christmas Island",
        //             "Cocos (Keeling) Islands",
        //             "Norfolk Island",
        //             "Ashmore and Cartier Islands",
        //             "Australian Antarctic Territory",
        //             "Coral Sea Islands",
        //             "Heard Island",
        //             "McDonald Islands",
        //             "Kiribati",
        //             "Nauru",
        //         ],
        //         "name_plural" => "Australian Dollars",
        //     ],
        //     "CAD" => [
        //         "name" => "Canadian Dollar",
        //         "relatedTerms" => [
        //             "Canada Dollar",
        //         ],
        //         "name_plural" => "Canadian Dollars",
        //     ],
        //     "SGD" => [
        //         "name" => "Singapore Dollar",
        //         "relatedTerms" => [
        //             "Singapore Dollar",
        //         ],
        //         "name_plural" => "Singapore Dollars",
        //     ],
        //     "CHF" => [
        //         "name" => "Swiss Franc",
        //         "relatedTerms" => [
        //             "Switzerland Franc",
        //             "Liechtenstein",
        //             "Campione d'Italia",
        //             "Büsingen am Hochrhein",
        //         ],
        //         "name_plural" => "Swiss Francs",
        //     ],
        //     "MYR" => [
        //         "name" => "Malaysian Ringgit",
        //         "relatedTerms" => [
        //             "Malaysia Ringgit",
        //         ],
        //         "name_plural" => "Malaysian Ringgits",
        //     ],
        //     "JPY" => [
        //         "name" => "Japanese Yen",
        //         "relatedTerms" => [
        //             "Japan Yen",
        //         ],
        //         "name_plural" => "Japanese Yen",
        //     ],
        //     "CNY" => [
        //         "name" => "Chinese Yuan Renminbi",
        //         "relatedTerms" => [
        //             "China Yuan Renminbi",
        //         ],
        //         "name_plural" => "Chinese Yuan Renminbi",
        //     ],
        //     "NZD" => [
        //         "name" => "New Zealand Dollar",
        //         "relatedTerms" => [
        //             "New Zealand Dollar",
        //             "Cook Islands",
        //             "Niue",
        //             "Pitcairn Islands",
        //             "Tokelau",
        //         ],
        //         "name_plural" => "New Zealand Dollars",
        //     ],
        //     "THB" => [
        //         "name" => "Thai Baht",
        //         "relatedTerms" => [
        //             "Thailand Baht",
        //         ],
        //         "name_plural" => "Thai Baht",
        //     ],
        //     "HUF" => [
        //         "name" => "Hungarian Forint",
        //         "relatedTerms" => [
        //             "Hungary Forint",
        //         ],
        //         "name_plural" => "Hungarian Forints",
        //     ],
        //     "AED" => [
        //         "name" => "Emirati Dirham",
        //         "relatedTerms" => [
        //             "United Arab Emirates Dirham",
        //         ],
        //         "name_plural" => "Emirati Dirhams",
        //     ],
        //     "HKD" => [
        //         "name" => "Hong Kong Dollar",
        //         "relatedTerms" => [
        //             "Hong Kong Dollar",
        //         ],
        //         "name_plural" => "Hong Kong Dollars",
        //     ],
        //     "MXN" => [
        //         "name" => "Mexican Peso",
        //         "relatedTerms" => [
        //             "Mexico Peso",
        //         ],
        //         "name_plural" => "Mexican Pesos",
        //     ],
        //     "ZAR" => [
        //         "name" => "South African Rand",
        //         "relatedTerms" => [
        //             "South Africa Rand",
        //             "Lesotho",
        //             "Namibia",
        //         ],
        //         "name_plural" => "South African Rand",
        //     ],
        //     "PHP" => [
        //         "name" => "Philippine Peso",
        //         "relatedTerms" => [
        //             "Philippines Peso",
        //         ],
        //         "name_plural" => "Philippine Pesos",
        //     ],
        //     "SEK" => [
        //         "name" => "Swedish Krona",
        //         "relatedTerms" => [
        //             "Sweden Krona",
        //         ],
        //         "name_plural" => "Swedish Kronor",
        //     ],
        //     "IDR" => [
        //         "name" => "Indonesian Rupiah",
        //         "relatedTerms" => [
        //             "Indonesia Rupiah",
        //             "East Timor",
        //         ],
        //         "name_plural" => "Indonesian Rupiahs",
        //     ],
        //     "SAR" => [
        //         "name" => "Saudi Arabian Riyal",
        //         "relatedTerms" => [
        //             "Saudi Arabia Riyal",
        //         ],
        //         "name_plural" => "Saudi Arabian Riyals",
        //     ],
        //     "BRL" => [
        //         "name" => "Brazilian Real",
        //         "relatedTerms" => [
        //             "Brazil Real",
        //         ],
        //         "name_plural" => "Brazilian Reais",
        //     ],
        //     "TRY" => [
        //         "name" => "Turkish Lira",
        //         "relatedTerms" => [
        //             "Turkey Lira",
        //             "North Cyprus",
        //         ],
        //         "name_plural" => "Turkish Lire",
        //     ],
        //     "KES" => [
        //         "name" => "Kenyan Shilling",
        //         "relatedTerms" => [
        //             "Kenya Shilling",
        //         ],
        //         "name_plural" => "Kenyan Shillings",
        //     ],
        //     "KRW" => [
        //         "name" => "South Korean Won",
        //         "relatedTerms" => [
        //             "Korea (South) Won",
        //         ],
        //         "name_plural" => "South Korean Won",
        //     ],
        //     "EGP" => [
        //         "name" => "Egyptian Pound",
        //         "relatedTerms" => [
        //             "Egypt Pound",
        //             "Gaza Strip",
        //         ],
        //         "name_plural" => "Egyptian Pounds",
        //     ],
        //     "IQD" => [
        //         "name" => "Iraqi Dinar",
        //         "relatedTerms" => [
        //             "Iraq Dinar",
        //         ],
        //         "name_plural" => "Iraqi Dinars",
        //     ],
        //     "NOK" => [
        //         "name" => "Norwegian Krone",
        //         "relatedTerms" => [
        //             "Norway Krone",
        //             "Bouvet Island",
        //             "Svalbard",
        //             "Jan Mayen",
        //             "Queen Maud Land",
        //             "Peter I Island",
        //         ],
        //         "name_plural" => "Norwegian Kroner",
        //     ],
        //     "KWD" => [
        //         "name" => "Kuwaiti Dinar",
        //         "relatedTerms" => [
        //             "Kuwait Dinar",
        //         ],
        //         "name_plural" => "Kuwaiti Dinars",
        //     ],
        //     "RUB" => [
        //         "name" => "Russian Ruble",
        //         "relatedTerms" => [
        //             "Russia Ruble",
        //             "Tajikistan",
        //         ],
        //         "name_plural" => "Russian Rubles",
        //     ],
        //     "DKK" => [
        //         "name" => "Danish Krone",
        //         "relatedTerms" => [
        //             "Denmark Krone",
        //             "Faroe Islands",
        //             "Greenland",
        //         ],
        //         "name_plural" => "Danish Kroner",
        //     ],
        //     "PKR" => [
        //         "name" => "Pakistani Rupee",
        //         "relatedTerms" => [
        //             "Pakistan Rupee",
        //         ],
        //         "name_plural" => "Pakistani Rupees",
        //     ],
        //     "ILS" => [
        //         "name" => "Israeli Shekel",
        //         "relatedTerms" => [
        //             "Israel Shekel",
        //             "Palestinian Territories",
        //         ],
        //         "name_plural" => "Israeli New Shekels",
        //     ],
        //     "PLN" => [
        //         "name" => "Polish Zloty",
        //         "relatedTerms" => [
        //             "Poland Zloty",
        //         ],
        //         "name_plural" => "Polish Zlotych",
        //     ],
        //     "QAR" => [
        //         "name" => "Qatari Riyal",
        //         "relatedTerms" => [
        //             "Qatar Riyal",
        //         ],
        //         "name_plural" => "Qatari Rials",
        //     ],
        //     "XAU" => [
        //         "name" => "Gold Ounce",
        //         "relatedTerms" => [
        //             "Gold",
        //         ],
        //         "name_plural" => "Gold Ounces",
        //     ],
        //     "OMR" => [
        //         "name" => "Omani Rial",
        //         "relatedTerms" => [
        //             "Oman Rial",
        //         ],
        //         "name_plural" => "Omani Rials",
        //     ],
        //     "COP" => [
        //         "name" => "Colombian Peso",
        //         "relatedTerms" => [
        //             "Colombia Peso",
        //         ],
        //         "name_plural" => "Colombian Pesos",
        //     ],
        //     "CLP" => [
        //         "name" => "Chilean Peso",
        //         "relatedTerms" => [
        //             "Chile Peso",
        //         ],
        //         "name_plural" => "Chilean Pesos",
        //     ],
        //     "TWD" => [
        //         "name" => "Taiwan New Dollar",
        //         "relatedTerms" => [
        //             "Taiwan New Dollar",
        //         ],
        //         "name_plural" => "Taiwan New Dollars",
        //     ],
        //     "ARS" => [
        //         "name" => "Argentine Peso",
        //         "relatedTerms" => [
        //             "Argentina Peso",
        //             "Islas Malvinas",
        //         ],
        //         "name_plural" => "Argentine Pesos",
        //     ],
        //     "CZK" => [
        //         "name" => "Czech Koruna",
        //         "relatedTerms" => [
        //             "Czech Republic Koruna",
        //         ],
        //         "name_plural" => "Czech Koruny",
        //     ],
        //     "VND" => [
        //         "name" => "Vietnamese Dong",
        //         "relatedTerms" => [
        //             "Viet Nam Dong",
        //         ],
        //         "name_plural" => "Vietnamese Dongs",
        //     ],
        //     "MAD" => [
        //         "name" => "Moroccan Dirham",
        //         "relatedTerms" => [
        //             "Morocco Dirham",
        //             "Western Sahara",
        //         ],
        //         "name_plural" => "Moroccan Dirhams",
        //     ],
        //     "JOD" => [
        //         "name" => "Jordanian Dinar",
        //         "relatedTerms" => [
        //             "Jordan Dinar",
        //         ],
        //         "name_plural" => "Jordanian Dinars",
        //     ],
        //     "BHD" => [
        //         "name" => "Bahraini Dinar",
        //         "relatedTerms" => [
        //             "Bahrain Dinar",
        //         ],
        //         "name_plural" => "Bahraini Dinars",
        //     ],
        //     "XOF" => [
        //         "name" => "CFA Franc",
        //         "relatedTerms" => [
        //             "Communauté Financière Africaine (BCEAO) Franc",
        //             "Benin",
        //             "Burkina Faso",
        //             "Ivory Coast",
        //             "Guinea-Bissau",
        //             "Mali",
        //             "Niger",
        //             "Senegal",
        //             "Togo",
        //         ],
        //         "name_plural" => "CFA Francs",
        //     ],
        //     "LKR" => [
        //         "name" => "Sri Lankan Rupee",
        //         "relatedTerms" => [
        //             "Sri Lanka Rupee",
        //         ],
        //         "name_plural" => "Sri Lankan Rupees",
        //     ],
        //     "UAH" => [
        //         "name" => "Ukrainian Hryvnia",
        //         "relatedTerms" => [
        //             "Ukraine Hryvnia",
        //         ],
        //         "name_plural" => "Ukrainian Hryvni",
        //     ],
        //     "NGN" => [
        //         "name" => "Nigerian Naira",
        //         "relatedTerms" => [
        //             "Nigeria Naira",
        //         ],
        //         "name_plural" => "Nigerian Nairas",
        //     ],
        //     "TND" => [
        //         "name" => "Tunisian Dinar",
        //         "relatedTerms" => [
        //             "Tunisia Dinar",
        //         ],
        //         "name_plural" => "Tunisian Dinars",
        //     ],
        //     "UGX" => [
        //         "name" => "Ugandan Shilling",
        //         "relatedTerms" => [
        //             "Uganda Shilling",
        //         ],
        //         "name_plural" => "Ugandan Shillings",
        //     ],
        //     "RON" => [
        //         "name" => "Romanian Leu",
        //         "relatedTerms" => [
        //             "Romania Leu",
        //         ],
        //         "name_plural" => "Romanian Lei",
        //     ],
        //     "BDT" => [
        //         "name" => "Bangladeshi Taka",
        //         "relatedTerms" => [
        //             "Bangladesh Taka",
        //         ],
        //         "name_plural" => "Bangladeshi Takas",
        //     ],
        //     "PEN" => [
        //         "name" => "Peruvian Sol",
        //         "relatedTerms" => [
        //             "Peru Sol",
        //         ],
        //         "name_plural" => "Peruvian Soles",
        //     ],
        //     "GEL" => [
        //         "name" => "Georgian Lari",
        //         "relatedTerms" => [
        //             "Georgia Lari",
        //         ],
        //         "name_plural" => "Georgian Lari",
        //     ],
        //     "XAF" => [
        //         "name" => "Central African CFA Franc BEAC",
        //         "relatedTerms" => [
        //             "Communauté Financière Africaine (BEAC) CFA Franc BEAC",
        //             "Cameroon",
        //             "Central African Republic",
        //             "Chad",
        //             "Congo/Brazzaville",
        //             "Equatorial Guinea",
        //             "Gabon",
        //         ],
        //         "name_plural" => "Central African Francs",
        //     ],
        //     "FJD" => [
        //         "name" => "Fijian Dollar",
        //         "relatedTerms" => [
        //             "Fiji Dollar",
        //         ],
        //         "name_plural" => "Fijian Dollars",
        //     ],
        //     "VEF" => [
        //         "name" => "Venezuelan Bolívar",
        //         "relatedTerms" => [
        //             "Venezuela Bolívar",
        //         ],
        //         "name_plural" => "Venezuelan Bolívares",
        //     ],
        //     "VES" => [
        //         "name" => "Venezuelan Bolívar",
        //         "relatedTerms" => [
        //             "Venezuela Bolívar",
        //         ],
        //         "name_plural" => "Venezuelan Bolívares",
        //     ],
        //     "BYN" => [
        //         "name" => "Belarusian Ruble",
        //         "relatedTerms" => [
        //             "Belarus Ruble",
        //         ],
        //         "name_plural" => "Belarusian Rubles",
        //     ],
        //     "HRK" => [
        //         "name" => "Croatian Kuna",
        //         "relatedTerms" => [
        //             "Croatia Kuna",
        //         ],
        //         "name_plural" => "Croatian Kunas",
        //     ],
        //     "UZS" => [
        //         "name" => "Uzbekistani Som",
        //         "relatedTerms" => [
        //             "Uzbekistan Som",
        //         ],
        //         "name_plural" => "Uzbekistani Sums",
        //     ],
        //     "BGN" => [
        //         "name" => "Bulgarian Lev",
        //         "relatedTerms" => [
        //             "Bulgaria Lev",
        //         ],
        //         "name_plural" => "Bulgarian Leva",
        //     ],
        //     "DZD" => [
        //         "name" => "Algerian Dinar",
        //         "relatedTerms" => [
        //             "Algeria Dinar",
        //         ],
        //         "name_plural" => "Algerian Dinars",
        //     ],
        //     "IRR" => [
        //         "name" => "Iranian Rial",
        //         "relatedTerms" => [
        //             "Iran Rial",
        //         ],
        //         "name_plural" => "Iranian Rials",
        //     ],
        //     "DOP" => [
        //         "name" => "Dominican Peso",
        //         "relatedTerms" => [
        //             "Dominican Republic Peso",
        //         ],
        //         "name_plural" => "Dominican Pesos",
        //     ],
        //     "ISK" => [
        //         "name" => "Icelandic Krona",
        //         "relatedTerms" => [
        //             "Iceland Krona",
        //         ],
        //         "name_plural" => "Icelandic Kronur",
        //     ],
        //     "XAG" => [
        //         "name" => "Silver Ounce",
        //         "relatedTerms" => [
        //             "Silver",
        //         ],
        //         "name_plural" => "Silver Ounces",
        //     ],
        //     "CRC" => [
        //         "name" => "Costa Rican Colon",
        //         "relatedTerms" => [
        //             "Costa Rica Colon",
        //         ],
        //         "name_plural" => "Costa Rican Colones",
        //     ],
        //     "SYP" => [
        //         "name" => "Syrian Pound",
        //         "relatedTerms" => [
        //             "Syria Pound",
        //         ],
        //         "name_plural" => "Syrian Pounds",
        //     ],
        //     "LYD" => [
        //         "name" => "Libyan Dinar",
        //         "relatedTerms" => [
        //             "Libya Dinar",
        //         ],
        //         "name_plural" => "Libyan Dinars",
        //     ],
        //     "JMD" => [
        //         "name" => "Jamaican Dollar",
        //         "relatedTerms" => [
        //             "Jamaica Dollar",
        //         ],
        //         "name_plural" => "Jamaican Dollars",
        //     ],
        //     "MUR" => [
        //         "name" => "Mauritian Rupee",
        //         "relatedTerms" => [
        //             "Mauritius Rupee",
        //         ],
        //         "name_plural" => "Mauritian Rupees",
        //     ],
        //     "GHS" => [
        //         "name" => "Ghanaian Cedi",
        //         "relatedTerms" => [
        //             "Ghana Cedi",
        //         ],
        //         "name_plural" => "Ghanaian Cedis",
        //     ],
        //     "AOA" => [
        //         "name" => "Angolan Kwanza",
        //         "relatedTerms" => [
        //             "Angola Kwanza",
        //         ],
        //         "name_plural" => "Angolan Kwanzas",
        //     ],
        //     "UYU" => [
        //         "name" => "Uruguayan Peso",
        //         "relatedTerms" => [
        //             "Uruguay Peso",
        //         ],
        //         "name_plural" => "Uruguayan Pesos",
        //     ],
        //     "AFN" => [
        //         "name" => "Afghan Afghani",
        //         "relatedTerms" => [
        //             "Afghanistan Afghani",
        //         ],
        //         "name_plural" => "Afghan Afghanis",
        //     ],
        //     "LBP" => [
        //         "name" => "Lebanese Pound",
        //         "relatedTerms" => [
        //             "Lebanon Pound",
        //         ],
        //         "name_plural" => "Lebanese Pounds",
        //     ],
        //     "XPF" => [
        //         "name" => "CFP Franc",
        //         "relatedTerms" => [
        //             "Comptoirs Français du Pacifique (CFP) Franc",
        //             "French Polynesia",
        //             "New Caledonia",
        //             "Wallis and Futuna Islands",
        //         ],
        //         "name_plural" => "CFP Francs",
        //     ],
        //     "TTD" => [
        //         "name" => "Trinidadian Dollar",
        //         "relatedTerms" => [
        //             "Trinidad and Tobago Dollar",
        //             "Trinidad",
        //             "Tobago",
        //         ],
        //         "name_plural" => "Trinidadian Dollars",
        //     ],
        //     "TZS" => [
        //         "name" => "Tanzanian Shilling",
        //         "relatedTerms" => [
        //             "Tanzania Shilling",
        //         ],
        //         "name_plural" => "Tanzanian Shillings",
        //     ],
        //     "ALL" => [
        //         "name" => "Albanian Lek",
        //         "relatedTerms" => [
        //             "Albania Lek",
        //         ],
        //         "name_plural" => "Albanian Leke",
        //     ],
        //     "XCD" => [
        //         "name" => "East Caribbean Dollar",
        //         "relatedTerms" => [
        //             "East Caribbean Dollar",
        //             "Anguilla",
        //             "Antigua and Barbuda",
        //             "Dominica",
        //             "Grenada",
        //             "The Grenadines and Saint Vincent",
        //             "Montserrat",
        //         ],
        //         "name_plural" => "East Caribbean Dollars",
        //     ],
        //     "GTQ" => [
        //         "name" => "Guatemalan Quetzal",
        //         "relatedTerms" => [
        //             "Guatemala Quetzal",
        //         ],
        //         "name_plural" => "Guatemalan Quetzales",
        //     ],
        //     "NPR" => [
        //         "name" => "Nepalese Rupee",
        //         "relatedTerms" => [
        //             "Nepal Rupee",
        //             "India (unofficially near India-Nepal border)",
        //         ],
        //         "name_plural" => "Nepalese Rupees",
        //     ],
        //     "BOB" => [
        //         "name" => "Bolivian Bolíviano",
        //         "relatedTerms" => [
        //             "Bolivia Bolíviano",
        //         ],
        //         "name_plural" => "Bolivian Bolivianos",
        //     ],
        //     "ZWD" => [
        //         "name" => "Zimbabwean Dollar",
        //         "relatedTerms" => [
        //             "Zimbabwe Dollar",
        //         ],
        //         "name_plural" => "Zimbabwean Dollars",
        //     ],
        //     "BBD" => [
        //         "name" => "Barbadian or Bajan Dollar",
        //         "relatedTerms" => [
        //             "Barbados Dollar",
        //         ],
        //         "name_plural" => "Barbadian or Bajan Dollars",
        //     ],
        //     "CUC" => [
        //         "name" => "Cuban Convertible Peso",
        //         "relatedTerms" => [
        //             "Cuba Convertible Peso",
        //         ],
        //         "name_plural" => "Cuban Convertible Pesos",
        //     ],
        //     "LAK" => [
        //         "name" => "Lao Kip",
        //         "relatedTerms" => [
        //             "Laos Kip",
        //         ],
        //         "name_plural" => "Lao Kips",
        //     ],
        //     "BND" => [
        //         "name" => "Bruneian Dollar",
        //         "relatedTerms" => [
        //             "Brunei Darussalam Dollar",
        //         ],
        //         "name_plural" => "Bruneian Dollars",
        //     ],
        //     "BWP" => [
        //         "name" => "Botswana Pula",
        //         "relatedTerms" => [
        //             "Botswana Pula",
        //         ],
        //         "name_plural" => "Botswana Pule",
        //     ],
        //     "HNL" => [
        //         "name" => "Honduran Lempira",
        //         "relatedTerms" => [
        //             "Honduras Lempira",
        //         ],
        //         "name_plural" => "Honduran Lempiras",
        //     ],
        //     "PYG" => [
        //         "name" => "Paraguayan Guarani",
        //         "relatedTerms" => [
        //             "Paraguay Guarani",
        //         ],
        //         "name_plural" => "Paraguayan Guarani",
        //     ],
        //     "ETB" => [
        //         "name" => "Ethiopian Birr",
        //         "relatedTerms" => [
        //             "Ethiopia Birr",
        //             "Eritrea",
        //         ],
        //         "name_plural" => "Ethiopian Birrs",
        //     ],
        //     "NAD" => [
        //         "name" => "Namibian Dollar",
        //         "relatedTerms" => [
        //             "Namibia Dollar",
        //         ],
        //         "name_plural" => "Namibian Dollars",
        //     ],
        //     "PGK" => [
        //         "name" => "Papua New Guinean Kina",
        //         "relatedTerms" => [
        //             "Papua New Guinea Kina",
        //         ],
        //         "name_plural" => "Papua New Guinean Kina",
        //     ],
        //     "SDG" => [
        //         "name" => "Sudanese Pound",
        //         "relatedTerms" => [
        //             "Sudan Pound",
        //         ],
        //         "name_plural" => "Sudanese Pounds",
        //     ],
        //     "MOP" => [
        //         "name" => "Macau Pataca",
        //         "relatedTerms" => [
        //             "Macau Pataca",
        //         ],
        //         "name_plural" => "Macau Patacas",
        //     ],
        //     "NIO" => [
        //         "name" => "Nicaraguan Cordoba",
        //         "relatedTerms" => [
        //             "Nicaragua Cordoba",
        //         ],
        //         "name_plural" => "Nicaraguan Cordobas",
        //     ],
        //     "BMD" => [
        //         "name" => "Bermudian Dollar",
        //         "relatedTerms" => [
        //             "Bermuda Dollar",
        //         ],
        //         "name_plural" => "Bermudian Dollars",
        //     ],
        //     "KZT" => [
        //         "name" => "Kazakhstani Tenge",
        //         "relatedTerms" => [
        //             "Kazakhstan Tenge",
        //         ],
        //         "name_plural" => "Kazakhstani Tenge",
        //     ],
        //     "PAB" => [
        //         "name" => "Panamanian Balboa",
        //         "relatedTerms" => [
        //             "Panama Balboa",
        //         ],
        //         "name_plural" => "Panamanian Balboa",
        //     ],
        //     "BAM" => [
        //         "name" => "Bosnian Convertible Mark",
        //         "relatedTerms" => [
        //             "Bosnia and Herzegovina Convertible Mark",
        //         ],
        //         "name_plural" => "Bosnian Convertible Marks",
        //     ],
        //     "GYD" => [
        //         "name" => "Guyanese Dollar",
        //         "relatedTerms" => [
        //             "Guyana Dollar",
        //         ],
        //         "name_plural" => "Guyanese Dollars",
        //     ],
        //     "YER" => [
        //         "name" => "Yemeni Rial",
        //         "relatedTerms" => [
        //             "Yemen Rial",
        //         ],
        //         "name_plural" => "Yemeni Rials",
        //     ],
        //     "MGA" => [
        //         "name" => "Malagasy Ariary",
        //         "relatedTerms" => [
        //             "Madagascar Ariary",
        //         ],
        //         "name_plural" => "Malagasy Ariary",
        //     ],
        //     "KYD" => [
        //         "name" => "Caymanian Dollar",
        //         "relatedTerms" => [
        //             "Cayman Islands Dollar",
        //         ],
        //         "name_plural" => "Caymanian Dollars",
        //     ],
        //     "MZN" => [
        //         "name" => "Mozambican Metical",
        //         "relatedTerms" => [
        //             "Mozambique Metical",
        //         ],
        //         "name_plural" => "Mozambican Meticais",
        //     ],
        //     "RSD" => [
        //         "name" => "Serbian Dinar",
        //         "relatedTerms" => [
        //             "Serbia Dinar",
        //         ],
        //         "name_plural" => "Serbian Dinars",
        //     ],
        //     "SCR" => [
        //         "name" => "Seychellois Rupee",
        //         "relatedTerms" => [
        //             "Seychelles Rupee",
        //         ],
        //         "name_plural" => "Seychellois Rupees",
        //     ],
        //     "AMD" => [
        //         "name" => "Armenian Dram",
        //         "relatedTerms" => [
        //             "Armenia Dram",
        //         ],
        //         "name_plural" => "Armenian Drams",
        //     ],
        //     "SBD" => [
        //         "name" => "Solomon Islander Dollar",
        //         "relatedTerms" => [
        //             "Solomon Islands Dollar",
        //         ],
        //         "name_plural" => "Solomon Islander Dollars",
        //     ],
        //     "AZN" => [
        //         "name" => "Azerbaijan Manat",
        //         "relatedTerms" => [
        //             "Azerbaijan Manat",
        //         ],
        //         "name_plural" => "Azerbaijan Manats",
        //     ],
        //     "SLL" => [
        //         "name" => "Sierra Leonean Leone",
        //         "relatedTerms" => [
        //             "Sierra Leone Leone",
        //         ],
        //         "name_plural" => "Sierra Leonean Leones",
        //     ],
        //     "TOP" => [
        //         "name" => "Tongan Pa'anga",
        //         "relatedTerms" => [
        //             "Tonga Pa'anga",
        //         ],
        //         "name_plural" => "Tongan Pa'anga",
        //     ],
        //     "BZD" => [
        //         "name" => "Belizean Dollar",
        //         "relatedTerms" => [
        //             "Belize Dollar",
        //         ],
        //         "name_plural" => "Belizean Dollars",
        //     ],
        //     "MWK" => [
        //         "name" => "Malawian Kwacha",
        //         "relatedTerms" => [
        //             "Malawi Kwacha",
        //         ],
        //         "name_plural" => "Malawian Kwachas",
        //     ],
        //     "GMD" => [
        //         "name" => "Gambian Dalasi",
        //         "relatedTerms" => [
        //             "Gambia Dalasi",
        //         ],
        //         "name_plural" => "Gambian Dalasis",
        //     ],
        //     "BIF" => [
        //         "name" => "Burundian Franc",
        //         "relatedTerms" => [
        //             "Burundi Franc",
        //         ],
        //         "name_plural" => "Burundian Francs",
        //     ],
        //     "SOS" => [
        //         "name" => "Somali Shilling",
        //         "relatedTerms" => [
        //             "Somalia Shilling",
        //         ],
        //         "name_plural" => "Somali Shillings",
        //     ],
        //     "HTG" => [
        //         "name" => "Haitian Gourde",
        //         "relatedTerms" => [
        //             "Haiti Gourde",
        //         ],
        //         "name_plural" => "Haitian Gourdes",
        //     ],
        //     "GNF" => [
        //         "name" => "Guinean Franc",
        //         "relatedTerms" => [
        //             "Guinea Franc",
        //         ],
        //         "name_plural" => "Guinean Francs",
        //     ],
        //     "MVR" => [
        //         "name" => "Maldivian Rufiyaa",
        //         "relatedTerms" => [
        //             "Maldives (Maldive Islands) Rufiyaa",
        //         ],
        //         "name_plural" => "Maldivian Rufiyaa",
        //     ],
        //     "MNT" => [
        //         "name" => "Mongolian Tughrik",
        //         "relatedTerms" => [
        //             "Mongolia Tughrik",
        //         ],
        //         "name_plural" => "Mongolian Tugriks",
        //     ],
        //     "CDF" => [
        //         "name" => "Congolese Franc",
        //         "relatedTerms" => [
        //             "Congo/Kinshasa Franc",
        //         ],
        //         "name_plural" => "Congolese Francs",
        //     ],
        //     "STN" => [
        //         "name" => "Sao Tomean Dobra",
        //         "relatedTerms" => [
        //             "São Tomé and Príncipe Dobra",
        //         ],
        //         "name_plural" => "Sao Tomean Dobras",
        //     ],
        //     "TJS" => [
        //         "name" => "Tajikistani Somoni",
        //         "relatedTerms" => [
        //             "Tajikistan Somoni",
        //         ],
        //         "name_plural" => "Tajikistani Somoni",
        //     ],
        //     "KPW" => [
        //         "name" => "North Korean Won",
        //         "relatedTerms" => [
        //             "Korea (North) Won",
        //         ],
        //         "name_plural" => "North Korean Won",
        //     ],
        //     "MMK" => [
        //         "name" => "Burmese Kyat",
        //         "relatedTerms" => [
        //             "Myanmar (Burma) Kyat",
        //         ],
        //         "name_plural" => "Burmese Kyats",
        //     ],
        //     "LSL" => [
        //         "name" => "Basotho Loti",
        //         "relatedTerms" => [
        //             "Lesotho Loti",
        //         ],
        //         "name_plural" => "Basotho Maloti",
        //     ],
        //     "LRD" => [
        //         "name" => "Liberian Dollar",
        //         "relatedTerms" => [
        //             "Liberia Dollar",
        //         ],
        //         "name_plural" => "Liberian Dollars",
        //     ],
        //     "KGS" => [
        //         "name" => "Kyrgyzstani Som",
        //         "relatedTerms" => [
        //             "Kyrgyzstan Som",
        //         ],
        //         "name_plural" => "Kyrgyzstani Soms",
        //     ],
        //     "GIP" => [
        //         "name" => "Gibraltar Pound",
        //         "relatedTerms" => [
        //             "Gibraltar Pound",
        //         ],
        //         "name_plural" => "Gibraltar Pounds",
        //     ],
        //     "XPT" => [
        //         "name" => "Platinum Ounce",
        //         "relatedTerms" => [
        //             "Platinum",
        //         ],
        //         "name_plural" => "Platinum Ounces",
        //     ],
        //     "MDL" => [
        //         "name" => "Moldovan Leu",
        //         "relatedTerms" => [
        //             "Moldova Leu",
        //         ],
        //         "name_plural" => "Moldovan Lei",
        //     ],
        //     "CUP" => [
        //         "name" => "Cuban Peso",
        //         "relatedTerms" => [
        //             "Cuba Peso",
        //         ],
        //         "name_plural" => "Cuban Pesos",
        //     ],
        //     "KHR" => [
        //         "name" => "Cambodian Riel",
        //         "relatedTerms" => [
        //             "Cambodia Riel",
        //         ],
        //         "name_plural" => "Cambodian Riels",
        //     ],
        //     "MKD" => [
        //         "name" => "Macedonian Denar",
        //         "relatedTerms" => [
        //             "Macedonia Denar",
        //         ],
        //         "name_plural" => "Macedonian Denars",
        //     ],
        //     "VUV" => [
        //         "name" => "Ni-Vanuatu Vatu",
        //         "relatedTerms" => [
        //             "Vanuatu Vatu",
        //         ],
        //         "name_plural" => "Ni-Vanuatu Vatu",
        //     ],
        //     "MRU" => [
        //         "name" => "Mauritanian Ouguiya",
        //         "relatedTerms" => [
        //             "Mauritania Ouguiya",
        //         ],
        //         "name_plural" => "Mauritanian Ouguiyas",
        //     ],
        //     "ANG" => [
        //         "name" => "Dutch Guilder",
        //         "relatedTerms" => [
        //             "Netherlands Antilles Guilder",
        //             "Bonaire",
        //             "Curaçao",
        //             "Saba",
        //             "Sint Eustatius",
        //             "Sint Maarten",
        //         ],
        //         "name_plural" => "Dutch Guilders (also called Florins)",
        //     ],
        //     "SZL" => [
        //         "name" => "Swazi Lilangeni",
        //         "relatedTerms" => [
        //             "eSwatini Lilangeni",
        //         ],
        //         "name_plural" => "Swazi Emalangeni",
        //     ],
        //     "CVE" => [
        //         "name" => "Cape Verdean Escudo",
        //         "relatedTerms" => [
        //             "Cape Verde Escudo",
        //         ],
        //         "name_plural" => "Cape Verdean Escudos",
        //     ],
        //     "SRD" => [
        //         "name" => "Surinamese Dollar",
        //         "relatedTerms" => [
        //             "Suriname Dollar",
        //         ],
        //         "name_plural" => "Surinamese Dollars",
        //     ],
        //     "XPD" => [
        //         "name" => "Palladium Ounce",
        //         "relatedTerms" => [
        //             "Palladium",
        //         ],
        //         "name_plural" => "Palladium Ounces",
        //     ],
        //     "SVC" => [
        //         "name" => "Salvadoran Colon",
        //         "relatedTerms" => [
        //             "El Salvador Colon",
        //         ],
        //         "name_plural" => "Salvadoran Colones",
        //     ],
        //     "BSD" => [
        //         "name" => "Bahamian Dollar",
        //         "relatedTerms" => [
        //             "Bahamas Dollar",
        //         ],
        //         "name_plural" => "Bahamian Dollars",
        //     ],
        //     "XDR" => [
        //         "name" => "IMF Special Drawing Rights",
        //         "relatedTerms" => [
        //             "International Monetary Fund (IMF) Special Drawing Rights",
        //         ],
        //         "name_plural" => "IMF Special Drawing Rights",
        //     ],
        //     "RWF" => [
        //         "name" => "Rwandan Franc",
        //         "relatedTerms" => [
        //             "Rwanda Franc",
        //         ],
        //         "name_plural" => "Rwandan Francs",
        //     ],
        //     "AWG" => [
        //         "name" => "Aruban or Dutch Guilder",
        //         "relatedTerms" => [
        //             "Aruba Guilder",
        //         ],
        //         "name_plural" => "Aruban or Dutch Guilders (also called Florins)",
        //     ],
        //     "DJF" => [
        //         "name" => "Djiboutian Franc",
        //         "relatedTerms" => [
        //             "Djibouti Franc",
        //         ],
        //         "name_plural" => "Djiboutian Francs",
        //     ],
        //     "BTN" => [
        //         "name" => "Bhutanese Ngultrum",
        //         "relatedTerms" => [
        //             "Bhutan Ngultrum",
        //         ],
        //         "name_plural" => "Bhutanese Ngultrums",
        //     ],
        //     "KMF" => [
        //         "name" => "Comorian Franc",
        //         "relatedTerms" => [
        //             "Comorian Franc",
        //         ],
        //         "name_plural" => "Comorian Francs",
        //     ],
        //     "WST" => [
        //         "name" => "Samoan Tala",
        //         "relatedTerms" => [
        //             "Samoa Tala",
        //         ],
        //         "name_plural" => "Samoan Tala",
        //     ],
        //     "SPL" => [
        //         "name" => "Seborgan Luigino",
        //         "relatedTerms" => [
        //             "Seborga Luigino",
        //         ],
        //         "name_plural" => "Seborgan Luigini",
        //     ],
        //     "ERN" => [
        //         "name" => "Eritrean Nakfa",
        //         "relatedTerms" => [
        //             "Eritrea Nakfa",
        //         ],
        //         "name_plural" => "Eritrean Nakfas",
        //     ],
        //     "FKP" => [
        //         "name" => "Falkland Island Pound",
        //         "relatedTerms" => [
        //             "Falkland Islands (Malvinas) Pound",
        //         ],
        //         "name_plural" => "Falkland Island Pounds",
        //     ],
        //     "SHP" => [
        //         "name" => "Saint Helenian Pound",
        //         "relatedTerms" => [
        //             "Saint Helena Pound",
        //         ],
        //         "name_plural" => "Saint Helenian Pounds",
        //     ],
        //     "JEP" => [
        //         "name" => "Jersey Pound",
        //         "relatedTerms" => [
        //             "Jersey Pound",
        //         ],
        //         "name_plural" => "Jersey Pounds",
        //     ],
        //     "TMT" => [
        //         "name" => "Turkmenistani Manat",
        //         "relatedTerms" => [
        //             "Turkmenistan Manat",
        //         ],
        //         "name_plural" => "Turkmenistani Manats",
        //     ],
        //     "TVD" => [
        //         "name" => "Tuvaluan Dollar",
        //         "relatedTerms" => [
        //             "Tuvalu Dollar",
        //         ],
        //         "name_plural" => "Tuvaluan Dollars",
        //     ],
        //     "IMP" => [
        //         "name" => "Isle of Man Pound",
        //         "relatedTerms" => [
        //             "Isle of Man Pound",
        //         ],
        //         "name_plural" => "Isle of Man Pounds",
        //     ],
        //     "GGP" => [
        //         "name" => "Guernsey Pound",
        //         "relatedTerms" => [
        //             "Guernsey Pound",
        //         ],
        //         "name_plural" => "Guernsey Pounds",
        //     ],
        //     "ZMW" => [
        //         "name" => "Zambian Kwacha",
        //         "relatedTerms" => [
        //             "Zambia Kwacha",
        //         ],
        //         "name_plural" => "Zambian Kwacha",
        //     ],
        //     "XBT" => [
        //         "name" => "Bitcoin",
        //         "relatedTerms" => [
        //             "BTC",
        //         ],
        //         "name_plural" => "Bitcoins",
        //     ],
        //     "FRF" => [
        //         "name" => "French Franc",
        //         "relatedTerms" => [
        //             "France Franc",
        //         ],
        //         "name_plural" => "French Francs",
        //     ],
        //     "VEB" => [
        //         "name" => "Venezuelan Bolívar",
        //         "relatedTerms" => [
        //             "Venezuela Bolívar",
        //         ],
        //         "name_plural" => "Venezuelan Bolívares",
        //     ],
        //     "ZMK" => [
        //         "name" => "Zambian Kwacha",
        //         "relatedTerms" => [
        //             "Zambia Kwacha",
        //         ],
        //         "name_plural" => "Zambian Kwacha",
        //     ],
        //     "TRL" => [
        //         "name" => "Turkish Lira",
        //         "relatedTerms" => [
        //             "Turkey Lira",
        //         ],
        //         "name_plural" => "Turkish Lira",
        //     ],
        //     "LVL" => [
        //         "name" => "Latvian Lat",
        //         "relatedTerms" => [
        //             "Latvia Lat",
        //         ],
        //         "name_plural" => "Latvian Lati",
        //     ],
        //     "LTL" => [
        //         "name" => "Lithuanian Litas",
        //         "relatedTerms" => [
        //             "Lithuania Litas",
        //         ],
        //         "name_plural" => "Lithuanian Litai",
        //     ],
        //     "EEK" => [
        //         "name" => "Estonian Kroon",
        //         "relatedTerms" => [
        //             "Estonia Kroon",
        //         ],
        //         "name_plural" => "Estonian Krooni",
        //     ],
        //     "DEM" => [
        //         "name" => "German Deutsche Mark",
        //         "relatedTerms" => [
        //             "Germany Deutsche Mark",
        //         ],
        //         "name_plural" => "German Deutsche Marks",
        //     ],
        //     "ROL" => [
        //         "name" => "Romanian Leu",
        //         "relatedTerms" => [
        //             "Romania Leu",
        //         ],
        //         "name_plural" => "Romanian Lei",
        //     ],
        //     "STD" => [
        //         "name" => "Sao Tomean Dobra",
        //         "relatedTerms" => [
        //             "São Tomé and Príncipe Dobra",
        //         ],
        //         "name_plural" => "Sao Tomean Dobras",
        //     ],
        //     "TMM" => [
        //         "name" => "Turkmenistani Manat",
        //         "relatedTerms" => [
        //             "Turkmenistan Manat",
        //         ],
        //         "name_plural" => "Turkmenistani Manats",
        //     ],
        //     "ITL" => [
        //         "name" => "Italian Lira",
        //         "relatedTerms" => [
        //             "Italy Lira",
        //         ],
        //         "name_plural" => "Italian Lire",
        //     ],
        //     "ESP" => [
        //         "name" => "Spanish Peseta",
        //         "relatedTerms" => [
        //             "Spain Peseta",
        //         ],
        //         "name_plural" => "Spanish Pesetas",
        //     ],
        //     "MRO" => [
        //         "name" => "Mauritanian Ouguiya",
        //         "relatedTerms" => [
        //             "Mauritania Ouguiya",
        //         ],
        //         "name_plural" => "Mauritanian Ouguiyas",
        //     ],
        //     "BEF" => [
        //         "name" => "Belgian Franc",
        //         "relatedTerms" => [
        //             "Belgium Franc",
        //         ],
        //         "name_plural" => "Belgian Francs",
        //     ],
        //     "NLG" => [
        //         "name" => "Dutch Guilder",
        //         "relatedTerms" => [
        //             "Netherlands Guilder",
        //         ],
        //         "name_plural" => "Dutch Guilders",
        //     ],
        //     "IEP" => [
        //         "name" => "Irish Pound",
        //         "relatedTerms" => [
        //             "Ireland Pound",
        //         ],
        //         "name_plural" => "Irish Pounds",
        //     ],
        //     "CYP" => [
        //         "name" => "Cypriot Pound",
        //         "relatedTerms" => [
        //             "Cyprus Pound",
        //         ],
        //         "name_plural" => "Cypriot Pounds",
        //     ],
        //     "SKK" => [
        //         "name" => "Slovak Koruna",
        //         "relatedTerms" => [
        //             "Slovakia Koruna",
        //         ],
        //         "name_plural" => "Slovak Koruny",
        //     ],
        //     "MTL" => [
        //         "name" => "Maltese Lira",
        //         "relatedTerms" => [
        //             "Malta Lira",
        //         ],
        //         "name_plural" => "Maltese Liri",
        //     ],
        //     "GRD" => [
        //         "name" => "Greek Drachma",
        //         "relatedTerms" => [
        //             "Greece Drachma",
        //         ],
        //         "name_plural" => "Greek Drachmae",
        //     ],
        //     "PTE" => [
        //         "name" => "Portuguese Escudo",
        //         "relatedTerms" => [
        //             "Portugal Escudo",
        //         ],
        //         "name_plural" => "Portuguese Escudos",
        //     ],
        //     "ATS" => [
        //         "name" => "Austrian Schilling",
        //         "relatedTerms" => [
        //             "Austria Schilling",
        //         ],
        //         "name_plural" => "Austrian Schillings",
        //     ],
        //     "FIM" => [
        //         "name" => "Finnish Markka",
        //         "relatedTerms" => [
        //             "Finland Markka",
        //         ],
        //         "name_plural" => "Finnish Markkaa",
        //     ],
        //     "SDD" => [
        //         "name" => "Sudanese Dinar",
        //         "relatedTerms" => [
        //             "Sudan Dinar",
        //         ],
        //         "name_plural" => "Sudanese Dinars",
        //     ],
        //     "LUF" => [
        //         "name" => "Luxembourg Franc",
        //         "relatedTerms" => [
        //             "Luxembourg Franc",
        //         ],
        //         "name_plural" => "Luxembourg Francs",
        //     ],
        //     "SRG" => [
        //         "name" => "Surinamese Guilder",
        //         "relatedTerms" => [
        //             "Suriname Guilder",
        //         ],
        //         "name_plural" => "Surinamese Guilders",
        //     ],
        //     "SIT" => [
        //         "name" => "Slovenian Tolar",
        //         "relatedTerms" => [
        //             "Slovenia Tolar",
        //         ],
        //         "name_plural" => "Slovenian Tolars",
        //     ],
        //     "GHC" => [
        //         "name" => "Ghanaian Cedi",
        //         "relatedTerms" => [
        //             "Ghana Cedi",
        //         ],
        //         "name_plural" => "Ghanaian Cedis",
        //     ],
        //     "MZM" => [
        //         "name" => "Mozambican Metical",
        //         "relatedTerms" => [
        //             "Mozambique Metical",
        //         ],
        //         "name_plural" => "Mozambican Meticais",
        //     ],
        //     "MGF" => [
        //         "name" => "Malagasy Franc",
        //         "relatedTerms" => [
        //             "Madagascar Franc",
        //         ],
        //         "name_plural" => "Malagasy Francs",
        //     ],
        //     "AZM" => [
        //         "name" => "Azerbaijani Manat",
        //         "relatedTerms" => [
        //             "Azerbaijan Manat",
        //         ],
        //         "name_plural" => "Azerbaijani Manats",
        //     ],
        //     "VAL" => [
        //         "name" => "Vatican City Lira",
        //         "relatedTerms" => [
        //             "Vatican City Lira",
        //         ],
        //         "name_plural" => "Vatican City Lire",
        //     ],
        //     "BYR" => [
        //         "name" => "Belarusian Ruble",
        //         "relatedTerms" => [
        //             "Belarus Ruble",
        //         ],
        //         "name_plural" => "Belarusian Rubles",
        //     ],
        // ];

        // foreach($xes as $key => $xe){

        //    $record = new AllCurrency;
        //    $record->name = $xe['name'];
        //    $record->code = $key;
        //    $record->save();
        // }

        // dd($xes);

        // $currencyDatas = [
        //     "USD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "EUR" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "GBP" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "INR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "AUD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "CAD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "SGD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "CHF" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "MYR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "JPY" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "CNY" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "NZD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "THB" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "HUF" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "AED" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "HKD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "MXN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ZAR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "PHP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "SEK" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "IDR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "SAR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BRL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TRY" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "KES" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "KRW" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "EGP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "IQD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "NOK" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "KWD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "RUB" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "DKK" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "PKR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ILS" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "PLN" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "QAR" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "XAU" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "OMR" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "COP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "CLP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "TWD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ARS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "CZK" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "VND" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "MAD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "JOD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "BHD" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "XOF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LKR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "UAH" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "NGN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TND" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "UGX" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "RON" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "BDT" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "PEN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "GEL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XAF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "FJD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "VEF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "VES" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BYN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "HRK" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "UZS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BGN" => [
        //         "isObsolete" => false,
        //         "isSellable" => true,
        //         "isBuyable" => true,
        //     ],
        //     "DZD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "IRR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "DOP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ISK" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XAG" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "CRC" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "SYP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LYD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "JMD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "MUR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "GHS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "AOA" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "UYU" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "AFN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LBP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XPF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TTD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "TZS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ALL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XCD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "GTQ" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "NPR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "BOB" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ZWD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BBD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "CUC" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LAK" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BND" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BWP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "HNL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "PYG" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "ETB" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "NAD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "PGK" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SDG" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MOP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "NIO" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BMD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "KZT" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "PAB" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BAM" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "GYD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "YER" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MGA" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "KYD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MZN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "RSD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "SCR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "AMD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SBD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "AZN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SLL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "TOP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "BZD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MWK" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "GMD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "BIF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SOS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "HTG" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "GNF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "MVR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MNT" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "CDF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "STN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TJS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "KPW" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MMK" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LSL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LRD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "KGS" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "GIP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XPT" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MDL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "CUP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "KHR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MKD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "VUV" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MRU" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ANG" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SZL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "CVE" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SRD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XPD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SVC" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BSD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XDR" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "RWF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "AWG" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "DJF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BTN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "KMF" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "WST" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => true,
        //     ],
        //     "SPL" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ERN" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "FKP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SHP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "JEP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TMT" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TVD" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "IMP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "GGP" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ZMW" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "XBT" => [
        //         "isObsolete" => false,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "FRF" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "VEB" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ZMK" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TRL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LVL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LTL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "EEK" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "DEM" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ROL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "STD" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "TMM" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ITL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ESP" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MRO" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BEF" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "NLG" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "IEP" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "CYP" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SKK" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MTL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "GRD" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "PTE" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "ATS" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "FIM" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SDD" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "LUF" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SRG" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "SIT" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "GHC" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MZM" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "MGF" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "AZM" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "VAL" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        //     "BYR" => [
        //         "isObsolete" => true,
        //         "isSellable" => false,
        //         "isBuyable" => false,
        //     ],
        // ];

        // $i = 1 ;

        // foreach($currencyDatas as $key => $currencyData){

        //     AllCurrency::where('id',$i)->update(['isObsolete' =>  ($currencyData['isObsolete'] == true ? 'true' : 'false' ) ]);

        //     $i++;
        // }

        $jayParsedArys = [
            [
                "id" => 1,
                "name" => "Afghanistan",
                "isoAlpha2" => "AF",
                "isoAlpha3" => "AFG",
                "isoNumeric" => 4,
                "currency" => [
                    "code" => "AFN",
                    "name" => "Afghani",
                    "symbol" => "؋",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3REQwQzQwNjE3NTMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3REQwQzQwNzE3NTMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjdERDBDNDA0MTc1MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjdERDBDNDA1MTc1MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+duaIkQAAAalJREFUeNpiZMAN2BkYVjMwyDIwfMCQ4mNgeM3K4DEFj24GJgaagaFpNAuR6v4yMPwDO4SRaOcQZfQfBgZOCQkOSclfb978/fbt59u3/6li9G+guTIy/EZGTOycbDJyrIIC3+7e/XX0CMgXZIc1I9hcPnstsbzA94ePfH/x+N//n683b+ILNBGw0v3zlwKjIYErGub94dhpJi5u3Q1rtFcuZeYTerNtj3CwJxMjBQECdBaXoRybhCzLFy6eQJuHPW1/P30W9Db9fuMlh6QSt706A8NNMl0NNJpNVIrHyIyDR/DV4UOMivxf3tx/vnM7p4gor5kFh7gs+QEC9NG38ze+3jz7m+Mrv6i2oKGtTGo6v7jOr3/vP1878fXcNfIDBCj39fWHdyt2K/Q3vpy05FFWCwMTE6+NrnhR3PvyCZ9vP6Mo8bEyMLxZsOkTD4tkaga/vvXvt285rdSeT5n1dckaFkrS9X+w9F+Gf8+nrPl9+Q2LkMD/f//+rd/4es8ePoZ/LKyUZZn/YIczMzC8PHgALsgGFvxPeW78D8477BiCw7RQpaHRAAEGAEWfj+c8VB1nAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 2,
                "name" => "Albania",
                "isoAlpha2" => "AL",
                "isoAlpha3" => "ALB",
                "isoNumeric" => 8,
                "currency" => [
                    "code" => "ALL",
                    "name" => "Lek",
                    "symbol" => "Lek",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyRDFBRDI0NTE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyRDFBRDI0NjE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjdERDBDNDA4MTc1MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJEMUFEMjQ0MTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+GPq/agAAAiRJREFUeNrEVb9rFEEUfm9m9nb3bhNz50UMClopRAsFrUURW1tBrSzsLPwfbPwDbGz8F8QiIkLAKiCkUIKiGBEFwXAhd7fZH7Mz83zZtbC4TdyF4LDF8N7ON9/73jczuN4/A4czBBzaqIUmAA+Q0wjQRzkUCsv4USEHKKs4/0DtWOdAgxLxrUk+mqyHIkLx2eg1k1gA3kwDtYFeFOqVnj5NRwXQip7eGG9+svlPV1wff3mejwuiZ9n2i3zCRWANAta1kaFX9OS1jkdkHdGyCt6blMmel8E3p1OgY6iueL2b/pEtZ5qx5kRCLIhMyK4WMQFt2HzdpEzypZ5OnOVUSoT1gqi6BOvA7ZoDUan5JB3BXxPeOALBahigxloLQO4SFy5hBjMOpuA0zc4ebL4OYExuZl0dxNiRh63MZ4jYXjzJiG77/cuqW8UvqvBO0Ge+jjsplKHmgrCIIeICyke9pXPKZ+kvqPCS1+X6T4vO42iJN/YB22jNIo6cYWN9dfqdya560TxKruKaF32w2abVW2VWtNCa6fRQnpTeD1vcD4anZOdNEa8VCZN9EA6/2+KE9Ob3dUit+XbJHRfqXjBgTZjYhk3nUDAQN/CsDJbDYIfcbvlhU+hqQUpuSo6tcstfYMp8q9z1+7+cyfZMuUe4zZGp/GfLxRm4bbIPu4scYbIJOO6EO+hSVf9y8zLQmGxUKrNDRu7HtSH0n+NHrpr8/1fmtwADAEjB+xzEjgF0AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 3,
                "name" => "Algeria",
                "isoAlpha2" => "DZ",
                "isoAlpha3" => "DZA",
                "isoNumeric" => 12,
                "currency" => [
                    "code" => "DZD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2hpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDowNDgwMTE3NDA3MjA2ODExODIyQUY5NTY0OTkxRjRDNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyRDFBRDI0QTE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyRDFBRDI0OTE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDQ4MDExNzQwNzIwNjgxMTgyMkFGOTU2NDk5MUY0QzYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDQ4MDExNzQwNzIwNjgxMTgyMkFGOTU2NDk5MUY0QzYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5FFcD/AAACLUlEQVR42sSWQWsTURDHf283dTdZQ9qCVWhQD2JrRCmEaqUoarEQi3c/QEDx2ByqtwgSUTx5EvINPHlQRLRYsViqHgQRVGopRpo2kKbppk26SbO+3UDBk5uY2mEXdh7s++1/5s3MClJDNi1a/daMLaDU5GtBeS/6+AcT7uVu1KwFFXbJmldstwfsXXFdEoWM7P4AGJt0qTqlpcUdBjtQTYUeP6c+LjOd+srKzSnq/RHMWIzNdBpqtR0Aq1Jpt07i8Ryzj74wmCtz72yY4qunBO/edyNRnZxsc46dnEqlo++yPHjx0106Ej9E5kqUGwNRuYMfbeAE9VwO2zQRwWCbFDtqq3Wuzyy77p0LvWTO9WLMm9ReT8Hnb2wkkwi/3zPUm2KZWy1fIbqw5rpv+jply7DoCPdRz+exRy+z4SgwDPREQj4o7cuxsBudYruabGdBQfh829Vll8uN9bYdLmuLSrfGp4ONMA7/kMqDGrVfGQgYKNPvCUxMoI+Py7SobQTXpApdJX26x3Vvv8ywbzZL6bBBx6UR+SWDBFIpqFSw19c9g1VGwsm/NWTKW3w/1gVmlfMLJtc+rGKvWpwcjhFas7CeP8MuFFAjEa9cS3iaTk4D8ctzGNrDxbdZHj6Z53jRohjqRJwZQhsbQ4vHZWR0r2BTeB6LDtynuDVNYYUDpb3MXU1jHO1vpWOa3oeEIhrwrFM8Oku+WqvQFqeT+N/Tadfn8Z9tvNVfH/O3AAMACcCyb9iaq58AAAAASUVORK5CYII=",
            ],
            [
                "id" => 4,
                "name" => "American Samoa",
                "isoAlpha2" => "AS",
                "isoAlpha3" => "ASM",
                "isoNumeric" => 16,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyRDFBRDI0RDE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyRDFBRDI0RTE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJEMUFEMjRCMTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJEMUFEMjRDMTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+O7eQxAAAAhJJREFUeNrslD9oE1Ecx7/v7l2TXpLGWNrGioJDO4ogLkJBhA6iFDpUOhc6ODiKBUWcBJ1bKLaCIIJOjl0ypDh1rFDQyUH6R1HitaRpcvfu+X0XHWp6TWkCGdof3D3evR/3+f35vp/AtafrADI4yLQGlEZ+oAdCCGxtedC2ADeNvnYXUv4uCl8LOM/1N/eHmeQz2PDV/FcZaAjx3cP7V1PIppO4OvoCyk0AFh3SXEON45oB7+zL+F82P7frSfem8eRlEUnHRki4m3MhCN4t16CNrzg+eL8FCsKr4NHMbfhBiOezBSwvfWIEiCrw+MEtjFy5iJsTc/BTLKcj2wSmWQRcHsqjsudDVJhZf0/9YL2Etwzi4+o3BC6h0m6p1P+JxILKZ3H3/puo2WIwBxAuux3cm7mD+XcrWCt+Bi71RRVoHxj1kiLhRCLSvgJKZQRlibUvm3CZqXcu2xI0HmyMYgKh3Wzu5PQNmFu0uLBMNSdbVnQdbDvxpzWNih/i2cMxnKXb6w+rCKoBO8B47cZ7XFZ+pMGjCF06QTX+lPqxQoXxqXkkuiSkqkEkLSq/euCwSalqBD1KE0Sx9/p27OTiPdXs88YPD4q5XOg7A2GmmW4sszZC5DvH4Cyuuknecqi20zS64czfcbbnxTuZeOhSshPM2IqCOBT8i47ttmbQaFagQ3YKPhngTCfAZlZvdAL+R4ABAI7lqpHANxKvAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 5,
                "name" => "Andorra",
                "isoAlpha2" => "AD",
                "isoAlpha3" => "AND",
                "isoNumeric" => 20,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEQUI4NkFBODE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEQUI4NkFBOTE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkRBQjg2QUE2MTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkRBQjg2QUE3MTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+lPUISQAAAfFJREFUeNrUlFtrE0EUx8/ZS7KXpJLNxiQ0biSt1hTFO4qCvgl+AF98EV/9Nr4UP4ePUuijFLzUait5iDZVm2a3NE22JNntzhx3i0WkWmWCoH+YeZlzfjNz/mcGr8McHBIDyYOJj/wRYI+1CocDZCeM5xX9spxjqBD8TBL8Nf0DaMTv8x9K+S1TTo+IVKBAUndpL4OYwtgLwPHRfLCTlTN9d+vCdrueKzaK9hIfGnp5F0gRRB+4Li0uPaRek5ixrdy2e2ZztYo4eWvqiSTHQSiCxm9X1qPGRu51JX+zf7z9WPmiD9VS/64/DLOm7h9dlV/aKEPSuZute4FvTd8AZ/Zces22vKbdXQ0j/V3jQXIuJRArSLIrDlysnfQ6nzpPYXRFxY4FWS2KdF1eS3zgMgqg+f5SsfwK9qZT7gK2AvVOhswoU+lq2C3lXyQxTJEhEuhrHg9/p45mBUxL0izkplEixgqcKpvupSRZ4mM0X0SD0Fmu3T92tmtonBmSu573PXvy9GJSNAIUQidZ2ann52dSAamfvWtbG/U0X6memNero9rM8oEfXPTUGJy6+CxOn3i/3m5fLVdeOrNvgCOkNKD0uA+d+QVEyTnzwam/JW7wXomAy7nwh4clhN4HEGcDE9EkgiMqIP6pEv13/7WAvgowAA7uuJg3MwVHAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 6,
                "name" => "Angola",
                "isoAlpha2" => "AO",
                "isoAlpha3" => "AGO",
                "isoNumeric" => 24,
                "currency" => [
                    "code" => "AOA",
                    "name" => "Kwanza",
                    "symbol" => "Kz",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEQUI4NkFBQzE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEQUI4NkFBRDE3NkYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkRBQjg2QUFBMTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkRBQjg2QUFCMTc2RjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+HdsdLgAAAZ1JREFUeNpiPCeoxkAbwMRAMzCYjP7/i+H/XwYGRqoazcjM8Pcb45+vTP++Mf7/Tdh0Yo1mZGL4/oZZ0O+X2dfXonE/vn9mIeh2JiLd+/sdEzPb/x+3mF/0csn3fVWf+fHHR5b/PxnxmM5CjNF/PjKyS/2V7f365w3jrRz+d5vYNHZ+lDrx9c1CDiZmkMU4jebHJQnxFyPD99+Mf34wCNr+Y+D9/3Pa3zdneL7M/CNe8v3rfG4uYMQy/sfU9fH/X5DRr/79wWP0v98MfAoMXwX+bjRjV5Jl/v7230+GH9fXMpl5MrMY/H54gZFDAIvR7IzgkAK6Gg8CultFmuXjFXEfA6ATGXhBIoxOeuxPzgurKwNdxoRDF9Fg6Ry+//8lzMyhehrruB9cFaVa7koK4Xh1UjQ2gFOIj/HJRbHidG4qGLptjsCqSfxARrAH+6tTok+PiGydLUA4yRJjdFESV1Mhz49/DH9+/582/9upy79drNjuPPw7Y/l36gSIoRaLkTY0H3CwM66dyt+Yx02Tgk1chCnAlZ1hFBANAAIMABE9jLBld63JAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 7,
                "name" => "Anguilla",
                "isoAlpha2" => "AI",
                "isoAlpha3" => "AIA",
                "isoNumeric" => 660,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzYwMTE0QkMxNzcwMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REFCODZBQjAxNzZGMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iOEI3QjgwQ0VBMDNEMDkyMkUxQTdFMDM0NUEwRTZDQzEiIHN0UmVmOmRvY3VtZW50SUQ9IjhCN0I4MENFQTAzRDA5MjJFMUE3RTAzNDVBMEU2Q0MxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/LHIVAAAA2lJREFUeNrsVGtoU2cYfr4kzdWk6QWSpqm1c6K0NnVOizhveNkPnXW0tUzdiMIo24QxW+oNJoj+qbIh+kNRweCFgsNg7RgFNwvtVi0FkzXepjPudK1r056Yk6RpknNOzr5zmq0w9kMq7tcezo/z3Z73ed/n/T4yuG9PyZa1Fx6aD3vuMj23AEfbgUUfXPrsh22t609w4KO55nTTgsShXW9yjvLS3Xe4YZZAh5eAanbrYOOWU++x3b6mkuOtbsfSivCEBBGspM9zmj9fyPqbbS1frDjKVTqPj3HDIYIcvBzIhx97BIt1dUHCbWYM9ZtjRRXMZa/orlOduWgsmzM39hibar3+5Ddnv02FIt91PkvzKUBDD8rxIdAfIAFoqUr6KQllstRSlR2CyEYzTDQjcC9sVn0ynhwXYAUMZk280Mk/H7VrksVv2NIqfdG9VWExl4CyS85SXqsWUiioXjPf/xMjJEasedpAgK6QKWqNr3jZeCgSFlJVtcvzJ0Imdigs6jI3OnM3rHEudzFdfRFHSX/uHCb6R8agE1Q5EKlSSGDdH1UfOeJODPUZTc8nSYPWUuL1ft+w9QpQPMWtWXy3wlX3zt6dSxZUTKY7bp4M5rvmFbpudT6paexZt7VuD1fq73rQHvz0fvVAzAS+T5FMCyLwKRNgN/7enh58YFhRCOviKN0grxI5NK3OmeYlvk3xHQOes+82VX3S3XLAn/QH7DboRga3l+9ftPG057Fh21rHz5ujx3Q/ag1JKWtjTlyg57uTBaszDT2pgBejh8ITdN70t40aa2j0q3PX2yLFPnY2xAjAXu0lv45X/XKbg1H9sLd/V6/v65qa3SsXmnW/qcVJaoEiioxx1Dq9/tF58BzsldBPDA/FaUiiSIYi/n3ay0oidCBnKiENFAGjRDZdJ8mOhxU6OikSpSskpErLdMHgl6qBNhhssFXC8pbL1RwIjBDMyqoGbJSOQK0M6Xm1ootD9l5kFE/yFSVpklVDYWCeBeuPdYztbXnxBBYLHJ72QOA+UDbdfASNmBEkWf6I+dr+WO1Kdf9T9YZ9ac5IYMRfBVETvD0zaqV6mryO/nKeSR3siIV4gLZ8ZnrDjFVPKZOQVJywALOIfD+nocErgbqaI8lu4R+8r049beu/vHx4bfif+r+j/lOAAQAGyVcqpoR/+gAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 8,
                "name" => "Antarctica",
                "isoAlpha2" => "AQ",
                "isoAlpha3" => "ATA",
                "isoNumeric" => 10,
                "currency" => [
                    "code" => "",
                    "name" => "",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozNjAxMTRCRjE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozNjAxMTRDMDE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM2MDExNEJEMTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM2MDExNEJFMTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+SFAcWAAAAxRJREFUeNqkVutLU2EY/52zc7a5i1te5r2lUFpiYl+CMsjoQ/QPRGYFUZAQ4WfBT0VEn6JPBSFGQZcPCQVS9EGswBAy7GagYuqWTp24zTnnzqXnfb3g1M3D/I2Xc3nfZ7/n/hzhWFu/H0AxrQgMQKCl6iv3ooBM4KT1T1q9wYZrSpglAdPhBOYWEpBNIhaWVDisJpTkWECP0HXj5JLRk4x0aCrGSW6fK4fNbEKQFOgfjeDZpwA/k59tJkVEUkLYUYktxEyAyZiElesaZiMJePOsaL9ehcrirPX3jcc9uFBfgC9DYXT0BDAxuwSJhMtyrSSvp1QgiZjFLKHp5EoFbrsEC1mprQpOhxK4eqooiXQNdfscqPXa0XSiAAN/F/DgnZ97J0sWIaTIA3HjQ5AIj5Q70XK2FCOBGCaCcVJGgErsCmVUdak9pevYOSfFu77KhbvnK7ilcUVLfX7jg0wu+u2PorHeg+62WrLOhl++KL6PR3F0fzb/UyPw5ltQnm9FlJLPUIxdNoksXULz4yE8v3kQ71sPo717ir8/XeOGVRYNEbPkysuWER5R+XW7OCcRM5d66CCz8ill6kWK2ZWGwoyKleVLLK6thGob5i0mOCwmrkDLk2H8mIgiUzy6dgA3zpTAPxeHvlOMGRQiTSg6Th5yo6bMjt2AxTkcUyAaIWZYXFbTZrBRlOSYqSxlJFTdGHGh24y3X4N42TuzK2IrdTdxUyNKS2wngRC56E7nGOajSsbE97t8vP5ZuzVEzOJc4JIRW9bQ8TGQEennPyEM+haR45B2LqekDfJRrlPGvTfj6OybwSUqLYlGEBsKFR4r6qjDsf3qUtu28rdej2FZ1aj2pfW2a4iYHWajjpGwAdH6YpT3XUau0eYklcnlhiJ0NFdukX34YRI/qRT30lDRdIPTafOkYnBT52JrDSxLbRYRTdRaN6Pr2xwNCR/2UDZLIjIjTteVPC4zXlHW99I4ZF4ZGIvyidQ3HOFxZUtR9bS9OrL69RExSszmLfv1DM4jtKjwDyI2YLLMIrnXwi1NQ8q5/gswAJWJHoEWuWKnAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 9,
                "name" => "Antigua and Barbuda",
                "isoAlpha2" => "AG",
                "isoAlpha3" => "ATG",
                "isoNumeric" => 28,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozNjAxMTRDMzE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozNjAxMTRDNDE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM2MDExNEMxMTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM2MDExNEMyMTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+VJu1tQAAA/pJREFUeNq8ll1oW2UYx3/nnJyk+W7SjqZZ25VORjvQSSdKLV5UL0ScugsvRKs4FCui6C7aiyHaqw0KMnFgy/BKvRG7KQgiDmGC1SGb7cZWW+0Xa5uadWnXNEvzcT58T9qUpk26gOADL+fwfP3f5/k/73uOdNThn/82vRIGVvl/xPuk3Rthpqol3qQ4TKEoazkdUu4Z8Mlm0C8X6MpZtbJqjgWb43KVZGPAU3fPbUrS+vOxwyqhKpnWB2w80qpS5ZPoeFgt8NlN+r11NMgqtjE9xRN2D33uWnruLpQMOHxQJbZsoCnQ1elCy5pIMrS22Lg2oRGulmkIK1y6li2Zo9cd4hm7j2FtDZuMxJ9airede7ikJTmfXtlRqSmadPlGloEPvERjBm1tdh49aMPQYGRGo0Js5mi7g9c+jBfEbJWn7F66Bca4KNRqjPRH4EBcx/SGRPmWb8edCSb1TEFQ2yGV/XUKlQGJMyf96CkTxW4xJqGLyhWXxPH340SjBpFFg58vF8aHRe6LlfvxiBbNG1kUpFXZMogXIkIRkBT6i/D929UsITXFma41sdUoinsRtFugb7ybUU6/keSAP7UD1JJPRc46AT67DprT2fJGVShyfCsV9Pkb6fH5IJXGoWjsrZH4aVHim2kPbX4b/9w2NgfJammNGLbhWY3vFxI0HTKJ3DJJZUX/nQ5643GOKE5G9HQOIy+2rTvL8/1OZo3f208w+NCL6LFxsmJwkm6ZznGd5K+62OW28RXtrggoNLygoK0Z6IvCp6qZp6+ep3uwh79UkVlRC2fH4tg61HmFLkoJJVcwZIWO179kKnw/RKZzhJQnAjTUQO3tKS5+9hKedIKIO4BSOG3rHG8Vy8FyDCaXGPi6G+4KDt0uMBLCmtx9WT7OCsgk6B/spj4+z5ynejvoRneLiGrojAX38fjCFfp+OAWBPaIQwcqaOD8pvfiybFmRLlhD74U+jtwcYjTYKHJpxS+k7a3Oi7geULUMjtVpZk9/jue9l8lOruYujWJi6ibqfT4SZ8/R0PU8Gc8+0vYKpCLVWq0uCbzOt4yyFKWpyoV/+Beob9yd3liU+IPtTM4togfDosVGKc9Vebc8VqBUHeZGbJ7rz3bec6xGn3uF63OTIqZ+N9DSHBe2MIsr2MKdkSGmu94t6Xfz+AmWhn7EGWjOxdxL5HIOiImBy9XIwtlPiH3x1Q778rnvmPv4FE5ng5gas6xDVxawdT3JTqe4bSqZOvYWqdG/N02ZmXkmX31T2Hziznbv/Dr8J2ALWxwxe/VesvoSE53HNvXWeyYREbb6nE+5Ytv45fGW8+uT53tleIjIyY+QXS6Why7gLpPX/K+PhfWvAAMAKo+tOTpRNSkAAAAASUVORK5CYII=",
            ],
            [
                "id" => 10,
                "name" => "Argentina",
                "isoAlpha2" => "AR",
                "isoAlpha3" => "ARG",
                "isoNumeric" => 32,
                "currency" => [
                    "code" => "ARS",
                    "name" => "Peso",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMzA3QTc5QTE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMzA3QTc5QjE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM2MDExNEM1MTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM2MDExNEM2MTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+VveEGAAAASFJREFUeNrklE1OAkEQhV/3DD8ODEQgxBBXwIa4YMUNPIJX4VK4NN6BxM0k7tQYI5FkMgzE4U8ZpttihAN0J9ALqlO9q3zdr+oV6w9evwC4OHHYlA0YCE45NwXGWYFtP9poFbL0oltKSB3wbaeioROHlXEIyOhIiHgFKYTaw6WUkZadxDfwswAuqJSV1BXTAW9mIwTePeLgGZlqB7XuHXK1prKPlSL5XSJ8GyL+fITLX7AYvSMsXKNerMPOF4841ek87borYKXVyb6/4rg/trIOqq0egugDoe8h17hBpd2j37qnGq4psKJSp0y6XaoLN3jy1cGMg5Od/t1MPt6uSe5ETeoHbwLdDcIOYI0NYl+Vs+e1q42CXRPgnY/HJuB/AgwAxP5dJ04ev60AAAAASUVORK5CYII=",
            ],
            [
                "id" => 11,
                "name" => "Armenia",
                "isoAlpha2" => "AM",
                "isoAlpha3" => "ARM",
                "isoNumeric" => 51,
                "currency" => [
                    "code" => "AMD",
                    "name" => "Dram",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMzA3QTc5RTE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMzA3QTc5RjE3NzAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkMzMDdBNzlDMTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkMzMDdBNzlEMTc3MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+WDHK5QAAAF9JREFUeNpi/M/A8IlhAAALEPMOhMVMDAMERi2mX+LawmD8ZyAsZmRg2P9vgLLTO8YBsvjvCEtcA2U30GKx/wOSqpcm2/weEIv/b2b4PzBB/Wy0rB7++Zjh80BYDBBgAAfmD594ReJrAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 12,
                "name" => "Aruba",
                "isoAlpha2" => "AW",
                "isoAlpha3" => "ABW",
                "isoNumeric" => 533,
                "currency" => [
                    "code" => "AWG",
                    "name" => "Guilder",
                    "symbol" => "ƒ",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzMwN0E3QTMxNzcwMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzMwN0E3QTIxNzcwMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQkNEODUwOUZCREQ1NjMyQTZERjhERDI5ODI4NUUwNUQiIHN0UmVmOmRvY3VtZW50SUQ9IkJDRDg1MDlGQkRENTYzMkE2REY4REQyOTgyODVFMDVEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+5hWk3QAAAbxJREFUeNqslM9LVFEUx88597wZZ6wZmRITxBJCUhBahjBtWkgbd23aBEG4EdrpX9D/4M6tuGhhK0FwE+hG3UkUZASRYDTVzJt33497jvclUuuZe7jc5YdzP99zDy6v76lpoIiixAXXjDOICgpDlCIbl7CaptANRHEq92YaFz8SZ3NAHAYtaBCIQAuP/W1dWq28sO+qEl+kvu0MIB/4oL/VEZRs5UZ9iT8tbr9p945Hb9/M0cDQZaYerdydbq1Ge8+31/4cfWvbo/mF8bOJue8/oYICg4lBIi3M/OOXSc7Hyfj51Fw7Odx4+HprdKnXzUCcFz6g8r9o8sKdLb7GIzutJyfP1t5PP/3YrdhUeGDudVE5ZYQTlZS7vzZby7niZBQjoQ7tmq8nESLQ089pjdSE4P6HLl8vdcoAw3D/oa/kI0LAYlUSLbMMCFUfoRKfx3dyvVX+n5BoZu3hwe5is9F3eUgXZLSfRPylc7+WFU4kWHw+NINpinzWeRBZFicA4Rr345sW+GF/dqzZL7KQs2EM9Czzq523Ak1AG7BrH6PRmOsjhZBfq960BEMjsAOuakfKjS0aUklE0L0UYAA5i9TZQeWJxAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 13,
                "name" => "Australia",
                "isoAlpha2" => "AU",
                "isoAlpha3" => "AUS",
                "isoNumeric" => 36,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2hpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDowNTgwMTE3NDA3MjA2ODExODIyQUY5NTY0OTkxRjRDNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozRUY3Nzg2NDE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozRUY3Nzg2MzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDU4MDExNzQwNzIwNjgxMTgyMkFGOTU2NDk5MUY0QzYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDU4MDExNzQwNzIwNjgxMTgyMkFGOTU2NDk5MUY0QzYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4Y+peLAAAD2UlEQVR42uyVW0wcVRjHfzu7s7uwOwgIFLA2VqjKLXKL1fjQ2DaBah9slbSp8qD2ZiSaStTG2AdTTBOrwagvJoZISWp8aDWUCjQ2bYgUGlCC0ECNAi1QspTLwnaX5TI7npkdKJZNQxMTE+OX7J5zvpn9/uf//y5r094tH6Y4V6kZyuZItYex1l4FYafKYnxF377Kzy9+QfFXquHTrTKr17dvfxxaYiHKnkHF758VXol7NcnycVzqm7vOKbtu/qj8/oZXOXq0AGd+HiNBWSGE4rHep9hT0vggs5/RIz72HipQyj3PK9LxBAE6FSFkCIfDSl5eEi6XbJwjArtcboZKyqgez8He08mh5+b4o+EJtmQ7YASeSpf5s0Ll8HvriD74CtWxO7nWNsALzj5kmy6EdkfIABkZ8bS2vkRh4RpxvhUR2KI9yDTzKOJb/MYSIZBp7hgRYzq8T9GvbCXuxod4tXhx8C97URVMneTmJtHdPcbU1IzwLaYjakkB28ym1/CMePHNyzy29XHk8T5xAUHVLxifPAU7tsOmTGjqZCFopzcxh2jvMG67hPqDkHJ+dompCGcE1/Pe3Nwr9tEG4Pr1yQQC83g8PnG2hoETv89RHjlcyvu748lJugIN7VQOZJK7wcEzdadoKS7j0pNFvLX/Fraen+g/PclHvi1cnkgWoA0moExaWiqTk0EmJvxmcD0NC9hsAS5ePEBT0xClpTXCF28AW6s/faf8y+xfHZnt3/HN243srpY4WRfFvoevsm6wjqnkAna8fIWac31EJ6SwJ2OUvbEdPNp9iTPjCuqc4CWI9fe/LiS209j4yxJTCBIKaXR2+jh/fkAwnjVVsWBTrvXx+Sf1fO0vpMu7WaTohngwTFWLhebRzfS1iAAOjYG2bg60XaVy+1YOPr2BVHst0rzO1s3cXIiSkjMMDuo1EGNKP0N+/kNYrVYuXGgXZ5f5CefY4nJ9pvn9etKnjcLQZdNvGt4/gFHahi0WxrTJJtkIHn5PL0q9texmcNUouG3bspFlidra30z/bbOFF7Najdws4BYVHAqppKdbuX49BlXE8flmTMBFRsvbRFvmV801hvr6HnOvrOjnCCNHxWIJUFVVxNmzOzlx4tllCqzGLCbIlKleVMQhIkWaPLGxkhgC97N2rWKsiYm2ewBWcTplCgrSjH7WFYw4uVa6ZLzeIMeOXTZaoKKihbGxgJm/1ZifrKwE2ttL2bgx5Y7h8jddjmsrpVoQ1SiL3AaRJIeR73D+tVUxdrudYlavoavrpiDhv11Kd2f8z1hHh+fu/078S/Y/8H8f+C8BBgBE6FYFFd8qlwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 14,
                "name" => "Austria",
                "isoAlpha2" => "AT",
                "isoAlpha3" => "AUT",
                "isoNumeric" => 40,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozRUY3Nzg2NzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozRUY3Nzg2ODE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNFRjc3ODY1MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNFRjc3ODY2MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+WaOFnwAAAGpJREFUeNpivCHH+pSBgUEKiD8z0AfwAvEzFiiDAYmmi+VMDAMERp7FLL8e/R4Yi4WSAgfEYsb///9/onOKBoHPo6l61GLaZafnBYkDk50uMjAMSHZiYZdjHU1cwzxVQ5s8vHRu+nwGCDAAxfQUXKOTRMkAAAAASUVORK5CYII=",
            ],
            [
                "id" => 15,
                "name" => "Azerbaijan",
                "isoAlpha2" => "AZ",
                "isoAlpha3" => "AZE",
                "isoNumeric" => 31,
                "currency" => [
                    "code" => "AZN",
                    "name" => "Manat",
                    "symbol" => "ман",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozRUY3Nzg2QjE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozRUY3Nzg2QzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNFRjc3ODY5MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNFRjc3ODZBMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+D6YUmwAAAZJJREFUeNrsVb9Lw0AYfXe52qotVYp0clI6SEFUdNBJERRHXdydBAfxLxCdu/iXCCK6lKLFOjj7B0ixSrGJTa32R3rnF+KeS5YgeuQl4fju3vvefXfHcHZvI4ImCKkoiDkian+PWCCbDTZCKcAZkGTSbITXLdZLJf8o5j4C8sOGrNZhTE+CJRJQnU+aYchVE5iYKeSU3oo0CX3g5BRIJIHyHXBxS301wjghmABh8bR/lBzAgYnM1Tn46iKaC1uUeYv0JMFSecimTY4QMWf6xeXQWvmhh3cYM2vgmyuwlvfQfqyga9cQ29/B2NsN4scH6KIOh3h15nOhWR0KTAjv76tDb8PrHo6TZxxsJO7aEshq1uBLvtGKrJZ4RaZ8CZ6fgjm/AVVtg/FRsIk01HOdZnIr0NAmp+Ka0ywu0/sWCpQcEVwXgWKF+p4IGTf9n6w1M94+2tWSyBCDtC3IlwbEbI6sH4K0TG87sRDbCQ+HKkg4JGXV63uHRywWag97J1e1E/LscW11/i+J33A7Aa0oiL8FGAD8Y4eRaTQ3EAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 16,
                "name" => "Bahamas",
                "isoAlpha2" => "BS",
                "isoAlpha3" => "BHS",
                "isoNumeric" => 44,
                "currency" => [
                    "code" => "BSD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2Rjk1NzM4NjE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2Rjk1NzM4NzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZGOTU3Mzg0MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZGOTU3Mzg1MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8++Dx9hgAAAetJREFUeNq0lv1LU1EYxz9nG0xplxppL5PobZawXsBlIAmOhAgJk0qSJc5NakH5Qj9EFIFY9Jv/hn+I/4S/bJSjaTktF9tiL/fOZ94GCiFTd77wwD2Xe+73nO9znud7FD39aeLvDIJ9sPETNiWcLnRDSVR3np5MwtgruHgVVpJQyMkCnFqJ/0gYOyNvG8TewINn4JFXKVlApQIOh2biOrpuQmQWBh5C8S+spuRLZYdW4jpqxJEZuN4D62vwO9O0/O9PXMfYaxiNg+8SJDYk/6bI/2+2VuIaTvnwxKcZDgc43loilchTrlRRDqWZWNAiMXLXyaePlzkfOiujMnyXM1AjVxqJd+PlhI/5D37a/Sck/zmsnIlyKf3EdUTCHSx8voLXcGHmzYbnHblAt7JlrOrBS+3QtTH6+DTz7/10dp+ETAEzW5RKc+iR2i0xJCX9Ze4C/sEOGYm0a4VDadf4jo8ZuF9M4Y0FWGyzWFnKUypb9onWVk7D4xAWA+m8AV9/QbYkHUxp7Fy9AzA+DbdDQiaE66uyQ2dTevb/pT4nrTEqLjX4FCyRM7lsu2cTfXrvn9wttis9ikL7GfHlhDSnor1LmudMey8C90dsWQNBSH+TBGxpvghcu5Xm+VuDO/cgl4Ufadv4lUIntgUYAIDGhVXvTDlFAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 17,
                "name" => "Bahrain",
                "isoAlpha2" => "BH",
                "isoAlpha3" => "BHR",
                "isoNumeric" => 48,
                "currency" => [
                    "code" => "BHD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2Rjk1NzM4QTE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2Rjk1NzM4QjE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZGOTU3Mzg4MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZGOTU3Mzg5MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+B1d5vgAAAOJJREFUeNrslbsKwkAQRe/GPCyTTxC/1spaBIuIqKCVlR9gJWovKAhCwCT4SAI+4q6j1tGQRNNk2pmduzOHyzAhxAkRcd1asFsm3M4Q/nIKCSo0owIwBgiONMFIWMQpDGYL2M023O6AHslgspJKWIpbKBs6JJXE4v3ze79Pyct6A6tWx2E0xtlaPRcETadVl2jVnP9wYmp+9wOE+yM4bu9y0kQGQxeMC8YF4zwZsxwYN+hg9IgxlP8yZpqKrCIZYyn9WUzmYxQ+TsbYi0qGO+c1oWP24c0n5CKBsl7NxMcPAQYAXoK9LDclUq4AAAAASUVORK5CYII=",
            ],
            [
                "id" => 18,
                "name" => "Bangladesh",
                "isoAlpha2" => "BD",
                "isoAlpha3" => "BGD",
                "isoNumeric" => 50,
                "currency" => [
                    "code" => "BDT",
                    "name" => "Taka",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2Rjk1NzM4RTE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4RkMxQjIzMDE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZGOTU3MzhDMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZGOTU3MzhEMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oE4gsAAAAkNJREFUeNrEVk1r1FAUPS8vmY9M2tJSpG4q6kIYu7LUTXWjG1EQCuJOf4Abf4BuXIi49Q/4A3TRXRfdCK6KCC5Uit1UFxXGQqXTZDL5ep6XpOCAM8lMBnzwXiaZd++59+S+cyPw6O4x/sMwOWfGthKcqjpw8dAgBmeDi+Q1ycEFl5A3fZXtEdME1iBNepQCq50QNw5jnO8liPjoiyOxdcbEjwVG4xM5VKXBzUJQx4BDp68/uLh3EOTOc+9KIWoaeHmhjqeXmxkr/XLg5kh6mWmTjnbed9HuRFBzEl5jcFuLgTz55GG5p/BwzQZiGsbFwMbIf0jvm48u2r8iuKTT+8fuE0sgmDfx4JuPx7s+YMtShTccmBRe+RnizkGIeFZqVocWeGBk7DzbD1A7YbqWqJbx/cMopa0nR782waCCuoE5N8ZNsoP6pMDajvaX3CQ9PqIEdaG2YZAr2kZUoXq8Y1nOYeE+ldX7nm2kWagSEVh6ITtftU2l4iJjbxfN1FkjKRa2Wj+By4re1jaBqgDsJ9g5a+HdkgXzOB7Ke0qOXjyF58s1+LNGJjITA8eZ141VG9/nJVpHcZr5AO387UQKjaMImxfreNGmuvRUxVrQANTk33xnV6/PYOtcDZJ0Osy+xcpNp2aCwbxasbGx1sokNlRT0GoN3k3QsQVurzu4RkG5xXN62iQ+s0lsLpnYW2Rp9fMuVbKsBT8EikM8bYtUs/Qa5UFRUkGqdT1Mvy3+3fi9ZPCAq8kPvQbujq0SFb8+9PgjwAAqQMkwoAhtcwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 19,
                "name" => "Barbados",
                "isoAlpha2" => "BB",
                "isoAlpha3" => "BRB",
                "isoNumeric" => 52,
                "currency" => [
                    "code" => "BBD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4RkMxQjIzMzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4RkMxQjIzNDE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjhGQzFCMjMxMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjhGQzFCMjMyMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+gU/BiQAAAf5JREFUeNq8lj1o1GAcxn/5bLy0l+v1esJpUcshBVcRREEo1MVBBUFcHJS6KAhS6yDYdqkIuhVcHQQXF3Fx8wsEB6WTWpGiRfzolaPtfeSaXhP/uSo4SE1qzj+E/PPy5Hny5H3eN1HYPRYQoRZv3190tlZ1Kh1/xtue4i1Zax1nTzpR+HSiV1oO9S+YICqZGkM4SAgTWzjR2pzwqhhLa+Bo631bhU0FthtgyLlHZ/pdg1dvGtArMdFkrM8EKzpd5HB9mPWoTpc5NJiHLpWR69+p1H1ePuwX9yqP75bodtLJO97TbzI8WebCxDfYovJ+zmPmk9dyee3mPKeuLjCw00xe2NphcmM0y4Mn1db8Dh2wOXywEwoG9x4tMznSjTVgJS+8Ku6OH3XIyZyWnlXJ2hqOpdB4UcPJ6pw5kYHwDSQt7K5IercZFMXh89kV0vIAmbzOU+kLWUl3n0HT9ZMPVyDBRXgH96U4ffEzmS4NX66n7pSZOJ8TJgW/beu41GRob4qx4RyZTo20rTJ+rocj+22Yb4JC8o5btdCkWLS5fKyX13Mz1Go+V6Z2wVsJXKkuAKNNwuEmUlmDr3UKKRU33Di+uLAkY7b0brsc/6qPHrcu5df7MMlKfIrNCQe/pSPs/5twKOoF//R9i3ObkhAmtuPln/hgA1FJGZF+fX4IMABjOotaoebfVQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 20,
                "name" => "Belarus",
                "isoAlpha2" => "BY",
                "isoAlpha3" => "BLR",
                "isoNumeric" => 112,
                "currency" => [
                    "code" => "BYR",
                    "name" => "Ruble",
                    "symbol" => "p.",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4RkMxQjIzNzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4RkMxQjIzODE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjhGQzFCMjM1MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjhGQzFCMjM2MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bnsnjAAAAapJREFUeNq8lc9KQkEUxr9773i7ev1LKCiVEJggGAW+QLto52P4Ar5K65YteoGibRC0aOGuhPAvpphpps7915mhV5gZOHdzF2e+7/zON8bu42O4fnlJRb4Pp1rF8uEB7+024lB7GB8MSl+3tzAcB2x/H/50CgPqjwnDWJnU1EomwbJZhL+/0HGY6bpg+TyE1d5kAvvgAJEOxZHnIfj5AaJIzpj3enqsFjYLi8lybN/eYJfLWhSzTaeDz+trOeNwvUa8XteimMkPzXjv+BiJRgP+bKYFLtOpVJC+uIBdLCJeqyFYLPRQHZC9/nwOw7bBx2MIyrUoFjSDVglBIMuwLD2NZXhQYpmJBFghj/B7CUveSG0x3u9j/fws12g7GiBnpSNOW52AWrhZxDl23a6kuegUcdc+C2/6CGOu4sZibwutFsLNFsnaJZ5OutbjFbmdU0y1UBxSiRnj/BTu6z0wpT8bxXAZsRisTIZoppG/U2QeHhHdGqgOVit4tL/CatAF+HD4n2fqIzMlX6cgJHu3FCQx6HglmF0qjXLNZiriHil2KTpLFCjqG/8JMADgr5w+bkv5NwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 21,
                "name" => "Belgium",
                "isoAlpha2" => "BE",
                "isoAlpha3" => "BEL",
                "isoNumeric" => 56,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEN0ZGMzM3RTE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEN0ZGMzM3RjE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjhGQzFCMjM5MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjhGQzFCMjNBMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+dCOj3gAAADpJREFUeNrszTERACAMwMBQS/i3ggVQUJBAJ1iSOXffgE2hPSi1OpmTaJcv+JSwsLCwsLCw8PuOAAMAnCIHFZwXd4kAAAAASUVORK5CYII=",
            ],
            [
                "id" => 22,
                "name" => "Belize",
                "isoAlpha2" => "BZ",
                "isoAlpha3" => "BLZ",
                "isoNumeric" => 84,
                "currency" => [
                    "code" => "BZD",
                    "name" => "Dollar",
                    "symbol" => "BZ$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEN0ZGMzM4MjE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEN0ZGMzM4MzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkQ3RkYzMzgwMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkQ3RkYzMzgxMTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0XP2EgAABMBJREFUeNqclmlsVFUYhp9ZO2uX6XSmLXSvlUUKlEiAxMIPSNEYFpuAEUohBjEklVg0BkyI8YeKKIkhyhIkoig1RqIQtBgggIoIItSUsg0dpttA29nuTNvprJ62/PDHzNB6ki+599xzz3vOe77vfY9M0uZ0A/ki/IyjyUREYnFC4ejou1olR6mQE48z3mYU0aNsKFli/E9HYjCBppDLcfb7cbqG0GuVlBRZiMWjdPS58A+Eyc3WkW82ir7YeBZhlFGzX0oFigBkOAT3+9HOncO8pcWsnFOESukToSYSyeDEtQ5+/rGF2JU2KDKBNg2isVTAfhkL308OLCjENwC9wzS+tZotdWYU4bv4w2l4BwbRaNJJV8UYRGKqpYatey/yyc4TkK2CTEMqcL+C4sXbxENawsMcGgY3fLa7ntfWhjhx+SgOXwQpEOCGdJ3Wvht4g0oysfBL29dsq11CbmkJJ4+1gEYsWqVMBhySp6S4a4D1G6rYuDLG3uOHmV1YL/6Qs/tWIx3dNnr+ukTTT69wwXeJ+eVr+fiHj9iwLIsdb66ADs/Y4pNNn/SLO0DejGJ2vVrNodNHWLNiJwU2O9Lh71ho2oC5R4dVZUajkHGqYTuhP+xsEmMONh9g3Qs5TFlQCf3+/wMcpG7ZTJzh38jSVuLe/yWdzctofLmEGRVV2Nqvc+r23xQs38pzUhnHNz7LreNfoddZ8UZaWF5TKvIjPEFgUafodEwrN9Dp6yLDVIhVF0aXbeRq81Hyez1srv2UHYuqWe3T45QkrroHCTp8zCqfg8PdyfzpQhqM2qQJlvj0I2KwUcMkk0YkUpSpBelY6jYTtC1huO8B2jY7GUVtzKoqxNHWTe23e8gvnE/B5Dz6B/p42HsDmSaAwpxFNCiNVce4gBE7HhECkRxGlRaVTDHaay0uwzC5jEBcgV3kZSzagyl+EkPxG2Tn542OkUJCZAL9lKSnjYrJxKhWCqBAkF53GK1axX2XfaysxTxKUUqKaTMZMsymzbmGSOU7IgfUyKKPyBocYrq1hF5/nLhbJJdKMQGq5WKrAwPcc4SZ/aSZm10ebN47nLv2BWqjk32/SmQ+zEOWWcGHbR7qp27BJFvAqoVbuGK7KEqrhDMOoXY+nxCTnMTalFxAZNzudrH9pVpuOc7T4bOTqddgu+cnquhCX3EBq/UmaQGJVls7ZqMJ31BYsOJlSlk1De+dx+UXqqdTJxSQpNKCyYDzmp23D55jT+Mqmk5/T07ZM6x9+nW8D11kZLrRG/R48tSEdQbUFg9/tnzDorlVNB0LcPv3VpiSN1YhCV0umVaPWNKQoKt3kKYDDSxf7KL54ln6I8OUpi1GKc5eJo/Q2n2HwtxhXB4HK6qf5+ylSaxctwssYkqdIDKxVY3DJLyD4PTywa4GNr2oJx5yc6//LvY+G1qlgSdyShmOD1GZv5Sthy6z+11hElkawZg+pUkIW9yX2hZHwEd23v4A3bxK6tbXUJzvo8KcLopOTjiq5/Q/fXx+5AxcbofJGWDQPN4WVz1Vnxp4lHXZaKJ39vlFeCm35grjNwjguHj30OOSyM3SU2BJfyR8j70J+GUenWXCV5+QULZgaKxwNWqFcD85E2ijV59/BRgAi/3kZlc/4VIAAAAASUVORK5CYII=",
            ],
            [
                "id" => 23,
                "name" => "Benin",
                "isoAlpha2" => "BJ",
                "isoAlpha3" => "BEN",
                "isoNumeric" => 204,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEN0ZGMzM4NjE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEN0ZGMzM4NzE3NzExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkQ3RkYzMzg0MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkQ3RkYzMzg1MTc3MTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+PY+RygAAALJJREFUeNrslkEKwjAQRf90IqgYFNx5nN7AA/Q8Hs5bCHUluoiLasmkU+hCEKQ1GDf55DMJBF5mQsIQDvsawE7t8EnPGeyywak8YqPR6/pLWfXZDBO8xBSyBf6kDM7gn8mM3hnUpMMKsBLQQxKB+9ooSy4suDOk5Zgqy3iwZhoc4VZtg3UtrnOOSThMztjXhn0T4CkKzJPvuFiLnkG9oPycMjiD334uN3QfLlX30bM6AQYA2ssuTyUvY0AAAAAASUVORK5CYII=",
            ],
            [
                "id" => 24,
                "name" => "Bermuda",
                "isoAlpha2" => "BM",
                "isoAlpha3" => "BMU",
                "isoNumeric" => 60,
                "currency" => [
                    "code" => "BMD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NDJFQzM0OUExNzcyMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NDJFQzM0OTkxNzcyMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNEI3RTM0NTcwNkE3OUY4MDY0M0U1Rjc0NzJDRTNGRkEiIHN0UmVmOmRvY3VtZW50SUQ9IjRCN0UzNDU3MDZBNzlGODA2NDNFNUY3NDcyQ0UzRkZBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+iROkEwAAAwZJREFUeNpifJaZJBnovvS5ZOPyS7d37GJgEF9RoRM+L393bIfbpK8M/z6JCf7OkXpZGyj3T1nzZEUD67NnjAyMDEQAJtnpXwrCJnnfXH82jre/L17exuD9D0aGvwwfmDnFlHmLVR+djmKszLZq4XHUXfv32/MXfAzEApaQZK83kiJb+D75375QEB2YVhj0YMnaCxMZtJX5j02WU773hyEkbtdb3quTtur+fCnIwvbr9w8ijWb8ryvK8Ov/88//n3z/x/D+vSgP249vv17/YxRg+M/FwfRdWunns+fCv74qqIoyMDOdu/7697//LDDN/xn+/2cAhQ6ERAsolvMSpi+ev/4pwGQQ5cDx7B7328cfmbhfbN8n5OIgbaZ5d8cRRlGdaxLqd54/FWb/x8B0hOXfH6ijgCYyMf3nF2SVEP795uP/9++Z/vz+jxzWRteMm/y7WVZvUciP5LcwnulQdDWxSp3j/4PA1NVJ0yS27dQujv8po1goFubEEPz97z9eBqj2Hwz/hTPSzN69EZEVNX14Xaqr7ytICmE4S121S5LkF/atcyqWn5/6TPrLR97e4p/uIgx7H7+PVWnjcRErC9NPs3mySPb55q1XfnFx/Pr6FRoYDAwcsgoXX93bc/uL4VsGNX42RqhnoID5gOz3L51NO0+/PvWcUfb9Zds/111YH3LcvcX4/z/L25siN9Y82byFlZXXWoLR8urOr09e/fr3DxKm/xgYxGRVbn260PqbSZ5ZyPL0/tdnzjEjmc54HExxMzBIMDCwgdmPGBg+MjDyMPxXAKv6zcDwjIHhMwMDOzD4QK6F6PzPxcBw28zgWoArDzfXizdPtLefMjpz+QuSq1lYwZxfYBPhPuVkAKZshrtITuAAi/9H0gm0huf6lSvSzx/Li4vdemp54y08qUCNxkyOGAzs3I8MDNLf/1fc+3Xlx1P1l//5GdjeMPxmRlZxhrhcixUA0zUwDLmYmL/9+/sT5A/UdM1AAQDGJzAmPv4DxigjE2YZwkAzMGr0qNH4AUCAAQBpbzCLqENWxgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 25,
                "name" => "Bhutan",
                "isoAlpha2" => "BT",
                "isoAlpha3" => "BTN",
                "isoNumeric" => 64,
                "currency" => [
                    "code" => "BTN",
                    "name" => "Ngultrum",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0MkVDMzQ5RDE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0MkVDMzQ5RTE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQyRUMzNDlCMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQyRUMzNDlDMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+QtQgBwAABGxJREFUeNq0lm2IlFUUx3/3eZnZmZ2ZnXV3dZ1cV1NTNGWTfEkj+lD5QoJZRBlI0PahD9kHicwPBX7ID0EEkQgZC4UaimVipSAqkpZFZUQIEpqaq+bLuPPyzDPPPM+9nRmF3HxJN7ozl3nmzpzzv+d//ufcq/Lbp54GcjKL/F/DyLTlPUKhL5q0tyXqd2QpffXn9NC8KnmFKBOiVfyadUvWqsgitMawmg3Btxpva0j0h0k7Qw9DNUKxjUcUBQKdxI3OYixx6bQ0gENtY3UmsAc8ypugujcC2Zt9l+I/AEfCXg1ftXE5ORNaekhXD9Ps/wiVU4TFk5h7nqH6y0XCD88Qnk9jD5fNxsRUM1Rgg9JV/NT9BJm5JEfMI5bOEYs/j+cV8YqHaQ8PCMULKH61E109Q3x4GePEG6BXEjEUkiWfEU0EugmVe5xE6zhc28VNSl7joqT+FNbdqyit24a9ZxOxYQqjBsc4pIjrTiy/H6dV8tc+hlqpjHduP8l8QaIei9V/hONr95E4cwwnnYRL57EctyHuv6V3J+pVElHwJ5Z3TD4vCW2iZL+Am27Gbe2m8PlWLi+YRXjiFJlpPWSXr8KkpFjCUEDNECJWok5dQZeOEYx4FpUcjamcwD7+AeFADR7toyU7EffNDQQLe4m8Ctn7ZhDLZKiMn4T+Zh+MnSBUmTsDVgIaRprauDWYCS+jpC6V+IiOLCb4+Sjqwlmc0Z3oHduwusfD7wcprHwJd/RY+O5r1MiuQaC3Dyy0mpZZmCkrMK4Ph3diZafhTF6CO1l8FopIq6C6YzNm1xdSZpJBVcMLApyuMdDc3KD7joClyYlNFd25GF23//gN9Jq3Jb3tNM1fiJ46HefFV6jt3IFavxklDcOM60b5Hk5CDHR0HegtgEVIutTQnhXmG7KoZqdiylL/TQK0dj/eutXU3vsI/cBBatPnYIZ3Yr22nPSDD+NLvVZf7SUmYjSx2A0RnBt3dMlfbBSWKLimE5TufQuGzUQf/xV39lzaurqkPF5n4KfvccIaZtl89IyHME8tI7PgCTJif27Jc0Tr38UaM/52gBWWqMaUT+Bncqg5feiO2Wg5t6xVvQRPLsXb0ie5/AR7Sg92W4d4kJq2Jau7t6MP7CbvbiQzbxGOL4J0YzdN4WBgyYexC4Q9T5PsWsHlQx6qKo1h+zr8jZuwOzpILu3FLuQJ9nyJlUpd4agOMGEyqlSgvOIFKqtbsWpV7LqwtL4F8NXz0mkPKA/kiH57BHfDp6gt7xD5Jfx4gihlk8mNomPiJE4/tojo4F7soIpJXN17JAJKpoQA6VCVUuP5nyU0GFhEp7JCZVrhH3Dxd8n/j64kCKU2R+awrTY56qQ8qj5GWl9FjMK+97HPyxFYr9NrIzLy7LoomVe+3xxYFQ5NK+i8SVc+i/D3aWy7hqo3dcu9ztDUW18sjlUReSeSN6XxNkbRCX7QRW9zmI5OmmL9akLMbWz8ut1a9RuFrOUvQCZ7y2j+ZdRvOsW/BBgAmn3MQYJZCMcAAAAASUVORK5CYII=",
            ],
            [
                "id" => 26,
                "name" => "Bolivia",
                "isoAlpha2" => "BO",
                "isoAlpha3" => "BOL",
                "isoNumeric" => 68,
                "currency" => [
                    "code" => "BOB",
                    "name" => "Boliviano",
                    "symbol" => '$b',
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0MkVDMzRBMTE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0MkVDMzRBMjE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQyRUMzNDlGMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQyRUMzNEEwMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+QnwfiwAAADFJREFUeNpiPCeoxkAbwMRAMzBq9LAwmvHnNbHRABk1GjdgYV+iPhogo0YPiNEAAQYAm/kD5VlL/agAAAAASUVORK5CYII=",
            ],
            [
                "id" => 27,
                "name" => "Bosnia and Herzegovina",
                "isoAlpha2" => "BA",
                "isoAlpha3" => "BIH",
                "isoNumeric" => 70,
                "currency" => [
                    "code" => "BAM",
                    "name" => "Marka",
                    "symbol" => "KM",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5MDZBM0U3RTE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5MDZBM0U3RjE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjkwNkEzRTdDMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjkwNkEzRTdEMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+PElEFQAAA31JREFUeNqsll1MW2UYx3+cnpZ2fMjYhyJj42PlS4tRRyLbpIVSyaJXM9NsQ72AJe4GF+PXmqi78MpEE73AmEwCiXFT44XxwmWBjYgj2WBRKKWTfTAwhE562roxOGy0x/ccLcY5J7R9knPy5rxv+/T/PL//cyoXFHRc7+7exZo1Flyu40jSfdy6peA/8SnV7iuovgIwxUk2rFtmOdbpZt+rrWzYEBHfrxnP5bVrbTl79nyH2WziyJHt7NhxP591zdPQspHvu9/hsaZfUf2pJb9bSMHgTVR1iVBIobAwW6gu4aUWK7+FVB5/+i38/VuwPjwDMQktjYlls1kiP99GOByno+Nn/H6FvtOTNDXlcEPdSuNzXnq+fB/HzsuogfQpl/VbPK6xfn0OgUCYwcFx7PYiBocOGAe2Vl6nxtPOL30fUl43mbayS4nF0lJMAGYW1zoWFzXOnQsa6kMzfrG7kQrXG4z2Fy+XPS2KE5GRISi0ymRlWUSpv0KWJdoOPEpz8yY+/kTF+ayNvq/fxuGaRh0tQBPKM1JVnIjMTBPB4By3b8eJRMJUV+fj8ZTR9qJE+PcFap7y8lNvKdbqGTJSUC7f+UDvt8ViEj9AFoljdHaOcvFihN7eSZzOXAGcneb9Xvq/eY+KuquoY8n1XP6vDU37E7jh4VnOnAlQUbFZ9L3N2CuvilLZ8AqB0x9RmWTye9YqAZzNto75+TgjI7MGcNNTOnAPUNXwGoGBzVgdq/e5/H8HdOBsNpnsbAv19ccN4A4dqsXtLhTALfDk7ixOfP4u2zxTq7LaiuhIAKeqMRRFoagol8bGUg62WlCiC9Q+c5gx3WoPrdxq8koOJYDTrRYOx+jq8jMxEaWn56qwXS5zi3ah/HV+/PYDqp64sqIJJ68GiL8nnMLZsxcoLn6Q8+dfNvZK7MJ6DX9NuO1Tyz5PqdR3Aqcr1yecppkYGrqGzxdCuXZhecINnyoxJty9fC4nY/4EcHrv6+uPGZ5vb99Gk2eTAZx7by6nvjhMTeM0zEvpUZwIveeh0IKxjkYjlJXl4XIW09oioUTmeGSXF98PJZAXZ+kuyuVkE+v91l+pFotVrGMcPepjfDzCyZMT4s9ElgCuHOfuN5ka8VJTLpRj+lfiG6m8ZRITzuebZWBgDIejVIzXfWRa4tTW3aR250FeeP4SeXnRf3zuDwEGABm+YrFv6121AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 28,
                "name" => "Botswana",
                "isoAlpha2" => "BW",
                "isoAlpha3" => "BWA",
                "isoNumeric" => 72,
                "currency" => [
                    "code" => "BWP",
                    "name" => "Pula",
                    "symbol" => "P",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5MDZBM0U4MjE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5MDZBM0U4MzE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjkwNkEzRTgwMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjkwNkEzRTgxMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+HqOdhgAAAHJJREFUeNpiKV11+ykDA4MUEH9moA/gBeJnLFAGAxJNF8uZGAYIjFpMN8Dy8vOfAbGY8fb3/5/onKJB4DPLvMqMgfExEA+IjwfMYpbsxIiBCeq7A5W4mpffHJh8LMrDPFpkjlpMm8QFbfLw0rnp8xkgwACv5h08wmledAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 29,
                "name" => "Bouvet Island",
                "isoAlpha2" => "BV",
                "isoAlpha3" => "BVT",
                "isoNumeric" => 74,
                "currency" => [
                    "code" => "NOK",
                    "name" => "Krone",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QkQ4RjhDMEUxNzcyMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTA2QTNFODYxNzcyMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRjAzOTJFRkE3RkEyQzU4RDMzMjNBNTYxNTJCMDI0MDQiIHN0UmVmOmRvY3VtZW50SUQ9IkYwMzkyRUZBN0ZBMkM1OEQzMzIzQTU2MTUyQjAyNDA0Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+71lBoAAAAj1JREFUeNrUlL1rFEEYxp/52FwumlyS8zjJEQl+QQpFCAgiAQlYRLA5sImFYJXCFDba+ScYtTSgIH50FjZiYWllyqvE4rwPuLvEwgT2Zj9mXmfubr1OdosIvizDzrs7v5l55nmHtc9dmlB9CE5wYZrt8q31zqu3px9+7n/b+/Bm4+brx917D/jSeaQPv4+Fk9ILlAwUeILWPqLI83hMDBrCc0kBLZTKgFaKglBCMLtkw8UwSZB2GtDoH7IvzPVIiAxoIYlLjiOLI0MzSNNqmsg3VodBaITY69oPYMy2zKYPDozttRtZwCF4LEsbVa4jxxpGr4frq1ZdFhtY5CFw+WL52lUsnspAjiI9U5CNnZfWD4bGGykCzcY+WdsI3ql9x1q1tVYlyqYGtGF886Mhhj/LtjroGFEIT4IRwtgZ0MuBTOKYNKs28zMT0vzyrcBjQZy3yYHmGLiE0ohieOEIytMde2ggNNv1tcxxbZKsxBxQr7XXn+0GneDp5oXbN5Z/2HzsZk/pVauCJcqVR/cHm02W/XMfq1cKd7YwWUPon11ZLn79VNx+joXKaFAqQWIzfVx2n+x4cR9O14EYoFK9Tne3jPTAiWaBF196796LwdyEdGiLmT8h+eISV8oW+nAQa7dQqgxK27g2Bgqz7pQrZ4C0YKgA5eK/L/TUO/9LSKa1exLDMkTQmjic2Y2rdRjjBLFmTx86Iq1llJsCE+P7Wk4jl9caeWEOc0QWKDxrbMofy6IFx+TU/3ipAr8FGAAsDO04lr0obAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 30,
                "name" => "Brazil",
                "isoAlpha2" => "BR",
                "isoAlpha3" => "BRA",
                "isoNumeric" => 76,
                "currency" => [
                    "code" => "BRL",
                    "name" => "Real",
                    "symbol" => "R$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCRDhGOEMxMTE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCRDhGOEMxMjE3NzIxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJEOEY4QzBGMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJEOEY4QzEwMTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+TFbakQAABKdJREFUeNq8ll1sU2UYx3+n7en6uZVa1oGDwtYxnCw0EhiCxjgjH14oiV6Ampj4GRONRpQ4ZZEYvTAhwSxGjRFilO2GDy8gZuEGTAS2xUHmBvuAuVG70U0o++ranq6tz2mHc8YB48KTnL7pec/7/p////k/z3kV6h4ZAJz8v9e4SX4Wz3uZkoGEFWQgL343wE6Djj4vwJTEOpHPA64/qXIPw6Qj90yfmwdjw529N72pDjJlYufybloD52gKnOfDkk55ZpQ55+x3b8dBcjx2yxzrTJJmiFtYt2CIH9adY0VJCq5PY3ggeEXhueY1/HK9SKRPgFnujHJLxnMD64D6YmFpMCX4pOQSNRuvQKeRfYeqaOv2kpa4K/3DvPd0CwQ09p1ZQs3lMhJJG1gnwJCeK4A5gLPmsWSZbiocoK60i/LKGCe/9/HMru1Eki5MJNG3TGLGbR6j/tMjbHmpm76OPN7qXcmxcDGoU6JA7L/Ax41sXVaje3OGpeQr6qTAEqOu7AKf+3vw+KY4edBF9c7XUdIKRWoILX0VG2N4raPEkk72n6ii2tdD4OEIO8xDlNjHOT1ewGS0AEzJHHv+DkCbYayDxkWitIEdi4LsW96F1y4Ri2K4TRxsq+NseDXL3EFsihOTptLRc4X2kyEutPZzjWKqK4Y40XAAY1L2kvgjk0Z29pfz3eDSHKg1epP9DGNFSmKVbYKvy9rZXdKPQ0mTEJWMLujtdPPlkScYutjDxeYQkXCUPNWE11/A5h2VrKpeQrhjkKZujSfX9LO4YpK4mM9pzrCtSMrOHuGikBrWLGRydaSZssOUSrGq0bT6LPYFZB0bE2UMEpySD6FwHocOt4mBY7icZnpbhvkx+SsWye8Kv5fHX72f3Q1bqf8mTGjwOGt0frI2Jua2agi3CJtdzfhbH6JPwHXpc8DGKa6mjGzqWEvtkl62FEawSlrisghpTIWuKL4FThxWG+lUFNVqodCejyqsB4IR9u5qJFDp5/nabWzwecgMhciI2lapQomNU0EXe4KlhISgjqVfhpummjKmODPiYetvVbzSeR/jkmuL9AvxD+WrR6RsBgjd8OB2W9CSKWFkkFvBas9j+T0uLrRbOFa7n4X2ThTJr742LuBvdpXzaNt6fr5RSFI32HSHm+lcmenkSwP49o9Syls30jDozXrCKMO7LzQyGosRDOfjcJjErfHsnZ9vImNeKkG4eefln6BA9BUdj4YXslL2+KJ/hbDWcnv/45pdTjqKHpGAT4gZjg4V0x6zUpUeIfDYCGvdA9QfLyU46mE8qnJtTCUkKtnUNF99cICnXuxhsE/lte4Kai9XMJoSne0T0yxn1bJ2684lchNziIMn2VvWwxsPSu7OW/i4YT0dl4qkYyqU+8LUbG/CsSHG/pZFvN21komEHWzC0JCaZ+f6dwCaCJLIo9ozSP36cxRJUyI83asXwY2r8OzZAI3D94qsydyn8q579Rx9WzoHe3yX+cjfl5367Hcf7/eXSe+UMrHpsqZvBzoP4FlfKjV7CNjoDaHK/1Pig+zXSPrAHQDOOoHc+bFH39gkdaiOcfq6N2cYneXNuXmcQP4SYAD5sNLFPECeSAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 31,
                "name" => "British Indian Ocean Territory",
                "isoAlpha2" => "IO",
                "isoAlpha3" => "IOT",
                "isoNumeric" => 86,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QkQ4RjhDMTYxNzcyMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QkQ4RjhDMTUxNzcyMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMUM0QkU0QUREQkJFMEY0QTBFMUM2MUYzOUU3M0FDOTgiIHN0UmVmOmRvY3VtZW50SUQ9IjFDNEJFNEFEREJCRTBGNEEwRTFDNjFGMzlFNzNBQzk4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+507x4gAABXBJREFUeNrs1OlPk3cAB/Dfw9MLWqAUeggF2nKWq1yFCIgDryGiQ5miTpxTkTDZpmObO5JplmyRxMjUOXRbxBmHyhweGxKCnAWs3AUKT6GU0pY+pS32Pmhpx9+x7M331ff75vPiC6kbfmFsSR4C9Mqn8+PNQoD1bazkHm+7MfBWeW4fDHQmdjjuxzBjCT9I7Rve5x+ZzqF0DS/WPRyWopay7JT33g7jRdG6RIprbQKx2CcnKehUURIrNGRQJINA7uU9RMMfFan+CZG3UGxVO3qd7T77T1377soiCflLIP0un+YJpFQtEH+dtoLRUeDjBj74U/v5adygL64+s2L1ZBYE2winS+P8nfFtwpVeAQIMFgo7GHOiKF6FITXorR8Kp85sST50dbexvcd+zcilktDcSOqyHfA331nwILP9JwLB1faPMXgsDoYUGqvD5XlwjzUpI4mWFbGRmCCPm+yAOr4vxWJcWpPd4/FA3rJi4HQAOzCZ1h1OVxDscbnXrL5kX7sRD7zmkFAfg5EMO0CYn3WdUBF9BBcbM/pKLBlBQYSVlwnTInUB0XMTY9D803zgCgbu1eRUJpdNG5lUQTOXr8llWiuBsCWfR52bAUF4m81j/umu3/5i/3cLQXu/VmcbonMgjZ7LIN4hc59M6SryOGf38fA43M2uthb9nWAfEE9JzWVs3RGfozOYbzyfah2WHSuIw3BHqcxtO65k+VEZNpcv5oKJvjl8vYzUKOAktjIKas/nUtEZXBtyTEJAHdTu6rhLpwsHZ1UFtY9HpGhmpvfwtiqiCtErIo42ClZRwbGdvM/K0mv2Jgtn1VBjU89xph0sKc89Ruq1VIDCTdUR5cL64bQKfoMWMPwqT/JvpUDAZviteerU2NoGHQjAVR/i82Oo99tUHa9XUkkjbgqnZOdODt3TMoC0ds4AjTUkjgpJu/r6H/VainYYqWGxkE1tsmnnVSzRCHvvdieH5dSbBhDUEULeSvcrdqOepASNF7uJiFVqbQqNOTGGLrI47JJ7MbyiQN9oiVieHkfDwD5KvRUPeyB86U0nngTUK/kZoclRlA6hHNHaAJsFpIrtXAqPS++bXJ5bNlndnjU3NjGclBFD6RhcWlYbANk/l6L/BDbw83ye/K0ZAL6dgTzdrJkVTY1gBksX1BCqM9CDAydl6N2O2WFEW5YfVVWcgIEJUrXudqtoUKwtyWHXHsiEgI9MvdrwYmoI0ezJiqzZl4bFYHWil2Nl21e5ZIbUwD3/Oe2DyytG2+3WcaEY3ZPNgQprm/rFmoNbY786nBEfHvKoF/n0Vu+K0Xlgc9TF49mxzBDBtOJcQ69EadybE3WhPDUxPKRjbKnm595ZuftgFnxpl4BCT7Eoxs+1BD+bTSrJDfimPJvLDn7xagE6U9+aEEFt6pt/NSYDZheRQfmoNC2GGfD7S6R7ZBE4XBh/fPU7vCQW9X4n0jO8CKxOmEyqOZDCDQt4/lrtxZP5sJwWF4MPoNjN5g6R8km3BKwYGdE0MDav/EuAaFbfeL3rK0aL02XvnpC1CBCdweT1enRGw0bKVPq214jRavV6vasmy0ZzfE71sGtarjMtmrwt31bPjQsH5fbmrmnNm42VV2s0Gs1mCFdYt2awAAiKT+ckRdE6+5FVpQEQfYHLyU1lprEZ/VMqucoA1l3A443lhiZHBveLllCFGcAwWLOzkjl5tp5eA31pjQGcNgC5IlibmDT/RaUOEsvQOBbl5ejS9ZaR4TndkYL4r49mB5EIfdPL9X8ODc3qdmUxfziZFxLg37Nxby1Do5KVoiz2xffzaIHEzgl53QOhUu8o3xpTW5ZBwOMnZOiV5qE+0fL+vKj/Qf4TIP8KMABGR9CQ7zSDAQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 32,
                "name" => "British Virgin Islands",
                "isoAlpha2" => "VG",
                "isoAlpha3" => "VGB",
                "isoNumeric" => 92,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6REM0MjcxOTMxNzhCMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REM0MjcxOTIxNzhCMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNDJDQ0E1MjFCMkQxQkRBQkYxM0U2NTQ3REJCRjE4NjkiIHN0UmVmOmRvY3VtZW50SUQ9IjQyQ0NBNTIxQjJEMUJEQUJGMTNFNjU0N0RCQkYxODY5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+SkUFawAAA8xJREFUeNq8k21sU1UYx59z7kvftnZtx7rSgbME5qgbhA1JlBEcUaOxiGxTDMOJCSaKgZjMEPniJ/lishjjDGriy8QPkCFbFGM2FhMgy2S4bN0kMma2du267qXre2/vPecebxkTv4jEOP+5H07uOc/v/J9z/gfNtLU5d9f0JO2tbf2j3VdBZ+t4Z9uhy5/21e5rvEhi4bjDht5ySyeaN6Y3PFS85zzct7gLI+uHT/cedsNLu9ZnytzXx6KPldFHBy/6ijd+5aM704HPvEUNXk9HtLSh7WZ0aoIQlRBK7ilKGCUUtRw7OxHHjeX0qDMmND0RtLkjnT0lTc/On/qIr6utjt6Cvc/1TOba3/s+9vvMyNB0MqWIIocQyDKjlMOgqoA1iwCU44goIsZQTlI01yi1v8F0YzwJ5lCKyDPhcrcjm8xMR+IOs7HQyC3ZndJCwpZNODz2FBLdI575nKjjCZXUdR5RWCsoOYDcIhb0yGBWwiT8q4REkHOyhuaD1nUJSE+nieeZR8qBmOMRIgHr7sZba4r21yvn+6TCgmFLrTMTZ3oeNLeMsrxPk7leqj44cCW8pj5ayPG3oh747VwVN2aiLLN81rjuWtHj7pZE95mKT06Yn9/Vvr3hauMhD8jj23d/8fKxNZc7Kz8+vrS5olHYuTVeF5NBj0m+WeDjSYnM+g1ziaNPf7jD+WRiajSbUvIz+e82+t0jO0LHS14JXjr31Nuepgtvtv40NzRqrCzmY5FXXW84t3zwzS/ogHfT+EF2SvApsiIBr5UxUDnAkso/bKioNtv1SExQkTKMtZkV8Q+qqTOtXR1zpmsLBUBTgJRLw2k1WjF0E8BgmfUNNh+5/v6+vSe9lS6r34KzccY0WwKwOBPGlJLD5qWQLA0sTFp4IfIXbh79eltvICBo9jleEg06YGrnlamz3DY0sKTXgmB0EoWNdP3wYpeKkEMnmgxYVVWmADbJOSsJBHn3A1Z9tcs95F/MyFpaAKM7G/DTAc2ECFqGiMp4LUZI4LX7ygAv5BtXVY4DjrOx/HqqLdUGGloFssXowNj+c0arhTKbRfKbN+idw5CllN55Mghq7rbAc8uDu3exIi0aaOWXLBuaX4h++3VvdrzqO7+rfaK3xxdr0ds+P9k3P1vcP2hHIP0t+t4iBDDWpWZze1zBA5XZItuN1k0zVXTx9Jeuvv61oRBFoCy7ee3PGp1evB80QiwnI5VaS0uTzd4Jd0EmLAkdP272TxZiHNXpVSlD/iX6H5WTbr9GWAUtW8SwalpFNP9fne//6noV0X8IMAASR8khb/BRHwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 33,
                "name" => "Brunei",
                "isoAlpha2" => "BN",
                "isoAlpha3" => "BRN",
                "isoNumeric" => 96,
                "currency" => [
                    "code" => "BND",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2MzhCMzg3NDE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2MzhCMzg3NTE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJEOEY4QzE3MTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJEOEY4QzE4MTc3MjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oskd/wAABCtJREFUeNrEVVtoHFUY/mZ2Zncnm002Sc1mczExdFPNbpMm3Sy2+FC89EVKSkEEFUEUH/RF1CpesIIpYvVJHwQfVAQvoGApxUihKRpQm6ZJtrm1m839YpPdNMns2r3M5fjP7BqamLVGLf7wz5xz5sz5vvP/3/kPl5xyy/gfTGCZRSd4anECOL6Q3lajc+uBhbJXwNID0NUImDIB6BrA6ItBhhfBcU7iIW74iamArTjLL71qct62cYzMXEz/jYBHwDLD0E0foP44gUyCabkYEBmOLwBvlyDPCpA8DFIRh/QyjVv+IfBWxvQ1IjIIPd1L7xHoBjHlCuzVMYydKsH0SRH7O5YobA4iWpAL038AvCUZdZZCO46el19E/4nLONK3F7ftHERycSWbIiM0PEeRMVJkz6uXbWeHE2qgogYjZ++C82A1SltOmuOS80eoKz9kU5QJk2bGAVW+gYwlR8aWXefQoQeZ3+9HMBiE3+fHTm9DXtCJnl4sd34Hy5lfcH16ChZRhFTlReb+gyhvfwR1LYXrc/XMEBEYymkmRGkaM8XLtIzJw/ANoa6vr0NbW4A8iObWVrgTKexo9sNzey0WOs+i9/mjqLG7EC+QkEomoceimE7EsPfddxB48nGsRmXMdJ1H08MPbMpRigiM5vQymB36Kz9M/oWngT309BPss/ZHWdcnn7OrpIvuYx3swuvHmWlzMTby2gmz+WGtl33qbWU3sz/teLOVUVC+QgmO4hqC1D9DvlxVgbfXgIbKaswffwEVchr+vhCYfzfCL72K++Rfb6oVy3tvOd/cUWaBQWFVZtC0jROS5I2SC3fwDrTsc6IRVpyei2K/JmI2toTnvv4Szu9Po9y5ho8/+hZ3NrXip+pi8Kk0SlwuWCxCvuNUld3xioaZGQ0XQwp6QyqGLqsIDSuYntbwBirRjRQiiGOm3YvHzi3AJzvMI3FekvHNPg/uGQyjJCrgMF+Ep/Rr5pKeinIEAgFTuLubmrBnTzNqa+uzwMlJN+Pp3FtttOVCcscfRYAhs6BhdFHBQocdw2IKnYk4nu2rwpH6AoTnVaqoDPWlAroX0jgwO4E2qmp32xz4IBnNG+Jdu7zw+XwEPOXeMscC4QsFRKTUqJMqEuMcLj1jx3wPQ10zBxsRNdIz2q9BWhHQpSTxPq7+/Usi3wdVJ08Qp7gGaxnlOkY1oFGDW+PpAmOIzlKNjnKorOEhNGs4dq8dB4pLcernJIaGVVyJqIjL+XWbd8ebbyOJwOFiMAR7fY7aEYGqEwfRr8BWyVDkNmaK2dKQ0LE0p2GACFzoV0gvCkJDKkbHaCF9G8Drp53WFKn8WiRqO5khTUDmoZH0lVR2jqkX0dAKedENNSqmIzKpYSSs4uIlZRvA/8LozoDVTg8nucSv5zh+q4F12loqSQ/Dc7H+XYABAOng97l61jkHAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 34,
                "name" => "Bulgaria",
                "isoAlpha2" => "BG",
                "isoAlpha3" => "BGR",
                "isoNumeric" => 100,
                "currency" => [
                    "code" => "BGN",
                    "name" => "Lev",
                    "symbol" => "лв",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2MzhCMzg3ODE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2MzhCMzg3OTE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYzOEIzODc2MTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjYzOEIzODc3MTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+McDtIQAAAD1JREFUeNpi/P///yeGAQCMQIv/D4TFTAwDBEYtpl/iYpiWN5q4Ri0etZg62emamtBodhq1eNRiqgCAAAMAqc4MILa4lBgAAAAASUVORK5CYII=",
            ],
            [
                "id" => 35,
                "name" => "Burkina Faso",
                "isoAlpha2" => "BF",
                "isoAlpha3" => "BFA",
                "isoNumeric" => 854,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2MzhCMzg3QzE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2MzhCMzg3RDE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYzOEIzODdBMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjYzOEIzODdCMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+uO74RgAAAV5JREFUeNrslr9Lw0AUx793SY6m2h/iVEUQRJfo4KqCc/GfcHJ08B9x6ayTq5uT+lc4qEtBRFsKkmBKY5Im50tSJG6XgnHJg+Nekrv73PflveOYbe28AVih5qIca1B712cOcn0pcI5/sgqsPmvKgJDNvfX5psWznuX8PwOzrJNjjuhJQN/3YBx6iB5F+i4/RsX09lesrjIpvi0fHhdgXQ8wJep3NZgbPvBiAAF91xTBg0XFkRGp9TjEboDm+Qiilm04vBni46oFv2+ALUhaUaqBrZOOeqgpmWzHxPHgFZfWQxqGs+dt9MQ6lk4pAgZBpaJie1lHof/c1HCgTXDfX0UQc+wxD701DruuKUNTMCYF0lIymJqLa6eF29EmQgIfdYZocxfOmAQwWUDDRfezyDnNksUDymQtSkFsSkAjLCI2MVcvWn+SVCegn+ecX53VFfh3OWVXnkbJVx/3W4ABAMUjZvAvTtkOAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 36,
                "name" => "Burundi",
                "isoAlpha2" => "BI",
                "isoAlpha3" => "BDI",
                "isoNumeric" => 108,
                "currency" => [
                    "code" => "BIF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBNkJDNTBFRDE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBNkJDNTBFRTE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYzOEIzODdFMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkE2QkM1MEVDMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+f3jizgAABTpJREFUeNqsVmlsVFUU/t7SmTcztNPOlFraTltIIZqWkoGACHUpFQiQGsQUECWYaGI0hAQRarVCadnREtTE5QcaI5KIQEGWFBAsGLQFbKEsFpRANwp02uns25vreY8wTKdFI3onNy9z77nnO+fc851zOcaYQ3Y4cXPNFnRs3gQZbugSRoKLE8DCYfzXwXE8mByGt+8KeEhIW7IU6RUrwBEwuyfkbvgNraXvo/engxAQD63ZAsZk4L7IvwPlRQRsHQiiD0mTpsKysRLxBRPVPYGVJFQUpjyh/tGkD8PQV16ClDUS7vqzcN9pgSjrwEs6Ao/xnoxhYUYeqW7FAAoIe7zwOP8g49MwvPpDZH9aDW1mRkRGqJtcX7G3qw4Z+mEYFT9CXTRY85Hy6iIwuwfOhlMIem9ClJLBCSKY34+AoxUhnw1hv4u+3TTtEHgDeI2WDAJ8PVcQDvmQuug1jKrZgfinJ0cAj9w6iQX1S8BlHyxg191tgOxDSdYcbBhdhhEGS0TQ9esZtC0vR8/PteDoJ0APU/FsJE4vgiEtHZ6um7AfPQ7b7t2UH04FF4kTCpG5qaofYIe3C6XN67H9+k4KiQhkHpjEFPC0/Y8zfGtmSTWj2eaWz1ns6Ny4hTWOmcB6avZH1sJR+/baH1mTdSJrX7VuwNmPrn7Jhu6zkn4TS/1hPBt+8EnGKcD3rBLJkjv+bjg9nbDSva/NW4EZqc8Mmjgtp4+jYfGbGLthE3ILiweVOXb7FMqaN6Lh9gkYpFQ8IqUgxELqHh8tqCyaNEnITspDY28zZp5YgNfPvoNWMiR2eO09cJ37HV7bnQF7SliXNK1CUd1cNNjOIsuYi2TJHAFVEzDa4+ghcAK8dO9d9kvINltRP6UGKVpzZJ9IBht8SCJuxkWdc8luTDw2BxdvnUAKARpEPWSFkjFDfBAHFWENHwcdJZokaJEYF9/fMJopBBo79IIeOkGCqE9Xv4OB/i1whK70E8l7Da+BwuTztd8BVNEem1ECLcH7abXlyB6EAn7kz3qRFHKqvFJzGB5ceB4ILFCpC4SD8Hk60Kc1wS67yHMdmkoWgnMFkCE/Dy0BEJPRWPIygn0+jAy/AA0ZYw84IHs7EaR7lchgmYX/GVjhKk+gCrdZyI35OYtQmbsMCcIQ1eNxB3aptdfI3b3ZBFIxbv8uyMEAJE6rKjxQsA1Vl7biq2vfkAc6ZBuU0sv6RWAAnXoDdvS6b2CUyUqAb2GepfihG0RN52GUX9iMi91nYDRkwKwx9aeT4qESjmt9lymTvVg5ZiWapx3uB3r7s21oLiiE89jJQUHcv5zGhaeeRVf1J5G12WnTcH5qLdZaq0h/iPRfImBZvUZOqSKtdI9y0IVZlplYl1eKfOOjkcOecxfQVrYa3Ye+VwOlQSKSF8yDcfoUiEOJm7ZeOI7WofvrHQgwm3rGXPQcLOtXwTB+7P2C47yGd6mY7G7bRzVfogvdbmQ5SWOoSr2NuVEeMn8QbeWr0fXBx5Q+DurROdSjKVHcbgR87Uom0NSQMUGaIWi06RCGJFBeBNXeK8CA1MVvwLKhErxBF9G7l8KvVDNuaVMlW5O3nPh3n5M9O/eQl1Vw/tkISUyDaDRSC4zhI1FKbYs8tUSeH9AWZacT3kA7hmTmIWPNe0heOD+qQsr9HwLey1fRuqwMtkO71C6kNWWRN+GHewhQj+aIbn7bDfVVYyoqRmb1eujzc+9uK0+fsMuNW1u/QHvFWgRDNkjxOdRbxf/n6UPRYEEZXsdVopoR6eWlSF2+GH8JMABj3HBuy+V6lgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 37,
                "name" => "Cambodia",
                "isoAlpha2" => "KH",
                "isoAlpha3" => "KHM",
                "isoNumeric" => 116,
                "currency" => [
                    "code" => "KHR",
                    "name" => "Riels",
                    "symbol" => "៛",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBNkJDNTBGMTE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBNkJDNTBGMjE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkE2QkM1MEVGMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkE2QkM1MEYwMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+4kgdbAAAAkRJREFUeNrslUtPE2EUhp+5tbTl0gsyA2podCPRjSuNMV42mBAIJSxYse2OFWEhv4S1MTGGJcGNP0D37pDGGu6lNE0HsC3TGc8MNSwobYwRNj2Tk2/mzJnznvf9zpdR4L3HDZjKDVkX+NpM+UxfRda+KxPkcjnDn0ANQ1ZPnk8J3R6Vlwr17R/SfaxlXhuz9fvYnbuL9QQg7nFFykNd/NabJxAyOFzJEWrWUKLhIM87qXSsqZfaAYrXxJPPX6KEQhTX1pDSVAPgDFosRmnlnfBDuELqxWsUTaO4vi7N/MMee02PT00xODtLQ+7L4tb8PEmJDYyPM5zNUmmqkJBYfHpaJO9sWhbeyhpu9dIvNjQxwcjyMsboKLHhYaLRKObCAmoigVutEk6nMcplrLk54jMzRMbGcDY2sMW1q3Hreruu/M5100QfHKS2uYm5uIi5tER9awunUJAEF8OyuLe6GuTXcjm0VCpophNrvd3++tKe7u9ztruL5zhUhYUPqPb2ng9RrYYjbOvb22jJpHzQwJHc450dnGYN72/2+M9Q9YTDDGUyQXHn6IiGbQegrgD6bBVd+vY81EhEJvmERqlEo1LBnJwkKrFqs1ZLjO9w6Rz73casAe5++QrpB3jFg4CNYo5cDN6JfR7rj1/ECnt4bgPVugN7eXaePcXOH7SS1dYPWgz+mYxV6vErjG8/+fXhE66wCeSR4+Mz9GVGpPd81obR1E6Vc24HSqi9/UQePaQsNQ7zH+W4GZe4K93fYhf4f9lvAQYAwe7UEk4oy7oAAAAASUVORK5CYII=",
            ],
            [
                "id" => 38,
                "name" => "Cameroon",
                "isoAlpha2" => "CM",
                "isoAlpha3" => "CMR",
                "isoNumeric" => 120,
                "currency" => [
                    "code" => "XAF",
                    "name" => "Franc",
                    "symbol" => "FCF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBNkJDNTBGNTE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBNkJDNTBGNjE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkE2QkM1MEYzMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkE2QkM1MEY0MTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KAzx3QAAAHJJREFUeNpiZKiKY8ANTneexCNreO49HlkmBpqBUaNHjaaO0X/+Mkpmf5cq/vbnL7FaWIhUp9T4VTT2BwMzA6sQw8NqLmq6+sMxFkb5v4xyfz6dZqayq5nZGJ5k8zL8IyFyiDX6/Ta20cQ3avSQMRogwADcmBpIZYNhTQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 39,
                "name" => "Canada",
                "isoAlpha2" => "CA",
                "isoAlpha3" => "CAN",
                "isoNumeric" => 124,
                "currency" => [
                    "code" => "CAD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFREI0MDA4MjE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFREI0MDA4MzE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVEQjQwMDgwMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVEQjQwMDgxMTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+IgrEzAAAAaZJREFUeNrEVktqwlAUPVr/TRXqB0QMHXbSqXsQdCRuQbsBR3bmJBtw7A4cuwyLBQNCKdgOVBCpIv7aUx8PiVZtNCo9cHm5yb3v3E/eTWwEPrEOlwtot4FIBGfBbAbE40C3u3HbsZQb/APslj3LZUDTrDMvS80NcbnIToc7MZkY18JO2K8wHu/2mU7JSIS/eRxHRTmfA6USMBjI3gnkckAoBBQKgNd7oYwFEomt6Hl3t99+T8Z20zdyhWZTrvn8tt3jo1wbjd2+R2fc65HpNFksksEgqWlkv0/a7Zs+7TZZqZCKQj49kckk+fr6Z8bmpY5GN5+nUqTTaeg2mwxuPRi327TUfxMLp3x+u6dmks1KX8vErRbp8x1PLKReP4H4+5us1cjb28MJPR6yWiW/vk7sscD7O3l/b06qqqSuH3ScDj/Hi4XceB+pqMpodIHJdXUFPDwAfj+gqsYWTifw9gaEw8D19QUm12xGDgaG/vJCPj8b+nAobc4yudYhMgsEDF3XpaygKNLmQDgsf9Y+PszH4kWIYzFgsTiJeGjJM5M56Q/kR4ABAHxxYPgLzAdZAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 40,
                "name" => "Cape Verde",
                "isoAlpha2" => "CV",
                "isoAlpha3" => "CPV",
                "isoNumeric" => 132,
                "currency" => [
                    "code" => "CVE",
                    "name" => "Escudo",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFREI0MDA4NjE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFREI0MDA4NzE3NzMxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVEQjQwMDg0MTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVEQjQwMDg1MTc3MzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+zvajFAAAAj5JREFUeNrsVU1rE1EUPfPmO9OYNEPSplAqmlaoFDcuBBdC9yqCGzddVRA3/gL/g6IbJXahiAtBxY0gLiIIUkEQDNlIija0JUnzMZlMJ3nzZsZpJG1SUhOF4sbHWb1759zDuffd4XDuHo7mEBzZ+U/9b6mJjx0VLRkcjscMXbPhEVgqqLgb+ntqn4MjpGY2db2Opvrk+ourZ3OoRE+d2IjHjCA0OjWP8CLarAsHO0BVvrP8OK7WVzPz7wrHPmanmOWnb66YDZL7NAdi9uT/Dlx48UGPZAYpyVx+Lpa1qLJejTBbFiQmK+3ZeLls6SX7pIQtuBa44fI56vfZ57vPic8qwjURGAdYkNG5LwMhNDT2yBeWAH0UQwQxk9krAzGBrZcAnUiegROw+WI3liRh0DyKz5BIQJmHawxXnTnY1png0sP3QckiQcpH3gcdRTXnv3q9r5qPoXZ/14boLXh1wOupGQItwEgjsgRpFl5zuCHO5Yv9XucJWIM/H7RpDHC7ExrUkVBT3ALIFXDxkVQfmBBfnPQ8kgx9czy5bKmOLfOCK0nOZNi0mGY408QtwWuDG/6MeapcoM12Fw6tNZ1Nend5Jcpvf3hzejy+1jKpXeTSNx4aNfbl/QR1q7RJez45FDxSl6AI+1AJNPyoJVfXU6Y39vb2U14VPucW1mw9W5xuChI0ri//cHAD/jLEgxEG70KzF6a2S2aoWInAViBThFpwR91ogx5VsIzC1q998nWj0zGRQeyMhPsHm/KnAAMAsIUm2hIeMowAAAAASUVORK5CYII=",
            ],
            [
                "id" => 41,
                "name" => "Cayman Islands",
                "isoAlpha2" => "KY",
                "isoAlpha3" => "CYM",
                "isoNumeric" => 136,
                "currency" => [
                    "code" => "KYD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjE0RDFFRjQxNzc0MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RURCNDAwOEExNzczMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNjIxRThDMEVFRkYzODE3N0JCQzk4OEY3MEExNjA3MkQiIHN0UmVmOmRvY3VtZW50SUQ9IjYyMUU4QzBFRUZGMzgxNzdCQkM5ODhGNzBBMTYwNzJEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+wV3Y3AAAAoRJREFUeNpiZGCYyUAbwMRAM8BCnLL/DAysYARk/GFg+AdGlBoNNIKLgUGAiem1nPQjVmbGh8/5/vzh//cX6N3P+D2Nz2hGhn//GQSYWX6XZy0QEn539oLUh/c8dtbffvxk2XPQ5OVrKQaGV3hMZ2Zg8MXtXkEuzs9r5/Z/+vytfZL5k5ca774J7z8kxsfzaUrPivOXZV+9lmdg+AJyA4lGA0OWbcXMyWfPi9Z1RyWlifX3K0WGCenoMi1cynvlKmd1ye6tOzV//+YGhz4JKQQYXfzB3od/ff/TPDHoxGmNqRMDWb6+un7xVXKSz5evzrcfuR0+IFycs42BgRuXq5lwhjPDt79/fta2mYWGS5mbOPz8xnVkw+q/f7+zcogwM+jW14tNmmd8/gowQD6SajQwNP4oyN1VUD6hrMkG5P/69fPvv3///v77/RskLSMnoqV1Q1b8DAPDT7Bi0rIMC9DZHg6Xfny8CLKKi0tUReXf3z9/wGafPfdFW/2ige4TcNL8T5LRQP3sx88Yy8iyPbm3fvu28xxsfy0cvDQ0NXi4WF48e3Lp+GIx0dcHTumBjf5Nagph+vqFj4fv1fPPDzom3n7z4o24hBIvD/+Gdcc9oyd+/7/D1JB388awT595wWGCPbpm4k4kAooyz7/zbnnx8hbDDwEOIRWm/+zfXp1nEH7OzmEl+NnvxVsBBoZPuKKREW/JBzKdgekXq/DJf4KH/r6RYPjLyCR7kemV49/Xbv//A819DVZDptFABZyMDIyMrO8YGDj+/2NhYP78/5cEOHy/kF+GwJzz7T8D8//fQmDj/jH8FQEXTH8JFsjEFKqM4PLkG4z7FcnWAaoKaGg0QIABAIW9+Bm2LAQNAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 42,
                "name" => "Central African Republic",
                "isoAlpha2" => "CF",
                "isoAlpha3" => "CAF",
                "isoNumeric" => 140,
                "currency" => [
                    "code" => "XAF",
                    "name" => "Franc",
                    "symbol" => "FCF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyMTREMUVGNzE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyMTREMUVGODE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjIxNEQxRUY1MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjIxNEQxRUY2MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bmc50gAAAqRJREFUeNrEVktrE1EU/u4jM5OMedJYadBNi2t1Jeqif0FX/QdCURAFQRHE7lz52LiRbtzowr0gqFhXFbQbF4KFQGjEaNsxaZtk5s69nk7SpCjaW1BzhmHOPffx3XPOd07CcGxuBcxMwLAWWj6KfhuMGaxt+EB2AzQHmkMiTh7nG29xv7GIj04uMR0Nm7hWPoE74ycB0i0kS29dJkrMwYXO8lwLF8++huQac0+nAQ6oiJbQ+C9LViYf8khvZDBzZhG3Zl8AKaBay2P+5WnAjfAvRKLZ7oVzjePNUgXvFnw4UmHh/RHgC3k61hqGWtA9u3QRwYYnbOtdBaxvAnHbHtgbyyZx7PiOzh3mOHf3CrTmKE0EZEzB83KDxR03D1l3mQ40g+jZjNKQGcfgUN64XQa2NyYlEJo11jYJAXkpjA62JEIlk81SxCj5EaJ4eJQsZhDMPWDBzetMFqYSmwqWkb96wxRvXzYqII+NsQH+LsvFDN8xFHO757ddcn7ZFaVT+AYD2R8raGQ8yYqUMBTStpHmHCOSkQHLtmrvFOmexerKNCKtGFFokHhGd1damZiyG8Yd2xxrOf1oemflnjtq1N9ml4y4gCnU+rZJeh5+eIx7z56Yymdrh42sBlWxi01/lIYbYr1TJsqVaRT2uih1m2Z3la2sfhVhkLIpp15HKKVL1tds+sTglEdMVgNbTI8nPWT8MkppbgsMGShhDdyJGNrUXMRPRdch21YkEKh9AFfcyJpccTpGTkgW7SIX6dTxYzPuRabicuvOJV8d/2RNLmdSm8bzilhGgXLb68vLpM0crOPSqZoJq9zilD65DrjamlzwCFwaCs3Qr+0wOcIwx4NwXG0LTD83ah9VH/0mIbo/p2ALPMLORW+r/3ek9Z8wE6wfAgwA1dTxt+cD8jYAAAAASUVORK5CYII=",
            ],
            [
                "id" => 43,
                "name" => "Chad",
                "isoAlpha2" => "TD",
                "isoAlpha3" => "TCD",
                "isoNumeric" => 148,
                "currency" => [
                    "code" => "XAF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyMTREMUVGQjE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyMTREMUVGQzE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjIxNEQxRUY5MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjIxNEQxRUZBMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+OGKo1gAAADBJREFUeNpiZJAoZMAN/m/rxyN72ckEjywTA83AqNGjRo8aPWr0qNGjRtPOaIAAAwAKxQQ+Km3kBAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 44,
                "name" => "Chile",
                "isoAlpha2" => "CL",
                "isoAlpha3" => "CHL",
                "isoNumeric" => 152,
                "currency" => [
                    "code" => "CLP",
                    "name" => "Peso",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1NDUwRENDODE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1NDUwRENDOTE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjIxNEQxRUZEMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjIxNEQxRUZFMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KpHXrgAAAXlJREFUeNrsVrtKA0EUPbOv7CZZNSIqQkCxsrHLR9hY2QuC+A12Pr7DT7ATrAQLbUJKC1tZI2rQVZNsspvMjHd3QxLwgcUONjkwu3fuFGfOfQyXoXJaB7BEq4nv8B4hv+DAO9vA7JSFDODSejAGRurQGdDup7sCHXEJRXCNoRlzdDimHR1Ej7eAA5aGZKMAWvIVEhYR4r6Fnc1l7G2tAvVW6hNqVKeKI4GIcewfVXCwuwbT0OCULByf3AJdrlCxTcqeAlxUn2GZGhiF97LWAPfagGMoVByDCPOU0+3DGoSg9MaFpjDHI+I5G9UbH1fnHhFL5MpFYN4B/FARMSukFikLQpK6aCOOdadHRWWSk5m07GFWMiO2pT9qJ33sJLbpHl1EyAkHMgzIUYQIs4kAu14vf4w9Il8g6UIaiZ1xreQvs+muprEiH38ve4o2p456ueuhL9J9JqF+Ze6fmk4rUZFn3sf/gAnxhFgZjMHI4/44+iiYPmKuTwEGAHthZNcUQvnKAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 45,
                "name" => "China",
                "isoAlpha2" => "CN",
                "isoAlpha3" => "CHN",
                "isoNumeric" => 156,
                "currency" => [
                    "code" => "CNY",
                    "name" => "YuanRenminbi",
                    "symbol" => "¥",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1NDUwRENDQzE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1NDUwRENDRDE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU0NTBEQ0NBMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU0NTBEQ0NCMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+63sBgQAAAXhJREFUeNrslL1OwzAQx322k+ZDpaFSVSQqOsCChGBl5wkY4AGYeQF2NkYQAxI7ElM3nqE7TAgJtkKaUtQPkto+zgUxwZCgMvUmW5Z+/t0/OcPDesRmU/x7BZKZIZgB0IIh0wmv7KbR3rtOgJnCaGCYQfYkvA3lbarsUdCW6OAhhKhioRIODuZFkyFDxZxlbfqwsJ9yn43b0mlo1eXxWWB6UD8aIsfkIhCRIYl8aApBRLhy1Xe3NLXRvNbxqZ/eC8siqkAQrEAmFi0qOLhxxZKp7wxp+9oqvbVK7pq9xqmb55OQZL1VhcqmlA9ty0OiDC5c1ExWDQRoezdsEvPa4Qgcllz6RQIhF6dhRm3ZPfdxAsF25jZt/5MO1zHnAfWFDAsF8umS3kpBvsDSO8nLqHu8ejAWIXaOQ16xPdHXzo+eihPu638sW0dKhpfsOXFllJtrhX+bRhoc9QJoQNaMTaNgID8VaYrFKdL8edBn+IbM0XP0P6M/BBgAWIia6c9dDfMAAAAASUVORK5CYII=",
            ],
            [
                "id" => 46,
                "name" => "Christmas Island",
                "isoAlpha2" => "CX",
                "isoAlpha3" => "CXR",
                "isoNumeric" => 162,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTQ1MERDRDExNzc0MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTQ1MERDRDAxNzc0MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRjgxOUM0M0NBNkU4M0Q2MkFEMjJGRURBREU5RUVFRTgiIHN0UmVmOmRvY3VtZW50SUQ9IkY4MTlDNDNDQTZFODNENjJBRDIyRkVEQURFOUVFRUU4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+BJjG7QAABAlJREFUeNq0VV1oW2UY/n5PTk7+mzRp2tmkpm61c1bxp4K0tZWxDRV/cCIqijdejF3JQEEvhzoRRBBBb9YLYV7ILhRxiEJvxqy1VYarFNqxxiZp0qRNcpKcnHO+H7+sk+DdCt3LufjOOe/3PO/3nOd9D3zosxnAMYAS7HcgcMcC2T+P82pQBi0YagAkgIT7BY0jxstsJS3LYUQ4jm8Db1u6GhQI7IWi3EKTg87rY62LK7q61QlQ+uLEwSmEuSxE+fWUlQ82WcM4UMeGLR0N3AYBgh2UnInnC+TLE9YTKfurP/WQBiEEuHfwKEASeh0OuK/R39c4Yq57OWYkUUHqBA4FEmMEHA7zJtqxoEZuFbUbbQY9VL4/0Qx5wceXtbMTjaVt/e8K9lNJbqVIWKzU3nwl88bzD558teEsH/CPFOjhNTqUI4QXCwGXwWdG3KABFrJwvUr6A4CJDr5irbZwri6/eLq+lMdlQaKE5bboQEpVnTp6swSpe2h1pz3/R7bqmEQT2SWK1jNaK1YVthZqff1s/dSUdexQ+74wuJyn6gQBTWFDBe0h8odVOrukjfejR4btR/tsQuHVMkGU4OJWvVQ2g35PsdJcuFp0XaHr8OwH46NjvuJv8ezsU6dj0ccONlwLn5/zfrpgfDTd9FLedBBXhpLAYiDoRTmT/FVEZgVd2dQ1jBgDZLNkvnNqqm3zz8//2hs1dB2XKpZPp2MjfVcW1x3dTOp0NG6d/sn/7e+hYo18OGlNjZp3z2v5bT3oBQ4TMT9I+Hivl1PCjl0ILxY8ES9LBwRxheiJGrbjMsaEkNzlxyeHrmdrL7x1IRYx+mK+jRxeKXvee6k0EfScmQufma6Alra8g9LJ7QHhh1z4fQAIsLQJr5V8Pg09EHeVUkxCeGT6k81SVZmlrzfEGOdcpAdClbpVqdo6xQiBQlk/NGjOzX6nJXIrDSNm+99+9/jFa0bqyUUtvYEhalQCbRcGdODB6pN1zQMPT5xTkin7KhT1VHE0LVejSCNYyE4aJeLGhn+ov3HyueW7ws43l+75ZX4gExRCtdjwP/TeVZoqKIMJ07jZB91ZBEcnzu2uCEZm09mptpKJgHKyEN0kiuVWzVMq+hREOGIl4y31UrpE1A2oOTizgUfXaCoPOwR+IOAuQcd8u/sbLWc4HTkxk7mxUW+3WadP/gshoa7xaE872mP5DCbVnFGXajTDhgjwfJyvpkQ5AnUXJSrQ4wBHNRXq7q+b7aHByGsvPqw0cV1+W7NDEWCOInWs22wl0/p+xvrxcTebRAELBppdQZTcXEilL4IIYyD3OsChlJzImgEow8Pr5P418r9Zo1B3B5IEYq80ym2Iw4jZWRZ7nUvJO/kr6LLuN/S/AgwAs/Hl9f4h+7IAAAAASUVORK5CYII=",
            ],
            [
                "id" => 47,
                "name" => "Cocos Islands",
                "isoAlpha2" => "CC",
                "isoAlpha3" => "CCK",
                "isoNumeric" => 166,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzRDMzYwQ0MxNzc0MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzRDMzYwQ0IxNzc0MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMERGMEFEOEU0MzBGNTQzMTkxQTc2NEY1NDFDOTdFQjkiIHN0UmVmOmRvY3VtZW50SUQ9IjBERjBBRDhFNDMwRjU0MzE5MUE3NjRGNTQxQzk3RUI5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+YY6ZXwAAAxVJREFUeNq0VU1IFVEUPuf+zLz3fE+T/jCy7AczrIgIqQgxEHKVSItwkdQqatEqWrttE1FQ0SbaRLQqMgr6g36wIrAoyFe+Sg01+ns+3xvnzcy9pzvzNFIji/Auhpm593zzne985wxCJ8LcLAZztiJow5sIvjMosPAhy8E1OzR7tAnUBA6BIsDfQpuNHO/YZA0ewoEOfrJFJgSDPMKsUimypdiwJJayBaiZ0CY+y440WBfaQaF98ZXdWiPf7bcRJHjmwB+5u1CXki/2JLcvtsCZvslhK1TO43faWFev3HGJ3XitLnygo5upoOlJn4J4xB0J8gzGGOgoT6MVYil6PMD7Q/Tsi++gCqmYAwInobfA6ip+qI5/U2p1JY1kWRXylrU4WtDX0wSJCCXLGpaLUy1W/SJ72GHZca3dKNrCotJ9w55DGjhflrJsmxc8XVKSgQ3pYfzswJZ17oGNxRMLsLvFW7nCvTKAICDChV1r7CcHhbTw9rsg5xMhq55nL0wICKJKJsOrHOePW8vObEuEyRFFrJtB56g3L9rr0VKs6q0Qzf7lbnnsUQBJBQGLW9arffRygBpP6/6iO+YHDHm6vSwp4VbaM8yAoZFCk84U8e6gP1AMQtEQhakTVsC1Xn/7ebF3Fdo11N3Fzz31wVLAERy9qprBfHX8ph8GlDNQJl994OF4JhdAfKKY6xdbnOOVHhfiGhIIGiNndpZMRibx8DFu3K0hRUbH0LMeS0mZO4wP3lPjWQ2VPsQgFGoMjIPCIpszLu6sKYsJutrnQIwAfpaxCSfcH8MwO04hF5MjRS8FelnVPyoO75TVlexTjgspCh5pi0InlJxpQeZrkP7mY8IwxF/M14RTumtmm9j0/AM9+qjbatnSctn7FUbyAbHJw+bOdKMVZUnTWnXW8VQaA6ORnYyU4ccmUTTEpVVbwTJjKh9Wj/5xPJWUqUAoJ5A4hZ0LtSnRszvZuNCOupGmdmPTXw/VmXJxKgT63pDu+eIVDO5UnuK/5ibHfKBvv3HCyovpWv8fdMlpSYTfzbG5/hXMzfohwAC83jil0Oj5WQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 48,
                "name" => "Colombia",
                "isoAlpha2" => "CO",
                "isoAlpha3" => "COL",
                "isoNumeric" => 170,
                "currency" => [
                    "code" => "COP",
                    "name" => "Peso",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDNEMzNjBDRjE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDNEMzNjBEMDE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkM0QzM2MENEMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM0QzM2MENFMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+jvkEygAAAC9JREFUeNpi/HNRjIE2gImBZmDU6FGjh6PRjAzac0cDZOhH4zlBtdEAGfJGAwQYAIs9A9bKIFNYAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 49,
                "name" => "Comoros",
                "isoAlpha2" => "KM",
                "isoAlpha3" => "COM",
                "isoNumeric" => 174,
                "currency" => [
                    "code" => "KMF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDNEMzNjBEMzE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDNEMzNjBENDE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkM0QzM2MEQxMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM0QzM2MEQyMTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+8TQxCAAAA4RJREFUeNq0lV9sFFUYxX8zs9PdbnfaZbstpVIsLkotFBGqxCiaDSSmEm3kpREQsCGYSJRGI7E+akx4UKImvmn6oImJJlBoomIwRMU2NaCgtWpjpEjb3aXun85udzu7szPeneqbL+1u7507M/dOJuee853vu9Lz72+Z7t+oa2v8BuRcUJBAXCvdXJ9GqpuHE26OhdI8c3sGfEXIig1YrOgG5Lt9hbRhw4kxP90jDXwXqYYaEzxiA/YKMjZtiYBqOeOqrrL/cpADLfP0CQWCdQI8k4f8QsXZu0q3EjFFVmhUUkzpcd4Y0/g8Ws8LoSSH2pqgqhZSESG/WTlgSVCRZZnJ+J/c5m/hld1H8Kgqb34zwLPfG1yYC9AXfpHt9z26+IeZpBIxUO7oaun/Y3bCvXNDmI97z7C5eSsfjg7Qe/9+PHKBc78PM/Trt8zm0nS2PoTHJTwglz8U74NK//r6kHvw6HncLje73n2AkeuXqPXUEdGn0VQX1YrNhfEznP/lNJpbY9OajvJdncwmOR5+2Zm89dVJrk5dE6y3CNnX4q3yki8aDsuO5nuIz//NsU+OsG/gSa5N/VBejAM19Q5QqV35a5RGLUjRKpLNZykUC04y26KbwlirtSYafI1cmrjIyMwoe7v7eGn9QZoLPoouaRnmkmRnYgkAWRJAtu0Al57/fXPcb1lOVtV0tKMbOpOvvs6NKwOkrWoMdYmM4/NxxiNjrFvVyr1rtzH001lEzFkXaGUqddNh6hZdEZUspUnEV8lsHTc4eBbCPzYxS0r0OPISpZbaXmvRBZD22XMXnYVtJzcKU0Xoat9DLB1lLpPEdruYXq0QTFrsG1pg75cGPtNmuk7GVIRRlpFdUvjtHfrErd+0PZu7ea/nAxJCgRODx3ls0xN88fMgX+vDeH217B42OXw6R3vCJCryPuMVIbKWby7p4VOduiRJ2nVRQDY03MlTnYdEbOGjy+8QUyKE9LvoOWeya3SBgtAzGpQXGZZZQ6Sdp7brwmBaqXol5pMkczFqanQC7hDmwtM03nhESJwnFrQoqOWx/N9aXRSOrdMM/LUerMzjWLHDqLk2on7h3iYTyZYqfUiU3JEWb7fAaKOYOoCd7hJa5IWpJqkSDKuylT+YXZJ6U7NFHtqJo1hzPYJ6QKzOOMAsOUmWAGxld8zYqV7NznYuslYn/wVcOdBS+0eAAQAC6FhYtEMFCwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 50,
                "name" => "Cook Islands",
                "isoAlpha2" => "CK",
                "isoAlpha3" => "COK",
                "isoNumeric" => 184,
                "currency" => [
                    "code" => "NZD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MkI3NjBDQzgxNzc1MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RjkyRURDQUUxNzc0MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQUQyMUNBQzdCMUE3RTk1MkU2N0IwNzVGOTlCQTRCQjIiIHN0UmVmOmRvY3VtZW50SUQ9IkFEMjFDQUM3QjFBN0U5NTJFNjdCMDc1Rjk5QkE0QkIyIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+isNl2wAABElJREFUeNq0lWtMHFUUx8+dGZZ5sN1dVpYFpNSwWBQFLJuWEpNaH0hoNDZIVoq08YGmMaaNHzRKiakRtcYq1Ni0poFa8VGigkmh0tY2tdSqRLq0lVJoKY/lsbuzL1h2dnd25nrXB2LiB9uEm8xk7p2c3znnnv89F6G8nbA0g4IlG9T0RqTuLfmo7qG0lZl40In91OEaiyq1fG9Lw5wOz9M8Ba+YRHULE3z9Dp2ex0E5bocQVgErGEMMz4awDPGpisn6P2jzvpntWw9VT/5yoSq54b1HU1ZliRIGhfIgjdbIbzW77Bvohs3WJs5629FEv8OPeJqYYVBBiYFrDmKKJdco6ChwB0CSMfkDf9HpTS/Vz69eK/Os1Td23xNrnq1dr3NOeg81p9RUPVeesyWXSX6qqtucd+zk8C0h16hfUqIYaAp+G7UUZTQ3Vgxcde7dUTrlmn3x+Xt5QXP5iB30AtIwgDHCq0uIfzGEJ0KgeP1pelaal5wqNiqKILABc4bs9KbIs7eu0Cp0kmlunTfGISS98UyxKKvZqcLuz/sETiO6gttqrA5vKDYb+bpn2OsOIZ5j+i157qmgn1UKK1YZfG5enBIRi7/pEspLM4ty0Fm7P8VgT10x7naCoImNMDAbweG5wjVZzR0Xmuq+hex0kKLAaeq2dWSX3d6+5/EvTl+FQBS0AoKSDwvLrK9V5VYa56Lf9TR5kvMthntspUONn409UF5p8Gl6fzh82vH2dX2/qoWf+3jAH7xZ1tJ+/qfOYVhuJOVcqCqEpBSztrai4MvjwyNDInWg5q7zVl/lidaPbW/lN/S/vH9UuThkMjHstOPJB3cVVLd9Mp5sK0qzF3vfD/7IRGJhhvIGJJ7jgaPRgnTJzpJvGSsU3Gkx0JRKhENXr72/a1/nC3b6gMfs8ceAVRgajzrCxxMzf5WQeG64o2uoXW9BmcsTJ0ePXptflmXSJyWcPHs9ihCi6UU6RqChwvOSLxiRItjtDiLQbofUVJAjwGGUwIAiY+Ig2QR+EQkUJCZgVYWpABBhZKSC5Ft5t3l//cM1r3ZOXBGRgV+MxqoCztnOr2rPXBp7Z8cRGnLKgVGQBiEKkbyAJMYxAFFgKURR8UyJkZYFPQtIIeXyXPNM+IO52cZLA05IYOJWC1H7IwXFmZMuX2tbv8xzcR769wGNs/5zkTwxDEHZ9kjBY+ss4PBCKIojMRIPVlRMdDIlbt6Y/7TNOu8IAM3QyLT+/7eFuAMDd6z7cve58daDm2bmgmODbsRS4A0xPPXpQVtPr2PXu6ckHU/iuDE0YRMbci4ULiEvPYnl2cb60oErM3t2btAIFNn4E73jI/ZpMAo3jv6TL2gUOXaqrX8srGKK6rs4mbSM6xsUW3afmQkpKF2HFPyH1m+2X2NSpnAMxADoEiEQBp0WtCwicvp7MDfdjhFpoRoa0pPjE0GIvxdxl/gqWDr07wIMALtx7x8ZU/QIAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 51,
                "name" => "Costa Rica",
                "isoAlpha2" => "CR",
                "isoAlpha3" => "CRI",
                "isoNumeric" => 188,
                "currency" => [
                    "code" => "CRC",
                    "name" => "Colon",
                    "symbol" => "₡",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQjc2MENDQjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQjc2MENDQzE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJCNzYwQ0M5MTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJCNzYwQ0NBMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Gc94EgAAADRJREFUeNpiZJAuZaANYGKgGRiaRjP++fxlNECQAMslOaPRABk1ejTLEB8go+U13YwGCDAAKSoIL49OOfsAAAAASUVORK5CYII=",
            ],
            [
                "id" => 52,
                "name" => "Croatia",
                "isoAlpha2" => "HR",
                "isoAlpha3" => "HRV",
                "isoNumeric" => 191,
                "currency" => [
                    "code" => "HRK",
                    "name" => "Kuna",
                    "symbol" => "kn",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQkE2MUNDNjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQkE2MUNDNzE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJCNzYwQ0QxMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJCNzYwQ0QyMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+JdsChwAAAshJREFUeNrEVc1rE0EU/83sbr5smtgmbalpTQ+2IehFrSCipE1BRD0qtAUVBG+tIK0H/wK9iQieBPEmiqkIiieLbRWjtaVSP2mRSDCxdTdpmmSz2Q93Nz14ywQPfTDL23lv3szvN2/eI5+C7RvYBuHN4WV1NggBryrQKQe+UrHnVKcTVNeg8g4Qw2DemLI4KTyPFklEz+8MFE8TiCCAd7fCKfjRRVW0NjvQbdp86znzAJQZcV0J5EXMHRkC5/eivLgGyRCwfDUG2UUgTywjJ1FcOC/Cly2gb3oBRa/7/xETVYW3xGHq0gmMP5zEIh9CIS1iPpjCVFRBauwHSodFXJ+YxIPJcyBVB7iqUh+xspat6ySb48DdBF6NjOH1yRj6b71Fz7cOeLKtiHwV0XmqD3ShgsjNeyho61BzZj7UA5QaHqnjQ+Asb6Lsa8NTeNAmZhEOtiMwMwNPtYiN3jiWzXvlQiqOShLKWhWqy10/0YwGRDLdC5E9tr4aDhtftpYXjw8ZK42FMoj1YXpKmoaNxwlUf2WwuTAP/2AcxOGA9PwZmvbug9DVBd/ZMyZBhPFtMopWKNTQJd8Zs+YyNZe3/+dMPZ9I2LpeLjMjboifj5Q3Vgfjtv79YL/xuaPT1lPDo8aShUHXmWPx7EVOh3cgBq57N4rJJFzRKLRQyNa5QADNx2Ko5TI71TLjsHhUjMWk8fKfG5q2aH/yyLBtNR+meGT44v0sG15e85TT7hv7qT94qBelF7PQZBne00OoLK/g8lwpn3P1lCipcizxTF5GGSu7RaGEyK4w7ry/je54FEinIa7mMT5wDW+WPpj2IOqXjq1otOUKe0vhBOh/SiZ8GR1tTgiU4GfG6lICaNBsclqF+Y75hpqoWZXoTgf0CkFGdtfe7A4C6hJMm8KeWA1vXMtGUIe1rLrFAm/PNSoU2yTW0QvbsfFfAQYAfYmMzgM7drcAAAAASUVORK5CYII=",
            ],
            [
                "id" => 53,
                "name" => "Cuba",
                "isoAlpha2" => "CU",
                "isoAlpha3" => "CUB",
                "isoNumeric" => 192,
                "currency" => [
                    "code" => "CUP",
                    "name" => "Peso",
                    "symbol" => "₱",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQkE2MUNDQTE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQkE2MUNDQjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFCQTYxQ0M4MTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFCQTYxQ0M5MTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+9/ZNdQAAAztJREFUeNq8ll9oHFUUxn+zM7OT3c3spMlus8kS3RL/RU1JokglJrRaFH3pg2+C+CAICq0PRqigVYrStFhEfBEVXwQRn0RQEESsT2KqjUhrpaR/NFmbpNlsdjO7m/l3PZsWRXxK2PXCMHDnzj33nO/7zne19/v3LxxL77MvWrsgrEK0DsRo99Dcrj61FOvgw877OG4/gGf0QLACymvrAbSZzHClO6rbhXCVH8w8b6cn+CQ1JjHjsPon+EF7Ap/J3FVRYEeS3UBYwYlqfGHdxpv2OBdGHyTyqgSLy6hItTSwsRldHp2Ied2mqHfyUGOOkbVzVO+tkp9+EWdoF76sCV2vtRnL2/5nRiOS7DpUgFG6gjVQIDf1HNahgy3N+D/s0bQYqlymUfdp9N3J8kKF088f4peh+yl9+lmLM9Y0G6WI6g18d5HuRw/gC67rP/2I6WSIWQkaSxdR1Ol55AD51w5zaeQeUrJBd7ghUGnbDBxFNrqOmeuldu5ndp+fYfXzr/jj6Ksk+4fwrhaJmZYQLSAoz+HoXXw9OMEJZx9XzF46RH6m8rckv+usDiM7qJXJPvkEuReepeOOQZGxx9o3p5h/6Q0av81hpJwb4Ogo1yXrXmNZS/BRapS3RIIQv6H9LWCsGTpGMk3xg5O4M2eunygeZ/HYu6zPzkjQrn/9pGLCAymvoxqkow2Z2KacmvhqcgYzuRMVBBSPniQ5Oow1eBPad80lsrOQTiDBX7qAQ5Jvb53geHovZ60BKfWqlLqZbWLrclK+j5HZiZF1KH//JVaiQHJsmNrZ8+jxBN7SvGi5xI49+7n59Zf5fXxy89RZIVds2+Rq6jimoaQ9hmtVjJ4dRG5NMmyCoVGvzZHK303+yBTZZ55qUwOREUlZLRViXruMmcnSe/BpkkcOyxeztS3zb9JIyZo8KXgl1hsuK489Tv8rUxh7douCxbRq/iYfWmoSoSCVEy/uFU8+FS8w3TnO7MjDssLDL15FJNdadzotttipNuxbghK/GhneEVd6T7wZXfpSWWzR89tji2td/aqu6dIIxpi2J1mJ90lNy1LSensvAh/nJhdOpPfas4nbhVXC5LDyv1x9/hJgAIwQQwS2sEaJAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 54,
                "name" => "Cyprus",
                "isoAlpha2" => "CY",
                "isoAlpha3" => "CYP",
                "isoNumeric" => 196,
                "currency" => [
                    "code" => "CYP",
                    "name" => "Pound",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQkE2MUNDRTE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQkE2MUNDRjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFCQTYxQ0NDMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFCQTYxQ0NEMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+efiilAAAAf5JREFUeNrslU1rE1EUhp+ZZNJJMGm0Wj9IpVWsSsGvpQa6qRR/gBsRN+IPcONG0JXgtgsFFQS17lwU/AALoqApQQwUxVJbBTW2jK10Mp0kk5kkc71pcCFS00JiNp7N5R7uPe9733PuOYoQYpk2mCKBRTuAVdpkLQP27XmEa/87YOEsUXx6nvLsI7nxmwi8WjBRwUldxx49gRII0XHoLIreuWqY4PrLUaX08grVnEMw0Sepa1TnMpQyD1AjcTacvEFwZ7K5VV3JvkHdmMAa6cN7V0LtrkWQj83LRYct1zzp0Jr0naoe1cX3uJk7FJ6MENgekgy8epL8erJEQboMiAwNET31GKTUjayx1GoQ5/ll8mMP0fprIB5Kx6+8QnlGAg5LwNPn8IvGXwtq3VJXshOYV48hypJHpK6mKMrLkra2Z5DomXuosZ7WdC7xZRxl4jZFO4uX/0Y4eQl18160XcnWtkz5WGpNvWtFgpxMUvyPMwu2RUyPoGta8zqXYf7gwuhNbqVTzPu/gxqWyd30Cy6O3cdYNtcUb83/eFsszpFED+mZSUxrgQOJXoYHDjM+Ncnrz7N8t3Irvh2dm5o/nWpHn02/JfVpmt6urQz2D/Dq4xQfjDmO7t7H8f0HCQYCrRuLX5cWCWshonqYgutiu44k0v1/HjcsLrsdwD8FGACqhNA6kzA7CgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 55,
                "name" => "Czech Republic",
                "isoAlpha2" => "CZ",
                "isoAlpha3" => "CZE",
                "isoNumeric" => 203,
                "currency" => [
                    "code" => "CZK",
                    "name" => "Koruna",
                    "symbol" => "Kč",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFNzhFNzJCMTE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFNzhFNzJCMjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFCQTYxQ0QwMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkU3OEU3MkIwMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+jrdRWwAAAjdJREFUeNq8lk1o02AYx/95k2aStKDr1s1VaPUgKKs7iuBFbSfDqzsJwtSriFdBp3VszsNwoHgR3MmL280Vdd51qOjBdrCt9aCttVK3Np1NszbxSVhxDFf2kfQJCSFf//f3/p4Xwl0YfJZ+cqO/a48oKGhOeWjPAIErRV/4pvH6/YLRxCqy1qMHkPu5jN7zoxgYmURFqzYFmxlVHV6/F/LBDkw8isHfdwczHxadDzYPek1Hi8ij9VgQ+V8F9Pbfx6WRKagO0rP6iWHQXq2hzaQP+vD04QsEzkXxxiF6tvFCrU7fE7TcR0z3w5PQVmvOBv+jX3N/iNw/jqGr7zao850Nrpfl3kX03QHkcwWcJfeX702hsrp799y+yK3i2qJuPEKeQVU1rMynwfUcwcToAC6GfEBFtWZom6UIW36UGo+JInDiOIxUEomTp1Ho0JDnRNS2Hwxha4tdx5LogS7KiKQ/Yjwzg8Plb5hbMF/naLM5mNEcaoyHIrVDqhRwN/Ec17+/xQovYm6vHzwNiNuhY6EhZQtRumScyX7Gg+RLdJey+Cp58YcJEOj+bkr4PyWDInfCoy4hOj+Nq5lZlJkLcXenNSB+B93UMNj86G9yaYgSwtlPGEu+Qqj0gyjbiNJlTa1dJWyklNRlDMXJZfodSuTyi3u/FWhnqBW8vmPD5HKMXIbIZYpclm1wuWlwXm6Hu1JENDGNa+lZa0rtdLlp8KlcXBlfjHnIpZIilyYl7xDlul8f5a8AAwAi6Fw0Cqzr+gAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 56,
                "name" => "Democratic Republic of the Congo",
                "isoAlpha2" => "CD",
                "isoAlpha3" => "COD",
                "isoNumeric" => 180,
                "currency" => [
                    "code" => "CDF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGOTJFRENBQTE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGOTJFRENBQjE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY5MkVEQ0E4MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY5MkVEQ0E5MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ljpEIwAABUxJREFUeNqslWtsk2UYhq/va7v19K3dBmOUwYBxxoBDFw4CQZQs/NCFhBAMKIQgYNgYEASNogElQnQ4YEDEAyD8UCQahYgZBgVEnMiQgbDJOIxtDBiuo13Xc+vTdiZDxRDwTd6kP94+93O4r+fTs+tiI+CQ60aJQDAVVD/oPBDVcf8nCopcXyYErHR/qJItw9dScKJcu/yW9ZpeXmgdDzWCaUy2H6Y20I0Lvt6SgO8+BaWAQFfw2rENOM8buVsoqt0Oiz1U7uiNN2rQVJQghK3QliMP01jVZzUTrFXQIk3wOxKBiNybphKGkNTR2p+krm5en/oKN7qNp2hNGZceN3Fk+wCiRhVzSgg9wXTGpR5hSu/vuR6yk2c9w62uX2E1tGBSvay9PotI2JRo/38Jhi2SvCSa4aQwfx1vejdj21xPw0Y7l1oHYEoOkWIL4r2twxgIoLDjpis9uUHbm7OCCRkHCftS0Rmc8UCza9ex8+Y0EfVK8NBdBCUpTxbY25k+5mPWUkb257/RvMFCdZ0DoyGCwRyJCyrSuWGPXsW8BLfC7hoXgW4aXhtHR41nbNZRmQ/kV5RT3jRJAtbGFDruX4LS+ohBBLPBHCJ/1B5KrBsZeqAC93oDZ85lo6pRjFoEv1slHFEYOrAeW7Gf6oI8lrctduuJir/k5mZ+zSPmahacKmF+xh5mOT6k3DlRVHQdc6Zj1qq0tC/oVfIe28+mzHcZ+d0hfG+rVPzSV17IDO1hgh6Vttt6BjiayChuo2n6QIooZNeR2eC0Shm7LroIpWpPpB9AlQQO1k8jRWvi6YxP+aJlEp5wilQYSOh6paWRZAaOOMymnBImHd9HdD1UHsrGTxJmW4iQT6Hdr6ePvZmei5y4ZvZgpbmQjRVz4XoXsDRJ0m3uhLAS0tRQCtGwRjS5QZyZKuMzQ3Kj1KpLsBi04hh6knVDyph5djeUhajam4ULExYtJAYUwXY9DpOTnDnNBOamU9JjDqt+Xoi/LjaSWxDzTmI3dAjHWVY6tbQD/kCXOIvW/jWsyd3MossfwVYP1TszaY6kYLEE43/ztBlIl/0zZKZUU2hmW/9nWXGqmNaawWBslQKaJaTa2SedhTs5NWiXaF3RZzeycmQZL938gKT3b3FpWxcahHWLKYSqj+IRi2j4GF5QD8Uqe4Y9w9LqYhqr8iBJNp9REon+zZj/EI6jEVsk3aHbHxSP2cZrnvdI21lH4xZhsSUjzqLOGBU09CQTZMTEKyiL4ZvRBSytW8T5ypgZxQ+m+o7uqXcjX1KOCUaM8lOcavMybfI2SvSlZO05z60SCz/UyxbSR9DsQXytOkL+CLkPX8S0LEJl/jgW3VjCsS+niC9EyHI50bE72/rvO4etUfFHWMsf/Qmltg0M2ncCV0kSZ6t7oRMWk1OERZdKKMZi/3rsy/xceCqX4ralHDg+HcRQWOqkuGCH4D0dtzK6ar/rncxSbczBbwmUKvx6ojdhcbLJFibQruAP6hjY4xoZsuAbpg7iZbWQ3T8+J7tcbGEVAnTe+/mKuZXIPpMrstGrnT6YjTfGoizwcEDQ8AmLacLiQifOGb1YnTKf0p+ehyb56liuC4vuB/lsupUqY5bL7TNppjiLYuZ2A44kJ/3mNROen8razHmsqlhI8EpPEbyDxQc5buWktU/c1TEWuwiLg2cJAgtMbO03m1dPL6SlZqi4VFhMar4n09yrsN7fphPTRxlb8DtKkbCYO4MXq4u4+tlIqa5dnF6bYPHBq7zT1TV5PRtzlt92HHtytPuFi8s5d3JiYjfHWeT/qrDziS2ra38KMABPHyYU69kw6AAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 57,
                "name" => "Denmark",
                "isoAlpha2" => "DK",
                "isoAlpha3" => "DNK",
                "isoNumeric" => 208,
                "currency" => [
                    "code" => "DKK",
                    "name" => "Krone",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFNzhFNzJCNTE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFNzhFNzJCNjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkU3OEU3MkIzMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkU3OEU3MkI0MTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+YilnMAAAAFRJREFUeNpivMBj/J+BSKD/+cxnEH2R14SXgULAQqJ6ii2EASaGAQKjFo9aPPwsZvwPBCPKxyzA4o9oxcAikwFaZI4mrlGLRy0euhZ/hmKKAUCAAQC4DxA6JMoNqgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 58,
                "name" => "Djibouti",
                "isoAlpha2" => "DJ",
                "isoAlpha3" => "DJI",
                "isoNumeric" => 262,
                "currency" => [
                    "code" => "DJF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFNzhFNzJCOTE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFNzhFNzJCQTE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkU3OEU3MkI3MTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkU3OEU3MkI4MTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+LIWFKAAAAkdJREFUeNq0ls9PE0EYhp/dbtllbUtLREmtCSYmvfAncdeoJy8aDImejB6MHDxhTJADGBIDJCaSeICEeBAL2ipGEBCbVqgabBf6g3Z3mMXUgyi04L7JHibZmed7v3lnMsrSj0rmUSIfncuWrA5TI9KqYgu8VFB+WUUIUXAHU6tFhpN50vka0aCG6Ve8LMD6DXZHlZrg6YLF2AeLUtUhFvLjU8ERHoPrym3VGHyTZ1p2wfSrnA74EBIuvAbXlVyvMCQLSG2UORXQCBs+7P9j/2BwXZNL2zxJFdiQnTgT0tA15bjtbwzsqlgVjCQLPFu09qBuABWFvS3wFFxXplBlcD7PzFqJNkPlpHmk/f8D7K7g2piehs5OiMf/OXM2U+KxLGB1s0ZXKIzh0+Txc47heHwcenqguxtGRyEWO3CFhTT0z2VF2SkKQ9MagcoDSn7/n7oOW1uws3Mo1FVCGWC0+kDYjiJMoTfqWOx3vLIik1SElpZfYNP868znX6e48voGy7mX6KEOWjRDhs7xLlyft9NclMDJT0PgDxAJnkOV2RDNxatxcNmucD11h/7UPahaBNvi6KrWjMvmwQPLI1ybv8nmz48YoS4C/pAE2t5dIDPfZrmU6CWVfoFqdhAxozIWdrNtbRycLeW4PNfH2OJDGX6dcPA88pY8alsPB9ekm1vv73P77V3sco5AOE6rBNvHa+vB4OEvE1xN9LH+PSmPx1mCejuOU/PuITC/+S5zYbY3+mptwlKMdiInYiiypQLPnh97T59dAQYAfWNBGb6d818AAAAASUVORK5CYII=",
            ],
            [
                "id" => 59,
                "name" => "Dominica",
                "isoAlpha2" => "DM",
                "isoAlpha3" => "DMA",
                "isoNumeric" => 212,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQUE5QkY3MDE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQUE5QkY3MTE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQTlCRjZFMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJBQTlCRjZGMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+yQnKJAAAAtZJREFUeNrElUtIVGEUx3/3znXGmbIZUxAdH4mJaWkUYdCDoCijFj2IgjYt2gQVREURWVCWPSAoqE27iIqKatE7aBEmUYFEadlz0pSkdLRGZ5xx5n6daVp774DkgbO49xzO//v/v/Odo3FuyW/sWngiB6vaODTnEwUV0GdWEf3Vxtn3t9l+bQfklNouZYhnMQ6mM06WPrASz4CQBjEt9UtT6QMbtjPNJKhOqHgEsqEwmrzjFGKoPE/iI6kcfSyBkwVd4vkxqi9LSzwrpXGCVxhLYHk9lQscUJEPQ5ITsQeucXK1siXvlBH2XQtS+0hREvIw6B/CzBrE1d5Lt5HJ57pS9m7yQIczlW/F+GjNa2vQEvBe9bD1RhaXppj05/WQHS3H1+4lkP+KaKaTPXdjTKzqY2Cz0A78pTQ6Y/Xd4nzJy8gVX1nE8dYy9nRG0B1h1q+voK9rOo+bmiQYpKHEywF/JzwOpOSOWjBODoJRCcv1uaXIxXg2NX5F0IgxKZ7L1DfVXF/TzOvWCNMGDOrCCd4/m8za7ABBUVyLWwD3mdNGz0iAU9fxxHTyeoeJa7qoOMDSd5kcW5XHhdpvnHqYhanHGZHkfn0GwWjcUmqUXdtwWNX7fWqYeUpRpe7MnK24OF/tXDxLvueqI8U5Kla3y3Y54/znB9Yt6NZx1Zo0XPVyT+Tuz3CyzB3gVrODl/3F3CvS2d85gSubDH73PEIbMm08p4Yy6+Z3im6FOZy+4KboaYTcsIdEgcmwyOvv+sJHRwY9iyrZtln2TfdPaSxlA/jcYmVrgLgF3Ovi0pMuNjaFufnTJ08iwZrChdxf5GTFrGYBlK4alHIOW7Naw9J1LfVEOlw83/ILXn6iMdjC7l75+eIMLSfWwZfu1ORy2KgnbqS1TpSJ76tsiCJ46zaJmKnZ6PvwQ2JGWivHSHutJA88LGCiaOLfJSntf6zFMbIk49B4AP8RYABEcEyuCxX37QAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 60,
                "name" => "Dominican Republic",
                "isoAlpha2" => "DO",
                "isoAlpha3" => "DOM",
                "isoNumeric" => 214,
                "currency" => [
                    "code" => "DOP",
                    "name" => "Peso",
                    "symbol" => "RD$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQUE5QkY3NDE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQUE5QkY3NTE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQTlCRjcyMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJBQTlCRjczMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bJEP0QAAAXtJREFUeNrslM8rw2Ecx1/f72ZjG7MNw3yxidlhuRE5qFH8D1yknJUiBwc5OTiSUi6O7i4yNxxGEYWy/GhfTZvJj8nM15oc5PJMmcve9dSnp3fP63me9/N5JPzDGvCAiMIq/YN9rC+OfZu+HJ4iurKEsdyDoEr1XwV5lsw/qQDOm/TcxMXdj7e83302QPIlSfJdw24ykX6655U4csIsDm7p8AmbL2+cNPqa2NkOcXB1jYU0VR43fo8Xu7MTWakRXkvSMsrlimKJOIGxGVprXUw83zF3dMjI8jyB+ua/zfj0LUa1IjEQTxKVKth1QSiy/4uMc9SbycqFekzViZk1o5WwdojX2fD34LZUCaNKO6pRxa+TGaeX7uLGnMHSWc+QcMapSBRLoAtlYZonLQ2ZYZYNTM6usry5h9tpEz/x/daGsPkFFb3bka3Nki6zbV22Pg9HiAWDxGyKONhQ7hI2a4kURRbHzxdqLwNTNdRVFr7MAvhHHz/kmVv6IcAA7rhthUQGtAoAAAAASUVORK5CYII=",
            ],
            [
                "id" => 61,
                "name" => "East Timor",
                "isoAlpha2" => "TL",
                "isoAlpha3" => "TLS",
                "isoNumeric" => 626,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQkUyNkYxQzE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQkUyNkYxRDE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVBQjJDNzU3MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVBQjJDNzU4MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+yD9vMAAAA2FJREFUeNq0ll9oHFUUxn/zdxN3N4mUdZNsInWNoVJwK0WFUhCLICKoNaQvkupTqXlJyUNqwbeANKBUQa2CGkNhV1daQnxIkYpYiQ/ii4hRUomVdmPTYNoku5n9N3M9k81Gq9Gk+XPhMpdz7/Cd73zfPTPa87vCmQ/2RMOBHzV+Gs9j3a2jWYDHtg5NptqzL0DyYBP3z9Ty6zuLOHkPu3V7E/CB52WGjZhG6tVmOu9tIHu6wNV0AaVpWBLfjgR0Tass3Izi0AsZXjo7Rehtm11f1FH3mE75d0XxskK5/uktZCzA80oR/nsw0R7g03Ot3Lc7SPGMw/RQkdw3HkpY21IBzM1XYFXgqgYfnmzixeN3wUKZ3Md5bpxzWfiygmg1b06CFWB/oWTxz3H0uQZOv9YM9wjNyTzZC54kUCZ7URCl/Bv1gCF4J+QZ0HWdnp4eHEdKOz1NIpFgR6SR0a9+Y/izOfbtrCH6YBC7TVH/uM4du3U8B/I/eLg3RP6QJOBLoNZprurC8zxM02RsbIyBgQFGRkYkWFra+36ySOLZSc68MSNv2FA2CAp4y7s2rW/ZhB41KE8pSmJQzVjWab3A8Xic9vZ2gsEgfX19jI6OMj4+fsvhwyemOPDEL1KRMtwUhOuK4JMGre/bxIcDNDwjCcwt34C1gH1ZI5EIvb29dHR0rGx0dXXR3d2NYRi3vPDHrIurllP2dZUcqBHN7tTQA+vX2r/G86ZlLbm6VCrhui7pdJrBwUFisRjJZIpCIb90+NTLUY4di8Cc7yqZtobztZjtbJmFz108ScJu0So6r6G1WQWsjv7+fgFLMjExsRLbGTFF3xb2H6yHXFEYeuS/Vcx+UmL+vItyKu42a26TsSQX/q8DnQfCDJ2KUfuAmCpTwLm4zPCClDxXaSha4Pavk/l/FXn9eJTek1G5r8IwtcjssDSQ8z6gEkAdS7b8braRJmKuFmxrskimW3hofz3e8CLXhkrSsQQwW9HQatSWANUm2ua/gA911ZF6M4aeMbjWMcdNX8NipUVWGaot+FL9BSy+eW+gkSN7d1B4pcjlj3K4hQrDqoZqCz+NPnA4vtci1dnEwzMhrjyVZeG6/AjIn4hVs3EN13T1022hzNAj0XDDJYOfv3MwxDRG7dayW238KcAADyNSlrek0dQAAAAASUVORK5CYII=",
            ],
            [
                "id" => 62,
                "name" => "Ecuador",
                "isoAlpha2" => "EC",
                "isoAlpha3" => "ECU",
                "isoNumeric" => 218,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQUE5QkY3ODE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QkM4MTQwQzE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQTlCRjc2MTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJBQTlCRjc3MTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oj8tnwAAAr1JREFUeNrUlU9IFGEYxn/fzOy66667urrrCusfyOgSWoc0KihLOllEXQsiiIgOdTG6dK1OdalTBxEpI+pi9IfoYBjZIaICpTAzJF1bXbdVd51d50+fa+dmRWHpY2aYj2+G533e93neVxifIguUYGnyrigFsEKJlraur21Q3fLhMkFX5VYgPPLdUDBzArndZMb2X9CIBR6JsxwAv4WotLB0nzyQV9Rew7U3EViReVEDWZ49Ndh/KkbkYB1PXtbS06ehtijsOB6iv99AqciiuFaRnakLqep/xiiEXSChqyEGXsUQ+TGGRxKk9ZPUhBYQ+kN2NjeQczVy4nASnzqLsOQ/trJRxjK9wmQq2UFTSKN5e5zWPStYK3GOtOe5cdWLHpqkOawxs9iJbSmSr+XM2B4LOFfFb6D/CPFx3OLaA6+MpYuGA9W4ZHF/vR/n7WQvPecjdLTKFNekYVl1VvXygs+5xrrANH4TqA3hmt5G+74Y0WPnuPl8Hl/mBZ3lcaL1X9GzM4hUECtfhJ2arlwsQtVu1KUMvZcesaXKJpEYwj8ZJpZapFx8ptHv5fucn0O3L2O6gjLSnDNwYipShK7dMG3ijSdpbGtl4p6XvsoBaqvc1A8ZZI66iWTSxEfrISz9JooAJpApAliHXBXj71a40D3M45k25r99IRCupHrrCme6Jhh5Iz1XUQbB1dZvOIuLvXedxSWbU1m1j2B6lLP+16R2ecgOZqVtBN4uP3UfdO783M1idQuZZGbNxg5WFp62W47AwrSkZ20WTB/p8Uqo0+mevo9mG1yPnZZl8OJrmiNaniE+q2Apzi4VgzUtdlEdUzYFS9pH1WyMvErQWCqcpTU/mtvANiWYbcqGIwpNx4my1phPrW+sGGuhLLs8hYDCZkJKQBSiK8AVOSi0lOrd0HjLqeX/3zxeLAXwHwEGAOXk99HjIQVFAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 63,
                "name" => "Egypt",
                "isoAlpha2" => "EG",
                "isoAlpha3" => "EGY",
                "isoNumeric" => 818,
                "currency" => [
                    "code" => "EGP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0QkM4MTQwRjE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QkM4MTQxMDE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRCQzgxNDBEMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRCQzgxNDBFMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+4WJtkQAAAL5JREFUeNpiPCeo9p9hAAATwwCBUYtHLaYZYPz///9HIM0HxCRmqw9AzALEPCTbCcSfWKAMBiQaP/j1iIHhxwkGBg5NiFt/3GRgYDcEYhWSLGchOYz+/WD4eyucgVEsg4GBmZvh//NeBiati0S6GgFItvj/v+9AXXwMjBwaQHczMfxn5gUK/iTZ/SRbzPjvEwOzZBGQ8Qsc1MzSpcBQ+EivxPUDiK9BtegAMTvJiYuR9NQ8WoCMWjxqMXYAEGAAUBIvbfhWcKoAAAAASUVORK5CYII=",
            ],
            [
                "id" => 64,
                "name" => "El Salvador",
                "isoAlpha2" => "SV",
                "isoAlpha3" => "SLV",
                "isoNumeric" => 222,
                "currency" => [
                    "code" => "SVC",
                    "name" => "Colone",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0QkM4MTQxMzE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QkM4MTQxNDE3NzYxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRCQzgxNDExMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRCQzgxNDEyMTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/anEfAAAALNJREFUeNpiZFCYz0AbwMRAMzBqNP2MZvz//z+NjGYhWuU3Bob3YPV8DAycVHT1j2ePTlx/LG8ox3Tt6TdFydfS8voMDPxUCOt/f59VzLxy+ehCIbbpR46eqph2+O+fO9QJkP8MrB+/Xnom8H7FZd4bP39/+gkMHGHqpBBmZvHsENlzL0RW7bJ5/uZVdog8M4sMFVPInwfPNp299N1Ij1dRypOBgXUgEx/jaMk3ajReABBgAAmeREpJl5cdAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 65,
                "name" => "Equatorial Guinea",
                "isoAlpha2" => "GQ",
                "isoAlpha3" => "GNQ",
                "isoNumeric" => 226,
                "currency" => [
                    "code" => "XAF",
                    "name" => "Franc",
                    "symbol" => "FCF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFRkMzOEFFNDE3NzgxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFRkMzOEFFNTE3NzgxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRCQzgxNDE1MTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRCQzgxNDE2MTc3NjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Tw5JAwAAAphJREFUeNq8lktPE1EUx/935jKlb0BCCYagEGJCdKE7ExMXfgE/gHFlaFwoK9mwUcQmbFyYmlA3xrgw0W9AQkI0xhijRNGqUay1D7AIQkceHTpzPXdaISMPKdje5Exm7pw7v/M/95yZYR2RcOZoe6xtuQC9aAKModrDT5blycwteYJj7TH/UgGoFVyB9hXJdBSTqTC8LoCrgBBVB0OBmgdcXwh+G5Pp2sEVCKLY8CkkU1J5b03g3D6uw0vKFQXoOXgHn2eBJYOclL9WUQ0oZDIuOzixV7ADnkAicZNgbtF3+onI6QewsGxCtUkW+bjBeT2stax9zbQ2WMUihPmLAlJ3l2XA4o4pG75I8BV8+jDAxMlGXDvjdIEoQCy+BuPNFLYPMCgAbzet81YimCmbpiTcRT3lSeNSdIJFxuZYKbllYy42/0NjV0cfsLMP+9irqRQjqNNnZ9sGLAdlFG7yaVEwEI2j99E3x+281oRnb8fx9N0o5uo8+yiubXeD4KbASsFyTHsCAVxuPQ9f8QKOd5yCYRrQVK3CdtpyB8gMgs0YuHLxCO6fO7R+KzOdRTz+EaveNWS1WUzE32BmOvcfFMvKNWmfeSv6+1vE8AlnpTaFWhGq96Cxuwsq56ij3mv2VVZYksI3QTntWX0bwu+jYvjuS8u414iFpaLdTpZlQXNp6AkE8T2Xox4WCIVC0PN5zBYK1P/KbsCqs50kVBZKsBPhFxGMjA2y+TzUuS3SIue0MiNhbZRqBUPlG0rdBO1C7/MhjIxfR97vh94cgFuYOz5B2/Mr8096GzoJegMxCfX58dMVgPoP6P4+ElJpg1QaIeggKfVVHVoCBw+XlRLUR1AtWHWonWpSqsceD/kpvXotlJZ/ffTfAgwAojL4tK6cs5UAAAAASUVORK5CYII=",
            ],
            [
                "id" => 66,
                "name" => "Eritrea",
                "isoAlpha2" => "ER",
                "isoAlpha3" => "ERI",
                "isoNumeric" => 232,
                "currency" => [
                    "code" => "ERN",
                    "name" => "Nakfa",
                    "symbol" => "Nfk",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFRkMzOEFFODE3NzgxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFRkMzOEFFOTE3NzgxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVGQzM4QUU2MTc3ODExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVGQzM4QUU3MTc3ODExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Jln83QAABGBJREFUeNqcVstvG0Uc/mb26XWcOHHjhDSv5l2prcojFKkHkHpCnLhwiTgjxAWJG/wBiBtcKnFBHAD1wAFRKAcEQhyQEDRNLYU0TkAN0KaJm4cdr73enZ3lN7tpsfOGlVZ+jX/fN99jbDZ3evre99NhZm4ixK3xEIuDEn5nBHAAOwxtFQYrwJEXa34R4dhLrWcCz0VajBJhS5e4Myxxc5JITIWYJTKFUQmZjZKBVYYU3TYRYeothv91qVFsXb9UoccMk4AjGZwm/hV689akxG0i8CsRmVOKDJAiuSihvUsk1UheRv8B/DHwvg9oih1ytMVqJDw36V7oF7h9kYgoa4jUnYEQXj56xBRONbGGRXs9OA6YFkcBQ0iARlbAfCaALHPIFY72oRD6LxaSAEjscImFIYmfLhCJieRuyQiRSB+SkVZgApUEyo0IxrMC2fcrCIo6vM9t6JMC9owH/1sT229noHlAir6SborXo4zMUkZmVUbImsJYa0ZiRfy9wORzKGlnr7uwX2rAvW7D/8qE86qH2ic2WFeEttdcMM7gfpiCN2dC02SMe1xGbk7ttmY3Iy3AoeCwzvvovFFG7V0Hm1fb4Qw3CLgO8/kAGy9mY0V6ChsQSxq2ZjogPVJI3x8r5XEqJKmbiGwTyfkRCVVdvrdh5nkBSUMrV9MweYCua2XotFABaX30CB31j1LQL5Fx0eE5VlWrEaESyb9O9wblwSQil5d0vPG11QQsk+QaTxFwUaNnDBHJvvMeidcWwf/RQLhF4BQ7/2cduKfBeFLE605yYNAouE1EeHOrGUs2oU0H0BjJ4UgEBR3hXxp0IqT0U0CsgxY5EbReeXRnjiDCW046HkE+4GCUQuURp+FdyyWEqxyNGxaMESW1BkvJPErPF7WTHxu0zBRkndhCt1j5F5ipGeRB/RvqKQUmS96KhxSgl3Mwzgp0fFyOq9V2sQ7rzRoaHzgIlg1wLg/FMoRATmwS0F10h3/ChIel7hF8+fQre+pErEI6rVIv+Oj4bhPBNQtrM93IXHbBqQaRy9D+xTbCH0yUrnQlkhky2bTaURjQSbdDmlTj0FA/sNwzisWhCarRBBaGp7DSO4zV3BMHnFw0R0iSc8KH81YdvEzDzwmE8zoiEkPMa6h/moKscti0gwwB8RiIUowcVvKDWO4fw29nzmJpYJxOskmsdfYg0HSkvRoytR3Yvnf4Wa06LYl76lwdzjs1+J9ZaFw3YyAL2zFDF3nc7R3EUv84Fs5MoUi7+v30CNazeXimDcerI1vdjoFO/COReKSkq1Jz3VhLGoOV7kEUSboi7aYwegF/9I2glD0F37CQrruPd8QjeWTV9JbUHeDRYn48BlocbPXI102SzkW7W0Hfw1VwSUCMNY07uma6I2qZNErx0hpOoZgf2+fRA/JIaAZJpzxSQPepbq01kjEoO+bvSRPwWmf+fmH0SqbZo5LyyLDpB75GHpUxsP73PqCD50cH/904YN0/AgwA07gejJviOMwAAAAASUVORK5CYII=",
            ],
            [
                "id" => 67,
                "name" => "Estonia",
                "isoAlpha2" => "EE",
                "isoAlpha3" => "EST",
                "isoNumeric" => 233,
                "currency" => [
                    "code" => "EEK",
                    "name" => "Kroon",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFRkMzOEFFQzE3NzgxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFRkMzOEFFRDE3NzgxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVGQzM4QUVBMTc3ODExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVGQzM4QUVCMTc3ODExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+r3fpugAAACxJREFUeNpitCo9wkAbwMRAMzBqNP2MHgWjYMAA4////0dz46jRA2I0QIABABDSBIkzwazwAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 68,
                "name" => "Ethiopia",
                "isoAlpha2" => "ET",
                "isoAlpha3" => "ETH",
                "isoNumeric" => 231,
                "currency" => [
                    "code" => "ETB",
                    "name" => "Birr",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozRjMwQjczRDE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozRjMwQjczRTE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVGQzM4QUVFMTc3ODExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNGMzBCNzNDMTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+yFhTVwAAAsBJREFUeNrUlU1oE0EYhp/t7rbJJv1JMaVJbUuLP4XowVapIGIv/hzUIh4VqScRUQ+CevDkwYMHL4qg0oJXUSiK4E3QQ0V78eK/TdVG20hjbX43ye74xYDgpRsRKQ4MzM7Mzvu97/fONxrHti2yDM2Q3rgcwHUsUzP+/BcNVBkcW8YKdJ9MGdXxPwHWRBzlQi4hOpngj1aDKHyuBuGPyLxR3VMTcKrBe5eqgBbA9wmiO6Frr4x7ZEEHewpmHki/C/kOicWS7g1u7BqY9zaC5tDizjCujqPWnCTvNLOuYQKrzuZpdoeQ34IKRdjvXuRb3UpcpXsLqJIh5ZlTf4p3ySFWTzyU73mR6SsH2x4Q1HPcmN2HTYvIHWFy824GovclHa2eOdeceFh5saU+xeDkPRLpGH1Nk4TMFBsCbyQkxYt8D3OFCG8z6/H5Znk5uB1KAWFtLi112dWW3FBv5MhmozxL99Em3LY0PyNipLie3EfZNTnSfodF/zTvJIBXmbXMpntpt95TLrX+/T1WYqKAyLpQCnNzdj9p16okgIKwyrv1jM4N86XQJV7L4lSMWMPVMrygXWURDCSIBeK8tcPkZe51vpuj7bdlTeNpJoYtZmrSCnRZH4gE4ygn6EnJ0MOOB1s5wXI51z/KnsfDdBpxOsw5LsyMUBbGI+FxLMNmRu/k0sYz1HUUcHKNotHS52rJsz2erta1Eq12ghP+81weOgVFqVbFuKzJ4cYqCBQ5/OgqY4unpCx0iNymt6unemOeCVEiZdDJ0BZMcGXTYcb6D/BxRYhKmYim0hx6fovTT64x/z3Md6MZTXO87/HHrX01FVlH02lwikQWPpHzNRIP9Qonje6FaRpz35hrWUne9KG7Tm0VuFbgX+w1DV+5iFXM/XR2zvQLYINUIvVvX6cKgK2b2P7m3+b+q/c4vRzAPwQYAEGjBC1vMOsHAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 69,
                "name" => "Falkland Islands",
                "isoAlpha2" => "FK",
                "isoAlpha3" => "FLK",
                "isoNumeric" => 238,
                "currency" => [
                    "code" => "FKP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6M0YzMEI3NDIxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6M0YzMEI3NDExNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQ0YxMEIyNURBNzM0NDZBNUFDQ0ZCMDM0M0E2RUFGQjkiIHN0UmVmOmRvY3VtZW50SUQ9IkNGMTBCMjVEQTczNDQ2QTVBQ0NGQjAzNDNBNkVBRkI5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oe8x+wAAA+dJREFUeNq0VV1MHFUYvffOnZnd2Z2dnYVdWaBAC8rWsqBESa0KbdKSRh+agqYJ6YNGjWmsTeShryb6ZOKzaW2JiSTGB6Ox2iaIP6VQK9BCKa2lrMDS/SngQrvL7s7uzNx7vVvbmvhiQuQ+zCSTfOc793znfANf6OkHm3MQ2LSD3vfMDh+Q+955OtTRFL1Hx+8K74alM1/0frjdWi6rjmJdF+FxT3y4KXriJS/S1FzB/k9QyBlTgI+sPb6zL9L7zO0Tu0I/HQufjOJiYaEILEvXnnSpe6+P9jSr6+66D1xN30TyMH5NVWXKiyEgNrNsBh/CMcawiDCGjD34go/tDqY9rTNyTpsc63xO7nyrfearxDTIbpHo6TDRJS/oPjC+Klhnr74qk599SiZvy2KpXhAAhZQwgBGklGKuAOIN+PsBNpzV64MFkizSGBVtwJwgzwAxUQDTFQEgpteYd5crQK7BJSTlwNudvSnZo1qFtdX883vKu3dXZrIkRaCEBV9AHRmK/vBdzOtzQQgZb5Y41BNZzueQ2NyxA49PaBLJCK7pU6fqD3aFXmyeuzCZtmG8sSX55x0sYlAUBdPkWpqmrXqdtdsCtVhxerxAkFfIvZHfFm2LPlIcH3XuCh9pP7zVajRurevSl9XPNgr5+k8/znYfPPfK/r2vrTZOjJ2ZWu8vfyrqrsCXxlykyEUVuLOoEMmSRCzS8ERdYjG6Rk1ZVBGC/0AfDzu6lr6VJ2OfTRe/15vODi3018bbgX05uvjm0YFmD3l9T9Whmvl9yanPR4f67BqKJQkwi1JdEht09+nBQfyH4lXdlUFNhEEkSAzy2ZYaYBiLf/LLld/DbcNqGY3Ew35zOCUvtr6RWpZDYnH+avK9K7cHuto6/DUW+tFJCgZ280HZFHLiTgZfbtmHjby2NaRWakuzFw3L1ID4N2thytE5WtW6tJINmBldlSTbml6jI6GdkTuGy8jqulLukWevLQ5cTk64ql0Skhi57wDBZMQfyOKc5dT8C3PzkuYZvJBKxtcVRSyx5l6p9kq2vY4ULh6ilHEHlTlBWXoJOHi9SGgJx+9T/CXrGoDwKHBPA0VzzNzMJi6mHwumzg9EK30gY9DJacOjKxDQjQedo2cLsGtLdHvs11tzat22HbnyNufJj1punjNVD4Fo49AMQAcrpCzfiLuFIKWmeT/K0CkQJMEgNgz4KI0bWmnUpeDz6YriitVUxRa+vpTLgBuVhwXEfGb6fvaFDUKXNKGsXC4CGSZTbC5BJdlRJmcgYbQ0CAA3zPrhPio9HBJwSKX8cJnYvy5HkYSY9f9uasY7oU39FWwe9F8CDAAMDrLSTxhgUgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 70,
                "name" => "Faroe Islands",
                "isoAlpha2" => "FO",
                "isoAlpha3" => "FRO",
                "isoNumeric" => 234,
                "currency" => [
                    "code" => "DKK",
                    "name" => "Krone",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6M0YzMEI3NDYxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6M0YzMEI3NDUxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQTQwRERENTE2RDdEODBFM0EwRTg2QkI4NUIwMjMxMTQiIHN0UmVmOmRvY3VtZW50SUQ9IkE0MERERDUxNkQ3RDgwRTNBMEU4NkJCODVCMDIzMTE0Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+qirpigAAAaZJREFUeNrsVM8rRFEUPufcNzOM8bMhiiEL/gAxUmxkQzayUChFSvkr/AMWUrKwkJI9O5ONhRT5uWChNKWxGIOeR71373EfjSbewlU2cu7m3Hfu+d65537ng1wux0E2tnQ82DR9AvEzqBpunBqaP2RDI/g1+4f+DC1EMDoict5nBtTLFLq0NBwYiAhSkvPQygoZV22NLx4RFTOqgqqQUKZy0c5EnZXWHOKS+prtx8jE8gGz8K/wTWh3YfXZdgWJwq8KVVd9befdlYNFui8d2Wtva+M+fUMmZMVTTCi+JwgXFqM7S+A5EHugqAKoUE4MbAkWAxv0OuQflp9y8lv59h9WvoNGuH5D5lr6bcezyILCTIZIQ3Uye5m83CWF+y3de/FWJ32LJjSxvNnREooyqA9onS8Qdh7L5OZ638U2gdyPN6cGRnorbcXff0WwNmbbAIu/BiZXzjPXNxJIc/opnekpf1mbaTfjtW27gQHXU0Tv92dEIV3zkfGkCgxoVfzorHZM3/DvKZ+eGvYFCt+o+CN5CoeD5YlFCJRWRY/02GueCGEK/SrAAFuW4Upd7P7AAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 71,
                "name" => "Fiji",
                "isoAlpha2" => "FJ",
                "isoAlpha3" => "FJI",
                "isoNumeric" => 242,
                "currency" => [
                    "code" => "FJD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjUyMkUxMDE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMjUyMkUxMTE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyNTIyRTBFMTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyNTIyRTBGMTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+scF2nwAAA6lJREFUeNrslH9oG2UYx9/3fiW55C7X/Lok7dKuxNqljii6abVzdXNQhqNanUzrwGq7yVxRJwMdgyEqYzgGKipDNgs6qdCOFf/QualU0ZVBdO2SJgWrTVuXn5fkkkvuktzlvLLgxL/GxvzLh+ePLy/v++HL8zzvA/s/mgC3JhBwywJ7I7XU2LXmi4z57bG5hTNTwGL9dLi997P3vt6++8mxNCiWHYyy31PZ3dMst3pfiFVKBYHE0Oty7X1/8ZXh8Sfiwcs7nUeP9TkeWJMpA70oFXA97WIOekuhZ5v29q87VGxlTyQT0aSVIq7TNXzqpVHJ1PAwU9kFlrHersKqtuWx7+DzA+DjE0bKsApkwUPdI8HSxAdnER2gOh1VBEBFRRVFNJJ5ikEkFUAtgYKitJgjhaIm6mhx7UbNY7YgRyWo8gWbHqkqtYzBxAh5PY5wNlZNcI1o1e4wAIYZGtybpBlKKsmkgV6I2hfiFRON6AGEKiZWUqw712jBpWq91vPdW7JRTgCIb0OHJZ0wFXlOBKmRUfrpPkdnu/lCKFWBl1gPcyVGspSKY2i1oj1LQcP6jLTvz7dkfzkV1VtliZgGH/r3jPmG3NJiHX1XiL1327YDj9/uoTh1cvpYjPa30neOfz7T88jE+q6hQYUKXgqfjw7zTRer9KMV0ULIkgKwfBH6nDNre2uZpDvI/eghSls43msjufK1CTm62bXHHsVGfjgyHn5nUc/JtlNDrBVDhYuRXc9885rf9vqOO15lsU2Ni+/+nPipbZ2I6CCoGgtFfoPr1H07LReUF49/O39466RfuT/kQ05PAbvxKho9vP+5X85PHSwyJ+kWxEq03GNpbmN4koy0ty21WAQ+fS4Sn7nb7+ju8BpLEYiVUAxT1YyV8k///vKhUfu5QCH1hy8obD8dn8vxgWYnVZXrrveFs7XOjYws9ZMQw91Akady8tnHdlDZ9OZNFOhxK7Va7Mrym5Oqjl3tEEukrNQgxBGcy8eiOrUBejgxXasmjeJshR+AqB4AqY52GwhQLGhKWKnSSotoLVMJTeSyCgAr7TbhhAnXbgia1rgrJ/F0oP22niMHOgAorh5s+cQhNdkuT/SScwFA6+roG/vEhAmVsvDLr0Jmq+yCSzPf+46bH/xtftZl1ikquCk0UGGDCQ+EZ8OK0jHQF0k3F7Koyyhqn+nahNzw9tF64DQQEoS/NjmNecFZK/+Te1Poq3XXFoqFz//dg/9oqf6P/lf8JcAAQ7yON3YEhEoAAAAASUVORK5CYII=",
            ],
            [
                "id" => 72,
                "name" => "Finland",
                "isoAlpha2" => "FI",
                "isoAlpha3" => "FIN",
                "isoNumeric" => 246,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjUyMkUxNDE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMjUyMkUxNTE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyNTIyRTEyMTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyNTIyRTEzMTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+FFmzagAAAQ5JREFUeNpi/A8EDAwMnxlwAJO4WQxn915jYJAWhAg8eMPgE2PFsLkvkoESwAKleRnoDJgYBgiMWjx4LGZkZMQiRrnFjIFlK/9jk2BmYmRgYmJi2HXiDsOHV8DcxskKkfj2k0FCVpjB2UyJ4fvPPwyQ3EiGxQwi2dh1ggwEycgIMTBwszMw/PkLEWdlZmD4+J2B4ek7oOuYQSaQabFpw/8RlbhYQEUg/qAGFpVcaEH9CRTUH0AJgeygZnELN2fAlbhAKfrIxUcMn959YWBghyYuoKWikgIMlu66DD9//SU/cf0noNM0fjbDmT1XUSoJ31grhk29kbSNY2zu+v9/OJdcoxZTLx9DwGd6WwwQYAA8jFV5xASNJwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 73,
                "name" => "France",
                "isoAlpha2" => "FR",
                "isoAlpha3" => "FRA",
                "isoNumeric" => 250,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjUyMkUxODE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDQjc4RjdFMDE3NzkxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyNTIyRTE2MTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyNTIyRTE3MTc3OTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+NYcTvAAAADBJREFUeNpiZFCYz4Ab/L+fgEf2q7YTHlkmBpqBUaNHjR41etToUaNHjaad0QABBgAMOgSHJZqdhAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 74,
                "name" => "French Guiana",
                "isoAlpha2" => "GF",
                "isoAlpha3" => "GUF",
                "isoNumeric" => 254,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Q0I3OEY3RTQxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Q0I3OEY3RTMxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMjY3OTAwMERCQTAxMEQwMDE2NDFDQTA2NzVDNjFGODUiIHN0UmVmOmRvY3VtZW50SUQ9IjI2NzkwMDBEQkEwMTBEMDAxNjQxQ0EwNjc1QzYxRjg1Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+CztmBAAAAiFJREFUeNqsVc9rE0EU/t7sbrKbuBsPLdQf4EVwYz0UehO86922ChYxqOAfIZ48F6EePVRyKCheihQUKWi9KFFbxXOFagINWnVrsjM7O042JARrQ7fJx+zCMjvfe/u9977FlffutnCUMpRCsmhYi5U3d/zVuPzNATIYKhjcuBaGsxVe+mDV+SGAAJWsQUFY0TdCrBDQSD47X6SZIxIQCTsNlrWG1liTeKrOm5cq8trHzI/ITrbUwFn3PCGGTn80n5n3remjOvews4FOJEqTdRdtDTy1FYYz7/jsulUXdld9CSuClVKQf6B5ckCelzcC/yWe1HRtW4zisRGVzU6RVUpBdsflQMOak5mbZ2GUwDa59TyKvzKcATPj9Fl3EYM5oDHxbJn/HIfxNLLWJD9phUt5oWx1EEF6waFCLN8Vd6aaZqukZNyyndvNrNWEov6ymP2ZY0paPKCJLXoxadZdHP/EP/8yb3hExAfQuhP9cGCcf2MsXpBw1bmH7NWEcXHcXDilcqb+DrlXO+6DmmBKikZ0PVWLp0CoKeywY54959PUmGaP9Eu7Z3cf1P+Jlejc0JzG1RPOvaIsmCIZtriXnx1gglX70l6Zkwsbf/xVPKpmk7JRmg7pH0Gf9rRxNqbf8utr2W3RNc7WNhvUOjWP9gJXPPgSnH7NF6t2x/cZG4LntytYUNVmeLkiS+v2d+EkTr0yvP8KJa34m0a9zP0i+yvAAIf0Al7jyOdLAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 75,
                "name" => "French Polynesia",
                "isoAlpha2" => "PF",
                "isoAlpha3" => "PYF",
                "isoNumeric" => 258,
                "currency" => [
                    "code" => "XPF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Q0I3OEY3RTgxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Q0I3OEY3RTcxNzc5MTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNjlGNzM1RDc4ODNGRUI1QUNGNDFCRDA4MDk5RUM0M0EiIHN0UmVmOmRvY3VtZW50SUQ9IjY5RjczNUQ3ODgzRkVCNUFDRjQxQkQwODA5OUVDNDNBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8++fOASwAAAW5JREFUeNpi3MugzEAbwMRAMzBqNBpg/P//PxHKbjMwrDyd8VIyVkDGWpiBwYOBQYOwpp+/fv3HC37/Wv3jn+S3vxK7mCXPx6r/+S/17af4r98LCej6/YeJAa+r//y5/fNHPzOjKAeTyf+/Ak8Xv2dgUOZgk/j1vfff3ztgJbi0/8cX1kBLWVjWcfOe+nmX59lZCY1+SZlkvWeXFV+fEefmvczEvIoBb1iy4I0IhgtXJFgFi9gPfn/z3IC/Opmb4e/DpZ8E7+7+p2b587O8vPR/kCIyjAZq2nHE6twT5xDRhzK7Jq75o/BbTclv9aIr3Cp7F2faSX8i32ggCPfl5zt0//5Pdg5LO3aGtx/ec/ww0n/HLy/Dcd3GRAWPuSCX/fr5k5WNDY+K42dvH7v08reIOP/fz5oinBvvfhNk/G0ozuHrboBH198/f4hK108ZGBZsf7j/1D1+Hk4uLs6SBH19TiKyzGjJN2o0XgAQYACA4seGjOuQWAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 76,
                "name" => "French Southern Territories",
                "isoAlpha2" => "TF",
                "isoAlpha3" => "ATF",
                "isoNumeric" => 260,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzlDMzYzNUQxNzdBMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzlDMzYzNUMxNzdBMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQUM2MDVGNjUzNTBDNUY0RTBEMjExQjAzNDdFNDlDNjIiIHN0UmVmOmRvY3VtZW50SUQ9IkFDNjA1RjY1MzUwQzVGNEUwRDIxMUIwMzQ3RTQ5QzYyIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+sbAVIgAAAuNJREFUeNrsVF9IU1EYP+fec+9256Zijvmngtm0CTIhp6VQkCVGPkiEL0FSkYEQSfQSPQRRVA89KCEh/REfSsSXShPNBlFGiFg9hDqidG5T57aubrvusuu9p3M306QXb9Fb3z3cc+453/19v8P3+z4ICxsAADi6osswTL9uz80xg6R9qW8MP+/RpW23WdJb7JWdEyHIQOIItmwU+Gf2H1orNMQQ/hk0woKozjFRYWgCtH6gF0UDSOiFZVMEU1gB2gOg4uIdZIrExCyTjkYb2pKseZK5iM7NTeRkypweSAmM2DV4CLEkg9VVNR5MKlIBgKEhogHeQIC7DzWvrQCWZeKFU9QxhQhV8rCIWQyEeD4GGCJs9ZyiKZaGIh/FigLIgBTN0GxGuihJm1i7J/0pImpAIZ50VRFAmgEwSP2UFUiQOFZd0zTmo3LAe+Jy04Nb56e9c/6FEEJsQYHlZEvby65nIH8nNHHkFwJJwxwHZBEk1yHvND00cmtDhyCi1H0dAogigdWrCnFHmc3qsI+MTro+uBmaOV5b5Q+GW7sGe/vf7a+pItfhgzxESKP4CHZ4ubbKcffGOX7WN3z/TnfHE7Id4Jced/Qqvvm262cqS23EJ9UPNECrCcsz3+t2le9rOnKwYnBkqNBR7J6aefSwb6C/dZfdVlF94alrHFiyU0qDqfa0VXSKUoJhktnx4bY9JVaPL7S0JEiy4iwtGng1Wld3EWzLgulGNStaqxEns3q4Zu/HCU95/ZWYkCgtsc2HI876S9+FSEmNE0hKClcz6yRxoIiSCcKoZ+5oQ/WLnps2Z+PXiRnOnBln9RQF8U9pa+4hiozBfPj2tdNjnzsH+t5A7sC0d8E709t89hjwBPCvJaOVtdpV4mKBzcLSaFlYKXMUvn0/Zc3PDkWivtkg5HR/0/kwKYpvbt+Ua7TcYW+/espo5D4Njfk9i9Bk2KwozazXU4r1NKWDICbLMqR+714/BBgAETMoHoqT0acAAAAASUVORK5CYII=",
            ],
            [
                "id" => 77,
                "name" => "Gabon",
                "isoAlpha2" => "GA",
                "isoAlpha3" => "GAB",
                "isoNumeric" => 266,
                "currency" => [
                    "code" => "XAF",
                    "name" => "Franc",
                    "symbol" => "FCF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozOUMzNjM2MDE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozOUMzNjM2MTE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM5QzM2MzVFMTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM5QzM2MzVGMTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+XUVjwgAAADFJREFUeNpiZJiXwEAbwMRAMzBq9LAwmvHPRbHRABk1ekCMZrFbsm40QEaNxg0AAgwAqCcEmp2Ti78AAAAASUVORK5CYII=",
            ],
            [
                "id" => 78,
                "name" => "Gambia",
                "isoAlpha2" => "GM",
                "isoAlpha3" => "GMB",
                "isoNumeric" => 270,
                "currency" => [
                    "code" => "GMD",
                    "name" => "Dalasi",
                    "symbol" => "D",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozOUMzNjM2NDE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozOUMzNjM2NTE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM5QzM2MzYyMTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM5QzM2MzYzMTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+I9VNdgAAAKpJREFUeNpiPCeo9pSBgUEKiD8z0AfwAvEzFiiDAYmmi+VMDAMERp7FLD/e3x0Qixn/11R/ANL8QPyPjqH8kfHsy//vgAxBIP5Dr1AG4veMDLxtA+JjFlZOZqYBSGhMLExMjAOTqn+++fEfyv5PrwQNsovx5tV/74EMASD+SyeLmYH4A8vZb50Dk48lE1g+0bmCAIHPLIriKqOVxDCvnaBNHl46N30+AwQYACKRKLCtBsdeAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 79,
                "name" => "Georgia",
                "isoAlpha2" => "GE",
                "isoAlpha3" => "GEO",
                "isoNumeric" => 268,
                "currency" => [
                    "code" => "GEL",
                    "name" => "Lari",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2RjU5RkIzNTE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2RjU5RkIzNjE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM5QzM2MzY2MTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZGNTlGQjM0MTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+6i4DLgAAAqBJREFUeNqkVllPE1EYPXfu1ASwIqEErCBRQ2Ki8UXjg5IQE/UJWfQXuP0+EzHGWPRF9MEHDRXColIBbbSWFlptO53l85uFhulMZVpPMpm7nu/ebzm5goi+A0jyV0YEULWG/IWbMDYy3JOQQwNILKWg9PchIuL8ZVWvgQN/wLIA0wQUhbklOoJpgZhD2PulEjCuhO75uo38lSkYq1/QKcxvWeyM34WxvhE6HzCspRZQuH0P+soSijMPoT1JtW1Ue/EahUnmWE6jOP0A2tz84Yah66BKDUJIJ56k6e1flzlQqULA46iHcHBylagJxqcM5c5dJ/3javMUWZUq5c6MUxYn+TtFP4cukZkvBNYZmS36dfEW6cvrFIKSGnZgeXYUA5ypEMrhtxMidFiODiPx/lnLeTXgBiInm0VMBRmc2brZ2CyOxFy32WvYkc44t6luuFv3uXwcPKcbgQOI3NhEyVdKh92OS80q7LrlZvfZgNJ3nK8g3TKMhrLIIhndMMhxv9LLYqFIt28fZI8PQkZorrYyrNrqEx10gJyaikN13R8Rqhw+Ed3ufky53Bpu5TGZHHRVjig6lbVb8rva4s0xCdHTDfpTdRND8ZKrNw7rRx47V2dgZDZ5gLV6MIH+t48hT4+A9sp+jm7mqPg5Gq62yUKK2zXU0xXMxt6j3hi567gtjsUbB/OFxQrnQKtsMDPb2Lk8CWPlc4SwU1O8PY5N1uprszDWomr1/BsUpu+jvryI4p1HrLMv21bM+vwCilPMkf6A4izr/dNXEW5cY23d+83eiTl/W2vbBdU0J97/5Gip1WMTpKdXOtfqjS3Knb9B+tJaG1rNGZpYfO5IXqeQI0kk3s05Mhtax96TJ+57+vCrQXRJ/BdYQoUqWz19yn8FGABqxdXVw6EGDQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 80,
                "name" => "Germany",
                "isoAlpha2" => "DE",
                "isoAlpha3" => "DEU",
                "isoNumeric" => 276,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2RjU5RkIzOTE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2RjU5RkIzQTE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZGNTlGQjM3MTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZGNTlGQjM4MTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+aBEH3AAAAChJREFUeNpiYBgFo2CAAOMNmhnNxDBq9KjReBPf/3OjATJqNB4AEGAA+2UCvgLMJf0AAAAASUVORK5CYII=",
            ],
            [
                "id" => 81,
                "name" => "Ghana",
                "isoAlpha2" => "GH",
                "isoAlpha3" => "GHA",
                "isoNumeric" => 288,
                "currency" => [
                    "code" => "GHC",
                    "name" => "Cedi",
                    "symbol" => "¢",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2RjU5RkIzRDE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2RjU5RkIzRTE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZGNTlGQjNCMTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZGNTlGQjNDMTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+nJIV5AAAAbxJREFUeNrsVstOwkAUPdMOCEJ5BEwMiS4wwbhRP8GNxGj8AF240K07FyZ+gq78A42J8RP8CMJKdia68MECQcqzls542xBkowzEoIk2venttDPn3Dl37gzLxzOPAFJkNYznMsieeNdB33Ms4Bp+6Pp7wNyq6EN3EmQBjXl+W8iR2POp9bbT9Z1hOvp8BEy3/SaHxXQjdZgsJqvkRMi+HkF2hdEJLaHj4qwBm6ju7oWAF3Ioco86Gwjs/mFyUdJYX8PnyUCgrYaEbjD4ExyX5xZMUxJwFHZZwCb6kyEGIZSiZlyqTnCQeDWBrf0qcoUOylUBh0BmU89YzOi4Oo6S8AyyJr83qwVFG0wynByEEY8w1JsSrbZEKABqMxCe1iHq6nprrmQq5jEsS6TXgshuBHoDrKwGsLAZpG8CmlQbyzWOSC8tB9MlDZsFC9V8A/fXYRj0fnhURymnI5mmZFWbZjeXJNs5Xa6QE1NZThN+hrsHB4kYw9I899pubjsolgTmZjgstaXlLqdXhu2sqbxBeJWDCBMBmOKj3NsESHoPUUlqHHFrtJqX6PP9rgz/m8Rv3Z26Rx5jzEef2rsAAwA2l4mM8iP6VQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 82,
                "name" => "Gibraltar",
                "isoAlpha2" => "GI",
                "isoAlpha3" => "GIB",
                "isoNumeric" => 292,
                "currency" => [
                    "code" => "GIP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDdFMDYwMzMxNzdBMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDdFMDYwMzIxNzdBMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iREFEM0ZBNDBFM0ZFNjU4NDVGNUVEQUQwMDAxODNFNEIiIHN0UmVmOmRvY3VtZW50SUQ9IkRBRDNGQTQwRTNGRTY1ODQ1RjVFREFEMDAwMTgzRTRCIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+XVIZMQAAAslJREFUeNq0VGtLFGEUPu9lZnfVHXddXV1NvKQZmeEG2QWMoiyIoCT/gFkGUZ/7Af4CwQ8JRRH2pT5I9SFSAqFPSTco8pK7Re6uu7q6V2dvM+/bpG142VnF7OEwDMOZ55z3Oed9kMfjEUWRMQa7B40wFApRs9ksSRLsNhKJBEYIwX8AIQRvJ4/viJ1uJyng/v5yoJ9Yik3U2Hn7libiv1IvuN3vHj2ke6pJkfT8ydP3Xt/Rg831h5rl+YBvYvr89V7LvsZ83NFolOsg4PO1mwxlAMNDj59dvnQH4MXV7qHBuw6AY5IUm1/g+vD7/fm0NtlLL9oqbmpax8Ihl3s/gPzTUyIIPQCdVVVKWenKGHQHgbSuzbm0UwEGz52tHn1dSIxjdmvNsnw4nnhbbPaKuH0hQlh6/IizZ+yNvaAw93gCAV2tCcA3Ig4D1FDsWgx+QuwjEWbkeCSu+ESDL5m2M9DjXUU+QTpOntKeCldqAUsMLYNq49AEFHElA9DVeWXnG+L5MH4BIAJ0XMAtCmpUYNJAFc4aM6xG031qMj81RpD7NgZmpmZHXu0FaEonS5fj9Uq6vpCZ5ThLyAeUVC2Aa3Qk6pnVJUaIKouLEAwqjK+phnBlhXfwfofZWtB2nKqqk6ElY8LbEO/6XJIhVHMyK0F1Xyfm+gekvj51bo6vdzdqEJRYFLmudZN7DzZskHYQYrGgcoeqKhxYUZK5HeJEHXa6UZU/GTdirSmEMfMHIBLe/K+mcrD3BgXGtGVQNpkGC4eRFtnsghBFYFCnZTXK0RoWPS/mXMEEGdFKEl4fqwX4yrsMwkKlYGmgjihKZL/z7IZtiD9sRMAcbeFrJu10BvzljERsmAHb0oL/JlCiZmi2VE5o19KWSp0YCsgVv1NEfRHWbjTKpGjM2Qo/ToO9XM+WtT4FBtYlZhCFUBtHKtvSvkk4lGpp/SXAACbxTzWqruOZAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 83,
                "name" => "Greece",
                "isoAlpha2" => "GR",
                "isoAlpha3" => "GRC",
                "isoNumeric" => 300,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpEN0UwNjAzNjE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpEN0UwNjAzNzE3N0ExMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkQ3RTA2MDM0MTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkQ3RTA2MDM1MTc3QTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KGW0OAAAAblJREFUeNrEVL9LAmEYfj79UgqtiCQoHIUobHAwiBrdG8JBqKUpAgkaoq1F/4aG9E8wxLHJVqmla8ghaDB/QA13lVh6X+99d6lIRHRH98DDvd9x3PM+74+PASc1APNEDWgim03h+HgdxeIdNjdP6fUk0QMTOlHAJoLER24FGHp+A2YJtondoUT+Ls5/992HFM/nU1hdXUCz+WpXGNwo7wA1CGH+VNc7RkUslx/SZTw+h6WlkKRdsEzmUv0qc6v1imRyGWtrYVSrT8jlrhEM+uH1MkqIUmh30evpYIzZ1dWYEEL9ub8DRKOHUJQrimw71nipVO2fVLVD5VxAJDKDRuMF5fIDxsc5fD6vdByLbSAUWkEg4IMDOCDHB8JkUmQyF8JAoXBD5x1imrhH3BeKUhcOQeXmnn6hTf3zy8jjMQZ+2trjnpzqev0Ni4sCtZrqxFR3R9ZGl5EwaivPg71NJM7gFLh5OQxfFMPwWO/YSGLM/jrd3rb6U93pdBEOT2F2dgKa9o77+2dw7oX97bG5Tk4K8+3tc7gBKuKWO47T6V13HLvW40rl0a0eH7njGBjTLGHtn0Sl1qcAAwDRS+VdNgkmcAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 84,
                "name" => "Greenland",
                "isoAlpha2" => "GL",
                "isoAlpha3" => "GRL",
                "isoNumeric" => 304,
                "currency" => [
                    "code" => "DKK",
                    "name" => "Krone",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MDcxODhGMUMxNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDdFMDYwM0ExNzdBMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNTkxNDkxNjg0Qzg2MzI5QzUyMjkyQjk5NEQ3QkI1QkQiIHN0UmVmOmRvY3VtZW50SUQ9IjU5MTQ5MTY4NEM4NjMyOUM1MjI5MkI5OTREN0JCNUJEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+jNGGgQAAAtNJREFUeNq0VU1rE1EUve9jkkltmqS2IU2tQhXB0FoVArYLQbDFgi1uFP0B4lYEl+KfEBHc66IFhRYbUYTqQkEpLdovTDc6adO0NWY6MZP5eM83KU1tkknjwsswi3fnnXfuufe8QZxz+D+B3RK89FQEq7XoFtQtIVCMrYw2+Vyfm8VaDlqaaW/cP3y1Kdy+ezQ6AJvXCsOylAf3lo/65hEsEfjmhUUMCwDJrubU/btmURff2IzXD1SttWUUlSsX9dcfpKhMggEhmvMNEgE8m9VXde+lgY7JNz6vrz73SmgbQBkd1icS3p4OZCPmCPNXiQgwRsbXNenyhWNT0/if2ph99LAwkfCcCnMHlVURAbEuxcJG4t3Wk8e7ojcGrU2OU1lgUHAbSkccQn3we+yZWTrOVRDRjHKysJJU4ieJ2NwkH9B7vQgFiHz+cijW4zp8hmFIQnDMEZHzqylb9uDWECdS/blCzDIz6WJ6VY71ILtYXSFjiK4NxR0NkW0rGc/gSLeyzVUNbPMAaEIh2Jq5cyt7+ybu7KyQxZGMc2osLhBHE8Y2uBX5RLGEg6EG/cZmZ1nyJ+SyFdAOUwSUtLUR7nTe49fNlWVtbqal71wjuNr8nJlcJN0B8PoqWomckzjeGx/ZZ6eLhZcvGqS8PfaUpfJY9ruNCErGIjusS0w00+Ltifeh02fdfCY8RQDUpYX1wX4CJvIHa42249z9cx0KIDW/eWM0n0m5cRG4+bSyfm2Y/1Jx6HBNy/CyZfaSpi2dOCKtKD/6z2y+nareZAHkpl+tD8Rh+bt0vJNbVm0pyoJgxzS8XIloLVM2zJxBh877R657e/u4P0BVtTA/o02Mm4mPJCiRrjAYtvvd7QiyX+tyOZhgy7DSm5bKIYC4j+CCDTlO/YCibUC9yLbq3tcONC1z3VcOY4ApjUZoVKhgIsZ4M4YOygGLPWCzOr+nHcuI1x8BBgBBy38D0GVnSQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 85,
                "name" => "Grenada",
                "isoAlpha2" => "GD",
                "isoAlpha3" => "GRD",
                "isoNumeric" => 308,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowNzE4OEYxRjE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowNzE4OEYyMDE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjA3MTg4RjFEMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjA3MTg4RjFFMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+RYghnAAAA/BJREFUeNrUVVtsFGUU/v6ZnZ2ZvU13S+n26gJ7aQPYEhKJtOqDIU0IJPhEY0I0RhMNRkHwmhh5UNPYENQ+qvHBRHwzPlCiiWhskAQJbi2l28u2DWxLd7tAd7ezO7M7M7//LKWm3PSFB0+yD3N2/u985zvff4Zc9EfxcILDQwsH+1V4ElTLZoksQAIoZRlw0bfynAvjx3wOWPgPGWKjkXpogkSveZyCSQkTRNKseIsn1KM+7svk8+4l3ckKsH4IB2pWCbBzFh6UoURxVhTf8vli3dSP3m2zBU3ibNahUunD1o2ndrV+1PjnwS1TPjEN3YOlagEX/afDEmGJOzOMbk0F0jLK8pej4bcXtndPzD0zlklIbhvaAmk2tdyw+9XBrhONkaOPTrzcPg1PDjmPmRZA6L21tAjvNODLIyV9dXlL/0h0PFWPJkeroVG7qarW7ChTBi4NtcVkVnnldPdnI+F3Osafa5vhvSpyXrPMw0H5AOMA8waHCuEFE7UFaM6T8VjfcOyvKw0QKwjk4JYZFF0dIwuRs6ALyHvAsLzFxGLg+cEnT1yKvNuZ2B+b4f0mJE9hSKAG8T1VRlmFRr4fDX8cj12YbYJgoDZnN8f4UbLGISzSmtiy/npEKZyZbIZUsgtQMjxf13s1OHB505u7E12fqJnvJOae+n3c+cOB/tNtv061gKM207tA10BPFlzdLelvHzvz+c87Xh/qgKzbx5RlWNzZTOjssXXxX37b+vVNyJh6VtmT3Ukjfig3wHq9F+iaKyPyVsYjYhAdo0toKjPElf8ZI55C5wydcH6Lq7NMA1Tl4LDuO947WLf51IF4eN/i7p/m/WikECx7uAU3ykJHaP7Ie2MbupYv7a1lgrQcVU8+fe74UPuFZFVlJt0tQe4H7XaYyAk/qI+gQYNuoOhi029rzhzZOvFiexI1Opq9pJ7AQM2uSq883RtIfTO+sX84OpIKwlmxC6Dq+ruhK8z5oglnETkZRW9zQ5aZ7+DmJNxF291zMjOfv0e3zZfhkK3hReNAZ+JAbOaLsU198dj03Hq4dPtlshaaPfKsqaKIRSlYn31jZ/y1zUlRKTAvmln/itwUZvr2AHhqGjyu1/DOykvbR1+Izg6Mho+PRFIL6yBzDIqsQrM1cNUhuTu193uGmQIOKQfLjZsKZMq7jOouqs579aKvZorsqMIH9UNtFw/tnfx0JPzB4rYr5ySuKo29nmTN+iPkC+1Rn1CuacuubEmCxZhWr6llc7SBTPKgDEcCku7yqr8XgpOnPDuS+ZLE2dC3lqpVwgLkqjPshRk5XOBEOt7nE26v0H/NEJAGaA7JmveIK0v1//eV+VuAAQAuXtAy981G1AAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 86,
                "name" => "Guadeloupe",
                "isoAlpha2" => "GP",
                "isoAlpha3" => "GLP",
                "isoNumeric" => 312,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MDcxODhGMjQxNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MDcxODhGMjMxNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRTRBREQyMDlFRjgxQ0Y2N0ZGMkQyMjRCRTVGNDc5OUMiIHN0UmVmOmRvY3VtZW50SUQ9IkU0QUREMjA5RUY4MUNGNjdGRjJEMjI0QkU1RjQ3OTlDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+1p/tyAAABCRJREFUeNqMVWtoXFUQnjn3tXt3NzbZJtm43WY3auqj9JE2qW0SpZAUqQiloAjSIpXmh5QiCioUsVgEtVBQmlL0j4gUEvSHiEWsadPWhkZoKjTU1iIhidlk83KTfd3XOcezd5tHN0nN/NnLzsx3Z775Zi4CdMN9IwDq/tbLN+9WDwzFAHKwYAgQaG28Zjtyd++zADPwgKl+f+rVvV2dP++enS0BcAB4AS6fiWgDRAEqvv/lvYMv/QrQACADUNcrXAGAredOnTj70UmALQB+909hDFDEbF7jS3/V8VlN1Xg+MQ/FhE8CeN2FLju4/9yBfd+F1BS1tK2bevr6I4ZZ5kIE6zf2tb32dSg4lJwJRKJ3ZpN8bPIJACNfGfrePdx+6JXzRloNl07v2n61q2ebC8sKVXPO17Y29b5zpPP3G0/HoiNtB77VPaIvxfWW1NYMHnvrdDIVNqnv2NHPa6Nxtw/Id8b9Lc2XWp67Gb9bvW9P797dF4D7XGiOc1xLBJWAf7rjiw9+vNTU/k0bQBLALDBGZBGnnz3+ieXA0RPvIxqOAy6hHFHl3Lch1t9x+vjbH795sWcPkBngJnAiI6xzoSnjwZkU5rIbExN1AKLfYXSnIVpjjg5QlTFilHoobXBdRuGtIoZD5fg0GU1sGh7ZLgaGPO66EAl+OqcCQYteXT6QMbwTqbUIdgGa5TsTodL6qlHTkhJTVQBZBILIXeUUyvI8tj6emHgkY2hzBS3WFy48kyLX/V+pMqiGypckwvKGi5/UMpQiqmiEDdhmkiIR9aDXp257Clt2qRtiTiRMB/5ROn/C7l4wrbRlcVjZFkOjvk6Xd0q2zWCM4Ag1VcmXxpcb1SNv0M1NZn6JBEnMe++OcuZL+OEyj0+aZi63ErT0wGssoAkGXk5q0K6S/B7+4gueDw/ZTzakYNyEWQ4ZDtQOPm431sEft5SxKUynrdVAA3MoyaAiy1wTc+fhEnL4ed68IwtTNp9HcABTTIsxv6lc6ZOmpi0x/WWhiwYGDrHNIYtdZ8pfWKbJkQpHlCm6WWCuoIAkbai3ayNCKNJKgyyG5gwcx7aytjHsVKaVCr8l1MeX5lKhZ+JFmdGVBLIEep4ZJ8umB21nVmEaKc4Wx0eDeI5OIFf9WJDA/3BdBJCxaTSk72gW2+MscMIBNQEtne/Xu4bx3zSopYockEipJJabm3w10IRzsXtyfa0eqnVQccT5FMSiOKiPajeu6qfa1Xt/UlNzZLG5QVDFQkwqTooWLurDoZExNpGUBodJuEQOBri2BsDrMZLKxS7t5Bn87Up2JmESm6GMUrns3LaNoRyfw33Ili7ULkl63TPazi0YDfPRSTYyJt+6Tf8eMQ07w2g+X5YUJaSZ4zlq09UcgKIYoqoKoiyWb+7rw+YbR4Blhf2fAAMAfojFQGvOh1sAAAAASUVORK5CYII=",
            ],
            [
                "id" => 87,
                "name" => "Guam",
                "isoAlpha2" => "GU",
                "isoAlpha3" => "GUM",
                "isoNumeric" => 316,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjBFNzZFM0QxNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjBFNzZFM0MxNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iODAwMDg1MDRFMkQxOTdFNEIwOTkxMTdCQ0ZCNzg0NTYiIHN0UmVmOmRvY3VtZW50SUQ9IjgwMDA4NTA0RTJEMTk3RTRCMDk5MTE3QkNGQjc4NDU2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+wSFE3QAAAhxJREFUeNq0lU1rE1EUhs+9dyaZTjqZVFPTRChElEJUqAGhHyAuqrSgLsSFiCsXXblwqSJuXApu3PgTRKU7Feqi+NVNQaulC0tqbUhqOyGTmSRNM8nce5z4AzKBSc/6npfnvLznXLI6eTUprGHXBiJDP4oI15SG8rIuJUX1fXh8RT9FxX5fpAVTM87W9cZP6Zgw3w2MvTo6D+2CTxMSIOivLSen7Nfz+58pHFpJAqS4Z0W7SNp7Xd4xABcUIEChiV3RETHBay0i9USNFFyMnK5tTlrrgqvIeqP2EQUPE9GNpO3c3dmBmBp1F9ZWtDMgH4Cf7bQHZEWvGLezjZfn+KOTpVsTzXStgHTQt9FHmnjITbiQsNjl878No174m5+bmRltQbWJQQ0hFOzy2LgepfEbViYWj0RF1BwN0dyuGEwA8gCGIBA1vJUvHthGJtYq/dgofMszuyQpUmekINSdKA2NbPz6nvn0cDGcWvxSuTmxWS7OutpFgnvBvPbIWvWdI5fSiuHw5WdPPr6YW1uqXRHoIIigCSHQKEfYvaXnD064d2K700/fbNMklY3AhvzfRCJK1ePJx1/vi7e4zM4S7Q9yifSckK7b6+2fvPOhkQVZZqFtzqkvckeaomt6+Q+lwHe9FE/Oi9uIN0bXy5cymMYIP8zLV2HRaSfnmAuU2336CrRsc90iKlmdujbMq7pbBSL1CZfXacRg+j8BBgALAtvzf7DSbgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 88,
                "name" => "Guatemala",
                "isoAlpha2" => "GT",
                "isoAlpha3" => "GTM",
                "isoNumeric" => 320,
                "currency" => [
                    "code" => "GTQ",
                    "name" => "Quetzal",
                    "symbol" => "Q",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjBFNzZFNDExNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjBFNzZFNDAxNzdCMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iODBCNDU1NDY3QzIxOTc2OTNGMUNENjM0NTMwOTgyQkUiIHN0UmVmOmRvY3VtZW50SUQ9IjgwQjQ1NTQ2N0MyMTk3NjkzRjFDRDYzNDUzMDk4MkJFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+T8VYKgAAAklJREFUeNpidF77kOn/v3+MjAxIgJHh/4Mv/0NV2NuspBhwgwNPPubsfyPKxcKMohuk/T8jAxMDw/9/jOh6/jMwMjH8Y/zPQAgwMv7/hykK1sfI8p/xL+N/JigXSRYizUAYMGLoBbv6P9Bx/4nRTxqAWMVEnNPQtREDiDL6719QgG44dfjAtQuMIO7ff//+EWE0I06zmZiAFjPMWbdg5rqFQMbreydyasN+/GO4cuNK7ZQ2oAg7GxvuGPjPhMeD3JxcDAx/lu5YziMg//nutFSbXU1RgqktZb+ZWLcf33rn8hFhQQ5cMQsMORY8PhLg4T1yYjMjK3OcqxMDg+L7PTeC+NRYnKR+Mf8y1TPbcmZXqK4NIyMuPzMy4UlTrMwMT9+95OBgBXLffeR49/URw+vp3N9WfvnBqqes++Hjhx+/GMgxGhhQ337+NVE1ev/u06tPv15dufiem+uvX8H5Z5yvXt05deWMopj0f0IpBCf4+OWTsqqRpqqGk7vKdyY+E5dlX9lSv/7VntVb8+rZs/io/C/fGPAkFXzR+P3nDyDZGFpw7+7j6CqPR88eHjq3a9LksitnrqT7xjEwcHz4+gmPy1gIpmpZVfWTp64s37r0zbt37H8ZC7IbjLVMvay9COYfFqb/wFKIQBbTVdDWzW6DsF2tXYnN6P9JyLqkAWA0/qOd0bQCLH8YmZmwlat/gaUQIYv/MTL9YWD5x8CMWYAApVg4GX4zMvz7j27Kf47//9gYWAi4699vTsZvrIwcLKgRBjQaCAECDAAobt+WaLkKAQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 89,
                "name" => "Guinea",
                "isoAlpha2" => "GN",
                "isoAlpha3" => "GIN",
                "isoNumeric" => 324,
                "currency" => [
                    "code" => "GNF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2MEU3NkU0NDE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2MEU3NkU0NTE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYwRTc2RTQyMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjYwRTc2RTQzMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+S1dzKQAAAJpJREFUeNpiPCeo9pSBgUEKiD8zYAF/PzIyskr8+6u55tN/Nql/LED+f2zqmFl+M375xfFX4ZQjw9tvPMwMrL+wqgMCXiB+xgJlMCDR2MA/KM3EgB/8h2JC6ngJKUA3lBpqiPIBzcCoxaMWj1o8avGoxaMWD22LGamkBgxYoE0eXlxNH6hhf6GVPAueyh6mDtwSwqMObBdAgAEADjElH4U24o0AAAAASUVORK5CYII=",
            ],
            [
                "id" => 90,
                "name" => "Guinea-Bissau",
                "isoAlpha2" => "GW",
                "isoAlpha3" => "GNB",
                "isoNumeric" => 624,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQTVBODZCRDE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQTVBODZCRTE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYwRTc2RTQ2MTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFBNUE4NkJDMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+NfA1TgAAAaZJREFUeNrslbtKA0EUhv+5uLlq1ChewBvElD6AhfgOvoKFjZVvYClYpLDVh7AQC59ASGOh4CUiGIKGBI2S7GVmPLMSBCvjbtTCA2f3LMPwzX8us6w8UrwHMIgvmNGA/8RNYfdF5dfbUt0KfNck+TR+wTh5q8c9hjyIA4zfUvx3wDaXCcYgwML4x8C8S6dH38E6bHGGHBOY5w72Ow3cKA8LwkGK8XA9TpPdIEmpfdQBKkpjgkuU2nV4pLcoE7hULgo8AZe+NR0uyzWDDCCkiQ62aodJbanziCOvRfNisEfwY4q3M5ME4/C0orobNJRQOc8x2hMsMrhlFEaZxMHQLJabVzhXnTC9O5kprDpZXFPabV3mSPfm3Yw5OR02rOZHV2w72Cc1FQLY90Yqj0P3GWd0gBVkYaVZd2it6juy0U4LdNzo4Hc4cEH13EqPE3gMa84zykEbdf1xUSnCDwllMEBqZRAP+In+AksyGdb61H9FQSSwSM1VI7AD1p+u7s6WZwweTBA2W5Pqbgc5buj/Xf3j4MEe97DPvfHd5qr2CDfhVEWEvwkwALDLl6s6AZ8RAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 91,
                "name" => "Guyana",
                "isoAlpha2" => "GY",
                "isoAlpha3" => "GUY",
                "isoNumeric" => 328,
                "currency" => [
                    "code" => "GYD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQTVBODZDMTE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQTVBODZDMjE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFBNUE4NkJGMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFBNUE4NkMwMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+jfKcDAAABN5JREFUeNq0ln1o1HUcx1+/e9jtbnO33eaUXDl1DqVRmYqohSZWKqhFBCb9WZhgiQhJkA+RFEmBf1RahBFSQU8gtc25ypGz6XyYJGqKc5t7OG+7290ebw939+v9u5umsZalfeEHP+73+X4/78/78/68v2e8XLW321nkYd+xQ0SazgLD4M4G1ziw2cE0+T+WMTuv2Hxqx2bWrp1Ng2eYvbX1VLXUEgydhIEQpGelgNiddxWE8R055hbCMCOP/btWM2/lE/p5GuVX0ygLtFLeWkO9vwb6m8GZLiAC4XRbW/X8dyBGMK/EdMQTvB1u5F36mb8cvt3pI//hlfq8BLqmUt2bxsFgN4faznEiIBDh8/oWFxO5akmmTjH+NRvGiZzppgsbxXYXl6PDbO4LUGF0sn4TfLhRERN1cMdcyFiqSmfRMHgPlZ0xfmi9wM8tR+gL1kGsR0x4U225zZYYp3OKk1Ex0Tbe5qDAcPK1qtvENcyZA7y/w82zKyS4SAz8CvQ9CLkrlGQBweh4fgwnKA34OdJ2kqaOWuhtgTQPeHwC4frbdtxIzEiI9Uy1p2GKyXfCIXYRYP7TsH+rj2mz0uBikFhoCIdeySwQkMfExiLMxAx+7fNS1h6ivLmWOv9RtemykttS4kzLUDbbDTZuSXx9xZXeraDpQnyub5AN/dc4nt7Fq1vdvPWCKM3SmF0aIhaLYiR6sSe0yZ2RApG9TKeWcLbHTXlogAr/RY5dO05/8Ixo7VNcTrIloya+viz6J9qcTDAcI/T7MUsG+eyNbFasVqKWOLFgAsNpiWtQGyIYYspmseGdAzmPSxdz6RiezE8RO6X+K1RerSQggY6Z2FqJkcEpFP02HbonHGG7ACxabOfjnV4KC+wkuhI3d08gpIdEt7xoGLvIIWNiCohbInUu4eJQgeT8T+ob6btVvUf0e7Fb3kZXl0nC+jDaCdZ4WTuNEeRxqX44qJeoeu3GJ1d0jJXUSpYnmieJ7tLOXjYl/HQVRfny9SzWPCdLDcSJt+vkJNWCE5cRxUzs1qneB0T1Ys35PDqHJlPTO47yej8VjV9w2V89emILpFNwZzhcNA/EWNXTTIXc7aXXXOzZMAHytO3SMLGhAUV1YwyocJdKz31EAtOoOebQGM2jIhilzF/P4dbP6QlJXEPh1B0gld+S2EwmNZksdbjF4e6OcLKfxUsTnN6Ry6yFssz6CLFTfTjUO0eGFOpbDePkcEjJ0XzKW3r1nKGq9RM53IURh1NchswlM+fGXN9iID7RWijnKQv1sVG0Bu/r573tbl58RmIZEJVNCswuUmXy84zFRCWSqoiNUs3u4bY6zgeOQ7c1u6rHkzumpzssWq2ySxzptIjWtZ1tfEWQNevgU9lm+hQh7pinl0fh/jmifhJHe1yUXmqgsvkbAu1yq8FgyrMt2/RN+Uui0YfGca+EkylUuzua2KZ+zVwIDbvSKVyg2wL5c08Jvzm9VAS6KG37neq2A9LQuT/NwGNRmHWTP9/eZWEctOWbWxLthAuz+OjN5Sx7/kn9XER1a5ZupAhlupvr2n4RhVdIDqXlwUkKbXd2LZZ4CsxV2zazft0SAt4Ye0828n19DYHOUxo7fyqJlUwKv6t/BF45+EF3zkP57Ks9RPMV64rrTVGY/OvjuKOqxlp/CDAALJ7xfKNmRxYAAAAASUVORK5CYII=",
            ],
            [
                "id" => 92,
                "name" => "Haiti",
                "isoAlpha2" => "HT",
                "isoAlpha3" => "HTI",
                "isoNumeric" => 332,
                "currency" => [
                    "code" => "HTG",
                    "name" => "Gourde",
                    "symbol" => "G",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBQTVBODZDNTE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBQTVBODZDNjE3N0IxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFBNUE4NkMzMTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFBNUE4NkM0MTc3QjExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+vIrdngAAAb5JREFUeNrslT1IHFEQx39vbzeunkYvIRH8QgJBBAXNR5EQkIASREhlomArVulSWVjY2CmiWKuVBLGwEYuASIrAhigkhSFgkDsQDWjirbruebfrrFZW9wTJEXDgLcwyzO/NvP+8p6ifTlMAM2WVFQJsUCC7Af9TcenbrRikXPD8yBFZ3oUj8YNDsMWvE51mgmsGh7L2PF501dP4MMGRf8LaxjrVVbU8uN/Ez19/Wf2YglLZgNIBK1sTLOS9E971t9DzqgqP7/SOOiy875QkjSyu7LL6ISngIgHnJ5vxQPP+CKKSXexYluTWDjPz20wMDDI55fL29Q6WkSNO1PYoOD9YfU48D/XAAd5BkpfOMrPZewy39fOlO82juVJGnGn6rD+stHZQXF4jks2vWbNas2IlYJMMm1vbpN/8oH3MYWg9Tue4y+FTh9RSggZ8crkDiY7lz/et4olWxUqibnsBy88q+NpsURnG8Q0TK8yyrzweb4S0f/qNaytCjTPWBkeWMxQl+8fY5xK3pAMGWTnUUDrhyde/U4YRhNc/xzFJmikv5vTy3/NNRDhd6NUvkGiq1MVI39zV/9Uj4RYCfCbAAH4eks60LgqAAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 93,
                "name" => "Heard Island and McDonald Islands",
                "isoAlpha2" => "HM",
                "isoAlpha3" => "HMD",
                "isoNumeric" => 334,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MUZDNzdDNzMxNzdDMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MUZDNzdDNzIxNzdDMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQUJFNDBERDA5OTRENzczODNGRTM3NEUxRkQ4MzI3NEUiIHN0UmVmOmRvY3VtZW50SUQ9IkFCRTQwREQwOTk0RDc3MzgzRkUzNzRFMUZEODMyNzRFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+8znuKgAABHNJREFUeNrsU1tsFFUYPre57M7sZbZ76x3aQtGiiCAFDCYqilVLbSASbomKKMQHL2iCmkjCgz7pi5JgMMRo+mKCmgKJEjDFWiOptQLlEqy0lLK07HZ3Z3e7cz/j2ZaiENEn3/zmTPKf+c/8/3/+7/thzwtvL396ZY9ctuurs8c6jgPM7312wfN793Wta98wiK+N5eaEuZel4ktNgcuza9o7L49fmghIPJgCxqio62PXJmNhn+zlbYdOf6cu4jiDbO5zVv1yePeGe44+Mbfjvrqdhwcd18UaRUjwVIRfc4pvNEditZFP7OCeX7PjI7pf9Fsumg5RmHQiZYHWllkn+hOXRzMekUAA3FJoDjoYtTy1wHiu7YAUyv08sHF+4Mz+9Y+vaBxRr1bOina9+cD76xrjr6w5+tjDvROFxVw+XsaZtoWgPb3UXH5eQ+CDXe1N9f5crkAdQzd0CCwMLQwoHF/0ZDRpG9Qe1ZGWycX9PDswXrQUgjyE5KqqzFQmVswptYqNhAdDC89zcpia01Xbtuvzk/oaZfBiZtJwqENN3RRFDhOBgzpe2/yISqVeV1Bal8crQ74yGcyflzk34GteHHtxtXFhuKgEflu8SEWiGg52Up9KkQhd12UXd3keFYr2wPkUJiiVyj+zYckXH2888v2FwaGMz4tIm12jrGnesbJhddQEJ85+hpUaSVxy4OvTq+7/ceeWta9vinf/dKbr93dSTYOcKPefCzkmEvnkRD6XN6orFZHH5VGJdRgRNJbInDw/ppkWz+ESyW9t3bav0bNsaOC7/cd39KvvHRtZSrPLLpy6Wlv36LvdR3pHYnNr2+vkLUIucHKwJ2UZBKvZ/PrWBds2L/2hb7io2RxBrguVgHdoNL1nf7djA1nyImCj2ZPq0Q8PbfnmSlsq2Hk2HdWLAxP2PrG+M4Maw1Lv4b7WrZ+v6dUPxeuCvKtQywawkLea5lW0tdyJS+0uCY7JzsPjJfdWV8SChGBKmUwAjDS9mhSisqPHeeCRBEMzhxJZPKfaHB6rkogSCZimlUhkVdPxVEeqOcBRapd+dCBEjgMJAa47TaoLIbOZLl1KiYB1UhHz+zXKSGXOfNGsLJerapW7myqTdyijVzJDo6pX5MorguVTo8AepzQpUNMBS+n38S6ArHDM6ofAcQBG1/MwlEQOZza27VxN5js+2rR0YUMyra7b/mmhYLLQ4Gaw+wo8ZgshpOnWeFINh3x+WbRs56/H0A2rJCcKYmU+2y5tGT9+ryB5yHTj/hYT6WJdTbDn4PaVK+YmxtVbvOSGBQGUJP7icPbLQyfZyB789lT/6YQkC+7tQzuUSZuUR/xMbKzvU0MOwcwL73po96TGM1ZLWwgMwwmFPEOX0rGoD071EcLbRQYIQd2yclnNK3lkr0CpM9MxIiKTcFBjKTG8XprsgemJtCIDU8sLAiEI/ANYUg92kR/xxGJC/rMIhDAySMHwQRZ75tKMZV4Qrtsu+BdMHSAc44hVepNHozwC/xn+D30L/hBgAKvpBE5JTVQ7AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 94,
                "name" => "Honduras",
                "isoAlpha2" => "HN",
                "isoAlpha3" => "HND",
                "isoNumeric" => 340,
                "currency" => [
                    "code" => "HNL",
                    "name" => "Lempira",
                    "symbol" => "L",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxRkM3N0M3QTE3N0MxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5NEM5MDFFMjE3N0MxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjFGQzc3Qzc4MTc3QzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjFGQzc3Qzc5MTc3QzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8++ppqvgAAALNJREFUeNpiZHDfxUAbwMRAMzBqNP2MZvz///8AuPrS1Q/4NV+98RGf9H8c4Pj1D3J+e68++IxLwdM3P+T9924+8QqXAnwB8vXvf25mRji3ZuGd7z/+9qSpM8LEvvz5z83CyIhDOwseDyGbC3TA1x9/P3/7C3QIXJSHhZGcADlz66NmxMEbj7/iUvDi3U/tqIM7z77BpQCn0UCw4/Tr/3jB9lP4FNAw8TGOlnyjRuMFAAEGAEq7Jwn/ToVjAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 95,
                "name" => "Hong Kong",
                "isoAlpha2" => "HK",
                "isoAlpha3" => "HKG",
                "isoNumeric" => 344,
                "currency" => [
                    "code" => "HKD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTRDOTAxRTYxNzdDMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTRDOTAxRTUxNzdDMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iOEM1NEUwNEVGRUE1NkNERDAwQzk2MEExNjM3RDQ2OTEiIHN0UmVmOmRvY3VtZW50SUQ9IjhDNTRFMDRFRkVBNTZDREQwMEM5NjBBMTYzN0Q0NjkxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+OzkpswAAAc9JREFUeNrEVUlLw0AUnknatGqhShGKIErVizfBsygVL+JFRFC8uJw9KOJycbnpz3ABF9qKeKwe3ZeT2NbisQoqNmm2mkyeY7RQEJO6FIePxzCZfPPlzfdesIGKNZiiMSNH4VtxbgJ/q5ry0tTpNJaUIoz/klpDSO3rVUOhjMdD8r7AahhfA3KgYuWuTgkgQ1FTTcxFww5Wz3QTYErOJm6Ex0cRQGtroSskd7bF64zFjand3dmJcb2jQ+c4cp9ifT60vS073crWpnZyLA6NaNaZsThWGRyWAUReyKys8fML4u5OOtgmAPA0Lbyo0ri6rDMM/EA1ioQILxDOhQf6iaJoo6Pg4F4mp1yRCJZlfWODRKPgdH7P1+/OdabTEGzFm1vg9RqNjcZ9qozBantQiSVKpmfYeMxtSoMfmI9FyH1+ydTVG3OzXp53rK3rTc0efxU3PQkNAWxu+JX5VH+llExKD09v5ltaVI4OhNSdfHur2znEhlqpKBfOz+hlPgNIh4fZQEDa36MWVOJxW2q7HsKy6PTCkRGMqysYH2NUjfT0osQ1Gw7/qhrzC4d8zBnqZamu9sXlsi1IbBTQmCAvFt787JsqfIr//ysoIvWrAAMA7mZQmc9OmukAAAAASUVORK5CYII=",
            ],
            [
                "id" => 96,
                "name" => "Hungary",
                "isoAlpha2" => "HU",
                "isoAlpha3" => "HUN",
                "isoNumeric" => 348,
                "currency" => [
                    "code" => "HUF",
                    "name" => "Forint",
                    "symbol" => "Ft",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5NEM5MDFFOTE3N0MxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5NEM5MDFFQTE3N0MxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjk0QzkwMUU3MTc3QzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjk0QzkwMUU4MTc3QzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+JU411wAAAFRJREFUeNpivKSs84lhAAALEPMOhMVMDAMERp7FjKcZuP4PiMX3kzMHxuL/QDBq8ajFw8pilvgd3QNTgDBUqQ+MjxmE5UcrieFtMagF8nkgLAYIMACetyMUbe9cBgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 97,
                "name" => "Iceland",
                "isoAlpha2" => "IS",
                "isoAlpha3" => "ISL",
                "isoNumeric" => 352,
                "currency" => [
                    "code" => "ISK",
                    "name" => "Krona",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowMEUwNDkwODE3N0QxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowMEUwNDkwOTE3N0QxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjk0QzkwMUVCMTc3QzExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjk0QzkwMUVDMTc3QzExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+EtSEQQAAAU1JREFUeNpiZLCYzoAG/jMwPHg/dWtBFuup8/oOAqbGiqfOuNcd2TXnCIO8AAPRgImBZmDUaPoZzcJw5y2WFPL2zdvv/xi4mYE8RkaQ2P2XXxmev2L4/ZcEo8tmxWMx+t03dxvuv1fZgcy//0BiFXkuN82VGAQ5iTea8f/Hh1iEWZkZHr19uXH3i4ZaDlU1menTuBVEGAS5SXI14xURERxS/xnZOZgFBf///v3n7VtGMsL63/dvOEwGhjITMxMonv9///7/P5BPmtGM/1/cwBADB8jDty+27n/Z3sKhrCzd18ejKMogyMXwi5RorD7Nj0X43ffAOHNDNpZnNZUs7Kw8zs4LbzHc2vqAQYCHBKPbEudiTXwcSi0mot+BHmAGh0NLz847s3cyiAiTkq5VhLEYzcIkzMnE8BPkfWAgA4GSBPcdSbHRkm/kGQ0QYAC5z2/IQnQq8QAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 98,
                "name" => "India",
                "isoAlpha2" => "IN",
                "isoAlpha3" => "IND",
                "isoNumeric" => 356,
                "currency" => [
                    "code" => "INR",
                    "name" => "Rupee",
                    "symbol" => "₹",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowMEUwNDkwQzE3N0QxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowMEUwNDkwRDE3N0QxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjAwRTA0OTBBMTc3RDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjAwRTA0OTBCMTc3RDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+OIHw6AAAAPlJREFUeNpi/D/T+D/DAAAmhgECoxYPf4sZ/wPBQFn8CUjzEqvh7cffDAtX32Xg4WZhYGJkZHj/8SdDTLAKg6QIGyn2fiY5qGdN38/A8OU5g5k2F4OxBicD59+3DHNm7CXZxyykKL548TGDmCgHg6mpMoObWz/Dr19/GHbvLmS4desZWE5fX5Y2Fv/794+BhYWR4e/ff0BLfwPxX4Y/f0BiTAz///+jbRy3te5gEBTgYrCyVgY65D/DuXOPGJ49/cBQW+9FUhyTbPGnt78Z9qx7wiAozMnAyMTI8OblVwYnfxkGIQk2ki0emOwk3MExWjuNWjy8LAYIMADBumJ9k9IhVwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 99,
                "name" => "Indonesia",
                "isoAlpha2" => "ID",
                "isoAlpha3" => "IDN",
                "isoNumeric" => 360,
                "currency" => [
                    "code" => "IDR",
                    "name" => "Rupiah",
                    "symbol" => "Rp",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDowMEUwNDkxMDE3N0QxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDowMEUwNDkxMTE3N0QxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjAwRTA0OTBFMTc3RDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjAwRTA0OTBGMTc3RDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+D76wCAAAAG9JREFUeNpiPCeo9pSBgUEKiD8z0AfwAvEzFiiDAYmmi+VMDAMERi0etZhmgOXP+w8DYzGbtOQ/KPsfHUP5H+Of9x/fAxkCQPyXThYzA/EHFmYBPmYkAXoB5tHsNGrxqMXUK7mgTR5eOjd9PgMEGACLNBM7Kx9mIgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 100,
                "name" => "Iran",
                "isoAlpha2" => "IR",
                "isoAlpha3" => "IRN",
                "isoNumeric" => 364,
                "currency" => [
                    "code" => "IRR",
                    "name" => "Rial",
                    "symbol" => "﷼",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0Mzc5RDMzQjE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0Mzc5RDMzQzE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjAwRTA0OTEyMTc3RDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQzNzlEMzNBMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bVy5sQAAAL5JREFUeNrslDEOgkAQRf+QLUSUwsZ7eAoaOo7Aoew5AyWcwwNAsa0IWZRdnC3sXaPZxOxPJltN3uyfn6HD+bTCgyJ4UgD/P5hWlg+w4Bq49m93zA9AE5a65neByHP2zQDxxoU72B9fXcATA3XfQ1ujjIGIt6BdgqQonMDOO1YMVeMI0hpkDbjPUF33+3ClWQbB4LFtcWsaRFIitXZ/EC4nq1+SZYmVBzhWFUgI1/bBX6ovROFyBXAAf0VPAQYAymNH2XVfIfsAAAAASUVORK5CYII=",
            ],
            [
                "id" => 101,
                "name" => "Iraq",
                "isoAlpha2" => "IQ",
                "isoAlpha3" => "IRQ",
                "isoNumeric" => 368,
                "currency" => [
                    "code" => "IQD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0Mzc5RDMzRjE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0Mzc5RDM0MDE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQzNzlEMzNEMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQzNzlEMzNFMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+A6ksKQAAANtJREFUeNpiPCeo9p9hAAATwwCBUYuHv8WM/4FgxAX1Z1I0LLizh+HJ1zdgNnJQPf76mmHDo+MMb39+IsaYzyT5+Oirawxy3KIMMtwiDOsfHmO4++k5XG7vs4sMxYf7GNbfP0yUWSzEWrrp0QmGzsurGTxkzRj6rqxjEPz7i8HOthgs13tpFcOWWzsZxJhZGR69vUu9OE4/0s9QdXYhAxcLB8OWhycYWJiYGTh4JRgufnoKlj/97h7DJzZuBlMFW4aPLOwM1z88Hk3VmD5GS5yjReaoxUPfYoAAAwDKmktuMenkLgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 102,
                "name" => "Ireland",
                "isoAlpha2" => "IE",
                "isoAlpha3" => "IRL",
                "isoNumeric" => 372,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0Mzc5RDM0MzE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0Mzc5RDM0NDE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQzNzlEMzQxMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQzNzlEMzQyMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+fGQZ6wAAADBJREFUeNpiZJiVzIAb/E+dg0eWodkOjyQTA83AqNGjRo8aPWr0qNGjRtPOaIAAAwDApAPmE9cHWgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 103,
                "name" => "Israel",
                "isoAlpha2" => "IL",
                "isoAlpha3" => "ISR",
                "isoNumeric" => 376,
                "currency" => [
                    "code" => "ILS",
                    "name" => "Shekel",
                    "symbol" => "₪",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMzVCNDMxQzE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMzVCNDMxRDE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEzNUI0MzFBMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEzNUI0MzFCMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+5PxNOAAAAaFJREFUeNrEVjtIA0EUnD3vEj9IiIlooSJYCmK0UAloIdhouhSinZBKIZ2dqIi1hZWFQRFBsJWUghaCkPhBBBsl0SIBTUhyBM391r0oClaXTcwNLHss3A0z8/a9I5QBNkDsm7uU2d5aZ15ZfLrLwxbFyKl8b5LvnTMosraXKPBY3eGWQAhBOqtwWU1YbXERKxqFrlM0OQUuYoE3I//SNQYWYlVkbBG6QRGJpiGpBk4fZMTYAlMcXL1HYMiNIss6NNMJSbSmxbLV5m0fXbxC7CIDV08zlme70egQsHHwjGyyiH6fG7e7w2gQiCWrYRJTi1BUg3qnzunW8cvPWSSaom2TZ/RD0WkFKFSUsSQSaKyoBPKrSmBf0JjlTkn4n4xNq8fDN8i9lrB5mCwTmlavRBIovJXgC8UR3xli56S2xAZjDk60Y97vKRdXePsRBiu46TEPAixvmT3rxpcDNS2uvxhkCnOyhsTRSH0bSL6oQWV5e10S35BY309yNQCzZZp5pjIKX69G1wmX4iqHhCzC47BnLHp7W2whJu8l3ZZfn08BBgBy6AUOH3COZgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 104,
                "name" => "Italy",
                "isoAlpha2" => "IT",
                "isoAlpha3" => "ITA",
                "isoNumeric" => 380,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMzVCNDMyMDE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMzVCNDMyMTE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEzNUI0MzFFMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEzNUI0MzFGMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oQE2+QAAAJNJREFUeNpiZJjk9pSBgUEKiD8zYAM/PjHy8Ir9fZK47D8/OzcLUOQ/A3bA+O/nz79Xnf0Yftx7wMwiJIhLHS8QP2OBMhiQaGzgH5RmYsAP/kMxIXW8hBSgG0oNNUT5gGZg1OJRi0ctHrV41OJRi4e2xYxUUgMGLNAmDy/Opg/EsL/QSh5v0weqDgSY8agD2wUQYAAUKyFbP8LJRAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 105,
                "name" => "Ivory Coast",
                "isoAlpha2" => "CI",
                "isoAlpha3" => "CIV",
                "isoNumeric" => 384,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQjc2MENDRjE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQjc2MENEMDE3NzUxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJCNzYwQ0NEMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJCNzYwQ0NFMTc3NTExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+qvgkAAAAAGxJREFUeNpi/F7P8JSBgUEKiD8zYAH/vzMwMPJxMbAXPWZg5BRiwAd+/P3NILuqmOHNe6CRnHy4lPEC8TMWKIMBiaYH4GViGCAwavGoxaMWj1o8avGoxaMWD7zFLNAmDy+upg8tWh8guwACDACJtBIpqc7c2AAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 106,
                "name" => "Jamaica",
                "isoAlpha2" => "JM",
                "isoAlpha3" => "JAM",
                "isoNumeric" => 388,
                "currency" => [
                    "code" => "JMD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMzVCNDMyNDE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCREQ2NTU2ODE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEzNUI0MzIyMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEzNUI0MzIzMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KsC74wAAA+VJREFUeNqsVttPk2cY/30tVAVKC1SRo3jAAjswnXFL3NwfsFvNlgXn1OjFoomJxgsvjInxZtNh1HnIZHPOw1iyLWEXS1y2zIsly2KiMcZjQRGwA1pBaEsp7ffs9/b9KhQ/AhPf5Pf1OzzP+zzP7zm8NdYe8Q/905sPt8tEVUECAkDU5SUuQ4GXx9FcDIw6sWJ+DIb5EHLhfglabpXgr2ABiuakUJE/lpaerQMZg8FYLkIjOVhdGsGm+jA2LA/DkACD9ALmMPDtvRKcu+PD3/9qBirzE2lF8386oHTUUhEOMsKVjHBjXRib/CG4PNzsKWUYlaCHUnHCQyMR4GKgGGdv+9IMFLo0AzNxQMkYGKd0dWk0bbCpNow5hVQe4sdcogpwLl6A/W808qGSGKAiWX69ZgQfLwmjujCB7qgL10N5iKcc8DINMOwpdShKafBRxIWG4jj2rgriyJourFocQ84oBUyiRsu3XtQ6Uu8Hdn0KbGniUzHxHAP2NaAq0S6HdhEqes9fAr44CVy78Uxdr9fq6cAOYON6PvgUZ8QIMbkG2AUqeochCNOgXQ7hsljkfeuPwKFjwNXr2UzJZDT4IWeOsqj7CNaAdBMsQumHpB5AvrtcLG8frpM3P2+Qr371SbzdSH+TduKRpfOEcqcgKxqf3z8T7FQfpK4WcvZLbVBtlgpYDtCh6B1Dnt52aOc6IMn7lsFByPctkMZXp953WsMZFBVB9uyADCkDjGj0Lg081FD3Y52QCO/37YaULph+P4WcmfTlAKt9mEUmhi7hZ+5OaCP1GIkCofDM+31KrxZVQpoPktZOi2pGLPc01Qn+xu5adUCakwEtk3wMOXUYUrvkBaiuKIMcOsDNe6y89Y4blC5I2xWPrG32y1sssB9+LxLptOpA5TmodUzqHP8MsrTG3nBWO1WV63ba+hGQV80XvdC9yHYCh8DPAS++5kT7s8eNeTlmemgMJ5x4p3wYm+tC+GA5czKPsoNEPlEGJINASyvb6SgQeJA9dKRiIbCTA2T7J8Bc1ex9lkGPNtjW4eUA8eG3rkLMdZqo5inmdOgN1Bjt4rSKjTnwXgUdaAhh/VI6kJftQIoOnDwHNHOAdHTSsMrhtiYrwn5L2Ksn10/tXnyjIux2I9cJLHKPpqOcPLMz79S4jCcdWFMWweb6ED6sfTLuQAGxUDtw5lLmkOjTEyZD6S8dHpy+OR9/dDNCUlrtZoQzOCQyDigGomTg3fIItr3Sj3UTGbAcMFTR2OXQ5ZQ0pY4XOBYzOuqAUSmwqwFDjcO2dvscmrP8IzCRgawaWDYA4/0Ty4auTJPD2S67GvhPgAEAIEVx8Ck6yuQAAAAASUVORK5CYII=",
            ],
            [
                "id" => 107,
                "name" => "Japan",
                "isoAlpha2" => "JP",
                "isoAlpha3" => "JPN",
                "isoNumeric" => 392,
                "currency" => [
                    "code" => "JPY",
                    "name" => "Yen",
                    "symbol" => "¥",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCREQ2NTU2QjE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCREQ2NTU2QzE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJERDY1NTY5MTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJERDY1NTZBMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+sXSjEQAAAbhJREFUeNrklt8rg1EYx79n3k2WrWGY363EkgtJSVntgguJi5UbbuTSrf/CHTeukDulUVJKCRciGVIYiYvNhqZmm9H2eo/nfWlcbW9sk5zT89TpfTqf59c552Wc8xsAlSQR5GYYSPyMwOGPRS5H5O+BeUKUNZhWmxtwYHqBxIWn00vicugbrbAMO1E1OpgdsBR9wYFjCF73IuQYtSghzZDAAwlHVVMPWrfmIZiNmQXvdfbBt72CIjTQSiO78vGFKTqEc1haHOg43FAF1qix8k7Nwp+E4gtUqbYiJjTi9mgT1+OTqnKtCny3sAyBImNpzHVkdb+0mhnw6/MTYsfXtKmZ4pJS9TnVvRgvF36IsejPwVyenCdrmf6cJdXPwEJBIfTNdYgjmMZc7vAQ8ustEPSGzNS4zNkLUUkzT4Fl5Fwcpf3dqhKj+jjttHXh1r1OnW1T0v/pBFNmCB6Ybe3oPNlVE476cywGw9i3D8DvWUM+de97s0G5QOKky2vtaNt2QVddmvkrk0sSfBNzCMy4EDu7oqaToG+wooKuzJqxETAhL/uPhPgYlT2BYDLiGyPyP9/jX/n1eRNgAFs4yk+Ai07FAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 108,
                "name" => "Jordan",
                "isoAlpha2" => "JO",
                "isoAlpha3" => "JOR",
                "isoNumeric" => 400,
                "currency" => [
                    "code" => "JOD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCREQ2NTU2RjE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCREQ2NTU3MDE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJERDY1NTZEMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJERDY1NTZFMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+QGsVzQAAApVJREFUeNq8lM1rE1EUxc97M5mZhExNG6F2oTRtzKapdVWJICIuVCguigtB/AMKUlz0X9FaqQtB6S6IYkMKIoJIRRDUhSt14UcVTWLSNsl8ZJ53Mk7aGLtoMu2FSZj3ePzOOfe+YXf1w1/nawV91a5iP4uVDqaFJQRyZgUL9SJeWJv7A37Vn6pojOkJrqAqHDwkAYv1AlatvU2AvSYw/esN+tHAkJA8AcvmelPA8z1KoAX2F3wBwyTAoBY8MstYqFECAc9AB/h/AmqUQNAzsCO4TQCjFvydgaWNNeRiGpSp85AjGswva3AMY9dguVMKg1OtQdg2JD0Kid7dqX/fMJoJXNHiuEwidDWO8OwMkEwE5NgBBDljnEGYFpim0YKzdYJzcNOGsf4JHCoGpqcxNHcN4cyJXYH59hdhWbCqvzBy7wbGP7yEejQB6/f39hOOA0fmUOIp8MggfmTv483J03g7NonCUrY7MCUJAXKpKpAH+sEkyVvcqSROuzKFZMAuFCHqhhtXF1FTP4VlkhwJ8oE+2MUSWEj18m+doKhtQlU+NnXHzl7Aoesz6Js618NwkVquaGhsbMAof4YcixFIuDHAIeMy8YdKm2SUI3rxKtS5WeDU8a6Gq2OqhdOg3oWbjzdrAjKBEyW72ZdsMoTlM0egXJqAEn4H60EeTr0ewHXy7y85VMjhCAHdepLUsDgZRW6CuiLT7X58E6Br1211gP1IXaA7JvmUhjsEXBmL0JeEPBctwKad6CA96B28HejW01ENtzJR5McJqNBmiVxWGt6Qs94/mbIP9HuYT4WbDvNp6rFKm8WG5zAgYOty/OSjIuSIrR4ei3hA16ElAoW1OX42rH67nYnqK2m/hxR1OXiH/9YfAQYA1jgMxWJoPhoAAAAASUVORK5CYII=",
            ],
            [
                "id" => 109,
                "name" => "Kazakhstan",
                "isoAlpha2" => "KZ",
                "isoAlpha3" => "KAZ",
                "isoNumeric" => 398,
                "currency" => [
                    "code" => "KZT",
                    "name" => "Tenge",
                    "symbol" => "лв",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGMzkyMjdBRTE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGMzkyMjdBRjE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJERDY1NTcxMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJERDY1NTcyMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+44EnowAAAkpJREFUeNq0lc9rE1EQx2fee7ub3cSyjbXWIgFFS7A9FC+iVEWk9KQI9qIYPHjyJBQE/wSxh4J48qpQwSL2UFA8tPESUC/tRVotKCX+SO2WpMlu9ufzvRybZCOYDLuw7M77MPOdH4vwugC9MQI9M4lWIKAQAWCX0SaxF9OPcsZ7CNPdRTMKYZYVB7ACnLV348A14AmZGdbl9Q8psjpX37rjG8GRxoGWFgE3EOvDrKRAWAxNn6cA7Y50ZqB3mFiDtALIG968KWLDJJWriUJOX2Hov3AuvnQuWJEJ6HRAB0Bdrnqt1RDx6hDpp7TV+8nFMUO26VFS3giGlp3zQKIGvW2PEZdLdFXoyGlTyFQTiRNH4EeVLeAIkXpC+d5HPPFSlVwa1yEGumfV9WO0JJtwn4XpW0b+wYH5Vf/4G3dcKCNYy+7pT97ITGrhhp6HsD++jNpH/+S38BBA0PSVOpxeS3x4WJ2+V75T8LIKBvPOxK8oedtYeVybAq7GoRmKYeE6eoDB/jKSnQV7aoT+vptcemZfmq1eN9G2eCKn59+5Y6/sSaBWHFoBPynUFOhmQ9+LUk9qV86omxPqepZtbQaZc+qazbW5vZu74UGg2zFyM+FXCs3tqK9V5xEg1p9wYMkZ7ic7GfpzVP3sAz63LxeDDLAfjcq3j1pDf1Jb+yJGJtKBlJvlBlID2NvlxtPqNJAqhINyXjpxJZoDfg2GLDlgftwWk/PtygfZS7wjV8op9nUS6wEwlystRvF/1pO4a3LvQHe5vf8V9Mj+CjAATRXoTFoxM8YAAAAASUVORK5CYII=",
            ],
            [
                "id" => 110,
                "name" => "Kenya",
                "isoAlpha2" => "KE",
                "isoAlpha3" => "KEN",
                "isoNumeric" => 404,
                "currency" => [
                    "code" => "KES",
                    "name" => "Shilling",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGMzkyMjdCMjE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGMzkyMjdCMzE3ODAxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkYzOTIyN0IwMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkYzOTIyN0IxMTc4MDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+4JAoOgAAAoJJREFUeNq8Vs9LG1EQ/jb74o8kdWOktWuKSamUtoqnekmRQA71UKjkIJSeEqgtmEIv/hXBgz2mpRcRkh6D1IMVSi7iIVBBPFRarIamIsWsRpq4yeY5m24llZa89JCFb9/MvmG+nXkzOysB+EYYIBTRnusSIS/R7dhS2nkVhYllWYYkSahWq3/dZ4zV13/tXySGRcybQVVVHolEznXJwm89Go1yv9/PRXxZnGLEJlKpFH8+O1uXX4+N8cVA4BdpLMbT6bQoaZ2Y9fp8TfNipvgwl8PbpSU8mJnB1NwcnoyPA4qCd2truBsOI5VM1m09fj845019si/r60LVYLfbodE5VohohPRdtxvM48ELki8XCng4P49X8Tgqui7kj+WDQeFSdBD56v4+nCSrNhsljWOP5I/T0wh5vTimwhKJtk68u70tFrG1viQ8JgQyGVTpRfIkv9E0eAkOkk8Fg2AdLXT9AWGL8J3QPzwM3tmJ/PIyNkk3CC6rcoSIW+l63rDaKFpOZ95IJLXgi1UEDU8I3YRb5vmaUWezqBG5+a29Y0Vg2oj6Y1f7BoTf0ikzxH4eQTk5ghwKwd7TA3VlBc8cLlx3eaAbVfGIb25lhQxtkozqlV5Mvf+AnfsTdKYcEqV6kPaCiwtwhSchH/xAjRtixLcXJoQMy0YZ1xQfPN4R3PAAiRwltmBDnKpus7aKYiKBr4ef4WAOIX8SHglOpzIwOjiKQtcpcjufkCzdg6PGMNmdgTo0hP6SCxt7G0CX6HR6Kj4WFUlBSS9Bd+tgGj2o0TTqox4v2OHscELjWgtjsQXi894x+0a2ZOPCc0Hilvr4D8fGfzZwwwekaEXczl+f4pkAAwBDiPf/eEPUkQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 111,
                "name" => "Kiribati",
                "isoAlpha2" => "KI",
                "isoAlpha3" => "KIR",
                "isoNumeric" => 296,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyNzJDQkU2RjE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyNzJDQkU3MDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI3MkNCRTZEMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI3MkNCRTZFMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+pmFxjgAAAoFJREFUeNrslE9IFFEcx3+/N292dmf/NLppIWJpCoKVCcUWIltqVlAQdOlWdOkWnQsEr12DLnXp0rlDkl7yH5HQHxLLKNTWrDZaXXd219kdZ957vbFT2k6HrVtfHrzffHjf7/B+82NwtP6QzhyoJI6izsGAgEXNtRVCXPWgwyOMpxX8roIiKvksRaV1jgU+cpBxR+yzw5eyIUaJwcsWL00GWUojLgBWjA5zh64TVW4VowOCpjRnSYUa0K9Y67O0cD+CMzoJcukGXtEnY3Eq3uEXjd7CnGIz4tINcEEFqmrCy5USftEU/CXNBeIYjnG1oHe5XhNfq/m7EbpCweD+1j9Fl9EJ8NpBs/6irRYpEOEmLbWRrd7YQUsKBIVfdDk3rzJKtbh8cO0sg6IsFAj/JGXbpG1mrBeUrxBSorI7VjYfTcJKc8Z6FQsyQ7Zru0uSsuJg/vYdnHiaGpkQnO852RM72weEFB+PeYTxxp5kzWA3dDuwNg7fxkBw2J2E2l6YCZo3ny2PjQvY5trMwb4eHBpdGGg1jmoFEOKFExtNFTmHgeZIYpO85NGRNyU7jWcS+rH9puz983fG8HSJxNmpTj1Bi791STIyb2L/9QeLmXUwDDkILLfW0RAjBN9+Nn8hiLMLeYY13sjwtQMtUe/MF9PH1bJTp9cuHG5uMBY/ZzkXrU3xdKYghGioj1VJltImdl2+B1y0743L1859XKEK8b6Dy6sk3v3ml1dLtjs980lwkehs2lUbljSTs6okekhFo//W6e628yfaEfHR5IeHT+bkjc4db6+SDE+9RzgyBGYJyhvelGvyX6F7Ra5qYugU9IC3tij0FwiBf6b/0Vv0Q4ABABynxPRdAJihAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 112,
                "name" => "Kuwait",
                "isoAlpha2" => "KW",
                "isoAlpha3" => "KWT",
                "isoNumeric" => 414,
                "currency" => [
                    "code" => "KWD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1NUZGOTk4RTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1NUZGOTk4RjE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU1RkY5OThDMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU1RkY5OThEMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+rNTKhQAAAX1JREFUeNrElktLw0AUhc8kadMmBhcttN1Y3IkguBC1goKPRRHxN4jiThR0r4KI/82NOxdaEZWKD9Qi9pXX9aatIhQhixm9cDfJJGc+zpnLGJgcrWJz1UExD9w+Al4AaAKqK1IgWClgfQVYngHqTeC5BuiaUmGdew+eb+L0HLh5AEaKwHAB+OAN+EwvhDLid27n+4mVBjaYvjwNNNvA05sS+n7hryqNdTcwlFPi/e/CUdk978sl4KoCNFrdL5QL90qbG8fS/q5vhiAKQvFnwiZ3q1YnDFoEScyxUkPJJF7dhuitFzI6dlx9CqWmWsM/VXxhyWfZiLPIDT1QJkud8SopXLGEF3UH4dpOcGcmKSBSf5wMfn1g57CVyqD6UuGFnvrJtZAYwJGdx1TCwkXQRp1TrUOo8zj6eSQYUbbZ0jO/yQkUUkX7hOeZ8phFJ5jykikbkin7LgIR0SF7uZ3O8qAgXIduh1LlPcSYTdj3J3bBmepQuuxloIzyZ30KMABBFnf5qa4SdQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 113,
                "name" => "Kyrgyzstan",
                "isoAlpha2" => "KG",
                "isoAlpha3" => "KGZ",
                "isoNumeric" => 417,
                "currency" => [
                    "code" => "KGS",
                    "name" => "Som",
                    "symbol" => "лв",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1NUZGOTk5MjE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1NUZGOTk5MzE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU1RkY5OTkwMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU1RkY5OTkxMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ZagutAAAAeJJREFUeNq0lc1qFEEUhb/bVT1Dz2gkShZRUQP+LDRushNXvoDuBcGFa0EXbnwIX0BcKAr6Ej6AgpsoYiAoKgnqxskkM9PVdb3V4nKmZwan6UUXRZ976px7bsnO8jqLefyxpTWR7L/jqkafZX4RlI2uN/ipWFcwEgRyxU3Huqk6BBgIVv2QEmFfUplCyUnLCVo34I4MWjhesV7KpWDf+t7z0fPVpQJtReeDDjXu+SA3DzgV+OGsmFwu2XH6vOCDp5wEMFFlE3e1klv7xk5fdBiKFdNXhekgt/ucrChlLkHsvKbmxcBh5YuTtaBPO0SVa0N6wopi9O0cYSzGeNZRjKxcKPnsWFZOV3QjS8pq5Gjkk5eNkiJSyeystX5bqTH0ZcFKJQ97tNFHXXYzuT5INkht9cxaZ5r6wZrhXGAAW16ujGRjyHdHL+Ns0HeefobT2VnbzgG8zblayt2+Pu7ogyMJqFC50+enYzNPxnZ0rg6x4O06fVaYb3J/j1aKjNzbM4f1SZFauzUpMxLO3Bgb9L+RMaNO1JGxbjHpN72+yfnmUtzHR6Yp6PZbG8rUfIalrzUVMxHKGjRnQhSb0kg9JYxdN1maJgn/ljTgJuhph7VX/IxDNcawqKvg1+/tBV1gfwQYAP8yvkoLLzQ/AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 114,
                "name" => "Laos",
                "isoAlpha2" => "LA",
                "isoAlpha3" => "LAO",
                "isoNumeric" => 418,
                "currency" => [
                    "code" => "LAK",
                    "name" => "Kip",
                    "symbol" => "₭",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5MTY0MkY2OTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5MTY0MkY2QTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU1RkY5OTk0MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjkxNjQyRjY4MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+vPM0fwAAAXBJREFUeNpiPCeoxkAbwMRAMzBqNBpgMRJ3AlKC/35hlWZkZGBgZv7w+M2/bz8ltOUYmZieX33IxMYqIC/K8Pfv///o6t8zsSGMhgqx8OCwmpnhznN+LZX2niR/aw0mRobtJ++Ulc57c/4ug6o0w5+/6OqRnMjIoJGB00tANz99q6Ald3l/Kw8Hwjm//v7Tc66+efYOg5wow7//ZIU10FF//zaVByObCwRszEwd1eEMzEwMv/6QG43ffjDLiztZamDKOJir8ShLMnz5Qa7R/0FBwgiKSoygYmIEif//T67R3Bx/Hr4+fPoWpsyRM3c+33vBwMNJrtHA5MHA0NCz7g+G66o71jD8/M3AzkKE0UysWBAjC4OS9I2T92TtalafffAFGPgMDJsvP5F3rr944DqDigwo7aJpQQ40SOLj/P8XR/Jj/M/M9P3BS4a//yUNlZiYGJ9euAdMcByK4kz//v/H8M13RmaE3tFCdTgYDRBgAIXydkRRgS1WAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 115,
                "name" => "Latvia",
                "isoAlpha2" => "LV",
                "isoAlpha3" => "LVA",
                "isoNumeric" => 428,
                "currency" => [
                    "code" => "LVL",
                    "name" => "Lat",
                    "symbol" => "Ls",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5MTY0MkY2RDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5MTY0MkY2RTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjkxNjQyRjZCMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjkxNjQyRjZDMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+92Hw/QAAAF1JREFUeNpibBNT/sQwAIAFiHkHwmImhgECoxbTL3F9f/d+YCzml5MZEIsZf37+8n9ALP4PBCMrcf368nVgLJ6sbzUwFn989GRgLOYUEhwtMkctplkL5PNAWAwQYABbtBShZM+mNQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 116,
                "name" => "Lebanon",
                "isoAlpha2" => "LB",
                "isoAlpha3" => "LBN",
                "isoNumeric" => 422,
                "currency" => [
                    "code" => "LBP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5MTY0MkY3MTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5MTY0MkY3MjE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjkxNjQyRjZGMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjkxNjQyRjcwMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KNMpfwAAAapJREFUeNrsVd0rg1EY/x2bNWab0aLEBWIkn+XChVJSSv4AKXfyH7hyq5SvcqeUK5I7VzIpLnxESzOTYhOab5t3693hffc+TnPP+5Zxs1+dntM5T8/v+TjPc1iqpZTwD8jBPyFL/GdgWvRFEtKuT53AcnIBmwNjO1OotpVgqH0QkDmIx0UYJr28cRCRRAbhjZ4RFlsJS13U65uhGKlGTUiGUx1O3GF0exJ19gaUMSe8vlWEX68yX+MJ/xIu4xHcJO5FtjR4imsxf74GRVMN2TEbUQ5Ew0ioHLL6DpclX5TUgsBTEIHbAzS6qjDi6c8M8fLFOnyPpyjKKwQXDihKEg6rExXuepzEQuCpD1iFM79K7I9eQjFb8fB0BkkQVJd3ICSiJVIR4G+wiztJkX+feP8xCL8gne6ZwLGQs3uzcLjrMN42nK65U6R+M3KEgcpunZ2ps504aRQiOb2fu9ogzHsIC00UlK7TZymxDpMRklSuq50YJeWfB4h4vRARwcSwFd7FM4+hs7QZ06crqCgowXBNHywWx1ePKBzQhD5j3w4Qlv0Ws8SZwqcAAwBwcmoqHHZSowAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 117,
                "name" => "Lesotho",
                "isoAlpha2" => "LS",
                "isoAlpha3" => "LSO",
                "isoNumeric" => 426,
                "currency" => [
                    "code" => "LSL",
                    "name" => "Loti",
                    "symbol" => "L",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMDVDQzk4MDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMDVDQzk4MTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkMwNUNDOTdFMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkMwNUNDOTdGMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0nsttgAAARhJREFUeNrslc9Kw0AQxr9Nd/uHNFSQHEqoKCrWIsV3EHydPEz1Fih4EfIMOXnOQTA3D1FQ6ik2K0hJ6o6rB8/ZHCyUfDDsZWd+sx+zuxz7c8IGZGFDasDbD2ZLWUi9OlUThBBotYDZ7ApluYbv+2CMoShKE+4HHzjCuNvnp0ck93dQSmHxconDowl6HbM6vI5Nijg+V9An/tKedWpZXQvseR7iOEae53Bd9/+GKwxDpGmKLMsQBEG96SIiSQaKoog45z/P7F/oRshQ0gi8zN7pYDgip92j6fGEzk/OaKfbpz13SG+vCyMwL9S6kjNti0PYXQS3N0iSB0gpYel7Zds2Tsdj9HcHv/uq1mO4vmh+pwa8XeBvAQYA1mkQ97LOB/MAAAAASUVORK5CYII=",
            ],
            [
                "id" => 118,
                "name" => "Liberia",
                "isoAlpha2" => "LR",
                "isoAlpha3" => "LBR",
                "isoNumeric" => 430,
                "currency" => [
                    "code" => "LRD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMDVDQzk4NDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMDVDQzk4NTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkMwNUNDOTgyMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkMwNUNDOTgzMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+G0pyRwAAAdFJREFUeNrklM9LVFEUxz/3vZlyZnyKNNXkGIhDmZtEaKEEZuBiFJfSJpyCgpYGEW1cuHLTP1ETCOqiGYJxIUgMtBpoIUVgPxQdTcksnljjNPOeZ17TIghqcfEtOnDfu++eCx/O93zPU5wf2gAsfkWlAqYJSv1cEpnVIgMHJdYx0BUBWa3ergZxHCiVIdQgGYFU5VuOw73dBIIBGl1XI1ipPUzDYn0LvpVI3btJemYeVopw5qTcMOl4niYilyOgEVytova/k7yWpL0txsMHt+hKnGXp5Rtmc3lJO3xJZ6nEopS0gkVLt/yDyqddJqbuEhGZ798eZezOJG5N2tBxPtxIkcDmPUobWNE5bAvBYvk1T7KP6O/p4m1xi8ErKYi2iL4hXlwf4VJLE3saK1bialsMZUVbTzHY183cTI7kyFWBb7OcL4AAV9YWaUdvKOIDtjwtz9UfdzxpPWefaJZsbXxcFnovcjncwGetPT4WrG+ln22nwaiNVX1sTMM7tp/mKIu1dnX2+J29b//2A/lDxF0HmWwcnVKLc/8Kpg41dEr9Sl3AjwhYQ/2+gP9Zau0Vf32c8afiAsqfis9NP/vPerw2Om75UvESiU15Nx41+FCAAQBN8o5alKABbgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 119,
                "name" => "Libya",
                "isoAlpha2" => "LY",
                "isoAlpha3" => "LBY",
                "isoNumeric" => 434,
                "currency" => [
                    "code" => "LYD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMDVDQzk4ODE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFMDA0MjA0QzE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkMwNUNDOTg2MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkMwNUNDOTg3MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oNTByAAAACVJREFUeNpiZJhqwEAbwMRAMzBq9KjRo0aPGj1q9KjRtDMaIMAADHgA7Vx8bugAAAAASUVORK5CYII=",
            ],
            [
                "id" => 120,
                "name" => "Liechtenstein",
                "isoAlpha2" => "LI",
                "isoAlpha3" => "LIE",
                "isoNumeric" => 438,
                "currency" => [
                    "code" => "CHF",
                    "name" => "Franc",
                    "symbol" => "CHF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFMDA0MjA0RjE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFMDA0MjA1MDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkUwMDQyMDREMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkUwMDQyMDRFMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+aIartAAAARBJREFUeNpiZNCuZ6ANYGKgGYAZzfSP4Q8bww9mhq+MdkrXpXlfMXxgZ/jOwcD8n2yjWaD0T0525k+V/ruEuL5fvv/fQuqfig/TpP12V+6oMPB8pczor1yqGje89e79ZBD4+e27vBSHjdb7C4+uX7mswcDDyMDwnwKjmX5+/iIgIi4kJ8umqcbHzsbA+e/7p1985BmKajTf94dvZIsX60lwXn736jsXN5OEpMrWG6YMPD/INp0RmviY2RiYWBieMTF8+szA9JvhPyMDGx+DNBsD2zeGv6Snon8/Ea7m/vOV4f9/BhFmBjE2BkZWoMj/f98Y/n1k/MVIhnu/gk1gPCeoNnSzzKjRo0aPGk0UAAgwAEdbUI8xqJtmAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 121,
                "name" => "Lithuania",
                "isoAlpha2" => "LT",
                "isoAlpha3" => "LTU",
                "isoNumeric" => 440,
                "currency" => [
                    "code" => "LTL",
                    "name" => "Litas",
                    "symbol" => "Lt",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFMDA0MjA1MzE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFMDA0MjA1NDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkUwMDQyMDUxMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkUwMDQyMDUyMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+dE0CWQAAADFJREFUeNpi/LtTmIE2gImBZmDU6GFhNAvzRsPRABk1GjdgPKiuOxogo0YPiNEAAQYAG3sD7LiMQpYAAAAASUVORK5CYII=",
            ],
            [
                "id" => 122,
                "name" => "Luxembourg",
                "isoAlpha2" => "LU",
                "isoAlpha3" => "LUX",
                "isoNumeric" => 442,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyMDQ3NDU2MjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyMDQ3NDU2MzE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkUwMDQyMDU1MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkUwMDQyMDU2MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+HeS18gAAADFJREFUeNpifK+ty0AbwMRAMzBq9LAwmvH///+jATJqNG7Awrjk3miAjBo9IEYDBBgAMnEF8ALV3YIAAAAASUVORK5CYII=",
            ],
            [
                "id" => 123,
                "name" => "Macao",
                "isoAlpha2" => "MO",
                "isoAlpha3" => "MAC",
                "isoNumeric" => 446,
                "currency" => [
                    "code" => "MOP",
                    "name" => "Pataca",
                    "symbol" => "MOP",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjA0NzQ1NjcxNzgyMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjA0NzQ1NjYxNzgyMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMDExNThCNDE1MTY2Nzc5NUQ5Rjc0M0UxN0MxOUIxM0MiIHN0UmVmOmRvY3VtZW50SUQ9IjAxMTU4QjQxNTE2Njc3OTVEOUY3NDNFMTdDMTlCMTNDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bj2DXgAAApBJREFUeNq0lU9IFFEcx3/vzZuZnWXXdQtX1z/bWuimGIFU0EZUqEVgEFRQF0+JVws6ZRGBBUGnTh0CIfJQhzynsUFiKFaHrBC2XDVXd7F1c2dnZ3fnzeuNXoIabc0ec3j8ePP5fX+/933vIXgI/2ngklajtW+70RypA8uVgMebEw2AAoApHKmTzza6JBAhb0UQ+ke0AbVueb/PCau4p4k8Og6CIda55NZKGaiwVTQXxQBW4bBP7GmSABe7RrI1A6qW0tqCuLtZYRmTJ+bakS3AziEMnKIUcpHZLNPyBYbJxd3dPjnwePp+Bv2g1Ah6iF6ks2oRBGCsJNVF8Mv4VqsIOKenaW/TlYH2B/eOXnvSPuhV/HrS6G0Wz9c7mQbMBkBs0RLMZPVLEV3LWxVUlFWuh/eWh5a1BP/v6ptMgffCBXZse9UMkCntlEI3DtwO14fHFycoozw8Gh+tFqpuHrt+wn/O0B2WTxgqsdcatPhaJi5MKKKiF9WR2Ku2+g6FyC++vAy66xp9jXxJ31jfndF+Vl6qahGW1KVoaoZPHaKrs6ETMXNRTZzc07bOBRM+rk4xe4AAZ2y9g4G8TYxLTFIM1wpNDX0dGpkb3qGU0wKdTIz3T96NxCMqVu3k2W+jCV63J+RuGFt4/W7+/TJOz2SiyGQfUp8DjhqCCE8ccOxK5hImYn/cSbJBPXF1YcWbOVV1mtK8pMa8gseK4kKtw18h+6ez0dlczARm5xC0yaWqwUFfa1fwcrj6UJnTKSCcz9Op75+ex54Ozj3jibhHeX1bQq/RwQHtNR37KppFQuZXvg3HI8vppAUltqb+OzR3LTd0du0KZFZLQAGQN4Ju1utfzo7lAfdvwe19ZUoaPwUYAKgQ8M86ceGrAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 124,
                "name" => "Macedonia",
                "isoAlpha2" => "MK",
                "isoAlpha3" => "MKD",
                "isoNumeric" => 807,
                "currency" => [
                    "code" => "MKD",
                    "name" => "Denar",
                    "symbol" => "ден",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyMDQ3NDU2QTE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyMDQ3NDU2QjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjIwNDc0NTY4MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjIwNDc0NTY5MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+x/HZMgAABhZJREFUeNpsVmtsFFUYPXdmdmdn9tHdblsoj7KFgkL5QSQRYggmihhNDCIgGAjgKyo/1BiRP5pGEzRKMPERNeIPgWCCIcZAsDwSEowxMSYkgElNoRRoaaGlu9t9zaMzc/3unW0owqQ3nex9fN853znfHebdaC4VTujofyeFiXEFRrsH+XDc8wQ2A+hvwY9FJJZOgE8Adr+K3q0ZeEUGNXGfTWILDeuKCtXgyH1WQnatA03NBMmmF2uIL5mQwQtndejNPrQUB/fvPkPROZxBFTYdklzpgtG8fUmDe0OB1hTcG1MF/CqDfVNFwzIXcz8vwVzuAg6DUiS0KDMYDzlY1J1H27tleHkF9jVVbhQZT81eJCNQyt8jdMZ1Fb7FwrVTUWo0N6jAvalg5hsVLD4xBvMRmzYzlM5Eofy7MYPeLWkUDpsAUTF7TwGdx/OItfuo9mmSTqbcjcIilLxCp9NcrVf7Hy1hcrXLGiItARYeKSD3TQFIByj+YuLStjR61megRVt8jB2LIX88hswhG9NetpBeU8WSXgd9W9O4ddCElggQnR6AU/kVk8MdIJTEkkrsOldVKEZYW4FyYpRQklZa1lmYf5gCqj7KJ03c3GcifzSGgJLVZwRg53IPcoEoENRT/cTmzGoHs3ZVEF9pYbzbxOXtaTgjqhRe4FAEj2HxmTy0TIALK7LwK6Gw7H4NWkOAju+LyDxfRe3vGAY/TqDQrdM+BfpMX+qEU8KSJ/EiAhpzfVrAkP9Nl4uzGyzM7ipjyflRXN2Vwq0DJgRD7oiCYncUapLLegtKLQravN5Cbm9JlqdvWxa3fzLgE0sxQqgYniwBr2tQs4kqgVjUjkW5zEjSSotGfzYxdsTAtNeqmP56FakVLga6EogmAwQW7Z5g0LOUrM/Q8dU4Eg+7GPqCaP06Dt9VEG0id5AABUteSQEnUJPBWf+OHPfpR2+cwRuj/wUFQY3BpyEWeiUGi4iJIMCMrVUwlyO7zkbDs1QbOqByJoKRgwZ4hGF4vwkXVBJ4kg0BQmhCpaFlaGQDWQqVrMp4JctpHbgbBgvIGqJRcFK+sIlfVGS2Xp7hNqFvWE7131uG26PJwNHOCQx9kET+RAxNL9iINNLBxIiov2KQGGM8HCRANc4lq5QbQam7gSmUEb37NFiEI9CY9CljgfRlYCtQxKZJawWT9WJhqWhONSgoIdLSPEQsg0IGFUOcO+l/bYCy9cdDSj2BbiykXdAtkAvbWI5G6zla19bgUlMok7CST7myrdZ+j8DuU6UuenZkKB9quxGiOlVHTAEFvZJmkZB4F1T/iU7O6sYXyhZZMj30pTsatqPmDTW0vklBqUtd3Zmk7gPMfK9KaXMM7Q2FlNtD7XChj+FvTYwcoJpTqkJc0qoEQLhFqF0ky2losdydhiwWiSbhUIMQk5knbLR9WIaxwMPAR0nc+DIhD3PzKvmevEv02sMkvOYAvS+l0fpKDblPS5jxdgXXu1LIH9PlufpsH5HIHSvVcdYDBuENYhMqYZtFvxaw8NRtOXdhWRMGKajR5pFSSVBkocbnbGQ3OdDbQlQGARj+IY7zS5tlr3/g6Cg6T+aRftyh8qjybGGlyfarCZEILwty06sctGyxkN1WlaK59lYaQxRQ0QPEOzzJhk/XX4zeVWrtispl4Oq5iKylSb+7wwourmoi9FW07ysitdqS98AIWa1wUpcABQOaQNjwqIuW7RaaNtdIcR5qfxm4/GoDShejMFp96UNev6aF5fRZvhQPIz3E5vgo/REVQpVrhMg0m2pP6MfPRtHxXQmZjVUaNvKHDFn/4mkd2oL9RTQ+7YA1CTMrGNqdxuDuhBREYq4naz31XhaHG/OJ3nhoLVH/qY+kk2ofn+fRva3hnycbMWtnFLO6KmjcXJWxCqd0KNlNFgUNqCFE0bsmg/73UyGSeX4ohqkfFTxsreLKlI+4adp8WfepwkE9WbFOo+vw2idJ9DyTgXMhSooNkFlLvdAfVcuCqiv09WEPqTDneLKzcO8+nz5kCaHgGKHhtdAeMUIfpVtHXpPxuz99xBmiPZoJD4XTMVx8LIJ2sl16tYv/BBgAT2nbpDC92HAAAAAASUVORK5CYII=",
            ],
            [
                "id" => 125,
                "name" => "Madagascar",
                "isoAlpha2" => "MG",
                "isoAlpha3" => "MDG",
                "isoNumeric" => 450,
                "currency" => [
                    "code" => "MGA",
                    "name" => "Ariary",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1OTdFNEQ0RjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1OTdFNEQ1MDE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjIwNDc0NTZDMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU5N0U0RDRFMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+9rDjCQAAAKdJREFUeNpi/P///1MGBgYpIP7MgAP8+/6D4YqOHcPPe/cZWASFGCgEvED8jAXKYECi6QF4mRgGCIxaPGoxzQALMYoYgZjv61+G30DI/OUP/Sz+D8RvBFgZvr9hZ2AVYqOfxT+AqkLa9Rgefpdg4GfjpaOPgWH9SYKDgeE3F8NHFk46xjEorD//ZmD4CcSsv0ez06jFoxbjzE6foa2Pz/RqfYDsAggwAPr+KDn2/fllAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 126,
                "name" => "Malawi",
                "isoAlpha2" => "MW",
                "isoAlpha3" => "MWI",
                "isoNumeric" => 454,
                "currency" => [
                    "code" => "MWK",
                    "name" => "Kwacha",
                    "symbol" => "MK",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1OTdFNEQ1MzE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1OTdFNEQ1NDE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU5N0U0RDUxMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU5N0U0RDUyMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+taggYQAAAP1JREFUeNpiZCAO8DAy8TEy/2dg+Pr/36f/f4nRwkhQhTATix8bnz4LpxAjM1D1x///Lv75vuHXx9f//lBktAELZz2X+Jv/f+f9ePvm318mBgZ+JuYEdiE5Jtamby9P/fnGQB5QZ2Y/IKCcwCEEZNuzcldziQORCxsvkBvOLnBYQAXoFTKNns8rW8olCmTM4JX5L6r/V0T/j4j+fzGDhXxyQMFsTpElfHLMuLXjlLJm5ZZlYm389rKESzSfU+zwn693//26/+/Xg7+/XFl52RmZur+9Mmbh/MfAABTEHtYXBdUZaAOYGGgGRo0eFkazJPXyjQbIqNEDYjRAgAEAy3xInECTax0AAAAASUVORK5CYII=",
            ],
            [
                "id" => 127,
                "name" => "Malaysia",
                "isoAlpha2" => "MY",
                "isoAlpha3" => "MYS",
                "isoNumeric" => 458,
                "currency" => [
                    "code" => "MYR",
                    "name" => "Ringgit",
                    "symbol" => "RM",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1OTdFNEQ1NzE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1OTdFNEQ1ODE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjU5N0U0RDU1MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjU5N0U0RDU2MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+WpzGZAAAA1RJREFUeNrElF1oW2UYx38n5zTn5Lvt+pE13XLhoNPNaZirUqc4BTdBBcdAwSlMFId3YxdjoHgnCIpXohcieDFwYAteCFZFhcKYFVfHKhq3Nfsg6dIk2CZtkybnY8/JWTtEbaMIeeDl5YX3ff7P//9/3kdhz2gWiLAaiuPthT5YNiC0DB0mBGX3r4Dt429DixJayfP9ucMk5Vhk/dBkDaydfBZUgzC7mYf3j/PC41+RySa4cCnJmfQOSgtdUkRdwFXvriYFOYr3tlxG6QyQOD1GTDcxmqnXB66sMW7owrSfd06+xfEXR3n/1DNMnr+HuapOh18AFRvqOnpkgYalYdcCYIgSjqhQX0HRQkQOPo1fsvpbYHxb4twgrz3/IcePjXLgyBuMnzoCW6+CXqezN8/Ijinu7JtlXlS5kE1yrRinVvd7zHt6sCtzpEM6SbNOUVkfWBGPy03GKwY+8c/6/Am++3Enj748BtunhaRKv17l/rt/5s0nT5O6N0P64gDvffkU354b5nKpF9sFXrIIb1KZeXuQLt3GRG2R8XKIgaFfoBc+ndwLAZHQ50rrJxRuMLItTWp3BsTaoVSOAzemODu1Rwq71WyOg6IoqLEYPkPBh69FYK3B4lK4mTgZK4jfHZ6nmsV8OcaVolS0JPc20dwz+T6KlRiolve+qwt7Kc/MzqFmVxdoVWq38hsJfvjgWYZFTmVkQpKKUX15DEslLt6+8tDXPLj9N9K5BB9PPMavV7ZRNqVAVaReaBCJG1z/7BGiQdUVoEVgt7lKPXR3Fyl9cYjpmTj7jn5EMS9M++cIR+expQAVm55IhcJilFrNEC8VrzHFmXAnXDwRoNtv0djAYwEe84BxvMqlWwe3/M4nr79Lh1Zl/OxeJi9tYfraHczO9XtDxFGb1jStaA4cAVfDBBsFJtKvstVapLQh8ANnymv/2E3ieuYCLMs3ue8nEvEC2esyY/Kbb/u5etddqwPkD/nngwEq3wwTZuNQTvTuL/9pZN5KaonnC9LplqkSDlYxhKniMvsn72om/qjO0Zd2o8kE8bG+yYqzb+ivwP8l3EY0bWYyFWzb2Zix4zj/D/C/DC13+DnaEZqZy7UFuH1SZ3bd1R6p9VQq0i6ps+2Q+qYAAwAlKDesr1W8UQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 128,
                "name" => "Maldives",
                "isoAlpha2" => "MV",
                "isoAlpha3" => "MDV",
                "isoNumeric" => 462,
                "currency" => [
                    "code" => "MVR",
                    "name" => "Rufiyaa",
                    "symbol" => "Rf",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5NjMyMTNFRTE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5NjMyMTNFRjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjk2MzIxM0VDMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjk2MzIxM0VEMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+25GwywAAAhZJREFUeNrEVktL41AU/u7tIzdarUU6oGNFRBlm5cafIAriQmRWs5jVLCoMzNI/ITO7wY24E0Hc6x9wXXQjiCBi1YKPtEmbdGqSOdemjG3iow/rgZvk3pyc757vnHtO2MHAdBbAMA0d3ZE+Ghdh7wGP7l0B53gnCT+euAxgLtBfBrhTnbcj0pZDNnSF7rw6DwSuvbjpIUU575B3Ubse1AcsPb0m0C9fIzhNAAkz2FCIh2BYRVjGLZRYAjERg+PYPr0CeZosAjubFaQ0QFOf8VjSqyXokuS4K7oBqBGi5AzqYBxzC8vInGaQuzoGIsKvS0CapJkxcLeeQx4UF2bSRYKWGoZJH15cYlCM4OjnHhbjn3CdPad1+HVLng3T23xD3JrLavseuLew+30NN8U7LP/5BruUpyCq7WX1i2LmMTQ6hemPn7G08QMQvURnnLh0mgZu+hxzVuXMfkim1vO+OWC1H9mzAxzmTrAyk6Y4UqqWDcLnbwwsM5qHMb+exuSHcfxOb1B86fxVrPaBH6qVeGIoFMuhYZxrx5j4NYv9cg4jqbHqunD9+jLnlJrhV5RMQeyBnFMDC0gFLDoK47aA7a1VhPqS6FF64Vr+AmJWyIbxvz7UHVtqi4VaZ2LexvQo89XWVsT1KI39dRtrv17nsSzonLRTBaejTSIv2MtNQr7TRKfag78BtXyOO9mPdS/G3fz10f8JMABK7r8EsRKWRQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 129,
                "name" => "Mali",
                "isoAlpha2" => "ML",
                "isoAlpha3" => "MLI",
                "isoNumeric" => 466,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5NjMyMTNGMjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5NjMyMTNGMzE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjk2MzIxM0YwMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjk2MzIxM0YxMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+uyeEmgAAAJtJREFUeNpiFNlq9ZSBgUEKiD8zYAHv/jEzyjL//nte8sF/QZbfLH//Mf/Hpo6Z7z/j79dMf6+H8jH8fMjEzCL4H6s6IOAF4mcsUAYDEo0N/IPSTAz4wX8oJqSOl5ACdEOpoYYoH9AMjFo8avGoxaMWj1o8avHQtpiRSmrAgAXa5OHF1fSBGvYXWsmz4KnsYerALSE86sB2AQQYAKnHJTCzkNpyAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 130,
                "name" => "Malta",
                "isoAlpha2" => "MT",
                "isoAlpha3" => "MLT",
                "isoNumeric" => 470,
                "currency" => [
                    "code" => "MTL",
                    "name" => "Lira",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5NjMyMTNGNjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBRTVFRUNEODE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjk2MzIxM0Y0MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjk2MzIxM0Y1MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+uPhVmAAAAQVJREFUeNrs1s9LwmAYwPHv5hvI3AvqZrrK6AcFDSSoTpWHIqi/OegQQYcOHaK/QNtWastY6jJ0Zeg5OmwR7AsvPLcPL7y8PEoURQ6wMArDoO94aFaZjKbxU+O3Hve1Y4YNB1HM84vk13HFdGDQ8eXd5RV2/YDCxjoxJ9Vx+M7Hs0/Te8DJqjy+vjB8ajPqD2KVRc91uT2/oGNIGq0mypzAu75h5+SIvL0VH6xVyuyeneL4bSJFYXNxibW9fbJmMdYbq5OHpK9UsYwSZqtL1ZhHX11GSD1eeDbkTIPt+iHSqpBEYjZkchqFmk1SqfxRKZzCKZzC/w+e/NXBdAsJEjK/rU8BBgBwJ0VJGmmDwgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 131,
                "name" => "Marshall Islands",
                "isoAlpha2" => "MH",
                "isoAlpha3" => "MHL",
                "isoNumeric" => 584,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBRTVFRUNEQjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBRTVFRUNEQzE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFFNUVFQ0Q5MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFFNUVFQ0RBMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+YGjCSQAAA6FJREFUeNrUlV1MW2UYx/+nfenHac8ppT0TLVAEZoWxwZKB4OayGJnL6p2axa9NEtggMcZEvV6yxBsvduONMTF6MdR4sSg6wleaxUFhDM1wLMDQ0CHQjkJp6ddpT885vgcKsjijI9mFb87Ved/3d57n/3+e5zBo+gSPZunwyNb9aAZQVO3ZzVLBKMhaEC81M/pzZUPkvt10zsgbZVnJiTkU6B8CS6EZHqKD5Rc7q75uc/c9zUV3oHP0m/KeQnMyk4usZ6DXQc9A/S9QDqKTQjuqutop1BaNR3DjjnELLcnEROr2F59sdgdXUlf8gWAornEp/d+gZn6ps6rrbHmvxxKLBnF7ASYX6zz+GtH0TUk05PoDj19898hz9U+kxFxlCf/RF+OJpXXYTA+I/S/oYuferray3uqCWCSIqTAsnnpXw2nu0OspYieQVZ43VdjN3iY35dKLrIm8+nzVyMj8TaILJrKSrEDH/D3Sjr1ftZd0VyupSBizhcR69JT7mTNsdUv+ZCqrCSIrqijJsVRWpSZvQNaTUiKTy0iymg+YbqibRm1AL7UJ39bIiCUQKK3hT5x5suENYndtZ5VKS4PDAULzTaal6XVRGg7UewRvs3s1Jn72/eS1O2FpLQ2rAXoKtSFjt/CL7RVfvs1drjMhwWLpqVdsTa3l+05uE4P3Er6x+Z6hwMD4H+HZVSbfjTRrhhEc7LGDruW19OhkKJPOgjDIUmgRZ5s/J3x32jq434FsWWmy7h370VYYhE3irZmVfv8cdX54IpSdX9MqzWmhJm1VCNHRV+G5yFVa3FSKaAJEoJparb+fdX7a6hytrQRqvDjygaHkmGHjxk+/hPquzfb4796cDIGWEy02hwXlRZoxqiYj2W4lrZCthtV4WhE55IotBVPtRRfbKm7ta7Tj8AUceA/gMkDfUKjHd7v/+t256WVQxTgjHCw8ezRE3pd8PZEdbUpf2ZUwVffnTqH7/WcXSl9qRPPHICeWgX5fpLu31zcWWP1thTYt7GYUsXiMy4PUB7QW2YAC2ULEDCbZ/2HN2IWXC/CiF8KbU9HawYH4Dz39V6/PSIGIdpwG6LJp+f0zccdAOvg5wlIxP3H+hZUOL4/K2plkyzfTxVd8kzf8E1iMwKjXbLEYtAgeZnAxDcfPv3VYbKnVKWzZ5YVDl0akmdEJhEIay8li0+ddjULS0sguGcpP/ej6dfwelgdg1Wk6eoStlHc/r5n/5V/mTwEGALOVkZrh0WmoAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 132,
                "name" => "Martinique",
                "isoAlpha2" => "MQ",
                "isoAlpha3" => "MTQ",
                "isoNumeric" => 474,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QUU1RUVDRTAxNzgyMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QUU1RUVDREYxNzgyMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNTRCRUExODlCNURCQzJGNENDOTYxREZBNTlDMDM2RUQiIHN0UmVmOmRvY3VtZW50SUQ9IjU0QkVBMTg5QjVEQkMyRjRDQzk2MURGQTU5QzAzNkVEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Ni1GggAABOVJREFUeNqkVXtsU1UcPufe29vH+ljbPdoO3NYyBuPhxjBluuwBm0HlFVCRYEKE8YdGA4FpFOQPFEg0DiVGTVSIBLMgiyNBeQ03V1DeiuMxRgvbunVruz7W9+P2nh5PVwUH2x/E89d9fOc73/nO9/sdqF/4KRgbEMNQlNPmSufOyHJ5IldvOCUihqYp8qe3373trZrdmxcRWNOBzsYP2gyGXAhwlOchgGUlGrlU+OetEafbr84UIwQBwARJpXkxxkmI5TKm2qgrK855t6Hys/frHa5wEmMwyYihpEgomDNDVWucUjFbe2Tf8idnal2eaJr3ATWRRkFA08yx9t6eAV+v1dN5eSDBIwFDT8hLZjMQCmh4o3v0lGkwGOXbz5utNh8jEEIIxlGTd6LP64tGIvHtr1fqtHKbzTWzSBWOcnAS1RQNuRhvG/RWPDV1S8OCbosnSykUsTiZBA+pJvSAFdLOoeCrm1r9odiJA2u3vlYxPByYzBOYBAwjoGTsJ990fvTV+caGiraDa7JUIl8gngYw96FJHof9kcNfvlRWrLlrdbUc7z56xpypkMDUDh+RjjHFMLZh34v103a/WWVzBm9bvM0/93j9cZGEuq+aiMLhaCJTLlq7bNbqF0qnF6p7eu2JKDq4Z5lxntbpDgKA8AOXUyshlIzzfHlpTuO6+dMMU/yh+J0ex/pVpTs3V9kdZKMpINPb7x3TjJ0jgZv3Ro523Hv5uRKlXGK2hzua2k6ftYAwAsPeEVcgTe3xhoDVbqVogHi7W2Rcc3hJdUH5bC1R+e0PV460dQNvrJ/3prK8fW/7mM+QrIQQdnqCiURCpczgeOTzxZ/QKcSswOHyL64pXFE3mwDP/Ha79cRtdbaSApDHiGTD5Q6o5CJWKHCPBqQiVper5BIolQw8eXL/56AeB/x4Ipim/aZ08lKGJ6HHH0rwXIKDYiGbp5GRqiGEHl+41lhQXzmTYM5eMZ/stKjV0pQoTIUiCV8oDCkUj+GpWqWQpdC/R840ftiWDlOqbHi+ula/cIF+yBHuuGC+dXkQSNnUslaHc9uS+soSAjzeaf74nR9Bfh5IIkBhZbZ0w8o5iKdsDt8X319NnTn7TwEzekP2WEAADaFEBBUSWSiEt79R1W12PL+xJT9PQVGwl6Y02Yr0hBy1FORrDYYcDiGREOtyxZ4QXzO/cNfb1Us2tP5x00mqZpzXNCl0gO2ueMfFvnUrSvoGPZv2nMIJJBDQExpNwCxDIY4ynR6QZYiX1xVt2dnx6+8WlgUCmh5HjcfylyGjOZTce/DiKVPf5zuW7t1RN2gfnSxC5DvDQmGW1HSxf993lwwFmnMt6wvyMt2+yHjVNAxzvHUgYJyr0apls4oVhVPEQ+5QLMgxzAQpSrc321BQqRSVz9EoFMKn52WRM7e7QgxNjaMOR3iVgn1lseFs88ZdW+u4KDp07C9djrjqGb3XF3uUmudTe9Hny5ublu7fvbK0SH3uguXGHeeyZ0vi8cS4q4A8MwxkMwQtJ6939QyRtltjnLFhVTkpKY5DE6rmUTJXpzBd6uvqtoaicKoud+Pq+WXF2aTzJf/b+TIkjGeUaz5mOdTaXbvAUFKUZbpqa3jvJ18gqs2RBYORh6iJgRSgrnWNtP9y90Dr9UUV0+UZgq9bu67dHNLnq9Ji/xZgAN6EQ6guk6ahAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 133,
                "name" => "Mauritania",
                "isoAlpha2" => "MR",
                "isoAlpha3" => "MRT",
                "isoNumeric" => 478,
                "currency" => [
                    "code" => "MRO",
                    "name" => "Ouguiya",
                    "symbol" => "UM",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFMUYxOTgxNjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFMUYxOTgxNzE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkFFNUVFQ0UxMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkFFNUVFQ0UyMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+I0M3dwAAAo9JREFUeNrEVjtvE0EQ/vZednx2/CDGcQiJLYMQ1EggRMkPQKKiA1FQIaVKRUWDxA9BNKRAogWEoKCFIgoSBixix1GwZRs/78W3xyGFxmcfyKw0utHu7Hwz3+7Mnth4fmUfwBqlh8WMFKWuBQqOfRcCrkTZpQkPA09BnyL1KGMuYEFxJU+2jrxio0DZp+7M62gWey8QOSxPICVcZBUHd5bbuJtuIUc9o7gYe+KPPaGshWUowVTSKSO0KR1XxfZyC1vZQxp4MFyBh90TsGhtENLm1yFyjGte1IwnBD2tWTCF54Mm+K07OnZGJtPiVq4/GydQs2JIkgnXt3FR4h4bIjrVMruL8QHW6KhLXWZgktoi5cbBJq7Xy8gyGFO1fVBpIwO9vNRH21GiUz1wFZzVx1in45c/0sgziJO8UK9HCbSDQOR5F1T7V6CWgavpIxQJPuA6VCdaxrJU3pDWe5kjnIkNUZ3E/YPXOS/BVilGUE7VSQxFBrlF27cMTA0ps6nABUb+tJfBnqVjb3PXp/EznR44GvpkQ0qTepVzeWb3sbSLBjN93M1xrx0dWJcGiodbjTIUOq5V3uNBrokLzEyyIeWcPsF9zh1WPiBJ/XajBFfe9pCiEuzV3WntUjqXNF4i1a/WPyGeoDlvcn8cp2uBpDEE4kN4wySufavgBY+mrI/YVKbe6p4WVugOS6ZsjPFusoRTX85jm5fnptnFhjHyz7vOIJ60VvGos4KmraFEUDeklGbK+Hjm33meHTo3eX4llS2D/r+yZfY4t8y5FV422xOzdMzwjH8P6TAt26PhYES9xkbi1zUbRo6MeIHNzA/NvK+KBJDtMCacuXrzX71O/3L8N2At+OVJLfjXp/dTgAEAlE8F1+TZHx0AAAAASUVORK5CYII=",
            ],
            [
                "id" => 134,
                "name" => "Mauritius",
                "isoAlpha2" => "MU",
                "isoAlpha3" => "MUS",
                "isoNumeric" => 480,
                "currency" => [
                    "code" => "MUR",
                    "name" => "Rupee",
                    "symbol" => "₨",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFMUYxOTgxQTE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFMUYxOTgxQjE3ODIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkUxRjE5ODE4MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkUxRjE5ODE5MTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+lva3PQAAALZJREFUeNrEVsEKwjAMTduIbjIcO6h48CO865/4j36QCB704BBHq4iMNWbQQ78gefBo6OX1JSENntfbGwBsmAFkUDHvmALIThFxC0rABf10hB+mjCmOQppjliMe6qOO4yc2NnuJFCya+NVxTL7QEZ4vvUpzmfayenFQMwchYcfsDPUTLzy1RgQculKnxm7a6wj7z0xncu1Oe0oXJGiY8PouXNZtUnDYKNVY7z9OK08lvPqEvwADAH3qLsG8TnJFAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 135,
                "name" => "Mayotte",
                "isoAlpha2" => "YT",
                "isoAlpha3" => "MYT",
                "isoNumeric" => 175,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RTFGMTk4MUYxNzgyMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RTFGMTk4MUUxNzgyMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNTg5MzRERTk0QkFGMDJBOUYxMTU2OEFENjQzOEYzRDMiIHN0UmVmOmRvY3VtZW50SUQ9IjU4OTM0REU5NEJBRjAyQTlGMTE1NjhBRDY0MzhGM0QzIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+pK5/OQAABEJJREFUeNqklMtvG1UUxs+8PbYndvwKiZ3EiROndVJIE5M0BSSya0slkBBSFlXYtGWBhAQCsahY8AcgdlQQiQWwRIIltI2g0ECqkHfckCZ2bCd2bMd2bDLjGXtm7mXkCkTVOKnEuaORrmbmd7/znXOGwBjD44H/uZPwv4I+gqsjtLeHWUonGXDYILIBPQGCodFyRCvlqbOD2uIKBkQgRDodmKYIijP1nyaeQBNHqMaodu2tymrEdO1NlM/o0TR1fkSdnSG7gpgAmufR1qZ+LkzPLAAF4sMHzJWrtisTlME6UTXCupbaJfJFvL6B8lkIDuB7s9zgGb23D6an6edHyM52+vKrWLBXkylqbp7UFATGKSepRhhUTYWNda7JUcmlye5Olub19YfMaFjNZbUffmQmJyGXpZub1UJGUyk2kVR5gho6xxBPYcgj7TKA2TipcTG1espKXS9z1AtHoGPR6KEoGldfsNfpdH337R/xnaJVYCsKMBiZeCKTq5oYeOfdlzEm5ucWLRYzb+WDhl3He722vLK4tHTh4oXt7djuTsrt9tyeTn/+RQTAVq+TWk8JB3uo9z+8mM2kFFkaeHbg9q1b+5n9F1568b+ox9ItFgqxrehwOLybSICu8RwLUHN5OIPb2mZ3ubk2n2N0zAtg6gkKAFWEsImlY1tbw0PhUqEYi8UaohVFMQvWUH+oqdkh2B1iRdKqUjqrG+gSwX/86eDNr0cl0g7QXKlaAB/mCwW7yyM02f3dfofLWZEqDQ3J7xfaO7yFwl6gJ2BsC8UisFarRTXBDk6ZP7uR43i0/qfEAbI0+YAQ/P5OlrNyLJPP59wtnlq11hCdy+cye5mJidcl8YBlOIuJ38HE297IDfioGZh0glRA6wS9CrBlfiNy+FoXxRnDKIlFl8N+f+Y+wVChgf6jDRk7P1aRlampb6bv/LS5sc6wNEfS7Nq2k4bvJ90uDzol6Hcm/AIPrbGdGgaKoePb0V/u/vrl1FeJeCIcDjdUbTFbrl+/+tvM75WK6O0IJJIJ5O+iSDanwaqF7eX5QBHNeoUACSzBIKSl0nmXu2V/v4gxfenyKyc0X7lUpgno6Oi02e3VWjWSy3pA9gB8cDOJALFAvvfJA6Njorr2lyS6SMpmszubnYokl8tlm8123Mjc+/muy+0Ohk6TBJFMJDPlsu/wQMwfMCarVsxg2oQ1XM3vmtva1eEhLEqhgQFZVjKZvXg8Pj4+fpxqT0uL0doOp0OuyLup1HOhEOU8Y6uPclySkKZ12Gy4PjzmWnVufmE9sur2PLOTSDYJwsmDvraymk6njNJ3B3pdbs/q8tJ2dLOttVUURU3TKIpS5Gqr1zsYHlFVZXFhThIVn6/jbHjoqX5PjyKbzao1lWXp7F7GarUKVmutVhMlsVQq+3w+BKQkiX2n+hp9fhxa03WSJIx19FNNk2VZeMKHf+NvAQYAoQ8NT92b0fIAAAAASUVORK5CYII=",
            ],
            [
                "id" => 136,
                "name" => "Mexico",
                "isoAlpha2" => "MX",
                "isoAlpha3" => "MEX",
                "isoNumeric" => 484,
                "currency" => [
                    "code" => "MXN",
                    "name" => "Peso",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxOUI5NTczNTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxOUI5NTczNjE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkUxRjE5ODIwMTc4MjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE5Qjk1NzM0MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0LwZHQAAAaBJREFUeNpiZMhw/8SAD7x4zODm6M+wM6+NgVjwKKeS4enUTgYOQVWcaliAmJdhAAATwwCBUYtJAv///aOfxf+B+Pev7wzPr51g2NwWydDkb8Rw5dQR2lvMCMTvX19guLWrleHW+bMMBw6cZygPtGV4fvsyLSz+D2f9/PWK4T87N4OgmgvD1TuvGBz8XRnEeRgYVk1sRKj+/5+giSxE+ZKJGUy/fHmLgecfJwPTh6sMHB9PM/z5+oXh4zdWBo+CLoZf/9kYnr+6zSAupsrAwsJKLR8zgskvnx8xHHv0jGHJ+a8MD8UcGFyqehg+Pj/J8OXjdwZbHy+GJ88voain2OL///6CaW4ucQZx9tsM/zS9GJZ8MWa4zf6HQaK0kYHZ0ovh8/MzDCJCsmAD//z9Rd3EJSGlyyDCI8WgzXKZIUDoFoPj48MM7L/5GYT5XzJwCIkxKMqaQf3LSJ04RgaSKk4M/C/OMdz8+53ht0kOgz/7WwZhEXEGaRkTksxhISc7cUsYMRgBMcM/YOplYhyAIpNMS0crCboCUOL6PBAWAwQYAIwVfZVkoDrqAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 137,
                "name" => "Micronesia",
                "isoAlpha2" => "FM",
                "isoAlpha3" => "FSM",
                "isoNumeric" => 583,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxOUI5NTczOTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxOUI5NTczQTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjE5Qjk1NzM3MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE5Qjk1NzM4MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+pVbFKwAAAjtJREFUeNq0Vt2KEzEU/jJJptMfu3+u7ML6i+zdXop4JSi+hyD4ArLghe8h+Bi+g/gCXgkFvXGFdi1ut9NpM5N4Tqq0CGmbcQ1th+lM8p3vfN85iTh937tAjeHc/CoEag1F32uxkxJCy8vKg7dTCfsnipg1YidIAj3PDW5uN3B3N0N/PINMRC3GUWM0q3BvL8PLR4dQBPj2wzd8HU6R6QTifwJbS2mlz1Y2n8psZ5VDU8etI8hcUQKVBNwl0FY6ZzgxFsO8hJbialPNUS0vyQCjosT3C+vvOYCGErAuPCcamI10SZqya1OZICUANjCnt5vJRfodOx2Ylo7Sbr3r2xSQdTWAk9/uPb7RxNQ4VIT4c1IGHcw677U0qMjQbUh87k/oqujObV5O/OrYVLhD5fL8wQFOnxwhUwnGMxtkUJDWEA6vHh/hxcMD3L+eITcVQiWerBKXyWn64TTP/1rvwyaVlab3kzUtLehqnjig5nBy2IYhsQpK9w9KvQqk2lCq9zvaS8H6fjrLvfsRCDaoMRtqv5OiNyi8ftwgON2h9tigZ/1Lg6K0PrjtplrZSle6uiKmvGCmF8EwYWbH9cuDA0ql8M+41LSUXtd1/XttHf8tFbPfIjY7rcUuNSS3L0uwyY4VvUlMjfWsXz+9hTfPbnvG+Qq3X1mvFoSqiBKnm9kyOUUp3qRb/RNwh/bfL8MC7z6e+c7GjWKXGkfsnhy/OxEAO7Y3mHiWOy1V6yDAwKM6xx52+/IRKHb8EmAAirfmzWzPA+cAAAAASUVORK5CYII=",
            ],
            [
                "id" => 138,
                "name" => "Moldova",
                "isoAlpha2" => "MD",
                "isoAlpha3" => "MDA",
                "isoNumeric" => 498,
                "currency" => [
                    "code" => "MDL",
                    "name" => "Leu",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxOUI5NTczRDE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxOUI5NTczRTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjE5Qjk1NzNCMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE5Qjk1NzNDMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+UdXXEwAAAipJREFUeNrUlctrE2EQwH/7SDbbmI3m0SpF2iJRaPEQ8FJE8OJFD55ETx5U0EMFEfInWBEFEfXmQfAmCFqhFKHSg+2lBsFSNAj2UFqatNQ8TLN57ToxBy89rCUxODDsx+zs/Ha+mfk+Bd4W2VVU0YM8u5fiauo99jc8iXYYKq985G+EUKOOGHb300VD9EBUeiR7AquKqPoPwS2gT4pTrYN/PwQi4Dptm6J0C9wQmCG9cghqsn76MMT8bAgzKjb5AUPeuY0ugI04pD/5uH39AEGBf8+FWVi2KLtwU2xfMhpGtAtgRbJa/Kzx6EUfM48tEopNJGsz9SDMk5cm6SVNxsc7WPfsuQPhqM7l8T5G5n5S2qgTUFwSQzoXT5lYluRQ9Z6KN7A0jr0Nc2mNuGUwGxvm+KUVlKbD/Md+Brc01nMqTkECxju81a0uNnwFPuQd7gYusDwcZHpglInCLcqObPs+G0UazHE6CZYGUiVo8ohJrlbi2sgb1jMx1paCXBmdweovELH8KH5oNrswTlZYJ7xR5Ky2SKigcsK/xmTiNdsrVfJV7XdJvM6z5+aqb8Lp8RLJ5zD1TmNhuohhNomdV5lIlRkccqhvieNAh8ENG6KW6BicsRxWs3l0SfLkOZejSanFqtxKO94DegYrArFlXNwMjB1zuX/nR/u8NgX4Vd7rbZ+uXRKt4JWsPKV7Hal7ZbMN/VvZwycCap0VtT/r/+o+bmVc6gX4lwADANw8m6+4QijQAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 139,
                "name" => "Monaco",
                "isoAlpha2" => "MC",
                "isoAlpha3" => "MCO",
                "isoNumeric" => 492,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0QjA3RTdDNjE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QjA3RTdDNzE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRCMDdFN0M0MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRCMDdFN0M1MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+o1HuEwAAAEdJREFUeNpiPCeo9omBgYGXgb7gMxPDAIFRi0ctHn4Ws/x5/35gLGaXlRkQixn/fP4yICUXCzMP92iqHrV41OJRi8kCAAEGAK++Cy3NEBqaAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 140,
                "name" => "Mongolia",
                "isoAlpha2" => "MN",
                "isoAlpha3" => "MNG",
                "isoNumeric" => 496,
                "currency" => [
                    "code" => "MNT",
                    "name" => "Tugrik",
                    "symbol" => "₮",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0QjA3RTdDQTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QjA3RTdDQjE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRCMDdFN0M4MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRCMDdFN0M5MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+XDrqKwAAAkFJREFUeNq8VT1vE0EQfbO7vrv44jgKiZ1YQRb5ATRRJCiQkKADGn5BaKho6egogQ7xExBlJBoKUDpEgyhQCiJSxBKREcaJ4yS272OXub1EwiYFEnuMtPJ5NLo3b/a9OfpaX/4GoILJMHxoPCW0xnynjacPn2Dj7jqW9lr4m2gvLuPG2w08evwAB9U5aCmhON8YqzoFS/cJImCwaQOTwnkIPv3JpO4Rpq4lUBc19DEKCTGZSH+SBVx6eYLwVoykJ/4PsPAZvEM42VSItiRInldVBOMYKF/nMddTTN1MIEO+40HRwCwiMyKEdxIGH6F6L2JxaZsrBphy+yQdATVrkHYJgw8+hh9Z9iv8f0Aw0Z/2+pdQv3tWNTIVE+JtbqAuuAE+P1hs8xpU4prYMWN9SCDfoPmpj9qzAQ5feWwpgT7/qkWNlS89q/C4Kxwz1nkLqqYRrKUIriR23N7lFF5TQ8zxmeUy7W7cY3esE8JoS0BWDfzVFIYnEW0LW2CGTq/4lPHZeh7xy3lNVu9HKDVThLdj6CNyq6pz7cSsM7ZJSyBpSwRXU4jMx0UDizJbmTf3/gsf8Q7h4LmH4zeKYbVz4DE7kTBQC8A0j1g1mCmryVzK2ZJX4ALRvKHiXcLRa+6HDIbvJQbvJEwCKy7njMWMsWuxtVaxGyreEfi+HtovVfY93l2dsb4uXeCRd90BVyxvfmfEVspGKhd4TbKVbEPMNvqs+FlDBW4Z7/GYK9m4M8CzhSLK+XO2KmUtF5dxqLFfAgwALQ/bbU/KwEgAAAAASUVORK5CYII=",
            ],
            [
                "id" => 141,
                "name" => "Montserrat",
                "isoAlpha2" => "MS",
                "isoAlpha3" => "MSR",
                "isoNumeric" => 500,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzRGM0JBODYxNzgzMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NEIwN0U3Q0UxNzgzMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRDk0NjlGNUJGQkI2MjlGQjlENTFEMUY4NDU0Q0QwMkQiIHN0UmVmOmRvY3VtZW50SUQ9IkQ5NDY5RjVCRkJCNjI5RkI5RDUxRDFGODQ1NENEMDJEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+w2IgWQAAA5FJREFUeNrsVG1sU1UYfs69l7WzWxlt1466spISNs02ZLgExpeogAmbGwEyg59hQKiO4PwIKmqm4JQQ1MVERhxkGfiDRBf/YACDQBgyzDbiGMOMwcCytdCt++rXbW/v8dzjiIs/jJnun0/uj3vved/nvO/zPueQ3u0vOzesrOtMrm7suNvSDJg/r3DtOPH+qZJdq4+KCA6KYugVu79225wxZ57jjc4Rb4AgCX8LCogihNlfeDY99el63/mOyozde56dkZvjG45DVgdUMXmG/gXbrbYK/d7qJ/YLRZlf0RFfgGXhn4CClD5TLxuMi/WDbsst83NPe7MW9dZ/Pb3y+ciez6jTVRjrxtqNje3xYwePR70DzRf6Y0qc/MlOqFZiAhAIBF7u/aoFtvbITISj92TBM0rjQwGHxaBEYr+FFDOoIUUYy3DJXl+6MjZrdroqiA/++rhPTSeI8Hy2wTAQBh4AotBUMhGobIVTE6nVmD0QGR6IhnJLltgTY0l3ro+y0HMt+kcLZi3Nv/lDc9DhaDG7bof9ZJoUI9MAhVfGeP1Wi2l3zebs/NyermsfvPetp+8OkMGb4B1h+uvOskU7X1ywLT8uN538qFOX87D1yaqyK2/W3ly7udziN176vq6p95MbabcVC9ovExoDJIqRh1ymtw+/GxGtKf4O0V4gRwP73R92dPURmJlKTBBx7ztVDUtjRT0/1lUdeqnR1/STtDpzZFnf6XtZ88sqznzT0p06x7Vlvt5t8iVdvXxhCImEntcVOHJoq3NZXs0Bf/jSzvPXjavW5a9faKtvOAOwAEEgVHIEB7989ViD39olZ4MGWc7JX5TQ0LxrbTGkSj0Xf950sX1fcfGO5UW2tL6kRCQGA9fEVLTYebYfqXYb1dkDROftx4rCmYAFiEMbKSFAGZDJvxUCiVVEtUwrk5Joium4BwJ87HYWRqBwH/hPn6qe+5jdXX7YYDkuFWx8a01p/G6woHAXYGTUbIxsGssZHdFUF8bV10Ykcz+xF8qXkqE9bG/Ky2H/5Lar5LXS4XKdu3VUKMnrXpKRWryl1+u5QbS2CBOExW3FZCBSDM7NmfdxZZhEznqiC498l9badoXABKjjvp4s9R+HZRSwpRj0wVAIGGIG5w2NHxkJkwTlkzBShDkvuA4Tt9YcSvCvQO4fbooJVFSlEhsl/huQiTefIFBJVVVMARLquOGmBP9T/wW/CzAAKWlmnQeZkEoAAAAASUVORK5CYII=",
            ],
            [
                "id" => 142,
                "name" => "Morocco",
                "isoAlpha2" => "MA",
                "isoAlpha3" => "MAR",
                "isoNumeric" => 504,
                "currency" => [
                    "code" => "MAD",
                    "name" => "Dirham",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NEYzQkE4OTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NEYzQkE4QTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RjNCQTg3MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc0RjNCQTg4MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+AgxteQAAAiBJREFUeNrEVktr1FAU/m5uMppOY9up2rEVVCgtFEU3iooVdNWFe3euXAiCf0DsTty5E6VbN+5EBdGFuFGLG3Ezm/rAQseBthJppm1y8/JLTEREnHScmV445Obcc853njcRDQzXAYySHPRmWaSverbBb8+egGvYptUWcEjaCZFS2Caw3o5SHwGXjQhaDFQCAdWLiImFIardP+biwREPFciU13XgXOHRGYVnJ3/GKrqR6ogkSbsJKVMIDctmgG9eBE8JNGWMaqinuQhIq9SICzjTEtikCZ+mbhxdhz0cY6oh8WKSHFre1GNcnlnDqUUDtWqAUZ5dq5mpE+p/gUMaSWq6x9Zw62oT1opA+ZPA9GcDygBejvl4clZhoxzj3k0Lu+ioXaDqgjfX2r8ujzxtVfq4YPq4cs7Bmwkf9bkRfDcjjF9awYX3Jdx9bWG/kmhkA9Yi1U7LiHMDdVZwYtPAbK0fM+M2Lp63UwjLFZhd6CeonsrITs9x3s2vRhSEJvB2yodO5Dggb6/C8bq+pREpLGtkPf5w2oN7MMb87UE8vzOI5mSMx6e99FR2A7hC0adDjPYj8OF6BYeDEk64Jbzj3lsE5vsUBrYw0S2bK187aPRLKWSTSexTGpZY4QRmjO9L5K9GEQ4EEl6xe8wpXOMNGjzErpVpo4W/miPZD/AiKfMkkel4cyUG3Swa+Qd//S/8rnwWO7H07JfH6vGvj/NDgAEAfcqvgcMO6pAAAAAASUVORK5CYII=",
            ],
            [
                "id" => 143,
                "name" => "Mozambique",
                "isoAlpha2" => "MZ",
                "isoAlpha3" => "MOZ",
                "isoNumeric" => 508,
                "currency" => [
                    "code" => "MZN",
                    "name" => "Meticail",
                    "symbol" => "MT",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NEYzQkE4RDE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NEYzQkE4RTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RjNCQThCMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc0RjNCQThDMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+9o9/QQAAAvpJREFUeNq0lt1rFFcYxn9nZnez3x8xGpMl6xpMNCaglCiliAqKYEVbCQriBwreFEuhsVAU71oopRRv2hu19QNBBEVQLxTxoqEgGsvWoIwSFzdo/EjiZjOzu2YzOzOeXan/QGcHZm5mzvmd55n3fc4RP36yafzYlmXteFWDt2VQBA2+IvJ+KRx69BvJRGTv9l6mkjGYKoFlg2joAgzlQSLMxvECmVPDrPlnHOYFIeAF22mobEVxHEaaQzRJlUOX/uW7axr4PZAINBQuRuL9es13S1obNi2WFMtcSs/nwJe9FFvDMGE0wnrjIxg5r20J7DmFFbM6z8J+dm3tZXhlEkomVKV699iGp/a0cFjgeGkKCp63ldBex+gtV7h8YYi//AOMHvycuWmdRXgxccd+cT/Ur6dVf+Se/g5t5WsGj5tktqW4ZZT4DJU1/hAPkgEGDI2sWWSpJ4QiB9r/V3FHK/xdKrBTH+XrxRXuyBn/CE7QZXTS0T0PTa+Qzs5xWoTZ40zwhJI7VZ1IOAz1P+ToL61sWNXP+v0ptvw8wPeFCuGDj6nM+Hgqq7wvsZCxlnUcCaTcAWsZL7/mekg8DbL75lp++PZPXnWfYPhMH85PUayKQAQsqvV/KwgJ1RWwpyUOhx4VuGsFuX6lg84Wg7F3xzm5Y4p9083Mj1dpKwuy1jTrqqNocgmugHXTpll20/nfX7Fs7AhPNiX59HYWezDK5OGldM3EuOrz8lVVo0hUDgm5U9WZwCo93a5EnNVTjF2OY8358KVKtPaYvLmp0jbYQ+6bzUzmDTodX731HNcCRBAxdR+epiqqv4qRj6N6qnSfzBDbL5NL9i+Wu8lVD5CaBF+sgl1RKOajxJdP0nnxMb4+E/ulfD0rk0t1N6vrYKE4mLJtTKuJ9l05Os7mPoh8Vqv7/75yGSyk3Nl8UO7/Fl2/jZA4lKeWEdbzxgA/gsuFMOH0jLRWw79a2v1GWltuLLQOXvDFCyN1LhtRohhWjg87kNpQZu3oY7wXYABi1iBcy/DE+QAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 144,
                "name" => "Myanmar",
                "isoAlpha2" => "MM",
                "isoAlpha3" => "MMR",
                "isoNumeric" => 104,
                "currency" => [
                    "code" => "MMK",
                    "name" => "Kyat",
                    "symbol" => "K",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMEI3N0VBQTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMEI3N0VBQjE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RjNCQThGMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc0RjNCQTkwMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+hqhQlQAAAbNJREFUeNrskrtuE0EUhudy7L3E9q7XlxDZEgbFCRApQkQgykjpeAFqyrS0afIGKdLTIB4gj0BKBEWaSFEig41jiPBtvbb3Mrt7MpZF7zWiifJLoznSGX3z6z+H7uweT6nCGXUmAjg1c4ppqI4T2I4vwljTwCZaKbI/9I/Wov6I6WRhQY/l5OV5oZnP7WyVp17ouaJSMDdXUp+/XA8FumnFpWkkhBIkScQ09BGJ50fP1q0VDfZeVw72X77aLjNG6zXTdUOyrJg8cYxFS7vpuiVLf/umvvk4//7d8yePTCFiXQPZXR6NBKVHy1DqD43u0G91HBn9Rs20TCWXSUfRP6Ald2h7ugqN9ohR8mKr1Po1Pv3aUdJ8NA5kdzk0yPEwxnyBV61R3lA/nlwYWaX9exKE+L3t2OMgVchiuAwdfkJxQlUosW+NcROw+oD/uB5UVzN92+tcMlirOah7CLF0QOJk6EP7U0j4rNLp4DwQZ1G5oP85d+c7HvXQp6CjrxExpWoiNPWfptjfhaWzkc4WRuY7r6U4ieXfLSgLwnkS49CE1QWf8oSBMPLfdI++R99F9K0AAwBXEavacm2ESgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 145,
                "name" => "Namibia",
                "isoAlpha2" => "NA",
                "isoAlpha3" => "NAM",
                "isoNumeric" => 516,
                "currency" => [
                    "code" => "NAD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMEI3N0VBRTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMEI3N0VBRjE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEwQjc3RUFDMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEwQjc3RUFEMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+rnH82gAABORJREFUeNqslmtsU3UYxn+n7dq1o2Mb7OJUBGZEjck22ECHiW4EI4iiDEb8IoPAMHFsA+/6xS9+YAheEgNjDBkDhiSKSKJhExmg8cKQXbgMAxHGGDCB9bKu69qe+vb0UBiXgOhJTtOe9LzP//c+z/89RyH3g7NAupxu/uthNMBgANo6Medn8dX2cmYca6HjpTIGunowJtjD/wp/dJv0L5ELSkhujIEBCyhyxeKDmMCdiZqM0HlRlu/j+XXlfDkvC+tHqzn45ttSKxHLiPsIBQfBYCA06LebojeGRd1xWG0D5GZ2EAopNJ+8H2+/FWze21O2nCK2IJuNG19jTqAb58TptBxowGZ9FKPVpokqhhh8jm5M+IkIiyZBI0mpl5iVfYzJGZ0EVAPj7u1hZ9tDXHDERxYWPm9G6RxgxtoythVnY/24isPL3sGDGXtCLiElqAmE/EHcnhZGpzxO+qYqXTjcV6GdOqmNwuyj5IzuxmIKMirJiddvYnNDHsR7hlL6hPKQUE7JpraulKLBszgmzaDl911C+QhxYUo1IJVNeC+fIQYHefNXEqoqY7Fnpy4cMGIa4cTljJUAKIzM6dcux/QKjMOKZaQDn0dabvGLqE7Z59Mo6+dnM+yzao6Uv0WfRplzHWUrGSlPkLppPfunplO0+1XO7/5aFzYGCfTGk5joJeRScRyK1YhVZ4ikxH58l6XV8RK0gCqUp7HkZ7Jp8xJm+4UyZxq/HGzUvIxSKtdSroD15Szq+551y6dJHqS7o7J1YUNIC1DD0QyS4rz0uIcRCCr80ZnODx0ZIipt7boErgFeqC7TEhu7cjWH33iffilvTxRK1EhcdC81yq117M1PZnbDQi42fQtp42C4DdQgV1Mt2+ZidzK1eyZy/NxfsnIDv50Yg7vXDKc6sDydRe3GUuaqkljx8pDupS1MGQpe46WTvGKhrKlgsXcXayufEcqQRomqaqJaLqPCsn0Y3ifb0EzjgcdkIRKg8+clRC6eW1PKtvkTsH0qXlbcxMvBMGUbY5MnkVa/gZ+npDOroYSepu1C+bB0zBYVjG6IIb/C4lZpq0H8bD+DpSCTzfWVFHo7xcvptGpeXp/YLqG8TN6CMGUZC13fUVM5TaccP4Ty1sJX9qXLx0wpsuWVLGyr1nDk9feEUrxMmCBrU4cmNlW8rP+JpvwRzG1YJJQ7IHXcTSlvFJYxhl9IW09jfiqTOklskXoOR65Mn+YGIdQTG/VSp5y3HDYspcSzi+rKJWILN3h5a+Ew5RmhdMj0qVoSnT5Hlr2rUcZHp4+e2L7WiJdb97G/II1C8fLvpm8iib0N5VDh5pNYp4zXKAs9p3FqlLqXtuunTy95C1dBdelVL33qHVNeeygza5pddQsm2O2fCOXSCGVcwliNUlEUVBmNfZ52xoqXaVs2sK8ghaLGpVz4UU+sxfavBPXDrYT+POG69HKp/bgkNu7Kk0QKKYarXmYVfwhfVFAi06f687LI9EkeE6EkdDdPbrfSbh3t8np9dmviA1LiCqVfEtvOgyl5ktha9hYkM7exggt7dkS8NN8V5RBhk6rEE5sUG/HSGMOgq4eAv4vJxZLY9ctY5JEZu+JZeTm4Oy9vGS7FbHZLsfBbiDvodGKxJzG+ah2/znmSFxtLIl7eI17abf+LoP7G4/5HgAEAvGpSdeM9fnwAAAAASUVORK5CYII=",
            ],
            [
                "id" => 146,
                "name" => "Nauru",
                "isoAlpha2" => "NR",
                "isoAlpha3" => "NRU",
                "isoNumeric" => 520,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMEI3N0VCMjE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMEI3N0VCMzE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEwQjc3RUIwMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEwQjc3RUIxMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+zsfIiwAAAWVJREFUeNpiZNCuZ6ANYGKgGRg1elgYzcLwm5l2RrMw/KeJ0Yy3N2jzsP/BJsP49+8/oKUszEwM/8mxnEWQ+xcvx290YWbmb5++f//3l4mRke0/Izc/J8PffyS7mkGtCU0A6E6GJ+8ZhLhs7dT//f9/9PAtho/fGST4QaaT4npmBkFnhv+MKOjTT14hntQU+wmV3iFuOv/Z2K7fev3z408GFhZ0lXgRCwM7amj8/cfC8N3PX6+n0JqDHZQ0uwusvnx8u3je0Z/M/0AeIj9d//vPyM7y9+cfDnYWaGwwM/3+9YeRjYXh33/Ksgwb8+8//44cu7dh3w2IwLYjtw8cvPXj9x8GmGVEh7WYA3rMcrN/eflp34XH7Gwsxy8+Lu/e9ezxO0Zh3v9//4MimYQUgrUqAKblD99AzmRiZPj2G5haGH7/I8lc3GUIMJ3xcjAAnQk0kY+D4Q/J5oIzOh7AyUpJRgcIMACUJouaxkAcBAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 147,
                "name" => "Nepal",
                "isoAlpha2" => "NP",
                "isoAlpha3" => "NPL",
                "isoNumeric" => 524,
                "currency" => [
                    "code" => "NPR",
                    "name" => "Rupee",
                    "symbol" => "₨",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRkNCMjg2OTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRkNCMjg2QTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEwQjc3RUI0MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGQ0IyODY4MTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+8vLQDwAAA4RJREFUeNq0lXtoU3cUx7+/+2rSR9L04SNWbdX6RuhGcW51uKp0FXVsQ9gfUphTBO0/goj/iCCICBNREJyzjk7ErlPEirZqnYiPWtOVRTYb1zZVY9NIjbFpk97eR+5OkmL/atLXfnC53PO793zO+Z3vOZcZhoHRFvvspyoIGYeh9rlhaFELxrOMpspR97hEH64JPt9W4Wt0QbL9ADGTPEUwVSshOFcLBard1eKZzqqzOcq7eqTOKgQnTkkACcG9fDoXSMnBtrfNuOc68eXWHspezNgHshF9+PofwDwiUBkHp9kOy1AYZzuquQvtp4/OD79ugtm+ErxpwtlzSQWi62DBPnjkMJwaj83eJtx5dvyT3a/rHoNLOQLT9GEfxtSBmSTG7xyHxbUXUHj5N/xjzoNuMJzw3cB118n9RUFXG8wzyyCkjSv7hGDF0430VcVY7LgJ25ZNsH2zASue1EP9Yi1a5AhKBrrQ8O+phQde1jQQ9GcKwBJ3aUwOzEkSQm1/Qw8GP7gy+19hZmsj5qtBqLwAW2QIh15cgufpwe3be267UxmrABOTgoWEm9NyoTv9cH1ejuklJXiZYsPvjjeYwS+AlF9M9ddAp04SZChQAigP/JXdLlmr71kWVULX95CLhxMCG6pKgysTgibg7YMbyCDb/SV7jdb8DUcQ6q4lsp0UwIaFQBHQ+2pQhBbOp2dpwhmDRBVRFOjyIDTzXGTrg2hs+5F9F5G/vZW37ijCXmdMUGxklGpMmHyNoxnrggJL6RosvVmD7DvXECndiFrPuUXrfX8EqJU+Hu65uKJjqh5bWyXMWH9HvmfnY9r5UxDsM2I2ueZX9K0vx0XnSW4LJzbdzfm0GIM9zmilx/MT4ZINDzFv9gdodJlybQgULEc/xXyl4xdxtb/5MbXRsrG20dgmF9VY83mhv+kdqaE/AL3bDS9vRYhG5tWOc6ZVfkczwRfGMzYmDxaybJC7OvGiYjcGHjlil3vrLoSfP4OJ1N4tWSDT36qusyqtONDaQvCCscKFZANEVHj037qN/gdPYj71UD+ktEwwQYBAovISfI7yHvXtZzLKCne2/Jm5ogiy79WkwCZtyJrDwuBS06GG/fFg0mgmkw3KSFYq42FXgmhpO5b11YIdXXVZHxVBHXg6YbAjNc/z9bzvrTITjGSHF4XPpcxVQ7dS49dSdKVk9o72/n8CDABnj2XIyb5lqQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 148,
                "name" => "Netherlands",
                "isoAlpha2" => "NL",
                "isoAlpha3" => "NLD",
                "isoNumeric" => 528,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRkNCMjg2RDE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRkNCMjg2RTE3ODMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNGQ0IyODZCMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGQ0IyODZDMTc4MzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+dLNUtgAAAEBJREFUeNpiXCej8Z9hAAATwwCBUYtHLaYZYPz///8nIM1LZ3s/j8bxqMU0AyxK7j28A2Av72gcj1o8/CwGCDAAa10IGBJ1C8IAAAAASUVORK5CYII=",
            ],
            [
                "id" => 149,
                "name" => "Netherlands Antilles",
                "isoAlpha2" => "AN",
                "isoAlpha3" => "ANT",
                "isoNumeric" => 530,
                "currency" => [
                    "code" => "ANG",
                    "name" => "Guilder",
                    "symbol" => "ƒ",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Q0ZDQjI4NzIxNzgzMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Q0ZDQjI4NzExNzgzMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQkE2REE0NTQ3MzVGMzJGMUI0MjQ3MkQxOUFBQTIyNDciIHN0UmVmOmRvY3VtZW50SUQ9IkJBNkRBNDU0NzM1RjMyRjFCNDI0NzJEMTlBQUEyMjQ3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ZDn+FAAAAexJREFUeNrkVM9P1EAY/aad7UK72RZkNSgmROFiOHEyZoUQIgkJRw/Gm4khMfHf8KxnxRMQTsqJAyEEDv6InkiAhUAWVrbID3cL2y67225nPmd7U9L2ZGL061ya7703r+/rDEFEiCxndcUcGVOuXAVKW++cez/M7tk54/GjaKIEf6z+VWkinl8LW4vEEmksItWj9YKflG1GBBglCj5A8loiXnpxaSu8i1SmF2vfTuQ7tKmf2S5y7OhUuXSSWS5pZIdxH5CEfy48i9y7KXwDyQBWHj4fNvS2qReLQNKAFkA1yJNEBGKEWRbMtKE4VQ/9BoDf+G5VLIFngC5Au6aptRpHZGEDkwGGgp1/Wy1pVU3OTI8XCo5ZPBbed7b2c+tFgDRA/fatztdvRj9/PD6vXAT+LisQIX0/JCjCGG6sl/O7tuuKyQnjqpgfgC1seq68mSvl9yqMkbBMhHQ2dIjIT0/LrusamY7BwZvFogPgZYf6GCbKJevo6IyxmKyt2H9f7e+dmBz49GFB4B88yb57v3d4kA9a9agDMfX2S0Rblmgtt119+SqRUj1seaSSz85t7emkeu8u580oSxhb+1+Ft3pKwes63tAbeltNsFbnY3nxp9EpVE2gSrILWABWuAdm92HD+F9vvr9R+qcAAwDK6wIXIkUhaQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 150,
                "name" => "New Caledonia",
                "isoAlpha2" => "NC",
                "isoAlpha3" => "NCL",
                "isoNumeric" => 540,
                "currency" => [
                    "code" => "XPF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTc4OUU5MjExNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTc4OUU5MjAxNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iOUZFRTg5QjJBOTY4REI3QkY3NEIwMDg0OEVCRkM5N0YiIHN0UmVmOmRvY3VtZW50SUQ9IjlGRUU4OUIyQTk2OERCN0JGNzRCMDA4NDhFQkZDOTdGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+xxyn+wAAA1BJREFUeNq0VVtoE0sY/ueW7CZNm9oY0jRFihjFC2K9HUWOCoceUEShL17w9tAXwSfx8qA+iFpQq0/KkUKtep4UER884PH0CK36oIiXIN5trVirrYk1SbOzu7PjpFU0wS2t4DK7MP/MfP83///9/yKoPQ+/5sEjLyOJ1OfrTjQmaDoisCOxDp/KIYOACHAYBDnoSXDEaNy4QTvACPTFoO9jxZS26TN6YxH+wdDb2uPWqxkwAYGvH8TwVeSYoB3EvPJlZdm4jsYjf69dfr+sCrc0+X5flGE7/cdb5xxu3gQlUyDSBaYDkrqhI5h1tdjGMHSXxSMX2s41xiapeajpoDx5CrVdZtXxDED6yqXQsi0HHd9cCPYpGm686RKSKAwEeDPBlKez9ezR2CRqvAlpYR7w04aN/up4LterUaL/ufL9X6nGE1t3RoKOQXIgkYsEZtYWSWbwCX2yq3PWngx/V8I0WxBt+w5obkmdaQ7VrxU8aTEvNkvTD+pD865XQtR2ZZ21yfdzv0UeBQ36h9oecKTEHpRNsbsJ6S3130ug+vytsJEFX6kOdfaryzI6npl544+gOS6A1kw2WGWGK3E+RQh4mgTCvK6Odb/1rVihfHEpdIStfOhqaM84EpWYEzTKkkFCSRjL4QhKxZ5AqDyn00w0DGBr6h2SBFJHBUIjyBsTKQsGE1ovzfWrbAqFThmy0vb9BDAvu9YhgFCJkMx7leS1E05KC8lihK+DlqVFgS/pzO+mL/4hUMsVYRpw+ru91/7nj5+nb9wMrl/nMKrOgQmi5KIWNxBkLN2tZDbUVxUgI0m5N/ks0PL0VkWc2ykWmkj279UPHIHD+5iTNiQGFrT+veJp8kyr3qZZwKWbQs4uCBbbNAxdFatbjasNd2iNOud52ulEInrimblwKcKQftnONv9X27OqCkqMoei76BpOrim2KRo+Am99s82uQ4sfntndc7r9y8qFY7rvt8jqi1MHYtW0NCW4Sqhr7/wR9DC64s4D0DUAt5/T7ABYlk08EK2E+TUQooAHwRJIVZx7C3TpfEpUXOnvI0zWYMFcAIEt6WHYNAGSWRCG0h3kFfrz/RojzmUvtxH+cpV8mkdiOmrofG0MNbZvIkBy1H+ZzwIMAI1PbW2aWDCwAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 151,
                "name" => "New Zealand",
                "isoAlpha2" => "NZ",
                "isoAlpha3" => "NZL",
                "isoNumeric" => 554,
                "currency" => [
                    "code" => "NZD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxNzg5RTkyNDE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxNzg5RTkyNTE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjE3ODlFOTIyMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE3ODlFOTIzMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ycsmVwAAAzVJREFUeNrslF9IU1Ecx3/nnHu3uc3p5lbOWDk1VDQj+muQgVREUJGFqEQPSf8IqYeIiAyKngojCiItqLCIrIewsH8WQf8gJNK0P6LRTJ0619y/u927e+7pbgn9hR7cY7+n8/L7nO/5nPM7qOvA8ezyOS0e87EbvZ9bX0CatXlv/vqWE/c21FVe84EgTk+T9zmFHRW5gs05++ib4LAXjFr4uWQFCAYEvxVX0jS6/XZfw+5Fm7c7GldVHm/3BBlNpVI0xZiRjepI3561OUa77fCg+dSdkYhrFMz6H90YQFS5KUCjoEGg/IJGm3ZcVczWckt0G3Fp1pUFHYUDN9uhtgqfv2owpcxEXli+4lJXpLXpoRIT27rHYkERNGSyO0qdDmN9CW7opj0DAmjxL+hA8Xw143hQdkUw8wesOhxTmFdnNIf8Wh5/tdrZmCeLSPaZJgnrbdFVAaYHJE12h2L5eemnl/D7X0mdH/zx4OquBAFLoF/vqg+6v4YYKSorNntHTILPI6Dei1dyayozSwv8L3s8Ev40bVbG+Agy6JY/5UICAx5AkOP9elUxiQuRIxwHBoM2HJZkVX3CO2nka/pXVyzbV72gNIuX0UnNPFi6qOjO5Xd1hx6Vbly6ZaUlP+PdGDvod9ZHciXXICgxjEnBrNQZthT1rEymQEUIxwqzTTfWpD8fUzxu1UzcGLmwv+5MUWB2x+NzR25Xt7ivPwlUO0Jzh54N2udWbG278vijwWKtylVqU93W7rftblAYxkh3dhlf6UDN/RyNicATwChCWV+AdY9EJIXFnajoDQtX3G1s29lBLg1ZfN4wpAND0PtFfIDsnTHk6+htbX1/U++UM7N499DdL1GFaJgidQnk/jAb9gnAJa6Ox5JI+11hSWWqur+7Bt0esGQAjYAeAc8BlWFChvRpMOEBEwYNr+aEoQlQby7TBnoKLKEymHCdyn2n/LUQ5NXDFEuicSd/jAyeElTtlhQwpAFlyUYLSrHTdKtcXuw0QpQlFS1RHY9zTDyvPgmR/v6HTAltJB0DoZJxEwhBMOKkpiYE1JHxjoMsx9fJTM2YOiyg5SbXyUz9r+fzH/1zfRNgANDBXQHER7G8AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 152,
                "name" => "Nicaragua",
                "isoAlpha2" => "NI",
                "isoAlpha3" => "NIC",
                "isoNumeric" => 558,
                "currency" => [
                    "code" => "NIO",
                    "name" => "Cordoba",
                    "symbol" => "C$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxNzg5RTkyODE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0RjI4OEFCQTE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjE3ODlFOTI2MTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjE3ODlFOTI3MTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+KfyaKgAAALBJREFUeNpiDG7/8p9hAAATwwCBUYtHLaYZYPwPBANhMQsQfwFiHuK1vGFg+PWD4d/rBwwM//4yMIkrMzCwcQLFhUmx9wsoqEny8ZdX74AW8TDEPbjBEPrgHsN/NkGGb+++kOrh/yyk6uDiE2Y4de0Jw+VbXxn+MTIxHBN8wGCtJklWUDOSlBo5WBjkud8wnHQMB2v8zHwbGAIkW8w4cIkrpOPraAEyavGoxVQBAAEGAHrPMKYO1i6mAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 153,
                "name" => "Niger",
                "isoAlpha2" => "NE",
                "isoAlpha3" => "NER",
                "isoNumeric" => 562,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0RjI4OEFCRDE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0RjI4OEFCRTE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRGMjg4QUJCMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRGMjg4QUJDMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+d5ixUwAAAKxJREFUeNpifBDExkAbwMRAMzBqNP2MZvz//z+NjGYhUt2HZV0/b1xmU9EUjKsi1uz/RIBnpWbXFRjuWDFcV2J4mqfz/+8fYnQRDusfl/Z92XuKw4CRRZKNU5/p68Er385spVI0/vvLAI8ORgYQ+98/6hjNoevEZ6v76/z//y9//Tz/j8dSmcvYk3opBKhkaj3DrSsMShoMOa1Exj0tEx/fRp3RjD5qNG4AEGAANauB5CbwRkAAAAAASUVORK5CYII=",
            ],
            [
                "id" => 154,
                "name" => "Nigeria",
                "isoAlpha2" => "NG",
                "isoAlpha3" => "NGA",
                "isoNumeric" => 566,
                "currency" => [
                    "code" => "NGN",
                    "name" => "Naira",
                    "symbol" => "₦",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0RjI4OEFDMTE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0RjI4OEFDMjE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRGMjg4QUJGMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRGMjg4QUMwMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+qCpo0QAAADBJREFUeNpiZGgPZMAN/leswyPL2BGER5aJgWZg1OhRo0eNHjV61OhRo2lnNECAAQBu1gQALTkVbAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 155,
                "name" => "Niue",
                "isoAlpha2" => "NU",
                "isoAlpha3" => "NIU",
                "isoNumeric" => 570,
                "currency" => [
                    "code" => "NZD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OUQwRkFFMkIxNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OUQwRkFFMkExNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMERFNDlDRUIyNzJFMTE3MjI0MzM4RjQ4QTlENUY2QzEiIHN0UmVmOmRvY3VtZW50SUQ9IjBERTQ5Q0VCMjcyRTExNzIyNDMzOEY0OEE5RDVGNkMxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+gBm+VQAAAyhJREFUeNq0U2tIk1EYfs/Z59znbbPZ5qU0nZp4qbRES41IMLooVn+meIkuNLtQGlhmhFj9iDAoyh8VlYRgJSVeEiSzmxgZeUMWbqNsOi9r07Xp9rnLSabkj5xp5MNzOJzDeR7e87znoJ8tje7RUXUjdMO1xkCRJ8eNHbxBmDCstLT2qtMT36qwVWtUDE66Ra3JovpC+FZ9Aq37oWBhDgCC+UHYbNP4WBDV1SxNrH+Wmp5WyQs7V1wDtnbxqaLdfk/gUcVXj5uSu1MwpKZZzve314Yci+odjaC1+UF+nfA3GI3ZuGwgAJWN1Z8or4p7JW/ayRWljkoZSAa4DIyZABYWxUxO3rCIT2/LrIuIzGhX9CpgEWCBK7Up1rdGJjkVI7B8kaav7x+SH9IBp3VPXU8DbK5ap/wYumpEYIpOyW9knn5u9t0WG7A2EKDbLncYiH0gpHv80KPtjdXMGV8ppPgarmK4xyn+okZGeXb7f08tW6MiwXyNwcBuH/UI8AaCLccrkb+S5dB31lo1kAdjOdnEvrLyQVsKxAtKISDj+nYFWZcUnKwGMO8H3VmYOTNN5h3bTJwIQY4J0xxUHqVMGQe0kfF9Cj0d4R0pDoGg94W84NyrfTEFbfdK072yrkBzE+Pr+5bn6mMyrFjBdREVs0EGC1VtDwsRXFRv5BcajgjETkdyWSpzm9yjevWuCpFcCbWBNHWLHwc5kpUT0KpiQhvDvC64dI6i37deyJqFscGGd2TG9oi/hd8tKcms3lLSX3mp2zkPu5eDeVx3klsWmtIm9Usq2mt95/3cVd+hMbIW7OFc4NRtiUgg/dBypjPjpf+IRQgg4/Jp+IThDqA0N/ARyt40hSf2557f+aBgg2Frl9ZtAhYHNNFx0MV6/wUdrelThQucGBsIRTyR+gd0D+uTRVI1sJipMY1RPqBJ2ZcSxGNs+tfABTxf1YTYQ0a26Wl4SAI6/eGZnv5B5GAfFngeNtusakgloYAgx9khWCIQmpNiQmA5QGwE/0Npi7NGeFmM7YlgWDYsozUFaGoxH3fpeTDUhCEc440WC///9ZNw6HGTKfSXAAMAWGmKZjQmfUsAAAAASUVORK5CYII=",
            ],
            [
                "id" => 156,
                "name" => "Norfolk Island",
                "isoAlpha2" => "NF",
                "isoAlpha3" => "NFK",
                "isoNumeric" => 574,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OUQwRkFFMkYxNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OUQwRkFFMkUxNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNUQwMEJDNTYzNEI1QTgxNDEyM0M0MDY2NUYyMzkzNUIiIHN0UmVmOmRvY3VtZW50SUQ9IjVEMDBCQzU2MzRCNUE4MTQxMjNDNDA2NjVGMjM5MzVCIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+7DZ3KwAAASZJREFUeNpiZExnZMAB/s34x0AIMGUw4ZRioBkYHEa//fLu3tv7NDF636VdnWs7aWD0/3+7r+zddGn9yTvHqGz0+jMbbry+xc3FveTIMiobfePlrb//fisKK7Iys1LZ6CdvH3/89oGdlePKy6sP3zykmtE/fv44cP0QIxPD5WdXdp/fffrOGaoZ/f8/gxAf/9UXN7hZuAQE+M88OkE1o4/fO8bDzivBL3X93a2fDL+4WLipZvS8A7N2HN3x4vNzhq//fn35s+3qjv1X9xLUxUJQxdN3z378/q2tpfvyy3MWXmZ1cfXfv/88+fCcCkaL8AovzlrKycqx6PCiE/dOTIufBgz6X39+UsFodlZ2aDr5/fPj149ABiMjQhAPYBwtr+lmNECAAQAuen74A2fWoQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 157,
                "name" => "North Korea",
                "isoAlpha2" => "KP",
                "isoAlpha3" => "PRK",
                "isoNumeric" => 408,
                "currency" => [
                    "code" => "KPW",
                    "name" => "Won",
                    "symbol" => "₩",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyNzJDQkU3MzE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyNzJDQkU3NDE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI3MkNCRTcxMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI3MkNCRTcyMTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+uqrYYwAAAdNJREFUeNrUVc9LVFEU/s69982bH82QzUjOiDjpNoSgraNIuGhR2apVG6GNJEJBLtxIiP4jA26CLCFwM/4KESlaWLTIhVIKJjX2Zkbfu+/d2xsJsnHz1N7Cs7hwDvd+97uc7zuXcGsa4QRDaCF4VIL0/wfWJLwDERbrxz1Jk5+gTf61gCvrq2H8SQOHv932NJULhSScfw4KgUoFW1+RvQrG8G0Hba1IJuG6waEtRMSv9U1P2cf6yrS1r2Q18eSZ6ClAK3dltTY1QSxKTVfgeQHRq8wU2qejIn9rnlI/tlMvXpr379ozs6hW45PPjb7ecn8/NyKIB22M9qEbSur7rjnwgF/LV4ZG5PwSlJJvV6KPBqMPB+1ikeVaz65rLS3RdZ2aLntfNuSn9/LzB3f9I11KiJs3tFc7l2WIxeTiMs+3x8dGCQaBx4aHeGeHLC34/T0XNGvJOaWSM/OaDCP9czdTs1hzxnkz57ya5S1tp9M1LEsfVwjnxMX+vTuJ8QmzOaNB7tq7ytMRIKalg8ODwJwdKnd3+7pGo64tbO8gna6ne3vIZZFKQbrBKdd1PXV71BQnhwhBa9hHrzFN0OnsWHejqym8oRrq5Ds0Lt5XECL0bwEGAHp0quOMFme/AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 158,
                "name" => "Northern Mariana Islands",
                "isoAlpha2" => "MP",
                "isoAlpha3" => "MNP",
                "isoNumeric" => 580,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OUQwRkFFMzMxNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OUQwRkFFMzIxNzg0MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNUVGNjQ4M0M4QUNDQjhENUNFRjMxODFCREQzNUFCMDMiIHN0UmVmOmRvY3VtZW50SUQ9IjVFRjY0ODNDOEFDQ0I4RDVDRUYzMTgxQkREMzVBQjAzIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8++R9VmgAAAxxJREFUeNrElV1IU2EYx5/3nG1nX7rtTOfH3PxqFplgH1pmRsaILvpSiYToA6qLIIhuveyioKKorhIyC8NEKkyCAkspjKJyRYZWOtfcMbe57Zx9n52zc5rV5cwZSM/NC8/D++Pl//yf50Vw5hksT2CwbCH5WxEBJAHiciDVJimjmqVzSEWc1LyjJeBmQRUGKQIB/QMaAceDqsCq/bE3fKmpkuQshY+mvA2qIIXxPUWHuiZNEPKBGkBMfx+HuiPpdWKFbMzQFps9T15u3C6Mq7YMSNb0fsfyDGTrqmBz5F5RYUF/yAxhFmRoKWgexySJjprkSfZqrL74uOdC+3MmOmorFvmnocp+OJCn4VqUQ7q8xifOJIhC2pYtgA4kju6oOCFe7AwKpwJnhx7PyIXxmy351Sb1tWH3yISuj9iKfC+aYw7aZP3o9IBMkplDBIQUWbv4Yczvvc7sHxudhYL49Ewov6pOX7XV6+YgN0HbHTeCzVPI14A7gCCW0EaklbM2r7+0zC23rNLOtB9dQ6oqSHMBCaKt4zAdiZy89dmFVgDOV2qHteQu2keDLJNXI0Csn9NUs+XH4lHPl4DyxjOnMUf3y4yYWa9uH3B+Cag4YHIte/32EohHUrpmJogIUqW+l7qNy+3bykvEJHa3j+qxzf4udr93d/fPiEhcX2R2Ibbz9VscV6fFpM/FI9hYQiOLfD29MRc8THZd8Z4Vsvtddx50dzWvVOhqjOCN7ysMrxbc+Q2bmUBwKeZjo1mGtY15aMfKxChR9uGtnXC+iHuogM/z1OYYnJA37a64UmVPZpWesxspigEFljFaipg5cTSmtsq/HaxgCb2JcjpK9QSB4yM0YW3cdLN2MsFxLYO6VyN+yCFAFDNGp4KAH17soUuxU0+1WuZcriDNy4I8vkEbbasPjrnYlsH8l58SoJcsNOlokaUalZYUa2pDA+uSjnJjYYryYXruo8zyRlrjnWZAgxbcIIujMQTh1NxLyrIpM07zIv5JMDIRHWARUGILYxddqvOTKYIydXB2MNiThj9JVWzeRuL/+wqWEf1TgAEAg8xMLtJFnKQAAAAASUVORK5CYII=",
            ],
            [
                "id" => 159,
                "name" => "Norway",
                "isoAlpha2" => "NO",
                "isoAlpha3" => "NOR",
                "isoNumeric" => 578,
                "currency" => [
                    "code" => "NOK",
                    "name" => "Krone",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNDZFOEY0MzE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNDZFOEY0NDE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjlEMEZBRTM0MTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY0NkU4RjQyMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+9564bgAAAchJREFUeNrslc9LG0EUxz872Wm226baJKKHSNNTwVbJQRC9xmsvvfgHePEmiH9Jj14KCgrtzUtBsIdcpLRioVINliIKkkMTxehisma3M/lBG9hoXUEP+mDZmfnOmy/v+97jGYcvB32CrFrFdxye/tzg7VqJ6ewMPLJY3nzHaydPaSSLSKVACMKY9ipz81YW3JLdPWKT8/NgRJ+3MMP4e67XvqpH123gIYvL9IrFYKRSwT89VSRe++ORCNRcvDMHtG9Y4vjWl2BEE9bU97CHXreg9ipCV5KqnsDgKMn9PFhRLUE44qWCFYxoSQ2B93Wdj6vr0NUFUcn8wgo/Rl4h0v1wpFPhhyI2sN4cq3/swltPbOjrBk+RHJTAqVy7j1Vx1S6/5nlNBfyG/P/jc1nEc5/3giNuSf1rl9XcNz4s5uCBZGpynMzwAOJZf7PqQ0ldNvxiB2KvWVy9z3n/aZOJ7KzKsc3G9hyZdFJJvqP20fZWu4rUpRfDF7ZT4uSAgqlybEiQEfblYzLf1/g9NIaIJ8O3k0gk6DidbLsudz36ltVUfiMSYdnUfcMSY5qdC6qF+f/kUa+1vFI28GuMxfvpdGPEsVvgjf0RYAA4FZnydWrl6wAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 160,
                "name" => "Oman",
                "isoAlpha2" => "OM",
                "isoAlpha3" => "OMN",
                "isoNumeric" => 512,
                "currency" => [
                    "code" => "OMR",
                    "name" => "Rial",
                    "symbol" => "﷼",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNDZFOEY0NzE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNDZFOEY0ODE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY0NkU4RjQ1MTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY0NkU4RjQ2MTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oZcAewAAAahJREFUeNrUlbtKA0EUhr/ZS7yu5iIWIoKN2FioYCPBS2NloRaKlY/hY/gSVhFUlGChWATEQhFLK61EEhW8JMRks+OZbDotLDYJHhhmlx32O2f+/8woPz11B/SitaZUhP44WDa8vkB3F5RKmkTSsveyPWYmonBkjPFVhpFRUPLmxgQs//c86BTwzRUCJOoQsPog1uFxew3La5LGOOgAHu7hICOJuLJERQ62CGpQqcDKOvT1w/QMLC6hh4ZhcyuEVqtNAJtiip8QF23nFtFeH/ryAnWahfwT2KK31YyKjZE6OuFoHyWVqcwuKncGs/OQOzfmEkHcyHV2CILQTOkF9OEemGF0TaZgdQMkEbPV+uRYvC+GQ0cCVn568h3b9erb+VxotJMVtlNC4DFJoiyuL+SpJxkR2Kn3UM0Xg/mhuUwYQDwBfjV0uDFYaiDqdmqE7fz8aoz123Mk5mpTOH/OULcaLPIqgaqiaB3o8FhtBdiqaiquYjvdw1u3Lb0etAas5EStyb2xMyEXRkqWl4IWbrVMg+818qbXy8H/dnVbwV67+vixHfBvAQYAQ9x1aPT3zwwAAAAASUVORK5CYII=",
            ],
            [
                "id" => 161,
                "name" => "Pakistan",
                "isoAlpha2" => "PK",
                "isoAlpha3" => "PAK",
                "isoNumeric" => 586,
                "currency" => [
                    "code" => "PKR",
                    "name" => "Rupee",
                    "symbol" => "₨",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNDZFOEY0QjE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNDZFOEY0QzE3ODQxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY0NkU4RjQ5MTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY0NkU4RjRBMTc4NDExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+95KRfwAAAndJREFUeNrElj9IW1EUxn8vRqpNrS8xoKnW0qBQzOTgpHZQoXTp5OBUOogIlcbQTScHpVAdhEKGDI4OUsSp4CLS0rFdirqorTXSanxIbXwxMXm9793bJhUpSB72wOG+c+6f751zvnO5mmVZSeCW0GPOydSbKcZfjUMTbkqN0D2v+qBkvAqp8fCfxFPWzh+gmRqaV7sCYE3t2oHOe530dvRi/bTAUv6c0IxaW1DrXQG2d3yB/gf9zI3Msb67DnkFIEB1XSfoCaKlNKp8VXKubGD78H1oibSw8GyBmcUZkh9FU9xU8ycQrguzPLHM2OMxcpmcS8AFGVX8SdwxE+8SEEIefiZU9EhXSxftze0MdQ+Rt/LSf4F4LwWcBv9dP32RPrb2tiiYhWITVsgwEm8T1FbXsp3aplFvJJlJugBsQmtDazHtXpWFCmULzZ5lnRKcGCLvdcLn48J0ey5PaknTcChMW6gNjL9PyRfynGgCtEEYlbhUY0HSzf1NzKzpmLFHMaeX7Uz8OUlTgPZ4SpHxZQHfgNRWiqUPS445eH+Q4aFh+GzfvkKzimRp2XLNgWYC/oDs7bKAPbKeE4sTorQFxxUfiDP/cp6mtqYi6HWYfj5N9GEU49BwgVz27VQPG5826JnsYWV8xan5QMeAo0fmEZWeSnzXfKx9W6N7stvpbXS1t6ybyw70Nqy+X6V+pJ7Z1Vl2jB1nSq/WMXMm0ddRIk8jGN8NyWyr3Ih/R22T5Q4cHBww+mKUWChGIBggk82Q3k3LKBskGd25QErBbbb6ZRqtU4vDr4cyf3ZagyVrcBO49Adsuab0vJ9/Ax+r18fxVb0+bKxfAgwAbIbG6d67HZUAAAAASUVORK5CYII=",
            ],
            [
                "id" => 162,
                "name" => "Palau",
                "isoAlpha2" => "PW",
                "isoAlpha3" => "PLW",
                "isoNumeric" => 585,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyNDdFNkQ0RTE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyNDdFNkQ0RjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI0N0U2RDRDMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI0N0U2RDREMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ffmgGQAAAhdJREFUeNrElc9rE1EQx+f92GR3ze7G2pi0sVKrJ5tLEVSE3kRD8eDRixcP6kVEEHrx4v/gpdCDB0EQxaMHBQNtb1FUMFpbwYrWxJhks5ts3J/PtwpSaLNhK8HhsYd5vM+b2e/MPDT3qALDMQxDMxqxh4Dxr+WnOr7KGBKJpdA2hoDxnX9BU+S2vZGanT8orc+kVijyq3Z+rVMQSW8s+dlndOAFtB/3mz0hIPfm1Hwx8yCf3OAc09WW28W7n268tY5Nye84PRqNtsvIU255o4zhhem5Qq4MNoAH4b8hACL8tKTLr5+86pyclD5E03eQMQDccPffPnylMFYGE8AJXSGaX2CCKPXuHD2fFhqGl45dIT+c3Cnt2ensY+juoCx3qqp+IbtQdSZiow0/fVwrgQDg9zkUwAntuYy7ASNx6xqptBUVTwB7hfoeYvgQG806nhadqu7uswIF982rD1oh7bI5G4pG+h56Ycx2/RRBMdGjiepSq7j0vQjy9nyAO3umfL92NZv4GltGHkuaNm99XFyrT4MCoZ7o9+JJKBA45Pr7h3VnXKPN2OiA4Uxik4+OS2+e3lu/1nQyYc9ScIJkafPcxZelFf3MIWl1N934t9d5Tzac7BG5MimvEuTV7AMVc4Zgfzy5wctulzOEm8cEleiqpOveyHLzLAMs4W5O/ELBGxjv4KH6J64UMfjaOgb+/1MwRPQvAQYAH8/QWrO3BXUAAAAASUVORK5CYII=",
            ],
            [
                "id" => 163,
                "name" => "Palestinian Territory",
                "isoAlpha2" => "PS",
                "isoAlpha3" => "PSE",
                "isoNumeric" => 275,
                "currency" => [
                    "code" => "ILS",
                    "name" => "Shekel",
                    "symbol" => "₪",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjQ3RTZENTMxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjQ3RTZENTIxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMDdFNzEzNzU3NjNERjUyQjNBQTU3RjQ5QTU4OTk1RTciIHN0UmVmOmRvY3VtZW50SUQ9IjA3RTcxMzc1NzYzREY1MkIzQUE1N0Y0OUE1ODk5NUU3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0ETvLAAAAYtJREFUeNq0lT1PAkEQht/Z2zvDHSCgiYbOwsrGvyCJhbGw0tbWCDQGEwtbC/+A/gBLeyu1ozBWGgsrGhI9SbRQlBC8G3fv8APouHUzuWQvm2dm3/lY1DJZSIF/WFadqGM7dWaEoWE2k83S3vUyEKZjb0uHlQPb2XEzsKRJ9LtGk6Y7TjmdhrCMo4WmS6eqlJGWSXQIEcR0x9ny0kaUIYV2P3uK3t9DebFO1tcmanuu/xQEgYGoY+vHPp3ny3NOtobRsSn6G/BweNDpdsfjhmE4LIiudFBU4UET8J5fCoX8OO3CPNwmqiNjbis7mbq5HY8b5YxGtY5qfHGZXxNKzXLQlba7HB3tFzPNY2o2OPrzRy7+Pjhwe4wcIDVBfrRm0um7n8JShXw3QCPhbIL8jZdxPYtSBe1cgEdgJglYgIUU3N9dFalUpQ8vgK9mbeJWhJoXekrzxRxWttFLMXzWJUJJJ7X6SJtxNi9WyxZI6RDqeImMPATydIE2NpU0EC1iy9IZ5uTPi772lwADAA1uTobZeCdSAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 164,
                "name" => "Panama",
                "isoAlpha2" => "PA",
                "isoAlpha3" => "PAN",
                "isoNumeric" => 591,
                "currency" => [
                    "code" => "PAB",
                    "name" => "Balboa",
                    "symbol" => "B/.",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyNDdFNkQ1NjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QzM3RkFEODE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI0N0U2RDU0MTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI0N0U2RDU1MTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+IRlRqwAAAbFJREFUeNq8lksvA1EUx//3TqcPtCpSz5BYioUNESxJvGJBmlhZS3wCH8CChYhY+QhsfQQJCemiC+xKUDSNKtOhNXPnOvWKhZFcOj3JyZ3JzcnvvO65l0kp0wDaSA0oiFMwcdo9BesqDS0aVTENk177Pj7wba2GhLnbjiTNmTaEIz0hu4ILRYHl3UtkDevLEc/BRxcmxjdOsL5zjunNU+yfFcCqAe7vrEV7OcTDLJop1UNddRVPtc9tY3S4CTMjrSiUBCQ5wVgVwJaQmB+IIeTnKFoObIpa15j34DLkExTUuWvbi4cH2MgDeXWnfM8Ukapo5IxlUxZaYpQeG1pDvTq4YymhbJR7EuiM6kjubaOeVkH/yuC722f1AtHZzllB8EgECGjQAn+pcVhXtyq3eB3Z/VJakX+EFqZjqHG1yfVnoaZzDBOZ1S3Y2Tu4jb2Kg58Sx0hNL+BmZQ2p2UWYB8kfM1NxcE1fD1hrCPfIgDUFUDvYqza5/iMNY5NojMfpzqbGLd9unHkPli8WonMT4KEgnGIJUthgXPcezPw6lfQdxIOBX0em8fH6MFAdeWO9CjAAKGmKMiin+isAAAAASUVORK5CYII=",
            ],
            [
                "id" => 165,
                "name" => "Papua New Guinea",
                "isoAlpha2" => "PG",
                "isoAlpha3" => "PNG",
                "isoNumeric" => 598,
                "currency" => [
                    "code" => "PGK",
                    "name" => "Kina",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0QzM3RkFEQjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QzM3RkFEQzE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRDMzdGQUQ5MTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRDMzdGQURBMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+A8tj8QAAA/9JREFUeNqcVUtoXGUU/u5z5t7x9s5MpjNgbLEzNYyEJkwqCBJTG5AiLUJXCbgRpBBSQrPpQhSSjeuARVG0rq24UOJCu5AuJAsfCRFja5OGpLE2tslknjdzn7/n3ljJZGbMJAd+uPd/fed83znn5wCwrBDCp9oxvCipWHBqcGiSx8GNEwB7gwNHh8U4A3Nb7/XvL99xTZwpLOF6LY9uMYwY3eCAHQyUbrIINNzpQe1x4Brc/20v/xeY79xI+U+MVh5AJ+A0sdAuuB+ZU+YgdjBkvysift6CYVD4/nGudcR19sn2Jl4rLuO+a6FXUII5ryXiDr28DFQsCUdesCF1m3CK5AQtOiUOrXxvKuVPjoGXifobZgG9RL1GPDZE799JU8aGgPAJF+lLFYSf88DWJCTfMJH7IQ+1y4W5JQQyNEhDo0RDaxXUmKjjvehxGMzDqmdBesIdgVoFAR2vmtDOWtBOOVBPu3D/5iDotPisi+2bMhYu6gFlgsqaa7zXRFHE0+kTuMaV8DpRv+k56BHCAe3+FWaBR+J8DSdv5pF6uwy124X3iAP5B2bRhg0eyrkaOscMWCbfQHlL4KGhITy4t4yxy1dwy64G1H9rlZEj6hWKmoUYBI0h/5mKtdEoqr+K4LMuhCiDuSZg68sQaj/K4DhigFDZHmCxFfDi4iLm5+dx9/bt4D9PqXuxtIJJNYWrahJR3cXKFzLWP1dgkf/F701kuxyIzzvgH/O4O6rh6AUTCO0g8gLqwPfVuJldkI/gmtaJDvL7jmNC5Hy9eYRiHrQzNtLvV7B6NYK/bijIvFPBw+sKvAqBSW1ovNdCoRAEQQi+v7FKeKVwD7+4BvpkBSLv17ALe4vD+lcKFgajMB8KiPdbCFGSKWkXToVvrnEkEsHk5CSmpqaQSqXqNvn/c3NzmJmZQSwWC+ZWqc7PEvgH2xvwW26ckWoUsRpzUF2izkdOiJTJhVsypKNeQznXuTEyMoLx8XHIsly3ybIs0oeB5/nge7ddoU53iTpehIr1JL/T7STdg7nCw/iD0opkri6IEBXWuo4zmQwSiQRmZ2dh23bLMnMcp2G+V1Tw8VPP4LSk4DebHhrCUY97sB9zsDd5iHrdo1HeN7kGBwcDGaanp/fPAyqdDwn8zXAcy/TwbBoeJIFeKz+zGdprIE9M13Ukk8m2EtAkOd4qr2Gc6O/gRGQiEhzZa9qvD1VO7dhLUgQfUfRdlHi/u7Wg4/HtRJzL5TAxMYH+/v5DAc9QtxsoLuFrq4ge0t9PPndX6C2B+/r6gvIaGBg4dNQFz8VQaRXvVtdxjJfQScPeBV76t+/XDU3T2PDwMKMsZ83WDzrOyRpbjGdZNXGK/RzrKv0jwACqn6gx8GMIaAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 166,
                "name" => "Paraguay",
                "isoAlpha2" => "PY",
                "isoAlpha3" => "PRY",
                "isoNumeric" => 600,
                "currency" => [
                    "code" => "PYG",
                    "name" => "Guarani",
                    "symbol" => "Gs",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0QzM3RkFERjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0QzM3RkFFMDE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRDMzdGQUREMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRDMzdGQURFMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+8tTVLQAAAOtJREFUeNpiPCeo9p9hAAATwwCBUYtHLaYZYPzz+csnIM1LtEsZGRj+cHMzfL10ieHn928MAmYWDOxA+u8/knLlZxZmHm6SXPoXiJ8d3s7w/OAJBl5uPobv314yyDj6M7DQPKhfvmQ4MLebYfqfNwyb5UQYLqxbwvDz6jXax/G/X78Z2L7yMjAKcDPc+v6I4ddbUHyRXvgx/v//n6Q4BoFPGzYy3Nm7k+Hn/98MqrZODCLhkaTa+5nx09dfJCYuJoafnMwMXw5fYGD8/ZeBy8mYgePHP4Z///6SZjGDxYrRSmLU4lGLqQIAAgwAPdxSxGn7ZuUAAAAASUVORK5CYII=",
            ],
            [
                "id" => 167,
                "name" => "Peru",
                "isoAlpha2" => "PE",
                "isoAlpha3" => "PER",
                "isoNumeric" => 604,
                "currency" => [
                    "code" => "PEN",
                    "name" => "Sol",
                    "symbol" => "S/.",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3QjRGQkQxQTE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3QjRGQkQxQjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjRDMzdGQUUxMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjRDMzdGQUUyMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+b8OOOwAAAGlJREFUeNpiPMPA8JSBgUEKiD8zYAF/gJiNi4tB7/FjBmYhIQZ84N+PHwyXZWUZvr95w8CKWxkvED9jgTIYkGh6AF4mhgECoxaPWjxq8ajFoxaPWjxq8cBbzAJt8vDiavrQovUBsgsgwABVWxD3afjyOwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 168,
                "name" => "Philippines",
                "isoAlpha2" => "PH",
                "isoAlpha3" => "PHL",
                "isoNumeric" => 608,
                "currency" => [
                    "code" => "PHP",
                    "name" => "Peso",
                    "symbol" => "Php",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3QjRGQkQxRTE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3QjRGQkQxRjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjdCNEZCRDFDMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjdCNEZCRDFEMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+RLn3GgAAAxpJREFUeNq0VUtME1EUPTOt0JYpnwJCVBQh0agLZWsgMXErGze60R0uNDGiK6IICiqkAgFjRFjgl5AQDX5CMEDih0QSNBiDIJRfpOVTSqG00EKZ97ydwWKEhXx6k5np3PfmnXPP/VRo+Dhmy7k7YOz50APEi0CiEZAZwBFSEzgZYEfZEzvy7vfB3T0OpEQBxm1EIHToInw/3ehPQ/ZZD3rfncS5S2mAZxHocarAGiFEEfsnZ5mr2Sga04GwJMXZ0TWF/IpvaGwYIB/JvydSlZ7zLZV6lp7GtRbrGodxvbwTlnaSP8GgXksshMDcTyvawLLyWlTVhYKH3zFvmQFSKf8Rm8//CjCbp4wbFCcbuQDoj0CMywputE16ca3kKx7V9VEN+FUCWkoD4xssrj9BLgxB7s0A99HBsosoieDOWrCh04FV7IzXo6YoHZ9fZeJ4Ziow6AFG3HQCqSIIm5OaT5jBHNUETAcKeiB8N8Qd+RCkY6s+rG2WcbX4DYY7xqj94oFI2i/L6wT2WYzMUakIwO0l5A2nIvIB0mGIUSfAtTEQt2cvC0TSOosBkwcMhShr9cJc0MScjikmSvr/Dl2rRApZjRKBitWo0gWO4Ax8yakS+dvkOboZFBpXlt4iw1rMZ20urpGkDUo9eQ9sopTwvQonQZcKMalcKbR/baalG7aci3B9aUWcGA1dVCT4uqUmYO7tBhs+A01yDZidwKSjJDcV2VwbxJSXwQ98vRZYc+/AXv9UEUUXnaoWGF9ff2uDDMJ2QbP/03JLMQhsEULCZfp9Xm2xOS+seYWYqKjCot8BfcReiDod4ckbmmhrDxCZBoVGCvKyVz/GaKEZnl8/oBMToY0xEeDSpgaIdk2vJlp5uJpaYLtRjOn2FtooQYo9SMHJmwZVgf2jlNNKCKZT1LqH1Dz2D8KWfxv25zXKuyFqH5ERtwRwBZjNQVh4TaBZYD4G280CjJc+wOLCBPSGZIh6/XIe2Rb/LXK1MhzPXsCWdwvuwU7ohARoTbFbGuGqiN1tHaPWXLNx+n09jQ4DJNMBGigspKAB+y3AAHmPY4VHbneXAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 169,
                "name" => "Pitcairn",
                "isoAlpha2" => "PN",
                "isoAlpha3" => "PCN",
                "isoNumeric" => 612,
                "currency" => [
                    "code" => "NZD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6N0I0RkJEMjMxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6N0I0RkJEMjIxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMDAxQ0NCOTM1MjkzODNFQzU2REU1MTU0ODY4MzQwMkMiIHN0UmVmOmRvY3VtZW50SUQ9IjAwMUNDQjkzNTI5MzgzRUM1NkRFNTE1NDg2ODM0MDJDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Q37dSAAABJRJREFUeNqsVGtMU2cYfs/p15bTy2lLKQUL46ZcHBlIXbEo6hwE2QZ/lhFcFsMuWWK2OTez6eIyl2X3zR8z8bIoW+YSF9wCGeiUjdFNRRJH8YroqEIr0AKFXml7ei7fTlGj/Jibxuff9+XNk+d9n+d9Cd8JmzaFHllY8t33f7oHXabMtIoSQ6HHEdWl2nnlhcHJgFpnlseqmLGMesvvw1y/3amh5XBXROI4P0OJdrUMkCf7N766siAgbXWT579oebFp6f7er4Yrqt6asF7/pXttBrO+4RFUuODzA1ff/7ov6nCDmro7NYTCL2woQyP5pc0OOtIb2FqT3thk7fzomYBn6lr7jrjG+M4667I1kSWNyx1S07tdUz+1n4yyDJh0IEXzmQgAPO8ZFSS0ihj97Etiz8GxtFzExpDTubi6JBbhLnbaZZkpi42qqAT5MvLGTg+ohFhpjrp+orAjaAQUu80jkMDLgOQBxQETNz9HQy+/YSYxoFnMeZ3XWI2Srlnpd3pCvhDISJJlpcVF4TiesPViUxqj13AYGIIUyQBh8FMwrQaxTj0t154xcJOIwBCXQpgGjIBINIG2+TMOlG3ZvbWqtpCDK0PNnS65dcljTQ2B6scPfvLpCta/LDzc3372wzOsTWryey8B64W4urLSiaQx29FSbb5v1cNUkk/dZqc4SUS7aCToNQiYFPWTT5Sn/7FWtcF94ueNuy2Nh17afu744XOmDEk2JWxqaM4p372tbeahTH1rrWpPuCct6geSAg5bF81kF9shdXCBlqlbFWAMPl4Oe7f0VK7oERg/sErAGOn4+K/fdG2a4s5CFgRJ0ET/GvK/HTIHLyEQYnB94uPNLTvXmJ+vy7XoUgwSj4enZMmRFqcriiSg9utkKveoZ4S5UL3cUmMJa/9W9+nk7mEBE1gioyt3dox5KCPgKKgJQiWbHJrugWz7OBf1emGBGpLp+MjU6bZzbQ48JVeClAdM+mK8CuSvr55dmtvrHqx7rmrq2NBo8+GyJ82xI71ZITdeWmkgJfokkEiAEkBGiA5hDgsaOaAgiCapKGAx8AJokiDPAHrRc060kffJFkuz9j09Ubga2+hXYjnEt2PvaYrMPsn5+s0Lx90qoHgxjuRcIolb8bwjm/jOsN543MovoyooP2UL2Ndvf63PtaG2fuAKm3Zx/M2mErRvx2HLo1chqhCLyXmE/xN09LducySYWmAO1uR/IHinny3ZFQk6HTFmxptz+XKmuOoiNYL7gIRhQroieYXCcMQb7jrUF9fn9ZlJoq27oONoMhemQBEU2yNvdE/8F25PSoRCYL2K/a1F3tGAy6W1LmMuXDSaaOdTBQbOsxCUs4kVFVcGJ2Yo3JtqHkNyZJwRMidyBpzFqYrj9mMV6aUzWB5ObPxN88SlnHMIYzz/xPwr5ipFSdxMmDp6ihZz9ePOdaCd/eGInkQCke7HrMgpiEUoEZLEWPC9CRcvEeKBToQMjLGEyiSRjwCR9xYVeUew7g/E3DGaoyPm6UNCgAEuAC4EDxCsTwhEUEl5+qqwxZisfIDM0wGmuNT0jwADAIjDExM8WLDuAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 170,
                "name" => "Poland",
                "isoAlpha2" => "PL",
                "isoAlpha3" => "POL",
                "isoNumeric" => 616,
                "currency" => [
                    "code" => "PLN",
                    "name" => "Zloty",
                    "symbol" => "zł",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCODVEM0U5RjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCODVEM0VBMDE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjdCNEZCRDI0MTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkI4NUQzRTlFMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+2uLYNgAAACtJREFUeNpi/P//PwNtABMDzcCo0aNGD0ejWe6K2o4GyKjRo0YPBqMBAgwACfQEVtJhm9QAAAAASUVORK5CYII=",
            ],
            [
                "id" => 171,
                "name" => "Portugal",
                "isoAlpha2" => "PT",
                "isoAlpha3" => "PRT",
                "isoNumeric" => 620,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCODVEM0VBMzE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCODVEM0VBNDE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkI4NUQzRUExMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkI4NUQzRUEyMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bRhArwAAAq9JREFUeNrElslPU1EUxn9voo+W2jIUKjiEIYpaNQRMVDYuHBIT3JqwIU5/gDsS18SlJG78A3TDBldiDEFNZHDHQjTBGMCWUQZpSynv9b3rrcXEBaCFoie5ue++m5fvne98Z1C43TwNVMuVYCczVHxph6GRZQ7JfUWed2l+uWb0zQd+2/+F+VX+k/09sADF1lDSKoorz8p+Aisit8c9IMEqA+vYNRaOoqDO6rnrXXKm7wiakdcplabILL4qm9MVKcpr1/g+6UGT8lAGAihBcEw3x0JhPJZcxlXaIws8OqtTM1DJen8Ja699JN9W8aWjjOTdNcSyQHUKSfWSSdu5aZ51zLHWB92jLl1OA/FjTaD5SfRpJG9tkLyxLL038o65vp2QMFxawkkocWkcmCHc1cPgtevYNpxv60drv4lDEC20RCYsXzr5xXtrYEcjUJYiXJzm8fMQja3fiFlTDL5bRQgX24qiX7IJWbDRso4ynMEbK8LxunukWmQLlYumC17NBfGO+ilfMLA8DrbpYEYNDk95SJQI+SOSZXWTpT3HWHdZTJm8j5aSCWaIJi1OuaNcbS3jSmsFJ+0xVsqS1N6LY370YUwbUtmiADHOplLCZHG1mCdtn+h+0MDxF0+50DtPrKSYNwdeEu4sRzszjZ6sQIlJcTVYeXm9fR4H0/SOhTkaSHG5+SsP6yLULw9zZzLFichBMtZnJu/7CfYcQK2zcfOkWpHdKb5lg8h6LUskaYOLdYsUSbHVR1J0Tkilj3iwl7yYg/Izv/NTVHkWkMT2HguZmIbMEU0wNB6S9TLNUixF/EMxR6IqiRkPojKDMETeVWtnqn+BZz0vTctarTEx78cYt9CQ1FbbuZi6+9mdRI56YUoPfQ5CspBv+uy+LRbY9M2Rx//H0aeA00cW64cAAwDqlwCQvzogDAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 172,
                "name" => "Puerto Rico",
                "isoAlpha2" => "PR",
                "isoAlpha3" => "PRI",
                "isoNumeric" => 630,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Qjg1RDNFQTgxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Qjg1RDNFQTcxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iOTc1NDI0M0UxMTY0OUU1MTE4RDY2RTdCMTg3MDk0QkQiIHN0UmVmOmRvY3VtZW50SUQ9Ijk3NTQyNDNFMTE2NDlFNTExOEQ2NkU3QjE4NzA5NEJEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+giI5EAAAAmdJREFUeNqslU1oE1EQx2d2Nx9NyKE9tLb42WNFW8SP2mJPUigKCl4EIyhetD2kJgQrgopoRCliFUE8eyh4sBXFgwcvSig9CS0UayqpFcGmSUiyyTa7mfHtpmnwtmQz/GG/3vvNvJl5b3G/Mv+Axkfg6y+AFIEMiNAckxbp8Fn4ElZimuTrQYFmQmRoAl/ESIwmchck7mLoPH3IVGANTDY7RAuChBVAInaBBGf4dbR9cqBvPZF0qyo7RNP/jxDYAQ9vp8eueQA8xFKFqg4acUOWRHoNgE0LYWpgmL/9YCdWR6NIC2gApS1PpvITNzbUYoHZaACNNZCZDb+/HAppsZhIhWJJvDd279FfTBmnh5aAlBLtRERJlJ3tlVHUsLNTLxakvkP650+wr5vVkiI+pDbclm+B0U4FPk5wtJdWVthbRkWywa7OpCPH85OP4UCPr7UN11PleFyLRtzfl32i8a1loaXCFbgfgUeiIGUbNVQsPs/HfcELajIpZvk1lS9fktNpL4oPpmOpNtgL4JJrfmyhLQC2tXozmc3ZGbW/nxRZxCtzfVnGyZb3t4zxo7S6BIoOKNvcMtbVGB3N/vntejvTEryo5nLud7P+amt2dRWfPTXODS5AhYu8V5JQst3kVvMhWQk0qjFa9zqAej2czec0ZnLS19u9vKXBYX1uQW/SljFjFzlnXwe/fJVlLldHVExRA8IaV3SwOKthBKdvtk+dOPY38dNTLFacnIC1mQp04No9CAfpjThUV7fPKgemmAAZrsKTMbrTzYVlFhVEd1P+MgeVuWl56DlHAlRYJNS5vkMc2j8BBgDaEDAtyJBdYwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 173,
                "name" => "Qatar",
                "isoAlpha2" => "QA",
                "isoAlpha3" => "QAT",
                "isoNumeric" => 634,
                "currency" => [
                    "code" => "QAR",
                    "name" => "Rial",
                    "symbol" => "﷼",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGN0NFMTgzRTE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGN0NFMTgzRjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY3Q0UxODNDMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY3Q0UxODNEMTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+iEFoawAAAcBJREFUeNq8Vs0vA0Ecfbs7uy1toyIiIvFxIiTiIOFIxN3VwdUf4eLiRBwcfJYEoU1VNUpCGjS+Qor0UPER0lJNQxUJJYLumm45UEezv8wmM3uYt+/93rxZTlGUR3wVnSN0cIjI8Tn8i16cbO5BEAl4QcB/F6GP6XvBcRxKaipTExx5d9UPoYNJkd8vfLNL8AxOI34ZhZSlBytkTlEyd36IxrAybIV3zKECE53EnvH6uBMLPRbEw1GY8sxqfxUGrP9k/Jp4gaOzD1tTLhBJUg3GnPHKkA1b1nncXkQg6nTgiaBNj18Tz7g+u8Ta2Ax8zmUIhGjD+GzHD9+cB8H9AAUUwfM8E8YZu2bnmFBYXob80qJ0qMiyNsDGvFyYC/KhM2SnUEEjRJsAuQqcYtvmpn2+AEdl5rSSuralGW29HahsqMPH2zvkjyR7xnJShmdgCpuTLtwEw9AbDenjpFWAJO4eMNfVTyVfAKHO1uQ4rY3a4e4ewWPsHgazSdvIjFGZVy12bEy4qLk4Gpsie8YpebetbtyGIvRWEsGqMlxd1ViPpvZWFFdXqK5WkjIzVz/9dHYSol5SfwLU1BLYXBKfAgwAZJi4vg1/oSQAAAAASUVORK5CYII=",
            ],
            [
                "id" => 174,
                "name" => "Republic of the Congo",
                "isoAlpha2" => "CG",
                "isoAlpha3" => "COG",
                "isoNumeric" => 178,
                "currency" => [
                    "code" => "XAF",
                    "name" => "Franc",
                    "symbol" => "FCF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGOTJFRENBNjE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGOTJFRENBNzE3NzQxMUUyODY3Q0FBOTFCQzlGNjlDRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY5MkVEQ0E0MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY5MkVEQ0E1MTc3NDExRTI4NjdDQUE5MUJDOUY2OUNGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+aVFAGwAAAdJJREFUeNqklttOwkAQhv+2W0CTBuMNwlMIJirXvpByeAopXvl42kRQ8QajjRoVus70gKW03VI22RuW8GW+mfmDhtuLRwAtui7KHI3ugu6bh8GJietjE/iWWMjE9zwJbc+A0apZ89HkSdBHVvhklYa+S/QZekrQD4J64Vscuk/QZg2v4wleho6lo+xZQT302gKjc4J+SfwucqD2BM99B6JRgb6r3l7bhN2t+JX+/tCTlqKXoHOGDu5hMvRAQOyit9chKFf64aVDV3qnmA0cmEdVGHUBuZRbVhzTe0V67S5BP2U+1NcbVBpB+ejlempi3DWz9e6n642gxcEx6ID1KqF7PnTGUNLLUCzX90vfTi+tzLm6Un9luKcNgtY3oWpwAlpYb/8uWJmDdGg+OAbtbwGNpjcPykcoE6lToXAQaujIT6RgkOr50PSKY5Ve+omUBcUqHLins+G9Um82OLEyN7l6dQ78sKfF9KaDE3rtIj0dRdNbTO8meCPw1XoZOhs6W+ldByf02kq9YTgMORzKQYOpjgK/zXoLTK/9oAyHYhW7Mgz8DCj9rlalSptVzMfBnpbVu1Yx9dS1z0yLoO4GlI+hwTgUVOn0P3vrO0H5n477J8AA8FlP8fwgsDwAAAAASUVORK5CYII=",
            ],
            [
                "id" => 175,
                "name" => "Reunion",
                "isoAlpha2" => "RE",
                "isoAlpha3" => "REU",
                "isoNumeric" => 638,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RjdDRTE4NDMxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RjdDRTE4NDIxNzg1MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iOTg3RkEzNzQyMTUxRDFERTcyRTlFRDRFRjVGM0VEQzQiIHN0UmVmOmRvY3VtZW50SUQ9Ijk4N0ZBMzc0MjE1MUQxREU3MkU5RUQ0RUY1RjNFREM0Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+w8A3BwAAA3NJREFUeNqUVV2MU0UUPmfu9La9LWv/KIsFQUB0FQk/0aBxNwFMDLAqkZiYrFljoglBFwOa+CZISHzyQQk+aDA+uEB4ImRFRQgQXH9YeABlN0tWYZdtC5GWdtvt7f2ZM87dTQjSgu1J7n2Yc+abc77znRl8ctXedLb07devrluzoPDOe5EvPj/49KZ/no/0xPqEGS25MxAJQUJzpnYQu2tB/csU2PrXWx9c7da4EwncwGkPYKOo0vtwJrBaX4xPAJ/89NqmjuGtWTveYlzj6ErZGDqB9AOLSzFC7O5CAITUQLPCxtiZW8vaBneeya00gtmQVhb/C02SKf4MsPtdcYhY/SCJyhE30kWXd1z+cNdYF/eXovpNqU4F9IxcmMzDZA6FDV6sVxPGGXPROey6JwgMZPfKQE4lkdCLPl9xx9jLG4a3F5wZD4QynBznxogo5yC5CGa1OZUJygwhmtosTY5Jc78th4DHOOiS379EIVkLqziGdTS34olqqnf2lx10TrR3a8+97Us9zhRx1y+JgYNwap/zR1mcj0qLMIGSpEqNN9AYZEjRcCZTmbm6v+vXndtWvdB522vMXgIv7S6yJfbGzdqDQYzqasO0sQb1hMQhd3Xdi4/diXubunDna/73eyg7OiWkZqCV2a6AgG/b+vl1x8Nr7iudMhbXqnbT0KZF0Zj/0VSw/uwpZhY9ore2ulalaWhEECRduk9PhCDh6bIpaAQ0gmwiLS8MWnVlqkw7e0H7e1SGQoTNQEskvZSAQPWT0a8ArFo68gAHhvqCFTtR5sRYY9DqolGxuaSVzPA3jw3o+14//Vlt1I6ze7rm/rxx97NVHyXTthpElTu/NwnqXELTYFWjuvR8pf04BUzML+sd7v09P/jR8jdWRhcrYfxZvLLr4v6L4/0Qm/vD2vDiefqRvZnll8rlVp3XH3JUx6K/EEfmTKz9vrp0AMwA5uOqCBZZOFK43P3TdjCSXqR5EzQdYw97lF8xx1OBFR8/tOeb6+/23eJ1WqISlj6ej1MiXVpz1J4zDsUIupoaGy9CCjSSUhKQ7YWGU4iaWp1mF7OOjLKeLanfFoZ5jRYAHA6FFqftl1L7jyJUxlxi6l4Td9zX0tOY5v+vRqaFAlqBqAK9G2I1WUtgarieOTn51HdQ9Xm4TDT1ygilDIfYqPWvAAMACcVzuqlUzzkAAAAASUVORK5CYII=",
            ],
            [
                "id" => 176,
                "name" => "Romania",
                "isoAlpha2" => "RO",
                "isoAlpha3" => "ROU",
                "isoNumeric" => 642,
                "currency" => [
                    "code" => "RON",
                    "name" => "Leu",
                    "symbol" => "lei",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGN0NFMTg0NjE3ODUxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQTJGMDNGODE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY3Q0UxODQ0MTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY3Q0UxODQ1MTc4NTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+vo6AZQAAADBJREFUeNpiZNCuZ8AN/iybjkf2koMAHlkmBpqBUaNHjR41etToUaNHjaad0QABBgCcdAQtf60IKgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 177,
                "name" => "Russia",
                "isoAlpha2" => "RU",
                "isoAlpha3" => "RUS",
                "isoNumeric" => 643,
                "currency" => [
                    "code" => "RUB",
                    "name" => "Ruble",
                    "symbol" => "руб",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQTJGMDNGQjE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQTJGMDNGQzE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNBMkYwM0Y5MTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNBMkYwM0ZBMTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+I9CATwAAAEhJREFUeNpi/P///1MGBgYpIP7MQB/AC8TPWKAMBiSaLpYzMQwQGLWYboCRgeH//9GgHrV41GKqZCdgXhrNTqMWj1pMFQAQYAD5egotBoZoiQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 178,
                "name" => "Rwanda",
                "isoAlpha2" => "RW",
                "isoAlpha3" => "RWA",
                "isoNumeric" => 646,
                "currency" => [
                    "code" => "RWF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQTJGMDNGRjE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQTJGMDQwMDE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNBMkYwM0ZEMTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNBMkYwM0ZFMTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+2RPnbwAAAbJJREFUeNrEVstKw0AUPZNMm0QN1hZq8QE+FoILdSeCoHt3bty680f6B/6BH6AbN4Lg1iqC4EZRQcH6wNLGvmxeM96OFdzoKmlOuJnkznDPzL3nwjAUS2UAY2QNRAZJpkGTKWWM/rtPwHyABTZNPnN62b3VdnTEOnHrGNQbWDAfkNXquHKn8eAVyJ8ics/miAMiDZtXsZPbx2bmhLYR4s6dwG5lC6etJUUePbGkkExgfegC29lD5LiDtjAxazzRaOHGnULNz1MhIiemNFMtF61bCAof0kZmzBc0wwFMpt+Q0x1axGIgZlIN9944BRfgLMBjZxSDWgfvQQYdaaj5GIhJucLAUWMFBx9rqATD+KRUn7XnsVfbwJNf6IqLzlws1aNVdO88lPIREtiydY08r+H8cw7XVF+pVO02ODw7BllL1VKOO4zj1qzqY9H1kuhURmCAD1gV8dME0dMTpdSUmHQiNb4pug7BXi9navSRIQvRH5Ds4fBRu6z/cvQLOveoDkmApw0/GeJqy4xNXH/3GgSfPliVv3qgX5C83rSSERcSqrGGhMB7Vx472qvPv1BcXwIMAIuEkzMvcwIMAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 179,
                "name" => "Saint Helena",
                "isoAlpha2" => "SH",
                "isoAlpha3" => "SHN",
                "isoNumeric" => 654,
                "currency" => [
                    "code" => "SHP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzUxRjY5MzExNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzUxRjY5MzAxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQjhGRTA5QUQ4QjRBNEYyMzI0MkY0NERCMDRFREYzQ0YiIHN0UmVmOmRvY3VtZW50SUQ9IkI4RkUwOUFEOEI0QTRGMjMyNDJGNDREQjA0RURGM0NGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bql65wAAAzxJREFUeNrsk1tIFGEUx8+3s7dZXWlHV921NFJLlCJMKrx0U2rdxC5GEfWWGERSUPSQRagVvnQheolACtSkMjQfTExDhcpiJUTLLrZe1zX35u6669x2mhldC32RpJfoMAzffPOd3/mfy4d+XC7VGrOeDitLH7zvbXoJoKkr232wsaIp+6Sx4htg1Bqd9GxKoPhQgle3Tl/U4rHYEShhCYbd7tA5W98UJmGnjImxWalfphWb9fjGvs7upEyTDc4lue8fX5uxI/mmI/pY9Yj9wyeRi5aCRkcLH8qjNUZ9IJ8dVRYYQBdvbnzlPrBXVfVYExsVMTkIBkPLd6rqboNjyNb8wsywFIBU9OU44S0RIwVgUUDEZe8Ekhl30WM+4ByOiDAl6aOcJKtGAYVaSUbHUBZrBFCxa8MpCR7ZGTfFKBAwAhhYgBU8QS7zUrRapLtF+lwIqSk8aXzcRqq5jQVblS57qM3i5eT2R43hh/fqUhP7W99DlLaHWDkwOYIUOIMwmOMiTIJfutKXscXjcam0uon29piysniaoea1I9BfyDi+reREem6MH168rrQo1iVGrzmWZ75R6dlj3EP4oKP1eZv5+iesa1IKr7uRoA7jQBoSins9N3hEU50+t8DCLwhNsdPFE8k51bXlOUfiJfD2SU11151+rMtKNJen6DYQXwfMBv3pzLz480dT9+2Ky08YvVf/9YyKJn1yURepQHR779UQQuXCLZ19GKvUYNIRAH+wE4AdTN7eVFFzsc1762PImN0LbECKocF+RxupNQ1Rw5+7a5/1tKMEf5iW+TzQ8n2aCcyiJSwdWGU1ObnsmdVnx+mcgdHMjkfPSWoagSJYENgPEAXA9x0hkAGwHMzwyQE4EcgBZGK7bOIkRIoO7GytASJKsq4R6fAxvD50onVqiKypV1PMFBJOiqoBtokOWHCL9xEC8HmJOxwSQoYAqMQ1F2ySsPAECHna+oYwjZW1mxps026ZqC94ZRBsWjiPwWfx5m+fPMJndRPDjrTI9CjbW9z+zg1AI0Fr8AyCIvhTE1OgccD8Qg3x2Szn/0pgGSbmIfMLhVrIhflBWR4d+5XDbyaBv2b/0f8E+qcAAwB+l0HjpzOhQgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 180,
                "name" => "Saint Kitts and Nevis",
                "isoAlpha2" => "KN",
                "isoAlpha3" => "KNA",
                "isoNumeric" => 659,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzUxRjY5MzUxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzUxRjY5MzQxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQ0ExRTQ3OThCOEZFNzE1NzlBNEFEMTBGOTU1MEQ0QkEiIHN0UmVmOmRvY3VtZW50SUQ9IkNBMUU0Nzk4QjhGRTcxNTc5QTRBRDEwRjk1NTBENEJBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+xg9gqQAAA/lJREFUeNqcVXtsU1UYP+fe03vX23v73EOnEgWWbUpwEFcSJQooooCEaNDAZkKs8mjCgAjGkMEY+AcdoJskYxAjc8FNIYEoWaTRTEMgmcNkKS5sMnoHbZmk7drdzrbr497jadctc2ug+CX35Nyc3/nle/y+78DV3370D4ggQIFHMAgBoCBOJKm/glxlhbxv0d99re7PjiUhk2RRBoQc4VtBGFYBFQA4V2ICpWRplIN6qmb96GKXeHi91Nk1E4byaS1F0YzC5kBNOAEN8WgUeWOqjStDWwo8vY2evV9EvPEsaDR156FGQTkuQ2eQe+5Z+eQyT+wHp9Ua6JeyYzNfLhmgIRRDyE+zR6q9p+f22t+7/rG1SIyXFhUazWbzLGZ04JTwcGpE4cC46laAffvleNeGgafP976z6N7pTpA/B4mDXTf7Bw0GwzQ4vXA1c8PH1j+jRQ/wlOggoUDnfXX5gnjba25T9926pcHLDhoAhQC2bt1S/EQx2SwxL7Hb7WTDFOU1tLE7F/LSh+pLF1Uoa60gkQAEosTILPrc4l2bFM9u9x5qldOAFK9Op7PZbGTl1Oz3310FwPR6zcjZw7qCdvXVNZpAktKC2GxqBVGKL8IGwszGV6V9C9zuDs+6vdEBH5yUUGqV0lZ3wOYbHh9jnWeucJsNT91dxl/ohQJICGnQf6hpCGIKdnrV5fOT595yPn7DXb8idO7aBN1saSJxaKDqEP21pZA5qv25kY0BxQRkDDBOh44ms4AhhLeDeTAveXTzSDU3dGbXcG0zVFKM1ESXTK8CCa7sTfqrRv6lPr6vXHM7BLUgoQFYyZxO6pqGyug44w8xG1b4G8yu4YuudXuE66IJgBGTyRSJRKLR6DRnMZ1P1X6pry3TSjvZzssqBii6dAFm9AdKytQdLz9vbvxHqzjP5flkpfTNL8TB0PHjDURVDoejqalp8lpKqcu3MydrhNLz2u5NjA9gPkU6ka6ZfYe8CdWR913biu/8ZAu8sZ8KJVKBm80vWCwWIoCWlpYpZ4VSVcMxbhurvfeu+sKfgHgqAAVnSLP0M/rtg0Hlj5urXsG/O8lvZhYYjcaenp6Kiucj0fHHCsrv+/qrDnK2VYaiU/yvrSgKFCNIklrhBw4JuGOT8UR7IJ10gpSnDiorzSXzy9o72uYs1zTXc2v6+b7drBgh5SKeKrmMHajTGSUpkH3E6OndB1V1JfqkTei+QjNAJhVTcptl6QlB4dkdTrS1uBqcqBJetBd274J+APh0QLnzpqgxnkGNuSfp/Z9q9rCC38pdGiIaiGtBBgQf5SlCPM+PjY2p1WpyOxzGS9eyzTv0JR2aa62MBBSNgGWcJ4P/Y/8KMAA88JPiKZPG5QAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 181,
                "name" => "Saint Lucia",
                "isoAlpha2" => "LC",
                "isoAlpha3" => "LCA",
                "isoNumeric" => 662,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NTFGNjkzODE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NTFGNjkzOTE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc1MUY2OTM2MTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc1MUY2OTM3MTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+RRKY5AAAAg9JREFUeNpi5A5cyEAbwMRAM0Cs0UwsTN++/fr29SeQQaQWFmIUMTIw/Pj0Q1FaAMh+9OQDBw/b//9UcvV/JsbfD95OzHGcV+r+5+Hb/yC7qGT092+/xMx12d9d5nh3Ud7e6PuXH9QxmpGR8d/zT+lh1stndk5oLcsIsfj38gtQkApG/waGq4ioFvfrAwf279hzXJHpMbOc7K+//yg1Gui2n68/+/pbMzw9cv/1zw+/GP48OhAZaPnr5SeC7mYiGIEMX/6FmQiuWbKclZGBh5Vh0bwlvjocDH9Y/hEym4DR37/+VHUw+vnw4todu06s5jq/jXfXkeMvLp7QcTH+/vkH+UYzASPwxedQe60tK9bFOTIYBQupuPDn+DHsXLs+xErt/6svTEyMZBr9698/Jmmpb/efmkuvWbiE/f+t3/+v/548l8NZY/O3e0/YFeR//vlLjtGgCHzzUc/QMdLkUkH4MwYmgb9f///98Z/ht0BG4Ksw4+Omxk6/Xn7EE+A4M/pfYJr7IV5ifdHMtpThFdCeVyzi4Nz9h4FLk8FYtLz0t9SRzTJ//+IMFpxG//nLyCbF9+3Vyh3b2f5+c2L4j5yQmZg573/+tJJTJuL3r2/sTNgLFEY85TUT858vn5kZfggxsGBo/sPEwPGOl/f3379swCRKcsn37y8LFxcDA9d7nIH2lxWXuYOjKiADAAQYACDo1+pXfbuqAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 182,
                "name" => "Saint Pierre and Miquelon",
                "isoAlpha2" => "PM",
                "isoAlpha3" => "SPM",
                "isoNumeric" => 666,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QkE1QjFENUMxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QkE1QjFENUIxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRTZGREUxMUI1NDA0MDcyNDExMDA2MDlERjhFMDQ1RTMiIHN0UmVmOmRvY3VtZW50SUQ9IkU2RkRFMTFCNTQwNDA3MjQxMTAwNjA5REY4RTA0NUUzIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+vSLwPQAABrFJREFUeNokUwlQlEcWfv0fczuHM9wMOMMhoKAcZjk1RghJWIOJ6GLMGjVG19LdlEllixC1ykSMJBWzu26tRrNaaq3ZmNVdssYNQUk8gCCCAoNyyTEwAnMxHPPPzP/P//f+E7u7Xr1+/d7Xr/u9D61uqqY7u/+xr0X/0SFm7w5lYu7FIvmRPyQTZ5NZi5bUcDQlOD0K26SalvOJxmkKcT5WwviQSsHQNCYpiW1K7pqULdD5jBHzBEEKAjE7zy1epKZeq/r6rdyhzDx8h3PHc5Jq/f2Ps6Sv1zYP/fCbZvUzANMwJzVm9O2q7J9285curwBXdGSKZffWpgXK4OlzmT2t+UVrr29/u0UtZ2v+VNhxKxfCZmGGRV6W2qEuKnqU8EpVZGNm4TYZ3DhX/dHt0f1Ox0vmeOB4IGUgqIoLHAe2Wb6/ueDS12tAq/Ar1A33VjJur9UpB41UHSYLcMmNHeTohAGMclD5gSaQToI8Pr8GSUGAoBwHMaYQQc0DqKDkzYvXL7aBSQ8yDk2o8aQJiJ7MsiGKJNsexoEjEab7aL0/PIuafKDnHSkAfWrTCGXUuCdkwPjjkrWEZu3WrvKVcT9uPGtrkLHEip/fqT69HrJWSu+0QXwMIIBZBZYIhRv+137rxN7KzvtdZug3gWKq8NX2xroLuZETvEOv+1XT+4ev1J+v0xLz4JUCEsMATr+QBrsgtRAcR45iJ390GQUfQGU5vGjcCSnHIe0wSE7WHivGGHoeR4D0C5Cd+N2BslGLQbR8+0M66D+7fCkbB0HcfvZlAcAZQGcAjplW1cLyS2/tbajqkqicRz7HGI/qTPVVmzNvb4OcXWA8BuZaiPq492EIKGfN7wEu3rlpEvWnK2F59cYdrz/VBy16UP9VmX7w+MnV+w7mLS05APfu3hcR7/U+6hsbE5WWhw9FiQOzCatqwLAfwj+Pzd8nRjIOEuAvpVveCAHNSbEAT/p1EFPz7+/Tn0LX1pYC1L26e72ot/yki8n7lMhYllb336tLTAmIYa5du5abmlrfUO902bMzl8K8Fzg6Id7V3mL+8pscAHbbK12hT2SRWINHA+HAU2lGO/wyfuoLFzv1Sl0+Cjua9+wGWiJQgtgVBEJYIEiSpmnRSUwPAQYeAUeBebq1Jy6/pApTU8gUyE61hWAQFoWHUQDl18j9IUuAGBwMh6Th9OSpgNc/PBoMCCqqvb29rKysu7tbJpOVlJSI2+KSUsx5e/ovgE4Cj/XLytt3v/vNz/e06nC1Vk2GgHQBUWhUDBCYFaiQRSowPtmqxdYLNWdVFOw5XvzVDQ0xNDwsHg0OD4+NhzLqHRgQ5YiNCfqsHx5sUOhnepsXmcyOE39uLUgZnp5BHrvmyj+XAQuZOePR4cy4W//GnrL39hcsNzvzs6zGDGCkuq/qs9SaGfD52Z6RUZbj7DMei9UqlrDXZvPhwLNbripTNzQ2xqkSa55bu1cszpMhfcmmTRB+CuBwXPbWaZduz8GXd/5xgyHmHYDz636782k9Syu3A/wtfs2nlOPw/if/uqHLTJ/DHudjj6do+cytttkYHXav9A4VTjoH5wY+sPYvhDlZlMn14vOjDZdDdLC2r/l1ZVhkgrX1RrrT7TXm3f3kUJ1oP/P33PorKyDCjgUluXnEEROwtIVZJhcPJAesnXxzdNyYpq3/3EDMuNl8+VT2lBc2V3ZCkICANDd9qKjgwQyFPQpiYEIj+PjsZwa3rGs7deS7WOMca5e8tmuHe04LGrtGp6BoZdi0oV/xpp4YoVxJgWQ36VQicswvm1IAwYDJf/KTirEnC69eOA8C97hTuyR98j+l3wan69ta1SlLOV2sx+UiBZHcDJaE+55fd3fgi2JgZWIbkdvTzCNWe4ZFim+Rs9cJeRvyfkfr2GAdnTYiiUDIC3rvwI9pNrfs5Zd6Fkb5aY4UW5VawMUavTKtX2CkSjWv1LIsSwTc8vJyy21L5HBzvHaRQL5tiA0TxjpWG7ybuFRNcLSCiMxgwib4s2NLbCo9YD8iWVAzHTeTPHNkU7u54v2K7p6I1UvtFKb3HHph+4dlKCBJNsy/W1tUUbW+oytueFzrcpPaSAI9iMn0Ge/731P5biNdIhdjB9ssirrJb36wsSl6CWAPIAJCDKLArgRBCiQPPKAIN9BBPB7xC4M4oFkACYiOflJ8JXCeuCQDxeeYYGY++ZTS3UPNG7FgC8olJBHFR+VH0LQmGhMYkQgEhHlhcQgFhW7iZ2YNPJZoknwU4CBPcUGJhBL5LKYgToVnjo42af4vwABodCN1ZcPdpwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 183,
                "name" => "Saint Vincent and the Grenadines",
                "isoAlpha2" => "VC",
                "isoAlpha3" => "VCT",
                "isoNumeric" => 670,
                "currency" => [
                    "code" => "XCD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QkE1QjFENjAxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QkE1QjFENUYxNzg2MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMzVDMjYxQ0FGM0Y5RUZGMzgyRDc3QzgwMjZFRDA0OUMiIHN0UmVmOmRvY3VtZW50SUQ9IjM1QzI2MUNBRjNGOUVGRjM4MkQ3N0M4MDI2RUQwNDlDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/PJycgAAAulJREFUeNqcVU1IlEEYnplvvp9ddRcx/zK33MyfkDJajaA8FNFB7BZBFF0iIYgu1UGNrCCoQ0cJOgR1EsJLUhelOiVaoVmYmGu7LWYqW667334/O9/bfLumtuou+l5mmHfeZ573mfedwUfOPkP/mz/kudnadelMtxrNwQhQRnM49bsDe2+/r/K6Y8uLCcScINO1uwFAoIwISJIslBkaAxH5TgbWOk66foQl8sGy8PJK2umrlwHW+JNGNuCTCrYAiCIySvgEkot8BJmAIlpWFqk2hF46wEGtOBMxBuEfL0rAABxNKE6a5SIyQUsi45m2vGgcnCmQcjTOGwCLTq3vR0FLbz2SmETZpqFTFImUeDLm6R/2PhzZxTPgQPZhSOgarXgzWt7ztZTI5no3kRE6lWggvK1zyOvePvvcX9jzpUJQTEHWn36qeBksyi2Ktg1Wzy26Ns1awDZ4+4A3OJ9X5tIIJh0fvVwOpst3hnZLYsLjjo9P598bqEpyhs2wBjvHE2XzSNZjCcliuKk4gjAWZONocdgwScSkolM/tmN206xTHXBhv/987Vwg5Pa41UfHR15NeF4HSh6fHC7K1UMz7st1Uy21waR6eAvFhzobxpGoPTg8jgTjXF/Nxf59VDQ7Dkwh0Whv+Ja5+GgGnxFzePMXe08PNldOd32oCUdcYQLdn3ddaZjc6VIL8+Kmqog52kaPQSZoXmiGRporQyM/C66/21OSrwOzWt/W+UoWTlUHtKgTMIhbaxlsi27ruBB3qAkqEVMQkMqE33HZdmLAGLbc6LbxrJu8oRv134N/8qYj9NbBSV/5LyMmZw3MDp3g7x8T2hrHZIdamqdf800gRhhkD6RZd2CM9bjkzo3fPzSZIzJZ1rnKmaVYhiZpAvOfQKS2xIoCK1XOHFd9fnvOZEWxlkoZ2y5Kk2E4/TGhAGZatwhYU6NsMYI0VVj5HIA/T7zMBIORVZSp4kBqDBHMPxpYrkKLTwD9FWAAxwI1PaSIg8UAAAAASUVORK5CYII=",
            ],
            [
                "id" => 184,
                "name" => "Samoa",
                "isoAlpha2" => "WS",
                "isoAlpha3" => "WSM",
                "isoNumeric" => 882,
                "currency" => [
                    "code" => "WST",
                    "name" => "Tala",
                    "symbol" => "WS$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCQTVCMUQ2MzE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCQTVCMUQ2NDE3ODYxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJBNUIxRDYxMTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJBNUIxRDYyMTc4NjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+mJryLAAAAXxJREFUeNrskEtLAlEUx8+9M97rjOMMGPaGHvQgWkW0MKI27iJo0ap2QVB9ABH8Rm2jhVAQ1CYisIU7kTLsoYk6ckWdmTs5pCVBMCnt/HEWh/v4nT8HBZeiVSzCFw1T1uTwytRV4qmYKwHxgIDABhCIbFRPHy9HjGpZIOACnBe9DItOEcqYybK67ZPW1xawKjPdYCYwkXw+aCZojkDgFuzjZqstVEKrs4eRzfdM4Sh6krlJx2Jbe9vL8FyCruhYhcFVhY4P+p1kVAwEg7fJ7EtOB4X2rB5W4+fJ+FkCVAne9LnQzMXdg/VahiENTKs3tclBpiDgQEDZCC9e32esSg0GlO68zq6B+oGqrfKqYNLy2OhBZKcxOQF18n1FNUb8HJBg2y7V6Hh6t4Fwx4kNCGk+WmZ14E7fziAQy9gvplRu1JDgSl2V5jF0BMHIMrler/sJET2CY2+P5AiniWIg7DK4mGou5AceAAnyv3xwvxAM/0Zf3Vf31X/iQ4ABAIPdhjTlJ084AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 185,
                "name" => "San Marino",
                "isoAlpha2" => "SM",
                "isoAlpha3" => "SMR",
                "isoNumeric" => 674,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxMzM1RkUxMDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxMzM1RkUxMTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjEzMzVGRTBFMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjEzMzVGRTBGMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+pmAcAgAAAlxJREFUeNrUVU1PE1EUPW86hGk7tbVAoaDpByGG1EBMVEyMceHGnQtNXLjTH+BKf4BxZ1g10Z0LY1ITjW6MCwPBqCHBFghpLTRAS0EoQqX0ezrTzniBLm0thtpwkzsvk7n3nbnn3Xse08jQAuPIsy3AzXJokR1HYLXq/xk4PPsWQf+rfwbmD5sgFVJYXFrAejQAoa2CcNgFx+kBGE2dzQXmdWYUtz7CZppEdK2A5K8SBvtHm0v158UZTO9u46Syg+zyF4jZaZwxSgiVJDwY8yJdzBx9xTllA775T7iq+DEfD+K9NAqLWYcriZfgx+8iEBeQv3AbZv2JxoCfTG6Xaa2Q11AwHfGi4uvKBJuYGmceV5zzi48xlr6M9mQFCasD9/AMdsWheQNPtQ79TVVjA7RbTV1i5GX+oS+mq0s5M9DUJMkjgKwiJnbRGUdxKiOjT1TgNpQRyRvh+1ZmWF2n+JAOmkCJqXoF63jYBFb9ixrA9KnSC07OgVttw+aSAfazL/D6h4Tv0SLO9QKzK3ZA3w9YrzHwF6sqLNQDZlxDbUBUa9wynLZ2BEPDkGIenHf/xJAzj0yiG1O7fehyfoDAZihebKhnG2guOn6tBE25Dt7ejTs3hjCSXoPbb4Bn0AVTxyXIOxa8i7yBzHooVqZ6tCMA1hTaiNrAdAsLqSK8cyoyhgx8GyMYZkb0SMDzzU5i5D7F5ajYLcr5+7YMj+b2hs/U8NTL9ChQitlMMkaoCjEiEr1sjxnWqH5n+UPfCzwBWKwHLwJ1vKAdsLIPqjVPMvcHQJX/MJrH5D7+LcAAowvZ8JHN4kgAAAAASUVORK5CYII=",
            ],
            [
                "id" => 186,
                "name" => "Sao Tome and Principe",
                "isoAlpha2" => "ST",
                "isoAlpha3" => "STP",
                "isoNumeric" => 678,
                "currency" => [
                    "code" => "STD",
                    "name" => "Dobra",
                    "symbol" => "Db",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxMzM1RkUxNDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoxMzM1RkUxNTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjEzMzVGRTEyMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjEzMzVGRTEzMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+A/jZ9wAAAoxJREFUeNq8lk1rE1EYhc98mGRSo0UkJSmiUJtFFwri2rUIgtiiuFGwiMtiili02A/caG2wC8WVUPeCf8B/oCBFVKzWhtgmTalpk2Yyk3ZmPJeZ0GASm4WTC2/mfk2e99x77k2k5I3TK6mRakTLSegqS7BldKSoj+eDcThAarQKrKBjcDkdskszr4NIzgZQ6XVQDrPT7oDiEkHLko2nhIuSSgrlMrp0+KpcVaiuqAFLsCGUKxIwc6eKSpqJbJOs+AQWHwJeInyZ8Ml5DUGuwMLDHLRMgAPwBS4tdJ8t8hkRDYsCA7qMo4aK6Ogi8GQd+In24LuMbvGNjN+MA/uYq74hlFthC7mQhQ+zA9hIxoBjHKAPUBWZ/RUCtuPBeoDsN9rjM+sxr2/Hm2M1RqN9bAlyeBdqyMD3uQTy9+IuXPNecuoCrrLCGlCmJcZfAiMpwGB9I1en2mmMpr51CFcID2kVLD1L4NfwCTgHORBsMpn9WRO4chV49RZ48w64eAlIb9U2sI2lbjCA7DA5GVZJcZeuWdkEBs4Bt8/vdV1j+8wFVgr7uLoZ0DIUGHoYx4cXEU9lgFUOmC1S5ZkvMO5eB0JclQq3xNlunWuDq901oEpCTQG9+QPRF4RmXGVNnS25e5bN01OnXCn5j0D0iDffaUOxuKkUQqO6ip5bX4HnOVdpmRFqkbpnllg/n2JfeTKifd47tcT+BRbQsEED8xzPDZbxacyG9r6Xayi12JD/cHPVoH2EPhoyMHGfh/YLj9Gmj1emgGo0TT+hk4NVTD0wEVyTcEjfgaX5+COhUWmC0OnLe9BISYKlwNcin9TliIBOjJsIrBNa7MwfAXV6qLo6NWZGBPTwlv9Ka+WPAAMAQOHt/oOWjtwAAAAASUVORK5CYII=",
            ],
            [
                "id" => 187,
                "name" => "Saudi Arabia",
                "isoAlpha2" => "SA",
                "isoAlpha3" => "SAU",
                "isoNumeric" => 682,
                "currency" => [
                    "code" => "SAR",
                    "name" => "Rial",
                    "symbol" => "﷼",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxMzM1RkUxODE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozMEE2MkM3MDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjEzMzVGRTE2MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjEzMzVGRTE3MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+VDw2aQAAAwhJREFUeNrEVttOE1EUXdNO72ALvQqIQYSIxIA+GCMvPJEYLzH+gN/hZ/jAL2jiB/jkgw+YeEkkgsRE4w0UrLTFdnqbzrTj2jNtANMpwgOc5uScM3PmrL3X2nufKlic/QlgiF3D8bR+9k21PcGe8VjAPTihprq/UgCrxd505h7aaFm771qGM/WoznOl8017v8L9Hu+eb/a3Hh5btMpCgABqUwcaZfg9Ps4b8BpVhAOnEPH64eNaFfCWCbXVhMfUoXBUXAAP9pgHRoJR3Bu7jbA/grWdLyjUS0jzWc2oYDw6iunkFFa2P6CglxBUQ9DNOm6dncPH4g+8+LWC19+eA+HEIT02ahiLnsGNsXlcy8wgrgbxieDJSBLzo3McU1gmqBCr09tUMIYdsjLcfxpR7m1aZk+PvbiZecAx0O1lhbTlawUk6eVkfBJb5SyGQgMo8blFzWPU8XtxHWkaoelFvM2uYSSSQCw4gDfZVfyu5YnQ9eiGu8fUrUFP8vUixPY4NVWocZ36DYfjWBi+iqnUNLZqO8hW8gj7wrgzsYD10iY+k5nxwXOUyzyCxtQrfWoE9y/eRZJePnz3CDOJCVyKn8er7HtkqN3S1jIKZg0+o4y52BV4yMCzjZcwabBXIlpReuTM4mypa/FgWvhJk3gnducqOVtfP5nIlTaQolGbpD5AT0MEqdE7g9RqzIQ0dS5UttFQvG64mjuw5CEtR0Nz8tnfb7Ng5y9ph87nDCJ7n53vlpO3AiZ7mGr2HF3TSnPXWA6TQxko8Pc5oLKWYDEbzihgYpwcLmCyR+Zkwd4L6yiVq6O1jkFG6mhfBnXOmzTIah+oyI86tmhAjMaVzSq+Mrh0Fhi7oh2tZLZbi4WElF5nLkuFMuiZAEmXIJL1H+bvODW/nLiAp+tLeLL6GAgNol1HDxlce7UWD4RG0a1TqxXsats0oNC4EAPRSwY00V/pef9oB3ssh0sQSf+3/nYuBoJZjOpqNedQrAZcL4f/p3ofkAt1nYj2hHfXB7QTvY+1tsbH+ddH+yvAAIKJO5Z5g0J2AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 188,
                "name" => "Senegal",
                "isoAlpha2" => "SN",
                "isoAlpha3" => "SEN",
                "isoNumeric" => 686,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozMEE2MkM3MzE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozMEE2MkM3NDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjMwQTYyQzcxMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjMwQTYyQzcyMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+MP7aXAAAAYNJREFUeNrkVjFLw1AQ/u7loRUMtS6Ck7v+AcGiIrjo4i/wH7j6C1xcBAf/gJM4irvgoiLqIAgOHUpbqCgKWm1pX85L8opFYptAgogH93K8fLkvd7m7PMLWfBXApOgroqQJGnVhyusOF/LQ3gc4CqbyisyDMfWlCjqltqPGnUiciCta09ZAzzVKvK5/9Be2OgjnDgJ8d5oGJlYEmckfI5aEkhNq/OSmQKwIaAlhy9rZEtvIVE6WMeDgnrF/x4Ed7CFZ9LGJtQqb6qzMQccf3grxdWify15H7jlOBsTkp3SEcFEXe9fguMQ4rTJox+BERpB2Mkp12/gLY6NI2CvKYzWJtsLYnlXYXAzdGBOfWMdG0tf8unxiLMwpDEuUVy92WDGFmNSJLXnjEVibIizPEIY04ejGw7OkvzCRLNXJiCXinES5Mk0gv5eajFV5Ae8teT/rpP3nFxm/9/A0bOFlOkDot0fmfyGmND+Gtkce98ejT+jM2LrVfeq3i/Ol338r4PoUYABXBXI3FKW/6wAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 189,
                "name" => "Serbia and Montenegro",
                "isoAlpha2" => "CS",
                "isoAlpha3" => "SCG",
                "isoNumeric" => 891,
                "currency" => [
                    "code" => "RSD",
                    "name" => "Dinar",
                    "symbol" => "Дин",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzBBNjJDNzgxNzg3MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzBBNjJDNzcxNzg3MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMTZGRkE1MTE3NTQ4QTZGRTFCQTUwQTQzRDk3NDNGMkUiIHN0UmVmOmRvY3VtZW50SUQ9IjE2RkZBNTExNzU0OEE2RkUxQkE1MEE0M0Q5NzQzRjJFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+6RpbdwAAAFFJREFUeNpilJffzEAbwPL3719aGc3IyEgjo5kYaAZoaDTL48dfaWV0TKwkjYxm/P///2g0jhqNJ/G9aJxEq8R3jWYOZ+FgUB+NxiFvNECAAQD7cg2p1+zRzwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 190,
                "name" => "Seychelles",
                "isoAlpha2" => "SC",
                "isoAlpha3" => "SYC",
                "isoNumeric" => 690,
                "currency" => [
                    "code" => "SCR",
                    "name" => "Rupee",
                    "symbol" => "₨",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NzkxOEIzNDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NzkxOEIzNTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjMwQTYyQzc5MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjMwQTYyQzdBMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+1AigIAAABGRJREFUeNqMlUlsW0Ucxr+ZeXa8hjgRJSCo1BQlQFUkoGqkSlGqJBWkjYTUqooEh1QVSBygNBHbtb30wqlCHOglEkUtqBfOSJQzByBpk7jO5nirlySO7Sz283sz/J+XbHbijDSW5Tf2N99vvvnM0HsnCsCLRoMrYMUOuExz/u4EOl7bEmbGdvB6qcBsHPx1J/SJdSM8NovNqXWh+WzMeqzRfAVHGQYH1mwYH51Dx+ks5KK7vJm6axVEK23qZTsy9+OIfDMHI2ugqcMJVSx/h34NuYaighaHnOgbTBgjw1GlYg4oVkfU+sgkURKAW8jY2Gxx4dq0lLpC04kd0arjxoiX7eDtBfnoi3mGLc5kQdS63Ubrgv5fzggR2uzjtEaCTLgElLF3vXYkxGm7undzRvq61jUZ8JQJ7EfbRmjb7SrzS9wMfz3HjOWicL7pLoMwa+loDREH3Ri4GDevD0e4ChOu3U5V2WkJbVHJ6JcBmfwxyrUWjTu6XDUujyZcQYxjefnQQlzgXOZ3IbbQ2gntSUI7uW6ERgntn6viILRHF7YQZ2xq/NaMbOskxLO7EFfRvkRofya03xHa1OFoa3wdluL+wYQ5shtxNbUnSqk1Y2MBY/HaNFe6Ehba0vNGmpwTDaOO40pRsPaC+dvnhHhT8FKKIXdSS4UQuhlA9q+05iC0vBFaS6xQgJFKwcxmIZqbZa2wQcWyalc/3ZpWrZUUKyWhVVP7IGFGKLV6Uj8crSWWz5fFcjkIn0+6zpyRnt5eeAcG9jmupPjCpbj5yXBEKMItLVErtbo0K6kVVmqd9VLLGJSubzvT2tqkq7tbevv64KHp7u5mleNlWm2KC/LXalEYGrRTDuhPNozQjWcltDWptZxZYolE2VlLi+VMeXp6VPPQEOh9Sagy66S63MVq/DYVxYmchlUfRKdNUdcakW/neNEqhLcIrSJX1izqe86s6szb3w/X2bOWiNgvVitcQuyyutgcuRIR0Frp/4pZXWsm7oatfxTueMMDubEFI1n/zNznzm1jPCTT1dwrbbuLj+XNR6MLHB4vK/yTN8JfzSL3eE2zvaoxtbGMrcnMQWcmDhMIp58jmI6r6WQQfpoL6Rj8yyFybHCGlA33fgjC12WyzP1NKvgpnk9FOD2E2iBn77ynms+fVy9+eJnh7VP7EUrrJZlbkf7kEuZXImx2NaqexOfVVGKRLa3FudxYo8zkGEy6MhrdDruLhBedeP9GFtcvhxD7aFJGHwRFC2vFyatXTdfAIPDBReB423bZxIs5Ob3kJ4GoekY7f5pYRHA1xpYySZZfSzDk13cEmigTTVQsrhfo6Fr37Jc1X7qjMp/9ATzMwHzaCfFxD4q972LldBdiTgdmkgFMTP2N+egC/KklBFIhGJkUXa9NajGDUkI3wU7XrYmmzUHbE6Vr1Wiw729/GhhCuv33bIcev3CcRbx2zMTmecD/L9NTz4HNDCW+yCE0S4DBQZ1tJwFG4TiCwEHjfwEGABOHZsLQl4B/AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 191,
                "name" => "Sierra Leone",
                "isoAlpha2" => "SL",
                "isoAlpha3" => "SLE",
                "isoNumeric" => 694,
                "currency" => [
                    "code" => "SLL",
                    "name" => "Leone",
                    "symbol" => "Le",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NzkxOEIzODE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NzkxOEIzOTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc3OTE4QjM2MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc3OTE4QjM3MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+GrndcAAAAE9JREFUeNpilNtq9Z9hAAATwwCBUYtHLaYZYPz///9HIM0HxPTKVoxA/IkFymBAouli+WjiohtgQUpU9Exc/xkZio6NVhKjFo9aTBUAEGAANLMNXBNFuocAAAAASUVORK5CYII=",
            ],
            [
                "id" => 192,
                "name" => "Singapore",
                "isoAlpha2" => "SG",
                "isoAlpha3" => "SGP",
                "isoNumeric" => 702,
                "currency" => [
                    "code" => "SGD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NzkxOEIzQzE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3NzkxOEIzRDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc3OTE4QjNBMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc3OTE4QjNCMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+k5fIFwAAAflJREFUeNrslk1rE1EUhp97Z5Jp0dTEL7S0GEFcKK4CLgouunARQcEK4kKqtTsVi8WFFUS6EDdiUXDvX9CFgkJXirjQoggiKoqSGIuYtpOk6SRzxzMTK/0BlwrSA5d5mYF5zrnnPXdG1fYMloBeHO3TDjEfv0CXh96Vh6AFLVmOA0phKTKyym4itIb6YsZ8LZEeG8U9WiS4fodw5m0CVZl1Hbgx1uA6uYRSaWUWb+oa3s2rmDfvaD16gNq2la57t1D5fsz7T7C01EnAQiRgU6qQOnyQ9PkRwulnNMcnSZ86g9qUJXw4DY1FUieHUH3bE20NTL2BPrA/ke37j1Ebs6TODic9DqbuEs3+JHX6OFrAkV+3AnaXhVo2T896onKZ5vCYtMCg9+6TLQ5ojl6ClIvanLNYcXc34fOXnUwGBzBUcAYKeLcnJYkfeDcu4544gvn23Zq7nStbdk6Ia73wxQxazOQOFVGu9PbJU6jVBVYmdn0kjo9+VVHptA1uoPzewoK8OENdIFWp7uIF3GOHaJ6bIHz9Cr1jN1Gl0gHmZJvbbRtgX9UKxTkRG2RMDEGA+fBZ3JxD5/uIag2bB8fK9s4rM79QFZGNp/nvIzEVMS+e2SiyDY4PgjlX9WScFTdWKxzNP4o18Br4/wPHXyf/z++Iv0rMhPVbgAEAAN6l96444okAAAAASUVORK5CYII=",
            ],
            [
                "id" => 193,
                "name" => "Slovakia",
                "isoAlpha2" => "SK",
                "isoAlpha3" => "SVK",
                "isoNumeric" => 703,
                "currency" => [
                    "code" => "SKK",
                    "name" => "Koruna",
                    "symbol" => "Sk",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMkIxODFBMzE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMkIxODFBNDE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc3OTE4QjNFMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyQjE4MUEyMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+YhjhIwAAApRJREFUeNrclktME0EYgL9l221LW/oAIlWBg4oXUUPkZGLwhIpBIDFBjeFoOPg6GfXqTSPxqDdNDEZJwMDBgB5MTJQLCWoPBhrwwSNAC7TUdvvYcQoESuRiq2icZLL//+/MfjOz/2MUIcQEsF32CFvTnLJPmlYFsp5bAi/gL7V/CyySSURcX5b1wDj6yNiKXdpEIvnnwIHWCwy59zCo2JnuuM/Mg0fL8pC3ipGmtt8CNvW+mVxTYiYr5W6VYv8HEvpXbNv24Wk+gaJphDq7iU0MY3y0M/h+hs+6BZsezR3ceP7FuhYU1DeW0el1yNhyUHatHVvVbkQ6je/mRSauXsdX7ODGnXd0Pw+CV+QOprRwXTMXYHZrGKEg9gOHSIcixEcDoKokp+aw19RiTI1gdpnBJ6OvKJUHWFXWNVUQFQUkYgm0ihI8p+rRXE6UAgVT0zGS/k+kxvxyjCq9Q+42e+4vgze4WpovKTtL+2so6eskNtDFwu0OsFjRLrXLw5d+cLyFb4aUxFxmpfmAs1ZtVQjMqMzu2MuYdRcvi2vp6naiq2Zad56jbvot5fLdcNACtsy83HescKQ/vJYuM98JGTQ1eIkuCgZef5eAWbk7aY+XUHfYRqnPzLMe6VgeZcWeW4sotypPh7PzdCqeoFBT8LW1cGX6IMFXfjAMHEeruVfhZ/7hUyKxNJrNkgdXghcL3RvAqqKwFA1T5HEweuYyZ23NJKQzPU72Uv3kLguz8zjsLtJC5JM/IsqQpyr8U2WS4aOHI1Qm57A0nETIf5zq62FccaO5PZBO5Zu4IqZNzTJhWBx2Jg07Wl9/JkuTcFWgZUbnD90knDZUCoEMX1KestWkLo/W+A/Komn1yuPc4qtP5IcAAwBoBOOwBg9d5gAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 194,
                "name" => "Slovenia",
                "isoAlpha2" => "SI",
                "isoAlpha3" => "SVN",
                "isoNumeric" => 705,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMkIxODFBNzE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMkIxODFBODE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyQjE4MUE1MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyQjE4MUE2MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ioEsVAAAAYNJREFUeNpi/P///1MGBgZeBjoDRqDF/xkGADAB8eeBsJgFn+STp78Z1h/4wHDnzi8GULgoKrMw+NkKMSgrMEPdTAEAhvSn/zjA9IZTQOtu/GcQ2f+fQXQ3kP3gf03+OaDMr/+UArzOtlT/DST/MgjpqDBIG6qCnMlgrgYSY6U8qLcd/ok91TExMZw48JfByYuZYdYsGQYONgaGlIx7DHuP/WBg0fvF8I/CNMnIIPr4E9bs9IeHgeHbaYbnV/QY+BXFGf78Bcbq+9cMihonGV7/t2ZgYPtKYeISZcYV+wycb8QYpjScYwju9GT4D1S2u+E8ww82CQYGEVAMMVPoY71n2H0MtBjka/531xkWlfxg4GJnZIjrYmF4zqXFwMD+DayVRhZDPfWHm4Hh7l2gO4BhraQGtPQ7UOwfNfIxnoT9F+Q0oO9UlaCBAGT/YaQ8D4MtZhMnQTk/9UquhS+jB6LEBFYSbAyfBqJ2Agb1gHiYCqlkKFrMOxAWg+rjZwNhOUCAAQDa9c0WhSA2hQAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 195,
                "name" => "Solomon Islands",
                "isoAlpha2" => "SB",
                "isoAlpha3" => "SLB",
                "isoNumeric" => 90,
                "currency" => [
                    "code" => "SBD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMkIxODFBQjE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMkIxODFBQzE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyQjE4MUE5MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyQjE4MUFBMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+3IS9UAAAA4NJREFUeNqslV9sU3UUxz+99+52bVe37i90bMbIDGFCiBjiZDGaDGEwBYXwYGJ8JD7og0+GxBB8kswnX0x88wVdsOqDIWRMFDXTkUzkj2iQZLCwrltHh2u3/r33cn53g6YPdmvnSZrm3tN7Pr9zvt9z6+HI+SmS+SAFG19zLemJJLQHQPOA4+DGZIq6rnpSiSxYci9kQmElV2V42HvWaW/z8fYrj7N7SwOffTfJN+NxMhlLsh5qDA8Htzdy7PUnuD6Z5JPIbSaiSxAw1gXWBJ3MxtIc7mnlxadDPNvdgBVbWkk72DNpureG6BP40ec3kJ7LqOOug2iDrWEQqGFOih379C9e6A7x9W8z5HVteaS2g+XVOX1xmnze5tKtf4mlC1BvqjNVOFsFlCnFNrnPenhteEEz9aA9L/rdl09HHdzLsFm6NGXMNy7fA5GCOyloMDGaaim4MlQC1CHeLkAPPdtGOf7yl7hC2Tkp5JOkzw9ygF6Bnj65k1pT440T44xcS0DY79ZZM1QBLSk/t8kd7e4dP3F8zxn27zsvHCh1iBqvGCoqxTvE4Sqi7shZdrEuRJ3yY1YaFqRsvMO97Nn+C+/3RXj1wDmQkn9ebOHjC20CVmsj+qG6VgVlhe7L9ZZ3RuXSIZHMr/hMknnHHRem7upfTsNe1WHfGfr3jbgdXvmhlcHv24hc8ZNJK3NlLR6T1dghrn3uqXqGLkS5I7DE7eRyQem8M+zj6J4OWacUY9cTzC+JwWq0Ug1nO0s0HDggI/WqDlv5aKSVod8D5HMaG5vyBEIFAUt3gbTFqTe7XLCqN/j5P+SUc9VkF3Ic6n+Swbe6uHF3kb3vjjKvpmNSVsNrP7ZxaqSFyB8BMlmNsAD9ZgFLSW8rjaXbaSn47aVZGutquDyRIqcc3hl0xbSk+6uS/3tqkcivM9yNL0KT5GLtZTX8YjxANiMdNkuHjUVg8c11aHjB8GpBQ3TJLBYIy+pE1UtC9zwy3MZmL9MzAvRJ5Xkxjfyu95mfy2roAk2nBFb6ypQ9lu+g0vqRW/1G0TzqRErueNjVcNfOMT7oH2Jg//B/a1gG+DCK6+TVi3cV9OEeRsPLGu6qTMPVwqh2D1fTcO3gCvewWmARXOUeVgssgmdlpJZe8R6uN4yerWPB9176iiMDZ6Hu/9Nw1f8Q55ZvipZ08ObVFj48t2HNe7jeeCDAAKYZ9xEESrmkAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 196,
                "name" => "Somalia",
                "isoAlpha2" => "SO",
                "isoAlpha3" => "SOM",
                "isoNumeric" => 706,
                "currency" => [
                    "code" => "SOS",
                    "name" => "Shilling",
                    "symbol" => "S",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDNzVGOTkxRTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDNzVGOTkxRjE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkM3NUY5OTFDMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM3NUY5OTFEMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+aPgnNwAAAXBJREFUeNrslr9Lw0AUx7+XXBLbJB0ULLi4+QsR3F1UEAUR/CecXRwcxal/gbOL4OCgo0v/AcFFcKpooS0SWmmitqbXxLujYsactHHpg0ceL5d87uX9yJH1UqUGYIZrgGzE5VqnAwOJayZwDf8kY3C6hwjQ68dShZ0ZOIoBkrBHChagmEMYJ1VbITaXHGwvu3huhtIXJzYzVDD/qsiZBIQQ1N8Ytjh0b7UAz2cSmjc1uSat0LQLO2EEk+o43S9ivmihkNOh8W1XSgt48kKcXL/KNbalDTdi8cLWB8NZuYngK5LRW5Sgy2Lp8wKGvKWNprgmbYqbex+3D7/Ttfz4jqu7trynkmOqAvY7fWwsOlibs3FwXsMnj/x4dxo7Ky5emj04ChErgQ2dIGdoOLpsyLyKojq8qPNodZi6WkMrgUVOG+0eQp7X2SlD+oJuXxbVhEGUepqqDg4RtdAfiGijvwyS8U8iM6GDI4+b8dEn+BZgAN1qgUfVZqa9AAAAAElFTkSuQmCC",
            ],
            [
                "id" => 197,
                "name" => "South Africa",
                "isoAlpha2" => "ZA",
                "isoAlpha3" => "ZAF",
                "isoNumeric" => 710,
                "currency" => [
                    "code" => "ZAR",
                    "name" => "Rand",
                    "symbol" => "R",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDNzVGOTkyMjE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDNzVGOTkyMzE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkM3NUY5OTIwMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM3NUY5OTIxMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+CE4TZgAAA8tJREFUeNqsln9MlHUcx1/Pj/vJ3WEMPBERMrDagkVba4I5BzihtCyZzsoxurVm65fVwgp1/eGMmrLlP7VYFvzjDJS00T8dUUmba9NqqDAwMDpQzBz3CHdwcPS5O9yOudbu4Lt992x3z/O8P6/P+/P5fB+Fuh0+YDlTQQPdROPWXXgeWg8Xu/F5thDovYz5vjwUiwVmZliE5ZQ9rPCWx491womugTEGN65SWlrFiZ1vkyp33DrwDiMNH6CnOTGtyGE2Ij47u1BxQ63MGpKLCyZ0cEgwWavo6GhhyZtP0HSlB8d7B8nvOIc5w03wt24IhUDVFoyttlf8TEvZLxAWijEJQJGX5twL0yGq656h9FgDRmERWV19uHfXEuzpZ3r4TxRdAlWUpIUVT4nub6yXvC/NpPxUAd4rK8EWlD0p4mHwDaCkZ/LlywfZmXM//H4e365tBC71L8R7IxKy32FRnR37FR6uttF6fjVVnQ+IqE1Sb4Cmws3r8M8oZZurObHtNVxR7/dw9Ug9iiRKsZqSE56rNGqKNT4/JD+lLaP8dGEc/ZR4K1TDA/Kfm6ZX62P0Z7uY+apJAnQk6vt84ciymVS8Qr/meRtt5/J5qrNQ6K1x9H/LHmV95bO07XgjWvlJNNmdwreXZ61G4yGVcIqbdacK6bqWDfYJMIvMtOxRH/b05SxZukLqcHLxhG+vY6+rbPeY+P5CLhsvFEk3iZ/6tBgr9IFxZPBImtWEhfX/u2MsILFpYVwiphIZHHEtFBGMeJu4MP8pvLVIo6VBRNwZPP5NAe2+XLAKnVkGiHQZo0OYXHeRkplNODS1GMIq7XsUKl+x0Hkpj/KjhcxMStU6xBFdAvHL9foIxaWb+K7mXWxYkhog84S3PKhx8rC8PDeDqtYCWvvvBovQpI7FKIeknexOmvd+zHOrS/j17A0+a+qSbjKhaUriwpp49G2twoaXLHh78qj4tIDpoNSb04jd4Rfh0RHWbXiSH6rrIk9QW3eGDw+cnmsmc+LEUS8/kmhXCeVJoewTSms85aCkOZXmfUeilL3dBmVPH8XXN4A5fRnWFDPhmXDis3r2ot3vHcpzbvTGeylV6r8plMOUlG3mTM2+OcqfhPJrecqGIztNHg4ne0Ia+nbvGo5fvgdMoTlKedPQH+Kli+b9QpkfofQL5RdCORg9Hq32GOVCTmX9+KCkNuVWrGInAnDtLx5ZW8GPL+wV58xzXrZFhimOldlRymRSe2dx2caN6OQKyqePpnP4xffZXfwYA73jPLrpE3z9871c8LdHbEoa/wowALUrYLIw4iaVAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 198,
                "name" => "South Georgia and the South Sandwich Islands",
                "isoAlpha2" => "GS",
                "isoAlpha3" => "SGS",
                "isoNumeric" => 239,
                "currency" => [
                    "code" => "GBP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RjVFN0M1MDIxNzg3MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Qzc1Rjk5MjYxNzg3MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQUFCNDk5RkE5MUYzQzIwNzI2QzhGMkI3RTUyMjlFNTQiIHN0UmVmOmRvY3VtZW50SUQ9IkFBQjQ5OUZBOTFGM0MyMDcyNkM4RjJCN0U1MjI5RTU0Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+0RH86wAAA/tJREFUeNrsVH9MG2UYfr/rHW1Z21V+lUKp0HUQkDLWMg3CSmDEmUzH5uZIZG5qdMkWo8G5+OMP42JMhv6xaGJCFpc1m7IYk5FNGIskMEWXYSfbrHN0Qmk7RsrRru21lF7vvp7X61Ci/2H8zyfNXfMm7/M97/O89yH/W91lHVtO3VYddUz4xkYA9F+9a93jODi6t6ftOANcVKNiD1cn3ntxPVNSYzw0Hp0LIpDDMggZwpgEAssA4zRC8BcIY4//QMcnT4XGbrxR9nHPC6WbakMJDAKEITfPoH7NErr5ZvGRbvsHTJ3hIzo6RyOg/mwWAMlzYJt9qrqawWk1AkFYQY32vuzAa7Ut+Yv71HeVu5+OF9fMfHkO9u8ie7/IqTCui7lh2+5z19mvT3yTpMNDl2ZYjkVASr0yAZLi64lK14aC+2duNgYWxbqAIBcgnaEW6vXApYMx3sek+Ui4eK2CXUzSPHoIBKWajBcYuLl5HZk0mHScTK5ztYSxFkmMAqRM5UXNrZXnzwx0lg6Ox9Yb7K8EA9Hxqz7pbERO6G0LdDSC2fqdTXnxkCLkD6cVwoVLmvbWsqbamdGfIqWGa5oyH0MLSoonSMDcCj/Yulqb5/HYLLAoWFVZ1fT75EimnjUEdO9YdjW//ZL1uWouNTDyqUdlqdRt2L95+lifr23PM3pGcWP47IXpD13KW3E1XHUiEKlJiVh8BgCsTU3trY2f/eh8fvQ7b77KkcC2pSXRNJD1Hj1yspm1TI2dOHxyn8N/djD1bNWibeYiXbHpyc7z/WMuTZmxq05+SB9U3fn1Ms1hLmd5DXhzlWbLVhQJOlm5l4ndtdsInX58ypuLedE0LOuqbx463vfqIPe5uyCUSAFgkkz7piLf4vKfaWHB80v/gKs/bpaVlFP+O8OeJR4r0PLIRpNSp6Wja8KajRcLTUq77eCp044Yk0JQA5AUFewEKAEpGWmxOCEzcjHAPAICQC5k4r4vOagXD0bAZ8IHUMhxZ5c25Pu+cOvrjzY73E5r3zGOInoDgc0Yb0QQkQG0SA2ERCS2EJKVrOgVApm0TOKRSsisFJb0Zv0gqZzEw+b5AiGWr2QXirYXElZzLlVv0V5xsoKgEhWI/baVn5Ck/cHvn8WVBZ4nc9PTiFpyD08Ohb3rHqvaYWqg77mvXE/xnChUIGGV4BGokx5dW63rmkZnjM3d6nmfqlDlReQ82wCQAmn8VULMwM8XdTzi2V597/IPO7yTjd0NE7A0y2EqO6F4oRxYLTlCcqq96PZsWP1b3AwoYc/3zCXXTMd1kIn6X1GLl5GoTiMuFYK4kMlcI0Udy5qxaq+zskQw2atqxf8HJhPwn+F/6r/hDwEGAJECp0W5qSuvAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 199,
                "name" => "South Korea",
                "isoAlpha2" => "KR",
                "isoAlpha3" => "KOR",
                "isoNumeric" => 410,
                "currency" => [
                    "code" => "KRW",
                    "name" => "Won",
                    "symbol" => "₩",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1NUZGOTk4QTE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo1NUZGOTk4QjE3ODExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjI3MkNCRTc1MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjI3MkNCRTc2MTc4MTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+kIg3fgAAA81JREFUeNrEVl1IW2cYfvJjMPGvIbhhJZ2tZaWFlqZgKEOHGtGZqtALcVR703mhRGk7dTBEhsz/UmVsYOl2IXOghYpFWmnNxYyKOkMXfy4EZTil/katTZqaH0/efefYZbgYPN5sL7wJ3/ne873f87x/R0JEywBOMnXiv5EopisS5tjxfhEkbrcbYWFhkMlkxzrZ7/fD6/UiPDw8lIlTGmpnfHwcRUVFsFqt8Hg8op3ytlNTUygsLITZbA5tyCOmQyQ/P5+0Wi3l5ORQY2Mjra2t0VGys7NDra2tlJmZSXFxcVRWVhbK1HEo1c3Nzejp6YFer8fExAQUCgVGRkYOXpjj2A8gkR8MQ1paGra2tpCcnCxQnZiYCJPJdDTVPE1dXV1Qq9VYWVmBUqlEW1tbYP/tyG+Yz72JmUQ90yTMffY5nL/+cymGGIwlcOxiQ0NDwnpsbCyI6SDHKpUKeXl5iIyMFOJsMBiQlJQk7G3c/xGTKVex8fQXeBb/hGdpCZsvHmEqPQWr334n2Oh0OgHlwMAAEhISUFBQgJiYGHEx9vl81NfXR4wiYhkqPNt8YaFhZm6VxJJNc55eqs/R7+qPaTEmgRYRTaNsb7PnaeCM8vJy6u7uZhHhxMf437LLdOJGKaK7HoA0F+AnDuF+DvFeJ2ZUsXgrV+LimhXSDCPizc/EJL9TLsZqgenjPzjUs3+77x3CaN/p16dScU+bwgImx3VZPO7bJoEtO6CJPfJMqRjHWq8fP39wGVUaA6I4D+Tkx+2PDLh39hogU/Apjt7TmXjwoY4V8htR9S4KcZRCiuQzGjycuYrn5/TgSIrlKNZl3TsA52XOWUm92sDrTxj6k2dFOQ6JmM/KioqKwPrW9UvAOweWSIPl6DiGjLV2v2/fqYt1ttdvkHojNWBfVVWF3t5e8Z1rYWGBmpqaiJUUsdKilpaWwN5XP1kIahMh/i5B9w3hClPtl4QTpWT63hyw6+joIFZClJubS/X19TQ/Px+U1UGIt7e30dnZCalUisrKSvT392N6enq/o33xKfqe3cEV4yXI1SrITqhwMeMCHj+5jR/KMgSb2dlZzM3Noa6uDg6HA+3t7VhfXw8CfGg5VVdXw2azobi4GA0NDUL3Gh4ePvCiy+3j2UKkUnHgeXp6Oux2O2pra8GQClOqpqYmqJxCDgl+OPB0ZWVlEXuRVldXRQ0JnlrmXAhTSUlJyCER0rHFYiGj0UiDg4O0u7tLYoXNcBodHaXs7Gyh+x1rOv0tDAEiIiKEj4HjyN7eHlwu1+E9+j3Vkv/r0+cvAQYAGzcZMFujiqgAAAAASUVORK5CYII=",
            ],
            [
                "id" => 200,
                "name" => "Spain",
                "isoAlpha2" => "ES",
                "isoAlpha3" => "ESP",
                "isoNumeric" => 724,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNUU3QzUwNTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNUU3QzUwNjE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY1RTdDNTAzMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY1RTdDNTA0MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+hN0M3QAAAeBJREFUeNrElk2L00AYx3+TTCw1adndLlS2KLv4Ug+CeBCk4s1v4cWLn8WLNz+B38GbXrysKOxBVFwRXRXStbiktcFmW5IZn5qCwgYRUpqBITPPPOT/vP5n1K5/LgS2ZMasZjRk9vViwV/flYA7VDR0+rMabH1qLasEWM0eO+N/5Ve7FqXztU0hzdQycGPt7ZjiIzs3S+YGRFENK/vW5hQvkoVZnJUJNcMC6Tz66zCdOTx/EDA4qpElE9rNjN49Qz0Q5CPRccsAF40AwvfimGeoZSkfXkoAOgHtbo1RGBEJbmdb9I6XDSyF/v30NpOPdYb9dzSCLa7dvs+ZXsynw0fo7CsdPSgV6pO9NM/dDPzxBeyry7wRz/eabQ5HG0y/XWXy+Qr+8LzkoVyedSGwmBMLIGdnXBcj9t9anr1+yCX3AJ8uyXHjT/HZZQHPfyQ5NBcNU0+RSLPdaod44QGD8Q/8uzs4616uZ8t43CyQ1mG01hfPXHo34UW3ixvVudF6wm7rC9YRezdFLylBINlTdZJAxBPl5iFXvpIeFg8dIRI3xY7lMFUYI3tVgkD27nSKq07lLJFZB+2kv2WpcXFVHmNjy3G8Tgb6P9TcpXO19nxTySVR3X28ePI0Vvz0iX8JMADE1p16B3U5CwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 201,
                "name" => "Sri Lanka",
                "isoAlpha2" => "LK",
                "isoAlpha3" => "LKA",
                "isoNumeric" => 144,
                "currency" => [
                    "code" => "LKR",
                    "name" => "Rupee",
                    "symbol" => "₨",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGNUU3QzUwOTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGNUU3QzUwQTE3ODcxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY1RTdDNTA3MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY1RTdDNTA4MTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ynHuxQAABGdJREFUeNqklttvVFUUxn/7XOYO03Zm2g6XDhbE1ghEMURRY2IUwpuX8OSbiW8++KwvmhhN/A+M0USJygOJMSYEH1CUBGICUgpUCW3ppNDaMlPauZ3OmXNxndML470tO1mz55yz91r7W+tba23lX+Qmm+jGwMXnn4cmYovURbaIGCL/tva/RqBnEZ0pZpV7mfrPU4lEsWFi6p7oU8TwOKxqmCvKXZGkyFa4caoXa84kXWjguWpNB1BqaS7fTJHbVaHv2bmG0YxjvXc+nzg51gEpgeUbxFWLKTVCh+/JLtlRFRmE+Xic4lAnrbrByQ/2EDUdlLYGy37gMIP9r0xQvR0j81TdMnR5GYk7YlQk6YaLUspBU8vuXJlj4DU10n0WDzxdYuJsNkSiGR5aoES12ZFH31Ph+2CNXdfJdNd4/LUi107k8Wwlx1hRrvwlkReaalOkWP2/Kb/I8FfbufRpgXiXjWPp6KaEx1ehcutuBDPhrlp3bJ1krokZd8M1nx09yJY98zyRHQ9psvYhng8U12ejaEKAAG2mv8b4uRzJTptHXy0yO7KZTdss+p+7w9T5TkZ/6EaLeHIwA6eiY2puiGRdhv1lopgJJ3yIJCUkgvjhw1PsPjRDZneNymQ8nH872Ut+34Kscbn67RY04UK4Ty2FwmCDwxP0yYyN72jEuiwyO2vMDKWpzMQY/zGLbypGTnXSf6BEz2CF6eGOv2XWukfIN08T9EKegFi+xi1hezLbpFaKcv74TloNnR0HS3QN1IkKcZ2W+pOODSH2Q+755PcssFgxufRlHy+8ew2z02Hg0DRPvjGGmZNYZmVtE26ezoiL2ljKfbjatTVSwvJI2uGhI9MMvj6NKvn3/Bjk/oKY6obOvkaYXu0ptzFXiwK3qVO+kWLb3rs8//4IqipG55eYT0oq47RkagKalsnomZ6QhO3FRtsoYmUEiqAlB2jNGDTGIpLoYqhscP2LXqG+YlJcXLkSoy75HeT7fZMr3Cik8gXB5KUuPnnpGc59tIvaTJSv33yM428doFqNM/TNdhYnTAoHyzhywPa6bqyXzcFPEN9ousXETznshrS1hkbhSImqFP6JoSyFQhnDcNj38iTd+ytcOLEDT9LOXWa2WrdhQRgYciSVUlEvZHTLMshJni78muC61OFE0iYtlWvo8wKxtE3xYpbyWIp03mKhFMf29DANjdUm0CZ+e6lqm635CL2DC+x9e5Lv3nkkRBHvsMXFMc58OIAhzaajx+L3q2nGz+ZW3RRNtsg+WOXFj3/hyrGt1OaiGI58aAoKKpElRkpbrAptvfbuFKRGAykCGobU6prU6lIphW556PPmUsyFbJ58n7uVXPXOyrBrBsUrOS4f245Tl8JjSOFxh6l/X0wlRmuR1YtAQnkcpYK5AlVKLJvlHFsVo6e7aZQjgrR1r2X+b4z8sOjMyUWgZ6BC/+E7DeVfkKvPZrn6ROSe4f21I7Rxv7l89clvhB3LewI9llx9bjP7hwADADpQzQBjYpxJAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 202,
                "name" => "Sudan",
                "isoAlpha2" => "SD",
                "isoAlpha3" => "SDN",
                "isoNumeric" => 736,
                "currency" => [
                    "code" => "SDD",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozNjc2Q0MxMjE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozNjc2Q0MxMzE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY1RTdDNTBCMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY1RTdDNTBDMTc4NzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+dsEThQAAAfVJREFUeNq8lT9oE1Ecxz/v3ZmkMae2xdbuiqRiqaugKDo5WFGQOiiimy5aR6dOOrhYHRx0qQoVaQOCbgpO/oHatTrVhCYNtIlJbWJJ7p6/C7aiVhfv5Qf3jrt33Of9vt/f7z118sTe+TvPE15XQzHbGaABZbAeihu7zc6sw71MB0cWXGY9n28uOJbhDgM910q9QXx8oEFnVXMsu6mVcSVuF64lZ1jUrYcrp2sMD9XwBbinpPFlzihb4LVxRQhlzdP9qxy6sMKbHT6DZYdYk9YCovd4JF2Vu7f+JpS3O4BVuPUiydXpBPMUKLLc+tweOAzhkpIVxJsMv1bm/rZTfrK7y2QrBWUi8t39qwGh9HWHiX1l9bF/znl8/rpJk1LReryxFiK7I0MPMy8nVf+lXfrhh2fqx8x/X/rf6xJdfR/60tAMOHdziMNjZyguL1rM+I+OF1caUKpXpd2MJY9/BxY/QSzJ3ZFxLh84a7G41toq3MJ0haMHj5tHFx+Y3giLa2NwIMDNMpWSje1V0WzJav/2zKjJfVlQgQksgUPo9oT4KYBMHvO+rKaYcKPeuX7+MCwYR2qtrwPmvsJUDgp1a4eE+4u0W2PwVlplMmv9PHbXpZU+5clnmF6iHeGKtB65mkgrWebrtCtc3i3lyeQ8jKGd8V2AAQDj9qv7QrTRKwAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 203,
                "name" => "Suriname",
                "isoAlpha2" => "SR",
                "isoAlpha3" => "SUR",
                "isoNumeric" => 740,
                "currency" => [
                    "code" => "SRD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozNjc2Q0MxNjE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozNjc2Q0MxNzE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM2NzZDQzE0MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM2NzZDQzE1MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ZGHpRwAAAa9JREFUeNrsVbtKA1EQPXf3buIa17xMNCqKaGyECJZW9v6An2BroSD+g5W1v2KhKNjYGBAMPglJCslmY9TNPsYxJGClNyAK6oXDDrNz98zcmbNXYD1H+IGl4YfW3yMWDlGdnwlG8E2cOsOWFnWMnkMxXcZj1x5i9D+euixOr/S2KW8PnzVExt1OAu3yADSzr8N6S5ukd18W7xwfE/JIEIc9I4rZtTtoEcLl7jRMuOwlfhsqk0s9kfg8jM+CSMDMufAdCaoMIrnqQh8KYO5aiGYMGGkP7UpEsQRAqqYYMKFRaCK/X4SIcWUhf10Qls6OQa7A7fYcni5ikHHvC+XEHHLYh32UwvXGPIKWhJgIIcYIoafjZjOP+kEauuUrVdufjnWCYbVRO52AfZYCsuwbAxrnSVQPJ2HEPG6c+njLwG4qB3swkJotIblYw9VyFuGLhpm9KkYWSmgULRgtT51YZpLKchLc5+gUcLNTwMNJptMDsZWFkQsQKSWgcztU5STI8Rw2LLU0OTeHu/PEGO1qt8b/HZOHLc7whWrBTeET2WzEOzL9vvuhIf7v419P/CrAALDtiSyIepCqAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 204,
                "name" => "Svalbard and Jan Mayen",
                "isoAlpha2" => "SJ",
                "isoAlpha3" => "SJM",
                "isoNumeric" => 744,
                "currency" => [
                    "code" => "NOK",
                    "name" => "Krone",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzY3NkNDMUIxNzg4MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzY3NkNDMUExNzg4MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iMTZFRUEwNDBGQTBGQUFDMTU0NTE1NkMxNzg3NzIyQzUiIHN0UmVmOmRvY3VtZW50SUQ9IjE2RUVBMDQwRkEwRkFBQzE1NDUxNTZDMTc4NzcyMkM1Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Ec+VdgAAAaJJREFUeNrslLtKA0EUhs+Z2c3mIhiLmASFFIKiKSRBIYVg8A1E7CzsLW18Dgt9Bi+FaCUoahNvoCQIAQsbxYhoTLy7M7vHWVHB9bYpBIuc8p/Dd5jz/zN4leyGj0VCQPW66XBrqvA4lh0Hg8/uTQ8b55epAR6LAufgrRj8WdXRrtLQstyaZaF8EfFNQQQi1flF8w9o8/TUrZnCfrxptCVy7VVRqZDSvClz2/KeEC2yu+LWbBuECeFEsyiCJUFgXN5BRyq2n4OA4dzAI3pJS7hzjUh+xtcLy8s70NgAGlucW6/0p+1E0tkVkUc0QmjomyOChiCLhNWO6awK90812wj34ttDwySOaGv0JOBBANWGxsnV4uebIGPspLSWy8/PbANnoyN9vT1d1BKzVUI8D0C6PftkIznuhVsXNg4GsxOg8838ZKYzAeVj8PkAPS/koj3jnmaa8roSPS+W9BBwXaFPtCAcFUptaV80Tu+J/BWtB/1u+3TOpTKAE0nHTPVanCWgEQjxYKCGXJNhuNGMgRLRYb5JBAxVp9Nc/1T/IfpZgAEASgSdGXymLpUAAAAASUVORK5CYII=",
            ],
            [
                "id" => 205,
                "name" => "Swaziland",
                "isoAlpha2" => "SZ",
                "isoAlpha3" => "SWZ",
                "isoNumeric" => 748,
                "currency" => [
                    "code" => "SZL",
                    "name" => "Lilangeni",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4MkM2NTlENTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4MkM2NTlENjE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM2NzZDQzFDMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgyQzY1OUQ0MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+wwOMbAAAA4hJREFUeNrslV1sFGUUhp/52Znu7uy6u0DLFlYMaREDgaiRBFAJhlATa2JSNMQLuVBDYiTGK1PU6IWJF6a3XuAP0Sv/wdAmuoEKSpoULanahtaK2Cpt12Jpmf2bnd0Zz+5WS0wM5YpEnck788038533nPOd94yyqm3M5wYcKjfo+O8RK/4UV+Qeua5VhmBeMC6wBI2CBoG3ZAu28vnNoWsSV+2pisJqxSdr5+nLQzkZZ/vdW3F+ucDM6XNorhhJikcNBn7lmh7Y+vyE9k/JoFrulYWgUoJuinxg3clAYYBnH3mKra++QlEIM6e+Y+iFg1zo7yEqbmqywpdTk7GDLXddEJaZRYd00/Kv5kKRd15OQUlU8EOQkCCaFJVnztt8pie444HnuXXsLL9nZmpLGgKwZtcmQTfjH3/CyT0dxCNzGBGT3yYd1ux9lOLEz+T6+tAta5H4T0LfV9BsX7aqQLFJJd+is6zRI2woPJbOMdKyjv0PdWC5WexoKx8dfYuH90zRefAlfhgdxdd0Nu28n/v6j3Fi34MYsQrLnn6ZYGobdL0oVoWjLAEZfm3v6lVdloEqpJuLXLotjHdvgOaNLqq49cTJEv2XYV08Tsi1Gfz2EMNDbxMOBjhytIfh4WFC4TCFrM3syCCpLe3c88YRKjGTVGuO+UP7uHiuH2u7iZFw8QrKopxU3ydYzHJ8w2662t8k7XQQ+qaMOqpRytRroFwuUXDKLF+5ivUb1uKVy1iSOtM00XW9Bk2rf2s4TVhDOWYff42RcIyu547xzl0HJD4Xs+JepWOJTJeo7dM645fX4p73mBuQjHyvczgSZKN8MqmqKMEgAVWjkHewIhE6OztJJpNkpqdxPY9QayvTX33JqV3b8H4V46KXS2diZCYbmRhshikPNVSvKeVTy6rJyfAruFLCZ5o3s372J5Y7c+Q0gxUio4SQHsjn+cJ1F+tQ5k709rJzx46/5i6m0xxva5P6BTMalUL1MEp5vk7eTsyxxe4YWcOq6sVWPiRSI/bk0RDxJMhJR2mQq1mTQ1VON8m7lYJuSdXrIpCp2ixs2b2X9989zGqnRO+T+5lIv0fVbEBEVZWOVxtX5CknKwNiNyQp9uoNpGdFeEkNpLp7KYmyIJk6K9n5UQQ8bLu035KkJSf7OXOFaNRACZlLaiCKP36dLVP5G2YWvIovdJultsz//8f/euI/BBgABhdR5PJe/UIAAAAASUVORK5CYII=",
            ],
            [
                "id" => 206,
                "name" => "Sweden",
                "isoAlpha2" => "SE",
                "isoAlpha3" => "SWE",
                "isoNumeric" => 752,
                "currency" => [
                    "code" => "SEK",
                    "name" => "Krona",
                    "symbol" => "kr",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4MkM2NTlEOTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4MkM2NTlEQTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjgyQzY1OUQ3MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgyQzY1OUQ4MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+cif6DQAAADdJREFUeNpi5A7oZMANvhSUQxg8E/ApwwqYGGgGRo0eNRo/YPx/YDRAUAJkNKOPGj1qNCkAIMAAQwkJrW14Lx4AAAAASUVORK5CYII=",
            ],
            [
                "id" => 207,
                "name" => "Switzerland",
                "isoAlpha2" => "CH",
                "isoAlpha3" => "CHE",
                "isoNumeric" => 756,
                "currency" => [
                    "code" => "CHF",
                    "name" => "Franc",
                    "symbol" => "CHF",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4MkM2NTlERDE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4MkM2NTlERTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjgyQzY1OURCMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgyQzY1OURDMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+hqToNQAAAfxJREFUeNq0Vb1uE0EQnp87/4iE8w+BCIxFAWVegD5lJCoKXiRdel6CDiFKKKHnBWiQoECRCVbC2Tmc4LP3ZpdZO44jkvPZBFan0+7c7jd733wzg5/aISwz8MLcLXUigOWGM3buJaB/BM0IJjMHc+hSy0EYgLjrQSPaJONmdP/Vc44aapCkd7i3K3FC6wG4RehYwDWhdMfhw3uPPnbObZ+3WubLN94sgV0ETQtAPRX61h9zIsfx1OwnTrzxfMNqhBDYn2OXKtEgJ0BxH2x29slmEvfVCDgCASwDRSHY5aEzy40Iy2t6S+oPw7u3gXjmlcNWG+iQ61X17Ma/3HBw5d0vcY1eZ+a7tF+/WN956tIUJAMOOaprSCcydJL0QQwwY6V68u7t/pNnwR3CMv+h96tvrZHnWpMqN0CfS5rhWmN+vrGRJxOC/zaCHDV7Jdj0tJCQrHeEuJKurcXK2jSMMgnjg/cfuHZrIr4fX7cfm4PiMOYoJCDpJS5NvPgG6ikFKzOvYjr7pjuyg+SvxGeBbpYgmlADI27WL4pPl/a0yxtlH26NYU5O5teQ6QENs1EHPGXDFyudIHujbliY6EXlyQFW0Y2HgzcvKWp6j0msSzUWVm0sbgXKhCZR56yGKkFhiyEkkOvXa4UgCjbdrLtoSSrGXaHL+DxesYH9FmAAtnngpsgiSsEAAAAASUVORK5CYII=",
            ],
            [
                "id" => 208,
                "name" => "Syria",
                "isoAlpha2" => "SY",
                "isoAlpha3" => "SYR",
                "isoNumeric" => 760,
                "currency" => [
                    "code" => "SYP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRjMyNzg5MDE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRjMyNzg5MTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNGMzI3ODhFMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGMzI3ODhGMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+bS4JXwAAAKdJREFUeNpiOSeo9p9hAAATwwCBUYtHLaYZYPz///8nIM2LSwEsrzESaSCR6j/j9PE/KL396WWGzU8uoIhRQz0DyMf/cYCdz2/8F5kT+19gVtT/jU+v/CcESFD/iQWfo5Q5eBje/vjE8P/3dwYFFg6CwUyKerxx/ODLG4Y3v74C4+0/gwArJ4Mqrzhew0hQ/5lg4qIR+MyIlBDpm51GLR61eNhZDBBgAHqosVFFWSaxAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 209,
                "name" => "Taiwan",
                "isoAlpha2" => "TW",
                "isoAlpha3" => "TWN",
                "isoNumeric" => 158,
                "currency" => [
                    "code" => "TWD",
                    "name" => "Dollar",
                    "symbol" => "NT$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRjMyNzg5NDE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRjMyNzg5NTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNGMzI3ODkyMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGMzI3ODkzMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+yLbMqgAAAZxJREFUeNrsVstKAlEY/maawNtgmQtvRC0ENy7Etbhz68KH6SV8gV5CFy7b5EJxUZCIuBBMvJTS4G2svMyl32nCaValhhB+8HH+c85wvv92hsMA110APqIIDQxR/jShEFndPiKquu0ADwFdXNE4wy/BE5843cB6lGG3c1DVOTyeEwjCBIpyjOlUMjixNXju+3wV7RzRqBvBoBudjohE4hy1Wh/5/CoxNkPU28EkPEMg4EIk4kMqdYlw2IVyeYBslkO7vUCjIdA3ll0Lq1rEoZALyeQFYjGvthqPe8FShuv1EQkP9LpvD9YsLEmKRiNkWcVyqe6yxsaTWK2xGo0xcrkWisU+NRhQKPSQyTTRbI5pX9K7e+c1tqDVGqFS6WE+l5BOP8Dvd6Ba7VOqv+q7s+aywyxeul+gdPcIj9eJm9sXSvXKP6chWgfxTb8FGwpbMTRdKAZLUdaOfK4PqZUYbdVGoqoe7TsWsGK0VfSMAPvE8BP5EVRyhCWXTskFZjNxkTvDK/YBFnvCQfgg/P+EOf3Jw6+fPn8OTetDgAEAPv6K9CaVVcEAAAAASUVORK5CYII=",
            ],
            [
                "id" => 210,
                "name" => "Tajikistan",
                "isoAlpha2" => "TJ",
                "isoAlpha3" => "TJK",
                "isoNumeric" => 762,
                "currency" => [
                    "code" => "TJS",
                    "name" => "Somoni",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRjMyNzg5ODE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFQUIyQzc0RTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNGMzI3ODk2MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGMzI3ODk3MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+YTHlPgAAAVFJREFUeNrklT9OwzAUxr8mdpM2bVJ1QELqUsRAB3qALhyAuRyEAzBwCa7AwAh34AKILkgUKEJi6J8AberY5jWJmHHV4oEnPSn/7N+X58/PpRHvzWAhGGXdBtiBpbAGZqkY2gEHxyep2ZASpYbDatm1Sj+KR9psFq21WkexBAGh4CJc649XYCOpy+QB4vMR5do+tJIQiye4vAk/6G4PPB/fEkzA4RGUmOTuZA0oGWdlrzZ723G14wbglRb8sIvZ6ArT0SX8qItydY/eVYwbyK8iXbxBiim88DC7D1t9MlRuD+bvQsyfKV8yYRsFlxxOIEmsBEk8QPx6s1ooKnUEr94hU6f0TXl75kq/hngfnGExucvW1YsOsNM5B6u2/2Y7/YQutrZpA7m4P5WmDcTlDSqrm3NJt1yOCwUGsxxdG47YVMtsBW0b3P94LFLGNsDfAgwA/Id16/aLV8UAAAAASUVORK5CYII=",
            ],
            [
                "id" => 211,
                "name" => "Tanzania",
                "isoAlpha2" => "TZ",
                "isoAlpha3" => "TZA",
                "isoNumeric" => 834,
                "currency" => [
                    "code" => "TZS",
                    "name" => "Shilling",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFQUIyQzc1MTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFQUIyQzc1MjE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVBQjJDNzRGMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVBQjJDNzUwMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+A+PQqgAAAytJREFUeNqsll1IU2EYx/9nHnVTpjMoQSuiNGeaXWmo0BddqFdFF12YQSVFVgpiObWbAlMhP/NO7SItIsOvUOhLQoouYxemiaVSGvtK3YebW/r2HOdsS8/RaQ8ctou9589vv+d5zuF296ZNAoiiy4ItVAAYbEwG/Xww1HI7Bo7osX3ejII8Kxqe2b1/qqRril/+Aq9Pv4qjS8YxjLmCACeHyv1GFCcZMNlpxp6rDkzoFtY6puT/C6VDjniifHXcgJ0uIypzbChpc0melW2WMoAox38HQW8PQkWMCZ+zxhHc/xMx8eZ1QjlcP835HyxQ2hmHMbsC8YFO/Mj4Dk30BCpyTNhxyoav+kXRsxGhHLRNDA/aIzcevOSSQscESkcgKolyMHMc8jdTiDtoRmmbU5oyA/g1wrA3Ox0JXR3g/etYX5cV5+ZQ+tgpeVal4PC+kSHhogLVWg2KXpTQDQOlgz2UgkvmkqEq1oRbSXrouszYd8WObxJ/q1D5WUB9C8NMWDri2msxYkwGQvQAPyMe7E2pXqbc5TKh6rwVmlbpjo0I4TBQz5CYq0CtthiF3QIljVvYKED9AcavDvbM5TjNJXPKUEGUmkN6mLrNiCXKUZ005TVy2fiQwapKRfzzOgwbUlYohUBP8WJzqVbY8faEAVHksjzbhttPpClVRPmRXKovyHFfW4qbPRq6Ie9D6V28h5LzcWkklwboOmYRm+dYh5JDQRZDXTO5DE8nylqiTF6T0ifY22UcuXx9jFwuCC5t5FK6Y8PlWOrYxEtycqlBYU+Zm1I56sZh4r3LTxDlopdLA7mMuWyXXARC3cgEGlqA2fA0qInyi4hL0eADtH1eCi6dJtzLtqJsHZfbaPt8aCCXS3NZgqKe4lUdu6GFxBajzVOd08qjG3CZn8lQ30yUEelI7a3BkJ+UXmWRleeaEH1mTjI0nLbPIC2C+j45qg13oHr0DkMzKW7KAIu/oStjaxZ/Fv+lnFERZV+N6Fz6WRbRh4QyhMenJoGSXBruIqK1H8PTKe6O3STlv3NsWSZeefU5ezIIT5s5OCJTaC6JUn8YCN0ypferj+WPAAMAfeh+JFKb/jQAAAAASUVORK5CYII=",
            ],
            [
                "id" => 212,
                "name" => "Thailand",
                "isoAlpha2" => "TH",
                "isoAlpha3" => "THA",
                "isoNumeric" => 764,
                "currency" => [
                    "code" => "THB",
                    "name" => "Baht",
                    "symbol" => "฿",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFQUIyQzc1NTE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFQUIyQzc1NjE3ODgxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkVBQjJDNzUzMTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkVBQjJDNzU0MTc4ODExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+MpuROAAAADdJREFUeNpivMcrykAbwMRAMzA0jWb8////aIAgBwiDSu1ogIwaPZpliAYs9/nERgOEPkYDBBgAYAkIxbRv61QAAAAASUVORK5CYII=",
            ],
            [
                "id" => 213,
                "name" => "Togo",
                "isoAlpha2" => "TG",
                "isoAlpha3" => "TGO",
                "isoNumeric" => 768,
                "currency" => [
                    "code" => "XOF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQkUyNkYyMDE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQkUyNkYyMTE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJCRTI2RjFFMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJCRTI2RjFGMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+DvPYywAAAaZJREFUeNrslM9LG0EUxz+zP2ISsxitmlTxIghCKVSx0EKP1pZAWuiltwQvKir6H3jw0D+iB/8DLwXx4KmHXnppEaSFEEwhAaFhS9JCks1mt7MrLPEmOPGUL7yZebxhvvO+8+aJ8/RKDbDoh++DEJGbcP1wXijM3tx3BxjSZiIvIJOkvUYTLZVCmMb1JQYATdrfG8k6XUafL0viJHS96D59AijLGKGbMssGbbeESZa5ow9cvt3mz9UXkizgWQ/wPRd/45Na4u6/OtaLZ2Rf76LFk8QX53l4uEf6cg3nW5X68Sm6YSmXWsqo0alVSCw9Ymq/GAbS73OM5/K0K+XwnQVCPbExOo5TqvLj1SpO9SoK/nySp/n1M/rIJN4giP2eKw/ukHm3Jd9bp1zYpFP6RfZgR0Z1TOEQ0z31xRVAj1t4rRbll+vYFye0zi5IPn1MLDVDgl5Y1uLjG2XE4nxspRk0kKCyXdvGxyU2kaVr/5brHubENPGOc91AirPqMm43En3udDi6djBmorUm5VYN4X9nMK3pNsU1JL4PGCq/yFDqIXE//gswALMxeaDVbAFMAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 214,
                "name" => "Tokelau",
                "isoAlpha2" => "TK",
                "isoAlpha3" => "TKL",
                "isoNumeric" => 772,
                "currency" => [
                    "code" => "NZD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MkJFMjZGMjUxNzg5MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MkJFMjZGMjQxNzg5MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRTFENzgyRDE4MTQzRDBERDc2RTIzOEJBMjNFMThGQkEiIHN0UmVmOmRvY3VtZW50SUQ9IkUxRDc4MkQxODE0M0QwREQ3NkUyMzhCQTIzRTE4RkJBIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+xugOwgAAAy9JREFUeNrsVFtIVFEUXfvMjNY0zaMEa8zINLEHViCTX2EQFGSYElHRk6h+il4/fQS9IAjsJ0IoKqLCCOyNFQ5o9uNPDyYwKxWVCjPLfF1nRmfOaZ87auPY66e/9sdcmLvPWvustfYlIXZBl5ByEBgAJgshAIU/lJLSBjiAwc3r6nPTvp24kNdnQAg52iFiDymDOTneYydLbDbNodTvQbnfAyQXrWioq7h99ab/VZO7z3AJEY3vs5rzGsBQMBT53D7Ah4CgUlai8bNrRiUnAROX+hqPH3pWsKIVLrTcdV57kAP0ARTfLZQMz5ufnpWd0db6vqzsvtNlP3p0TWqqw9QnvqSUViVT3A7j4umHtfcrCla2ohnohv95OpMBkYTbCYX+kuJFqwvnA2HAEwpHntY2G/1hIstYESYDzo2FgXc15Tv2BfAVeEP6VgKd3XbAQpQonFUIz6lTfn4jaAqIDCNc8+Q1YBfCYgrCoHxoShIZl85Ubdpdjy4gwIgETc3oykLyp56w1iRlRGqZFBQnxCmEIwZqisAx8OTntl4/+zgzvwtvgVAMNHYZDe1NYZWHlCKiMd5or4Rg05A2w1O0ZoktSSkVGcF1Aq5DO+rqHpVnLujSww7G4eqhFZu3bHEH0G9OiQTomPehGV7n1i15E5JsSkWl5NikuO2hO+dvlZ6r0XFv0npinKAserqvx7fgCzApIVE0sjJ6SCAqyCpVMg+73Pf26tmq6Qt70cDmC+33T0sqZKPy8pzCg8VAj4hrE6McsV+p3IDtyJ5qf2XF9IxeBCyQOvq/XCAGaMOqbY0Fi98AKYAcD8368rDeubM/1d64dbK0TiehhWCVJiv9ejlJGxvEvSv+ae52llEp+UMQrQSSs2YOnNj/YsOBl/rvLpNe4G9rCEgFOql4/dq71bxBQV56q5S8SJwwmjb1Y2d35PBOX8+XCcKitPvDtgjWSSeL4wUdchCNvCIzgOYjAm+24Z3KaUnj5eRAU37u2r3bG7yucEeH9UO7Qw4fIhNn9Dh/PXgxhjfO5DDbNDuPQFHSlGoIHk8wY1aoszfpQnkmN+3CvymBf1b/oRPquwADAFZsVbvbw0XNAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 215,
                "name" => "Tonga",
                "isoAlpha2" => "TO",
                "isoAlpha3" => "TON",
                "isoNumeric" => 776,
                "currency" => [
                    "code" => "TOP",
                    "name" => "Paanga",
                    "symbol" => "T$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4M0I5RDdCNzE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4M0I5RDdCODE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJCRTI2RjI2MTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgzQjlEN0I2MTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+hRrzKAAAAQ9JREFUeNpi/Pn0KTMvLwMM/Pn06e/372yCgoxsbHDBx52dN1pbuRhIA0xsUlJAo+HoYV3dbi2t3+/eIQsysbP/ZyAZsPz9/BniaqDzf7548fXq1X+/f7/bvZv/yxdubW1kt5NsNJx1r7DwwurV4gwMQHQuO5uRgcH5zh1OZWUqGC3g6qry8eOfixe/vXwpaWLCLizMwsfHQAFggrMkU1NNd+7k0tB4wsCgPn267o4drKKilBjNgsZX6u3lPXeO18QEWZBNQuIfGUY/7ukBJgA4n11GRlBE5OnEiX+/foWIMDIxfdi7l510oxm3MzAQTFisDAwcDAykOpyFyIzwj5JopDoYNXrU6FGjSQIAAQYAmalRzsgP/4UAAAAASUVORK5CYII=",
            ],
            [
                "id" => 216,
                "name" => "Trinidad and Tobago",
                "isoAlpha2" => "TT",
                "isoAlpha3" => "TTO",
                "isoNumeric" => 780,
                "currency" => [
                    "code" => "TTD",
                    "name" => "Dollar",
                    "symbol" => "TT$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4M0I5RDdCQjE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4M0I5RDdCQzE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjgzQjlEN0I5MTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgzQjlEN0JBMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+K4UpOgAAA9dJREFUeNqslX9M1HUYx193YMovAe8uajKwgXCA8cO4TCejBUUGo2mDwmprLSFD0yBmtWajVWoTSjR0qeuPiiVmw5FsAbnGUFJAwQkcIqUEDPHu8E4PSO7u2+cOYavvlQI9f7+fez1397yej2JCkizrc1/j6MHD/LO0UVHUNzSweNBIc1wCdiQWqMOQ7BPMtRTDb26TNHt2sumD9/niw49lgUCNml8u6YmVvGlbspRRywDeqhgkx9zgyq6yXfSnZ7Ov+CM+P1IhC4xcNxAXqKa6v5t4cz+qKB03jR0oUIqxFbMH+wVEcqXmKD2PJLMlO4fKM6fdBjNjEzhUU01k51lCnnyWmyN6sNkFXDk7sHNwX1U0w+cauBAURtYyHc3Ga3gt9JOFN6Rn8k7pbkJrq4jOe4tblss4xscE22MWYEkS/5cNH3UM1uE+Wnw1JBrH0ZstPKSNlDXsKizi5c35qA6UovvkM8ZGr2KzjKDw8Jwh+E45N9VLHYldDNIUEUrIiZNc7tLzWMoTsqZv9pWTlJbG/He3srq6jtsTQ/xpGBDweTMHT8Hnqxfj6RnEqYwU7GVf0lT/M8+9+oqssbG2lqiEeIYyUlnV2oGHkG3M0HPPcNlmSHYb8/wXscArhF+35GF6422+P/wV20o+lTV3t7UTHhBAW/gDJFqM+KgexGoQG6/0vOvGu11JyWFH6e2N78JwLu4vYWDti+wsKKLk269lWavZTIK/ihNXu4g1XHHpdsvYKT5E8Z/wf3fB4QBPD/wCtfxWVYE+egUF61/ieFur23jGw/HsqfwOrdBtibgL07oplTMEu766JKaW8BO6mbrOckETRmZ4LM2mYXz8/WXxrc/n8J7QLfjHIyzbWDip2+ioW93ubv8d3byduhn6afYNJPGPEXpv3CAsOkoW3yF0yxa3f1H5blaU7GdsrA+b2STT7Z7PzqRuS3GIlqa4SIJqTqLv6GRlaoos63xwkp5O476C11l9rIYJ2zVuGwb/tvEzuneTugW7dGtMT0EqLed0XT05G/Pkuv1US3hMNIPr1rBS/7u47U7dLk3DZ3xop3TzcupWmI8pv4iK8gMU7JC/bL2dXWhVKlo0Pugs1/HVBE/rpjgXGCHN7sqLmSdsrgUKSc0ktO44B8Ujkivuubv6ofUMa5c/Ss/yJIbON84B7HrNFS5frUKd+2NXEdF+iqrzLQKgcxvfe6ySTeuy6Hsma47gKb746aziaPj4BxPf10u7zUxyhBaz0STLbi7eTtn2YhfYwv9QzqUZN/SKm+2B7mIbfRo/nkp+nG69XpZ9IXcDfwkwAKswgUQLQuTdAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 217,
                "name" => "Tunisia",
                "isoAlpha2" => "TN",
                "isoAlpha3" => "TUN",
                "isoNumeric" => 788,
                "currency" => [
                    "code" => "TND",
                    "name" => "Dinar",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4M0I5RDdCRjE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4M0I5RDdDMDE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjgzQjlEN0JEMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgzQjlEN0JFMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+2pqf5gAAAmNJREFUeNrkVktrE1EU/u7MNBk6mTws+IgiqRUpdFXUjaAiXYkbFyoSFSxiddMGsRUFsfgoYlcipYhIMAURQXDnT1BXSt3YlmJQ21iU5tHbvOfhmZtZuLKJjXHhgXOHy9w53znfPY9h39CxCCBMytEa0UlTjIBX3E0rhUv4R6I0/gkjrcLCqthJ0Gj1kNp/C9ghpwIDOQGteMPCCaOcIkibDAVo7yU167ZW1zEbRQGqHT6BcDqHTaVF0gVs/vIV2tEz4p0tWJCaCWxQHKsI3r6H0KvnqL6fxvKePuT6B2EtfUfo5RRC4/fpTEGw0iRgohMZaMdOwXf9CrKnzyPVdwBSZBu0qzEwvw47k4U2EoN28qyIvJYH6wauCjP62E1UXr9F+uljdNy6i9CLBMzZeTDVC4vXEk0b6BcGbZTXD2whD0XdCmVXF/ITkyJ/fbFBmDPzSEejWIpshzmXFGfb9u6GHOgkYN6MiKlMmEud6WasxGAXy7ArTln9wqzzZKw5dyzBB6O4ADP5Ge0XB0TqFB4+gtLbgw2JJ9jycRZyV6R2Ke+mYWY/Eb6vGRG3idbAR+/Ae+gggkeO48fIJeTODcGzfx8kXQcL+MXJQjwhGGBQ17QqD6P9mlv5v/HOg9KHN5CDGxF4MA61pxfF+DMYM3OQd0SgdO8kFuLIjY1CprbPaF1DKnUOCaeB5EWd6tELCExNkss1sqzlDPjlG+CJCYJTCdRfT/fiDUwnJ4oS1ekKuSFDCXYSpxKMTJLorVLLdADVelsm/4OxKAkHLLdknESq3WlDQ4L/n/OYuxG38teH/xRgALiVytS9zUFxAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 218,
                "name" => "Turkey",
                "isoAlpha2" => "TR",
                "isoAlpha3" => "TUR",
                "isoNumeric" => 792,
                "currency" => [
                    "code" => "TRY",
                    "name" => "Lira",
                    "symbol" => "YTL",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCMDRBRjkyRTE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCMDRBRjkyRjE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkIwNEFGOTJDMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkIwNEFGOTJEMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ZWWjjQAAAsJJREFUeNrEVktoE1EUPW/eTGaaZPpJ0w9N60K6ENxVRIhIUdz4wSJYEFy40EXpzoILcSWoCxFddFVc6KKr4qKC4kJt6aoqCOIHoehCbGsNrZq8NMnMvJnxZtKSVmuJNaRvGN68Tzj33HvuvWFfwm1zADroFajNMOmdV1c+sGauCbiCbRrqpqeMAbYDz8kGS0WNAHoI8P3/Bt6YscIB14VcXoB0lsDNOLSubvi8tAcpS3eqCswU+NYyHGsR4YPHkHj3AYnMLNreP0fL+H00DA6Rn1T42XTJI1UDJjbSSSN25RpaJx5C270L2ZG7SPUeQebqDTBNg967n2aDDMzDl1Zg7L8ORumUKSuawcktIHqiHy0PxoKd1OHjyDx7BC04VeDRo8V3kIHEVmHw3BxkOgWuhsH1Jgq/rARXrBOX79iBC8yB86XTkTsQBGqE4oCmBqJSyL2+KEBaKYT29qC+bxD5sXHw1mYUnk6A1UUqCoGyHjgHXt8BbU9PsLYmp0oXVkBLl3x4VhZaezcaBi6g4fJFtL+agptaJF/kKo77BsFh5d9ukjZMUYidUU47KbcuLkZxcjNzsF+/Cdb6gSSxKArOKzOhWTFM2PMz+HH7OsStYRLeUfDOBJlcV3GObygus/8M4mOjwc635CGI6UkSF6dTlQyxoDVRafdoHdLheZR6S19JXBESV2PF4uJDWvRSkdzqDoeB/NsXUM0Y9OQ+RM+dhaqb8D7NQ+3sQuTkKbD6RsiZj/DyZLMtwetiYLyoe69ST9u/MS6S5vALggqXQLTvNJqGb4J3JeBlBOzplyg8foLle6Pw0j/BIuZWy6f4E3i1ZDoFql7fSQQqtLadUOIxyNnPlLPk1lAjuTlMBN2tFq6/AK9pEn7Bgu8WW7VPzjDBDKMaTUJs3p0IgFE3Ymhet1eNsa39WKy4upZ/fcQvAQYAj5ICkAs+H68AAAAASUVORK5CYII=",
            ],
            [
                "id" => 219,
                "name" => "Turkmenistan",
                "isoAlpha2" => "TM",
                "isoAlpha3" => "TKM",
                "isoNumeric" => 795,
                "currency" => [
                    "code" => "TMM",
                    "name" => "Manat",
                    "symbol" => "m",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCMDRBRjkzMjE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCMDRBRjkzMzE3ODkxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkIwNEFGOTMwMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkIwNEFGOTMxMTc4OTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+rBlHvAAAAuFJREFUeNqklEtIVFEYx79zzr137rzfvsFMExEqaVQyULLc9bKEWlU7DRfhql3bdm1atQqCqF27AqEQkRIsSMkHPlAZx5nRcZyZq6P3dc7pTthrEcw4H4dzdr/zPz++7yAYvg5WybYr6cTjjzvp6rN+1wad24rVuod6ahRmA0rB0EEQASEADoCguMJHp8jyy2pFQ5urv2esJaR0HwbzRFMIYI4QqgxXE11DpmnthWswLgYtHJ2IM+Z1Cusz+eg6Pd0beaBFx5mqglMCndZ4fPW+4Pa+EhCl+c111XoHQkWn1om3iSprm63vY4PZg+rReFZLCn4Osre+si6eSVNG15MbkYaW2119kEmVkpqD4DBspjOzGogfTvkXaWUHMTCSRKnWFxQl6dvyLKzM2Tt7m040F5yU4Fpi5pr81eXKD+Q8F/ji1cBsLuA7lHRl5/PidDSdyqUS+GTLw5v3LSGASSmpC7ot4QCmpRFhho7eIopcMzZ3kgOX+0cu3Xg7Nf7y3RsIVZWC1jGuV7vm92ZG/Qd15vkV4C27qtsHFAERKEZxJTP06tn89y8QCAMhwFjxHQJYJVkM82Bup3inkHdS6Si7IJjUnLSgVlXUFHqjCO7frilPOiY0WIlAU6Rqos5YTUviAQHCgf8cE4ersAqSeIkjQzF49WaMgjEaTSuNWVvYYVKJASt29v4vxEA0oLWJDC2RuE+J7KJUu2FYaAplo0XuSAifZOfrPgkEMpsirXuafIDydjg2/RcaczmHluy28VbZaxhxv1gxxURrMuy87NQ6Vk7RWx/S8tONLY+vXXe7z+Ty3kA5rvHv5mOamdWg487g8L2GsGsxs08QHJ/7T/Ntzxjha92hJxcn/fTc83bVXqMljELzlYvWiadOcvLp7Iu7UnX7nm8kVJXkjJaT+s9/je12Gl3IzHiROxaDRx53wpSbC39KuakFlt21xbfD9jBWF8YEIbmy1kgEs5zUPwQYADj4QuUGyU1HAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 220,
                "name" => "Turks and Caicos Islands",
                "isoAlpha2" => "TC",
                "isoAlpha3" => "TCA",
                "isoNumeric" => 796,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MkFDQjQzOTYxNzhBMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QjA0QUY5MzYxNzg5MTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNTI1QjE1RURFM0E2MkU5MzlCRDM2MzZFMjczNThGQkQiIHN0UmVmOmRvY3VtZW50SUQ9IjUyNUIxNUVERTNBNjJFOTM5QkQzNjM2RTI3MzU4RkJEIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+vPd/igAAA8RJREFUeNrsVFtMXFUU3efcN/NkykNmYmSoCrYROgG0TUVNY1qtVavYppoyWrVNbAI24COpiT9qNY3BH/RPawxNCVg7pNJIEUzmo9rXWKYYZLQ1zAwiXIZ5z9zX3OMdsE2NMTZ8GnfOxz7ZO+usvbL2QdEPD7se8HyTrurs/u7yoB94x5GD657/9qOx5tYnh3BqNlm1ihyozrzeVpNd3WDZ2Ac3Hbjl8JU99/V4vv96+NnK/Qe2A2dfmBbh58n0TCwl5lryVwfuJ6/s9hyJ3VHXGSKQI6AR0P/hkGtJ3oBG3o6+Kwl2h1t62bnI7nh4pnT1/MDpsp1bxEM99MZ76xOT8Hjr8FX94/dOJn6JXApEMpkCx1FEJ7KMCFAA5BpLhJBRIghwXpKQcU23PmL+MZRC1mhaU36bdddUSOlsdC5ZbuOtAhMrc8kLSUcudctaRxb4mmDTvMxzlIKAsjgo2iqqGRNW7ZZKncIo+bsej0sIgSwbDUDP2KtTSIpk1bVb17tBtiXmCjIpDPpwwz321oeU46OyuXS81ClmY1CkhIEUZJlYyk3+T4N1OJOyi0rJQmjU5iyPz3DNm7xNci62PAXdct6Zd2/qeefBWg8Hw6M9E9StlcLmwYGzzZtH2rr2dHRVBMaCR8c7glwoZWLUAI9VqYCoknQZYeDM+9b1Z3KrusKfzcsCCPvmWDuj5NCSSgi/9WJTtMP2XPRU/5bONU+fbH/VLwYmhDobm5h9wdXubOju/QHv2nbn1DOFd5mLqqxJRX2BKCbFIkLFhBqq5mnWdDsLTrDeJmg5WMYtsnaTVO9rX34+Zz63aActDUgaGc9A3HPxJwoE62zwXNtL5z/Y/sSbj9W7SsM2nEgaZAFSoiXNLcLevVmfq9erNW6zNa5RLiMiJfRl3CL0/u7RcNgEwFJ0nhU4IPpx/3Q/3oDOxnmKoJIqTSXjvqGdvgKgKp4zC0jXEFZV7ULAWds86ftVbPcLx3brGx6FC91mA5Fjkaws+ToSNl6heR4zNCZGAGJonsMZlqaNlOg6RRGed3BcOcdqYHjWcBxl8Mr0D94FYB0YMWD0U6eTAGV9J+425sH4TzMaXY1F8jR1fRBYytBfVwsVjXrjNT85VVGiu9bVToSnFe9T/NBXuz456mDZJEJY0wpGL4YVBU0biotvvO2KzLWNfVF/acp78FAlxgsURd3IZl9RIJ75G9F/CYSJpuiqZuUFs5RPMkyOLkpaLEnS0sos98mSujL6COJyPm4AaWrxXNcTVizITf18/0P/F6D/EGAAAySvSKv5fokAAAAASUVORK5CYII=",
            ],
            [
                "id" => 221,
                "name" => "Tuvalu",
                "isoAlpha2" => "TV",
                "isoAlpha3" => "TUV",
                "isoNumeric" => 798,
                "currency" => [
                    "code" => "AUD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQUNCNDM5OTE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQUNCNDM5QTE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQ0I0Mzk3MTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJBQ0I0Mzk4MTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+uL136QAABDBJREFUeNrslHtMW1Ucx++5jz4ube/tg3ZreYXwHEwGgSEEQomasQQyMpUEZqIYJlsGRmNwRmO2mCyZic5kajTRGJxmzoxNFo1xcWMP2SQuk+LKuk1hFFg7aEvfvbf3dTwEXSLDhH/8z99fJ/fmfu73fL/fc8Afb33gaCj9eok5NHR7ZngMM5m/6C/Z8eXRH57u7RgKYsm0lZUHcoTellwpv2CPX0jFEzRJYGsNDhRe1qRlDVpryRRx1F0eujzxUinV1+QwP7rJJdM1Jqzu+qVr1c6LIXzAFDjWkddQX/Bm0NbxXcCkitmtGZIE10TzihZg2GtlB+rMo6PBZrKzq2pBZ/ycE164eefFHQ3d3fXzQyO35n01WbrxvZnZGIs1OwfdqTunz7ZoMb3NmhIU8E8ixAAFxJjELPAbcmivQzsXE1iAQcBtbtJwfDgueXkAo3GLBhdlZUmrYxMxDYWHLDa4EHIQYqZVi7Hs7p6+RQOr51MPHJAh4eccBJBrTaPFhptXgk4fl6XG08gQcsr5RNgbSmD4psYyU3BBl4yGOCwweMLQtdNaV8L8PBkQgMuWw/r8tE0PKZIQhQd6E5LOpr7fWTz4vb+9ynhtZ8lXN65smZKLNASnQJysnLTVtrW9/mRxjj4EL00c8Rsq8g1bTh3/raX1zNaG3T2y3u3ynPP2R7N+EQ3tAmdSSbz8F1qQNQYq0lpwGseUE3PPumLVQcHKqMIrGyLe6+n90B4pvjDy9v5v2o/NDp8NbGOitTdG3abirmeG3/92ktAyz1mlXeKczuWeKdhIZGiArKygkbq4xPzkf8wTK18SLPdS2RJUoSQUDIcQJw4PPP/rubE3kuxnhlzcTOVVG3OL2BhNe0qK5vKMiejSj7f8rqoKq7OsMCPlAWQKJ9VAEhR1RDSh9Chcus/ZRUWNA8jJGehnhypebrBcPL+wHTQfOalYzIzEb6QBSZGYJM1GpagpUx8O5ulxTEXJCvTdi0RETG0zW7kULslpqNISnF0z5+OzOZkmcRH1QUfGkdiYwBwo35+QDIc9B8Guj8483FDUW4CtPahqi7yt3XFyX+U7n070HZ/tZlXhqMgeLHu1MXvklbGPr4drGSpipJaIR1o7H/7+37grL5FkCZLTscKpeJECCajgIqRUuMintePhrRKkaGK5nWuj10ZikJfppKRD6cdE42LC5klsLjW4363Zo8djg3f3Xg01aYkUKjUAy8cVXycX+YAE5tB3662XkaeNmecHH3+qzX5qJpnvTzvQETerAxs0Poi68fee160agkDatt0+vK/yyHSo6Pd4qQzJ+UTudLLwgm/b7UQZQ0VX2Uiu1w2w3IGrQWd8nPFyed5U/ieefhUpZKoXU6gkQIJwdUDrRaOhySRqArrS0NqiXlx5iPJU4cKKY6vvWOw/m//Rq+ZPAQYAm+jfAfBELj0AAAAASUVORK5CYII=",
            ],
            [
                "id" => 222,
                "name" => "U.S. Virgin Islands",
                "isoAlpha2" => "VI",
                "isoAlpha3" => "VIR",
                "isoNumeric" => 850,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6REM0MjcxOTcxNzhCMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REM0MjcxOTYxNzhCMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iNjE4QUQ1Njc1ODU2QTk1QjU5NTgzQTRGRDNBMTc2RDAiIHN0UmVmOmRvY3VtZW50SUQ9IjYxOEFENTY3NTg1NkE5NUI1OTU4M0E0RkQzQTE3NkQwIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+uuR1EwAABFVJREFUeNrMVWtMHFUUvrOP2R1mHzP7Ynd5LYFCYBVKwSImWBvTYIGYCkbTGu0fk0agUdtqiI2KWmOqxhgfbdPWRGhII0nRUjV9YNtAK0HAtVhWaCndJ8s+YHeHnZ3d2Xk4VAQaQ2lMTPySm5xz5ztfvrn3zBmI53nw30D0r6ruxw0vWknn1qIn2Tsc/l7EpWeSpXxm8igX6ZNoNsvxTSieL73bQjJBOexfQuFvdEV7cfN2aLFo2VmCBbHgME1eTYYGFdl79KaKRWmBmqAitKsTCnW6UkoU36rSlkvQKllalgK3wGIomSACrvOF8hE+sCeu247CwluKKJpLzHuS5DgVuUIG+lnqsgIGSQrIMl4X3EBL15hKsU77gYSjTaYC4QBQIsCUA4K+NBG2Ke/hHwUC6XoJ1RwBbiRinMZwDPC/2882qBAPhiXGJgAqBTIUxGmJ1nranFsrglZIL+LmZ1Oel6EkIJJYaUmEocD0NHChNp0+pp6pvuW3oBChL37FzzUl3Hut6nYsHVCE6NoEYtKTDKs2FfWmZVQsnoQgPeYPnHJ79xcXXiGZ4QjUwP2sIT7xes7DaSB/I+RwNuXuKAKEDTUB0mEFmoBKGyAG9I89n3np8PcgcO7aKDDIACWrx0uOzcqNI56bDbkWKSxduAeVDH771sx4nPrU6ztNRHLX1Xw0uRWxHI7GMmfsvIY/Y4FHAJ1Pzq1T6gJSDiY8GUCCVBr7ATsybgOyWPYlrOuH9B5ca+z0OZ4bd4tFf3dIFoY980DBQeeMmANfWM2Hu1vf7zpIP9vxakWH21aXmne1V7rMpjoGC8aGXICMiwzaFFyGFB+bHAjFoeIzhuOHgpm7FY543JAuVxXguEgiXv5kmnXqE7dnCDpVkiaG1dlADr66uMtFZ5fV31CUfw6v36yi5owaVIcgegTNhTG9eJIva1Favp4s7HqPQnni127fdIJNaeUSaKHdoGXpR1UIkMIaTC3E9RuezkvPYlDmZN/HEiSzpLSFVvQ6Y93xuD7KiBKcNJiAb0Mfpqn59MKdIrWFdQwL51qTZdYosbFw1B6e5VhG0BG3tbWxPGB4Vp6i6nBFvloph+XuKddl58BvsevkXEEOsU/F90qqm+YnrFjAyXgj0UeeyHgxEOs/ZNA82OwpjSTc9i1VT+aYBbNsMp7PM5UaXAoLnc/zHMczwlrEQjBkH8reWWhoAc1vAk9f5uzU/tD84HfttT1P7eioXd97cR/Dh9jBbeQFpPV4zXV/iL8bf2lBq00+rz/ERkf7hl6DC95ofKjR3qOJ0OEwhcoRgKVIiWHX2ewj3159txX7pXHbKQBk/1SA7j1UQ0lwoKt1Y96Wx9V/TNl2V1WD+TnguiGmc05sCJaDGPRWifEdq3KV2bcWWIaeDvmEYNz2gfMnMHkB93sHhPSF3j5w9OQ5r3e1wjWluZWJzzscjU7f2eV4khh1TJE0vVol9H/7y9wX/hRgAAzpo5Xw0k2XAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 223,
                "name" => "Uganda",
                "isoAlpha2" => "UG",
                "isoAlpha3" => "UGA",
                "isoNumeric" => 800,
                "currency" => [
                    "code" => "UGX",
                    "name" => "Shilling",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyQUNCNDM5RDE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDoyQUNCNDM5RTE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQ0I0MzlCMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJBQ0I0MzlDMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+TD5l0QAAAL5JREFUeNpiYBgFKIDx/39aGc1EO1cPTaNZbilz4lfx/9P3P6zsqnMOLTi0UEFL31nR/kaUCcu3L4wCnJS6mpeHQW7uIjYvs+t/BMT1bZht1SXmLOIVoEaAfNFS4PUMO3v99tnLJ/nYQelJ2D3wi7YydULtPxikFJUqcjHdCjH7/+Uj1SJk/fqNQKN37d43ceKU/zdP71i6hKgsQ1zG4umfsFpYiF9RQe7lT6YQbwOGn69Gc+OQyo0jLEAAAgwAGc1CiUqK1KwAAAAASUVORK5CYII=",
            ],
            [
                "id" => 224,
                "name" => "Ukraine",
                "isoAlpha2" => "UA",
                "isoAlpha3" => "UKR",
                "isoNumeric" => 804,
                "currency" => [
                    "code" => "UAH",
                    "name" => "Hryvnia",
                    "symbol" => "₴",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjExNEYxODE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMjExNEYxOTE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQ0I0MzlGMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJBQ0I0M0EwMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+LuVcRwAAACtJREFUeNpitCo9wkAbwMRAMzBq9KjRw9Foxp93xUYDZNToUaMHg9EAAQYArtQDhtrhiMcAAAAASUVORK5CYII=",
            ],
            [
                "id" => 225,
                "name" => "United Arab Emirates",
                "isoAlpha2" => "AE",
                "isoAlpha3" => "ARE",
                "isoNumeric" => 784,
                "currency" => [
                    "code" => "AED",
                    "name" => "Dirham",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjExNEYxQzE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMjExNEYxRDE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyMTE0RjFBMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyMTE0RjFCMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+VISheQAAAGRJREFUeNpiPCeo9p+BCGDUeYuBmoCJYYDAqMWjFtMMsBCr8H/c/98M7AzMQCYjfX38/QsjVD0jNTDRFv/592eEJS5mZuaBSVyM/LygMv0ftRIX8RYzMrKOFiCjFo9ajA8ABBgAlQMOS8Um6UkAAAAASUVORK5CYII=",
            ],
            [
                "id" => 226,
                "name" => "United Kingdom",
                "isoAlpha2" => "GB",
                "isoAlpha3" => "GBR",
                "isoNumeric" => 826,
                "currency" => [
                    "code" => "GBP",
                    "name" => "Pound",
                    "symbol" => "£",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpBMjExNEYyMDE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpBMjExNEYyMTE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyMTE0RjFFMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkEyMTE0RjFGMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+EXnauAAABW1JREFUeNrEVm1QVGUUfu7dXZBYvuRj1yATNT5FxCClTNLUFNBJrfxRZjOKkVLqGA2okGGDhA44ToqT1pigA9qAhkho4kiCqRhgCKwBg5IiLCDs8rEid0/vvVfWGD/K/nR27u7Ofc85z/Oe9znnXi52S4Eh49sy9DS1AE6OgJsdYDYDhOFWr8fi+Ej8kByBuoDX0FOtA6dUwVrrignNlxG1vQT7Yg8B492Gx3HsUvBAmxHoNEAdFoyYqDAoUzeF2322djbS0k9h53el6NXdAEY6Aa6PIfBvTQTkGaBeBOxmgCFYFz0Dy5z0GJe7FbzxdAlc1CokJ4SjoSoRsQkLoXawAXTNLKhHZss9JaAY085idbdgN9EbcQfWo2JTIJLOpoCbOw0VR8+BvzUrDFe8X4Th5Blo7K2RmjQfjZWJiE9aBHtnW5lAe69IH9wTCMhrDLCD+epuwiHIFwmHGGCcH5LPpYCfHYrKY2VwPJyHoNYGoKXwNAlvRVItK2qVTzAZi0toyNqNAxT7eT6px2xkBQ+nsI+OSPdrJ4TRJWipXPkc/e4xWbq3OKGI+USSOmwPxX1fQ01nLhB9+AHVs7y/uXpSR3auJa+JXYB/Gi1LKaXiXTlUExRKvzDHinGB1L430+LYqjfS8k8yacmmEyQMCg8BCwN3KSqthN5dtZ+u7s4iYekS+kPMM8qL2nPyLHkq6jto6apDBJuPCVOXHSSvyH0UnVVPOrbYf7Gc6uYtpLqIt6kzt4AeZdV+IrCGyhUeVOkeRGbpw6zpGvVGR1PtjHl0p/DnYTFX20y08ouT5D13D730/kHiSNo2s/4+GE1m2DmpLec2qO8Ap1KxtlGAFwWj4iEYTagJjcTdaw1SOyldRsL31+Ow0joDBna+Ls6WeBIEUL9J+t9nuge1rTVgYyNromb8VBqSI+MNQSBwCgXTCSe3khhMw3vK3Nsnt9p9VfG2z7Af5q9UyqoW/QeFYZ2oUDA/jsdQfyp7G5ufukWVjg7si5dzMJB7f7Y8RO6fjMf/ZEqbMR4PGpGxFszDmXPi9JGalKTyihuTSi08KLXKYxT+3uTc0NSCnJNYHM+OjruPIflYxMUSm7qMsGJCkhZUSnDW1g8YiGAsl8BEUsvEZRoSlysTV1k+E5eLRMZsloFEwrza1hJ+19gHazsrca/yjhfFF+B2UycCfLWIWfkqArQjLM791XW4uXUbnNlIdYpbC3j6QGEjKYXxNMsCEzU1ij0YrKyGnV33ybPQ7z8gbg2jN3wKvb8f0nOv43xeKVw1avB5KbkYYerFmzPHW0CNpRdRHTwDTQG+eL6tAb0+gVifcRkrvipmIjdLupIQWekUolJZ0dZ8XYaYuBx0G+X2cZg1BXYhITCeKcWlCf4whM7E67fK4f6CB37MY3OyubXH0uTGkjK6Mmka1bBUg/PfoJajJyg9V0djlxxmx7GQQpdnP3ZkLogvZD7zyNk7kRKTC6mzX7Dk7TpeRJWeE6VpZn7vHbp+oph4Dzdb9F24jOrJ03F9+svwfXYE3PILsSc6A1Myu7Bu0Q405rNBCi3cNXaPValGfKBgDDpuG5C04TDGTtyML7efgmGA4BAxB4GNVXA5Voiqs9Uwhc8ELwI2TQ2G32hbaAqKsGvFboRkGRATsQ03fjoPeLkC7k6S+J7Uq/IaO3M3e8B7NLraepAQmw3PgM1gFUD3gBmOC+Zi0o2r0OQXQemjscKd3HxkWPkjPfMSGrLT2FhTyYBSrqd8ExAJsOknvclo7NHZasCWjUew+8AFrF3xCtasmQ2HyDlQ7lr9jXFnVjkac1IZIJO6l9t/A3wUAbpPQGuPjpZuqQI79p7D6qhp+EuAAQB9e+n65ZcRTgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 227,
                "name" => "United States",
                "isoAlpha2" => "US",
                "isoAlpha3" => "USA",
                "isoNumeric" => 840,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpERTc5MkI3RjE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpERTc5MkI4MDE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkEyMTE0RjIyMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkRFNzkyQjdFMTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+60cYSwAAAyhJREFUeNrElN1PU3cYxz/tOQUBD/aNymtbUAq2IOiUWXmZA40Iy2BzcW53y7JlyZLtZuE/8NaY7Gbe7WJbdJnTDOdQCbLKrERUotgCSodQ7AsFpK28yKT7rfsL2gv7JCcn+eV3zpPv5/l+H9X2xp65SqtJGfr1Fg3vNPD02SIhfwRniwP3pdvsOVxPaCHGs7+DOA/VJs8crXXEs3P48OfTfMIcU+SRaqlMzm8SNut2VuefIxvyydZIxFbWyX35iviLNZRiPZJaxdLyCkoiQUyc6cwFTPvC9FRkcbJMy7JaTrmxHIuvxaZm5xW7+Jl3NkKRaRt5OVlMjvuoqa9gwr9AgS4PvTYP78hjdtVVEAw9J+Kdxv7Td+hL8tGTeslGg8Jeexk3/riLs62O+cU441NBDjbZGbg+SlNbPYvRF9zzzHCoycFA/yhvCtRqnZbr5a1YEjGm5S2po1ZXfRHVaCTlWLODq24v1eWFGPVbuXH5Dh3vORm88xhziR5zoZ5rl9y0dx/ggS/EzGSQs5Ua3s39h7CUlbri0mKdUGzmijBXqzBXYH4Z931fsmlf7zBvd+wjIigMDI/TcbyRvt+GOSgUZ62uU3S2h8IdRgrTQK1S2T6PyhpZ+aB9LxcF2hpbCUUF27hy4S+Of/wWfUMeykuNVIin9/xNuj9qYWR8juknIc5szNC1voA/DdSypayAhlor57/vp/NEC7OBRfpveek+0cwvP/7JsfedhEWcLg8+pOtkMxfOuTjc5WSrSc+S6ymSQYtGyk5dsVT9/4zbhZmu3Z5IztggXOwSZjvSuZ+hUR9mEan/KAz+PkJb5z7GngSYdXu46T9Ho3EL6ZSKnZ9Fax0W5aFrDNuB6mROA6El7BYTnns+bPt3srK2gV+QcIjIPRLzrxL3ZkLLfB0c40udRCAd1EfFNioxaSG+Sl2NmchSnCKjwh6HBWlzk/rd1uTyMOTn8MbuctRiieyqLKbKbqXs4gSvQmFephOnRCIRFW+F11yyp/3TtD/eSKjYTM4rjcZh110yUZlDPfnVqcwovkppRhRnDrX/2x+UjKDuJXcuE4r/FWAAjBMttNdoYOEAAAAASUVORK5CYII=",
            ],
            [
                "id" => 228,
                "name" => "United States Minor Outlying Islands",
                "isoAlpha2" => "UM",
                "isoAlpha3" => "UMI",
                "isoNumeric" => 581,
                "currency" => [
                    "code" => "USD",
                    "name" => "Dollar",
                    "symbol" => "$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6REU3OTJCODQxNzhBMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REU3OTJCODMxNzhBMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iRDlFRDQ5RDY4NDg3RDhBMTZFQTNCOTM5RjMyNEY1QzIiIHN0UmVmOmRvY3VtZW50SUQ9IkQ5RUQ0OUQ2ODQ4N0Q4QTE2RUEzQjkzOUYzMjRGNUMyIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+MuFAFgAAAwpJREFUeNq0kt1PUnEYx5/fOQdQ4BxeEjLLFBzqQJFCM98oTXPqNMXM1nW3tmxtXbS11nX/QFtr1SzzZc3WWisz06gww3fN0nwrXwYCKno4B4ET/QWcLvheP/vs+3yeBzXU3tTqkro6B8srTKF9bqBv5lxTnv3zDykp1uuTerrt1XV5Ho9vamrFUl/qttnqP7UJAe1DHEQLqq26mZwq3/GxMlKKY9zWNh1gAwlqJYbh7k0Px4UoUorhBO33exlWwgRulWlFQpwJclHRBL1HLy+Ej6YoXU43ISAUCmp0dkOllgdY9s+y25Cdsr7uIaUSKYWPO1ayTOkZVy4DvxAypTRTn9L3ZuREQSZw4eGhnycL9YsLaxJxfI5ZaxuYtJQafTv+mam1gpqSLbuj55AhDkMshkcXUlx4NbL12epjDvtivEhgNGsfPei9eOnU3i7zrneksbnYNjCtTpRpNIntL0YMCvy6YhsDjgEUHd1kvZ1zTPv0cW9lVX5k/s3rIev508P27yQl0WUmdz/rtzYVuzd3J0bncgvS2RC6f+8aTyGoOL8lGAyeqTSNOuZFItxo0j199MF6oYim/f3vxhrOF322zahUyhSNsuv1ZLYcb413EgAsj9ZE0pEDWTmajvb3FZVmDPAX3fbG5pKJyXmSFNfU5Xd1DNZZLV6vd6B/uraxxPXp697KqohAAcTD9amiFoIQmnLTfs39ERBCjeZQ39sxS7nBz/jHh5eKLMbp6UW5XKxOpF69/JZfkNXWdoOvkDJLa5ouaWHOeTQ1ASPQ8uK6TCbBBSJCgO8zAZfTo0k7HA7BpssDFEW4Nu/kUXFCgtdfq1RkQoLky0enLkMdDofWV93HzdrhoTkpKdGlH5wYm80ypno9u84N74ncjG3XyuTdJyIM9nFR9NYBjtvjQI5gl/t3GgmCHQ6kCEIADAckAh8HkbvFI9gCoAAw4Bu00d7JczQCDQHGIDziAkF0IeghiIF/EfiPIJamITYh6KXfsUI/1xtihEZux3is0OFwMFZCXiabY4T+K8AADkw4XY7JKsMAAAAASUVORK5CYII=",
            ],
            [
                "id" => 229,
                "name" => "Uruguay",
                "isoAlpha2" => "UY",
                "isoAlpha3" => "URY",
                "isoNumeric" => 858,
                "currency" => [
                    "code" => "UYU",
                    "name" => "Peso",
                    "symbol" => '$U',

                ],
                "flag" => 'iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpERTc5MkI4NzE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpERTc5MkI4ODE3OEExMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkRFNzkyQjg1MTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkRFNzkyQjg2MTc4QTExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+foYgAAAXJJREFUeNrsls9KAlEUxn93nCadUbISM03BWraofbRq07JV79GL5KZdqx6h1wjaBBkGQVCNWmo1M47jn7xdw4L2ji7qg3M5Bw5897sf53KElPIJyKpwmQ4SKmyhiJ1xgec6CE2jPxB0uwGWGSUet0Jh178TvxNwU74kqnuqEjTqt+SKu6zmN0nEzXCIh0NJq+XQ9yr41RM8547l1DrNh4CXxhs7u3uqS0OI0sSItdHheH1atQsM54ymfU3SUortMv3aKaZu0+tp4Sg25g26gUf98YpCPoKlfDVNQbV6z4IcMJCqR/VJeTRZxSMIITFiabr+B+22j+/6JJJ5Ws0HXpvP4SienxtlGaKZQ4bOOXajghVbwUgekCrsk8umx5crTZY4onQXN7Z5X87xVltHT/sMpWSgrVEoboUyTr/mOOi0CYLO1xwjA3Q9wuJSJgxed0QsmQE0ZgT1psd/TPG/x3/C458PZIpw9fHKk5jy6uN+CjAArpeUjQlCP/MAAAAASUVORK5CYII=',
            ],
            [
                "id" => 230,
                "name" => "Uzbekistan",
                "isoAlpha2" => "UZ",
                "isoAlpha3" => "UZB",
                "isoNumeric" => 860,
                "currency" => [
                    "code" => "UZS",
                    "name" => "Som",
                    "symbol" => "лв",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0NjFFMjIwNDE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0NjFFMjIwNTE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ2MUUyMjAyMTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQ2MUUyMjAzMTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+5O2LbgAAAeRJREFUeNrkkz1rFEEYx3/7fhd3c+EE0XAE0UZrG/E7JGhjZWHnZ0jhd7ARsbS2sRGLFIrfQBBBMIJwigkhyb14+zI7Oz4zFwSxWpHbwgdmZ/YZZn7z/z8zHk9ffgUyAh8mP6Afs33lEvvzgg8HJxAGuGgMF84lTKuaNcktak0WhZxWCqUNeLSKUNqmG81yRsOMvdu3GC9Kdl+/czC3odJLcC+h0g3DJEIbw7AXMVM1ioa2ZAueYUxGqXixfZNrGynXn+2BKEPGiLIt6a2y8aKgEPCJzJWi8rhQ7gB4LeVK+O5rcJbeOL/O97yE45mY3xdo7cBpFBD5HoM4JJGS2D4OPDZEeWChFt4yAnbu7YpLCUXFkWxw9+om87UeH8dHrA9SPIFMKu0U1jKfi+W1OJuLcqt2Ib3D2gO0aCG5WvoUxzx+8563k5w7ly/SDDIOrN1WdRKDrikCuWi2pmGEqRVTyeuqdLm2docPDz/LKrRdWDcNn17t8yX0eZD2hanFYp9K8tZqJWrjs3/bF1q7Gx78RY09M5+cuue0rDS/au728n5L/8vwjDFyk0hZcVjw9Ezx/wPuyOqF+fNyrSDCJ4/um1VDl+/l+VYnVoejdLRytQ7sN3QSPnQHzjqxWtq3LuA/BRgAxkzb8HD3VjsAAAAASUVORK5CYII=",
            ],
            [
                "id" => 231,
                "name" => "Vanuatu",
                "isoAlpha2" => "VU",
                "isoAlpha3" => "VUT",
                "isoNumeric" => 548,
                "currency" => [
                    "code" => "VUV",
                    "name" => "Vatu",
                    "symbol" => "Vt",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0NjFFMjIwODE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo0NjFFMjIwOTE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ2MUUyMjA2MTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQ2MUUyMjA3MTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+q4FzLAAAA2xJREFUeNq0lX9o1HUYx1/f7/2au7vdbdMLl2KzmT+oTUaQVjAhFbYKQ6SRqF3gwLKLCkKNsKyhYv/4h2RETlAQRAZO/+gHVlrLAn/NBvkjHJOam7Rzeafb/fw+Pd+7TpT+kDu3B54P932+3++9n+f9vJ/na1RRMbDTNd3/ckUVaRH6rQRZBBODiTRj2Uqk6wDMpZptZTUs9QY1AUsTSCo8msDEmLl/A/GrZ6GmNcpLiV4WRC/w89gtHnd4mW56cuAyEcCVDdDZBUe3wvkfwdk8TMvYeZ6LXqI3Oco8RzlTTTeWwo9nAmZWj3e3QNVs6FbgX3ZDTzcMNg2x6PY5mqOX6UsnqHf6CJmucUvgTgsTGVj/AZQ9osCnoXcfHDsCF54cYkG8h5XDVxjMpHnC4aPacOYEKOMBbJuvzKByioPw2xB4DEauwZ+H4esjFt/O+4vG2DnWRPuIWRbzNYGgJmCr312C2zMTU/eHl3loW11GygmZm8KSV+M5Wc1WBj7bAc8+pVo4AeHNkOp3E6GGSDDEZKV/TKySqo8tbHDK4HdBeeVFjzxT75Tfjgblp70VBUHnvGEu8sMhRK4hX3ypsYAdd4uPgDjwiVmk5yreGin3T/LAJ7vHaGly030mTU9ngNqWfxiJ3VtL3QzY8T48vwQOfgMDN3QSStg1Tvs48FWSjo98LF6U5ftTaT58bRKWyj2V/v8LN0ZUiAmt1Q+zVCFTkrqFHKUJzO6xNM5xSvt75bJrk1ckHpK9W7z3UD1rBnK4A0n3I8f2II868lRDUN1ftN8RVyGLoROVnLycZXlbLHc9tRp2bYflzTrjOueRCJyJKlFNM6FVea9wQTJb/K6+G9il/2eYJqmUhc3e56rmcCv88Tu8tV7nuk8fb1SZr6lVngMQVc5HtR+mxg2j+B4XLJ2xT4udusnWrYa+i7DiBejq1XC9An6qPieoqab05s08mI2XldLEVdgkH29QKsMQV6W2rYL9JzVYOw3atcIG5fyWVnc1/t8LD/bZzAFv1k218U3dVMPwzuvQcVyD0x7WTGbmAe19WgAcp8+0cf1XYi4P/u3tOp+dGqkOwVoFfPohFY0Ff4+qpqXoHt4XeFMrsu2g/vJOhjfqYGEoP0BDtycE8C5VBwZYV+dncU2+b9e1woz1wD28n/0rwAB0+lOwpXHd2QAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 232,
                "name" => "Vatican",
                "isoAlpha2" => "VA",
                "isoAlpha3" => "VAT",
                "isoNumeric" => 336,
                "currency" => [
                    "code" => "EUR",
                    "name" => "Euro",
                    "symbol" => "€",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MUZDNzdDNzcxNzdDMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MUZDNzdDNzYxNzdDMTFFMjg2N0NBQTkxQkM5RjY5Q0YiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iODY0MDczNUVBM0I5QTUyOTc2RUZFMTAzNDJBQjczRUMiIHN0UmVmOmRvY3VtZW50SUQ9Ijg2NDA3MzVFQTNCOUE1Mjk3NkVGRTEwMzQyQUI3M0VDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+oPgNXQAAAghJREFUeNrkVL9v00AUvvNvO44dbFNBkjYllIWUASbEQAeQEBszjP0D+B9g4p9ghY2VlQEhIdQGRwyIItFUSUOSJnbqn3dnH05IUISEVEdCDLzh3d17T989fXrfg0n/HjizQev12YsZ8NfsP4GmMKN8es7ZhxTCFIIULOIQAoxRduPyQs8R4NSlCfFOByhyOUEq6hWG5X/G4cznhqY06wsmJIzjKHCP0Ml77B8yohlbNwtaVZJlVjiXJJTj8kO7w/bgqIkRUo2yLMaR84mMba5Q5tXK9xC4E5SmqXVhc2N9MzfX7R77rjn8aH9wRrZAfdI1nINt9/hqyhjIt6Ovz5RvjxTn+SqEAMgyUh2yZpCWhrjcr103as2yekAg1hhHO+9I2IdsNC3Mq8ZsQlAKEAIFCcSj/darx5/3WmL1wa2HT6q1dQgmAMgk4Vkmf9coCMPhWxq2sDai3omR4iJ/3yV32oPjfjwCHtZ1zTLXdL2UGzoJulHvTeJ/mRwOQrLlmk/rOyWR7w77nV6HUgBPXZZn8SrQTLHOrt0lndCdXAzINVkkPOdlw6YSL85oUouXNkzLklfhOhMMXDzD8dh++aK3t29uXbmxu6tY5mzw59n8klloMjNBUS7f3qk0tkVNE9XCb5Xcaqtn1hplBMFqNH6plNLlf1eFXtb9n1L51xNd3lT/aF//EGAA9B/iH5xEuF4AAAAASUVORK5CYII=",
            ],
            [
                "id" => 233,
                "name" => "Venezuela",
                "isoAlpha2" => "VE",
                "isoAlpha3" => "VEN",
                "isoNumeric" => 862,
                "currency" => [
                    "code" => "VEF",
                    "name" => "Bolivar",
                    "symbol" => "Bs",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0NjFFMjIwQzE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3ODRDOUJGQTE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ2MUUyMjBBMTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjQ2MUUyMjBCMTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+suiZXgAAATpJREFUeNpi/H5RnIE2gImBZmDUaPoZzcigsJdGRrMQ8hUjw/ufDO9/+VTov33x/cfPv/LKfBvaLzDwsjAIcjAw/AdCEl0t+IGBhZHh02+Gm+9rpzt8/vHnz+9/v379+/HrrxA/2++//2XEOCsT9jGoCjAIsDG85iPFaOF3DF9+yyrxOZuLs7Iyffz2Z9X0KwyinCBPPP8WmqktxMf648ffo+ff3LnxgeGXOCkBwsLE8OkXz/c/Ho5SmblH3j/5yqDEx/AP7HkV/tVzr3MIsM2f5XDl9GuGDz8ZuEgKEOYX5rZSif6KWVUn/v3/z8DDxvD/P7Imhm9/GX7+ndRuvnbv04M7f5PiambBd//57rMIMIuI//vwi4EZQxnrXwZe5kdsQm/+fWZgeEvvxMd4XkR7NKOPGo0bAAQYAF2fdeRLtEqSAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 234,
                "name" => "Vietnam",
                "isoAlpha2" => "VN",
                "isoAlpha3" => "VNM",
                "isoNumeric" => 704,
                "currency" => [
                    "code" => "VND",
                    "name" => "Dong",
                    "symbol" => "₫",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3ODRDOUJGRDE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3ODRDOUJGRTE3OEIxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjc4NEM5QkZCMTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjc4NEM5QkZDMTc4QjExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+MKjEOwAAAg1JREFUeNrMVTtrFVEQnvPae3fvPryPRLmInQn2WkhMIYggGEHs0gqC2FhraSE2grW9VgF/hZWPQmMqCZFAQhTxZu8jZ3fPjLPXRiSsm5iAU83Oznz7zTdzzop37Tk4HpNwbFYXWkjATKAVojYZXTPPTYSZRXbyHSkbdHSsBWRj2b1lZ5YtO/x4dNAlXUgu5+2rVgBB8S+C8BcLKHZLHOZYkGh2XDDnZIj+aTfa1Ebgr1c6phIDa7OmDKRPyaXcn3deH7m2s5TJHkJI7GggDvpnkRNki8geiDWK4ocIL9j+wzGMIVvXqoO4JZlp/97k5LL1zjhIcPtJkL4xzH3aXT3Wokmc/uVpsH47IgPeYianrWBWFngLOYS4cTfaeNxiTO7vIGNEkAE1Ytx65b+f76YvmqLLIRAK2BmueBzcfOmbCFWL9hW6ckOwVCtoF+lA735gj9QsqlPITrpm0m+6lThhgPBwy8drloq4k8/csBDT6K0ZvTYQY++ajXpFMdxX4dp7nRcyvpJ5F+3gub96Pfl4M/n+LDDn7Yklm7m/nJ0qaG5WAbUX851H4eqdhIGkorX7yfaDqL2QG4mUVx7hiku1hA5JJ5R+MiZwKiglwj2wIxWdy3EoioHgwR7meuIyHInJV+nxxJi/mwYb0NRu77NSAQkNFXLr6jGyCOV6wW8QVKr4Z/A//RUcwn4KMAD6MNV+pBmzeAAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 235,
                "name" => "Wallis and Futuna",
                "isoAlpha2" => "WF",
                "isoAlpha3" => "WLF",
                "isoNumeric" => 876,
                "currency" => [
                    "code" => "XPF",
                    "name" => "Franc",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzU5MkY0Q0MxNzhDMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzU5MkY0Q0IxNzhDMTFFMkE3MTQ5QzRBQkZDRDc3NjYiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0iQTQyMEE3MkQyMkZFM0NBMzRDNzNGM0VCOTZCQjUzQ0UiIHN0UmVmOmRvY3VtZW50SUQ9IkE0MjBBNzJEMjJGRTNDQTM0QzczRjNFQjk2QkI1M0NFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+5wahWgAAAsdJREFUeNrEVUtrE1EUPudOMpOkNcmExsRgGyr1USRYUOrKFFTQhXWl0YUoLizWhdS0ttBCpdWuqhY3RRFUFBSh+heUdm9pKbopupPYR5o0j2YmmXu8k1RR6bSlULwMw33M+e53vvudO9jcPDo7m+zrOxaPR3lRS504TTNfjKZDmE5KuQIxhK02BtvWbLSpz/7hTuVng4Rsmg7ZAtcr9O12KBWpUCBuAK4mRPkciCERYAWLSMjEGLpcwJg5bwUt+1VXFmyqRwxKYoMaP9u9y3B7MLUMRpEkBRr2wlyCUwkJyzCEaMdAEObnQOwqK1boOB1tNTj3uBXV6+Ai+Pt3k2MRWWqRGGifZ5Thh96uWzQ/DwYvS4MU3Jkde1+IXZL21IEiowVvpPoQSGwlo2czmsgY/TUk2SRuGGKxvl6KthSGH1S9eeluPfM7ZmVqZunwEbmny/71m/FxglTv2g5ZdNQs2n15X5CFw1gXBmcVygp3OKFk8MQP952+Hc8ep8+2pl+8qgRkxicWmiJKb7d36B5mMlzXLbUun/Va6YgUdN1IJKpjMZ5Mpa9eJr9qqw1nWqKu2z2+wUERZiwvmydpDW3ZSNhA00THfb0NQsH8xSs8n3PeH1E7O8QkL+ogSeuZbwND06p5WeNByiYFkhSJ/L2ytWoUrpJMgOyH8fS+BsfII+fYu+ypkxXd0WYDzrfCGjlJiFRbm5+cTh1vccY71Y6bZhWOjgrd0eV0x84xRTas0W3rCA2hUP7125W2a9X9/Z6BgUq9e9rbWZUzd+E85Z5DIGBytyK30BixwAaQHaXpKezt9g3dFSpzXjIvDiYxwKWnT7QbcXn/ATD0XxfA5qFR2EMn4nC0GZJJphXIhKCKyhQI8E+TmNfA5bAsdEvosrfNdzJJxeIf95zocBS28/lEGQOnLZmvQkdVLW3G6f/8CrYR+qcAAwC0wiFWD6ebGgAAAABJRU5ErkJggg==",
            ],
            [
                "id" => 236,
                "name" => "Western Sahara",
                "isoAlpha2" => "EH",
                "isoAlpha3" => "ESH",
                "isoNumeric" => 732,
                "currency" => [
                    "code" => "MAD",
                    "name" => "Dirham",
                    "symbol" => false,
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozNTkyRjRDRjE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozNTkyRjREMDE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM1OTJGNENEMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM1OTJGNENFMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+f+9yRAAAAhJJREFUeNrElEFIlEEYht8Z/zWyRNiU2EoQIqJdyUjEkxcDD1qEBHaIwFMRRGQH9RIR1LFA8Fi0bODFi4fyWLGnCJMWkgo2aFeWbNEOq8uu/us/vaNjsLm7/cbu3wfPDDMM837zzseHMUDhf5DjMEvavBZ+wyFDvpLzHgpLyXGB+MgUeQhvQutuiX4nCTJKXpBWL4R11JE8mSc95DW5WEvhBmB1ZyFMAp9IPQlrB6RUDFQbWSoby1ifJLeVEpnBQQdra07FgonHFSIR98VVzgr9cpssMbuFmRn53u+XmelpYYwpIheNio/d3cLu6hJIpUqe+RP5t7/Qz9ynk7BtxIaG8KWvD3Y6XXQmMT6OAwMDyCWTeNfRgfTEBJx83l1xuanCTZ1AKgUUCsXJZbPYHwyisLyMnysrWE8kIHy+iveJOYD9A41lD5B1Mx9/9Bgtd0Z2nVl9OYtvw8NoC4dRHzgCq6UZda3H/k3YMf98mDSdPqcOTj1XCAVE2ZtefQDi7H/XLrly0Cq1qY08RAJkMiTw+b5/81koYFW8qffMNi5DRFnaDWahzD+eIBvkbj/wRDfwG6rqDeS3sGNaZ5C8bQJuXQFiZ7lIm5Ymqyts7VjbTI6Sp6eAm1eZiN5Y3Gvt70FYi7abR12/AET6zWLRVFeNQmRp9Xzj9itjndz5URtrdwk/OAl17zKLSpfxEjyLXwIMABpOQfz9h5ChAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 237,
                "name" => "Yemen",
                "isoAlpha2" => "YE",
                "isoAlpha3" => "YEM",
                "isoNumeric" => 887,
                "currency" => [
                    "code" => "YER",
                    "name" => "Rial",
                    "symbol" => "﷼",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozNTkyRjREMzE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozNTkyRjRENDE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjM1OTJGNEQxMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjM1OTJGNEQyMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ACJHhgAAAE1JREFUeNpifK+t+59hAAATwwCBUYtHLaYZYPz///9HIM0HxPTKVoxA/IkFymBAouli+WjiohtgQUpU9Exc/xnpaOFoyTVq8TC3GCDAAFQ1DF5mtiLSAAAAAElFTkSuQmCC",
            ],
            [
                "id" => 238,
                "name" => "Zambia",
                "isoAlpha2" => "ZM",
                "isoAlpha3" => "ZMB",
                "isoNumeric" => 894,
                "currency" => [
                    "code" => "ZMK",
                    "name" => "Kwacha",
                    "symbol" => "ZK",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAIAAAAVyRqTAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4MTY2NjI2QzE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4MTY2NjI2RDE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjgxNjY2MjZBMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgxNjY2MjZCMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+b9h8+AAAAXJJREFUeNrslLtKA0EUhr+ZzW7MbYkmMQoqVhZWilrb+Q4i2PgGPoCVlbVoY20r+AQigoKFlWDnhaAYMCYm0d3s7oyzwScYsBEPpzicA9/8/GdmxOQeFiE0sUMgySa4GgWRxFOI4TR00iKDVSQOhZCy4j5H5EGMH9F3CYa8fEAttkV3XGotDhrgc1CimmehzX6JCcFSl/V3jnK2aH/AY4U9wc4rh02KRaI6Kw3q79QLXPmc1RB2Xkvjr+TFZWTA8gfzfTZiyh+cCk5nufYpCFvVWpAIKgmhx8UUlyHVWx7GOZ7B7LQUpSu1RJsbEjnp0kyRj6gKTuZo5Sgq4oBEEtipNpKfS2yfZzdust1R/Rqrrebnkznkjc1Vdte4b/FliTbpMd/MLN5lGVNvStH5TLsw2WJaMx3RE0gbN0gNbeeMqapdVs++0uJnFBqeQ0emKfm1+Ef/o/8i2v57Gjj0PN139ZfUYgSRpP04Q2/44vvwLcAAr1qGyCgX8xUAAAAASUVORK5CYII=",
            ],
            [
                "id" => 239,
                "name" => "Zimbabwe",
                "isoAlpha2" => "ZW",
                "isoAlpha3" => "ZWE",
                "isoNumeric" => 716,
                "currency" => [
                    "code" => "ZWD",
                    "name" => "Dollar",
                    "symbol" => "Z$",
                ],
                "flag" => "iVBORw0KGgoAAAANSUhEUgAAAB4AAAAUCAYAAACaq43EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4MTY2NjI3MDE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4MTY2NjI3MTE3OEMxMUUyQTcxNDlDNEFCRkNENzc2NiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjgxNjY2MjZFMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjgxNjY2MjZGMTc4QzExRTJBNzE0OUM0QUJGQ0Q3NzY2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+WOc8GAAAAtBJREFUeNq0lmtIk1EYx/+7b7pJ2LxfKv1QqZVBEERfEsKCyi5ghqldmEk1urAuUBGYSRCChMrQkqAsM8IuokVRUeAFBmVhsVa6OWeba7rNy9bedzudN9KK+ra9f3jOC88D7++c55zzPEfQ2NBkO3xLo5LnAzmZcvjGAAjBuwSEyvCkH2U1xfiwcAC5eSK4RqUIBrkov2Av/aoCowQ7daXo9N1ESRngD8gwNUEjIp7Bs47a6mtwWzU4UU4QvYg6LPzA/wFzau924JV+PTQF75G1gzqmqUU49X+Bpz1vIXZFQ2a2YiAnD/pLB5Dtb0TFMhqMkWEyEIJYEGGwj7EjOJiLQAeBVTeGFY6vYOMTcf7cdbir9uMIQkifp4aZJRAREjlwiA6M8SRC1ssY0gJy0UZk9DykETHajGZ0bivA9o/vsCVVCchk4YOD7nHvtzM6lffuY4jzY5BS6YJEIoK92gnv/QVQSGVIqzwKY3E59BoN/M1XkaoUQiSXh0cmLOud6nxAPsVnEstmEGYwmcz0JRH/SAKx6UCMq5PITPdrMiv90+cEsXFcrsMzLtXcD1lqjrYUMv1SSkx31GS4PZ74ekEmnGdJiPxWi+ENgTohbLB4buXUFEvT4DTYwEg89PqEYDUBCUnZP2/RMLX6Q1qMNdThVBS92rGKsDI9B2bNk2BeuKFcIoGSxNEt8CDoFiImvQhdQ3a0FG7FJkMfipIpNUoRuVPNWkfhHzFBvmoxnQ3tEhIp4BHjYs0jWC5ocRAuZCnn4wvLFTLCT+XiZB4H6uprkWg5Bl0GdagV8DDBiFXP/4I7uj7jWWspCtf1YE0JdQS4veC5VjdfuQdL/y6crmDoYeOW/qs/C/gC03HPcS1u2+uwdx/XkaTwOgV/HD8eHgKm3iGUVO1GX2w3cjYAEw4FmAC/LxFB6402W0VTmcq/1oeVy2X4bheCCAn41g8BBgCi8X7r2R04cwAAAABJRU5ErkJggg==",
            ],
        ];

        $allcurrencys = AllCurrency::all();

        foreach ($allcurrencys as $key => $allcurrency) {
            foreach ($jayParsedArys as $key => $jayParsedAry) {
                if ($allcurrency->code == $jayParsedAry['currency']['code']) {
                    AllCurrency::where('code', $allcurrency->code)->update(['flag' => $jayParsedAry['flag']]);
                }
            }
        }

        dd("done");
    }

    public function creat_currency(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(['currency' => 'required|unique:currencies,code'], ['required' => 'Currency is required']);

            //-- add new currency in currenvy table

            $new_currency = AllCurrency::where('code', $request->currency)->first();

            $currency = new Currency;
            $currency->name       = $new_currency->name;
            $currency->code       = $new_currency->code;
            $currency->isObsolete = $new_currency->isObsolete;
            $currency->flag       = $new_currency->flag;
            $currency->save();

            //-- end

            //-- creating possibilty of already added currency with new currency from(old_currency) - to(new_currency)

            $existing_currencies   = CurrencyConversions::groupBy('from')->get(['from']);
            
            foreach ($existing_currencies as $key => $existing_currency) {
                $cc       = new CurrencyConversions;
                $cc->from = $existing_currency->from;
                $cc->to   = $new_currency->code;
                $cc->save();
            }
                
            //-- end

            //-- creating possibilty of already added currency with new currency from(new_currency) - to(old_currency)

            $all_currencies = Currency::all();

            foreach ($all_currencies as $key => $all_currency) {
                $cc       = new CurrencyConversions;
                $cc->from = $currency->code;
                $cc->to   = $all_currency->code;
                $cc->save();
            }

            //-- end


            //-- updating cuurency rate of new added currencies

            $values = CurrencyConversions::whereNull('value')->count();
            $from   = CurrencyConversions::whereNull('value')->pluck('from');
            $to     = CurrencyConversions::whereNull('value')->pluck('to');

            for ($i=0 ; $i<$values; $i++) {
                $url = "https://free.currencyconverterapi.com/api/v6/convert?q=$from[$i]_$to[$i]&compact=ultra&apiKey=9910709386be4f00aa5b";
                $output2 =  json_decode($this->curl_data($url));
                $key = "$from[$i]_$to[$i]";
        
                CurrencyConversions::where('from', "$from[$i]")->where('to', "$to[$i]")->update(['value' => floatval($output2->{$key}) ]);
            }

            //-- end

            return Redirect::route('view-currency')->with('success_message', 'Added Successfully');
        }

        
        return view('currency.create')->with([ 'all_currencies' => AllCurrency::all() ]);
    }

    public function edit_currency(Request $request, $id)
    {
        if ($request->isMethod('post')) {
      
            // $request->validate(['currency' => 'required|unique:currencies,code'], ['required' => 'Currency is required']);

            // dd($request->all());
      
            $currency         = Currency::find($id);
            $currency->status = $request->status;
            $currency->save();

            return Redirect::route('view-currency')->with('success_message', 'Currency Successfully Updated!!');
        }

        return view('currency.edit')->with([ 'currencies' => Currency::all(), 'selected_currency' => Currency::find($id),  ]);
    }

    public function view_currency()
    {
        return view('currency.view')->with([ 'currencies' => Currency::all() ]);
    }

    public function create_user(Request $request)
    {

// $allcurrencys = AllCurrency::all();

        // foreach($allcurrencys as $key => $allcurrency){

//     // dd($allcurrency->code);

//     // dd($jayParsedAry['currency']['code']);

//     // AllCurrency::where('id',$i)->update(['isObsolete' =>  ($currencyData['isObsolete'] == true ? 'true' : 'false' ) ]);

//     foreach($jayParsedArys as $key => $jayParsedAry){

//         if($allcurrency->code == $jayParsedAry['currency']['code']){

//             // dd("sd");

//             // dd($jayParsedAry['flag']);

//             AllCurrency::where('code',$allcurrency->code)->update(['flag' => $jayParsedAry['flag'] ]);

//         }

//         // dd($jayParsedAry['currency']['code']);

//         // AllCurrency::where('id',$i)->update(['isObsolete' =>  ($currencyData['isObsolete'] == true ? 'true' : 'false' ) ]);

//     }

        // }

        // foreach($jayParsedArys as $key => $jayParsedAry){

//     dd($jayParsedAry['currency']['code']);

//     // AllCurrency::where('id',$i)->update(['isObsolete' =>  ($currencyData['isObsolete'] == true ? 'true' : 'false' ) ]);

        // }

        //  dd($jayParsedArys);

        // $new_currency = AllCurrency::find(4);

        // $currency = new Currency;
        // $currency->name = $new_currency->name;
        // $currency->code = $new_currency->code;
        // // $currency->save();

        // $all_currencies = Currency::all();

        // foreach($all_currencies as $key => $all_currency ){

        //     $cc = new CurrencyConversions;
        //     $cc->from = $currency->code;
        //     $cc->to   = $all_currency->code;
        //     // $cc->save();
        // }

        // dd("done");


        if ($request->isMethod('post')) {
            $request->validate([
                'username' => 'required|string',
                'email' => 'required|email|unique:users',
                'role' => 'required',
                'password' => 'required',
                // 'brand'     => 'required',
                // 'currency'  => 'required',
                // 'supervisor'=> 'required|sometimes',
            ]);

            $user = new User;
            $user->name = $request->username;
            $user->role_id = (int) $request->role;
            $user->email = $request->email;
            $user->supervisor_id = $request->supervisor ?? null;
            $user->brand_name = $request->brand ?? null;
            $user->currency_id = $request->currency ?? null;
            $user->password = bcrypt($request->password);
            $user->save();

            return Redirect::route('view-user')->with('success_message', 'Created Successfully');
        } else {
            $branch = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output = $this->curl_data($url);

                return json_decode($output);
            });

            $data['roles'] = role::all();
            $data['supervisors'] = User::where('role_id', 5)->orderBy('name', 'ASC')->get();
            $data['currencies'] = Currency::get();
            $data['all_currencies'] = AllCurrency::get();
            $data['brands'] = $branch;
            return view('user.create_user', $data);
            // return view('user.create_user')->with(['name' => '', 'id' => '', 'roles' => role::all(), 'supervisors' => User::where('role_id',5)->orderBy('name','ASC')->get() ]);
        }
    }

    public function view_user(Request $request)
    {
        $data['data'] = user::get();
        return view('user.view_user', $data);
    }

    public function update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'username' => 'required|string',
                'role' => 'required',
                // 'brand'     => 'required',
                // 'currency'  => 'required',
                // 'supervisor'=> 'required|sometimes',
            ]);

            $user->name = $request->username;
            $user->role_id = (int) $request->role;
            $user->email = $request->email;
            $user->supervisor_id = $request->supervisor ?? null;
            $user->brand_name = $request->brand ?? null;
            $user->currency_id = $request->currency ?? null;
            if ($request->has('password') && $request->password != null) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            return Redirect::route('view-user')->with('success_message', 'Update Successfully');
        } else {
            $branch = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output = $this->curl_data($url);
                return json_decode($output);
            });
            $data['data'] = $user;
            $data['roles'] = role::all();
            $data['supervisors'] = User::where('role_id', 5)->orderBy('name', 'ASC')->get();
            $data['currencies'] = Currency::get();
            $data['brands'] = $branch;

            return view('user.update_user', $data);
        }
    }
    public function delete_user($id)
    {
        // if (booking::where('user_id', $id)->count() == 1) {
        //     return Redirect::route('view-user')->with('error_message', 'You can not delete this user because user already in use');
        // }
        user::destroy('id', '=', $id);
        return Redirect::route('view-user')->with('success_message', 'Delete Successfully');
    }

    // CRUD related to seasson
    public function create_season(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required|unique:seasons']);
            $this->validate($request, ['start_date' => 'required']);
            $this->validate($request, ['end_date' => 'required']);

            if ($request->end_date < $request->start_date) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'end_date' => ['End date should be greater start date.'],
                ]);
            }

            if ($request->set_default_season == 1) {
                season::query()->update(['default_season' => 0]);
            }

            $season = new season;
            $season->name = $request->name;
            $season->default_season = $request->set_default_season;
            $season->start_date = Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d');
            $season->end_date = Carbon::parse(str_replace('/', '-', $request->end_date))->format('Y-m-d');
            $season->save();

            return Redirect::route('view-season')->with('success_message', 'Created Successfully');
        } else {
            return view('season.create_season')->with(['name' => '', 'id' => '']);
        }
    }

    public function view_season(Request $request)
    {
        return view('season.view_season')->with('data', season::all());
    }
    public function update_season(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required|unique:seasons,name,' . $id]);
            $this->validate($request, ['start_date' => 'required']);
            $this->validate($request, ['end_date' => 'required']);

            if ($request->end_date < $request->start_date) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'end_date' => ['End date should be greater start date.'],
                ]);
            }

            if ($request->set_default_season == 1) {
                season::query()->update(['default_season' => 0]);
            }

            season::where('id', $id)->update(array(
                'name' => $request->name,
                'default_season' => $request->set_default_season,
                'start_date' => Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d'),
                'end_date' => Carbon::parse(str_replace('/', '-', $request->end_date))->format('Y-m-d'),
            ));

            return Redirect::route('view-season')->with('success_message', 'Update Successfully');
        } else {
            return view('season.update_season')->with(['data' => season::find($id), 'id' => $id]);
        }
    }
    public function delete_season($id)
    {
        if (booking::where('season_id', $id)->count() >= 1) {
            return Redirect::route('view-season')->with('error_message', 'You can not delete this record because season already in use');
        }
        season::destroy('id', '=', $id);
        return Redirect::route('view-season')->with('success_message', 'Delete Successfully');
    }
    //
    public function create_supervisor(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);
            $this->validate($request, ['email' => 'required|email|unique:supervisors']);
            supervisor::create(array(
                'name' => $request->name,
                'email' => $request->email,
            ));
            return Redirect::route('create-supervisor')->with('success_message', 'Created Successfully');
        } else {
            return view('supervisor.create_supervisor')->with(['name' => '', 'id' => '', 'email' => '']);
        }
    }
    public function view_supervisor(Request $request)
    {
        return view('supervisor.view_supervisor')->with('data', supervisor::all());
    }
    public function update_supervisor(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:100|unique:supervisors,email,' . $id,
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            supervisor::where('id', '=', $id)->update(
                array(
                    'email' => $request->email,
                    'name' => $request->name,
                )
            );
            return Redirect::route('view-supervisor')->with('success_message', 'Update Successfully');
        } else {
            return view('supervisor.update_supervisor')->with(['data' => supervisor::find($id), 'id' => $id]);
        }
    }
    public function delete_supervisor($id)
    {
        if (User::where('supervisor_id', $id)->count() == 1) {
            return Redirect::route('view-supervisor')->with('error_message', 'You can not delete this record because supervisor already in use');
        }
        supervisor::destroy('id', '=', $id);
        return Redirect::route('view-supervisor')->with('success_message', 'Delete Successfully');
    }
    //
    public function create_booking(Request $request)
    {
        if ($request->isMethod('post')) {

            // $this->validate($request, ['supplier'                     => 'required'], ['required' => 'Please select Supplier']);
            // $this->validate($request, ['ref_no'                     => 'required'], ['required' => 'Reference number is required']);
            // $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
            // $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
            // $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
            // $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
            // $this->validate($request, ['date_of_travel'             => 'required'], ['required' => 'Please select date of travel']);
            // $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);

            // $this->validate($request, ['fb_airline_name_id'         => 'required_if:flight_booked,yes'], ['required_if' => 'Please select flight airline name']);

            // $this->validate($request, ['fb_payment_method_id'       => 'required_if:flight_booked,yes'], ['required_if' => 'Please select payment method']);

            // $this->validate($request, ['fb_booking_date'            => 'required_if:flight_booked,yes'], ['required_if' => 'Please select booking date']);

            // $this->validate($request, ['fb_airline_ref_no'          => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter airline reference number']);

            // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            // //
            // // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']);
            // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            // //
            // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            // // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // //
            // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            // // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
            // // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']);

            // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            // // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
            // $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'], ['required_if' => 'Plesse enter itinerary sent date']);

            // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
            // $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
            // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);
            // $this->validate($request, ['tdp_current_date'              => 'required_if:document_prepare,yes'], ['required_if' => 'Plesse enter Travel Document Prepared Date']);

            if ($request->form_received_on == '0000-00-00') {
                $form_received_on = null;
            } else {
                $form_received_on = $request->form_received_on;
            }
            //
            if ($request->app_login_date == '0000-00-00') {
                $app_login_date = null;
            } else {
                $app_login_date = $request->app_login_date;
            }
            //
            $booking_id = booking::create(array(
                'ref_no' => $request->ref_no,
                'brand_name' => $request->brand_name,
                'season_id' => $request->season_id,
                'agency_booking' => $request->agency_booking,
                'pax_no' => $request->pax_no,
                'date_of_travel' => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
                'flight_booked' => $request->flight_booked,
                'fb_airline_name_id' => $request->fb_airline_name_id,
                'fb_payment_method_id' => $request->fb_payment_method_id,
                'fb_booking_date' => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
                'fb_airline_ref_no' => $request->fb_airline_ref_no,
                'fb_last_date' => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
                'fb_person' => $request->fb_person,
                //
                'aft_last_date' => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
                'aft_person' => $request->aft_person,
                'ds_last_date' => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
                'ds_person' => $request->ds_person,
                'to_last_date' => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
                'to_person' => $request->to_person,
                //
                'document_prepare' => $request->document_prepare,
                'dp_last_date' => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
                'dp_person' => $request->dp_person,
                //
                //
                'flight_booking_details' => $request->flight_booking_details,
                'asked_for_transfer_details' => $request->asked_for_transfer_details,
                'transfer_details' => $request->transfer_details,
                'form_sent_on' => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
                'form_received_on' => $form_received_on,
                'app_login_date' => $app_login_date,
                // 'transfer_info_received'      => $request->transfer_info_received,
                // 'transfer_info_details'       => $request->transfer_info_details,
                'itinerary_finalised' => $request->itinerary_finalised,
                'itinerary_finalised_details' => $request->itinerary_finalised_details,
                'itf_last_date' => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
                'itf_person' => $request->itf_person,
                'documents_sent' => $request->documents_sent,
                'documents_sent_details' => $request->documents_sent_details,
                'electronic_copy_sent' => $request->electronic_copy_sent,
                'electronic_copy_details' => $request->electronic_copy_details,
                'transfer_organised' => $request->transfer_organised,
                'transfer_organised_details' => $request->transfer_organised_details,

                'sale_person' => $request->sale_person,
                'deposit_received' => $request->deposit_received == '' ? 0 : $request->deposit_received,
                'remaining_amount_received' => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
                'fso_person' => $request->fso_person,
                'fso_last_date' => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
                'aps_person' => $request->aps_person,
                'aps_last_date' => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
                'finance_detail' => $request->finance_detail,
                'destination' => $request->destination,
                'user_id' => Auth::user()->id,
                'itf_current_date' => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
                'tdp_current_date' => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
                'tds_current_date' => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),
                // 'holiday'                     => $request->holiday,

            ));

            if ($request->flight_booked == 'yes') {
                //Sending email
                $template = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;
                $template .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
                $template .= '<h1>Last Date Of Flight Booking : ' . $request->fb_last_date . '</h1>';

                if ($request->fb_person == '') {
                    $email = Auth::user()->email;
                    $template .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
                } else {
                    $record = User::where('id', $request->fb_person)->get()->first();
                    $email = $record->email;
                    $name = $record->name;
                    $template .= '<h1>Responsible Person : ' . $name . '</h1>';
                }
                $data['to'] = $email;
                $data['name'] = config('app.name');
                $data['from'] = config('app.mail');
                $data['subject'] = "Task Flight Booked Alert";
                try {
                    \Mail::send("email_template.flight_booked_alert", ['template' => $template], function ($m) use ($data) {
                        $m->from($data['from'], $data['name']);
                        $m->to($data['to'])->subject($data['subject']);
                    });
                } catch (Swift_RfcComplianceException $e) {
                    return $e->getMessage();
                }
                //Sending email
            }
            if ($request->form_received_on == '0000-00-00') {
                //Sending email
                $template = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

                $template .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
                $template .= '<h1>Reminder for sent on date : ' . $request->fso_last_date . '</h1>';

                if ($request->fso_person == '') {
                    $email = Auth::user()->email;
                    $template .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
                } else {
                    $record = User::where('id', $request->fso_person)->get()->first();
                    $email = $record->email;
                    $name = $record->name;
                    $template .= '<h1>Responsible Person : ' . $name . '</h1>';
                }
                $data['to'] = $email;
                $data['name'] = config('app.name');
                $data['from'] = config('app.mail');
                $data['subject'] = "Reminder for form sent on";
                try {
                    \Mail::send("email_template.form_sent_on", ['template' => $template], function ($m) use ($data) {
                        $m->from($data['from'], $data['name']);
                        $m->to($data['to'])->subject($data['subject']);
                    });
                } catch (Swift_RfcComplianceException $e) {
                    return $e->getMessage();
                }
                //Sending email
            }

            if ($request->electronic_copy_sent == 'no') {
                //Sending email
                $template = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

                $template .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
                $template .= '<h1>App Reminder Sent Date : ' . $request->aps_last_date . '</h1>';

                if ($request->aps_person == '') {
                    $email = Auth::user()->email;
                    $template .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
                } else {
                    $record = User::where('id', $request->aps_person)->get()->first();
                    $email = $record->email;
                    $name = $record->name;
                    $template .= '<h1>Responsible Person : ' . $name . '</h1>';
                }
                $data['to'] = $email;
                $data['name'] = config('app.name');
                $data['from'] = config('app.mail');
                $data['subject'] = "Reminder for app login sent";
                try {
                    \Mail::send("email_template.app_login_sent", ['template' => $template], function ($m) use ($data) {
                        $m->from($data['from'], $data['name']);
                        $m->to($data['to'])->subject($data['subject']);
                    });
                } catch (Swift_RfcComplianceException $e) {
                    return $e->getMessage();
                }
                //Sending email
            }

            return Redirect::route('create-booking')->with('success_message', 'Created Successfully');
        } else {
            $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
                $url = 'http://localhost/unforgettable_payment/backend/api/payment/get_ref';
                $output = $this->curl_data($url);
                //   return json_decode($output)->data;
            });

            $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
                $output = $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
                $url = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_holiday_type';
                $output = $this->curl_data($url);
                return json_decode($output);
            });

            $booking_email = booking_email::where('booking_id', '=', 1)->get();
            return view('booking.create_booking')->with([
                'get_holiday_type' => $get_holiday_type,
                'seasons' => season::all(),
                'persons' => user::all(),
                'get_refs' => $get_ref,
                'get_user_branches' => $get_user_branches,
                'booking_email' => $booking_email,
                'payment' => payment::all(),
                'airline' => airline::all(),
            ]);
        }
    }

    public function view_booking_season(Request $request)
    {
        $group_by_seasons = season::join('codes', 'codes.season_id', '=', 'seasons.id')->orderBy('seasons.created_at', 'desc')->groupBy('seasons.id', 'seasons.name')->get(['seasons.id', 'seasons.name']);
        return view('booking.view_booking_season')->with('data', $group_by_seasons);
    }

    public function delete_booking_season($id)
    {
        season::destroy('id', '=', $id);
        return Redirect::route('view-booking-season')->with('success_message', 'Deleted Successfully');
    }

    public function view_booking(Request $request, $id)
    {
        //
        $staff = Cache::remember('staff', $this->cacheTimeOut, function () {
            return User::orderBy('id', 'DESC')->get();
        });
        //
        $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
            $url = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
            $output = $this->curl_data($url);
            return json_decode($output)->data;
        });
        //
        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output);
        });
        //
        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });
        $query = old_booking::join('seasons', 'seasons.id', '=', 'old_bookings.season_id')
            ->join('users', 'users.id', '=', 'old_bookings.user_id')
            ->leftjoin('users as user_fb', 'user_fb.id', '=', 'old_bookings.fb_person')
            ->leftjoin('users as user_ti', 'user_ti.id', '=', 'old_bookings.aft_person')
            ->leftjoin('users as user_to', 'user_to.id', '=', 'old_bookings.to_person')
            ->leftjoin('users as user_itf', 'user_itf.id', '=', 'old_bookings.itf_person')
            ->leftjoin('users as user_tdp', 'user_tdp.id', '=', 'old_bookings.dp_person')
            ->leftjoin('users as user_ds', 'user_ds.id', '=', 'old_bookings.ds_person')
            ->leftjoin('airlines', 'airlines.id', '=', 'old_bookings.fb_airline_name_id')
            ->leftjoin('payments', 'payments.id', '=', 'old_bookings.fb_payment_method_id')->where('old_bookings.season_id', '=', $id);

        if ($request->created_at != '') {
            $date = explode('-', $request->created_at);
            $start_date = $date[0];
            $end_date = $date[1];

            $start_created_at = Carbon::parse($start_date)->format('Y-m-d');
            $end_created_at = Carbon::parse($end_date)->format('Y-m-d');
            $query = $query->whereRaw('DATE(old_bookings.created_at) >= ?', $start_created_at);
            $query = $query->whereRaw('DATE(old_bookings.created_at) <= ?', $end_created_at);
        }
        if ($request->created_by != '') {
            $query = $query->where('old_bookings.user_id', '=', $request->created_by);
        }
        if ($request->ref_no != '') {
            $query = $query->where('old_bookings.ref_no', '=', $request->ref_no);
        }
        if ($request->date_of_travel != '') {
            $date = explode('-', $request->date_of_travel);
            $start_date = $date[0];
            $end_date = $date[1];

            $query = $query->where('old_bookings.date_of_travel', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query = $query->where('old_bookings.date_of_travel', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if ($request->brand_name != '') {
            $query = $query->where('old_bookings.brand_name', '=', $request->brand_name);
        }
        if ($request->season_id != '') {
            $query = $query->where('old_bookings.season_id', '=', $request->season_id);
        }
        if ($request->agency_booking != '') {
            $query = $query->where('old_bookings.agency_booking', '=', $request->agency_booking);
        }
        if ($request->flight_booked != '') {
            $query = $query->where('old_bookings.flight_booked', '=', $request->flight_booked);
        }
        if ($request->form_sent_on != '') {
            $date = explode('-', $request->form_sent_on);
            $start_date = $date[0];
            $end_date = $date[1];
            $query = $query->where('old_bookings.form_sent_on', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query = $query->where('old_bookings.form_sent_on', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if ($request->type_of_holidays != '') {
            $query = $query->where('old_bookings.type_of_holidays', '=', $request->type_of_holidays);
        }
        if ($request->fb_payment_method_id != '') {
            $query = $query->where('old_bookings.fb_payment_method_id', '=', $request->fb_payment_method_id);
        }
        if ($request->fb_airline_name_id != '') {
            $query = $query->where('old_bookings.fb_airline_name_id', '=', $request->fb_airline_name_id);
        }
        if ($request->fb_responsible_person != '') {
            $query = $query->where('old_bookings.fb_person', '=', $request->fb_responsible_person);
        }
        if ($request->ti_responsible_person != '') {
            $query = $query->where('old_bookings.aft_person', '=', $request->ti_responsible_person);
        }
        if ($request->to_responsible_person != '') {
            $query = $query->where('old_bookings.to_person', '=', $request->to_responsible_person);
        }
        if ($request->itf_responsible_person != '') {
            $query = $query->where('old_bookings.itf_person', '=', $request->itf_responsible_person);
        }
        if ($request->dp_responsible_person != '') {
            $query = $query->where('old_bookings.dp_person', '=', $request->dp_responsible_person);
        }
        if ($request->ds_responsible_person != '') {
            $query = $query->where('old_bookings.ds_person', '=', $request->ds_responsible_person);
        }
        if ($request->pax_no != '') {
            $query = $query->where('old_bookings.pax_no', '=', $request->pax_no);
        }
        if ($request->asked_for_transfer_details != '') {
            $query = $query->where('old_bookings.asked_for_transfer_details', '=', $request->asked_for_transfer_details);
        }
        if ($request->transfer_organised != '') {
            $query = $query->where('old_bookings.transfer_organised', '=', $request->transfer_organised);
        }
        if ($request->itinerary_finalised != '') {
            $query = $query->where('old_bookings.itinerary_finalised', '=', $request->itinerary_finalised);
        }
        $query = $query->orderBy('old_bookings.created_at', 'desc')->paginate(10, ['old_bookings.*', 'airlines.name as airline_name', 'payments.name as payment_name', 'seasons.name', 'users.name as username', 'user_fb.name as fbusername', 'user_ti.name as tiusername', 'user_to.name as tousername', 'user_itf.name as itfusername', 'user_tdp.name as tdpusername', 'user_ds.name as dsusername'])->appends($request->all());

        return view('booking.view_booking')->with([
            'data' => $query,
            'book_id' => $id,
            'staffs' => $staff,
            'get_refs' => $get_ref,
            'get_holiday_type' => $get_holiday_type,
            'type_of_holidays' => $request->type_of_holidays,
            'get_user_branches' => $get_user_branches,
            'created_at' => $request->created_at,
            'created_by' => $request->created_by,
            'ref_no' => $request->ref_no,
            'date_of_travel' => $request->date_of_travel,
            'brand_name' => $request->brand_name,
            'seasons' => season::all(),
            'session_id' => $request->season_id,
            'agency_booking' => $request->agency_booking,
            'flight_booked' => $request->flight_booked,
            'form_sent_on' => $request->form_sent_on,
            'payment' => payment::all(), 'airline' => airline::all(),
            'fb_payment_method_id' => $request->fb_payment_method_id,
            'fb_airline_name_id' => $request->fb_airline_name_id,
            'fb_responsible_person' => $request->fb_responsible_person,
            'ti_responsible_person' => $request->ti_responsible_person,
            'to_responsible_person' => $request->to_responsible_person,
            'itf_responsible_person' => $request->itf_responsible_person,
            'dp_responsible_person' => $request->dp_responsible_person,
            'ds_responsible_person' => $request->ds_responsible_person,
            'pax_no' => $request->pax_no, 'asked_for_transfer_details' => $request->asked_for_transfer_details,
            'transfer_organised' => $request->transfer_organised,
            'itinerary_finalised' => $request->itinerary_finalised,
        ]);
    }
    public function delete_booking($season_id, $booking_id)
    {
        booking::destroy('id', '=', $booking_id);
        return Redirect::route('view-booking', $season_id)->with('success_message', 'Deleted Successfully');
    }

    public function cf_remote_request($url, $_args = array())
    {
        // prepare array
        $array = array(
            //'status' => false,
            'message' => array(
                '101' => 'Invalid url',
                '102' => 'cURL Error #: ',
                '200' => 'cURL Successful #: ',
                '400' => '400 Bad Request',
            ),
        );

        // initalize args
        $args = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'ssl' => true,
            'headers' => array(),
            'body' => array(),
            'returntransfer' => true,
            'encoding' => '',
            'maxredirs' => 10,
            'format' => 'JSON',
        );

        if (empty($url)) {
            $code = 101;
            $response = array('status' => $code, 'body' => $array['message'][$code]);
            return $response;
        }

        if (!empty($_args) && is_array($_args)) {
            $args = array_merge($args, $_args);
        }

        $fields = $args['body'];
        if (strtolower($args['method']) == 'post' && is_array($fields)) {
            $fields = http_build_query($fields);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => $args['returntransfer'],
            CURLOPT_ENCODING => $args['encoding'],
            CURLOPT_MAXREDIRS => $args['maxredirs'],
            CURLOPT_HTTP_VERSION => $args['httpversion'], // CURL_HTTP_VERSION_1_1,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            //CURLOPT_HEADER             => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_TIMEOUT => $args['timeout'],
            CURLOPT_CONNECTTIMEOUT => $args['timeout'],
            CURLOPT_SSL_VERIFYPEER => $args['ssl'] === true ? true : false,
            //CURLOPT_SSL_VERIFYHOST     => $args['ssl'] === true ? true : false,
            // CURLOPT_CAPATH             => APPPATH . 'certificates/ca-bundle.crt',
            CURLOPT_CUSTOMREQUEST => $args['method'],
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => $args['headers'],
        ));

        $curl_response = curl_exec($curl);
        $err = curl_error($curl);
        $curl_info = array(
            'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            'header' => curl_getinfo($curl, CURLINFO_HEADER_OUT),
            'total_time' => curl_getinfo($curl, CURLINFO_TOTAL_TIME),
        );

        curl_close($curl);

        if ($err) {
            $response = array('message' => $err, 'body' => $err);
        } else {
            if ($curl_info['status'] == 200
                && in_array($args['format'], array('ARRAY', 'OBJECT'))
                && !empty($curl_response) && is_string($curl_response)) {
                $curl_response = json_decode($curl_response, $args['format'] == 'ARRAY' ? true : false);
                $curl_response = (json_last_error() == JSON_ERROR_NONE) ? $curl_response : $curl_response;
            } else {
                $curl_response = json_decode($curl_response, true);
            }

            $response = array(
                //'message'     => $array['message'][ $curl_info['status'] ],
                'body' => $curl_response,
            );
        }

        $response = array_merge($curl_info, $response);
        return $response;
    }

    public function refresh_token()
    {
        $zoho_credentials = ZohoCredential::findOrFail(1);
        $refresh_token = $zoho_credentials->refresh_token;
        $url = "https://accounts.zoho.com/oauth/v2/token?refresh_token=" . $refresh_token . "&client_id=1000.0VJP33J6LLOQ63896U88RWYIVJRSFD&client_secret=81212149f53ee4039b280b420835d64b8443c96a83&grant_type=refresh_token";
        $args = array('ssl' => false, 'format' => 'ARRAY');
        $response = $this->cf_remote_request($url, $args);
        if ($response['status'] == 200) {
            $body = $response['body'];
            $zoho_credentials->access_token = $body['access_token'];
            $zoho_credentials->save();
        }
    }

    // get reference function start
    public function get_ref_detail(Request $request)
    {
        $ajax_response = array();

        if ($request->reference_name == "zoho") {
            $zoho_credentials = ZohoCredential::findOrFail(1);
            $ref = $request->id;
            // $refresh_token = '1000.18cb2e5fbe397a6422d8fcece9b67a06.d71539ff6e5fa8364879574343ab799a';
            $url = "https://www.zohoapis.com/crm/v2/Deals/search?criteria=(Booking_Reference:equals:{$ref})";
            $args = array(
                'method' => 'GET',
                'ssl' => false,
                'format' => 'ARRAY',
                'headers' => array(
                    "Authorization:" . 'Zoho-oauthtoken ' . $zoho_credentials->access_token,
                    "Content-Type: application/json",
                ),
            );

            $response = $this->cf_remote_request($url, $args);
            if ($response['status'] == 200) {
                $responses_data = array_shift($response['body']['data']);
                $passenger_id = $responses_data['id'];

                $url = "https://www.zohoapis.com/crm/v2/Passengers/search?criteria=(Deal:equals:{$passenger_id})";
                $passenger_response = $this->cf_remote_request($url, $args);

                if ($passenger_response['status'] == 200) {
                    $pax_no = count($passenger_response['body']['data']);
                }

                $ajax_response = array(
                    "holiday_type" => isset($responses_data['Holiday_Type']) && !empty($responses_data['Holiday_Type']) ? $responses_data['Holiday_Type'] : null,
                    "sale_person" => isset($responses_data['Owner']['email']) && !empty($responses_data['Owner']['email']) ? $responses_data['Owner']['email'] : null,
                    "currency" => isset($responses_data['Currency']) && !empty($responses_data['Currency']) ? $responses_data['Currency'] : null,
                    "pax" => isset($pax_no) && !empty($pax_no) ? $pax_no : null,
                );
            }
        }

        if ($request->ajax()) {
            return response()->json($ajax_response);
        }
        return redirect()->back();
    }

    //get reference funtion end
    private function curl_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $output = curl_exec($ch);
    }

    public function update_booking(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            // old code start

            // $this->validate($request, ['ref_no'                     => 'required'], ['required' => 'Reference number is required']);
            // $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
            // $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
            // $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
            // $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
            // $this->validate($request, ['date_of_travel'             => 'required'], ['required' => 'Please select date of travel']);
            // $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);
            // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'], ['required_if' => 'Please select booked person']);
            // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            // //
            // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'], ['required_if' => 'Please select document person']);
            // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // //
            // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            // $this->validate($request, ['transfer_info_received'     => 'required'], ['required' => 'Please select transfer info received']);
            // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'], ['required_if' => 'Please transfer info detail']);
            // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
            // $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
            // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);

            // if ($request->form_received_on == '0000-00-00') {
            //     $form_received_on = NULL;
            // } elseif ($request->form_received_on == '') {
            //     $form_received_on = NULL;
            // } else {
            //     $form_received_on = $request->form_received_on;
            // }

            // if ($request->app_login_date == '0000-00-00') {
            //     $app_login_date = NULL;
            // } elseif ($request->app_login_date == '') {
            //     $app_login_date = NULL;
            // } else {
            //     $app_login_date = $request->app_login_date;
            // }

            // booking::where('id', '=', $id)->update(array(
            //     'ref_no'                      => $request->ref_no,
            //     'brand_name'                  => $request->brand_name,
            //     'season_id'                   => $request->season_id,
            //     'agency_booking'              => $request->agency_booking,
            //     'pax_no'                      => $request->pax_no,
            //     'date_of_travel'              => Carbon::parse($request->date_of_travel)->format('Y-m-d'),
            //     'flight_booked'               => $request->flight_booked,
            //     'flight_booking_details'      => $request->flight_booking_details,
            //     'asked_for_transfer_details'  => $request->asked_for_transfer_details,
            //     'transfer_details'            => $request->transfer_details,
            //     'form_sent_on'                => Carbon::parse($request->form_sent_on)->format('Y-m-d'),
            //     'form_received_on'            => $form_received_on,
            //     'app_login_date'              => $app_login_date,
            //     'transfer_info_received'      => $request->transfer_info_received,
            //     'transfer_info_details'       => $request->transfer_info_details,
            //     'itinerary_finalised'         => $request->itinerary_finalised,
            //     'itinerary_finalised_details' => $request->itinerary_finalised_details,
            //     'documents_sent'              => $request->documents_sent,
            //     'documents_sent_details'      => $request->documents_sent_details,
            //     'electronic_copy_sent'        => $request->electronic_copy_sent,
            //     'electronic_copy_details'     => $request->electronic_copy_details,
            //     'transfer_organised'          => $request->transfer_organised,
            //     'transfer_organised_details'  => $request->transfer_organised_details,
            //     'type_of_holidays'            => $request->type_of_holidays,
            //     'sale_person'                 => $request->sale_person,
            //     'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
            //     'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
            //     'finance_detail'              => $request->finance_detail,
            //     'destination'                 => $request->destination
            // ));

            // old code end

            $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name' => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency' => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no' => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, ["booking_due_date" => "required|array", "booking_due_date.*" => "required"]);
            $this->validate($request, ["cost" => "required|array", "cost.*" => "required"]);
            $this->validate($request, ['fb_airline_name_id' => 'required_if:flight_booked,yes'], ['required_if' => 'Airline is required']);
            $this->validate($request, ['fb_payment_method_id' => 'required_if:flight_booked,yes'], ['required_if' => 'Payment is required']);
            $this->validate($request, ['fb_booking_date' => 'required_if:flight_booked,yes'], ['required_if' => 'Booking Date is required']);
            $this->validate($request, ['fb_airline_ref_no' => 'required_if:flight_booked,yes'], ['required_if' => 'Airline Ref No is required']);
            $this->validate($request, ['flight_booking_details' => 'required_if:flight_booked,yes'], ['required_if' => 'Flight Booking Details is required']);
            $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Transfer Organised Details is required']);

            $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Itinerary Finalised Details is required']);
            $this->validate($request, ['itf_current_date' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Itinerary Finalised Date is required']);
            $this->validate($request, ['tdp_current_date' => 'required_if:document_prepare,yes'], ['required_if' => 'Travel Document Prepared Date is required']);

            $this->validate($request, ['documents_sent_details' => 'required_if:documents_sent,yes'], ['required_if' => 'Document Details is required']);
            $this->validate($request, ['tds_current_date' => 'required_if:documents_sent,yes'], ['required_if' => 'Travel Document Sent Date is required']);

            $this->validate($request, ['aps_person' => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Responsible Person is required']);
            $this->validate($request, ['aps_last_date' => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Date is required']);
            $this->validate($request, ['electronic_copy_details' => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'App Login Sent Details is required']);

            $season = season::find($request->season_id);

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            // }

            // $errors = [];
            // foreach ($request->booking_due_date as $key => $duedate) {
            //     $duedate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));

            //     $startDate = date('Y-m-d', strtotime($season->start_date));
            //     $endDate   = date('Y-m-d', strtotime($season->end_date));

            //     $bookingdate     = (isset($request->booking_date) && !empty($request->booking_date[$key]))? $request->booking_date[$key] : NULL;
            //     if($bookingdate != NULL){
            //         $bookingdate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
            //     }
            //     $dateofservice   = (isset($request->date_of_service) && !empty($request->date_of_service[$key]))? $request->date_of_service[$key] : NULL;
            //     if ($dateofservice != null) {
            //         $dateofservice   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
            //     }
            //     $error = [];
            //     $dueresult = false;
            //     $dofresult = false;
            //     $bookresult = false;

            //     if($this->checkInSession($duedate, $season) == false){
            //         $a[$key+1] = 'Due Date should be season date range.';
            //     }else{
            //         $dueresult = true;
            //     }
            //     if($bookingdate != NULL && $this->checkInSession($bookingdate, $season) == false){
            //         $b[$key+1]  = 'Booking Date should be season date range.';
            //     }else{
            //         $bookresult = true;
            //     }
            //     if($dateofservice != NULL && $this->checkInSession($dateofservice, $season) == false){
            //         $c[$key+1]  = 'Date of service should be season date range.';
            //     }else{
            //         $dofresult = true;
            //     }

            //     if($dateofservice != NULL && $bookingdate  == NULL){
            //         $b[$key+1]  = 'Booking Date field is required before the date of service.';
            //         $bookresult = false;
            //     }

            //     if($bookresult == true){
            //         if($bookingdate != null && $bookingdate < $duedate){
            //             $b[$key+1]  = 'Booking Date should be smaller than booking due date.';
            //         }
            //     }

            //     if($dofresult == true){
            //         if ($bookingdate != null && $bookingdate > $dateofservice) {
            //             $c[$key+1]  = 'Date of service should be smaller than booking date.';
            //         }
            //     }

            //     $error['date_of_service'] = (isset($c) && count($c) >0 )? (object) $c : NULL;
            //     $error['booking_date'] = (isset($b) && count($b) >0 )? (object) $b : NULL;
            //     $error['booking_due_date'] = (isset($a) && count($a) >0 )? (object) $a : NULL;

            //     $errors = $error;
            // }

            // if(count($errors) > 0){
            //   if($error['date_of_service'] != NULL || $error['date_of_service'] != NULL || $error['date_of_service'] != NULL){
            //     throw \Illuminate\Validation\ValidationException::withMessages($errors);
            //     }
            // }

            $booking = Booking::find($id);

            $booking_log = new BookingLog;
            $bookingDetailLogNumber = $this->increment_log_no($this->get_log_no('BookingLog', $id));
            $booking_log->booking_id = $booking->id;
            $booking_log->log_no = $bookingDetailLogNumber;
            $booking_log->reference_name = $booking->reference_name;
            $booking_log->ref_no = $booking->ref_no;
            $booking_log->qoute_id = $booking->qoute_id;
            $booking_log->quotation_no = $booking->quotation_no;
            $booking_log->dinning_preferences = $booking->dinning_preferences;
            $booking_log->lead_passenger_name = $booking->lead_passenger_name;
            $booking_log->brand_name = $booking->brand_name;
            $booking_log->type_of_holidays = $booking->type_of_holidays;
            $booking_log->sale_person = $booking->sale_person;
            $booking_log->season_id = $booking->season_id;
            $booking_log->agency_booking = $booking->agency_booking;
            $booking_log->agency_name = $booking->agency_name;
            $booking_log->agency_contact_no = $booking->agency_contact_no;
            $booking_log->currency = $booking->currency;
            $booking_log->convert_currency = $booking->convert_currency;
            $booking_log->group_no = $booking->group_no;
            $booking_log->net_price = $booking->net_price;
            $booking_log->markup_amount = $booking->markup_amount;
            $booking_log->selling = $booking->selling;
            $booking_log->gross_profit = $booking->gross_profit;
            $booking_log->markup_percent = $booking->markup_percent;
            $booking_log->show_convert_currency = $booking->show_convert_currency;
            $booking_log->per_person = $booking->per_person;
            $booking_log->created_date = date("Y-m-d");
            $booking_log->user_id = Auth::user()->id;
            $booking_log->pax_name = $booking->pax_name;
            $booking_log->save();

            $booking = Booking::updateOrCreate(
                ['quotation_no' => $request->quotation_no],
                [
                    'ref_no' => $request->ref_no,
                    'reference_name' => $request->reference,
                    'qoute_id' => $request->qoute_id,
                    'quotation_no' => $request->quotation_no,
                    'dinning_preferences' => $request->dinning_preferences,
                    'lead_passenger_name' => $request->lead_passenger_name,
                    'brand_name' => $request->brand_name,
                    'type_of_holidays' => $request->type_of_holidays,
                    'sale_person' => $request->sale_person,
                    'season_id' => $request->season_id,
                    'agency_booking' => $request->agency_booking,
                    'agency_name' => $request->agency_name,
                    'agency_contact_no' => $request->agency_contact_no,
                    'currency' => $request->currency,
                    'convert_currency' => $request->convert_currency,
                    'group_no' => $request->group_no,
                    'net_price' => $request->net_price,
                    'markup_amount' => $request->markup_amount,
                    'selling' => $request->selling,
                    'gross_profit' => $request->gross_profit,
                    'markup_percent' => $request->markup_percent,
                    'show_convert_currency' => $request->show_convert_currency,
                    'per_person' => $request->per_person,

                    'flight_booked' => !empty($request->flight_booked) ? $request->flight_booked : null,
                    'fb_person' => !empty($request->fb_person) && ($request->flight_booked != 'NA') ? $request->fb_person : null,
                    'fb_last_date' => $request->fb_last_date && ($request->flight_booked != 'NA') ? Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d') : null,
                    'fb_airline_name_id' => !empty($request->fb_airline_name_id) && ($request->flight_booked == 'yes') ? $request->fb_airline_name_id : null,
                    'fb_payment_method_id' => !empty($request->fb_payment_method_id) && ($request->flight_booked == 'yes') ? $request->fb_payment_method_id : null,
                    'fb_booking_date' => $request->fb_booking_date && ($request->flight_booked == 'yes') ? Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d') : null,
                    'fb_airline_ref_no' => !empty($request->fb_airline_ref_no) && ($request->flight_booked == 'yes') ? $request->fb_airline_ref_no : null,
                    'flight_booking_details' => !empty($request->flight_booking_details) && ($request->flight_booked == 'yes') ? $request->flight_booking_details : null,

                    'asked_for_transfer_details' => $request->asked_for_transfer_details,
                    'aft_person' => $request->aft_person && ($request->asked_for_transfer_details != 'NA') ? $request->aft_person : null,
                    'aft_last_date' => $request->aft_last_date && ($request->asked_for_transfer_details != 'NA') ? Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d') : null,
                    'transfer_details' => $request->transfer_details && ($request->asked_for_transfer_details == 'yes') ? $request->transfer_details : null,

                    'transfer_organised' => $request->transfer_organised,
                    'to_person' => $request->to_person && ($request->transfer_organised != 'NA') ? $request->to_person : null,
                    'to_last_date' => $request->to_last_date && ($request->transfer_organised != 'NA') ? Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d') : null,
                    'transfer_organised_details' => $request->transfer_organised_details && ($request->transfer_organised == 'yes') ? $request->transfer_organised_details : null,

                    'itinerary_finalised' => $request->itinerary_finalised,
                    'itf_person' => $request->itf_person && ($request->itinerary_finalised != 'NA') ? $request->itf_person : null,
                    'itf_last_date' => $request->itf_last_date && ($request->itinerary_finalised != 'NA') ? Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d') : null,
                    'itinerary_finalised_details' => $request->itinerary_finalised_details && ($request->itinerary_finalised == 'yes') ? $request->itinerary_finalised_details : null,
                    'itf_current_date' => $request->itf_current_date && ($request->itinerary_finalised == 'yes') ? Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d') : null,

                    'document_prepare' => $request->document_prepare,
                    'dp_person' => $request->dp_person && ($request->document_prepare != 'NA') ? $request->dp_person : null,
                    'dp_last_date' => $request->dp_last_date && ($request->document_prepare != 'NA') ? Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d') : null,
                    'tdp_current_date' => $request->tdp_current_date && ($request->document_prepare == 'yes') ? Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d') : null,

                    'documents_sent' => $request->documents_sent,
                    'ds_person' => $request->ds_person && ($request->documents_sent != 'NA') ? $request->ds_person : null,
                    'ds_last_date' => $request->ds_last_date && ($request->documents_sent != 'NA') ? Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d') : null,
                    'documents_sent_details' => $request->documents_sent_details && ($request->documents_sent == 'yes') ? $request->documents_sent_details : null,
                    'tds_current_date' => $request->tds_current_date && ($request->documents_sent == 'yes') ? Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d') : null,

                    'electronic_copy_sent' => $request->electronic_copy_sent,
                    'aps_person' => $request->aps_person && ($request->electronic_copy_sent == 'yes') ? $request->aps_person : null,
                    'aps_last_date' => $request->aps_last_date && ($request->electronic_copy_sent == 'yes') ? Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d') : null,
                    'electronic_copy_details' => $request->electronic_copy_details && ($request->electronic_copy_sent == 'yes') ? $request->electronic_copy_details : null,
                ]
            );

            $bookingDetails = BookingDetail::where('booking_id', $booking->id)->get();

            foreach ($bookingDetails as $key => $bookingDetail) {
                $bookingDetailLog = new BookingDetailLog;
                $bookingDetailLog->booking_id = $booking->id;
                $bookingDetailLog->log_no = $bookingDetailLogNumber;
                $bookingDetailLog->qoute_id = $bookingDetail->qoute_id;
                $bookingDetailLog->quotation_no = $bookingDetail->quotation_no;
                $bookingDetailLog->row = $key + 1;
                $bookingDetailLog->date_of_service = $bookingDetail->date_of_service ? Carbon::parse(str_replace('/', '-', $bookingDetail->date_of_service))->format('Y-m-d') : null;
                $bookingDetailLog->service_details = $bookingDetail->service_details;
                $bookingDetailLog->category_id = $bookingDetail->category;
                $bookingDetailLog->supplier = $bookingDetail->supplier;
                $bookingDetailLog->product = $bookingDetail->product;
                $bookingDetailLog->booking_date = $bookingDetail->booking_date ? Carbon::parse(str_replace('/', '-', $bookingDetail->booking_date))->format('Y-m-d') : null;
                $bookingDetailLog->booking_due_date = $bookingDetail->booking_due_date ? Carbon::parse(str_replace('/', '-', $bookingDetail->booking_due_date))->format('Y-m-d') : null;
                $bookingDetailLog->booked_by = $bookingDetail->booked_by;
                $bookingDetailLog->booking_refrence = $bookingDetail->booking_refrence;
                $bookingDetailLog->booking_type = $bookingDetail->booking_type;
                $bookingDetailLog->comments = $bookingDetail->comments;
                $bookingDetailLog->supplier_currency = $bookingDetail->supplier_currency;
                $bookingDetailLog->cost = $bookingDetail->cost;
                $bookingDetailLog->actual_cost = $bookingDetail->actual_cost;
                $bookingDetailLog->supervisor_id = $bookingDetail->supervisor;
                $bookingDetailLog->added_in_sage = $bookingDetail->added_in_sage;
                $bookingDetailLog->qoute_base_currency = $bookingDetail->qoute_base_currency;
                $bookingDetailLog->qoute_invoice = $bookingDetail->qoute_invoice;
                $bookingDetailLog->save();

                $financebookingDetails = FinanceBookingDetail::where('booking_detail_id', $bookingDetail->id)->get();

                // dd($financebookingDetails);

                foreach ($financebookingDetails as $financebookingDetail) {
                    $financeBookingDetailLog = new FinanceBookingDetailLog;

                    $financeBookingDetailLog->booking_detail_id = $bookingDetailLog->id;
                    $financeBookingDetailLog->log_no = $bookingDetailLogNumber;
                    $financeBookingDetailLog->row = $key + 1;
                    $financeBookingDetailLog->deposit_amount = !empty($financebookingDetail->deposit_amount) ? $financebookingDetail->deposit_amount : null;
                    $financeBookingDetailLog->deposit_due_date = $financebookingDetail->deposit_due_date ? Carbon::parse(str_replace('/', '-', $financebookingDetail->deposit_due_date))->format('Y-m-d') : null;
                    $financeBookingDetailLog->paid_date = $financebookingDetail->paid_date ? Carbon::parse(str_replace('/', '-', $financebookingDetail->deposit_due_date))->format('Y-m-d') : null;
                    $financeBookingDetailLog->payment_method = $financebookingDetail->payment_method ?? null;
                    $financeBookingDetailLog->upload_to_calender = $financebookingDetail->upload_calender;
                    $financeBookingDetailLog->additional_date = $financebookingDetail->additional_date;
                    $financeBookingDetailLog->save();
                }
            }

            if (!empty($request->actual_cost)) {
                foreach ($request->actual_cost as $key => $cost) {
                    if (!is_null($request->qoute_invoice)) {
                        if (array_key_exists($key, $request->qoute_invoice)) {
                            $oldFileName = $request->qoute_invoice_record[$key];

                            $file = $request->qoute_invoice[$key];
                            $newFile = $request->qoute_invoice[$key]->getClientOriginalName();
                            $name = pathinfo($newFile, PATHINFO_FILENAME);
                            $extension = pathinfo($newFile, PATHINFO_EXTENSION);
                            $filename = $name . '-' . rand(pow(10, 4 - 1), pow(10, 4) - 1) . '.' . $extension;

                            $folder = public_path('booking/' . $request->qoute_id);

                            if (!File::exists($folder)) {
                                File::makeDirectory($folder, 0775, true, true);
                            }

                            // $destinationPath = public_path('booking/'. $request->qoute_id .'/'.  $oldFileName);
                            // File::delete($destinationPath);

                            $file->move(public_path('booking/' . $request->qoute_id), $filename);
                        } else {
                            $filename = isset($request->qoute_invoice_record[$key]) ? $request->qoute_invoice_record[$key] : null;
                        }
                    } else {
                        $filename = isset($request->qoute_invoice_record[$key]) ? $request->qoute_invoice_record[$key] : null;
                    }

                    $arrayBookingDetail = [
                        'qoute_id' => $request->qoute_id,
                        'booking_id' => $booking->id,
                        'quotation_no' => $request->quotation_no,
                        'row' => $key + 1,
                        'date_of_service' => $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null,
                        'service_details' => $request->service_details[$key],
                        'category_id' => $request->category[$key],
                        'supplier' => $request->supplier[$key],
                        'product' => $request->product[$key],
                        'booking_date' => $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null,
                        'booking_due_date' => $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null,
                        // 'booking_method'    => $request->booking_method[$key],
                        'booked_by' => $request->booked_by[$key],
                        'booking_refrence' => $request->booking_refrence[$key],
                        'booking_type' => $request->booking_type[$key],
                        'comments' => $request->comments[$key],
                        'cost' => $request->cost[$key],
                        'actual_cost' => $request->actual_cost[$key],
                        'supervisor_id' => $request->supervisor[$key],
                        'added_in_sage' => $request->added_in_sage[$key],
                        'qoute_base_currency' => $request->qoute_base_currency[$key],
                        'qoute_invoice' => $filename,
                    ];

                    if ($request->has('supplier_currency') && !empty($request->supplier_currency)) {
                        $arrayBookingDetail['supplier_currency'] = $request->supplier_currency[$key];
                    }

                    $bookingDetail = BookingDetail::updateOrCreate(
                        [
                            'quotation_no' => $request->quotation_no,
                            'row' => $key + 1,
                        ],
                        $arrayBookingDetail
                    );
                    $nowDate = Carbon::now()->toDateString();
                    foreach ($request->deposit_due_date[$key] as $ikey => $deposit_due_date) {
                        if ($request->upload_calender[$key][$ikey] == true && $deposit_due_date != null) {
                            $supplier = ($request->has('supplier_currency')) ? $request->supplier_currency[$key] : $bookingDetail->supplier_currency;
                            $event = new Event;
                            $event->name = "To Pay " . $request->deposit_amount[$key][$ikey] . ' ' . $supplier . " to Supplier";
                            $event->description = 'Event description';

                            $addDate = (int) $request->additional_date[$key][$ikey];

                            if (Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate)->toDateString() >= $nowDate && $addDate != 0) {
                                $event->startDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate) : null;
                                $event->endDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate) : null;
                            } else {
                                $event->startDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->startOfDay() : null;
                                $event->endDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->endOfDay() : null;
                            }
                            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
                            // $event->save();
                        }

                        FinanceBookingDetail::updateOrCreate(
                            [
                                'booking_detail_id' => $bookingDetail->id,
                                'row' => $ikey + 1,
                            ],
                            [
                                'upload_to_calender' => $request->upload_calender[$key][$ikey],
                                'deposit_amount' => !empty($request->deposit_amount[$key][$ikey]) ? $request->deposit_amount[$key][$ikey] : null,
                                'deposit_due_date' => $request->deposit_due_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'paid_date' => $request->paid_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'payment_method' => $request->payment_method[$key][$ikey] ?? null,
                                'additional_date' => $request->additional_date[$key][$ikey] ?? null,
                            ]
                        );
                    }
                }
            }

            return response()->json(['success_message' => 'Booking Updated Successfully']);

        // return Redirect::route('update-booking', $id)->with('success_message', 'Updated Successfully');
        } else {
            $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output = $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
                $output = $this->curl_data($url);
                return json_decode($output);
            });

            // $get_ref = Cache::remember('get_ref', 60, function () {
            //     // $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
            //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
            //     $output =  $this->curl_data($url);
            //     return json_decode($output)->data;
            // });

            // $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            //     // $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
            //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            //     $output =  $this->curl_data($url);
            //     return json_decode($output);
            // });

            // $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            //     // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            //     $output =  $this->curl_data($url);
            //     return json_decode($output);
            // });

            return view('booking.update_booking')->with([

                'booking' => Booking::where('id', '=', $id)->first(),
                'booking_email' => booking_email::where('booking_id', '=', $id)->get(),
                'users' => user::all(),
                'seasons' => season::all(),
                // 'get_refs'          => $get_ref,
                'get_user_branches' => $get_user_branches,
                'record' => old_booking::where('id', '=', $id)->get()->first(),
                'currencies' => Currency::all()->sortBy('name'),
                'get_holiday_type' => $get_holiday_type,
                'booking_details' => BookingDetail::where('booking_id', $id)->get(),
                'categories' => Category::all()->sortBy('name'),
                'suppliers' => Supplier::all()->sortBy('name'),
                'users' => User::all()->sortBy('name'),
                'booking_methods' => BookingMethod::all()->sortBy('id'),
                'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
                'payment_method' => payment::all()->sortBy('name'),
                'id' => $id,
                'booking_logs' => BookingLog::where('booking_id', $id)->orderBy('log_no', 'DESC')->get(),
                'airlines' => airline::all(),
                'payments' => payment::all(),
                'products' => Product::all()->sortBy('name'),
            ]);
        }
    }

    public function view_booking_version($booking_id, $log_no)
    {
        $booking_log = BookingLog::where('booking_id', $booking_id)->where('log_no', $log_no)->first();
        $booking_detail_logs = BookingDetailLog::where('booking_id', $booking_id)->where('log_no', $log_no)->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        return view('booking.view-booking-version')->with([
            'booking_log' => $booking_log,
            'booking_detail_logs' => $booking_detail_logs,
            'seasons' => season::all(),
            'currencies' => Currency::all()->sortBy('name'),
            'categories' => Category::all()->sortBy('name'),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'payment_method' => payment::all()->sortBy('name'),
            'products' => Product::all()->sortBy('name'),
        ]);
    }

    // view quotation version in update booking
    public function view_quotation_version($quote_id, $log_no)
    {
        $qoute_log = QouteLog::where('qoute_id', $quote_id)->where('log_no', $log_no)->first();

        $qoute_detail_logs = QouteDetailLog::where('qoute_id', $quote_id)->where('log_no', $log_no)->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        return view('booking.view-booking-quotation-version')->with([
            'qoute_log' => $qoute_log,
            'qoute_detail_logs' => $qoute_detail_logs,
            'seasons' => season::all(),
            'currencies' => Currency::all()->sortBy('name'),
            'categories' => Category::all()->sortBy('name'),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'products' => Product::all()->sortBy('name'),
        ]);
    }

    public function view_quotation($id)
    {
        return view('booking.view-quotation')->with([

            'qoute' => Qoute::findOrFail($id),
            'qoute_details' => QouteDetail::where('qoute_id', $id)->get(),
            'seasons' => season::all(),
            'currencies' => Currency::all()->sortBy('name'),
            'categories' => Category::all()->sortBy('name'),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function delete_multi_booking(Request $request, $id)
    {
        $customMessages = ['required' => 'Please select at least one checkbox'];
        $this->validate($request, ['multi_val' => 'required'], $customMessages);
        foreach ($request->multi_val as $val) {
            booking::destroy('id', '=', $val);
        }
        return Redirect::route('view-booking', $id)->with('success_message', 'Action Perform Successfully');
    }

    public function create_airline(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required'], ['required' => 'Name is required.']);

            airline::create(array(
                'name' => $request->name,

            ));
            return Redirect::route('view-airline')->with('success_message', 'Created Successfully');
        } else {
            return view('airline.create_airline')->with(['name' => '', 'id' => '']);
        }
    }
    public function view_airline(Request $request)
    {
        return view('airline.view_airline')->with('data', airline::all());
    }

    public function update_airline(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), ['name' => 'required'], ['required' => 'Name is required.']);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            airline::where('id', '=', $id)->update(
                array(

                    'name' => $request->name,
                )
            );
            return Redirect::route('view-airline')->with('success_message', 'Update Successfully');
        } else {
            return view('airline.update_airline')->with(['data' => airline::find($id), 'id' => $id]);
        }
    }

    public function delete_airline($id)
    {
        // if (booking::where('fb_airline_name_id', $id)->count() >= 1) {
        //     return Redirect::route('view-airline')->with('error_message', 'You can not delete this record because season already in use');
        // }
        airline::destroy('id', '=', $id);
        return Redirect::route('view-airline')->with('success_message', 'Deleted Successfully');
    }
    public function create_payment(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required'], ['required' => 'Name is required']);

            payment::create(array(
                'name' => $request->name,

            ));
            return Redirect::route('view-payment')->with('success_message', 'Created Successfully');
        } else {
            return view('payment.create_payment')->with(['name' => '', 'id' => '', 'email' => '']);
        }
    }
    public function view_payment(Request $request)
    {
        return view('payment.view_payment')->with('data', payment::all()->sortByDesc("id"));
    }

    public function update_payment(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), ['name' => 'required'], ['required' => 'Name is required.']);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            payment::where('id', '=', $id)->update(
                array(
                    'name' => $request->name,
                )
            );
            return Redirect::route('view-payment')->with('success_message', 'Update Successfully');
        } else {
            return view('payment.update_payment')->with(['data' => payment::find($id), 'id' => $id]);
        }
    }

    public function delete_payment($id)
    {
        // if (booking::where('fb_payment_method_id', $id)->count() >= 1) {
        //     return Redirect::route('view-payment')->with('error_message', 'You can not delete this record because season already in use');
        // }
        payment::destroy('id', '=', $id);
        return Redirect::route('view-payment')->with('success_message', 'Deleted Successfully');
    }

    public function add_role(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required'], ['required' => 'Name is required']);

            role::create(array(
                'name' => $request->name,
            ));

            return Redirect::route('view-role')->with('success_message', 'Created Successfully');
        }
        return view('roles.create');
    }

    public function view_roles(Request $request)
    {
        return view('roles.view_roles')->with(['data' => role::all()]);
    }

    public function del_role(Request $request, $id)
    {
        role::destroy('id', '=', $id);
        return Redirect::route('view-role')->with('success_message', 'Deleted Successfully');
    }

    public function update_role(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required'], ['required' => 'Name is required']);

            role::where('id', '=', $id)->update(array(
                'name' => $request->name,
            ));

            return Redirect::route('view-role')->with('success_message', 'Update Successfully');
        }
        return view('roles.update_role')->with(['data' => role::find($id)]);
    }

    public function add_category(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required|unique:categories']);

            Category::create(array(
                'name' => $request->name,
            ));
            return Redirect::route('view-category')->with('success_message', 'Category Added Successfully');
        }
        return view('category.add_category');
    }

    public function view_category(Request $request)
    {
        return view('category.view_categories')->with(['data' => Category::all()]);
    }
    public function delete_category(Request $request, $id)
    {
        Category::destroy('id', '=', $id);
        return Redirect::route('view-category')->with('success_message', 'Category Successfully Deleted!!');
    }
    public function update_category(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);

            Category::where('id', '=', $id)->update(array(
                'name' => $request->name,
            ));

            return Redirect::route('view-category')->with('success_message', 'Category Successfully Updated!!');
        }
        return view('category.update_category')->with(['data' => Category::find($id)]);
    }

    public function details_supplier($id)
    {
        $supplier = Supplier::findOrFail(decrypt($id));
        $data = [
            'name' => $supplier->name,
            'email' => $supplier->email,
            'phone' => $supplier->phone,
            'currency' => $supplier->currency->name ?? null,
        ];

        $category = [];
        foreach ($supplier->categories as $categoires) {
            $c = [
                'name' => $categoires->name,
            ];
            array_push($category, $c);
        }

        $product = [];
        foreach ($supplier->products as $pro) {
            $p = [
                'name' => $pro->name,
            ];
            array_push($product, $p);
        }

        $data['category'] = $category;
        $data['product'] = $product;

        return view('supplier.detail_supplier', $data);
    }

    public function add_supplier(Request $request)
    {
        if ($request->isMethod('post')) {

            // dd($request->all());

            $this->validate($request, ['username' => 'required'], ['required' => 'Name is required']);
            // $this->validate($request, ['email' => 'required|unique:suppliers'], ['required' => 'Email is required']);
            // $this->validate($request, ['phone' => 'required|unique:suppliers'], ['required' => 'Phone Number is required']);
            $this->validate($request, ['categories' => 'required'], ['required' => 'Category is required']);
            // $this->validate($request, ['products' => 'required'], ['required' => 'Product is required']);
            // $this->validate($request, ['currency' => 'required'], ['required' => 'Currency is required']);

            $supplier = new Supplier();
            $supplier->name = $request->username;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->currency_id = $request->currency;
            $supplier->save();

            $supplier->categories()->sync($request->categories);
            $supplier->products()->sync($request->products);

            // if (!empty($request->categories)) {
            //     foreach ($request->categories as $category) {
            //         $cat = new supplier_category();
            //         $cat->supplier_id = $supplier->id;
            //         $cat->category_id = $category;
            //         $cat->save();
            //     }
            // }

            // if (!empty($request->products)) {
            //     foreach ($request->products as $product) {
            //         $prod = new supplier_product();
            //         $prod->supplier_id = $supplier->id;
            //         $prod->product_id = $product;
            //         $prod->save();
            //     }
            // }

            return Redirect::route('view-supplier')->with('success_message', 'Supplier Added Successfully');
        }

        $categories = Category::all();
        $products = Product::all();
        $currencies = Currency::all();

        return view('supplier.create_supplier')->with(['categories' => $categories, 'products' => $products, 'currencies' => $currencies]);
    }

    public function view_supplier(Request $request)
    {
        $suppliers = Supplier::all();
        return view('supplier.view_suppliers')->with('suppliers', $suppliers);

        // $set = [];
        // $cat = DB::select('select suppliers.* , categories.name as category from supplier_categories INNER JOIN suppliers ON suppliers.id = supplier_categories.supplier_id INNER JOIN categories ON supplier_categories.category_id = categories.id');
        // // var_dump($cat);
        // foreach ($cat as $c) {
        //     if (empty($set[0][$c->id])) {
        //         $set[0][$c->id] = [];
        //     }
        //     array_push($set[0][$c->id], $c->category);
        // }

        // $prod_set = [];
        // $prod = DB::select('select suppliers.* , products.name as product from supplier_products INNER JOIN suppliers ON suppliers.id = supplier_products.supplier_id INNER JOIN products ON supplier_products.product_id = products.id');
        // // var_dump($cat);
        // foreach ($prod as $c) {
        //     if (empty($prod_set[0][$c->id])) {
        //         $prod_set[0][$c->id] = [];
        //     }
        //     array_push($prod_set[0][$c->id], $c->product);
        // }

        // return view('supplier.view_suppliers')->with(['data' => DB::select('select suppliers.* , categories.name as category from supplier_categories INNER JOIN suppliers ON suppliers.id = supplier_categories.supplier_id INNER JOIN categories ON supplier_categories.category_id = categories.id GROUP BY suppliers.id'), 'categories' => $set, 'prod' => $prod_set]);
    }

    public function view_supplier_products()
    {
        $supplier_products = supplier_product::join('suppliers', 'suppliers.id', '=', 'supplier_products.supplier_id')
            ->leftJoin('products', 'products.id', '=', 'supplier_products.product_id')
            ->select('suppliers.id as supplier_id', 'suppliers.name as supplier_name', 'products.id as product_id', 'products.code', 'products.name', 'products.description')
            ->get();

        return view('supplier.view_supplier_product')->with('supplier_products', $supplier_products);
    }

    public function view_supplier_categories()
    {
        $supplier_categories = supplier_category::join('suppliers', 'suppliers.id', '=', 'supplier_categories.supplier_id')
            ->leftJoin('categories', 'categories.id', '=', 'supplier_categories.category_id')
            ->select('suppliers.id as supplier_id', 'suppliers.name as supplier_name', 'categories.id as category_id', 'categories.name as category_name')
            ->get();

        return view('supplier.view_supplier_category')->with('supplier_categories', $supplier_categories);
    }

    public function delete_supplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail(decrypt($id));
        $supplier->delete();
        return Redirect::route('view-supplier')->with('success_message', 'Supplier Successfully Deleted!!');
    }

    public function update_supplier(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['username' => 'required'], ['required' => 'Name is required']);
            // $this->validate($request, ['email' => 'required|email|unique:suppliers,email,'.$id], ['required' => 'Email is required']);
            // $this->validate($request, ['phone' => 'required|unique:suppliers,phone,'.$id, ], ['required' => 'Phone Number is required']);
            $this->validate($request, ['categories' => 'required'], ['required' => 'Product is required']);
            // $this->validate($request, ['products' => 'required'], ['required' => 'Currency is required']);

            $supplier = Supplier::findOrFail($id);
            $supplier->name = $request->username;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->currency_id = $request->currency;
            $supplier->save();

            $supplier->categories()->sync($request->categories);
            $supplier->products()->sync($request->products);

            // supplier_product::where('supplier_id', $id)->delete();
            // supplier_category::where('supplier_id', $id)->delete();

            // Supplier::where('id', '=', $id)->update(array(
            //     'name' => $request->username,
            //     'email' => $request->email,
            //     'phone' => $request->phone
            // ));

            // if (!empty($request->categories)) {
            //     foreach ($request->categories as $category) {
            //         $cat = new supplier_category();
            //         $cat->supplier_id = $id;
            //         $cat->category_id = $category;
            //         $cat->save();
            //     }
            // }
            // if (!empty($request->products)) {
            //     foreach ($request->products as $product) {
            //         $prod = new supplier_product();
            //         $prod->supplier_id = $id;
            //         $prod->product_id = $product;
            //         $prod->save();
            //     }
            // }

            return Redirect::route('view-supplier')->with('success_message', 'Supplier Successfully Updated!!');
        }

        // $categories = DB::select('SELECT category_id as category FROM supplier_categories WHERE supplier_id = ' . $id);
        // $cat = [];
        // foreach ($categories as $value) {
        //     array_push($cat, $value->category);
        // }
        // $products = DB::select('SELECT product_id as product FROM supplier_products WHERE supplier_id = ' . $id);
        // $prod = [];
        // foreach ($products as $value) {
        //     array_push($prod, $value->product);
        // }

        $supplier = Supplier::find($id);
        $categories = Category::all();
        $products = Product::all();
        $currencies = Currency::all();

        $supplier_category = supplier_category::where('supplier_id', $id)->pluck('category_id')->toArray();
        $supplier_product = supplier_product::where('supplier_id', $id)->pluck('product_id')->toArray();

        return view('supplier.update_supplier')->with(['supplier' => $supplier, 'categories' => $categories, 'products' => $products, 'currencies' => $currencies, 'supplier_category' => $supplier_category, 'supplier_product' => $supplier_product]);
    }

    public function add_product(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['code' => 'required|unique:products']);
            $this->validate($request, ['name' => 'required']);
            $this->validate($request, ['description' => 'required']);

            Product::create(array(
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ));
            return Redirect::route('view-product')->with('success_message', 'Product Added Successfully');
        }
        return view('product.add_product');
    }

    public function view_product(Request $request)
    {
        return view('product.view_products')->with(['data' => Product::all()]);
    }
    public function delete_product(Request $request, $id)
    {
        Product::destroy('id', '=', $id);
        return Redirect::route('view-product')->with('success_message', 'Product Successfully Deleted!!');
    }
    public function update_product(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);
            $this->validate($request, ['code' => 'required']);

            Product::where('id', '=', $id)->update(array(
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ));

            return Redirect::route('view-product')->with('success_message', 'Product Successfully Updated!!');
        }
        return view('product.update_product')->with(['data' => Product::find($id)]);
    }

    public function create_code(Request $request)
    {
        if ($request->isMethod('post')) {
            $season = season::find($request->season_id);
            $start_date = $season->start_date;
            $end_date = $season->end_date;

            $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['category' => 'required'], ['required' => 'Please select Category']);
            $this->validate($request, ['product' => 'required'], ['required' => 'Please select Product']);
            $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Please select Agency']);
            $this->validate($request, ['pax_no' => 'required'], ['required' => 'Please select PAX No']);
            $this->validate($request, ['supplier' => 'required'], ['required' => 'Please select Supplier']);
            $this->validate($request, ['date_of_travel' => 'required'], ['required' => 'Please select date of travel']);

            if ($request->date_of_travel) {
                if ($request->date_of_travel < $start_date || $request->date_of_travel > $end_date) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'date_of_travel' => ['Wrong Date Selected'],
                    ]);
                }
            }

            // $this->validate($request, ['fb_airline_name_id'         => 'required_if:flight_booked,yes'], ['required_if' => 'Please select flight airline name']);

            // $this->validate($request, ['fb_payment_method_id'       => 'required_if:flight_booked,yes'], ['required_if' => 'Please select payment method']);

            // $this->validate($request, ['fb_booking_date'            => 'required_if:flight_booked,yes'], ['required_if' => 'Please select booking date']);

            // $this->validate($request, ['fb_airline_ref_no'          => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter airline reference number']);

            // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            // //
            // // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']);
            // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            // //
            // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            // // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // //
            // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            // // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
            // // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']);

            // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            // // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
            // $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'], ['required_if' => 'Plesse enter itinerary sent date']);

            // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);

            // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);
            // $this->validate($request, ['tdp_current_date'              => 'required_if:document_prepare,yes'], ['required_if' => 'Plesse enter Travel Document Prepared Date']);

            if ($request->form_received_on == '0000-00-00') {
                $form_received_on = null;
            } else {
                $form_received_on = $request->form_received_on;
            }
            //
            if ($request->app_login_date == '0000-00-00') {
                $app_login_date = null;
            } else {
                $app_login_date = $request->app_login_date;
            }
            //
            $booking_id = code::create(array(
                'ref_no' => $request->ref_no,
                'brand_name' => $request->brand_name,
                'season_id' => $request->season_id,
                'agency_booking' => $request->agency_booking,
                'pax_no' => $request->pax_no,
                'date_of_travel' => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
                'category' => $request->category,
                'supplier' => $request->supplier,
                'product' => $request->product,
                'flight_booked' => $request->flight_booked,
                'fb_airline_name_id' => $request->fb_airline_name_id,
                'fb_payment_method_id' => $request->fb_payment_method_id,
                'fb_booking_date' => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
                'fb_airline_ref_no' => $request->fb_airline_ref_no,
                'fb_last_date' => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
                'fb_person' => $request->fb_person,
                //
                'aft_last_date' => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
                'aft_person' => $request->aft_person,
                'ds_last_date' => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
                'ds_person' => $request->ds_person,
                'to_last_date' => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
                'to_person' => $request->to_person,
                //
                'document_prepare' => $request->document_prepare,
                'dp_last_date' => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
                'dp_person' => $request->dp_person,
                //
                //
                'flight_booking_details' => $request->flight_booking_details,
                'asked_for_transfer_details' => $request->asked_for_transfer_details,
                'transfer_details' => $request->transfer_details,
                'form_sent_on' => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
                'form_received_on' => $form_received_on,
                'app_login_date' => $app_login_date,
                // 'transfer_info_received'      => $request->transfer_info_received,
                // 'transfer_info_details'       => $request->transfer_info_details,
                'itinerary_finalised' => $request->itinerary_finalised,
                'itinerary_finalised_details' => $request->itinerary_finalised_details,
                'itf_last_date' => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
                'itf_person' => $request->itf_person,
                'documents_sent' => $request->documents_sent,
                'documents_sent_details' => $request->documents_sent_details,
                'electronic_copy_sent' => $request->electronic_copy_sent,
                'electronic_copy_details' => $request->electronic_copy_details,
                'transfer_organised' => $request->transfer_organised,
                'transfer_organised_details' => $request->transfer_organised_details,
                'type_of_holidays' => $request->type_of_holidays,
                'sale_person' => $request->sale_person,
                'deposit_received' => $request->deposit_received == '' ? 0 : $request->deposit_received,
                'remaining_amount_received' => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
                'fso_person' => $request->fso_person,
                'fso_last_date' => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
                'aps_person' => $request->aps_person,
                'aps_last_date' => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
                'finance_detail' => $request->finance_detail,
                'destination' => $request->destination,
                'user_id' => Auth::user()->id,
                'itf_current_date' => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
                'tdp_current_date' => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
                'tds_current_date' => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),
                'holiday' => $request->holiday,

            ));

            // if ($request->flight_booked == 'yes') {
            //     //Sending email
            //     $template   = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;
            //     $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
            //     $template   .= '<h1>Last Date Of Flight Booking : ' . $request->fb_last_date . '</h1>';

            //     if ($request->fb_person == '') {
            //         $email = Auth::user()->email;
            //         $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
            //     } else {
            //         $record = User::where('id', $request->fb_person)->get()->first();
            //         $email  = $record->email;
            //         $name   = $record->name;
            //         $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
            //     }
            //     $data['to']        = $email;
            //     $data['name']      = config('app.name');
            //     $data['from']      = config('app.mail');
            //     $data['subject']   = "Task Flight Booked Alert";
            //     try {
            //         // \Mail::send("email_template.flight_booked_alert", ['template' => $template], function ($m) use ($data) {
            //         //     $m->from($data['from'], $data['name']);
            //         //     $m->to($data['to'])->subject($data['subject']);
            //         // });
            //     } catch (Swift_RfcComplianceException $e) {
            //         return $e->getMessage();
            //     }
            //     //Sending email
            // }
            // if ($request->form_received_on == '0000-00-00') {
            //     //Sending email
            //     $template     = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

            //     $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
            //     $template   .= '<h1>Reminder for sent on date : ' . $request->fso_last_date . '</h1>';

            //     if ($request->fso_person == '') {
            //         $email = Auth::user()->email;
            //         $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
            //     } else {
            //         $record = User::where('id', $request->fso_person)->get()->first();
            //         $email  = $record->email;
            //         $name   = $record->name;
            //         $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
            //     }
            //     $data['to']        = $email;
            //     $data['name']      = config('app.name');
            //     $data['from']      = config('app.mail');
            //     $data['subject']   = "Reminder for form sent on";
            //     try {
            //         // \Mail::send("email_template.form_sent_on", ['template' => $template], function ($m) use ($data) {
            //         //     $m->from($data['from'], $data['name']);
            //         //     $m->to($data['to'])->subject($data['subject']);
            //         // });
            //     } catch (Swift_RfcComplianceException $e) {
            //         return $e->getMessage();
            //     }
            //     //Sending email
            // }

            // if ($request->electronic_copy_sent == 'no') {
            //     //Sending email
            //     $template    = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

            //     $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
            //     $template   .= '<h1>App Reminder Sent Date : ' . $request->aps_last_date . '</h1>';

            //     if ($request->aps_person == '') {
            //         $email = Auth::user()->email;
            //         $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
            //     } else {
            //         $record = User::where('id', $request->aps_person)->get()->first();
            //         $email  = $record->email;
            //         $name   = $record->name;
            //         $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
            //     }
            //     $data['to']        = $email;
            //     $data['name']      = config('app.name');
            //     $data['from']      = config('app.mail');
            //     $data['subject']   = "Reminder for app login sent";
            //     try {
            //         // \Mail::send("email_template.app_login_sent", ['template' => $template], function ($m) use ($data) {
            //         //     $m->from($data['from'], $data['name']);
            //         //     $m->to($data['to'])->subject($data['subject']);
            //         // });
            //     } catch (Swift_RfcComplianceException $e) {
            //         return $e->getMessage();
            //     }
            //     //Sending email
            // }

            return Redirect::route('creat-code')->with('success_message', 'Created Successfully');
        } else {
            $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_ref';
                $output = $this->curl_data($url);
                //   return json_decode($output)->data;
            });

            $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output = $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
                $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
                $output = $this->curl_data($url);
                return json_decode($output);
            });

            $booking_email = booking_email::where('booking_id', '=', 1)->get();
            return view('code.create-code')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(), 'persons' => user::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'booking_email' => $booking_email, 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(), 'suppliers' => Supplier::all()]);
        }
    }

    public function checkInSession($date, $season)
    {
        $startDate = date('Y-m-d', strtotime($season->start_date));
        $endDate = date('Y-m-d', strtotime($season->end_date));
        if (($date >= $startDate) && ($date <= $endDate)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_quote($id)
    {
        $qoute = Qoute::findOrFail(decrypt($id));
        $qoute->delete();
        return Redirect::route('view-quote')->with('success_message', 'Supplier Successfully Updated!!');
    }

    public function convert_quote_to_booking($id)
    {
        $qoute = Qoute::find($id);
        $qoute->qoute_to_booking_status = 1;
        $qoute->qoute_to_booking_date = date('Y-m-d');
        $qoute->save();

        $booking = new Booking;
        $booking->reference_name = $qoute->reference_name;
        $booking->ref_no = $qoute->ref_no;
        $booking->qoute_id = $id;
        $booking->quotation_no = $qoute->quotation_no;
        $booking->dinning_preferences = $qoute->dinning_preferences;
        $booking->lead_passenger_name = $qoute->lead_passenger_name;
        $booking->brand_name = $qoute->brand_name;
        $booking->type_of_holidays = $qoute->type_of_holidays;
        $booking->sale_person = $qoute->sale_person;
        $booking->season_id = $qoute->season_id;
        $booking->agency_booking = $qoute->agency_booking;
        $booking->agency_name = $qoute->agency_name;
        $booking->agency_contact_no = $qoute->agency_contact_no;
        $booking->currency = $qoute->currency;
        $booking->convert_currency = $qoute->convert_currency;
        $booking->group_no = $qoute->group_no;
        $booking->net_price = $qoute->net_price;
        $booking->markup_amount = $qoute->markup_amount;
        $booking->selling = $qoute->selling;
        $booking->gross_profit = $qoute->gross_profit;
        $booking->markup_percent = $qoute->markup_percent;
        $booking->show_convert_currency = $qoute->show_convert_currency;
        $booking->per_person = $qoute->per_person;
        $booking->port_tax = $qoute->port_tax;
        $booking->total_per_person = $qoute->total_per_person;
        $booking->qoute_to_booking_date = date('Y-m-d');
        $booking->pax_name = $qoute->pax_name;
        $booking->save();

        $qouteDetails = QouteDetail::where('qoute_id', $id)->get();
        foreach ($qouteDetails as $key => $qouteDetail) {
            $bookingDetail = new BookingDetail;
            $bookingDetail->qoute_id = $id;
            $bookingDetail->booking_id = $booking->id;
            $bookingDetail->quotation_no = $qoute->quotation_no;
            $bookingDetail->row = $key + 1;
            $bookingDetail->date_of_service = $qouteDetail->date_of_service ? Carbon::parse(str_replace('/', '-', $qouteDetail->date_of_service))->format('Y-m-d') : null;
            $bookingDetail->service_details = $qouteDetail->service_details;
            $bookingDetail->category_id = $qouteDetail->category;
            $bookingDetail->supplier = $qouteDetail->supplier;
            $bookingDetail->booking_date = $qouteDetail->booking_date ? Carbon::parse(str_replace('/', '-', $qouteDetail->booking_date))->format('Y-m-d') : null;
            $bookingDetail->booking_due_date = $qouteDetail->booking_due_date ? Carbon::parse(str_replace('/', '-', $qouteDetail->booking_due_date))->format('Y-m-d') : null;
            $bookingDetail->booked_by = $qouteDetail->booked_by;
            $bookingDetail->booking_refrence = $qouteDetail->booking_refrence;
            $bookingDetail->booking_type = $qouteDetail->booking_type;
            $bookingDetail->comments = $qouteDetail->comments;
            $bookingDetail->supplier_currency = $qouteDetail->supplier_currency;
            $bookingDetail->cost = $qouteDetail->cost;
            $bookingDetail->actual_cost = $qouteDetail->actual_cost;
            $bookingDetail->supervisor_id = $qouteDetail->supervisor;
            $bookingDetail->added_in_sage = $qouteDetail->added_in_sage;
            $bookingDetail->qoute_base_currency = $qouteDetail->qoute_base_currency;
            $bookingDetail->save();
        }

        return Redirect::route('view-quote')->with('success_message', 'Quotation Converted Successfully. ');
    }

    public function create_quote(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name' => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency' => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no' => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, ["booking_due_date" => "required|array", "booking_due_date.*" => "required"]);
            $this->validate($request, ["cost" => "required|array", "cost.*" => "required"]);
            $this->validate($request, ["pax_name" => "array", "pax_name.*" => "required|string|distinct"], ['required' => 'Pax Name is required']);
            $season = season::find($request->season_id);

            // if(!empty($request->date_of_service)){
            //     $error_array = [];
            //     foreach($request->date_of_service as $key => $date){

            //         $start = date('Y-m-d', strtotime($season->start_date));
            //         $end   = date('Y-m-d', strtotime($season->end_date));

            //         if(!is_null($date)){
            //             $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($date) && !is_null($start)  && !is_null($end)){
            //             if( !(($date >= $start) && ($date <= $end)) ){
            //                 $error_array[$key+1] = "Date of service should be season date range.";
            //             }
            //         }

            //     }
            // }

            // if(!empty($error_array)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'date_of_service' =>  (object) $error_array
            //     ]);
            // }

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            $errors = [];
            foreach ($request->booking_due_date as $key => $duedate) {
                $duedate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));

                $startDate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->start_date))->format('Y-m-d')));
                $endDate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->end_date))->format('Y-m-d')));
                $bookingdate = (isset($request->booking_date) && !empty($request->booking_date[$key])) ? $request->booking_date[$key] : null;
                if ($bookingdate != null) {
                    $bookingdate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                }
                $dateofservice = (isset($request->date_of_service) && !empty($request->date_of_service[$key])) ? $request->date_of_service[$key] : null;
                if ($dateofservice != null) {
                    $dateofservice = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
                }
                $error = [];
                $dueresult = false;
                $dofresult = false;
                $bookresult = false;

                if ($this->checkInSession($duedate, $season) == false) {
                    $a[$key + 1] = 'Due Date should be season date range.';
                } else {
                    $dueresult = true;
                }
                if ($bookingdate != null && $this->checkInSession($bookingdate, $season) == false) {
                    $b[$key + 1] = 'Booking Date should be season date range.';
                } else {
                    $bookresult = true;
                }
                if ($dateofservice != null && $this->checkInSession($dateofservice, $season) == false) {
                    $c[$key + 1] = 'Date of service should be season date range.';
                } else {
                    $dofresult = true;
                }

                if ($dateofservice != null && $bookingdate == null) {
                    $b[$key + 1] = 'Booking Date field is required before the date of service.';
                    $bookresult = false;
                }

                if ($bookresult == true) {
                    if ($bookingdate != null && $bookingdate < $duedate) {
                        $b[$key + 1] = 'Booking Date should be smaller than booking due date.';
                    }
                }

                if ($dofresult == true) {
                    if ($bookingdate != null && $bookingdate > $dateofservice) {
                        $c[$key + 1] = 'Date of service should be smaller than booking date.';
                    }
                }

                $error['date_of_service'] = (isset($c) && count($c) > 0) ? (object) $c : null;
                $error['booking_date'] = (isset($b) && count($b) > 0) ? (object) $b : null;
                $error['booking_due_date'] = (isset($a) && count($a) > 0) ? (object) $a : null;

                $errors = $error;
            }

            if (count($errors) > 0) {
                if ($error['date_of_service'] != null || $error['date_of_service'] != null || $error['date_of_service'] != null) {
                    throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }

            $qoute = new Qoute;
            $qoute->ref_no = $request->ref_no;
            $qoute->reference_name = $request->reference;
            $qoute->quotation_no = $request->quotation_no;
            $qoute->dinning_preferences = $request->dinning_preferences;
            $qoute->lead_passenger_name = $request->lead_passenger_name;
            $qoute->brand_name = $request->brand_name;
            $qoute->type_of_holidays = $request->type_of_holidays;
            $qoute->sale_person = $request->sale_person;
            $qoute->season_id = $request->season_id;
            $qoute->agency_booking = $request->agency_booking;
            $qoute->agency_name = $request->agency_name;
            $qoute->agency_contact_no = $request->agency_contact_no;
            $qoute->currency = $request->currency;
            $qoute->convert_currency = $request->convert_currency;
            $qoute->group_no = $request->group_no;
            $qoute->net_price = $request->net_price;
            $qoute->markup_amount = $request->markup_amount;
            $qoute->selling = $request->selling;
            $qoute->gross_profit = $request->gross_profit;
            $qoute->markup_percent = $request->markup_percent;
            $qoute->show_convert_currency = $request->show_convert_currency;
            $qoute->per_person = $request->per_person;
            $qoute->pax_name = $request->pax_name;
            // if($request->has('pax_name') && count($request->pax_name) > 0){
            // }
            $qoute->save();

            if (!empty($request->cost)) {
                foreach ($request->cost as $key => $cost) {
                    $qouteDetail = new QouteDetail;
                    $qouteDetail->qoute_id = $qoute->id;
                    $qouteDetail->date_of_service = $request->date_of_service[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->service_details = $request->service_details[$key];
                    $qouteDetail->category_id = $request->category[$key];
                    $qouteDetail->supplier = $request->supplier[$key];
                    $qouteDetail->product = $request->product[$key];
                    $qouteDetail->booking_date = $request->booking_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->booking_due_date = $request->booking_due_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->booking_method = $request->booking_method[$key];
                    $qouteDetail->booked_by = $request->booked_by[$key];
                    $qouteDetail->booking_refrence = $request->booking_refrence[$key];
                    $qouteDetail->booking_type = $request->booking_type[$key];
                    $qouteDetail->comments = $request->comments[$key];
                    $qouteDetail->supplier_currency = $request->supplier_currency[$key];
                    $qouteDetail->cost = $request->cost[$key];
                    $qouteDetail->supervisor_id = $request->supervisor[$key];
                    $qouteDetail->added_in_sage = ($request->has('added_in_sage') && isset($request->added_in_sage[$key])) ? $request->added_in_sage[$key] : null;
                    $qouteDetail->qoute_base_currency = $request->qoute_base_currency[$key];
                    $qouteDetail->save();
                }
            }

            return response()->json(['success_message' => 'Quote Successfully Created!!']);
        }

        $get_user_branche = Cache::remember('get_user_branche', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output, true);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.create')->with([
            'get_user_branche' => $get_user_branche,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            'products' => Product::all()->sortBy('name'),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'currencies' => Currency::where('status', 1)->orderBy('id', 'ASC')->get(),
            'templates' => Template::all()->sortBy('name'),
            // 'sale_person' => User::where('role_id',2)->orderBy('name', 'asc')->get(),
        ]);
    }

  
    public function view_quote()
    {
        $data['quotes'] = Qoute::select('*', DB::raw('count(*) as quote_count'))->groupBy('ref_no')->get();
        return view('qoute.view', $data);
    }

    public function booking(Request $request, $id)
    {

        if ($request->isMethod('post')) {

            $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name' => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency' => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no' => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, ["booking_due_date" => "required|array", "booking_due_date.*" => "required"]);
            $this->validate($request, ["cost" => "required|array", "cost.*" => "required"]);

            $season = season::find($request->season_id);

            // if(!empty($request->date_of_service)){
            //     $error_array = [];
            //     foreach($request->date_of_service as $key => $date){

            //         $start = date('Y-m-d', strtotime($season->start_date));
            //         $end   = date('Y-m-d', strtotime($season->end_date));

            //         if(!is_null($date)){
            //             $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($date) && !is_null($start)  && !is_null($end)){
            //             if( !(($date >= $start) && ($date <= $end)) ){
            //                 $error_array[$key+1] = "Date of service should be season date range.";
            //             }
            //         }

            //     }
            // }

            // if(!empty($error_array)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'date_of_service' =>  (object) $error_array
            //     ]);
            // }

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            // }

            $errors = [];
            foreach ($request->booking_due_date as $key => $duedate) {
                $duedate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));

                $startDate = date('Y-m-d', strtotime($season->start_date));
                $endDate = date('Y-m-d', strtotime($season->end_date));

                $bookingdate = (isset($request->booking_date) && !empty($request->booking_date[$key])) ? $request->booking_date[$key] : null;
                if ($bookingdate != null) {
                    $bookingdate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                }
                $dateofservice = (isset($request->date_of_service) && !empty($request->date_of_service[$key])) ? $request->date_of_service[$key] : null;
                if ($dateofservice != null) {
                    $dateofservice = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
                }
                $error = [];
                $dueresult = false;
                $dofresult = false;
                $bookresult = false;

                if ($this->checkInSession($duedate, $season) == false) {
                    $a[$key + 1] = 'Due Date should be season date range.';
                } else {
                    $dueresult = true;
                }
                if ($bookingdate != null && $this->checkInSession($bookingdate, $season) == false) {
                    $b[$key + 1] = 'Booking Date should be season date range.';
                } else {
                    $bookresult = true;
                }
                if ($dateofservice != null && $this->checkInSession($dateofservice, $season) == false) {
                    $c[$key + 1] = 'Date of service should be season date range.';
                } else {
                    $dofresult = true;
                }

                if ($dateofservice != null && $bookingdate == null) {
                    $b[$key + 1] = 'Booking Date field is required before the date of service.';
                    $bookresult = false;
                }

                if ($bookresult == true) {
                    if ($bookingdate != null && $bookingdate < $duedate) {
                        $b[$key + 1] = 'Booking Date should be smaller than booking due date.';
                    }
                }

                if ($dofresult == true) {
                    if ($bookingdate != null && $bookingdate > $dateofservice) {
                        $c[$key + 1] = 'Date of service should be smaller than booking date.';
                    }
                }

                $error['date_of_service'] = (isset($c) && count($c) > 0) ? (object) $c : null;
                $error['booking_date'] = (isset($b) && count($b) > 0) ? (object) $b : null;
                $error['booking_due_date'] = (isset($a) && count($a) > 0) ? (object) $a : null;

                $errors = $error;
            }

            if (count($errors) > 0) {
                if ($error['date_of_service'] != null || $error['date_of_service'] != null || $error['date_of_service'] != null) {
                    throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }

            $booking = Booking::updateOrCreate(
                ['quotation_no' => $request->quotation_no],

                [
                    'ref_no' => $request->ref_no,
                    'reference_name' => $request->reference,
                    'qoute_id' => $request->qoute_id,
                    'quotation_no' => $request->quotation_no,
                    'dinning_preferences' => $request->dinning_preferences,
                    'lead_passenger_name' => $request->lead_passenger_name,
                    'brand_name' => $request->brand_name,
                    'type_of_holidays' => $request->type_of_holidays,
                    'sale_person' => $request->sale_person,
                    'season_id' => $request->season_id,
                    'agency_booking' => $request->agency_booking,
                    'agency_name' => $request->agency_name,
                    'agency_contact_no' => $request->agency_contact_no,
                    'currency' => $request->currency,
                    'convert_currency' => $request->convert_currency,
                    'group_no' => $request->group_no,
                    'net_price' => $request->net_price,
                    'markup_amount' => $request->markup_amount,
                    'selling' => $request->selling,
                    'gross_profit' => $request->gross_profit,
                    'markup_percent' => $request->markup_percent,
                    'show_convert_currency' => $request->show_convert_currency,
                    'per_person' => $request->per_person,

                ]
            );

            if (!empty($request->actual_cost)) {
                foreach ($request->actual_cost as $key => $cost) {

                    if (!is_null($request->qoute_invoice)) {

                        if (array_key_exists($key, $request->qoute_invoice)) {

                            $oldFileName = $request->qoute_invoice_record[$key];

                            $newFile = $request->qoute_invoice[$key];
                            $filename = $newFile->getClientOriginalName();

                            $folder = public_path('booking/' . $request->qoute_id);

                            if (!File::exists($folder)) {
                                File::makeDirectory($folder, 0775, true, true);
                            }

                            $destinationPath = public_path('booking/' . $request->qoute_id . '/' . $oldFileName);
                            File::delete($destinationPath);

                            $newFile->move(public_path('booking/' . $request->qoute_id), $filename);

                        } else {
                            $filename = isset($request->qoute_invoice_record[$key]) ? $request->qoute_invoice_record[$key] : null;
                        }
                    } else {

                        $filename = isset($request->qoute_invoice_record[$key]) ? $request->qoute_invoice_record[$key] : null;
                    }

                    $bookingDetail = BookingDetail::updateOrCreate(
                        [
                            'quotation_no' => $request->quotation_no,
                            'row' => $key + 1,
                        ],

                        [
                            'qoute_id' => $request->qoute_id,
                            'booking_id' => $booking->id,
                            'quotation_no' => $request->quotation_no,
                            'row' => $key + 1,
                            'date_of_service' => $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null,
                            'service_details' => $request->service_details[$key],
                            'category_id' => $request->category[$key],
                            'supplier' => $request->supplier[$key],
                            'booking_date' => $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null,
                            'booking_due_date' => $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null,
                            // 'booking_method'    => $request->booking_method[$key],
                            'booked_by' => $request->booked_by[$key],
                            'booking_refrence' => $request->booking_refrence[$key],
                            'booking_type' => $request->booking_type[$key],
                            'comments' => $request->comments[$key],
                            'supplier_currency' => $request->supplier_currency[$key],
                            'cost' => $request->cost[$key],
                            'actual_cost' => $request->actual_cost[$key],
                            'supervisor_id' => $request->supervisor[$key],
                            'added_in_sage' => $request->added_in_sage[$key],
                            'qoute_base_currency' => $request->qoute_base_currency[$key],
                            'qoute_invoice' => $filename,
                        ]
                    );

                    foreach ($request->deposit_due_date[$key] as $ikey => $deposit_due_date) {

                        if ($request->upload_calender[$key][$ikey] == true && $deposit_due_date != null) {
                            $event = new Event;
                            $event->name = "To Pay " . $request->deposit_amount[$key][$ikey] . ' ' . $request->supplier_currency[$key] . " to Supplier";
                            $event->description = 'Event description';
                            $event->startDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->startOfDay() : null;
                            $event->endDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->endOfDay() : null;
                            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
                            $event->save();

                        }
                        FinanceBookingDetail::updateOrCreate(
                            [
                                'booking_detail_id' => $bookingDetail->id,
                                'row' => $ikey + 1,
                            ],
                            [
                                'upload_to_calender' => $request->upload_calender[$key][$ikey],
                                'deposit_amount' => !empty($request->deposit_amount[$key][$ikey]) ? $request->deposit_amount[$key][$ikey] : null,
                                'deposit_due_date' => $request->deposit_due_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'paid_date' => $request->paid_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'payment_method' => $request->payment_method[$key][$ikey] ?? null,
                            ]

                        );

                    }

                }
            }

            return response()->json(['success_message' => 'Successfully Converted To Booked']);
        }

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        $booking = Booking::where('qoute_id', $id)->first();

        if (!is_null($booking)) {
            $quote = $booking;
        } else {
            $quote = Qoute::find($id);
        }

        $bookingDetail = BookingDetail::where('qoute_id', $id)->get();

        if ($bookingDetail->count()) {
            $quote_details = $bookingDetail;
        } else {
            $quote_details = QouteDetail::where('qoute_id', $id)->get();
        }

        return view('qoute.booking.edit')->with([
            'quote' => $quote,
            'quote_details' => $quote_details,
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'payment_method' => payment::all()->sortBy('name'),
            'currencies' => Currency::all()->sortBy('name'),
            'qoute_logs' => QouteLog::where('qoute_id', $id)->get(),
        ]);

        // $booking->ref_no           =  $request->ref_no;
        // $booking->quotation_no     =  $request->quotation_no;
        // $booking->brand_name       =  $request->brand_name;
        // $booking->type_of_holidays =  $request->type_of_holidays;
        // $booking->sale_person      =  $request->sale_person;
        // $booking->season_id        =  $request->season_id;
        // $booking->agency_booking   =  $request->agency_booking;
        // $booking->agency_name       =  $request->agency_name;
        // $booking->agency_contact_no =  $request->agency_contact_no;
        // $booking->currency          =  $request->currency;
        // $booking->convert_currency  =  $request->convert_currency;
        // $booking->group_no          =  $request->group_no;
        // $booking->net_price         =  $request->net_price;
        // $booking->markup_amount     =  $request->markup_amount;
        // $booking->selling           =  $request->selling;
        // $booking->markup_percent    =  $request->markup_percent;
        // $booking->show_convert_currency =  $request->show_convert_currency;
        // $booking->per_person       =  $request->per_person;
        // $booking->save();

        // $bookingDetail = BookingDetail::where('qoute_id', $id)->get();

        // $qouteDetailLog = new QouteDetailLog;

        // foreach($qouteDetails as $key => $qouteDetail){

        //     $QouteDetailLog = new QouteDetailLog;
        //     $QouteDetailLog->qoute_id          = $qouteDetail->qoute_id;
        //     $QouteDetailLog->date_of_service   = $qouteDetail->date_of_service;
        //     $QouteDetailLog->service_details   =  $qouteDetail->service_details;
        //     $QouteDetailLog->category_id       =  $qouteDetail->category_id;
        //     $QouteDetailLog->supplier          =  $qouteDetail->supplier;
        //     $QouteDetailLog->booking_date      =  $qouteDetail->booking_date;
        //     $QouteDetailLog->booking_due_date  =  $qouteDetail->booking_due_date;
        //     $QouteDetailLog->booking_method    =  $qouteDetail->booking_method;
        //     $QouteDetailLog->booked_by         =  $qouteDetail->booked_by;
        //     $QouteDetailLog->booking_refrence  =  $qouteDetail->booking_refrence;
        //     $QouteDetailLog->comments          =  $qouteDetail->comments;
        //     $QouteDetailLog->supplier_currency =  $qouteDetail->supplier_currency;
        //     $QouteDetailLog->cost              =  $qouteDetail->cost;
        //     $QouteDetailLog->supervisor_id     =  $qouteDetail->supervisor_id;
        //     $QouteDetailLog->added_in_sage     =  $qouteDetail->added_in_sage;
        //     $QouteDetailLog->qoute_base_currency =  $qouteDetail->qoute_base_currency;
        //     $QouteDetailLog->log_no = $qouteDetailLogNumber;
        //     $QouteDetailLog->save();
        // }

        // Delete old qoute
        // QouteDetail::where('qoute_id',$id)->delete();

        // if(!empty($request->cost)){
        //     foreach($request->cost as $key => $cost){

        //         $qouteDetail = new QouteDetail;
        //         $qouteDetail->qoute_id = $qoute->id;
        //         $qouteDetail->date_of_service   = $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null;
        //         $qouteDetail->service_details   = $request->service_details[$key];
        //         $qouteDetail->category_id       = $request->category[$key];
        //         $qouteDetail->supplier          = $request->supplier[$key];
        //         $qouteDetail->booking_date      = $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null;
        //         $qouteDetail->booking_due_date  = $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null;
        //         $qouteDetail->booking_method    = $request->booking_method[$key];
        //         $qouteDetail->booked_by         = $request->booked_by[$key];
        //         $qouteDetail->booking_refrence  = $request->booking_refrence[$key];
        //         $qouteDetail->comments          = $request->comments[$key];
        //         $qouteDetail->supplier_currency = $request->supplier_currency[$key];
        //         $qouteDetail->cost              = $request->cost[$key];
        //         $qouteDetail->supervisor_id     = $request->supervisor[$key];
        //         $qouteDetail->added_in_sage     = $request->added_in_sage[$key];
        //         $qouteDetail->qoute_base_currency     = $request->qoute_base_currency[$key];

        //         // if(!is_null($request->qoute_invoice)){

        //         //     if(array_key_exists($key,$request->qoute_invoice))
        //         //     {

        //         //         $file = $request->qoute_invoice[$key];

        //         //         $folder = public_path('quote/' . $qoute->id );
        //         //         $filename = $file->getClientOriginalName();

        //         //         if (!File::exists($folder)) {
        //         //             File::makeDirectory($folder, 0775, true, true);
        //         //         }

        //         //         $destinationPath = public_path('quote/'. $id .'/'.  $filename  );
        //         //         File::delete($destinationPath);

        //         //         $file->move(public_path('quote/' . $qoute->id ), $filename);

        //         //         $qouteDetail->qoute_invoice  = $filename ? $filename : null;

        //         //     }
        //         //     else{
        //         //         $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
        //         //     }
        //         // }else{

        //         //     $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
        //         // }

        //         $qouteDetail->save();

        //     }
        // }

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });
        return view('qoute.view')->with(['quotes' => Qoute::all()]);
    }

    public function upload_to_calendar(Request $request)
    {

        if ($request->isMethod('post')) {

            // dd($request->all());

            // $title = "To Pay $request->deposit_amount $request->supplier_currency to Supplier";

            // $dynamic_text_area = "$request->details";

            // $calendar_start_date = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->format('Ymd');
            // $calendar_end_date = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->format('Ymd');

            // $location = "";
            // $description = "test";
            // // $guests = "kashan.mehmood13@gmail.com";
            // $message_url ="https://www.google.com/calendar/render?action=TEMPLATE&text=".$title."&dates=".$calendar_start_date."/".$calendar_end_date."&details=".$dynamic_text_area."&location=".$location."&sf=true&output=xml";
            // return $message_url;

            $event = new Event;
            $event->name = "To Pay $request->depositAmount $request->supplier_currency to Supplier";
            $event->description = 'Event description';
            $event->startDate = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->startOfDay();
            $event->endDate = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->endOfDay();
            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
            $event->save();

            dd($request->all());
        }

    }

    public function edit_quote(Request $request, $id)
    {

        if ($request->isMethod('post')) {

            $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name' => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency' => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no' => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, ["booking_due_date" => "required|array", "booking_due_date.*" => "required"]);
            $this->validate($request, ["cost" => "required|array", "cost.*" => "required"]);
            $this->validate($request, ["pax_name" => "array", "pax_name.*" => "required|string|distinct"], ['required' => 'Pax Name is required']);

            $season = season::findOrFail($request->season_id);
            // if(!empty($request->date_of_service)){
            //     $error_array = [];
            //     foreach($request->date_of_service as $key => $date){

            //         $start = date('Y-m-d', strtotime($season->start_date));
            //         $end   = date('Y-m-d', strtotime($season->end_date));

            //         if(!is_null($date)){
            //             $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($date) && !is_null($start)  && !is_null($end)){
            //             if( !(($date >= $start) && ($date <= $end)) ){
            //                 $error_array[$key+1] = "Date of service should be season date range.";
            //             }
            //         }

            //     }
            // }

            // if(!empty($error_array)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'date_of_service' =>  (object) $error_array
            //     ]);
            // }

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            // }

            $errors = [];
            foreach ($request->booking_due_date as $key => $duedate) {
                $duedate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));

                $startDate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->start_date))->format('Y-m-d')));
                $endDate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->end_date))->format('Y-m-d')));

                $bookingdate = (isset($request->booking_date) && !empty($request->booking_date[$key])) ? $request->booking_date[$key] : null;
                if ($bookingdate != null) {
                    $bookingdate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                    // date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                }
                $dateofservice = (isset($request->date_of_service) && !empty($request->date_of_service[$key])) ? $request->date_of_service[$key] : null;
                if ($dateofservice != null) {
                    $dateofservice = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
                }
                $error = [];
                $dueresult = false;
                $dofresult = false;
                $bookresult = false;

                if ($this->checkInSession($duedate, $season) == false) {
                    $a[$key + 1] = 'Due Date should be season date range.';
                } else {
                    $dueresult = true;
                }
                if ($bookingdate != null && $this->checkInSession($bookingdate, $season) == false) {
                    $b[$key + 1] = 'Booking Date should be season date range.';
                } else {
                    $bookresult = true;
                }
                if ($dateofservice != null && $this->checkInSession($dateofservice, $season) == false) {
                    $c[$key + 1] = 'Date of service should be season date range.';
                } else {
                    $dofresult = true;
                }

                if ($dateofservice != null && $bookingdate == null) {
                    $b[$key + 1] = 'Booking Date field is required before the date of service.';
                    $bookresult = false;
                }

                if ($bookresult == true) {
                    if ($bookingdate != null && $bookingdate < $duedate) {
                        $b[$key + 1] = 'Booking Date should be smaller than booking due date.';
                    }
                }

                if ($dofresult == true) {
                    if ($bookingdate != null && $bookingdate > $dateofservice) {
                        $c[$key + 1] = 'Date of service should be smaller than booking date.';
                    }
                }

                $error['date_of_service'] = (isset($c) && count($c) > 0) ? (object) $c : null;
                $error['booking_date'] = (isset($b) && count($b) > 0) ? (object) $b : null;
                $error['booking_due_date'] = (isset($a) && count($a) > 0) ? (object) $a : null;

                $errors = $error;
            }

            if (count($errors) > 0) {
                if ($error['date_of_service'] != null || $error['date_of_service'] != null || $error['date_of_service'] != null) {
                    throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }

            $qoute = Qoute::findOrFail($id);

            $qoute_log = new QouteLog;

            $qouteDetailLogNumber = $this->increment_log_no($this->get_log_no('QouteLog', $id));
            $qoute_log->qoute_id = $id;
            $qoute_log->ref_no = $qoute->ref_no;
            $qoute_log->reference_name = $qoute->reference_name;
            $qoute_log->quotation_no = $qoute->quotation_no;
            $qoute_log->dinning_preferences = $qoute->dinning_preferences;
            $qoute_log->lead_passenger_name = $qoute->lead_passenger_name;
            $qoute_log->brand_name = $qoute->brand_name;
            $qoute_log->type_of_holidays = $qoute->type_of_holidays;
            $qoute_log->sale_person = $qoute->sale_person;
            $qoute_log->season_id = $qoute->season_id;
            $qoute_log->agency_booking = $qoute->agency_booking;
            $qoute_log->agency_name = $qoute->agency_name;
            $qoute_log->agency_contact_no = $qoute->agency_contact_no;
            $qoute_log->currency = $qoute->currency;
            $qoute_log->convert_currency = $qoute->convert_currency;
            $qoute_log->group_no = $qoute->group_no;
            $qoute_log->net_price = $qoute->net_price;
            $qoute_log->markup_amount = $qoute->markup_amount;
            $qoute_log->selling = $qoute->selling;
            $qoute_log->gross_profit = $qoute->gross_profit;
            $qoute_log->markup_percent = $qoute->markup_percent;
            $qoute_log->show_convert_currency = $qoute->show_convert_currency;
            $qoute_log->per_person = $qoute->per_person;
            $qoute_log->created_date = date("Y-m-d");
            $qoute_log->log_no = $qouteDetailLogNumber;
            $qoute_log->user_id = Auth::user()->id;
            $qoute_log->pax_name = $qoute->pax_name;
            $qoute_log->save();

            $qoute->ref_no = $request->ref_no;
            $qoute->quotation_no = $request->quotation_no;
            $qoute->reference_name = $request->reference;
            $qoute->dinning_preferences = $request->dinning_preferences;
            $qoute->lead_passenger_name = $request->lead_passenger_name;
            $qoute->brand_name = $request->brand_name;
            $qoute->type_of_holidays = $request->type_of_holidays;
            $qoute->sale_person = $request->sale_person;
            $qoute->season_id = $request->season_id;
            $qoute->agency_booking = $request->agency_booking;
            $qoute->agency_name = $request->agency_name;
            $qoute->agency_contact_no = $request->agency_contact_no;
            $qoute->currency = $request->currency;
            $qoute->convert_currency = $request->convert_currency;
            $qoute->group_no = $request->group_no;
            $qoute->net_price = $request->net_price;
            $qoute->markup_amount = $request->markup_amount;
            $qoute->selling = $request->selling;
            $qoute->gross_profit = $request->gross_profit;
            $qoute->markup_percent = $request->markup_percent;
            $qoute->show_convert_currency = $request->show_convert_currency;
            $qoute->per_person = $request->per_person;
            $qoute->pax_name = $request->pax_name;

            $qoute->save();

            $qouteDetails = QouteDetail::where('qoute_id', $id)->get();

            $qouteDetailLog = new QouteDetailLog;

            foreach ($qouteDetails as $key => $qouteDetail) {

                $QouteDetailLog = new QouteDetailLog;
                $QouteDetailLog->qoute_id = $qouteDetail->qoute_id;
                $QouteDetailLog->date_of_service = $qouteDetail->date_of_service;
                $QouteDetailLog->service_details = $qouteDetail->service_details;
                $QouteDetailLog->category_id = $qouteDetail->category_id;
                $QouteDetailLog->product = $qouteDetail->product;
                $QouteDetailLog->supplier = $qouteDetail->supplier;
                $QouteDetailLog->booking_date = $qouteDetail->booking_date;
                $QouteDetailLog->booking_due_date = $qouteDetail->booking_due_date;
                $QouteDetailLog->booking_method = $qouteDetail->booking_method;
                $QouteDetailLog->booked_by = $qouteDetail->booked_by;
                $QouteDetailLog->booking_refrence = $qouteDetail->booking_refrence;
                $QouteDetailLog->booking_type = $qouteDetail->booking_type;
                $QouteDetailLog->comments = $qouteDetail->comments;
                $QouteDetailLog->supplier_currency = $qouteDetail->supplier_currency;
                $QouteDetailLog->cost = $qouteDetail->cost;
                $QouteDetailLog->supervisor_id = $qouteDetail->supervisor_id;
                $QouteDetailLog->added_in_sage = $qouteDetail->added_in_sage;
                $QouteDetailLog->qoute_base_currency = $qouteDetail->qoute_base_currency;
                $QouteDetailLog->log_no = $qouteDetailLogNumber;
                $QouteDetailLog->save();
            }

            // Delete old qoute
            QouteDetail::where('qoute_id', $id)->delete();

            if (!empty($request->cost)) {
                foreach ($request->cost as $key => $cost) {
                    $qouteDetail = new QouteDetail;
                    $qouteDetail->qoute_id = $qoute->id;
                    $qouteDetail->date_of_service = $request->date_of_service[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->service_details = $request->service_details[$key];
                    $qouteDetail->category_id = $request->category[$key];
                    $qouteDetail->supplier = $request->supplier[$key];
                    $qouteDetail->product = $request->product[$key];
                    $qouteDetail->booking_date = $request->booking_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->booking_due_date = $request->booking_due_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->booking_method = $request->booking_method[$key];
                    $qouteDetail->booked_by = $request->booked_by[$key];
                    $qouteDetail->booking_refrence = $request->booking_refrence[$key];
                    $qouteDetail->booking_type = $request->booking_type[$key];
                    $qouteDetail->comments = $request->comments[$key];
                    $qouteDetail->supplier_currency = $request->supplier_currency[$key];
                    $qouteDetail->cost = $request->cost[$key];
                    $qouteDetail->supervisor_id = $request->supervisor[$key];
                    $qouteDetail->added_in_sage = $request->added_in_sage[$key];
                    $qouteDetail->qoute_base_currency = $request->qoute_base_currency[$key];

                    // if(!is_null($request->qoute_invoice)){

                    //     if(array_key_exists($key,$request->qoute_invoice))
                    //     {

                    //         $file = $request->qoute_invoice[$key];

                    //         $folder = public_path('quote/' . $qoute->id );
                    //         $filename = $file->getClientOriginalName();

                    //         if (!File::exists($folder)) {
                    //             File::makeDirectory($folder, 0775, true, true);
                    //         }

                    //         $destinationPath = public_path('quote/'. $id .'/'.  $filename  );
                    //         File::delete($destinationPath);

                    //         $file->move(public_path('quote/' . $qoute->id ), $filename);

                    //         $qouteDetail->qoute_invoice  = $filename ? $filename : null;

                    //     }
                    //     else{
                    //         $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                    //     }
                    // }else{

                    //     $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                    // }

                    $qouteDetail->save();

                }
            }

            return response()->json(['success_message'=>'Quote Successfully Updated!!']);
        }

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.edit')->with([
            'quote' => Qoute::find($id),
            'quote_details' => QouteDetail::where('qoute_id', $id)->orderBy('date_of_service', 'ASC')->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            'products' => Product::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'currencies' => Currency::all()->sortBy('name'),
            'qoute_logs' => QouteLog::where('qoute_id', $id)->orderBy('log_no', 'DESC')->get(),
        ]);
    }

    public function view_version($quote_id, $log_no)
    {

        $qoute_log = QouteLog::where('qoute_id', $quote_id)
            ->where('log_no', $log_no)
            ->first();

        $qoute_detail_logs = QouteDetailLog::where('qoute_id', $quote_id)
            ->where('log_no', $log_no)
            ->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output = $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.view-version')->with([
            'qoute_log' => $qoute_log,
            'qoute_detail_logs' => $qoute_detail_logs,
            'seasons' => season::all(),
            'currencies' => Currency::all()->sortBy('name'),
            'categories' => Category::all()->sortBy('name'),
            'suppliers' => Supplier::all()->sortBy('name'),
            'products' => Product::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
        ]);

    }

    public function recall_version($quote_id, $log_no){

        $qoute_log = QouteLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->first();

        $qoute_detail_logs = QouteDetailLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.recall-version')->with([
            'quote' => $qoute_log,
            'quote_details' => $qoute_detail_logs,
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            'products' => Product::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'currencies' => Currency::all()->sortBy('name'),
            'qoute_logs' => QouteLog::where('qoute_id', $quote_id)->orderBy('log_no', 'DESC')->get(),
        ]);

    }


    public function get_log_no($table,$qoute_id)  {

        $modelName = "App\\$table";
        $qoute_log = $modelName::where('qoute_id', $qoute_id)->orderBy('created_at', 'DESC')->first();

        if(is_null($qoute_log)){
            return 0;
        }else{
            return $qoute_log->log_no;
        }

    }

    public function increment_log_no($number)  {
        return  $number =  $number + 1;
    }

    // public function view_code()
    // {
    //     return view('code.view-code')->with(['codes' => code::all()]);
    // }

    public function booking_method(Request $request){

        if($request->isMethod('post')){

            $this->validate($request, ['booking_method_name'  => 'required'], ['required' => 'Booking Method is required']);

            $booking_method = new BookingMethod;
            $booking_method->name = $request->booking_method_name;
            $booking_method->save();

            return Redirect::route('view-booking-method')->with('success_message', 'Created Successfully');
        }

        return view('booking_method.create');
    }

    public function view_booking_method()
    {

        $booking_methods = BookingMethod::all();
        return view('booking_method.view')->with('booking_methods', $booking_methods);
    }

    public function edit_booking_method(Request $request, $id)
    {

        if ($request->isMethod('post')) {

            $this->validate($request, ['booking_method_name' => 'required'], ['required' => 'Booking Method is required']);

            $booking_method = BookingMethod::find($id);
            $booking_method->name = $request->booking_method_name;
            $booking_method->save();

            return Redirect::route('view-booking-method')->with('success_message', 'Successfully Updated!!');
        }

        $booking_method = BookingMethod::find($id);
        return view('booking_method.edit')->with('booking_method', $booking_method);
    }

    public function del_booking_method(Request $request, $id)
    {

        BookingMethod::destroy('id', '=', $id);
        return Redirect::route('view-booking-method')->with('success_message', 'Delete Successfully');
    }

    public function update_code(Request $request, $id)
    {

        // dd($request->all());

        $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
        $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
        $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
        $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
        $this->validate($request, ['category' => 'required'], ['required' => 'Please select Category']);
        $this->validate($request, ['product' => 'required'], ['required' => 'Please select Product']);
        $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
        $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Please select Agency']);
        $this->validate($request, ['pax_no' => 'required'], ['required' => 'Please select PAX No']);
        $this->validate($request, ['date_of_travel' => 'required'], ['required' => 'Please select date of travel']);

        $this->validate($request, ['supplier' => 'required'], ['required' => 'Please select Supplier']);

        // $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);

        // $this->validate($request, ['fb_airline_name_id'         => 'required_if:flight_booked,yes'], ['required_if' => 'Please select flight airline name']);

        // $this->validate($request, ['fb_payment_method_id'       => 'required_if:flight_booked,yes'], ['required_if' => 'Please select payment method']);

        // $this->validate($request, ['fb_booking_date'            => 'required_if:flight_booked,yes'], ['required_if' => 'Please select booking date']);

        // $this->validate($request, ['fb_airline_ref_no'          => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter airline reference number']);

        // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
        // //
        // // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']);
        // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);

        // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
        // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
        // // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
        // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
        // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
        // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
        // //
        // // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
        // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
        // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);

        // // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
        // // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']);

        // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
        // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

        // // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
        // $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'], ['required_if' => 'Plesse enter itinerary sent date']);

        // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
        // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

        // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
        // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

        // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
        // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
        // $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
        // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);
        // $this->validate($request, ['tdp_current_date'              => 'required_if:document_prepare,yes'], ['required_if' => 'Plesse enter Travel Document Prepared Date']);

        if ($request->form_received_on == '0000-00-00') {
            $form_received_on = null;
        } else {
            $form_received_on = $request->form_received_on;
        }
        //
        if ($request->app_login_date == '0000-00-00') {
            $app_login_date = null;
        } else {
            $app_login_date = $request->app_login_date;
        }

        $product = code::where('id', $id)->update(array(
            'ref_no' => $request->ref_no,
            'brand_name' => $request->brand_name,
            'season_id' => $request->season_id,
            'agency_booking' => $request->agency_booking,
            'pax_no' => $request->pax_no,
            'date_of_travel' => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
            'category' => $request->category,
            'supplier' => $request->supplier,
            'product' => $request->product,
            'flight_booked' => $request->flight_booked,
            'fb_airline_name_id' => $request->fb_airline_name_id,
            'fb_payment_method_id' => $request->fb_payment_method_id,
            'fb_booking_date' => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
            'fb_airline_ref_no' => $request->fb_airline_ref_no,
            'fb_last_date' => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
            'fb_person' => $request->fb_person,
            //
            'aft_last_date' => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
            'aft_person' => $request->aft_person,
            'ds_last_date' => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
            'ds_person' => $request->ds_person,
            'to_last_date' => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
            'to_person' => $request->to_person,
            //
            'document_prepare' => $request->document_prepare,
            'dp_last_date' => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
            'dp_person' => $request->dp_person,
            //
            //
            'flight_booking_details' => $request->flight_booking_details,
            'asked_for_transfer_details' => $request->asked_for_transfer_details,
            'transfer_details' => $request->transfer_details,
            'form_sent_on' => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
            'form_received_on' => $form_received_on,
            'app_login_date' => $app_login_date,
            // 'transfer_info_received'      => $request->transfer_info_received,
            // 'transfer_info_details'       => $request->transfer_info_details,
            'itinerary_finalised' => $request->itinerary_finalised,
            'itinerary_finalised_details' => $request->itinerary_finalised_details,
            'itf_last_date' => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
            'itf_person' => $request->itf_person,
            'documents_sent' => $request->documents_sent,
            'documents_sent_details' => $request->documents_sent_details,
            'electronic_copy_sent' => $request->electronic_copy_sent,
            'electronic_copy_details' => $request->electronic_copy_details,
            'transfer_organised' => $request->transfer_organised,
            'transfer_organised_details' => $request->transfer_organised_details,
            'type_of_holidays' => $request->type_of_holidays,
            'sale_person' => $request->sale_person,
            'deposit_received' => $request->deposit_received == '' ? 0 : $request->deposit_received,
            'deposit_received' => isset($request->deposit_received) ? $request->deposit_received : 0,
            // 'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
            'remaining_amount_received' => isset($request->remaining_amount_received) ? $request->remaining_amount_received : 0,
            'fso_person' => $request->fso_person,
            'fso_last_date' => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
            'aps_person' => $request->aps_person,
            'aps_last_date' => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
            'finance_detail' => $request->finance_detail,
            'destination' => $request->destination,
            'user_id' => Auth::user()->id,
            'itf_current_date' => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
            'tdp_current_date' => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
            'tds_current_date' => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),
        ));

        // $code = code::find($id);
        // $code->ref_no =  $request->ref_no;
        // $code->brand_name =  $request->brand_name;
        // $code->season_id         = $request->season_id;
        // $code->agency_booking    = $request->agency_booking;
        // $code->pax_no            = $request->pax_no;
        // $code->date_of_travel    = Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d');
        // $code->category             = $request->category;
        // $code->supplier             = $request->supplier;
        // $code->product              = $request->product;
        // $code->flight_booked        = $request->flight_booked;
        // $code->fb_airline_name_id   = $request->fb_airline_name_id;
        // $code->fb_payment_method_id = $request->fb_payment_method_id;
        // $code->fb_booking_date      = Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d');
        // $code->fb_airline_ref_no    = $request->fb_airline_ref_no;
        // $code->fb_last_date         = Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d');
        // $code->fb_person            = $request->fb_person;
        // $code->aft_last_date        = Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d');
        // $code->aft_person          = $request->aft_person;
        // $code->ds_last_date        = Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d');
        // $code->ds_person           = $request->ds_person;
        // $code->to_last_date        = Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d');
        // $code->to_person           = $request->to_person;
        // $code->document_prepare    = $request->document_prepare;
        // $code->dp_last_date        = Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d');
        // $code->dp_person           = $request->dp_person;
        // $code->save();

        // $booking_id = code::update([
        //     'ref_no'                      => $request->ref_no,
        //     'brand_name'                  => $request->brand_name,
        //     'season_id'                   => $request->season_id,
        //     'agency_booking'              => $request->agency_booking,
        //     'pax_no'                      => $request->pax_no,
        //     'date_of_travel'              => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
        //     'category'                    => $request->category,
        //     'supplier'                    => $request->supplier,
        //     'product'                     => $request->product,
        //     'flight_booked'               => $request->flight_booked,
        //     'fb_airline_name_id'          => $request->fb_airline_name_id,
        //     'fb_payment_method_id'        => $request->fb_payment_method_id,
        //     'fb_booking_date'             => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
        //     'fb_airline_ref_no'           => $request->fb_airline_ref_no,
        //     'fb_last_date'                => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
        //     'fb_person'                   => $request->fb_person,
        //     //
        //     'aft_last_date'                => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
        //     'aft_person'                   => $request->aft_person,
        //     'ds_last_date'                 => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
        //     'ds_person'                    => $request->ds_person,
        //     'to_last_date'                 => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
        //     'to_person'                    => $request->to_person,
        //     //
        //     'document_prepare'             => $request->document_prepare,
        //     'dp_last_date'                 => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
        //     'dp_person'                    => $request->dp_person,
        //     //
        //     //
        //     'flight_booking_details'      => $request->flight_booking_details,
        //     'asked_for_transfer_details'  => $request->asked_for_transfer_details,
        //     'transfer_details'            => $request->transfer_details,
        //     'form_sent_on'                => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
        //     'form_received_on'            => $form_received_on,
        //     'app_login_date'              => $app_login_date,
        //     // 'transfer_info_received'      => $request->transfer_info_received,
        //     // 'transfer_info_details'       => $request->transfer_info_details,
        //     'itinerary_finalised'         => $request->itinerary_finalised,
        //     'itinerary_finalised_details' => $request->itinerary_finalised_details,
        //     'itf_last_date'               => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
        //     'itf_person'                  => $request->itf_person,
        //     'documents_sent'              => $request->documents_sent,
        //     'documents_sent_details'      => $request->documents_sent_details,
        //     'electronic_copy_sent'        => $request->electronic_copy_sent,
        //     'electronic_copy_details'     => $request->electronic_copy_details,
        //     'transfer_organised'          => $request->transfer_organised,
        //     'transfer_organised_details'  => $request->transfer_organised_details,
        //     'type_of_holidays'            => $request->type_of_holidays,
        //     'sale_person'                 => $request->sale_person,
        //     'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
        //     'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
        //     'fso_person'                  => $request->fso_person,
        //     'fso_last_date'               => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
        //     'aps_person'                  => $request->aps_person,
        //     'aps_last_date'               => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
        //     'finance_detail'              => $request->finance_detail,
        //     'destination'                 => $request->destination,
        //     'user_id'                     => Auth::user()->id,
        //     'itf_current_date'            => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
        //     'tdp_current_date'            => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
        //     'tds_current_date'            => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),

        // ])->where('id',$id);

        return Redirect::route('view-code')->with('success_message', 'Code Successfully Updated!!');

    }

    public function edit_code(Request $request, $id)
    {

        $code = code::find($id);

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
            $output =  $this->curl_data($url);
            //   return json_decode($output)->data;
        });

        // return view('code.create-code')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(), 'persons' => user::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'booking_email' => $booking_email, 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(),'suppliers' => Supplier::all()]);
        $booking_email = booking_email::where('booking_id', '=', 1)->get();

        return view('code.edit-code')->with([ 'code' => $code, 'get_user_branches' => $get_user_branches, 'get_holiday_type' => $get_holiday_type, 'get_user_branches' => $get_user_branches, 'codes' => $code, 'seasons' => season::all(), 'persons' => user::all(), 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(), 'suppliers' => Supplier::all(), 'booking_email' => $booking_email]);
    }

    public function get_supplier(Request $request)
    {

        $supplier_category = supplier_category::where('category_id', $request->category_id)
            ->select('suppliers.id', 'suppliers.name')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_categories.supplier_id')
            ->get();

        return $supplier_category;
    }

    public function get_product_details(Request $request)
    {

        $product = Product::find($request->product_id);
        return $product;

        // $supplier_category = supplier_category::where('category_id',$request->category_id)
        // ->select('suppliers.id','suppliers.name')
        // ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_categories.supplier_id')
        // ->get();
        // return $supplier_category;
    }

    public function get_supplier_currency(Request $request)
    {

        $supplier_currency = Supplier::leftJoin('currencies', 'currencies.id', '=', 'suppliers.currency_id')->where('suppliers.id', $request->supplier_id)->first();
 
        $supplier_products = Supplier::find($request->supplier_id)->products;

        return array('supplier_currency' => $supplier_currency, 'supplier_products' => $supplier_products);
    }

    public function get_saleagent_supervisor(Request $request)
    {

        $saleagent_supervisor = User::where('id', $request->booked_by)->first();
        return $saleagent_supervisor;
    }

    public function get_currency(Request $request)
    {

        $test = CurrencyConversions::where('to', $request->to)->get(['from', 'value']);

        $arr = [];
        foreach ($test as $test) {
            $arr[$test->from] = $test->value;
        }

        return $arr;
    }

    public function delete_code($id){
        code::destroy($id);
        return Redirect::route('view-code')->with('success_message', 'Code Successfully Deleted!!');
    }
    
    
    public function childReference(Request $request, $refNumber)
    {
        $data['quotes'] = Qoute::where('ref_no', $refNumber)->where('id', '!=' ,$request->id)->orderBy('created_at')->get();
        return response()->json(View::make('qoute.partial_quote_child', $data)->render());
    }
}
