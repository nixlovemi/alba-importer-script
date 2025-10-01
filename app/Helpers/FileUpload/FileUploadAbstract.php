<?php

namespace App\Helpers\FileUpload;

use Illuminate\Http\UploadedFile;

abstract class FileUploadAbstract
{
    public const ALLOWED_EXTENSIONS = ['csv'];
    public const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    private UploadedFile $file;
    private string $fileExtension;
    private array $albaColumns = [
        'Address_ID',
        'Territory_ID',
        'Language',
        'Status',
        'Name',
        'Suite',
        'Address',
        'City',
        'Province',
        'Postal_code',
        'Country',
        'Latitude',
        'Longitude',
        'Telephone',
        'Notes',
        'Notes_private',
    ];

    protected function __construct(UploadedFile $file) {
        $this->file = $file;
        $this->fileExtension = strtolower($file->getClientOriginalExtension());
    }

    public static function getValidatorString(): string
    {
        $extensions = implode(',', self::ALLOWED_EXTENSIONS);
        return "required|file|mimes:$extensions|max:" . (self::MAX_FILE_SIZE / 1024);
    }

    /**
     * @param UploadedFile $file
     * @return self
     * @throws Exception
     */
    final public static function init(UploadedFile $file): self
    {
        $fileExtension = strtolower($file->getClientOriginalExtension());
        if (!in_array($fileExtension, self::ALLOWED_EXTENSIONS)) {
            throw new \InvalidArgumentException('Apenas arquivos os seguintes são permitidos: ' . implode(', ', self::ALLOWED_EXTENSIONS) . '.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('O tamanho máximo permitido para o arquivo é ' . (self::MAX_FILE_SIZE / (1024 * 1024)) . ' MB.');
        }

        switch ($fileExtension) {
            case 'csv':
                return new Csv($file);
            case 'xls':
                return new Xls($file);
            default:
                throw new \Exception("Tipo de arquivo não suportado.");
        }
    }

    final public static function initByFilePath(string $filePath): self
    {
        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new \InvalidArgumentException("O arquivo especificado não existe.");
        }

        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, self::ALLOWED_EXTENSIONS)) {
            throw new \InvalidArgumentException('Apenas arquivos os seguintes são permitidos: ' . implode(', ', self::ALLOWED_EXTENSIONS) . '.');
        }

        // read file path and create a temporary UploadedFile instance
        $tempFile = new UploadedFile($filePath, basename($filePath), null, null, true);

        switch ($fileExtension) {
            case 'csv':
                return new Csv($tempFile);
            case 'xls':
                return new Xls($tempFile);
            default:
                throw new \Exception("Tipo de arquivo não suportado.");
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    final public function saveFile(): string
    {
        $storagePath = storage_path('app/uploads');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filename = uniqid('upload_', true) . '.' . $this->fileExtension;
        $fullPath = $storagePath . '/' . $filename;

        if (!$this->file->move($storagePath, $filename)) {
            throw new \Exception("Falha ao mover o arquivo para o diretório de uploads.");
        }

        return $fullPath;
    }

    final public function removeFile(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    final public function getCsvColumnsArray(string $delimiter = ',', string $enclosure = '"', bool $skipEmpty = true): array
    {
        $columns = [];
        if (($handle = fopen($this->file->getRealPath(), 'r')) !== false) {
            if (($data = fgetcsv($handle, 0, $delimiter, $enclosure)) !== false) {
                foreach ($data as $column) {
                    $trimmed = trim($column);
                    if ($skipEmpty && empty($trimmed)) {
                        continue;
                    }
                    $columns[] = $trimmed;
                }
            }
            fclose($handle);
        }
        return $columns;
    }

    final public function getCsvColumnsAsHtmlSelect(string $selectName, string $selectId, array $selectedValues = [], string $cssClass = 'form-control', string $placeholder = 'Selecione uma coluna', string $delimiter = ',', string $enclosure = '"', bool $skipEmpty = true): string
    {
        $columns = $this->getCsvColumnsArray($delimiter, $enclosure, $skipEmpty);
        $html = "<select name=\"{$selectName}\" id=\"{$selectId}\" class=\"{$cssClass}\">";
        if ($placeholder) {
            $html .= "<option value=\"\">{$placeholder}</option>";
        }
        foreach ($columns as $column) {
            $isSelected = in_array($column, $selectedValues) ? 'selected' : '';
            $html .= "<option value=\"" . htmlspecialchars($column) . "\" {$isSelected}>" . htmlspecialchars($column) . "</option>";
        }
        $html .= "</select>";
        return $html;
    }

    final public function getAlbaColumns(): array
    {
        return $this->albaColumns;
    }

    final public function getAlbaColumnsForMapping(): array
    {
        return array_filter($this->albaColumns, fn($col) => $col !== 'Address_ID' && $col !== 'Territory_ID');
    }

    /**
     * @throws Exception
     */
    final public function generateMappedCsvFile(array $mapping, string $delimiter = ',', string $enclosure = '"'): string
    {
        if (empty($mapping)) {
            throw new \InvalidArgumentException("O mapeamento de colunas não pode estar vazio.");
        }

        $inputHandle = fopen($this->file->getRealPath(), 'r');
        if ($inputHandle === false) {
            throw new \Exception("Não foi possível abrir o arquivo de entrada.");
        }

        $outputFilePath = storage_path('app/processed/' . uniqid('mapped_', true) . '.csv');
        // Ensure the output directory exists
        if (!file_exists(dirname($outputFilePath))) {
            mkdir(dirname($outputFilePath), 0755, true);
        }

        // Create the output file
        if (!file_exists($outputFilePath)) {
            touch($outputFilePath);
        }

        $outputHandle = fopen($outputFilePath, 'w');
        if ($outputHandle === false) {
            fclose($inputHandle);
            throw new \Exception("Não foi possível criar o arquivo de saída.");
        }

        // Write header with all Alba columns. $mapping only contains mapped columns, so we need to ensure all are present.
        fputcsv($outputHandle, $this->getAlbaColumns(), $delimiter, $enclosure);

        // Skip the first row (header row) from the input file
        fgetcsv($inputHandle, 0, $delimiter, $enclosure);

        // loop through alba columns to ensure all are present in the output
        // and map them to the input file columns if provided
        while (($data = fgetcsv($inputHandle, 0, $delimiter, $enclosure)) !== false) {
            $row = [];
            foreach ($this->getAlbaColumns() as $albaColumn) {
                if (isset($mapping[$albaColumn]) && $mapping[$albaColumn] !== '') {
                    $inputColumn = $mapping[$albaColumn];
                    $inputIndex = array_search($inputColumn, $this->getCsvColumnsArray($delimiter, $enclosure, true));
                    $row[] = $inputIndex !== false && isset($data[$inputIndex]) ? $data[$inputIndex] : '';
                } else {
                    $row[] = '';
                }
            }
            fputcsv($outputHandle, $row, $delimiter, $enclosure);
        }

        fclose($inputHandle);
        fclose($outputHandle);

        // delete the input file
        $this->removeFile($this->file->getRealPath());
        
        return $outputFilePath;
    }
}