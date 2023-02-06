<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\View\Components;

use Illuminate\View\Component;

class Label extends Component
{
    /** @var string */
    public $name;

    /**
     * Create the component instance.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('marketing::components.label');
    }
}
