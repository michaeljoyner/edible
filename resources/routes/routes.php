<?php

Route::group(['prefix' => 'package-edible', 'middleware' => 'auth', 'namespace' => 'Michaeljoyner\Edible\Http'], function() {
    Route::get('pages/{pageId}', 'EdiblesController@showPage');
    Route::get('pages/{pageId}/textblocks/{textblockId}/edit', 'EdiblesController@editTextblock');
    Route::get('pages/{pageId}/galleries/{galleryId}/edit', 'EdiblesController@editGallery');
    Route::post('textblocks/{textblockId}', 'EdiblesController@updateTextblock');
    Route::post('galleries/{galleryId}/uploads', 'EdiblesController@storeUploadedImage');
    Route::get('galleries/{galleryId}/uploads', 'EdiblesController@getGalleryImages');
    Route::get('galleries/{galleryId}/order', 'EdiblesController@showGalleryForOrdering');
    Route::post('galleries/{galleryId}/order', 'EdiblesController@setGalleryOrder');
});