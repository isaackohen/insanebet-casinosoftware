<?php

use App\Http\Controllers\Admin\CurrenciesController;
use App\Http\Controllers\Admin\GamesController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ChallengesController;
use App\Http\Controllers\Admin\VipsController;
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
   Route::middleware(['admin'])->group(function(){//admin routes will goes here

Route::get('wallet/withdrawals', [WalletController::class, 'withdrawals']);
Route::get('wallet/deposits', [WalletController::class, 'deposits']);
Route::get('users/get', [UserController::class, 'users']);
Route::get('user/games/{id}', [UserController::class, 'games']);
Route::get('user/transactions/{id}', [UserController::class, 'transactions']);
Route::get('user/deposits/{id}', [UserController::class, 'deposits']);
Route::get('user/withdrawals/{id}', [UserController::class, 'withdrawals']);
Route::get('extgames/games/get', [GamesController::class, 'games']);
Route::get('extgames/providers/get', [GamesController::class, 'providers']);
Route::get('/{vue_capture?}', [MainController::class, 'main'])->where('vue_capture', '[\\/\\w\\:.-]*');

Route::prefix('stats')->group(function () {
    Route::post('games', [MainController::class, 'games']);
    Route::post('analytics', [MainController::class, 'analytics']);
    Route::post('deposits', [MainController::class, 'deposits']);
});

Route::post('clearCache', [MainController::class, 'clearcache']);

Route::post('maintenance', [MainController::class, 'maintenance']);
Route::post('/info', [MainController::class, 'info']);

Route::prefix('user')->group(function () {
    Route::post('get', [UserController::class, 'get']);
    Route::post('/ban', [UserController::class, 'ban']);
    Route::post('/role', [UserController::class, 'role']);
    Route::post('/balance', [UserController::class, 'balance']);
});

Route::post('txstats', [UserController::class, 'userTxStats']);

Route::post('checkDuplicates', [UserController::class, 'checkDuplicates']);
Route::post('ethereumNativeSendDeposits', [WalletController::class, 'ethereumNativeSendDeposits']);

Route::prefix('wallet')->group(function () {
    Route::post('info', [WalletController::class, 'info']);
    Route::post('infoIgnored', [WalletController::class, 'infoIgnored']);
    Route::post('accept', [WalletController::class, 'accept']);
    Route::post('decline', [WalletController::class, 'decline']);
    Route::post('ignore', [WalletController::class, 'ignore']);
    Route::post('unignore', [WalletController::class, 'unignore']);
    Route::get('autoSetup', [WalletController::class, 'autoSetup']);
    Route::post('/transfer', [WalletController::class, 'transfer']);
});

Route::prefix('notifications')->group(function () {
    Route::post('/browser', [NotificationsController::class, 'browser']);
    Route::post('/standalone', [NotificationsController::class, 'standalone']);
    Route::post('/global', [NotificationsController::class, 'global']);
    Route::post('/global_remove', [NotificationsController::class, 'globalRemove']);
});

Route::post('/notifications/data', [NotificationsController::class, 'notificationsData']);
Route::post('/toggle_module', [ModuleController::class, 'toggle']);
Route::post('/option_value', [ModuleController::class, 'setValue']);
Route::post('/option_value', [ModuleController::class, 'setValue']);
Route::post('modules', [ModuleController::class, 'setData']);
Route::post('/toggle', [GamesController::class, 'toggle']);
Route::post('/extToggle', [GamesController::class, 'extToggle']);

Route::prefix('extgames')->group(function () {
    Route::post('settings', [GamesController::class, 'settings']);
    Route::post('game', [GamesController::class, 'game']);
    Route::post('updateProviders', [GamesController::class, 'updateProviders']);
    Route::post('updateGames', [GamesController::class, 'updateGames']);
    Route::post('restoreGamesList', [GamesController::class, 'restoreGamesList']);
});
Route::post('/currencyExtraSettings', [SettingsController::class, 'currencyExtraSettings']);
Route::post('/currencyOption', [CurrenciesController::class, 'currencyOption']);
Route::post('/currencySettings', [CurrenciesController::class, 'currencySettings']);
Route::post('/toggleCurrency', [CurrenciesController::class, 'toggleCurrency']);
Route::post('/currencyBalance', [CurrenciesController::class, 'currencyBalance']);
Route::post('/activity', [MainController::class, 'activity']);

Route::prefix('settings')->group(function () {
    Route::post('get', [SettingsController::class, 'get']);
    Route::post('create', [SettingsController::class, 'create']);
    Route::post('edit', [SettingsController::class, 'edit']);
    Route::post('remove', [SettingsController::class, 'remove']);
});

Route::prefix('bot')->group(function () {
    Route::post('settings', [SettingsController::class, 'botSettings']);
    Route::post('start', [SettingsController::class, 'startBot']);
});
Route::prefix('telegram')->group(function () {
    Route::post('settings', [SettingsController::class, 'telegramSettings']);
});
Route::prefix('promocode')->group(function () {
    Route::post('get', [PromocodeController::class, 'get']);
    Route::post('remove', [PromocodeController::class, 'remove']);
    Route::post('remove_inactive', [PromocodeController::class, 'removeInactive']);
    Route::post('create', [PromocodeController::class, 'create']);
});

Route::prefix('challenges')->group(function () {
    Route::post('get', [ChallengesController::class, 'get']);
    Route::post('remove', [ChallengesController::class, 'remove']);
    Route::post('remove_inactive', [ChallengesController::class, 'removeInactive']);
    Route::post('create', [ChallengesController::class, 'create']);
});

Route::prefix('vips')->group(function () {
    Route::post('get', [VipsController::class, 'get']);
    Route::post('remove', [VipsController::class, 'remove']);
    Route::post('vip', [VipsController::class, 'vip']);
    Route::post('change', [VipsController::class, 'change']);
    Route::post('save', [VipsController::class, 'save']);
});

});

});
