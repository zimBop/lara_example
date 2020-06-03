<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Disk;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DriverRequest;
use App\Models\Avatar;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{

    public function index(Request $request)
    {
        $drivers = Driver::latest()->paginate(25);
        return view('admin.drivers.index', get_defined_vars());
    }

    public function create()
    {
        return view('admin.drivers.edit');
    }

    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', get_defined_vars());
    }

    public function store(DriverRequest $request, Driver $driver)
    {
        $data = $request->validated();

        if ($request->hasFile(Avatar::FILE_INPUT_NAME)) {
            AvatarService::update($request->file(Avatar::FILE_INPUT_NAME), $driver, Disk::DRIVER_AVATARS);
        }

        if ($request->has('delete_avatar')) {
            AvatarService::delete($driver, Disk::DRIVER_AVATARS);
        }

        $driver->fill($data)->save();

        return redirect()
            ->route(R_ADMIN_DRIVERS_LIST)
            ->with('success', 'Driver\'s information successfully saved');
    }

    public function delete(Driver $driver)
    {
        $driver->delete();

        return redirect()
            ->route(R_ADMIN_DRIVERS_LIST)
            ->with('success', 'Driver successfully removed');
    }
}
