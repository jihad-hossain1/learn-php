<?php

namespace App\Http\Controllers;


class ProductController extends Controller
{
    public function index()
    {
        return "product list";
    }

    public function show(int $id)
    {
        return "Product id: {$id}";
    }

    public function customProducts()
    {
        return "admin product list";
    }
}
