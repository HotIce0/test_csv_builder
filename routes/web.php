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
    //return utf8_encode('sao');
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    /* form */
    Route::Group(['prefix'=>'form'],function() {
        Route::get('/operateTypeSelect', 'FormController@operateTypeSelect')->name('operateTypeSelect');
        Route::get('/addDataFile', 'FormController@addDataFile')->name('addDataFile');
    });

    /* 管理所有csv */
    Route::Group(['prefix'=>'csv-data'],function() {
        Route::get('/downloadCSVFile/{id?}', 'HomeController@downloadCSVFile')->name('downloadCSVFile');

        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/allCSVFile', 'HomeController@allCSVFile')->name('allCSVFile');
        Route::get('/deleteCSVFile', 'HomeController@deleteCSVFile')->name('deleteCSVFile');
    });

    /* csvHeader */
    Route::Group(['prefix'=>'csv-header'],function() {
        Route::get('/', 'CSVHeaderController@CSVHeaderManage')->name('CSVHeaderManage');
        Route::get('/all', 'CSVHeaderController@CSVHeaderManageALL')->name('CSVHeaderManageALL');
        Route::get('/add', 'CSVHeaderController@CSVHeaderManageADD')->name('CSVHeaderManageADD');
        Route::get('/rename', 'CSVHeaderController@CSVHeaderManageRename')->name('CSVHeaderManageRename');
        Route::get('/operateType', 'CSVHeaderController@CSVHeaderManageOperateType')->name('CSVHeaderManageOperateType');

        Route::Group(['prefix'=>'edit'],function() {
            Route::get('/CSVHeaderFile', 'CSVHeaderController@CSVHeaderFile')->name('CSVHeaderFile');
            Route::get('/allCSVHeaderFileItem/{id}', 'CSVHeaderController@allCSVHeaderFileItem')->name('allCSVHeaderFileItem');
            Route::get('/addCSVHeaderFileItem/{id}', 'CSVHeaderController@addCSVHeaderFileItem')->name('addCSVHeaderFileItem');
            Route::get('/deleteCSVHeaderFileItem/{id}', 'CSVHeaderController@deleteCSVHeaderFileItem')->name('deleteCSVHeaderFileItem');
            Route::get('/editCSVHeaderFileItem/{id}', 'CSVHeaderController@editCSVHeaderFileItem')->name('editCSVHeaderFileItem');
            Route::get('/saveCSVHeaderFileItem/{id}', 'CSVHeaderController@saveCSVHeaderFileItem')->name('saveCSVHeaderFileItem');
            Route::get('/downloadCSVHeaderFile/{id?}', 'CSVHeaderController@downloadCSVHeaderFile')->name('downloadCSVHeaderFile');
        });
    });

    /* csvData */
    Route::Group(['prefix'=>'csv-data'],function() {
        Route::get('/', 'CSVDataController@CSVDataManage')->name('CSVDataManage');
        Route::get('/all', 'CSVDataController@CSVDataManageALL')->name('CSVDataManageALL');
        Route::get('/add', 'CSVDataController@CSVDataManageADD')->name('CSVDataManageADD');
        Route::get('/rename', 'CSVDataController@CSVDataManageRename')->name('CSVDataManageRename');

        Route::Group(['prefix'=>'edit'],function() {
            Route::get('/', 'CSVDataController@CSVDataFile')->name('CSVDataFile');
            Route::get('/allCSVDataFileItem/{id}', 'CSVDataController@allCSVDataFileItem')->name('allCSVDataFileItem');
            Route::any('/editCSVDataFileItem/{id}', 'CSVDataController@editCSVDataFileItem')->name('editCSVDataFileItem');
            Route::get('/deleteCSVDataFileItem/{id}', 'CSVDataController@deleteCSVDataFileItem')->name('deleteCSVDataFileItem');
            Route::get('/copyCSVHeaderFileItem/{id}', 'CSVDataController@copyCSVHeaderFileItem')->name('copyCSVHeaderFileItem');
            Route::get('/saveCSVDataFileItem/{id}', 'CSVDataController@saveCSVDataFileItem')->name('saveCSVDataFileItem');
        });
    });

    /* csvLink */
    Route::Group(['prefix'=>'csv-Link'],function() {
        Route::get('/', 'CSVLinkController@CSVLinkManage')->name('CSVLinkManage');
        Route::Group(['prefix'=>'edit'],function() {
            Route::get('/', 'CSVDataController@CSVLinkFile')->name('CSVLinkFile');
        });
    });
});
