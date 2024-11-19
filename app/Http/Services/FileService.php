<?php

namespace App\Http\Services;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function uploadFile(Request $request): array
    {
        if (!$request->hasFile('file')) {
            return ['error' => 'File not found'];
        }
        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        $size = round($file->getSize() / 1024);
        $stringSize = strval($size).'KB';
        $type = $file->getClientMimeType();
        $path = $file->store('uploads', 'public');

        File::create(array_merge(
            ['name' => $name],
            ['type' => $type],
            ['path' => $path],
            ['size' => $stringSize],
        ));

        return ['success' => 'File has been saved'];
    }

    public function changeNameFile(string $file_key, string $file_name): array
    {
        $file = File::where('file_key', $file_key)->first();
        $file->name = $file_name;
        $file->save();

        return ['success' => 'File name has been modified'];
    }

    public function getFilesSortingNameAsc(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $sortOrder = $request->input('order', 'asc');

        return File::orderBy('name', $sortOrder)->paginate($perPage);
    }

    public function getFilesSortingNameDesc(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $sortOrder = $request->input('order', 'desc');

        return File::orderBy('name', $sortOrder)->paginate($perPage);
    }

    public function getFilesSortingDateAsc(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $sortOrder = $request->input('order', 'asc');

        return File::orderBy('created_at', $sortOrder)->paginate($perPage);
    }

    public function getFilesSortingDateDesc(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        $sortOrder = $request->input('order', 'desc');

        return File::orderBy('created_at', $sortOrder)->paginate($perPage);
    }

    public function deleteFile(string $file_key): array
    {
        $file = File::where('file_key', $file_key)->first();

        if (!$file || !Storage::disk('public')->exists($file->path)) {
            return ['error' => 'File not  found'];
        }

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return ['success' => 'File has been deleted'];
    }
}
