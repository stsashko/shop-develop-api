<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AuthApi extends Controller
{
    public function response($user)
    {
        $token = $user->createToken(str()->random(40))->plainTextToken;

        return $this->response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'file'  =>  ($request->hasFile('file') ? 'required|mimes:jpg,jpeg,png,gif|max:2048' : ''),
        ]);

        // $validator->errors()

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 200);
        }

//        return response()->json([
//            'success' => false,
//            'errors' => $validator->messages()
//        ], 400);


        $insert = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(80),
            'remember_token' => Str::random(10),
        ];

        if($request->hasFile('file') && $request->file()) {
            $fileName = Str::random(15) . '.' . $request->file->extension();
            $request->file->move(public_path('avatars'), $fileName);
            $filePath = url('avatars/' . $fileName);
            $insert['avatar'] = $filePath;
        }

        $user = User::create($insert);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                    'success' => true,
                    'data' => new UserResource($user),
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]
            );
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 200);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json([
                    'success' => false,
                    'errors' => ['Authentication failed']
                ], 200);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                'success' => true,
                'data' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
    }

    public function logout()
    {
        Session::flush();
        Auth::user()->tokens()->delete();

        return response()
            ->json([
                'success' => true,
                'message' => 'You have successfully logged out and the token was successfully deleted'
            ], 200);
    }

    public function getUser()
    {

//        return response()->json([
//            Auth::user()
//        ]);



        return response()
            ->json([
                'success' => true,
                'data' => new UserResource(Auth::user())
            ], 200);
    }

}
