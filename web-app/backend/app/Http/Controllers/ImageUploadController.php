<?php

namespace App\Http\Controllers;


class ImageUploadController extends Controller
{
    public function upload()
    {
        $base64Image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', request('profile_picture')));
        $publicDirectory = public_path("customer/profile_picture");
        if (!file_exists($publicDirectory)) {
            mkdir($publicDirectory, 0777, true);
        }
        file_put_contents($publicDirectory . '/' . request('imageName'), $base64Image);
        return request('imageName');
    }
}
