<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait ImageUpload
{
    public function storeImage($image, $disk = 'public', $path = null)
    {
        // Generate a unique filename with extension
        // getClientOriginalName
        $imageName = uniqid('', true) . '.' . $image->getClientOriginalName();

        // Sanitize the filename to prevent potential vulnerabilities
        $imageName = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '_', $imageName);

        // Ensure the file extension is allowed
        $allowedExtensions = ['jpeg', 'jpg', 'png']; // Adjust as needed
        if (!in_array($image->getClientOriginalExtension(), $allowedExtensions)) {
            throw new Exception('Invalid image file extension. Allowed extensions: ' . implode(', ', $allowedExtensions));
        }

        // Check maximum file size (optional)
        if ($image->getSize() > 2048 * 1024) { // 2MB limit
            throw new Exception('Image file exceeds maximum size of 2MB.');
        }

        // Determine the storage path
        if (!$path) {
            $path = $this->getImagePath();
        }

        // Store the image on the specified disk
        Storage::disk($disk)->put($path . '/' . $imageName, $image);

        return $imageName;
    }
    public function deleteImage($imageName, $disk = 'public', $path)
    {

        // Check if the image file exists
        // if (Storage::disk($disk)->exists($path . '/' . $imageName)) {
        //     // Storage::disk($disk)->delete($path . '/' . $imageName);
        //     $image_path = public_path().'/Authors'.'/'.$imageName;
        //     Storage::delete($image_path);
        //     echo "Image deleted successfully";
        // } else {
        //     throw new Exception('Image not found: ' . $imageName);
        // }
        if(File::exists(storage_path('app/' . $disk . '/' . $path . '/' . $imageName))        ){
            File::delete(storage_path('app/' . $disk . '/' . $path . '/' . $imageName));
        }else{
            echo('File does not exists.');
        }
    }
    // Optional function to define image path within the disk (customize as needed)
    private function getImagePath()
    {
        return 'images'; // Example: Store images in 'images' folder within the disk
    }
}
