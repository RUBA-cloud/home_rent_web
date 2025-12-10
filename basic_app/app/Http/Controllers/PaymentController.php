<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
    /** List payments */
    public function index($isHistory =false)
    {
     if($isHistory)
    {

      $history = PaymentHistory::with('user')
            ->orderByDesc('id')
            ->paginate(20);

        return view('payments.history', compact('history'));
    }

        $payments = Payment::with('user')
            ->orderByDesc('id')
            ->paginate(5);

        return view('payments.index', compact('payments'));
    }



    /** Show create form */
    public function create()
    {
        return view('payments.create');
    }

    /** Search by name/active */
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $activeFilter = $request->has('active')
            ? filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN)
            : null;

        $payments = Payment::query()
            ->when($q !== '', function ($qbuilder) use ($q) {
                $qbuilder->where(function ($w) use ($q) {
                    $w->where('name_en', 'like', "%{$q}%")
                      ->orWhere('name_ar', 'like', "%{$q}%");
                });
            })
            ->when(!is_null($activeFilter), fn ($qb) => $qb->where('is_active', $activeFilter))
            ->with('user')
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('payments.index', compact('payments', 'q', 'activeFilter'));
    }

    /** Store */
    public function store(PaymentRequest $request)
    {
        $data =$request->validated();
        $data['user_id']   = $data['user_id']   ?? Auth::id();
        $data['is_active'] = array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true;
        Payment::create($data);
        return redirect()->route('payment.index')
            ->with('success', __('Payment created successfully.'));
    }

    /** Show */
    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    /** Edit form */
    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    /** Update (log old/new) */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $data = $validated = $request->validated();

        DB::transaction(function () use ($payment, $data) {
            $this->writeHistory($payment, 'updated_before', $payment->toArray());
            $payment->update($data);
            broadcast(new \App\Events\PaymentEventUpdate($payment));
            $this->writeHistory($payment, 'updated_after', $payment->fresh()->toArray());
        });

        return redirect()->route('payment.index')
            ->with('success', __('Payment updated successfully.'));
    }

    /** "Delete": archive + deactivate */
    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $this->writeHistory($payment, 'deleted', $payment->toArray());
            $payment->update(['is_active' => false]);
        });

        return redirect()->route('payment.index')
            ->with('success', __('Payment deactivated and archived to history.'));
    }



    /** Restore from a specific history row and set active */
    public function restore(PaymentHistory $history)
    {
        DB::transaction(function () use ($history) {
            // Use the model to get the actual table name (payment_history vs payment_histories)
            $historyTable = (new PaymentHistory())->getTable();

            // Find target payment
            $payment = null;
            if (Schema::hasColumn($historyTable, 'payment_id') && !empty($history->payment_id)) {
                $payment = Payment::find($history->payment_id);
            }

            // Fallback heuristic if no FK or not found
            if (!$payment) {
                $payment = Payment::where('name_en', $history->name_en ?? null)
                                  ->where('name_ar', $history->name_ar ?? null)
                                  ->first();
            }

            $payload = [
                'name_en'   => $history->name_en,
                'name_ar'   => $history->name_ar,
                'is_active' => true,
                'user_id'   => $history->user_id,
            ];

            if ($payment) {
                $this->writeHistory($payment, 'restored_before', $payment->toArray());
                $payment->update($payload);
                $this->writeHistory($payment, 'restored_after', $payment->fresh()->toArray());
            } else {
                $payment = Payment::create($payload);
                $this->writeHistory($payment, 'restored_created', $payment->toArray());
            }
        });

        return redirect()->route('payment.index')
            ->with('success', __('Payment restored and activated from history.'));
    }

    /** Write a history row (adds optional columns only if they exist) */
    protected function writeHistory(Payment $payment, string $action, array $snapshot = []): void
    {
        $historyTable = (new PaymentHistory())->getTable();

        $payload = [
            'name_en'   => $snapshot['name_en']   ?? $payment->name_en,
            'name_ar'   => $snapshot['name_ar']   ?? $payment->name_ar,
            'is_active' => $snapshot['is_active'] ?? $payment->is_active,
            'user_id'   => $snapshot['user_id']   ?? $payment->user_id,
        ];

        if (Schema::hasColumn($historyTable, 'payment_id')) {
            $payload['payment_id'] = $payment->id;
        }
        if (Schema::hasColumn($historyTable, 'action')) {
            $payload['action'] = $action;
        }

        PaymentHistory::create($payload);
    }
}
