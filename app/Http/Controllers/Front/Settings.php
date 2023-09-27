<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\conf;
use Illuminate\Http\Request;

class Settings extends Controller
{
    public function select(Request $request) {
        return conf::where('domain', $request->header('origin').'/')->first();
    }
}
