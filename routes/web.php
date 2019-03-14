<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','HomeController@index');

Route::get('file-upload', 'FileController@fileUpload');
Route::post('file-upload', 'FileController@fileUploadPost')->name('fileUploadPost');
Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin'], function(){
	Route::resource('permissions', 'Admin\PermissionController');
	Route::resource('roles','Admin\RolesController');
	Route::resource('users','Admin\UsersController');
});

Route::group(['prefix' => 'materiais', 'as'=>'materiais.'], function(){
	Route::group(['prefix' => 'cadastro_materiais', 'as'=>'cadastro_materiais.'], function(){
		Route::get('/', 'Materiais\MaterialController@index');
		Route::get('/getData', 'Materiais\MaterialController@getData')->name('getData');
		Route::post('/store', 'Materiais\MaterialController@store')->name('store');
		Route::get('/edit', 'Materiais\MaterialController@edit')->name('edit');
		Route::post('/update', 'Materiais\MaterialController@update')->name('update');
		Route::post('/delete', 'Materiais\MaterialController@delete')->name('delete');
	});
	Route::group(['prefix' => 'demanda', 'as'=>'demanda.'], function(){
		Route::get('/', 'Materiais\DemandaController@index');
		Route::get('/getData', 'Materiais\DemandaController@getData')->name('getData');
		Route::get('/create', 'Materiais\DemandaController@create')->name('create');
		Route::post('/store_file', 'Materiais\DemandaController@storeFile')->name('store_file');
		Route::post('/store', 'Materiais\DemandaController@store')->name('store');
		Route::get('/edit', 'Materiais\DemandaController@edit')->name('edit');
		Route::post('/store_acrescimo', 'Materiais\DemandaController@store_acrescimo')->name('store_acrescimo');
		Route::post('/update', 'Materiais\DemandaController@update')->name('update');
		Route::get('/export', 'Materiais\DemandaController@export')->name('export');
		Route::post('/delete', 'Materiais\DemandaController@delete')->name('delete');
		Route::get('/get_codigo_material_select', 'Materiais\DemandaController@getCodigoMaterial')->name('get_codigo_material_select');
		Route::get('/get_descricao_material_select', 'Materiais\DemandaController@getDescricaoMaterial')->name('get_descricao_material_select');
	});
});


//Route::resource('admin.users', 'Admin\UsersController');
