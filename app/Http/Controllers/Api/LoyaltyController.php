<?php
namespace App\Http\Controllers\Api;

use App\Models\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Models\Campaign;
use App\Models\Models\Customer;
use App\Models\Models\Transaction;
use Illuminate\Routing\Controller;
use App\Models\Models\StoreSetting;
use Illuminate\Support\Facades\Hash;

class LoyaltyController extends Controller
{
    public function adminLogin(Request $request)
    {
        $admin = Admin::where('username', $request->username)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        $admin->update(['last_login' => now()]);
        return response()->json(['success' => true, 'data' => [
            'token' => 'demo-admin-token',
            'user' => $admin
        ]]);
    }

    public function getStore() {
        $store = StoreSetting::first();
        return response()->json(['success' => true, 'data' => $store]);
    }

    public function updateStore(Request $request) {
        $store = StoreSetting::first();
        if (!$store) $store = StoreSetting::create($request->all());
        else $store->update($request->all());
        return response()->json(['success' => true, 'message' => 'Store details updated successfully', 'data' => $store]);
    }

    public function getAdminUsers() {
        return response()->json(['success' => true, 'data' => Admin::all()]);
    }

    public function createAdminUser(Request $request) {
        $admin = Admin::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);
        return response()->json(['success' => true, 'message' => 'Admin user created successfully', 'data' => $admin]);
    }

    public function updateAdminCredentials(Request $request) {
        $admin = Admin::first();
        if (!Hash::check($request->currentPassword, $admin->password)) {
            return response()->json(['success' => false, 'message' => 'Current password incorrect'], 403);
        }
        if ($request->newUsername) $admin->username = $request->newUsername;
        if ($request->newPassword) {
            if ($request->newPassword != $request->confirmPassword) {
                return response()->json(['success' => false, 'message' => 'Password confirmation mismatch'], 422);
            }
            $admin->password = bcrypt($request->newPassword);
        }
        $admin->save();
        return response()->json(['success' => true, 'message' => 'Credentials updated successfully']);
    }

    public function deleteAdminUser($id) {
        Admin::destroy($id);
        return response()->json(['success' => true, 'message' => 'Admin user deleted successfully']);
    }
