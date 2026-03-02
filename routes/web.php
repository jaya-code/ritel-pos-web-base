<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ClosingController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');

// Email Verification Routes
Route::prefix('email')->group(function () {
    Route::get('/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
    Route::get('/verify/{id}/{hash}', [\App\Http\Controllers\Auth\VerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/verification-notification', [\App\Http\Controllers\Auth\VerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});

// Root Route
Route::get('/', function () {
    return redirect()->route('login');
});

// Shared Admin & Owner Routes
Route::middleware(['auth', 'role:admin,owner', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('pembelian', \App\Http\Controllers\PembelianController::class);
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'show']);
    Route::resource('users', \App\Http\Controllers\UserController::class);
});

// Owner Only Routes
// Owner Only Routes
Route::middleware(['auth', 'role:owner', 'verified'])->group(function () {
    Route::resource('stores', \App\Http\Controllers\StoreController::class)->only(['create', 'store']);
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    Route::resource('promos', \App\Http\Controllers\PromoController::class);

    // Configuration / Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/update', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Subscription Management
    Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/checkout', [\App\Http\Controllers\SubscriptionController::class, 'createTransaction'])->name('subscription.checkout');
    Route::get('/payments', function () {
        return 'Payments Page';
    })->name('payments.index');
    Route::get('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'ownerIndex'])->name('withdrawals.index');
    Route::post('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'store'])->name('withdrawals.store');

    // Payment Settings
    Route::get('/payment-settings', [\App\Http\Controllers\PaymentSettingController::class, 'index'])->name('payment-settings.index');
    Route::put('/payment-settings', [\App\Http\Controllers\PaymentSettingController::class, 'update'])->name('payment-settings.update');
});

// Admin Only Routes for Stores Index
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/switch-store', function (\Illuminate\Http\Request $request) {
        if ($request->store_id === 'all') {
            session()->forget('admin_active_store_id');
        } else {
            session(['admin_active_store_id' => $request->store_id]);
        }
        return back()->with('success', 'Store filter updated');
    })->name('admin.switch_store');

    Route::get('/stores', [\App\Http\Controllers\StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{store}/edit', [\App\Http\Controllers\StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{store}', [\App\Http\Controllers\StoreController::class, 'update'])->name('stores.update');

    Route::resource('subscription-plans', App\Http\Controllers\SubscriptionPlanController::class);

    Route::get('/admin/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'adminIndex'])->name('admin.withdrawals.index');
    Route::post('/admin/withdrawals/{withdrawal}/approve', [\App\Http\Controllers\WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
    Route::post('/admin/withdrawals/{withdrawal}/reject', [\App\Http\Controllers\WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
});

// Cashier Routes (POS)
// Note: Admin should also be able to access POS if needed, but for now we separate strictly or allow based on role array.
// Let's allow admin, owner, and kasir to access POS for flexibility.
// Based on request "membedakan", but usually Admin/Owner can do everything. Use role:admin,owner,kasir for POS.
Route::middleware(['auth', 'role:admin,owner,kasir', \App\Http\Middleware\CheckSubscription::class])->group(function () {
    Route::get('/pos', [TransactionController::class, 'index'])->name('pos.index');
    Route::get('/pos/history', [TransactionController::class, 'history'])->name('pos.history');
    Route::get('/pos/stock', [TransactionController::class, 'stock'])->name('pos.stock');
    Route::post('/pos/stock', [TransactionController::class, 'storeStock'])->name('pos.stock.store');
    
    // Stock Opname
    Route::get('/pos/opname', [\App\Http\Controllers\StockOpnameController::class, 'index'])->name('pos.opname');
    Route::post('/pos/opname', [\App\Http\Controllers\StockOpnameController::class, 'store'])->name('pos.opname.store');

    Route::post('/pos/cancel/{penjualan}', [TransactionController::class, 'cancel'])->name('pos.cancel');
    Route::post('/pos/search', [TransactionController::class, 'search'])->name('pos.search');
    Route::post('/pos/search-member', [TransactionController::class, 'searchMember'])->name('pos.search.member');
    Route::post('/pos/token', [TransactionController::class, 'getPaymentToken'])->name('pos.token');
    Route::post('/pos/store', [TransactionController::class, 'store'])->name('pos.store');

    // Web Receipt (needs auth)
    Route::get('/pos/receipt/{id}/print', [TransactionController::class, 'receiptWeb'])->name('pos.receipt.web');

    // Cashier Printer Settings
    Route::get('/kasir/settings/printer', [\App\Http\Controllers\KasirController::class, 'printerSettings'])->name('kasir.settings.printer');
    Route::post('/kasir/settings/printer', [\App\Http\Controllers\KasirController::class, 'savePrinterSettings'])->name('kasir.settings.printer.save');

    // Kasir Closing
    Route::middleware(['role:kasir'])->group(function () {
        Route::post('/closing/open', [ClosingController::class, 'open'])->name('closing.open');
        Route::get('/closing', [ClosingController::class, 'index'])->name('closing.index');
        Route::post('/closing', [ClosingController::class, 'store'])->name('closing.store');
    });
});

// JSON Endpoints for External Bluetooth Apps (unauthenticated)
Route::get('/pos/receipt/{id}/json', [TransactionController::class, 'receiptJson'])->name('pos.receipt.json');
Route::get('/closing/receipt/{id}/json', [\App\Http\Controllers\ClosingController::class, 'receiptJson'])->name('closing.receipt.json');
