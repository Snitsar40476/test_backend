<?php

namespace App\Http\Controllers;

use App\Http\Services\FileService;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    protected $fileService;

    /**
     * @param FileService $fileService
     */
    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadFile(Request $request): \Illuminate\Http\JsonResponse
    {
        $result = $this->fileService->uploadFile($request);

        if (isset($result['error'])) {
            return response()->json($result, 400);
        }

        return response()->json($result, 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFiles(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 12);

        $files = File::paginate($perPage);
        $files->getCollection()->makeHidden('id');

        return response()->json($files);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setNameFile(Request $request): JsonResponse
    {
        if (!$request->has('file_key') || !$request->has('name')) {
            return response()->json(['error', 'Invalid data params'], 400);
        }
        $fileName = $request->get('name');
        $fileKey = $request->get('file_key');

        $this->fileService->changeNameFile($fileKey, $fileName);

        return response()->json(['success' => 'File name has been updated'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFilesSortingNameAsc(Request $request): JsonResponse
    {
        $result = $this->fileService->getFilesSortingNameAsc($request);

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFilesSortingNameDesc(Request $request): JsonResponse
    {
        $result = $this->fileService->getFilesSortingNameDesc($request);

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFilesSortingDateAsc(Request $request): JsonResponse
    {
        $result = $this->fileService->getFilesSortingDateAsc($request);

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFilesSortingDateDesc(Request $request): JsonResponse
    {
        $result = $this->fileService->getFilesSortingDateDesc($request);

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteFile(Request $request):JsonResponse
    {
        if (!$request->has('file_key')) {
            return response()->json(['error', 'Invalid data params'], 400);
        }
        $fileKey = $request->get('file_key');

        $result = $this->fileService->deleteFile($fileKey);

        return response()->json($result);
    }

  
    public function downloadFile(Request $request)
    {
        $path = $request->get('path');
        $file = storage_path("app/public/{$path}");
		
		return $path;
    }

    /**
     * @return JsonResponse
     */
    public function clearTableFiles():JsonResponse {
        File::query()->delete();

        return response()->json(['success' => 'The table has been cleared'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setAllFilesNames(Request $request): JsonResponse
    {
        if (!$request->has('name')) {
            return response()->json(['error', 'Invalid data params'], 400);
        }
        $name = $request->get('name');

        File::query()->update(['name' => $name]);

        return response()->json(['success' => 'The name has been changed in all entries'], 200);
    }
}
