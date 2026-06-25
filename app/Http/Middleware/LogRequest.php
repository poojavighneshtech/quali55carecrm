<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        //$response = $next($request);
        $duration = $request->end - $request->start;
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();
        
        $reqResp = [
            'Ip'=>$ip,
            'Url'=>$url,
            'Method'=>$method,
            //'Duration'=>$duration,
            'Request' => $request->all(),
            // 'Request' => json_decode($request->getContent()),
            //'Response' => $response->original,
        ];
        Log::channel('request_log')->info(json_encode($reqResp));
        return $next($request);
        
    }

   

    // public function terminate(Request $request, Response $response)
    // {
    //     $request->end = microtime(true);
    //     $this->log($request,$response);
    // }
    // protected function log(Request $request, Response $response)
    // {
    //     $duration = $request->end - $request->start;
    //     $url = $request->fullUrl();
    //     $method = $request->getMethod();
    //     $ip = $request->getClientIp();
    //     $reqResp = [
    //         'Ip'=>$ip,
    //         'Url'=>$url,
    //         'Method'=>$method,
    //         'Duration'=>$duration,
    //         'Request' => $request->all(),
    //         //'Response' => $response->original,
    //     ];
    //     Log::channel('request_log')->info(json_encode($reqResp));
    // }
}