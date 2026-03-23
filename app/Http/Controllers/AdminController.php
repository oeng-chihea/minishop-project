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

        $raw = Order::selectRaw("
            count(*) as total,
            count(*) filter (where status = 'paid') as paid,
            count(*) filter (where status = 'pending') as pending,
            coalesce(sum(total_amount) filter (where status = 'paid'), 0) as revenue
        ")->first();

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

    // POST /admin/orders/{id}/status  — update order status from dashboard
    public function updateStatus(Request $request, int $id)
    {
        if (! $this->isAuthenticated()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate(['status' => 'required|in:pending,paid,cancelled']);

        $order = Order::findOrFail($id);
        $order->status  = $request->status;
        $order->paid_at = $request->status === 'paid' ? now() : $order->paid_at;
        $order->save();

        return response()->json(['ok' => true, 'status' => $order->status]);
    }
}
