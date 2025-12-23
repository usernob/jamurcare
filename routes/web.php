<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    /** @var User $user */
    $user = Auth::user();

    if (!$user->devices()->exists()) {
        return redirect()->route('device.add.form');
    }

    return redirect()->route("dashboard.index", [
        "ulid" => $user->devices()->first()?->ulid
    ]);
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // Social Authentication Routes
    Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('social.login');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
});

Route::middleware('auth')->group(function () {
    Route::get('device/add', [DeviceController::class, "form"])->name("device.add.form");
    Route::post('device/add', [DeviceController::class, "create"])->name("device.add");
    Route::get('device/show/{ulid}', [DeviceController::class, "show"])->name("device.show")->whereUlid("ulid");

    Route::get("dashboard", [DashboardController::class, "default"])->name("dashboard.default");
    Route::get('dashboard/{ulid}', [DashboardController::class, "index"])->name("dashboard.index")->whereUlid("ulid");

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get("profile/edit", [ProfileController::class, "show"])->name("profile.edit.form");
    Route::post("profile/edit", [ProfileController::class, "store"])->name("profile.edit");

    Route::get("api/monitoring/{ulid}", [DashboardController::class, "getMonitoringData"]);
    Route::get("api/ping/{ulid}", [DashboardController::class, "pingDevice"]);
    Route::post("api/control/{ulid}", [DashboardController::class, "controlDevice"]);
});
