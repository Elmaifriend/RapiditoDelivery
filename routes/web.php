<?php

use Illuminate\Support\Facades\Route;

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
Route::livewire('/restaurant', 'pages::restaurant')->name('restaurant');
Route::livewire('/checkout', 'pages::checkout')->name('checkout');
