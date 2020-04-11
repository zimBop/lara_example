<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VehicleRequest;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use App\Models\Vehicle;


class VehicleController extends Controller
{
    // TODO: refactor in future for multiple brands
    protected const TESLA = 1;

    public function index(Request $request)
    {
        $vehicles = Vehicle::latest()->paginate(10);
        return view('admin.vehicles.index', get_defined_vars());
    }

    public function create()
    {
        $brands = VehicleService::getBrands();
        $models = VehicleService::getModels(self::TESLA);
        $statuses = VehicleService::getStatuses();
        $colors = VehicleService::getColors();

        return view('admin.vehicles.edit', get_defined_vars());
    }

    public function edit(Vehicle $vehicle)
    {
        $brands = VehicleService::getBrands();
        $models = VehicleService::getModels(self::TESLA);
        $statuses = VehicleService::getStatuses();
        $colors = VehicleService::getColors();

        return view('admin.vehicles.edit', get_defined_vars());
    }

    public function store(VehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();
        $vehicle->fill($data)->save();

        return redirect()
            ->route(R_ADMIN_VEHICLES_LIST)
            ->with('success', 'Vehicle\'s information successfully saved');
    }

    public function delete(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()
            ->route(R_ADMIN_VEHICLES_LIST)
            ->with('success', 'Vehicle successfully removed');
    }
}
