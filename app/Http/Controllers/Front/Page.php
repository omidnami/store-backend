<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SearchEngin;
use Illuminate\Http\Request;

class Page extends Controller
{
    //
    public function select(Request $request) {
        $page = \App\Models\Page::where('slug', $request->slug)->first();
        if (!$page) {
            return json_encode((object)['status'=>false,'msg'=> 'get_404']);
        }
        $seo = SearchEngin::where('type', 'page')->where('id', $page->id);
        return (object)['page'=> $page, 'seo', $seo];
    }
    public function home(Request $request) {
        $page = \App\Models\Page::where('home', true)->first();
        if (!$page) {
            return json_encode((object)['status'=>false,'msg'=> 'get_404']);
        }
        $seo = SearchEngin::where('type', 'page')->where('id', $page->id);
        return (object)['page'=> $page, 'seo'=> $seo];
    }
}
