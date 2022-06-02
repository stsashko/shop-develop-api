<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAdminResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index($page = null, $limit = 30)
    {
        if(!$page)
            return response()->json([
                'success' => true,
                'data' => UserResource::collection(User::all())
            ], 200);
        else {
            $limit = !in_array($limit, [30, 50, 100]) ? 30 : $limit;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $users = User::select('id', 'name', 'email', 'avatar', 'role', 'created_at', 'updated_at');

            return response()->json([
                'success' => true,
                'data' => $users->paginate($limit)
            ], 200);
        }
    }

    public function show($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new UserAdminResource(User::findOrFail($id))
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response([
                'success' => false,
                'errors' => ['404 not found']
            ], 201);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'role' => 'required',
            'avatar'  =>  ($request->hasFile('avatar') ? 'nullable|mimes:jpg,jpeg,png,gif|max:2048' : ''),
            'password' => 'nullable|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        }
        else {
            $create = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'api_token' => Str::random(80),
                'remember_token' => Str::random(10),
            ];

            if ($request->input('password'))
                $create['password'] = Hash::make($request->input('password'));

            if($request->hasFile('avatar') && $request->file()) {
                $fileName = Str::random(15) . '.' . $request->image->extension();
                $request->image->move(public_path('avatars'), $fileName);
                $filePath = url('avatars/' . $fileName);
                $create['avatar'] = $filePath;
            }

            $id = User::insertGetId($create);

            return response()->json([
                'success' => true,
                'data' => new UserAdminResource(User::findOrFail((int)$id))
            ], 201);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . (int) $id,
            'role' => 'required',
            'avatar'  =>  ($request->hasFile('avatar') ? 'nullable|mimes:jpg,jpeg,png,gif|max:2048' : ''),
            'password' => 'nullable|min:6'
        ]);

        if (User::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 201);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->messages()
            ], 201);
        } else {

            $update = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'updated_at' => date("Y-m-d H:i:s"),
            ];

            if ($request->input('password'))
                $update['password'] = Hash::make($request->input('password'));

            if($request->hasFile('avatar') && $request->file()) {
                $fileName = Str::random(15) . '.' . $request->image->extension();
                $request->image->move(public_path('avatars'), $fileName);
                $filePath = url('avatars/' . $fileName);
                $update['avatar'] = $filePath;
            }

            $user = User::findOrFail((int)$id);
            $user->update($update);

            return response()->json([
                'success' => true,
                'data' => new UserAdminResource(User::findOrFail((int)$id))
            ], 201);
        }
    }

    public function destroy($id)
    {
        if (User::where('id', (int)$id)->count() <= 0) {
            return response()->json([
                'success' => false,
                'errors' => ['Id not found']
            ], 400);
        }

        $user = User::findOrFail((int) $id);
        $user->delete();

        return response()->json([
            'success' => true,
        ], 204);
    }

}
