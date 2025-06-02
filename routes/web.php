<?php
use Inertia\Inertia;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;

use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/tienda', [TiendaController::class, 'tienda'])->name('tienda');

// Ruta para procesar el checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/success', [CheckoutController::class, 'success'])->name('success');

// Rutas para el controlador de facturas
Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
Route::get('/invoice/download/{id}', [InvoiceController::class, 'download'])->name('invoice.download');

// Rutas para el controlador de compras exitosas
Route::get('/purchase-success', [InvoiceController::class, 'purchaseSuccess'])->name('invoice.purchaseSuccess');


//Rutas del Controlador Pages
Route::get('/package', [PageController::class, 'package'])->name('package');
Route::get('/promotion', [PageController::class, 'promotion'])->name('promotion');
Route::get('/speaker/{speaker}', [PageController::class, 'speaker'])->name('speaker');
Route::get('/about-us', [PageController::class, 'about_us'])->name('about_us');
Route::get('/privacy-policy', [PageController::class, 'privacy_policy'])->name('privacy_policy');
Route::get('/terms-of-service', [PageController::class, 'terms_of_service'])
    ->name('terms_of_service');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/stand', [PageController::class, 'stand'])->name('stand');
Route::get('/settings', [PageController::class, 'settings'])->name('settings');
Route::put('/settings', [PageController::class, 'updateSettings'])->name('settings.update');
Route::post('/update-dark-mode', [PageController::class, 'updateDarkMode'])->name('update-dark-mode');



//Rutas del Controlador de Contacto
Route::get('/contact-us', [ContactController::class, 'contact_us'])->name('contact_us');
Route::post('/contact-us/save', [ContactController::class, 'save'])->name('contact_us.save');

//Rutas Controlador Newletters
Route::post('/web/newsletter', [NewsletterController::class, 'subscribe']);

//Rutas para sidebar Dashboard - Administrador
Route::get('/dashboard', [DashboardController::class, 'home'])->name('dashboard.home');  
Route::get('/my-account', [DashboardController::class, 'myAccount'])->name('dashboard.my_account');  
Route::get('/ventas', [DashboardController::class, 'ventas'])->name('dashboard.ventas'); 
Route::get('/finanzas', [DashboardController::class, 'finanzas'])->name('dashboard.finanzas');

// Rutas para generación de reportes PDF
Route::get('/ventas/pdf', [DashboardController::class, 'generarPDFVentas'])->name('ventas.pdf');
Route::get('/finanzas/pdf', [DashboardController::class, 'generarPDFFinanzas'])->name('finanzas.pdf');

// Rutas para el Dashboard del Usuario
Route::post('/dashboard/update-account', [DashboardController::class, 'updateAccount'])->name('update_account');
Route::post('/dashboard/change-password', [DashboardController::class, 'changePassword'])->name('change_password');
// Ruta para la vista de Mi Cuenta
Route::get('/dashboard/my-account', [DashboardController::class, 'myAccount'])->name('my_account');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/my-account', 'my_account')->name('my_account');
            Route::get('/account-details', 'account_details')->name('account_details');
            Route::post('/account-details', 'store_account_details')->name('store_account_details');
            Route::get('/my-orders', 'my_orders')->name('my_orders');
            Route::get('/change-password', 'change_password')->name('change_password');
            Route::post('/change-password', 'store_change_password')->name('store_change_password');
        });
    ;
});

//Rutas Tienda
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product');

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', function () {
        if (auth()->user()->hasRole('super-admin')) {
            return Inertia::render('Home/Home'); // Carga la vista del dashboard para super-admin
        }
        return redirect()->route('home'); // Redirige a home si no es super-admin
    })->name('home');
});

require __DIR__ . '/auth.php';
