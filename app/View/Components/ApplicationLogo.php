<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ApplicationLogo extends Component
{
    /**
     * The application logo's component class.
     *
     * @var string
     */
    public $class;

    /**
     * Create a new component instance.
     *
     * @param  string  $class
     * @return void
     */
    public function __construct($class = '')
    {
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.application-logo');
    }
}
