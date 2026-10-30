<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductCreateRequest;
use App\Services\CodeService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CodeService $codeService
    ) {}

    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $products = $this->productService->getAll();

        return view('products.index', [
            'products' => $products,
            'search' => $search
        ]);
    }

    public function newform()
    {
        $lookups = $this->codeService->getByType('product');

        return view("products.new", [
            'lookups' => $lookups ?? []
        ]);
    }

    public function store(ProductCreateRequest $request)
    {
        $this->productService->store($request->toDTO());

        return response()->json([
            'success' => 'create done.'
        ]);
    }

    public function show(string $slug)
    {
        $result = $this->productService->getBySlug($slug);

        return view('products.view', [
            'slug' => $slug,
            'product' => $result,
        ]);
    }
}
