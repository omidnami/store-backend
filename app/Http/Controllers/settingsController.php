<?php

namespace App\Http\Controllers;

use App\Models\conf;
use Illuminate\Http\Request;

class settingsController extends Controller
{

    public function make() {
        if (!conf::all()){
           return conf::create([
                'title' => 'title your site',
                'domain' => 'example.com',
                'logo' => '',
                'icon' => '',
                'lang' => 'EN'
            ]);
        }
        return true;
    }
    public function glob() {
        $this->make();
        return conf::all();
    }
    public function glob_update(Request $request) {
        $this->make();
    }


}
