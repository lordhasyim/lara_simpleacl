<?php

namespace App\Http\Controllers;

use App\User;
use Auth;

// Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

//enable to output flash messaging
use Session;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function __construct()
    {
       /*
        * isAdmin middleware lets only users with a
        * specific permission permission to access these resources
        */
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        //return view('users.index', compact('users'));
        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Get All role and pass it tho the view
        $roles = Role::get();
        return view('users.create', ['roles' => $roles]);
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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        //Retrieving only the email and password data
        $user = User::create($request->only('email', 'name', 'password'));

        //Retrieving the roles field
        $roles = $request['roles'];

        //Checking if a role was selected
        if (isset($roles)) {
            foreach ($roles as $role) {
                $role_r = Role::where('id', '=', $role)->firstOrFail();
                //Assigning role to the user
                $user->assignRole($role_r);
            }
        }

        //Redirect to the users.index view and display message
        return redirect()->route('users.index')
                          ->with('flash_message', 'User Successfully added');

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
        //Get user with specified id
        $user = User::findOrFail($id);
        //Get All Role
        $roles = Role::get();
        //pass user and role to view
        return view('users.edit', compact('user', 'roles'));
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
            'name'=>'required|max:120',
            'email'=>'required|email|unique:users,email,'.$id,
            'password'=>'required|min:6|confirmed'
        ]);

        //Retrieve the name, email and password fields
        $input = $request->only(['name', 'email', 'password']);
        $roles = $request['roles']; //retrieve all role
        $user->fill($input)->save();
        if (isset($roles)) {
            //If one or more role is selected associate user to roles
            $user->roles()->sync($roles);
        }
        else {
            //If no role is selected remove existing role associated to a user
            $user->roles()->detach();
        }
        return redirect()->route('users.index')
            ->with('flash_message',
                'User successfully edited.');
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
            ->with('flash_message',
                'User successfully deleted.');
    }
}
