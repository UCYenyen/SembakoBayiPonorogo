<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminMenuChooser extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $title, public string $description, public string $link, public string $extraClasses = '')
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pages.dashboard.admin.admin-menu-chooser');
    }
}
