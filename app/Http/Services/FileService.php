<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function uploadFile($file, $folderName = 'uploads', $fileName = false): string
    {
        $file_name = Str::slug($fileName) ?? Str::slug($file->getClientOriginalName());
        $fileName = $file_name . '_' . time() . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folderName, $fileName, 'public');
        return $path;
    }
    public function fileDelete($path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
