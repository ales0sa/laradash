<?php
use Illuminate\Support\Facades\Route;

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


    Route::get('/adm/home', 'HomeController@index');
    Route::get('/adm/webstats', 'HomeController@webstats');


    Route::group([
        'prefix'     => config('laradash.prefix', 'adm'),
    ], function() {
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('.login');
        Route::post('login', 'Auth\LoginController@login');
        Route::post('logout', 'Auth\LoginController@logout');
        Route::get('logout', 'Auth\LoginController@logout')->name('.logout');
    });



    Route::group([
        'prefix'     => config('laradash.prefix', 'adm'),
        'as'         => 'admin',
        'middleware' => 'auth',
        //'namespace'  => 'Admin',
    ], function() {



        Route::get('/', 'HomeController@index')->name('.home');
        Route::get('/whoami', 'HomeController@whoami')->name('.whoami');
        Route::get('/api/menu', 'HomeController@menu')->name('.menu');
        Route::get('/symlink', 'HomeController@symlinkgenerator')->name('.symlink');
        Route::get('/ccache', 'HomeController@ccache')->name('.ccache');
        Route::get ('/dbtables', 'CrudGeneratorController@dbtables')->name('.dbtables');
        Route::get ('/dbtables/{tablename}', 'CrudGeneratorController@dbgetcols')->name('.dbgetcols');
        Route::post ('/jsonfromdb', 'CrudGeneratorController@jsonfromdb')->name('.jsonfromdb');
        //Route::get('/api/groups', 'HomeController@menu')->name('.menu');

        Route::get('/loginas/{id}', 'HomeController@loginas')->name('.loginas');


        Route::group([
            'prefix' => 'crud-generator',
            'as' => '.crud-generator',
        ], function() {
            Route::get ('/', 'CrudGeneratorController@index');
            Route::get ('/cg', 'CrudGeneratorController@index2')->name('.index');
            
            Route::get ('/create', 'CrudGeneratorController@create')->name('.create');
            Route::post('/', 'CrudGeneratorController@store')->name('.store');
            Route::get ('/{id}/edit', 'CrudGeneratorController@edit')->name('.edit');
            Route::post('/{id}', 'CrudGeneratorController@update')->name('.update');
            //
            Route::get ('/{id}/delete', 'CrudGeneratorController@destroy')->name('.destroy');
            Route::get ('/trash', 'CrudGeneratorController@trash')->name('.trash');
            Route::get ('/{id}/restore', 'CrudGeneratorController@restore')->name('.restore');
            //
            Route::get ('api/data/{id?}', 'CrudGeneratorController@data')->name('.data');
        });

        Route::group([
            'prefix' => 'crud',
            'as' => '.crud',
        ], function() {
            Route::get ('/{tablename}', 'CrudController@index');
            Route::get ('/{tablename}/create', 'CrudController@create')->name('.create');
            Route::post('/{tablename}/{id?}', 'CrudController@store')->name('.store');
            Route::get ('/{tablename}/{id}/edit', 'CrudController@edit')->name('.edit');
            Route::delete ('/{tablename}/{id}/clean/{col}', 'CrudController@clean')->name('.clean');
            Route::post ('/{tablename}/{id}/upload/{col}', 'CrudController@upload')->name('.upload');
            //
            Route::get ('/{tablename}/{id}/delete', 'CrudController@destroy')->name('.destroy');
            Route::get ('/{tablename}/trash', 'CrudController@trash')->name('.trash');
            Route::get ('/{tablename}/{id}/restore', 'CrudController@restore')->name('.restore');
            //
            Route::get ('/{tablename}/data/{id?}', 'CrudController@data')->name('.data');
            Route::get ('/{tablename}/{id}/copy', 'CrudController@copy')->name('.copy');
            // single row
            Route::get ('/{tablename}/{id}/data', 'CrudController@sr')->name('.sr');
        });

        Route::group([
            'prefix' => 'user',
            'as' => '.user',
        ], function() {
            Route::get ('/', 'UserController@index');

            Route::get ('/groups', 'UserController@groups')->name('.groups');
            Route::get ('/list', 'UserController@users')->name('.list');

            Route::get ('/create', 'UserController@create')->name('.create');
            Route::post('/', 'UserController@store')->name('.store');
            Route::get ('/{id}/edit', 'UserController@edit')->name('.edit');
            Route::post('/{id}', 'UserController@update')->name('.update');
            //
            Route::get ('/{id}/delete', 'UserController@destroy')->name('.destroy');
            Route::get ('/trash', 'UserController@trash')->name('.trash');
            Route::get ('/{id}/restore', 'UserController@restore')->name('.restore');
            //
            Route::get ('/{id}/permission', 'UserController@permission')->name('.permission');
            Route::post('/{id}/permission', 'UserController@updatePermission')->name('.permission.update');

        });
        
        Route::group([
            'prefix' => 'profile',
            'as' => '.profile',
        ], function() {
            Route::get ('/', 'ProfileController@index');
            Route::post('/', 'ProfileController@update')->name('.update');
        });
        Route::group([
            'prefix' => 'permission',
            'as' => '.permission',
        ], function() {
            Route::get ('/', 'PermissionController@index');
        });
        Route::group([
            'prefix' => 'groups',
            'as' => '.groups',
        ], function() {
            Route::get ('/', 'GrupoController@index');
            Route::get ('/create', 'GrupoController@create')->name('.create');
            Route::post('/', 'GrupoController@store')->name('.store');
            Route::get ('/{id}/edit', 'GrupoController@edit')->name('.edit');
            Route::post('/{id}', 'GrupoController@update')->name('.update');
            //
            Route::get ('/{id}/delete', 'GrupoController@destroy')->name('.destroy');
            Route::get ('/trash', 'GrupoController@trash')->name('.trash');
            Route::get ('/{id}/restore', 'GrupoController@restore')->name('.restore');
            //
            Route::get ('/{id}/permission', 'GrupoController@permission')->name('.permission');
            Route::post('/{id}/permission', 'GrupoController@updatePermission')->name('.permission.update');
        });
        Route::group([
            'prefix' => 'content',
            'as' => '.content',
        ], function() {
            Route::get('{seccion}', ['uses' => 'ContentController@home', 'as' => '']);
            Route::post('store', ['uses' => 'ContentController@store', 'as' => '.store']);
        });
        Route::group([
            'prefix' => 'dynamic-content',
            'as' => '.dynamic-content',
        ], function() {
            Route::get ('{seccion}', 'DynamicContentController@index');
            Route::get ('{seccion}/create', 'DynamicContentController@create')->name('.create');
            Route::post('{seccion}/', 'DynamicContentController@store')->name('.store');
            Route::get ('/{id}/edit', 'DynamicContentController@edit')->name('.edit');
            Route::post('/{id}/update', 'DynamicContentController@update')->name('.update');
            //
            Route::get ('/{id}/delete', 'DynamicContentController@destroy')->name('.destroy');
            Route::get ('/{seccion}/trash', 'DynamicContentController@trash')->name('.trash');
            Route::get ('/{id}/restore', 'DynamicContentController@restore')->name('.restore');
            //
            Route::get ('api/{seccion}/data/{id?}', 'DynamicContentController@data')->name('.data');
            Route::get ('/{id}/copy', 'DynamicContentController@copy')->name('.copy');
        });
        Route::group([
            'prefix' => 'translation',
            'as' => '.translation',
        ], function() {
            Route::get ('/', 'TranslationController@index');
            Route::get ('/create', 'TranslationController@create')->name('.create');
            Route::post('/', 'TranslationController@store')->name('.store');
            Route::get ('/{id}/edit', 'TranslationController@edit')->name('.edit');
            Route::post('/{id}', 'TranslationController@update')->name('.update');
            //
            Route::get ('/{id}/delete', 'TranslationController@destroy')->name('.destroy');
            Route::get ('/trash', 'TranslationController@trash')->name('.trash');
            Route::get ('/{id}/restore', 'TranslationController@restore')->name('.restore');
        });
        Route::group([
            'prefix' => 'seo',
            'as' => '.seo',
        ], function() {
            Route::get ('/', 'SeoController@index');
            Route::get ('/{id}/edit', 'SeoController@edit')->name('.edit');
            Route::post('/{id}', 'SeoController@update')->name('.update');
        });
        Route::group([
            'prefix' => 'company-data',
            'as' => '.company-data',
        ], function() {
            Route::get ('/', 'CompanyDataController@index');
            Route::post('/update', 'CompanyDataController@update')->name('.update');
            Route::get('/api/data', 'CompanyDataController@data')->name('.data');
            
        });
    });