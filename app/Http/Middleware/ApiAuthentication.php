<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        $duration = $request->end - $request->start;
        $url = $request->fullUrl();
        $method = $request->getMethod();
        $ip = $request->getClientIp();
        
        $reqResp = [
            'Ip'=>$ip,
            'Url'=>$url,
            'Method'=>$method,
            //'Duration'=>$duration,
            //'Request' => $request->all(),
            'Request' => $request->all(),
            //'Response' => $response->original,
        ];
        Log::channel('request_log')->info(json_encode($reqResp));
        if(DB::table('delusers')->where('username',$request->get('username'))->where('usertoken',$request->get('token'))->where('status','Active')->exists()){
            $user = DB::table('delusers')->where('username',$request->get('username'))->where('usertoken',$request->get('token'))->where('status','Active')->first();
            $request->request->add(['defrole' => $user->role,'defusername' => $user->username]);
            return $next($request);
        }else{
            return response(json_encode(['status'=>'success','description'=>'notfound','resp'=>'Username/Password Incorrect!']));
            // return response(json_encode(['status'=>'success','description'=>'notfound','resp'=>json_encode($request->all())]));
        }
    }
}
