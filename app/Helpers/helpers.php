<?php

use Illuminate\Support\Facades\Storage;

/**
 * Upload base64 images
 */
if (!function_exists('upload_base64_image')) {
    function upload_base64_image($image, $path = 'uploads/', $name = null)
    {
        [$imageType, $image_base64] = explode(';', $image);
        $imageData = base64_decode(explode(',', $image_base64)[1]);
        $file = $path . ($name ?? uniqid()) . '.' . explode('/', $imageType)[1];
        Storage::put($file, $imageData);
        return $file;
    }
}
