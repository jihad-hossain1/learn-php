<?php

namespace App\Services;

use App\DTOs\ProductCreateDTO;
use App\DTOs\ProductUpdateDTO;
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
        return Product::with('code')->get();
    }

    public function update(ProductUpdateDTO $data)
    {
        $store_data = Product::find('slug', $data->slug);
        $store_data->updateOrFail([
            'name' => $data->name,
            'price' => $data->price,
            'description' => $data->description,
            'cat_id' => $data->cat_id
        ]);

        return ['success' => true];
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
