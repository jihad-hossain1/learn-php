<?php


namespace App\Services;

use App\Models\Product;

class ProductService 
{
   public array $products = [];

    public function store(string $name, float $price): void
    {

        $product = new Product($name, $price);

        $this->products[] = $product;
    }

    public function getAllProducts(): array
    {
        return $this->products;
    }
}


