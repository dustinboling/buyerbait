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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('extensions', 'ExtensionController')->only([
    'index', 'show'
]);

Route::group(['prefix' => 'hello'], function () {
    Route::any('/greeting', 'IvrController@greeting')->name('greeting');
    Route::any('extension-message', 'IvrController@extensionMessage')->name('extension-message');
    Route::any('connect-agent', 'IvrController@connectAgent')->name('connect-agent');

    Route::any('test/{$number}', 'IvrController@testGetExtensionMessage');

    Route::any('conf-greeting', 'ConferenceController@greeting')->name('conf-greeting');
    Route::any('agent-called', 'AgentIvrController@agentCalled')->name('agent-called');
});
