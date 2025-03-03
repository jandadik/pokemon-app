<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller pro správu administrátorského rozhraní
 * Zajišťuje základní funkcionalitu admin sekce
 */
class AdminController extends Controller
{
    /**
     * Zobrazí hlavní stránku administrace
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Admin/Index');
    }
} 