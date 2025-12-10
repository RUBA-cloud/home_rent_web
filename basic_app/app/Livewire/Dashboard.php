<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

class Dashboard extends Component
{
    public int $limit = 10;
    public bool $autoRefresh = true;

    public function mount(): void
    {
        // set $this->limit from config if you want
    }

    #[On('dashboard:refresh')]
    public function refreshNow(): void
    {
        // no-op: Livewire will re-render when this method is invoked
    }

    private function money(int|float|string|null $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if (!is_numeric($value)) {
            return '-';
        }
        return number_format((float) $value, 2);
    }

    public function render(): View
    {
        $newOrders = Order::query()
            ->with(['user:id,name'])
            ->latest('id')
            ->limit($this->limit)
            ->get();

        $completedOrders = Order::query()
            ->with(['user:id,name'])
            ->whereIn('status', ['completed', 'done', 'paid']) // adjust to your statuses
            ->latest('updated_at')
            ->limit($this->limit)
            ->get();

        $newUsers = User::query()
            ->latest('id')
            ->limit($this->limit)
            ->get(['id', 'name', 'email', 'created_at']);

        $newProducts = Product::query()
            ->latest('id')
            ->limit($this->limit)
            ->get(['id', 'name_en', 'price', 'created_at']);

        return view('livewire.dashboard', [
            'newOrders'       => $newOrders,
            'completedOrders' => $completedOrders,
            'newUsers'        => $newUsers,
            'newProducts'     => $newProducts,
            'money'           => \Closure::fromCallable([$this, 'money']),
        ]);
    }
}
