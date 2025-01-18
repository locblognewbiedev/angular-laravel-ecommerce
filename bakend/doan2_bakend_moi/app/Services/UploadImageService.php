<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UploadImageService
{
    static function uploadImage(string $img)
    {
        // // Kiểm tra chuỗi có phải Base64 hay không
        // if (preg_match('/^data:image\/(\w+);base64,/', $img, $type)) {
        //     $img = substr($img, strpos($img, ',') + 1);
        //     $type = strtolower($type[1]); // jpg, png, gif, etc.

        //     if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
        //         throw new \Exception('Loại tệp không được hỗ trợ.');
        //     }

        //     $img = base64_decode($img);

        //     if ($img === false) {
        //         throw new \Exception('Giải mã Base64 thất bại.');
        //     }
        // }

        return $img;
        //  else {
        //     throw new \Exception('Dữ liệu không phải là Base64.');
        // }

        // // Tạo tên file duy nhất
        // $fileName = Str::uuid()->toString() . '.' . $type;

        // // Thư mục lưu trữ (storage/app/public/images)
        // $filePath = 'public/images/' . $fileName;

        // // Lưu ảnh vào hệ thống
        // Storage::put($filePath, $img);

        // // Trả về đường dẫn URL
        // return Storage::url($filePath);
    }
}
