<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::all();
        return view('orders.create', compact('users'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate(
            [
                'user_id' => 'required|exists:users,id',        // Ensure user_id exists in the users table
                'products' => 'required|array|min:1',           // Products must be an array with at least one item
                'products.*.name' => 'required|string|max:255', // Product name is required and has a max length
                'products.*.qty' => 'required|integer|min:1|max:1000',   // Quantity must be a positive integer
                'products.*.amount' => 'required|numeric|min:1|max:100000', // Amount must be a non-negative number
            ],
            [
                'products.*.name.required' => 'Product name is required.',
                'products.*.name.max' => 'Product name must be less than 255 characters.',
                'products.*.qty.required' => 'Quantity is required.',
                'products.*.amount.required' => 'Amount is required.',
                'products.*.qty.max' => 'Quantity must be less than or equal to 10000.',
                'products.*.amount.max' => 'Amount must be less than or equal to 1000000.',
            ]
        );

        // Create the order
        $order = Order::create([
            'user_id' => $validatedData['user_id'],
            'total' => $request->grand_total,
        ]);

        // Create the associated order items
        foreach ($validatedData['products'] as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $product['name'],
                'quantity' => $product['qty'],
                'amount' => $product['amount'],
                'total' => $product['qty'] * $product['amount'],
            ]);
        }

        // Redirect to the orders index page
        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }


    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
