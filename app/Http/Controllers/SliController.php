<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SliController extends Controller
{
    public function sli()
    {
        return response()->json([
            'message' => 'Hello, this is the SLI endpoint!'
        ]);
    }

    public function random_response_time() {
        $response_time = rand(1, 2000);
        usleep($response_time * 1000);
        return response()->json([
            'message' => 'This is a random response time endpoint!',
            'response_time' => $response_time . 'ms'
        ]);
    }
}
