<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminOrderDashboard extends Component
{
    public $transactions;
    public $statusCounts;
    public $currentStatus;
    /**
     * Create a new component instance.
     */
    public function __construct($transactions, $statusCounts, $currentStatus = 'all')
    {
        $this->transactions = $transactions;
        $this->statusCounts = $statusCounts;
        $this->currentStatus = $currentStatus;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pages.dashboard.admin.admin-order-dashboard');
    }
}
