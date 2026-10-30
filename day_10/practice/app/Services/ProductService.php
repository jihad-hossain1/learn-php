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
            'slug' => $slug
        ]);

        return [
            'success' => true
        ];
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
