<?php
use Illuminate\Support\Facades\Log;

Route::group(['prefix' => 'contenteditor'], function () {
    Route::post('image/upload', 'DasRoteQuadrat\BetterContentEditor\Controllers\ImageController@upload');
    Route::post('image/save', 'DasRoteQuadrat\BetterContentEditor\Controllers\ImageController@save');

    // Additional styles route
    Route::get('styles', 'DasRoteQuadrat\BetterContentEditor\Controllers\AdditionalStylesController@render');
});

Route::post('/dasrotequadrat/image/upload', 'DasRoteQuadrat\BetterContentEditor\Controllers\ImageController@saveImageUploader'
);
