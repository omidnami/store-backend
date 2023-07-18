<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\File;
use App\Models\Product;
use App\Models\SearchEngin;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function select($id = 0) {
        if ($id) {
            return Product::find($id);
        }
        return  Product::all();
    }

    public function insert(Request $request) {
        $request['sk'] = rand(10000,99999);
        $request['slug'] = str_replace(' ','_',$request['title']);
        try {
            Product::create($request->all());
            //if isset request file => set file(s) to addGallery
            if (isset($request->img) and $request->img) {

                $this->addGallery([
                    'img' => $request['img'],
                    'sk' => $request['sk']
                ]);
            }

            //if text !== null or '' or isset text request set text to article
            if (isset($request['text']) and !is_null($request['text'])) {
               $this->article([
                    'sk' => $request['sk'],
                    'text' => $request['text']
               ]);
            }

            //add search engin options
            $this->searchEngin([
                'sk' => $request['sk'],
                'meta_key' => $request['tags'] ?? null,
                'description' => $request['description'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }catch (Exception $e){

           return ResponseHelper::error($e->getMessage(), false);
        }


        return ResponseHelper::success(__('response.product_inserted'));
    }

    public function update(Request $request) {
        if (!isset($request['id'])){
            return ResponseHelper::error(__('response.id_ex',false));
        }

        Product::where('id',$request->id)
            ->update($request->all());

        return ResponseHelper::success(__('response.product_updated'));
    }

    public function delete(Request $request) {
        $del = $this->select($request->id);
        $del->delete();
        return ResponseHelper::success(__('response.product_deleted'));
    }

    private function addGallery($file) {
//         add single file to gallery
//         file uploads and blob
//         after upload file return boolean status
        $imageName = time().rand(111111,999999).'.'.$file['img']->extension();
        $format = $file['img']->extension();
        $size = $file['img']->getSize();
        $file['img']->move(public_path('uploads/products'), $imageName);

        $product = Product::where('sk', $file['sk'])->first();
       return File::create([
            'title' => $product->slug,
            'pid' => $product->id,
            'type' => 'product',
            'url' => '/uploads/products/'.$imageName,
            'data' => json_encode(['size'=>$size,'format'=>$format])
        ]);

    }

    private function article(array $data) {
        //array data text product sk
        //get productID with sk
        //set type product
        //set full description product
        $product = Product::where('sk', $data['sk'])->first();
        return Article::create([
            'type' => 'product',
            'pid' => $product->id,
            'text' => $data['text']
        ]);

    }

    private function searchEngin(array $data) {
        //array data search engine and sk
        //get productID with sk
        //set type product
        //set  search engin options
        $product = Product::where('sk', $data['sk'])->first();
        return SearchEngin::create([
            'type' => 'product',
            'pid' => $product->id,
            'meta_key' => $data['meta_key'],
            'meta_description' => $data['description'],
            'canonical' => $data['canonical'],
        ]);
    }
}
