<?php

namespace App\Helpers\FileUpload;

use Illuminate\Http\UploadedFile;

class Xls extends FileUploadAbstract {
    protected function __construct(UploadedFile $file) {
        parent::__construct($file);
    }
}