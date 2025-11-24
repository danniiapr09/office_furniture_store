<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /admin/users
    public function index(Request $request)
    {
        $query = User::query();

        if($request->has('q') && $request->q){
            $query->where('name','like','%'.$request->q.'%')
                  ->orWhere('email','like','%'.$request->q.'%');
        }

        $perPage = 10;
        $users = $query->orderBy('id','desc')->paginate($perPage);

        return response()->json($users);
    }

    // GET /admin/users/{id}
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    // PUT /admin/users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->only('name','email','phone');

        if($request->has('password') && $request->password){
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json($user);
    }

    // DELETE /admin/users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null,204);
    }
}