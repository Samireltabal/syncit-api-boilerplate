<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Auth;
use App\User;

class RolesController extends Controller
{

    public function __construct() {
        $this->middleware('role:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $roles = Role::all();
        return response()->json($roles,200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        return response()->json($role, 201);
        return $request;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id || string $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::where('id',$id)->orWhere('name',$id)->first();
        if( $role ) {
            return response()->json($role, 200);
        } else { 
            $response = array(
                'message' => 'Resource Not Found',
                'code'    => '404'
            );
            return response()->json($response,404);
        }
        
    }

    public function grant(Request $request) {
        $current_user = Auth::user()->id;
        $validation = $request->validate([
            'role' => 'required|exists:roles,name',
            'user_id' => "required|integer|exists:users,id|not_in:$current_user"
        ]);
        $user = User::find($request->input('user_id'));
        $user->assignRole($request->input('role'));
        $user->save();
        return $user;
    }

    public function revoke(Request $request) {
        $current_user = Auth::user()->id;
        $validation = $request->validate([
            'role' => 'required|exists:roles,name',
            'user_id' => "required|integer|exists:users,id|not_in:$current_user"
        ]);
        $user = User::find($request->input('user_id'));
        $user->removeRole($request->input('role'));
        $user->save();
        return $user;
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
        $role = Role::where('id',$id)->orWhere('name',$id)->first();
        if( $role ) {
            return response()->json($role, 200);
        } else { 
            $response = array(
                'message' => 'Resource Not Found',
                'code'    => '404'
            );
            return response()->json($response,404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::where('id',$id)->orWhere('name',$id)->first();
        if($role) {
            if( $role->delete() ) {
                return response()->json('success', 200);
            } else {
                return response()->json('Failed', 503);
            }
        } else { 
            $response = array(
                'message' => 'Resource Not Found',
                'code'    => '404'
            );
            return response()->json($response,404);
        }
    }
}
