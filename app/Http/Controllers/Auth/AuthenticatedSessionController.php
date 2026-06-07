<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    // MANIPULASI LOGIN: Klik login langsung ke Dashboard (Home)
    public function create() { 
        return response('<html><body><form action="/login" method="POST"><button name="iniLogin">Login</button></form></body></html>'); 
    }
    public function store(Request $request) { 
        return redirect('/'); 
    }
}
