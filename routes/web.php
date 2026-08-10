<?php

use App\Livewire\Pages\BusinessOrdersDashboard;
use App\Livewire\Pages\BusinessPage;
use App\Livewire\Pages\CartPage;
use App\Livewire\Pages\Checkout;
use App\Livewire\Pages\CheckoutSuccess;
use App\Livewire\Pages\DriverTasksManager;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Location;
use App\Livewire\Pages\Profile;
use App\Livewire\Pages\Search;
use App\Livewire\Pages\TagPage;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\DriverProfile;
use App\Livewire\Pages\BusinessProfile;
use App\Livewire\Pages\CityManagement;

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';

Route::get('/test', function () {
    return view('test');
});

Route::get('/', Home::class)->name('home');
Route::get('/search', Search::class)->name('search');
Route::get('/cart', CartPage::class)->name('cart');
Route::get('/profile', Profile::class)->name('profile');
Route::get('/business/{business}', BusinessPage::class)->name('business');
Route::get('/tag/{tag}', TagPage::class)->name('tag');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/location', Location::class)->name('location');
Route::get('/checkout/address', Location::class)->name('checkout.address');
Route::get('/kitchen/orders/{businessId}', BusinessOrdersDashboard::class)->name('kitchen.orders');
Route::get('/checkout/success/{order}', CheckoutSuccess::class)->name('checkout.success');
Route::get('/driver/{driver}', DriverTasksManager::class)->name('driver.tasks');
Route::get('/driver/{driver}/profile', DriverProfile::class)->name('driver.profile');
Route::get('/businesses/{business}/profile', BusinessProfile::class)->name('businesses.profile');


Route::get('/admin/ciudades/{city?}', CityManagement::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.cities.manage');