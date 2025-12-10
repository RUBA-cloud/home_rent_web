<?php
// app/Http/Livewire/Notifications/Bell.php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    use WithPagination;

    public $show = false;
    public $perPage = 8;
    protected $listeners = ['notify:refresh' => '$refresh'];

    protected $queryString = ['show'];

    public function getUserId(): ?int {
        return Auth::id();
    }

    public function getCountProperty(): int {
        return AppNotification::forUser($this->getUserId())->unread()->count();
    }

    public function markAsRead(int $id): void {
        $n = AppNotification::forUser($this->getUserId())->where('id', $id)->first();
        if ($n && !$n->read_at) {
            $n->update(['read_at' => now()]);
            $this->emitSelf('notify:refresh');
        }
    }

    public function markAllAsRead(): void {
        AppNotification::forUser($this->getUserId())->unread()->update(['read_at' => now()]);
        $this->emitSelf('notify:refresh');
    }

    public function toggle(): void {
        $this->show = !$this->show;
    }

    public function render() {
        $userId = $this->getUserId();

        $items = AppNotification::forUser($userId)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.notification-bell', [
            'items' => $items,
        ]);
    }
}
