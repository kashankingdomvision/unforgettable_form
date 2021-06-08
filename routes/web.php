<?php

header('Access-Control-Allow-Origin: *');
header( 'Access-Control-Allow-Headers: Authorization, Content-Type');
header( 'Access-Control-Allow-Methods: GET,POST,PUT,DELETE');

use Spatie\GoogleCalendar\Event;

    Route::get('/test', function () {
        // return view('welcome');
        // $events = Event::get();

        //create a new event
        $event = new Event;

        $event->name = 'A nedfsdfsdfw event';
        $event->description = 'Event description';
        $event->startDateTime = Carbon\Carbon::now();
        $event->endDateTime = Carbon\Carbon::now()->addHour();
        
        // $event->addAttendee(['email' => 'kashan.mehmood13@gmail.com']);

        $event->save();

        // dd($events);
    });


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

//////////////////////////////////////////     All Routes related to Get method ////////////////////////
Auth::routes();
Route::get('update-email',array('as'=>'update-email','uses'=>'Auth\LoginController@update_email'));
// Route::get('/',array('as'=>'login_submit','uses'=>'Auth\LoginController@Login'));
// Route::get('permission',array('as'=>'user-permission','uses'=>'PermissionController@user_permission'));
Route::group(['middleware' => ['auth']], function(){
	Route::get('/',array('as'=>'admin','uses'=>'AdminController@index'));
	Route::get('logout',array('as'=>'logout','uses'=>'AdminController@logout'));
	Route::get('add-fatwa',array('before'=>'csrf','as'=>'add-fatwa','uses'=>'AdminController@add_fatwa'));
	Route::get('update-fatwa/{id}',array('as'=>'update-fatwa','uses'=>'AdminController@update_fatwa'));
	Route::get('view-fatwa',array('as'=>'view-fatwa','uses'=>'AdminController@view_fatwa'));
	Route::get('del-fatwa/{id}',array('as'=>'del-fatwa','uses'=>'AdminController@delete_fatwa'));
	Route::get('activate-fatwa/{id}',array('as'=>'activate-fatwa','uses'=>'AdminController@publish_fatwa'));
	Route::get('deactivate-fatwa/{id}',array('as'=>'deactivate-fatwa','uses'=>'AdminController@unpublish_fatwa'));

	// category 
	Route::get('creat-category',array('as'=>'creat-category','uses'=>'AdminController@create_category'));
	Route::get('view-category',array('as'=>'view-category','uses'=>'AdminController@view_category'));
	Route::get('update-category/{id}',array('as'=>'update-category','uses'=>'AdminController@update_category'));
	Route::get('del-category/{id}',array('as'=>'del-category','uses'=>'AdminController@delete_category'));
	Route::match(['get', 'post'],'add-supplier',array('as'=>'add-supplier','uses'=>'AdminController@add_supplier'));
	Route::match(['get', 'post'],'view-supplier',array('as'=>'view-supplier','uses'=>'AdminController@view_supplier'));
	Route::match(['get', 'post'],'update-supplier/{id}',array('as'=>'update-supplier','uses'=>'AdminController@update_supplier'));
	Route::match(['get', 'post'],'delete-supplier/{id}',array('as'=>'delete-supplier','uses'=>'AdminController@delete_supplier'));
	Route::match(['get', 'post'],'add-category',array('as'=>'add-category','uses'=>'AdminController@add_category'));
	Route::match(['get', 'post'],'view-category',array('as'=>'view-category','uses'=>'AdminController@view_category'));
	Route::match(['get', 'post'],'update-category/{id}',array('as'=>'update-category','uses'=>'AdminController@update_category'));
	Route::match(['get', 'post'],'delete-category/{id}',array('as'=>'delete-category','uses'=>'AdminController@delete_category'));
	Route::match(['get', 'post'],'add-product',array('as'=>'add-product','uses'=>'AdminController@add_product'));
	Route::match(['get', 'post'],'view-product',array('as'=>'view-product','uses'=>'AdminController@view_product'));
	Route::match(['get', 'post'],'update-product/{id}',array('as'=>'update-product','uses'=>'AdminController@update_product'));
	Route::match(['get', 'post'],'delete-product/{id}',array('as'=>'delete-product','uses'=>'AdminController@delete_product'));
	Route::match(['get', 'post'],'add-role',array('as'=>'add-role','uses'=>'AdminController@add_role'));
	Route::match(['get', 'post'],'view-role',array('as'=>'view-role','uses'=>'AdminController@view_roles'));
	Route::match(['get', 'post'],'del-role/{id}',array('as'=>'del-role','uses'=>'AdminController@del_role'));
	Route::match(['get', 'post'],'update-role/{id}',array('as'=>'update-role','uses'=>'AdminController@update_role'));
	Route::match(['get', 'post'],'creat-code',array('as'=>'creat-code','uses'=>'AdminController@create_code'));
	Route::match(['get', 'post'],'view-code',array('as'=>'view-code','uses'=>'AdminController@view_code'));
	
	
    // Route::get('view-supplier-products',array('as'=>'view-supplier-products','uses'=>'AdminController@view_supplier_products'));
    // Route::get('view-supplier-categories',array('as'=>'view-supplier-categories','uses'=>'AdminController@view_supplier_categories'));

    Route::get('details-supplier/{id}',array('as'=>'details-supplier','uses'=>'AdminController@details_supplier'));

	Route::get('edit-code/{id}',array('as'=>'edit-code','uses'=>'AdminController@edit_code'));
	Route::put('update-code/{id}',array('as'=>'update-code','uses'=>'AdminController@update_code'));
	
	Route::match(['get','post'],'delete-code/{id}',array('as'=>'delete-code','uses'=>'AdminController@delete_code'));
	Route::post('get-supplier',array('as'=>'get-supplier','uses'=>'AdminController@get_supplier'));
	Route::post('get-supplier-currency',array('as'=>'get-supplier-currency','uses'=>'AdminController@get_supplier_currency'));
    Route::post('get-saleagent-supervisor',array('as'=>'get-saleagent-supervisor','uses'=>'AdminController@get_saleagent_supervisor'));
	Route::post('get-currency',array('as'=>'get-currency','uses'=>'AdminController@get_currency'));
	//end category


    Route::match(['get', 'post'],'creat-quote',array('as'=>'creat-quote','uses'=>'AdminController@create_quote'));
    Route::match(['get', 'post'],'view-quote',array('as'=>'view-quote','uses'=>'AdminController@view_quote'));
    Route::match(['get', 'post'],'edit-quote/{id}',array('as'=>'edit-quote','uses'=>'AdminController@edit_quote'));
    Route::get('delete-quote/{id}',array('as'=>'delete-quote','uses'=>'AdminController@delete_quote'));
    
	Route::match(['get', 'post'],'confirm-booking/{id}',array('as'=>'confirm-booking','uses'=>'AdminController@booking'));

	Route::match(['get', 'post'],'upload-to-calendar',array('as'=>'upload-to-calendar','uses'=>'AdminController@upload_to_calendar'));
	Route::match(['get', 'post'],'export_to_csv/{id}',array('as'=>'export_to_csv','uses'=>'AdminController@export_to_csv'));

    Route::get('view-version/{quote_id}/{log_no}',array('as'=>'view-version','uses'=>'AdminController@view_version'));

    Route::get('recall-version/{quote_id}/{log_no}',array('as'=>'recall-version','uses'=>'AdminController@recall_version'));

    Route::match(['get', 'post'],'creat-booking-method',array('as'=>'creat-booking-method','uses'=>'AdminController@booking_method'));
    Route::get('view-booking-method',array('as'=>'view-booking-method','uses'=>'AdminController@view_booking_method'));
    Route::match(['get', 'post'],'edit-booking-method/{id}',array('as'=>'edit-booking-method','uses'=>'AdminController@edit_booking_method'));
    Route::get('del-booking-method/{id}',array('as'=>'del-booking-method','uses'=>'AdminController@del_booking_method'));

	// replier 
	Route::get('creat-replier',array('as'=>'creat-replier','uses'=>'AdminController@create_replier'));
	Route::get('view-replier',array('as'=>'view-replier','uses'=>'AdminController@view_replier'));
	Route::get('update-replier/{id}',array('as'=>'update-replier','uses'=>'AdminController@update_replier'));
	Route::get('del-replier/{id}',array('as'=>'del-replier','uses'=>'AdminController@delete_replier'));
	//end replier

	// book 
	Route::get('creat-book',array('as'=>'creat-book','uses'=>'AdminController@create_book'));
	Route::get('view-book',array('as'=>'view-book','uses'=>'AdminController@view_book'));
	Route::get('update-book/{id}',array('as'=>'update-book','uses'=>'AdminController@update_book'));
	Route::get('del-book/{id}',array('as'=>'del-book','uses'=>'AdminController@delete_book'));
	//end book

	// chapter 
	Route::get('creat-chapter',array('as'=>'creat-chapter','uses'=>'AdminController@create_chapter'));
	Route::get('view-chapter',array('as'=>'view-chapter','uses'=>'AdminController@view_chapter'));
	Route::get('update-chapter/{id}',array('as'=>'update-chapter','uses'=>'AdminController@update_chapter'));
	Route::get('del-chapter/{id}',array('as'=>'del-chapter','uses'=>'AdminController@delete_chapter'));
	//end chapter

	// mufti 
	Route::get('creat-mufti',array('as'=>'creat-mufti','uses'=>'AdminController@create_mufti'));
	Route::get('view-mufti',array('as'=>'view-mufti','uses'=>'AdminController@view_mufti'));
	Route::get('update-mufti/{id}',array('as'=>'update-mufti','uses'=>'AdminController@update_mufti'));
	Route::get('del-mufti/{id}',array('as'=>'del-mufti','uses'=>'AdminController@delete_mufti'));
	//end mufti

	// mufti 
	Route::get('creat-user',array('as'=>'creat-user','uses'=>'AdminController@create_user'));
	Route::get('view-user',array('as'=>'view-user','uses'=>'AdminController@view_user'));
	Route::get('update-user/{id}',array('as'=>'update-user','uses'=>'AdminController@update_user'));
	Route::get('del-user/{id}',array('as'=>'del-user','uses'=>'AdminController@delete_user'));
	//end mufti

	// season 
	Route::match(['get', 'post'],'creat-season',array('as'=>'creat-season','uses'=>'AdminController@create_season'));
	Route::get('view-season',array('as'=>'view-season','uses'=>'AdminController@view_season'));
	Route::match(['get', 'post'],'update-season/{id}',array('as'=>'update-season','uses'=>'AdminController@update_season'));
	Route::get('del-season/{id}',array('as'=>'del-season','uses'=>'AdminController@delete_season'));
	//end season

	// Super Visor 
	Route::match(['get','post'],'create-supervisor',array('as'=>'create-supervisor','uses'=>'AdminController@create_supervisor'));
	Route::get('view-supervisor',array('as'=>'view-supervisor','uses'=>'AdminController@view_supervisor'));
	Route::match(['get', 'post'],'update-supervisor/{id}',array('as'=>'update-supervisor','uses'=>'AdminController@update_supervisor'));
	Route::get('del-supervisor/{id}',array('as'=>'del-supervisor','uses'=>'AdminController@delete_supervisor'));
	//end Super Visor

	// season 
	Route::match(['get','post'],'create-booking',array('as'=>'create-booking','uses'=>'AdminController@create_booking'));
	Route::get('view-booking/{id}',array('as'=>'view-booking','uses'=>'AdminController@view_booking'));
	Route::match(['get','post'],'update-booking/{id}',array('as'=>'update-booking','uses'=>'AdminController@update_booking'));
	Route::get('del-booking/{season_id}/{booking_id}',array('as'=>'del-booking','uses'=>'AdminController@delete_booking'));
	Route::post('del-multi-booking/{id}',array('as'=>'del-multi-booking','uses'=>'AdminController@delete_multi_booking'));

	Route::get('view-booking-season',array('as'=>'view-booking-season','uses'=>'AdminController@view_booking_season'));
	Route::get('del-booking-season/{id}',array('as'=>'del-booking-season','uses'=>'AdminController@delete_booking_season'));
	//end season

	//////////////////////////////////////////     All Routes related to Post method ////////////////////////

	// Route::post('login_submit',array('before'=>'csrf','as'=>'login_submit','uses'=>'Auth\RegisterController@Register'));
	Route::post('add-fatwa',array('before'=>'csrf','as'=>'add-fatwa','uses'=>'AdminController@add_fatwa'));
	Route::post('update-fatwa/{id}',array('before'=>'csrf','as'=>'update-fatwa','uses'=>'AdminController@update_fatwa'));
	Route::post('get-ref-detail',array('before'=>'csrf','as'=>'get-ref-detail','uses'=>'AdminController@get_ref_detail'));

	Route::get('refresh-token',array('before'=>'csrf','as'=>'refresh-token','uses'=>'AdminController@refresh_token'));

	// category
	Route::post('creat-category',array('before'=>'csrf','as'=>'creat-category','uses'=>'AdminController@create_category'));
	Route::post('update-category/{id}',array('before'=>'csrf','as'=>'update-category','uses'=>'AdminController@update_category'));
	// replier
	Route::post('creat-replier',array('before'=>'csrf','as'=>'creat-replier','uses'=>'AdminController@create_replier'));
	Route::post('update-replier/{id}',array('before'=>'csrf','as'=>'update-replier','uses'=>'AdminController@update_replier'));
	// book
	Route::post('creat-book',array('before'=>'csrf','as'=>'creat-book','uses'=>'AdminController@create_book'));
	Route::post('update-book/{id}',array('before'=>'csrf','as'=>'update-book','uses'=>'AdminController@update_book'));
	// chapter
	Route::post('creat-chapter',array('before'=>'csrf','as'=>'creat-chapter','uses'=>'AdminController@create_chapter'));
	Route::post('update-chapter/{id}',array('before'=>'csrf','as'=>'update-chapter','uses'=>'AdminController@update_chapter'));
	Route::post('view-chapter',array('as'=>'view-chapter','uses'=>'AdminController@view_chapter'));
	// mufti
	Route::post('creat-mufti',array('before'=>'csrf','as'=>'creat-mufti','uses'=>'AdminController@create_mufti'));
	Route::post('update-mufti/{id}',array('before'=>'csrf','as'=>'update-mufti','uses'=>'AdminController@update_mufti'));

	// mufti
	Route::post('creat-user',array('before'=>'csrf','as'=>'creat-user','uses'=>'AdminController@create_user'));
	Route::post('update-user/{id}',array('before'=>'csrf','as'=>'update-user','uses'=>'AdminController@update_user'));

	//airline
	Route::post('creat-airline',array('before'=>'csrf','as'=>'creat-airline','uses'=>'AdminController@create_airline'));
	Route::get('creat-airline',array('as'=>'creat-airline','uses'=>'AdminController@create_airline'));
	Route::get('view-airline',array('as'=>'view-airline','uses'=>'AdminController@view_airline'));
	Route::post('update-airline/{id}',array('before'=>'csrf','as'=>'update-airline','uses'=>'AdminController@update_airline'));
	Route::get('update-airline/{id}',array('as'=>'update-airline','uses'=>'AdminController@update_airline'));
	Route::get('del-airline/{id}',array('as'=>'del-airline','uses'=>'AdminController@delete_airline'));


	//payment

	Route::post('creat-payment',array('before'=>'csrf','as'=>'creat-payment','uses'=>'AdminController@create_payment'));
	Route::get('creat-payment',array('as'=>'creat-payment','uses'=>'AdminController@create_payment'));
	Route::get('view-payment',array('as'=>'view-payment','uses'=>'AdminController@view_payment'));
	Route::post('update-payment/{id}',array('before'=>'csrf','as'=>'update-payment','uses'=>'AdminController@update_payment'));
	Route::get('update-payment/{id}',array('as'=>'update-payment','uses'=>'AdminController@update_payment'));
	Route::get('del-payment/{id}',array('as'=>'del-payment','uses'=>'AdminController@delete_payment'));

});

// Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
