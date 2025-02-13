<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\RegisterCategory;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        Log::info('Admin controller hit');
        
        try {
            return Inertia::render('Admin/Index', [
                'categories' => RegisterCategory::with('registers')->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in admin controller: ' . $e->getMessage());
            throw $e;
        }
    }
} 