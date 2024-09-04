<?php

namespace App\Classes;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\Drivers\Gd\Driver;

class Helper
{
    public static function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success' => true,
            'msg'     => $message,
            'result'  => $result
        ];

        return response()->json($response, $code);
    }

    public static function sendError($message, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public static function checkPaginateSize($request)
    {
        $paginateSize        = $request->paginate_size;
        $maxPaginateSize     = config('crud.paginate_size.max');
        $defaultPaginateSize = config('crud.paginate_size.default');
        $paginateSize        = $paginateSize ?? $defaultPaginateSize;
        $paginateSize        = $paginateSize > $maxPaginateSize ? $maxPaginateSize : $paginateSize;

        return $paginateSize;
    }

    public static function uploadFile($obj, $file, $uploadPath, $oldFilePath = null, $width = 450, $height = 450)
    {
        if ($file) {
            // Delete old file
            if ($oldFilePath) {
                $oldFilePath = public_path($oldFilePath);

                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            // Upload new image
            $fileName = time() . '.webp';
            $imgPath  = $uploadPath . "/" . $fileName;
            $filePath = $file->move(public_path($uploadPath), $fileName);


            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $img = $manager->read($filePath);

            // resize by width and height
            $img->resize($width, $height);

            $img->save($filePath);

            $obj->img_path = $imgPath;
            $obj->save();
        }
    }

    public static function getFilePath($filePath)
    {
        if ($filePath) {
            if (File::exists(public_path($filePath))) {
                $imagePath = asset($filePath);
            } else {
                $imagePath = asset('uploads/default.png');
            }
        } else {
            $imagePath = asset('uploads/default.png');
        }

        return $imagePath;
    }

    public static function deleteFile($filePath)
    {
        // Delete file
        if ($filePath) {
            $filePath = public_path($filePath);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    public static function updateEnvVariable(array $data)
    {
        if (count($data)) {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            foreach ($data as $key => $value) {
                // Create a regex pattern to match the env key and update its value
                $pattern = "/^{$key}=(.*)$/m";
                $replacement = "{$key}=\"{$value}\"";

                if (preg_match($pattern, $envContent)) {
                    // Update existing key
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    // If the key does not exist, add it
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envPath, $envContent);

            Artisan::call('optimize:clear');
        }

        return true;
    }
}
