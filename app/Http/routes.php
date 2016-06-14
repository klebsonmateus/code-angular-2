<?php

use \Illuminate\Support\Facades\Response,
    \LucaDegasperi\OAuth2Server\Facades\Authorizer;

Route::get('/', function () {
    return view('app');
});

Route::post('oauth/access_token', function(){
    return Response::json(Authorizer::issueAccessToken());
});


Route::group(['middleware'=>'oauth'], function(){

    Route::resource('client', 'ClientController', ['except'=>['create','edit']]);

    Route::resource('project', 'ProjectController', ['except'=>['create','edit']]);


    Route::group(['middleware' => 'check.project.permission', 'prefix'=>'project'], function(){
        
        Route::get('{id}/note', 'ProjectNoteController@index');
        Route::post('{id}/note', 'ProjectNoteController@store');
        Route::get('{id}/note/{noteId}', 'ProjectNoteController@show');
        Route::put('{id}/note/{noteId}', 'ProjectNoteController@update');
        Route::delete('{id}/note/{noteId}', 'ProjectNoteController@destroy');


        Route::get('{id}/file', 'ProjectFileController@index');
        Route::get('{id}/file/{fileId}', 'ProjectFileController@show');
        Route::get('{id}/file/{fileId}/download', 'ProjectFileController@showFile');
        Route::post('{id}/file', 'ProjectFileController@store');
        Route::put('{id}/file/{fileId}', 'ProjectFileController@update');
        Route::delete('{id}/file/{fileId}', 'ProjectFileController@destroy');

        Route::get('project/{id}/member', 'ProjectController@members');
        Route::post('project/{id}/member/{member_id}', 'ProjectController@addMember');
        Route::delete('project/{id}/member/{member_id}', 'ProjectController@removeMember');

        Route::get('project/{id}/task', 'ProjectController@tasks');
        Route::post('project/{id}/task', 'ProjectController@addTask');
        Route::delete('project/{id}/task/{task_id}', 'ProjectController@removeTask');

    });

    Route::get('user/authenticated', 'UserController@authenticated');
});


