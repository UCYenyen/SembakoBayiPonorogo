<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserOrderDashboard extends Component
{
    public $transactions;
    public $statusCounts;
    public $currentStatus;

    /**
     * @param $transactions
     * @param $statusCounts
     * @param string $currentStatus
     */
    public function __construct($transactions, $statusCounts, $currentStatus = 'all')
    {
        $this->transactions = $transactions;
        $this->statusCounts = $statusCounts;
        $this->currentStatus = $currentStatus;
    }

    public function render(): View|Closure|string
    {
        return view('components.pages.dashboard.user.user-order-dashboard');
    }
}
