<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BusinessOrdersDashboard;
use App\Livewire\CheckoutSuccess;
use App\Livewire\DriverTasksManager;

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';



Route::get('/test', function () {
    return view('test');
});

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/search', 'pages::search')->name('search');
Route::livewire('/cart', 'pages::cart')->name('cart');
Route::livewire('/profile', 'pages::profile')->name('profile');
Route::livewire('/business/{business}', 'pages::business')->name('business');
Route::livewire('/tag/{tag}', 'pages::tag')->name('tag');
Route::livewire('/checkout', 'checkout')->name('checkout');
Route::livewire('/location', 'pages::location')->name('location');
Route::livewire('/checkout/address', 'pages::location')->name('checkout.address');
Route::get('/kitchen/orders/{businessId}', BusinessOrdersDashboard::class)->name('kitchen.orders');
Route::get('/checkout/success/{order}', CheckoutSuccess::class)->name('checkout.success');
Route::get('/driver/{driver}', DriverTasksManager::class)->name('driver.tasks');
