<?php

namespace App\Service;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload multiple files and return their paths.
     *
     * @param array $files
     * @param string $directory
     * @return array
     */
    public function uploadFiles(array $files, string $directory = 'assignments')
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $filePaths[] = $file->store($directory, 'public');
        }

        return $filePaths;
    }

    public function uploadImage($image, string $directory = 'images'){
        return $image->store($directory, 'public');
    }

    
    /**
     * Delete files from storage.
     *
     * @param array $filePaths
     * @return void
     */
    public function deleteFiles(array $filePaths): void
    {
        foreach ($filePaths as $file) {
            Storage::disk('public')->delete($file);
        }
    }

    public function deleteImage($image){
        Storage::disk('public')->delete($image);
    }

    /**
     * Download a file securely.
     *
     * @param string $filePath
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFile(string $filePath)
    {
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($filePath);
    }
}
