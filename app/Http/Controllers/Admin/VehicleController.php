<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{

    public function index(Request $request)
    {
        $vehicles = Vehicle::latest()->paginate(10);
        return view('admin.vehicles_index', get_defined_vars());
    }
}
