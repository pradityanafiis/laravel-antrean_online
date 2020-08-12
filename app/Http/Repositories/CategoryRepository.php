<?php

namespace App\Http\Repositories;

use App\Category;

class CategoryRepository
{
    private Category $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    public function findByName($name)
    {
        return $this->category
            ->where('name', $name)
            ->first();
    }
}
