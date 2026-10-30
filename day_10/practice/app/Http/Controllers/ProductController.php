<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\ProductCreateRequest;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function store(ProductCreateRequest $request)
    {
        $this->productService->store($request->toDTO());

        return response()->json([
            'success' => 'create done.'
        ]);
    }
}
