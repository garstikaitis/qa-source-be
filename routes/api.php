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

//TODO 
// Secure files route
Route::group(['prefix' => 'files'], function() {
    Route::get('/{type}/{fileId}', 'FileController@downloadFile')->name('downloadFile');
});
Route::group(['middleware' => 'auth:api'], function() {
    Route::group(['prefix' => 'chat'], function() {
        Route::post('/', 'ChatController@chat')->name('chat');
        Route::post('/token', 'ChatController@generateChatToken')->name('generateChatToken');
    });
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', 'TaskController@getTasks')->name('getTasks');
        Route::post('/', 'TaskController@getTask')->name('getTask');
        Route::post('/create', 'TaskController@createTask')->name('createTask');
        Route::post('/rate', 'TaskController@rateTask')->name('rateTask');
    });
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UserController@getUsers')->name('getUsers');
        Route::get('/me', 'UserController@getUserData')->name('getUserData');
    });
    Route::group(['prefix' => 'companies'], function () {
        Route::get('/', 'CompanyController@getCompanies')->name('getCompanies');
        Route::post('/create', 'CompanyController@createCompany')->name('createCompany');
        Route::post('/update', 'CompanyController@updateCompany')->name('updateCompany');
        Route::post('/add-user', 'CompanyController@addUserToCompany')->name('addUserToCompany');
    });
    Route::group(['prefix' => 'projects'], function() {
        Route::get('/', 'ProjectController@getProjects')->name('getProjects');
        Route::post('/', 'ProjectController@getProject')->name('getProject');
        Route::post('/take', 'ProjectController@applyToProject')->name('applyToProject');
        Route::post('/return', 'ProjectController@returnProject')->name('returnProject');
    });
});


Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

