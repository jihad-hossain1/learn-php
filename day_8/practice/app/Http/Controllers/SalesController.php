<?php

namespace App\Http\Controllers;

class SalesController extends Controller
{
    public function index()
    {
        return 'here sales list';
    }

    public function show(int $id)
    {
        return "Sales ID: {$id}";
    }

    public function reports()
    {
        return 'Here Sales Reports Generate: 1,2,3';
    }

    public function reportsView(int $id)
    {
        return "Sales report view on ID: {$id}";
    }
}
