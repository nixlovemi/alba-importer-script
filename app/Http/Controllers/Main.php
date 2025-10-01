<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\FileUpload\FileUploadAbstract;
use App\Presenters\MainControllerPresenter;

class Main extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    private const SESSION_KEY_UPLOADED_FILE_PATH = 'uploaded_file_path';
    private const SESSION_KEY_PROCESSED_FILE_PATH = 'processed_file_path';

    public function index()
    {
        return view('site.index', [
            'PAGE_TITLE' => MainControllerPresenter::INDEX_PAGE_TITLE,
        ]);
    }

    public function sendFile(Request $request)
    {
        $request->validate([
            'file' => FileUploadAbstract::getValidatorString(),
        ]);

        try {
            $file = $request->file('file');
            $fileHandler = FileUploadAbstract::init($file);

            // Process the file (e.g., move it to a storage location)
            $path = $fileHandler->saveFile();

            // save it to session for later use
            $request->session()->put(self::SESSION_KEY_UPLOADED_FILE_PATH, $path);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()]);
        }

        // Here you can add your file processing logic
        // For example, converting the file to a format compatible with the Alba system
        return redirect()->route('site.columns')
            ->with('success', 'Arquivo enviado com sucesso! Agora você pode mapear as colunas.');
    }

    public function columns()
    {
        $filePath = session(self::SESSION_KEY_UPLOADED_FILE_PATH);

        if (!$filePath) {
            return redirect()->route('site.index')
            ->withErrors(['file' => 'Nenhum arquivo enviado. Por favor, envie um arquivo primeiro.']);
        }

        // Here you can add logic to read the file and extract columns
        // For demonstration, we'll just pass the file path to the view
        $fileHandler = FileUploadAbstract::initByFilePath($filePath);

        return view('site.columns', [
            'PAGE_TITLE' => MainControllerPresenter::COLUMNS_PAGE_TITLE,
            'filePath' => $filePath,
            'fileHandler' => $fileHandler,
        ]);
    }

    public function mapAndDownloadFile(Request $request)
    {
        // check if we have a file in session
        $filePath = session(self::SESSION_KEY_UPLOADED_FILE_PATH);

        if (!$filePath) {
            return redirect()->route('site.index')
                ->withErrors(['file' => 'Nenhum arquivo enviado. Por favor, envie um arquivo primeiro.']);
        }

        // check if we have columns mapping in request
        $request->validate([
            'columns' => 'required|array',
        ]);
        $columnsMapping = $request->input('columns');

        try {
            $fileHandler = FileUploadAbstract::initByFilePath($filePath);
            $downloadFilePath = $fileHandler->generateMappedCsvFile($columnsMapping);

            // save to session for later download
            $request->session()->put(self::SESSION_KEY_PROCESSED_FILE_PATH, $downloadFilePath);

            // force download from storage path
            return response()->download($downloadFilePath, 'mapped_file.csv')
                ->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->withErrors(['columns' => $e->getMessage()])->withInput();
        }
    }
}