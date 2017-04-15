<?php

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

Route::get('/', 'HomeController@homeAction')->name('home');
Route::get('/home', 'HomeController@homeAction');

Route::get('/{page}.html', 'PageController@view')->name('view_page');
Route::get('/file/{file}', 'FileController@display')->name('view_file');
Route::get('/download/{securite}/{billet}.pdf', 'BilletController@download')->name('download_billet');

Route::group(['prefix' => 'login'], function () {
    Route::get('/', 'EtuUTTController@login')->name('login')->middleware('guest');
    Route::get('/callback', 'EtuUTTController@callback');
    Route::get('/off', 'EtuUTTController@logout')->name('logout');
});

Route::group(['prefix' => 'caddie'], function () {
    Route::get('/', 'OrderController@getCaddie')->name('getCaddie');
    Route::get('/reset', 'OrderController@resetCaddie')->name('resetCaddie');
    Route::post('/paiement', 'PaiementController@postCaddie')->name('postCaddie');
});

Route::group(['prefix' => 'billet'], function () {
    Route::get('add', 'OrderController@getNewItem')->name('addNewItem');
    Route::post('add', 'OrderController@postNewItem');
    Route::post('process', 'OrderController@processNewItem')->name('processNewItem');
    Route::post('resend', 'BilletController@postSendAgain')->name('billet_resend');
});

Route::group(['prefix' => 'admin', 'middleware' => ['right:seller']], function () {
    Route::get('sell', 'BilletController@adminSell')->name('admin_sell');

    //Admin
    Route::get('users', 'UserController@index')->name('admin_users_index');
    Route::get('users/{user}/level/{level}', 'UserController@changeLevel')->name('admin_users_level');
    //Prices
    Route::get('prices', 'PriceController@index')->name('admin_prices_index');
    Route::post('prices', 'PriceController@create');

    Route::post('prices/lists/lin/{price}', 'PriceController@linkListe')->name('admin_prices_lists_link');
    Route::get('prices/lists/remove/{price}/{liste}', 'PriceController@removeListe')->name('admin_prices_lists_delete');

    Route::get('prices/edit/{price}', 'PriceController@edit')->name('admin_prices_edit');
    Route::post('prices/edit/{price}', 'PriceController@edit');
    // ORDER
    Route::get('orders', 'OrderController@adminOrderList')->name('admin_orders_list');
    Route::get('orders/create', 'OrderController@adminGetCreateNewOrder')->name('admin_orders_create');
    Route::post('orders/create', 'OrderController@adminPostCreateNewOrder')->name('admin_orders_create_post');
    Route::get('orders/express/{place_id?}', 'OrderController@adminExpressOrder')->name('admin_express_orders');

    Route::get('billet/sendMail/{id}', 'BilletController@adminSendMail')->name('admin_billet_mail');
    Route::get('billet/validation/{id}', 'BilletController@adminValid')->name('admin_billet_validate');
    Route::get('billet/view/{billet}', 'BilletController@adminView')->name('admin_billet_view');


    //Guichet
    Route::get('guichet/create', 'GuichetController@getAdminGuichet')->name('admin_guichet_create');
    Route::post('guichet/create', 'GuichetController@postAdminGuichet')->name('post_admin_guichet_create');

    Route::get('guichet', 'GuichetController@getIndex')->name('admin_guichet');
    Route::get('guichet/autocomplete', 'GuichetController@getAutocomplete')->name('guichet_autocomplete');
    Route::get('guichet/validate', 'GuichetController@validateTicket')->name('guichet_validate');

    Route::get('guichet/vente/{uuid}', 'GuichetController@getSellGuichet')->name('get_sell_guichet');
    Route::post('guichet/vente/{uuid}', 'GuichetController@postSellGuichet')->name('post_sell_guichet');

    //Listes
    Route::get('lists/items/add', 'ListeController@addItemAction')->name('lists_items_add');
    Route::post('lists/items/add', 'ListeController@addItemAction')->name('post_lists_items_add');

    //Pages
    Route::get('pages', 'PageController@getAdminList')->name('lists_pages');
    Route::get('pages/create', 'PageController@create')->name('create_page');
    Route::post('pages/create', 'PageController@create');

    Route::get('pages/edit/{page}', 'PageController@edit')->name('edit_page');
    Route::post('pages/edit/{page}', 'PageController@edit');

    Route::get('pages/tooglePublication/{page}', 'PageController@tooglePublication')->name('toogle_page');
    Route::get('pages/setHomepage/{page}', 'PageController@setHomepage')->name('set_homepage_page');

    Route::post('pages/store', 'PageController@store')->name('post_store_page');

    // Files
    Route::post('file/upload', 'FileController@upload')->name('upload_file');

    //Mail template
    Route::get('mail/template', 'MailTemplateController@getAdminList')->name('lists_mail_templates');
    Route::get('mail/template/view/{mail_template}', 'MailTemplateController@view')->name('view_mail_template');

    Route::get('mail/template/edit/{mail_template}', 'MailTemplateController@edit')->name('edit_mail_template');
    Route::post('mail/template/edit/{mail_template}', 'MailTemplateController@edit');

    Route::get('mail/template/toogleActive/{mail_template}', 'MailTemplateController@toogleActivation')->name('toogle_mail_template');
});

Route::group(['prefix' => 'etupay'], function () {
    Route::get('return', 'EtuPayController@etupayReturn');
    Route::post('callback', 'EtuPayController@etupayCallback');
    //Route::get('callback', 'EtuPayController@etupayCallback');
});