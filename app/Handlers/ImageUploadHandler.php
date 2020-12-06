<?php


namespace App\Handlers;


use Illuminate\Support\Str;

class ImageUploadHandler
{
    // 允许的图片后缀
    protected $allowedExtension = ['png', 'gif', 'jpg', 'jpeg'];

    public function save($file, $folder, $file_prefix)
    {
        // 存储图片的文件路径, 不包含文件名
        $folderName = "uploads/images/$folder/" . date('Ym/d', time());

        $uploadPath = public_path() . '/' . $folderName;

        // 文件后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        // 文件名
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        if (! in_array($extension,$this->allowedExtension)) {
            return false;
        }
        $file->move($uploadPath,$filename);

        return [
            'path' => config('app.url') . "/$folderName/$filename"
        ];
    }
}