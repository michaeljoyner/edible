<?php

Route::get('hello', function() {
   return 'hello';
});
Route::group(['prefix' => 'package-edible', 'namespace' => 'Michaeljoyner\Edible\Http'], function() {
    Route::get('pages/{pageId}', 'EdiblesController@showPage');
    Route::get('pages/{pageId}/textblocks/{textblockId}/edit', 'EdiblesController@editTextblock');
    Route::get('pages/{pageId}/galleries/{galleryId}/edit', 'EdiblesController@editGallery');
    Route::post('textblocks/{textblockId}', 'EdiblesController@updateTextblock');
    Route::post('galleries/{galleryId}/uploads', 'EdiblesController@storeUploadedImage');
    Route::get('galleries/{galleryId}/uploads', 'EdiblesController@getGalleryImages');
});