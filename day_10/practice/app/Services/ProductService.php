<?php

namespace App\Services;

use App\DTOs\ProductCreateDTO;
use App\Models\Product;
use Illuminate\Support\Str;

final class ProductService
{
    public function store(ProductCreateDTO $data)
    {
        $slug = $this->generateSlug($data->name);

        Product::create([
            'name' => $data->name,
            'price' => $data->price,
            'description' => $data->description,
            'slug' => $slug,
            'cat_id' => $data->cat_id
        ]);

        return [
            'success' => true
        ];
    }

    public function getBySlug(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return $product;
    }

    public function getAll()
    {
        $products = Product::all();

        return $products;
    }

    private function generateSlug(string $name): string
    {
        $slug = Str::slug($name);

        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
