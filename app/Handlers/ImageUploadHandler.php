<?php


namespace App\Handlers;


use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageUploadHandler
{
    // 允许的图片后缀
    protected $allowedExtension = ['png', 'gif', 'jpg', 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        // 存储图片的文件路径, 不包含文件名
        $folderName = "uploads/images/$folder/" . date('Ym/d', time());

        $uploadPath = public_path() . '/' . $folderName;

        // 文件后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        // 文件名
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;

        if (!in_array($extension, $this->allowedExtension)) {
            return false;
        }
        $file->move($uploadPath, $filename);

        if ($max_width && $extension != 'gif') {
            $this->reduceSize($uploadPath . '/' . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folderName/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}