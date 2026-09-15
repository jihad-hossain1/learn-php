<?php 

namespace App\Http\Controllers;

abstract class TodoController
{
    public function index(): string 
    {
       return 'client todo retrieve done';
    }
}