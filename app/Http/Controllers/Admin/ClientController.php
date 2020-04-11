<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        $clients = Client::latest()->paginate(25);
        return view('admin.clients_index', get_defined_vars());
    }

    public function changeActivity(Client $client, ClientService $clientService)
    {
        $status = $clientService->setClient($client)
            ->changeActivity();

        return response()->json(['status' => $status ? 'success' : 'error']);
    }
}
