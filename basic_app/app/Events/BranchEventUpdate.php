<?php

namespace App\Events;

use App\Models\CompanyBranch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BranchEventUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Flat branch payload.
     *
     * @var array
     */
    public array $branch;

    // If you want to force pusher:
    // public string $connection = 'pusher';

    public function __construct(CompanyBranch $branch)
    {
        // Make sure relations you need are loaded:
        $branch->loadMissing('companyInfo');

        $this->branch = $branch->toArray();
    }

    /**
     * Public channel – must match JS subscription.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('company_branch');
    }

    /**
     * Event name – must match JS events.
     */
    public function broadcastAs(): string
    {
        return 'company_branch_updated';
    }

    /**
     * Data sent to the frontend.
     * !!! JS expects payload.branch !!!
     */
    public function broadcastWith(): array
    {
        return [
            'branch' => $this->branch,
        ];
    }
}
