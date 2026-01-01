<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserVoucher extends Component
{
    public $voucher;
    public $userVoucherId;

    public function __construct($voucher, $userVoucherId)
    {
        $this->voucher = $voucher;
        $this->userVoucherId = $userVoucherId;
    }

    public function render(): View|Closure|string
    {
        return view('components.pages.user-voucher');
    }
}