public function getCustomers(Request $request) {
    $customers = Customer::paginate($request->per_page ?? 10);

    $formatted = $customers->items();
    $mapped = collect($formatted)->map(function ($customer) {
        return [
            'id' => $customer->id,
            'name' => $customer->full_name,
            'phone' => $customer->phone_number,
            'gender' => $customer->gender,
            'points' => $customer->points,
            'tier' => $customer->tier,
            'cashValue' => '$' . number_format($customer->points * 0.05, 2),
            'joined' => optional($customer->created_at)->format('m/d/Y') ?? 'N/A',
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $mapped,
        'pagination' => [
            'current_page' => $customers->currentPage(),
            'total_pages' => $customers->lastPage(),
            'total_items' => $customers->total(),
            'per_page' => $customers->perPage()
        ]
    ]);
}

public function searchCustomers(Request $request) {
    $term = $request->q;

    $results = Customer::where('full_name', 'like', "%$term%")
        ->orWhere('phone_number', 'like', "%$term%")
        ->get();

    $mapped = $results->map(function ($customer) {
        return [
            'id' => $customer->id,
            'name' => $customer->full_name,
            'phone' => $customer->phone_number,
            'gender' => $customer->gender,
            'points' => $customer->points,
            'tier' => $customer->tier,
            'cashValue' => '$' . number_format($customer->points * 0.05, 2),
            'joined' => optional($customer->created_at)->format('m/d/Y') ?? 'N/A',
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $mapped
    ]);
}

   public function getCustomerById($id) {
    $customer = Customer::with('transactions')->findOrFail($id);

    $formatted = [
        'id' => $customer->id,
        'name' => $customer->full_name,
        'phone' => $customer->phone_number,
        'gender' => $customer->gender,
        'points' => $customer->points,
        'tier' => $customer->tier,
        'cashValue' => '$' . number_format($customer->points * 0.05, 2),
        'joined' => optional($customer->created_at)->format('m/d/Y') ?? 'N/A',
        'transactions' => $customer->transactions, // assuming frontend needs full transactions
    ];

    return response()->json([
        'success' => true,
        'data' => $formatted
    ]);
}


   private function formatCustomer($customer) {
    return [
        'id' => $customer->id,
        'name' => $customer->full_name,
        'phone' => $customer->phone_number,
        'gender' => $customer->gender,
        'points' => $customer->points,
        'tier' => $customer->tier,
        'cashValue' => '$' . number_format($customer->points * 0.05, 2),
        'joined' => optional($customer->created_at)->format('m/d/Y') ?? 'N/A',
    ];
}

public function createCustomer(Request $request) {
    $customer = Customer::create([
        'full_name' => $request->fullName,
        'phone_number' => $request->phoneNumber,
        'email' => $request->email,
        'gender' => $request->gender,
        'pin_code' => $request->pinCode,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Customer registered successfully',
        'data' => $this->formatCustomer($customer)
    ]);
}

public function updateCustomer(Request $request, $id) {
    $customer = Customer::findOrFail($id);
    $customer->update([
        'full_name' => $request->name,
        'phone_number' => $request->phone,
        'gender' => $request->gender,
        'points' => $request->points,
        'tier' => $request->tier,
        // ignore cashValue and joined, those are derived
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Customer updated successfully',
        'data' => $this->formatCustomer($customer)
    ]);
}


    public function deleteCustomer($id) {
        Customer::destroy($id);
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
    }

public function earnPoints(Request $request) {
    $customer = Customer::findOrFail($request->customerId);
    
    $transaction = $customer->transactions()->create([
        'type' => 'earned',
        'points' => $request->points,
        'amount' => $request->amount,
        'description' => $request->description
    ]);

    $customer->points += $request->points;
    $customer->save();

    return response()->json([
        'success' => true,
        'message' => 'Points added successfully',
        'data' => [
            'transaction' => $transaction,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'phone' => $customer->phone_number,
                'tier' => $customer->tier,
                'points' => $customer->points,
                'cashValue' => '$' . number_format($customer->points * 0.05, 2),
                'joined' => optional($customer->created_at)->format('m/d/Y') ?? 'N/A'
            ]
        ]
    ]);
}


    public function redeemPoints(Request $request) {
        $customer = Customer::findOrFail($request->customerId);
        $transaction = $customer->transactions()->create([
            'type' => 'redeemed',
            'points' => -$request->points,
            'amount' => $request->cashValue,
            'description' => $request->description
        ]);
        $customer->points -= $request->points;
        $customer->save();
        return response()->json(['success' => true, 'message' => 'Points redeemed successfully', 'data' => compact('transaction', 'customer')]);
    }

    public function deductPoints(Request $request) {
        $customer = Customer::findOrFail($request->customerId);
        $pointsToDeduct = floor($request->refundAmount * 2);
        $transaction = $customer->transactions()->create([
            'type' => 'deducted',
            'points' => -$pointsToDeduct,
            'amount' => $request->refundAmount,
            'reason' => $request->reason,
        ]);
        $customer->points -= $pointsToDeduct;
        $customer->save();
        return response()->json(['success' => true, 'message' => 'Points deducted successfully', 'data' => compact('transaction', 'customer', 'pointsToDeduct')]);
    }

 
   
  public function getTransactions(Request $request) {
    $query = Transaction::with('customer');

    if ($request->type) {
        $query->where('type', $request->type);
    }

    if ($request->customer_id) {
        $query->where('customer_id', $request->customer_id);
    }

    $data = $query->paginate($request->per_page ?? 10);

    $formatted = array_map(function ($transaction) {
        return [
            'id' => $transaction->id,
            'customerName' => $transaction->customer->full_name ?? 'N/A',
            'customerId' => (string) $transaction->customer_id,
            'type' => $transaction->type,
            'points' => (int) $transaction->points,
            'amount' => '$' . number_format($transaction->amount, 2),
            'date' => optional($transaction->created_at)->format('m/d/Y') ?? 'N/A',
            'description' => $transaction->description,
        ];
    }, $data->items());

    return response()->json([
        'success' => true,
        'data' => $formatted,
        'pagination' => [
            'current_page' => $data->currentPage(),
            'total_pages' => $data->lastPage(),
            'total_items' => $data->total(),
            'per_page' => $data->perPage()
        ]
    ]);
}


    public function deleteTransaction($id) {
        Transaction::destroy($id);
        return response()->json(['success' => true, 'message' => 'Transaction deleted successfully']);
    }

    public function getCampaigns() {
        return response()->json(['success' => true, 'data' => Campaign::all()]);
    }

    public function createCampaign(Request $request) {
        $campaign = Campaign::create($request->all());
        return response()->json(['success' => true, 'message' => 'Campaign created successfully', 'data' => $campaign]);
    }

    public function updateCampaign(Request $request, $id) {
        $campaign = Campaign::findOrFail($id);
        $campaign->update($request->all());
        return response()->json(['success' => true, 'message' => 'Campaign updated successfully', 'data' => $campaign]);
    }

    public function deleteCampaign($id) {
        Campaign::destroy($id);
        return response()->json(['success' => true, 'message' => 'Campaign deleted successfully']);
    }

    public function customerRegister(Request $request) {
        $customer = Customer::create([
            'full_name' => $request->fullName,
            'phone_number' => $request->phoneNumber,
            'email' => $request->email,
            'gender' => $request->gender,
            'pin_code' => $request->pinCode,
        ]);
        return response()->json(['success' => true, 'message' => 'Registration successful', 'data' => $customer]);
    }

    public function customerLogin(Request $request) {
        $customer = Customer::where('phone_number', $request->phoneNumber)->first();
        if (!$customer || $customer->pin_code !== $request->pinCode) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        return response()->json(['success' => true, 'data' => [
            'token' => 'demo-customer-token',
            'customer' => $customer
        ]]);
    }

    public function getCustomerProfile() {
        $customer = Customer::with('transactions')->first();
        return response()->json(['success' => true, 'data' => [
            'customer' => $customer,
            'transactions' => $customer->transactions
        ]]);
    }

    public function redeemRequest(Request $request) {
        $value = $request->points * 0.05;
        return response()->json(['success' => true, 'message' => 'Redemption request submitted successfully', 'data' => [
            'requestId' => rand(100, 999),
            'points' => $request->points,
            'cashValue' => "$" . number_format($value, 2),
            'status' => 'pending',
            'submittedAt' => now()
        ]]);
    }


    public function searchCustomerByPhone(Request $request)
{
    $phone = $request->get('phone');

    $customer = Customer::where('phone_number', $phone)->first();

    if (!$customer) {
        return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $customer->id,
            'name' => $customer->full_name,
            'phone' => $customer->phone_number,
            'tier' => $customer->tier,
            'points' => $customer->points,
            'cashValue' => '$' . number_format($customer->points * 0.05, 2),
            'joined' => optional($customer->created_at)->format('m/d/Y') ?? 'N/A'
        ]
    ]);
}

}
