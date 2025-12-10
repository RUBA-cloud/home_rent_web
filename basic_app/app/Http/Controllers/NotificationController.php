<?php
// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request) {
        $q = AppNotification::forUser(Auth::id())->latest();
        if ($request->filled('filter') && $request->filter === 'unread') {
            $q->unread();
        }
        $items = $q->paginate(15)->withQueryString();
        return view('notifications.index', compact('items'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'user_id' => ['nullable','exists:users,id'],
            'title'   => ['required','string','max:200'],
            'body'    => ['nullable','string'],
            'type'    => ['nullable','string','max:50'],
            'icon'    => ['nullable','string','max:80'],
            'link'    => ['nullable','url'],
        ]);
        AppNotification::create($data);
        return back()->with('success', __('notification created'));
    }

    public function mark(AppNotification $notification) {
        $this->authorizeView($notification);
        if (is_null($notification->read_at)) $notification->update(['read_at'=>now()]);
        return back();
    }

    public function markAll() {
        AppNotification::forUser(Auth::id())->unread()->update(['read_at'=>now()]);
        return back();
    }

    public function destroy(AppNotification $notification) {
        $this->authorizeView($notification);
        $notification->delete();
        return back()->with('success', __('Notification deleted'));
    }

    protected function authorizeView(AppNotification $n): void {
        if (!is_null($n->user_id) && $n->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
