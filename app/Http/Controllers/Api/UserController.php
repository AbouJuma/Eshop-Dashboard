<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\UserResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    //notifications
    public function notifications()
    {
        $notifications = Notification::where('user_id', auth()->user()->id)
            ->latest()
            ->select('title', 'content', 'action_time', 'created_at')
            ->paginate(500);
        
        $response = [
            'status' => 'success',
            'message' => 'RETRIEVE_SUCCESS',
            'status_code' => 200,
            'data' => $notifications
        ];
        
        return response()->json($response, 200);
    }

    function delete(Request $request) {
        $user = User::where('phone',$request->phone)->first();
        if($user){
            $user->phone = "deleted-".$user->phone;
            return response("Sucess");
        }else{
            return response("Failed");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
