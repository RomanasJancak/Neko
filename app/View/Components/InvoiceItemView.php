<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use App\Models\InvoiceItem;

class InvoiceItemView extends Component
{
    public InvoiceItem $item;
    /**
     * Create a new component instance.
     */
    public function __construct(InvoiceItem $item)
    {
        $this->item = $item->load('jobs');
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.invoice-item.view');
    }
}
