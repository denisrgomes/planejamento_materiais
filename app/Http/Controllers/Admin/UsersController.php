<?php

namespace App\Http\Controllers\Admin;   

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Session;
//https://scotch.io/tutorials/user-authorization-in-laravel-54-with-spatie-laravel-permission
//https://github.com/caleboki/acl
class UsersController extends Controller
{

    public function __construct() 
    {
        $this->middleware(['auth', 'admin']);
    }
    
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$users = User::all();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();
        return view('admin.users.create', ['roles' => $roles]);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:120',
            'username' => 'required|unique:users',
            'password' => 'required|min:5|confirmed',
        ]);
        $request['password'] = bcrypt($request['password']);

        $user = User::create($request->only('username', 'name', 'password'));

        $roles = $request['roles'];

        if(isset($roles))
        {
            foreach ($roles as $role) {
                $role_r = Role::where('id', '=', $role)->firstOrFail();
                $user->assignRole($role_r);

            }
        }

        return redirect()->route('users.index')
            ->with('flash_message',
                'User successfully added.');
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
     {
        return redirect('users');
     }


     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::get();

        return view('admin.users.edit', compact('user', 'roles'));
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
        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|max:120',
            'username' => 'required|unique:users,username,' . $id,
            'password'=> 'required|min:5|confirmed'
        ]);

        $request['password'] = bcrypt($request['password']);
        $input = $request->only(['name', 'username', 'password']);
        $roles = $request['roles'];
        $user->fill($input)->save();
        

        if(isset($roles))
        {
            $user->roles()->sync($roles);
            
        }else
        {
            $user->roles()->detach();
        }

        return redirect()->route('users.index')
            ->with('flash_message', 'User successfully edited.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('flash_message', 'User successfully deleted.');
    }

}

