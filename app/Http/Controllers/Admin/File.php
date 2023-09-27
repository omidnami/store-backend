<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

class File extends Controller
{
    public static function serverSide($data = []) {
//         add single file to gallery
//         file uploads and blob
//         after upload file return boolean status
        $imageName = time().rand(111111,999999).'.'.$data['file']->extension();

        $destinationPath = public_path('uploads/'.$data['type']);
        $format = $data['file']->extension();
        $size = $data['file']->getSize();

        $data['file']->move($destinationPath, $imageName);

        //resize
//        $image_resize = Image::make($data['file']->getRealPath());
//        $image_resize->resize(300, 300);
//        $image_resize->save(public_path($destinationPath.'/'.'md_'.$imageName));
error_log($data['def']);
         \App\Models\File::create([
                'title' => $data['slug'],
                'pid' => $data['pid'],
                'type' => $data['type'],
                'url' => '/uploads/'.$data['type'].'/'.$imageName,
                'data' => json_encode(['size'=>$size,'format'=>$format]),
                'def' => isset($data['def'])?$data['def']:false
        ]);

        return json_encode((object)[
            'url' => '/uploads/'.$data['type'].'/'.$imageName,
            'size' => $size,
            'format' => $format,
            'name' => $imageName,
            'def' => isset($data['def'])?$data['def']:false
        ]);
    }

    public function clientSide() {

    }

    public function byApi() {

    }

    public function setLink() {

    }

    public static function fileDelete($file) {
        $image_path = public_path($file);
        if(file_exists($image_path)){
            unlink($image_path);
        }
        return true;
    }
}
