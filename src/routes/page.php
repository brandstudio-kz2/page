<?php

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => [
        config('backpack.base.web_middleware', 'web'),
        config('backpack.base.middleware_key', 'admin'),
    ],
    'namespace'  => 'BrandStudio\Page\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('page', 'PageCrudController');
});

Route::group([
    'prefix'        => config('page.prefix'),
    'middleware'    => config('page.middleware'),
    'namespace'     => 'BrandStudio\Page\Http\Controllers',
], function() {

    Route::get('', 'PageController@index');
    Route::get('{page}', 'PageController@show');

});
