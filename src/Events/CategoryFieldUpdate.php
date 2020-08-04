<?php

namespace PortedCheese\CategoryProduct\Events;

use App\Category;
use Illuminate\Queue\SerializesModels;

class CategoryFieldUpdate
{
    use SerializesModels;

    public $category;

    /**
     * Create a new event instance.
     *
     * CategoryFieldUpdate constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
