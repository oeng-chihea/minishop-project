<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private function isAuthenticated(): bool
    {
        return session('admin_authenticated') === true;
    }

    // GET /admin
    public function index(Request $request)
    {
        if (! $this->isAuthenticated()) {
            return redirect()->route('admin.login');
        }

        $query = Order::with('items')->orderBy('created_at', 'desc');

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'ilike', '%' . $request->search . '%');
        }

        $orders = $query->get();

        $sql = "count(*) as total, count(*) filter (where status = 'paid') as paid, count(*) filter (where status = 'pending') as pending, coalesce(sum(total_amount) filter (where status = 'paid'), 0) as revenue";
        $raw = Order::selectRaw($sql)->first();

        $stats = [
            'total'   => (int) $raw->total,
            'paid'    => (int) $raw->paid,
            'pending' => (int) $raw->pending,
            'revenue' => (float) $raw->revenue,
        ];

        return view('admin.dashboard', compact('orders', 'stats'));
    }

    // GET /admin/login
    public function loginForm()
    {
        if ($this->isAuthenticated()) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    // POST /admin/login
    public function login(Request $request)
    {
        $passcode = env('ADMIN_PASSCODE');

        if ($request->input('passcode') === $passcode) {
            session(['admin_authenticated' => true]);
            Log::info('Admin login successful', ['ip' => $request->ip()]);
            return redirect()->route('admin.index');
        }

        Log::warning('Admin login failed', ['ip' => $request->ip()]);
        return back()->withErrors(['passcode' => 'Incorrect passcode.'])->withInput();
    }

    // POST /admin/logout
    public function logout()
    {
        session()->forget('admin_authenticated');
        return redirect()->route('admin.login');
    }

    // GET /admin/data — JSON snapshot for live sync (no full page reload)
    public function data(Request $request)
    {
        if (! $this->isAuthenticated()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Order::with('items')->orderBy('created_at', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'ilike', '%' . $request->search . '%');
        }

        $orders = $query->get()->map(function ($o) {
            return [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'status'       => $o->status,
                'total_amount' => $o->total_amount,
                'currency'     => $o->currency,
                'created_at'   => $o->created_at->format('d M Y H:i'),
                'paid_at'      => $o->paid_at ? $o->paid_at->format('d M Y H:i') : null,
                'items'        => $o->items->map(function ($i) {
                    return [
                        'product_name'  => $i->product_name,
                        'product_image' => $i->product_image,
                        'quantity'      => $i->quantity,
                        'unit_price'    => $i->unit_price,
                        'subtotal'      => $i->subtotal,
                    ];
                }),
            ];
        });

        $rawSql = "count(*) as total, count(*) filter (where status = 'paid') as paid, count(*) filter (where status = 'pending') as pending, coalesce(sum(total_amount) filter (where status = 'paid'), 0) as revenue";
        $raw = Order::selectRaw($rawSql)->first();

        return response()->json([
            'stats'  => [
                'total'   => (int) $raw->total,
                'paid'    => (int) $raw->paid,
                'pending' => (int) $raw->pending,
                'revenue' => number_format((float) $raw->revenue, 2),
            ],
            'orders' => $orders,
        ]);
    }

    // POST /admin/orders/{id}/status  — update order status from dashboard
    public function updateStatus(Request $request, int $id)
    {
        if (! $this->isAuthenticated()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate(['status' => 'required|in:pending,paid,cancelled']);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->paid_at = $request->status === 'paid' ? now() : null;
        $order->save();

        return response()->json([
            'ok'      => true,
            'status'  => $order->status,
            'paid_at' => $order->paid_at ? $order->paid_at->format('d M Y H:i') : null,
        ]);
    }
}
