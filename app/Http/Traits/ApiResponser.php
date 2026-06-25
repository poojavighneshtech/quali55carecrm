<?php

namespace App\Http\Traits;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
	/**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     * @param  string  $message
     * @param  int|null  $code
     * @return \Illuminate\Http\JsonResponse
     */

	// protected function success($data, string $message = null, int $code = 200)
	// {
	// 	return response()->json([
	// 		'status' => 'Success',
	// 		'message' => $message,
	// 		'data' => $data
	// 	], $code);
	// }
	protected function sendSuccess($result,$message)
	{
		$response = [
            // 'success' => true,
            // 'data'    => $result,
            // 'message' => $message,
            'status'=>0,
            'statusdescription'=>$result,
        ];
        //return response()->json($response, 200);
        //$cookie = cookie('key1 ', 'value 1', '1');
        //return response($response,200)->cookie($cookie); //with cookie
        
        return response($response,200);
        
	}
	

	/**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|string|null  $data
     * @return \Illuminate\Http\JsonResponse
     */
	protected function sendError($error, $errorMessages = [], $code = 404)
	{
		$response = [
            // 'success' => false,
            // 'message' => $error,
            'status'=>1,
            'statusdescription'=>$error,
        ];

        // if(!empty($errorMessages)){
        //     $response['data'] = $errorMessages;
        // }
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        //return response()->json($response, $code);
        
        return response($response,$code);
	}
	
}
