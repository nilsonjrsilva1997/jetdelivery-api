<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeliveryPeopleController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IfoodIntegrationController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/teste', function (Request $request) {
        return 'testando';
    });
    
    Route::group(['prefix' => 'addresses'], function () {
        Route::get('', [AddressController::class, 'index']);
        Route::post('', [AddressController::class, 'store']);
        Route::get('{address}', [AddressController::class, 'show']);
        Route::put('{address}', [AddressController::class, 'update']);
        Route::delete('{address}', [AddressController::class, 'destroy']);
    
    });

    Route::group(['prefix' => 'customers'], function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/{customer}', [CustomerController::class, 'show']);
        Route::put('/{customer}', [CustomerController::class, 'update']);
        Route::delete('/{customer}', [CustomerController::class, 'destroy']);
    });

    

    Route::group(['prefix' => 'order-statuses'], function () {
        Route::get('/', [OrderStatusController::class, 'index']);
        Route::post('/', [OrderStatusController::class, 'store']);
        Route::get('/{orderStatus}', [OrderStatusController::class, 'show']);
        Route::put('/{orderStatus}', [OrderStatusController::class, 'update']);
        Route::delete('/{orderStatus}', [OrderStatusController::class, 'destroy']);
    });

    Route::group(['prefix' => 'payment-methods'], function () {
        Route::get('/', [PaymentMethodController::class, 'index']);
        Route::post('/', [PaymentMethodController::class, 'store']);
        Route::get('/{paymentMethod}', [PaymentMethodController::class, 'show']);
        Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update']);
        Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
    });

    Route::group(['prefix' => 'delivery-peoples'], function () {
        Route::get('/', [DeliveryPeopleController::class, 'index']);
        Route::post('/', [DeliveryPeopleController::class, 'store']);
        Route::get('/{deliveryPeople}', [DeliveryPeopleController::class, 'show']);
        Route::put('/{deliveryPeople}', [DeliveryPeopleController::class, 'update']);
        Route::delete('/{deliveryPeople}', [DeliveryPeopleController::class, 'destroy']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    Route::group(['prefix' => 'restaurants'], function () {
        Route::get('/', [RestaurantController::class, 'index']);
        Route::post('/', [RestaurantController::class, 'store']);
        Route::get('/{category}', [RestaurantController::class, 'show']);
        Route::put('/{category}', [RestaurantController::class, 'update']);
        Route::delete('/{category}', [RestaurantController::class, 'destroy']);
    });

    Route::group(['prefix' => 'ifood'], function () {
        Route::post('/user-code', [IfoodIntegrationController::class, 'getOauthUserCode']);
        Route::post('/token', [IfoodIntegrationController::class, 'getOauthToken']);
        Route::get('/integration-status', [IfoodIntegrationController::class, 'getIntegrationStatus']);
        Route::get('/orders', [IfoodIntegrationController::class, 'getOrders']);
    });

    Route::group(['prefix' => 'wallet'], function () {
        Route::get('/balance', [WalletController::class, 'getBalance']);
        Route::post('/deposit', [WalletController::class, 'deposit']);
        Route::post('/withdraw', [WalletController::class, 'withdraw']);
    });
});

// rotas de autenticação na api
Route::post('register', [AuthController::class, 'register']);
Route::post('register_restaurant', [AuthController::class, 'registerRestaurant']);
Route::post('login', [AuthController::class, 'login']);
Route::post('login_restaurant', [AuthController::class, 'loginRestaurant']);
Route::post('login_delivery_people', [AuthController::class, 'loginDeliveryPeople']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('user', [AuthController::class, 'user']);
