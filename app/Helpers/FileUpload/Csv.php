<?php

namespace App\Helpers\FileUpload;

use Illuminate\Http\UploadedFile;

class Csv extends FileUploadAbstract {
    protected function __construct(UploadedFile $file) {
        parent::__construct($file);
    }
}