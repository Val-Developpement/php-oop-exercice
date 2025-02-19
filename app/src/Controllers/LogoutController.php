<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Commands\ConnectDatabase;

class LogoutController extends AbstractController
{


    public function process(Request $request): Response
    {
        session_start();
        session_destroy();
        return new Response(json_encode(['message' => 'Logout successful']), 200);
    }
}
