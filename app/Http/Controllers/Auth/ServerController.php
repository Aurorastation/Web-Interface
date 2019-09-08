<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Services\Server\ServerQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;


class ServerController extends Controller
{
    public function beginLogin(Request $request)
    {
        $client_token = 'Inv@lid';
        if($request->has('token'))
        {
            $client_token = $request->input('token');
            // Check if out token is MD5 hash, what it should be
            if(!preg_match('/^[a-f0-9]{32}$/', $client_token)) {
                abort(400, 'Invalid request.');
            }
            // Let's store our token for later use
            $request->session()->put('server_client_token', $client_token);
        } elseif ($request->session()->has('server_client_token')) {
            // If we already have token, aka, we are being redirected, then we just use that token.
            $client_token = $request->session()->pull('server_client_token');
        } else {
            abort(400, 'Invalid request.');
        }

        if (!Auth::check()) {
            return redirect('login');
        }

        $query = New ServerQuery;
        try {
            $query->setUp(config('aurora.gameserver_address'),config('aurora.gameserver_port'),config('aurora.gameserver_auth'));
            $query->runQuery([
                'query' => 'get_auth_client_ip',
                'clienttoken' => $client_token,
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode != '200') {
            abort($query->response->statuscode);
        }

        if($request->getClientIp() !== $query->response->data) {
            redirect()->route('server.login.warn');
        }

        return redirect()->route('server.login.end');
    }

    public function warning(Request $request)
    {
        return view('auth.server.warning');
    }

    public function endLogin(Request $request)
    {
        if(!$request->session()->has('server_client_token') || !Auth::check()) {
            abort(500, 'Invalid state');
        }

        $client_token = $request->session()->pull('server_client_token');
        
        if($request->user()->byond_key == null) {
            return view('auth.server.nokey');
        }
        $query = New ServerQuery;
        try {
            $query->setUp(config('aurora.gameserver_address'),config('aurora.gameserver_port'),config('aurora.gameserver_auth'));
            $query->runQuery([
                'query' => 'auth_client',
                'clienttoken' => $client_token,
                'key' => $request->user()->byond_key
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode == '200') {
            return view('auth.server.success');
        } else {
            abort($query->response->statuscode);
        }
    }
}
