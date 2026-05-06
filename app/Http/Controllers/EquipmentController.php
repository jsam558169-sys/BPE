<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = \App\Models\Equipment::all();
        return view('dashboard', compact('equipment'));
    }
}
