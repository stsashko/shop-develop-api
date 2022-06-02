<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . Auth::id(),
            'file'  =>  ($request->hasFile('file') ? 'nullable|mimes:jpg,jpeg,png,gif|max:2048' : ''),
            'password' => 'nullable|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        }
        else {
//            return response()->json(['id' => Auth::id()], 200);
            $user = User::findOrFail(Auth::id());

            if($request->hasFile('file') && $request->file()) {
                $fileName = Str::random(15) . '.' . $request->file->extension();
                $request->file->move(public_path('avatars'), $fileName);
                $filePath = url('avatars/' . $fileName);
                $user->avatar = $filePath;
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if($request->input('password'))
                $user->password = Hash::make($request->input('password'));

            $user->save();

//            $user->update($data);

            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
//                'path' => asset('storage/file.txt')
            ], 201);
        }


    }
}
