<?php

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

Route::get('/states', 'LocationsController@states');

Route::get('/states/{state}', 'LocationsController@cities');

Route::get('/home/search', 'Search');

Route::namespace ('Auth')->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('signup', 'UsersController@signup');
        Route::post('signin', 'UsersController@signin');
        Route::post('verify_phone', 'UsersController@verifyPhone');
        Route::post('forget_password', 'UsersController@forgetPassword');
        Route::post('verify_forget_password', 'UsersController@verifyForgetPassword');
        Route::post('change_password', 'UsersController@changePassword');
    });

    Route::prefix('admins')->group(function () {
        Route::post('/signin', 'AdminsController@signin');
    });
});

Route::namespace ('User')->group(function () {
    Route::prefix('users/me')->group(function () {
        Route::get('/', 'UsersController@showMe');

        Route::post('/update_profile_picture', 'UsersController@updateProfilePicture');

        Route::patch('/update_profile', 'UsersController@updateProfile');

        Route::patch('/update_password', 'UsersController@updatePassword');
    });

    Route::prefix('users/addresses')->group(function () {
        Route::post('/', 'AddressesController@store');

        Route::get('/', 'AddressesController@index');

        Route::get('/{id}', 'AddressesController@show');

        Route::patch('/{id}', 'AddressesController@update')
            ->name('address.update');

        Route::delete('/{id}', 'AddressesController@destroy');
    });

    Route::prefix('users/categories')->group(function () {
        Route::post('/bookmarks', 'CategoriesController@bookmark');

        Route::get('/bookmarks', 'CategoriesController@index');

        Route::delete('/bookmarks', 'CategoriesController@deleteBookmark');

        Route::get('/remaining', 'CategoriesController@remaining');
    });

    Route::prefix('users/products')->group(function () {
        Route::post('/bookmarks', 'ProductsController@bookmark');
        Route::delete('/bookmarks', 'ProductsController@deleteBookmark');
        Route::get('/bookmarks', 'ProductsController@index');
        Route::post('/comments', 'ProductsController@addComment');
    });

    Route::prefix('users/carts')->group(function () {
        Route::post('/', 'CartsController@store')
            ->name('carts.add');

        Route::get('/products', 'CartsController@similarProducts');

        Route::get('/', 'CartsController@index')
            ->name('carts.show');

        Route::patch('/', 'CartsController@update')->name('carts.update');

        Route::delete('/{id}', 'CartsController@destroy');
    });

    Route::prefix('users/booths')->group(function () {
        Route::get('/{booth}/follow', 'BoothsController@follow');
        Route::get('/follows', 'BoothsController@getFollows');
        Route::delete('/{booth}/unfollow', 'BoothsController@unfollow');

        Route::post('/{booth}/rate', 'BoothsController@rate');

        Route::post('/{booth}/comments', 'BoothsController@comment');

        Route::get('/{booth}/comments', 'BoothsController@indexComments');
    });

    Route::prefix('users/orders')->group(function () {
        Route::post('/', 'OrdersController@create');
        Route::post('/{order}/pay', 'OrdersController@pay');
        // Route::post('/discount', 'OrdersController@discount');
        // Route::post('/{order}/submit', 'OrdersController@submit');
        Route::get('/', 'OrdersController@index');
        Route::get('/{id}', 'OrdersController@show');
    });

    Route::prefix('users/notifications')->group(function () {

        Route::get('/', 'NotificationsController@index');
    });
});

Route::prefix('ads')->group(function () {
    Route::get('/', 'AdsController@index');

    Route::get('/{ad}', 'AdsController@show');

    Route::post('/', 'AdsController@store');
});

