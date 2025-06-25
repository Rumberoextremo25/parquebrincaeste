<?php

use App\Http\Controllers\Api\TicketController;
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
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

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

// // Ruta GET para la Página A (donde está el formulario)
// Route::get('/test-a', function () {
//     return Inertia::render('Test/TestA');
// })->name('test-a'); // Damos un nombre a la ruta por si necesitamos referenciarla

// // Ruta POST que recibe el mensaje de Página A y lo procesa
// Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('send-message');

// // Ruta GET para la Página B (que mostrará el mensaje)
// // NOTA: Esta ruta no necesita un controlador si solo renderiza una vista y recibe props vía flash.
// Route::get('/test-b', function () {
//     // Cuando Inertia redirige con flash data, ese flash data se convierte en props.
//     // Lo leeremos en PageB.jsx usando usePage().props.flash.message
//     return Inertia::render('Test/TestB');
// })->name('test-b');




Route::get('/home', [PageController::class, 'home'])->middleware('record.visit')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/tienda', [TiendaController::class, 'tienda'])->name('tienda');
    Route::post('/tienda', [TiendaController::class, 'comprar'])->name('comprar');
});

// Ruta para procesar el checkout
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.show') // Asigna un nombre a la ruta para poder referenciarla fácilmente
    ->middleware('auth');   // Protege esta ruta, requiriendo que el usuario inicie sesión
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');



Route::get('/success/success', function (Request $request) {
    //dd('params',$request); // <- ¡Ojo con este dd()!
    // Recupera los datos flasheados de la sesión
    $orderNumber = $request->session()->get('order_number');
    $paymentMethod = $request->session()->get('payment_method');
    $facturaId = $request->session()->get('factura_id');
    $facturaNumber = $request->session()->get('numero_factura');

    if (!$orderNumber || !$paymentMethod) {
        // Redirige a una página de inicio o muestra un error si los datos no están presentes
        return redirect()->route('home')->with('error', 'No se encontraron los detalles de la orden.');
    }
    // Renderiza la vista de Inertia con los datos recuperados
    return Inertia::render('Success/Success', [
        'order_number' => $orderNumber,
        'payment_method' => $paymentMethod,
        'factura_id'=> $facturaId,
        'numero_factura'=> $facturaNumber,
    ]);
})->name('success');



// This route handles downloading invoices by their 'numero_factura'
Route::get('/invoice/numero/{order_number}/download', [InvoiceController::class, 'downloadInvoiceByNumber'])
    ->name('invoice.download.number');

// This route handles downloading invoices by their 'ID' (if you're using this too)
Route::get('/invoice/{factura}/download', [InvoiceController::class, 'downloadInvoiceById'])
    ->name('invoice.download.id');


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
Route::get('/my-account', [DashboardController::class, 'myAccount'])->name('dashboard.my_account');
Route::get('/ventas', [DashboardController::class, 'ventas'])->name('dashboard.ventas');
Route::get('/finanzas', [DashboardController::class, 'finanzas'])->name('dashboard.finanzas');

// Rutas para generación de reportes PDF
Route::get('/ventas/pdf', [DashboardController::class, 'generarPDFVentas'])->name('ventas.pdf');
Route::get('/finanzas/pdf', [DashboardController::class, 'generarPDFFinanzas'])->name('finanzas.pdf');


Route::get('/dashboard-admin', [DashboardController::class, 'home'])->name('dashboard-admin');

// PARA VER LOS TICKETS CREADOS
Route::get('/tickets', [DashboardController::class, 'tickets'])->name('dashboard.tickets');

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
        });;
});

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('web.newsletter');

//Rutas Tienda
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product');

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', function () {
        if (auth()->user()->hasRole('super-admin')) {
            return redirect()->route('dashboard-admin'); // Carga la vista del dashboard para super-admin
        }
        return redirect()->route('home'); // Redirige a home si no es super-admin
    })->name('home');
});

Route::get('/dashboard/change-password', function () {
    return Inertia::render('Profile/ChangePassword');
})->name('dashboard.change-password.show');


Route::post('/profile/store-change-password', [App\Http\Controllers\ProfileController::class, 'storeChangePassword'])->name('profile.store_change_password');

Route::post('/profile/account-details', [App\Http\Controllers\ProfileController::class, 'storeAccountDetails'])->name('store_account_details');

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    // Ruta para actualizar la información del perfil (POST/PUT/PATCH)
Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    // Ruta para eliminar la cuenta del usuario
Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');


// 1. Ruta para descargar la factura por su ID (e.g., /invoice/123/download)
// Si el frontend envía solo el ID numérico y luego /download.
Route::get('/invoice/{identifier}/download', [TicketController::class, 'downloadInvoice'])
     ->where('identifier', '[0-9]+') // Asegura que {identifier} sea un número
     ->defaults('type', 'id') // Pasa 'id' como el segundo parámetro al método downloadInvoice
     ->name('ticket.invoice.download_by_id');

// 2. Ruta para descargar la factura por su NÚMERO DE FACTURA (e.g., /invoice/numero/FAC-ABC-123/download)
// Esta ruta coincide con la URL que estabas usando.
Route::get('/invoice/numero/{identifier}/download', [TicketController::class, 'downloadInvoice'])
     ->where('identifier', '[\w-]+') // Permite letras, números y guiones para el número de factura
     ->defaults('type', 'numero') // Pasa 'numero' como el segundo parámetro al método downloadInvoice
     ->name('ticket.invoice.download_by_number');


// RUTA DEL REPORTE PARA COMPROBANTE DE COMPRA

Route::get('/invoice/{factura}/download', [InvoiceController::class, 'downloadInvoiceById'])
     ->name('invoice.download'); // Manteniendo el nombre original si quieres

// Ruta para descargar la factura por su NÚMERO DE FACTURA
// Ejemplo: /invoice/numero/FAC-XYZ-123/download
Route::get('/invoice/numero/{numero_factura}/download', [InvoiceController::class, 'downloadInvoiceByNumber'])
     ->name('invoice.download_by_number'); // Nuevo nombre para esta ruta

require __DIR__ . '/auth.php';
