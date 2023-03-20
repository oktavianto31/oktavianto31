<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

$versionApi = '/v1';

//LOGIN, LOGOUT, REGISTER, TOKEN
Route::post( $versionApi.'/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post( $versionApi.'/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::post( $versionApi.'/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post( $versionApi.'/logger', [App\Http\Controllers\AuthController::class, 'logger'])->name('logger');

//HOMEPAGE
Route::post( $versionApi.'/menu', [App\Http\Controllers\Services\ServicesController::class, 'menu' ])->name('home-menu');
Route::post( $versionApi.'/activity/summary', [App\Http\Controllers\Order\OrderController::class, 'activitySummary'])->name('activity-summary');

//BANNER
Route::post( $versionApi.'/banner/home', [App\Http\Controllers\Banner\BannerController::class, 'home'])->name('banner-home');
Route::post( $versionApi.'/banner/insight', [App\Http\Controllers\Banner\BannerController::class, 'insight'])->name('banner-insight');
Route::post( $versionApi.'/banner/detail', [App\Http\Controllers\Banner\BannerController::class, 'detail'])->name('banner-detail');

//ARTICLE
Route::post( $versionApi.'/article', [App\Http\Controllers\Insight\InsightController::class, 'index'])->name('article-home');
Route::post( $versionApi.'/article/detail', [App\Http\Controllers\Insight\InsightController::class, 'detail'])->name('article-detail');
Route::post( $versionApi.'/article/category', [App\Http\Controllers\Insight\InsightController::class, 'category'])->name('article-category');

//PROFILE
Route::post( $versionApi.'/profile', [App\Http\Controllers\Customer\CustomerController::class, 'profile'])->name('profile');
Route::post( $versionApi.'/profile/edit', [App\Http\Controllers\Customer\CustomerController::class, 'profileEdit'])->name('profile-edit');
Route::post( $versionApi.'/address', [App\Http\Controllers\Customer\CustomerController::class, 'address'])->name('address');
Route::post( $versionApi.'/address/add', [App\Http\Controllers\Customer\CustomerController::class, 'addressAdd'])->name('address-add');
Route::post( $versionApi.'/address/edit', [App\Http\Controllers\Customer\CustomerController::class, 'addressEdit'])->name('ddress-edit');
Route::post( $versionApi.'/address/delete', [App\Http\Controllers\Customer\CustomerController::class, 'addressDelete'])->name('address-delete');
Route::post( $versionApi.'/address/primary', [App\Http\Controllers\Customer\CustomerController::class, 'addressPrimary'])->name('address-primary');
Route::post( $versionApi.'/static/page', [App\Http\Controllers\StaticPage\StaticPageController::class, 'staticPage'])->name('static-page');
Route::post( $versionApi.'/account/disactive', [App\Http\Controllers\Customer\CustomerController::class, 'disactiveAccount'])->name('disactive-account');

//INBOX
Route::post( $versionApi.'/inbox', [App\Http\Controllers\PushNotification\PushNotificationController::class, 'inbox'])->name('inbox');
Route::post( $versionApi.'/inbox/detail', [App\Http\Controllers\PushNotification\PushNotificationController::class, 'detail'])->name('inbox-detail');

//ACTIVITY
Route::post( $versionApi.'/activity', [App\Http\Controllers\Order\OrderController::class, 'activity'])->name('activity');
Route::post( $versionApi.'/activity/detail', [App\Http\Controllers\Order\OrderController::class, 'detail'])->name('activity-detail');

//ORDER & SERVICES
Route::post( $versionApi.'/services', [App\Http\Controllers\Services\ServicesController::class, 'index'])->name('services');
Route::post( $versionApi.'/services/category', [App\Http\Controllers\Services\ServicesController::class, 'category'])->name('services-category');
Route::post( $versionApi.'/services/timeslot', [App\Http\Controllers\Services\ServicesController::class, 'timeslot'])->name('services-timeslot');
Route::post( $versionApi.'/services/information', [App\Http\Controllers\Services\ServicesController::class, 'information'])->name('services-information');
Route::post( $versionApi.'/order/create', [App\Http\Controllers\Order\OrderController::class, 'create'])->name('order-create');
Route::post( $versionApi.'/order/detail', [App\Http\Controllers\Order\OrderController::class, 'detail'])->name('order-detail');
Route::post( $versionApi.'/order/payment', [App\Http\Controllers\Order\OrderController::class, 'payment'])->name('order-payment');
Route::post( $versionApi.'/order/create-review', [App\Http\Controllers\Order\OrderController::class, 'createReview'])->name('order-create-review');

