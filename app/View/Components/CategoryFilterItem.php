<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Category;

class CategoryFilterItem extends Component
{
    public Category $category;
    public int $level;

    /**
     * Create a new component instance.
     */
    public function __construct(Category $category, int $level = 0)
    {
        $this->category = $category;
        $this->level = $level;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pages.shop.category-filter-item');
    }
}
