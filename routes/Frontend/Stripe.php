<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 18:10
 */


Route::group(['middleware' => 'auth'], function () {
    Route::post('subscription/stripe', 'StripeController@subscription')->name('stripe.subscription');
    Route::get('subscription/stripe/success', 'StripeController@success')->name('stripe.subscription.success');
    Route::get('subscription/stripe/cancel', 'StripeController@cancel')->name('stripe.subscription.cancel');

    Route::post('purchase/stripe', 'StripeController@purchase')->name('stripe.purchase');
    Route::get('purchase/stripe/success', 'StripeController@success')->name('stripe.purchase.success');
    Route::get('purchase/stripe/cancel', 'StripeController@cancel')->name('stripe.purchase.cancel');
});