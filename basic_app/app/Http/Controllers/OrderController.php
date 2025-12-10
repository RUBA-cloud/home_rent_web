<?php
// app/Http/Controllers/OrderController.php
namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, OrderHistory, User};
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
  use App\Services\Notifier;
class OrderController extends Controller
{
    public function index(Request $r)
    {
        $q = Order::query()
            ->with(['user','employee','offer'])
            ->when($r->filled('status'), fn($qq) => $qq->where('status', $r->integer('status')))
            ->latest();

        return view('orders.index', [
            'orders' => $q->paginate(15)->withQueryString(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user','employee','items.product','offer']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['items.product']);
        $employees = User::get(['id','name']);
        return view('orders.edit', compact('order','employees'));
    }

    public function update(Request $r, Order $order)
    {
        $employee = null;

$user=$order->user_id;

        // Validate main fields; status transitions handled below
        $data = $r->validate([
            'notes'        => ['nullable','string','max:2000'],
            'status'       => ['required', Rule::in([Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_REJECTED, Order::STATUS_COMPLETED])],
            'employee_id'  => ['nullable','exists:users,id'],
        ]);

        // If accepting, require permissioned employee
        if ((int)$data['status'] === Order::STATUS_ACCEPTED) {
            if (empty($data['employee_id'])) {
                return back()->withErrors(['employee_id' => 'Employee is required when accepting an order.'])->withInput();
            }
            $employee = User::find($data['employee_id']);
            if (!$employee) {
                return back()->withErrors(['employee_id' => 'Selected user is not permitted to handle orders.'])->withInput();
            }
            // Optional: also require the current user to be allowed to accept
           // Gate::authorize('orders.accept');
        }

        // Save (Observer will snapshot old state automatically)
        $order->fill([
            'notes'       => $data['notes'] ?? $order->notes,
            'status'      => $data['status'],
            'employee_id' => $data['status'] == Order::STATUS_ACCEPTED ? $data['employee_id'] : $order->employee_id,
        ])->save();

        // Recalc total if needed (example)
        $order->load('items');
       // $order->total_price = $order->items->sum('total_price');
        $order->save();

        // Send FCM to customer
        $title = "Order #{$order->id}";
        $body  = match ($order->status) {
            Order::STATUS_ACCEPTED  => 'Your order was accepted ✅',
            Order::STATUS_REJECTED  => 'Sorry, your order was rejected ❌',
            Order::STATUS_COMPLETED => 'Your order is completed 🎉',
            default                 => 'Your order status changed',
        };


// send to a specific user
Notifier::toUser($employee, "Order Updated", "Order #55 is shipped", [
    'icon' => 'fas fa-box',
    'link' => route('orders.show', 55),
    'type' => 'order',
]);
Notifier::toUser($user, "Order Updated", "Order #55 is shipped", [
    'icon' => 'fas fa-box',
    'link' => route('orders.show', 55),
    'type' => 'order',
]);




        $order->user->notify(new OrderStatusChanged($title, $body));

        return redirect()->route('orders.show', $order)->with('success', 'Order updated & customer notified.');
    }

    public function destroy(Order $order)
    {
        // Observer will snapshot previous state
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    public function history()
    {
        $history = OrderHistory::with('items.product','actor','offer')
            ->latest()->paginate(20);

        return view('orders.history', compact('history'));
    }
}
