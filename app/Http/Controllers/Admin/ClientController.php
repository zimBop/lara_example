<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        $clients = Client::paginate(25);
        return view('admin.clients_index', get_defined_vars());
    }

    public function changeActivity(Request $request)
    {
        $client = new Client();
        $status = $client->changeActivity($request->clientId);
        return response()->json(['status' => $status ? 'success' : 'error']);
    }
}
