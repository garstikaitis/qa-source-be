<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function() {
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', 'TaskController@getTasks')->name('getTasks');
        Route::post('/', 'TaskController@getTask')->name('getTask');
        Route::post('/create', 'TaskController@createTask')->name('createTask');
    });
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UserController@getUsers')->name('getUsers');
    });
    Route::group(['prefix' => 'companies'], function () {
        Route::get('/', 'CompanyController@getCompanies')->name('getCompanies');
        Route::post('/create', 'CompanyController@createCompany')->name('createCompany');
        Route::post('/update', 'CompanyController@updateCompany')->name('updateCompany');
        Route::post('/add-user', 'CompanyController@addUserToCompany')->name('addUserToCompany');
    });
    Route::group(['prefix' => 'projects'], function() {
        Route::get('/', 'ProjectController@getProjects')->name('getProjects');
        Route::post('/take', 'ProjectController@applyToProject')->name('applyToProject');
        Route::post('/return', 'ProjectController@returnProject')->name('returnProject');
    });
});


Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

