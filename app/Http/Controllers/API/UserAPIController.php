<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use App\Models\User;
use App\Traits\UploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserAPIController extends Controller
{
    use UploadTrait;

    /**
     * @return JsonResponse
     */
    public function uploadFile(Request $request)
    {
        try {
            if ($request->get('modelName') == config('constants.import_csv_log.models.cash_token_point')) {
                $validation = 'required|max:10240'; // 10 MB
            } else {
                $validation = 'required|mimes:csv,txt|max:10240'; // 10 MB
            }

            $request->validate([
                'file' => $validation,
            ]);

            if ($request->hasFile('file')) {
                $uploadFilePath = User::uploadOne($request->file('file'), $request->get('folderName'));
                if ($uploadFilePath != false) {
                    /* Insert data into the import_logs table */
                    ImportLog::create(
                        [
                            'file_name' => basename($uploadFilePath),
                            'file_path' => $uploadFilePath,
                            'model_name' => $request->get('modelName'),
                            'status' => config('constants.import_csv_log.status.key.pending'),
                            'import_flag' => config('constants.import_csv_log.import_flag.key.pending'),
                            'user_id' => $request->get('userId'),
                        ]
                    );

                    return response()->json(['success' => __('messages.import_history.messages.success')]);
                } else {
                    return response()->json(['error' => __('messages.something_went_wrong')], config('constants.validation_codes.unprocessable_entity'));
                }
            } else {
                return response()->json(['error' => __('messages.import_history.messages.validate_error')], config('constants.validation_codes.unprocessable_entity'));
            }
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);

            return response()->json(['error' => $th->getMessage()], config('constants.validation_codes.unassigned'));
        }
    }

    public function uploadEditorFile(Request $request)
    {
        try {
            $file = null;
            $fieldName = '';

            // Check which field contains the file
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $fieldName = 'upload_editor_image';
            } elseif ($request->hasFile('file')) {
                $file = $request->file('file');
                $fieldName = 'file_editor_image';
            } else {
                return response()->json([
                    'success' => false,
                    'uploaded' => false,
                    'error' => [
                        'message' => 'No file was uploaded',
                    ],
                ], 400);
            }

            // Process the file using UploadTrait
            $realPath = 'image_bucket/editor_image/';
            $result = $this->compressAndUploadToS3($file, $realPath, true);

            if (! isset($result['image'])) {
                return response()->json([
                    'success' => false,
                    'uploaded' => false,
                    'error' => [
                        'message' => 'Failed to upload image',
                    ],
                ], 500);
            }

            $imageUrl = Storage::url($result['image']);

            // Return the response in the format expected by TinyMCE
            return response()->json([
                'success' => true,
                'uploaded' => true,
                'url' => $imageUrl, // Add the 'url' field that TinyMCE expects
                'location' => $imageUrl,
            ]);
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);

            return response()->json([
                'success' => false,
                'uploaded' => false,
                'error' => [
                    'message' => $th->getMessage(),
                ],
            ], 500);
        }
    }
}
