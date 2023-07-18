<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\File;
use App\Models\ProductCat;
use App\Models\SearchEngin;
use Illuminate\Http\Request;
use Exception;

class ProductCatController extends Controller
{

    public function select($id = 0){
        if ($id) {
            return ProductCat::find($id);
        }
        return ProductCat::all();
    }

    public function insert(Request $request) {
        $request['slug'] = str_replace(' ','_',$request['title']);

        try {
            ProductCat::create($request->all());
            //if isset request file => set file(s) to addGallery
            if (isset($request->img) and $request->img) {

                $this->addGallery([
                    'img' => $request['img'],
                    'slug' => $request['slug']
                ]);
            }
            //if text !== null or '' or isset text request set text to article
            if (isset($request['text']) and !is_null($request['text'])) {
                $this->article([
                    'slug' => $request['slug'],
                    'text' => $request['text']
                ]);
            }

            //add search engin options
            $this->searchEngin([
                'slug' => $request['slug'],
                'meta_key' => $request['tags'] ?? null,
                'description' => $request['description'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }
        return ResponseHelper::success(__('response.cat_added'));
    }

    public function update(Request $request) {
        if (!isset($request['id'])){
            return ResponseHelper::error(__('response.id_ex',false));
        }

        ProductCat::where('id',$request->id)
            ->update($request->all());

        return ResponseHelper::success(__('response.cat_updated'));
    }

    public function delete(Request $request) {
        $del = $this->select($request->id);
        $del->delete();
        return ResponseHelper::success(__('response.cat_deleted'));
    }

    private function addGallery($file) {
//         add single file to gallery
//         file uploads and blob
//         after upload file return boolean status
        $imageName = time().rand(111111,999999).'.'.$file['img']->extension();
        $format = $file['img']->extension();
        $size = $file['img']->getSize();
        $file['img']->move(public_path('uploads/products'), $imageName);

        $cat = ProductCat::where('slug', $file['slug'])->first();
        return File::create([
            'title' => $cat->slug,
            'pid' => $cat->id,
            'type' => 'product_cat',
            'url' => '/uploads/products/'.$imageName,
            'data' => json_encode(['size'=>$size,'format'=>$format])
        ]);

    }

    private function article(array $data) {
        //array data text cat slug
        //get catID with slug
        //set type product_cat
        //set full description cat
        $cat = ProductCat::where('slug', $data['slug'])->first();
        return Article::create([
            'type' => 'product_cat',
            'pid' => $cat->id,
            'text' => $data['text']
        ]);

    }

    private function searchEngin(array $data) {
        //array data search engine and sk
        //get productID with sk
        //set type product
        //set  search engin options
        $cat = ProductCat::where('sk', $data['sk'])->first();
        return SearchEngin::create([
            'type' => 'product_cat',
            'pid' => $cat->id,
            'meta_key' => $data['meta_key'],
            'meta_description' => $data['description'],
            'canonical' => $data['canonical'],
        ]);
    }
}
