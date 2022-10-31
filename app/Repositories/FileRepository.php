<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\FileRelation;
use App\Models\FileRelationType;
use Illuminate\Support\Facades\Storage;

class FileRepository
{
    public function createNewFile(array $validated): File
    {
        $file = $validated['file'];
        $name = $file->hashName();
        $extension = $file->extension();
        $path = Storage::putFileAs(
            'photos',
            $validated['file'],
            $name,
        );

        $file_model = new File();
        $file_model->filename = $name;
        $file_model->filepath = $path;
        $file_model->filetype = $extension;
        $file_model->file_category_id = $validated['file_category_id'];
        $file_model->save();

        $file_relation = new FileRelation();
        $file_relation->file_id = $file_model->id;
        $file_relation->file_relation_type_id = FileRelationType::CUSTOMER;
        $file_relation->save();

        return $file_model;
    }

    public function generateTemporaryUrl(File $file): string
    {
        return Storage::temporaryUrl($file->filepath, now()->addMinutes(30));
    }
}