Route::namespace ('Shop')->group(function () {
    Route::prefix('/slides')->group(function () {
        Route::get('/', 'SlidesController@index');
        Route::post('/', 'SlidesController@create');
        Route::delete('/{slide}', 'SlidesController@delete');
    });
    Route::prefix('categories')->group(function () {
        Route::get('/', 'CategoriesController@index');
        Route::get('/{category}/attributes', 'CategoriesController@attributes');
        Route::get('/{category}/products', 'CategoriesController@products');
        Route::get('/{category}', 'CategoriesController@show');
        Route::post('/', 'CategoriesController@create');
        Route::post('/{category}', 'CategoriesController@update');
    });

    Route::prefix('products')->group(function () {
        Route::post('/mass_update', 'ProductsController@massUpdate');
        Route::post('/mass_create', 'ProductsController@massCreate');
        Route::get('/filters', 'ProductsController@getFilters');
        Route::get('/excel', 'ProductsController@getExcel');
        Route::get('/', 'ProductsController@index');
        Route::get('/new_products', 'ProductsController@newProducts');
        Route::get('/{product}', 'ProductsController@show')->name('product.show');
        Route::get('/{product}/similar_products', 'ProductsController@similarProducts');
        Route::get('/{product}/other_booth_products', 'ProductsController@otherBoothProducts');
        Route::post('/', 'ProductsController@create');
        Route::patch('/{product}', 'ProductsController@update');
        Route::delete('/{product}/{imageUrl}', 'ProductsController@deleteImage');
        Route::post('/{product}', 'ProductsController@updateGallery');
        Route::post('/{product}/comments', 'ProductsController@addComment');
        Route::get('/{product}/comments', 'ProductsController@getComments');
    });

    Route::prefix('/delivery_methods')
        ->group(function () {
            Route::get('/', 'DeliveryMethodsController@index');
        });

    Route::prefix('/discount_codes')
        ->group(function () {
            Route::post('/check', 'DiscountCodesController@check');
        });

    Route::prefix('/brands')->group(function () {
        Route::get('/', 'BrandsController@index');
        Route::get('/{brand}', 'BrandsController@show');
    });

    Route::prefix('/banners')->group(function () {
        Route::get('/', 'BannersController@index');
        Route::get('/{banner}', 'BannersController@show');
        Route::post('/', 'BannersController@create');
        Route::put('/{banner}', 'BannersController@update');
        Route::delete('/{banner}/cover', 'BannersController@deleteCover');
        Route::post('/{banner}/cover', 'BannersController@updateCover');
        Route::delete('/{banner}', 'BannersController@delete');
    });
});

Route::namespace ('Admin')->group(function () {
    Route::prefix('admins/users')->group(function () {
        Route::get('/', 'UsersController@index');
        Route::get('/{user}', 'UsersController@show');
    });

    Route::prefix('admins/orders')->group(function () {
        Route::get('/', 'OrdersController@index');
        Route::get('/{order}', 'OrdersController@show');
        Route::patch('/{order}', 'OrdersController@update');
    });

    Route::prefix('/admins/attributes')->group(function () {
        Route::get('/', 'AttributesController@index');
        Route::post('/', 'AttributesController@create');
        Route::patch('/{attribute}', 'AttributesController@update');
        Route::delete('/{attribute}', 'AttributesController@delete');
    });

    Route::prefix('/admins/discount_codes')->group(function () {
        Route::post('/', 'DiscountCodesController@create');
        Route::get('/', 'DiscountCodesController@index');
        Route::get('/{id}', 'DiscountCodesController@show');
        Route::patch('/{id}', 'DiscountCodesController@update');
    });

    Route::prefix('/admins/brands')->group(function () {
        Route::get('/', 'BrandsController@index');
        Route::post('/', 'BrandsController@create');
        Route::post('/{brand}', 'BrandsController@update');
    });
});

Route::get('/payment_methods', function () {
    $methods = \DB::table('payment_methods')->get();
    return response()->json(['data' => $methods]);
});

Route::apiResource('/contact_us/subjects', 'SubjectsController');

Route::prefix('/contact_us')->group(function () {
    Route::post('/', 'ContactUsController@create');
    Route::get('/', 'ContactUsController@index');
    Route::get('/{id}', 'ContactUsController@show');
    Route::delete('/{id}', 'ContactUsController@delete');
});
