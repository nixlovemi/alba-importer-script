<?php

namespace App\Presenters;

use App\Helpers\FileUpload\FileUploadAbstract;

final class MainControllerPresenter
{
    public const INDEX_PAGE_TITLE = 'Home';
    public const COLUMNS_PAGE_TITLE = 'Mapear Colunas';

    public static function getIndexDescription(): string
    {
        $allowedExtensions = implode(', ', FileUploadAbstract::ALLOWED_EXTENSIONS);
        return "Você pode utilizar esse formulário para converter seu arquivo {$allowedExtensions} em um formato compatível com o sistema Alba.";
    }

    public static function getIndexFormFileLabel(): string
    {
        $allowedExtensions = implode(', ', FileUploadAbstract::ALLOWED_EXTENSIONS);
        $maxFileSizeMB = FileUploadAbstract::MAX_FILE_SIZE / (1024 * 1024);
        return "Arquivo {$allowedExtensions} (máx. {$maxFileSizeMB}MB)";
    }

    public static function getIndexFileAcceptString(): string
    {
        $allowedExtensions = array_map(fn($ext) => '.' . $ext, FileUploadAbstract::ALLOWED_EXTENSIONS);
        return implode(', ', $allowedExtensions);
    }

    public static function getIndexTextForSitesToConvert(): string
    {
        $allowedExtensions = implode(', ', FileUploadAbstract::ALLOWED_EXTENSIONS);
        $sites = [
            '* <a href="https://cloudconvert.com/xls-to-csv" target="_blank" rel="noopener noreferrer">https://cloudconvert.com/xls-to-csv</a>',
            '* <a href="https://cloudconvert.com/xlsx-to-csv" target="_blank" rel="noopener noreferrer">https://cloudconvert.com/xlsx-to-csv</a>',
        ];
        return "Se necessário, utilize os seguintes sites para converter seus arquivos para o formato {$allowedExtensions} antes de enviá-los: <br /><br />" . implode('<br />', $sites);
    }

    public static function getColumnsDescription(): string
    {
        return "Use esse formulário para mapear as colunas do seu arquivo para os campos do sistema Alba. Você pode deixar campos em branco se não quiser importar determinados dados. Após mapear as colunas, você poderá baixar o arquivo convertido.";
    }
}