<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedBack;
use stdClass;

class FeedBackController extends Controller
{
    function send(Request $request){
        Log::info($request->all());
        $user= new stdClass();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        Mail::to("info@dadisonestop.com")->send(new FeedBack($request->message,$user));
        return response("Sucess");
    }
}
