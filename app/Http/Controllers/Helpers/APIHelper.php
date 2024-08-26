<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APIHelper extends Controller
{
    const HTTP_CODE_SUCCESS             = 200;
    const HTTP_CODE_CREATED             = 201; // for successful creation requests
    const HTTP_NO_DATA_FOUND            = 204; // for no content, Delete, save and continue, etc
    const HTTP_CODE_BAD_REQUEST         = 400;
    const HTTP_CODE_UNAUTHORIZED        = 401; // lacks valid authentication credentials
    const HTTP_CODE_FORBIDDEN           = 403; // lacks permission for the request
    const HTTP_CODE_NOT_FOUND           = 404; // the requested resource could not be found
    const HTTP_CODE_CONFLICT            = 409; // the request could not be completed due to a conflict with the current state of the target resource
    const HTTP_CODE_SERVER_ERROR        = 500;


    /**
     * Summary of makeApiResponse
     * @param mixed $status
     * @param mixed $message
     * @param mixed $data
     * @param mixed $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function makeApiResponse($status = true, $message = 'Success', $data = null, $status_code = self::HTTP_CODE_SUCCESS): JsonResponse
    {
        // set the response
        $response = [
            'success' => $status,
            'status_code' => $status_code,
            'message' => $message,
        ];

        // add the data to the response if it exists or is an array
        if ($data !== null || is_array($data)) {
            $response['data'] = $data;
        }

        // make the api response
        return response()->json($response, $status_code);
    }

    /**
     * Summary of validateRequest
     * @param mixed $schema
     * @param mixed $request
     * @param string $type default is 'insert'
     * @return array
     */
    public static function validateRequest($schema, $request, $type = 'insert'): array
    {
        // Get schema keys into a array
        $schema_keys = array_keys($schema);

        // If the request is not and create, $request will take passed data
        $input = $request;

        // Only get full request object when creating
        // Ignore when doing the update
        if ($type == 'insert') {
            // Remove unnecessary fields from request
            $input = $request->only($schema_keys);
        }

        // Validate data feilds against schema
        $validator = Validator::make($input, $schema);

        // Return validation errors, if something went wrong
        if ($validator->fails()) {
            return ['errors' => true, 'error_messages' => $validator->errors()];
        }

        return ['errors' => false, 'data' => $input];
    }
}
