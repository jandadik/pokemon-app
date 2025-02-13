<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserParameters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserParametersController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parameters = $user->parameters;

        if (!$parameters) {
            $parameters = $user->parameters()->create([
                'settings' => json_encode([])
            ]);
        }

        // Dekódujeme JSON ze settings a vrátíme jako objekt
        return response()->json(
            json_decode($parameters->settings) ?? []
        );
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $parameters = $user->parameters;

        if (!$parameters) {
            $parameters = $user->parameters()->create([
                'settings' => json_encode($request->all())
            ]);
        } else {
            $parameters->update([
                'settings' => json_encode($request->all())
            ]);
        }

        return response()->json(
            json_decode($parameters->settings)
        );
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $parameters = $user->parameters;

        if (!$parameters) {
            $parameters = $user->parameters()->create([
                'settings' => json_encode($request->all())
            ]);
        }

        return response()->json(['message' => 'Parameters saved successfully.']);
    }
}
