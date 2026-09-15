<?php 

namespace App\Http\Controllers;



class ClientController extends Controller
{
    public function index(): string 
    {
        return "client list here.";
    }

    public function show(): string 
    {
        return "client show here.";
    }

    public function superAdminOrder(): string 
    {
        return "Get Super Admin Order";
    }
    
    public function superAdminUsers(): string 
    {
        return "Get Super Admin Users";
    }
}

