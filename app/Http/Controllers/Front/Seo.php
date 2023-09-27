<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SearchEngin;
use Illuminate\Http\Request;

class Seo extends Controller
{

    public function select(Request $request) {
        switch ($request->type) {

        }
        SearchEngin::where()->get();
    }
}
