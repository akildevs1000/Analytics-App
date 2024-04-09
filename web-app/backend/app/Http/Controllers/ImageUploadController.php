<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif', // Adjust max size as needed
            'imageName' => 'required|string|max:255', // Assuming image_name is provided in the payload
        ]);


        if ($request->hasFile('image')) {
            $path = "customer/profile_picture/";
            $destinationPath = public_path($path);

            $request->image->move($destinationPath, $request->imageName);
        }

        return public_path($path . $request->imageName);
    }
}
