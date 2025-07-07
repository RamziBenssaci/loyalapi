<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoyaltyController;

Route::post('/admin/login', [LoyaltyController::class, 'adminLogin']);
Route::post('/customer/login', [LoyaltyController::class, 'customerLogin']);
Route::post('/customer/register', [LoyaltyController::class, 'customerRegister']);

Route::get('/store', [LoyaltyController::class, 'getStore']);
Route::put('/store', [LoyaltyController::class, 'updateStore']);

Route::get('/admin/users', [LoyaltyController::class, 'getAdminUsers']);
Route::post('/admin/users', [LoyaltyController::class, 'createAdminUser']);
Route::put('/admin/credentials', [LoyaltyController::class, 'updateAdminCredentials']);
Route::delete('/admin/users/{id}', [LoyaltyController::class, 'deleteAdminUser']);

Route::get('/customers', [LoyaltyController::class, 'getCustomers']);
Route::get('/customers/search', [LoyaltyController::class, 'searchCustomers']);
Route::get('/customers/{id}', [LoyaltyController::class, 'getCustomerById']);
Route::post('/customers', [LoyaltyController::class, 'createCustomer']);
Route::put('/customers/{id}', [LoyaltyController::class, 'updateCustomer']);
Route::delete('/customers/{id}', [LoyaltyController::class, 'deleteCustomer']);

Route::post('/points/earn', [LoyaltyController::class, 'earnPoints']);
Route::post('/points/redeem', [LoyaltyController::class, 'redeemPoints']);
Route::post('/points/deduct', [LoyaltyController::class, 'deductPoints']);

Route::get('/campaigns', [LoyaltyController::class, 'getCampaigns']);
Route::post('/campaigns', [LoyaltyController::class, 'createCampaign']);
Route::put('/campaigns/{id}', [LoyaltyController::class, 'updateCampaign']);
Route::delete('/campaigns/{id}', [LoyaltyController::class, 'deleteCampaign']);

Route::get('/transactions', [LoyaltyController::class, 'getTransactions']);
Route::delete('/transactions/{id}', [LoyaltyController::class, 'deleteTransaction']);

Route::get('/customer/profile', [LoyaltyController::class, 'getCustomerProfile']);
Route::post('/customer/redeem-request', [LoyaltyController::class, 'redeemRequest']);



Route::get('/points/search-customer', [LoyaltyController::class, 'searchCustomerByPhone']);
