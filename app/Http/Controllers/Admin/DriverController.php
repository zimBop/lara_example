<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{

    public function index(Request $request)
    {
        $drivers = Driver::latest()->paginate(25);
        return view('admin.drivers.index', get_defined_vars());
    }
}
