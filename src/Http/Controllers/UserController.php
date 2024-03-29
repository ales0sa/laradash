<?php

namespace Ales0sa\Laradash\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ales0sa\Laradash\Models\User;
use App\Models\Sucursal;
// use Junges\ACL\Http\Models\Group;
// use Junges\ACL\Http\Models\Permission;
use Illuminate\Support\Str;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::get();
        return view('Dashboard::admin.users.index', [
            'data' => $data,
            '__admin_active' => 'Dashboard::admin.user'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups  = Group::get();
        if (class_exists('App\Models\Sucursal')) {
            $sucursales = Sucursal::with('tienda')->get();
        } else {
            $sucursales = [];
        }
        return view('Dashboard::admin.users.create', [
            'groups' => $groups,
            'sucursales' => $sucursales,
            '__admin_active' => 'Dashboard::admin.user'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        if($request->id){

            $validatedData = $request->validate([
                //'username' => 'required|unique:users',
                //'password' => 'required'
            
            ]);

            $item   = User::where('id', $request->id)->firstOrFail();
            $action = 'edito';
            /*if ($request->root != null && $request->root != '' && auth()->user()->root) {
                $item->root = $request->root;
            }*/

        } else {

            $validatedData = $request->validate([
                'username' => 'required|unique:users',
                'password' => 'required'
            
            ]);

            $item       = new User;
            $action     = 'añadio';
            //$item->uuid = __uuid();
        }
        //$item->root = 0;
        $item->name       = $request->name;
        $item->username   = $request->username;
        $item->email      = $request->username.'@'.url('/');//$request->email;
        $item->password   = bcrypt($request->password);
        //$item->root       = $request->root;
        //$item->sucursal_id  = $request->sucursal_id;
        $sr  = explode(',',$request->role);
        $item->syncRoles($sr);
        $item->save();

        return [ 'statu' => 'success', 'user' => $item ];
        //$item->groups()->sync($request->groups);
        //return redirect()->route('admin.user')->with('success', 'Se añadio un <strong>Usuario</strong> con éxito.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
    }

    public function users(){
        $users  = User::where('root', '!=', 1)->with('roles')->get();

        return ['data' => $users ];
    }

    public function groups(){
        //$groups  = Group::get();
        $groups = Role::get();
        return ['data' => $groups ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        // Verifico si es root
        if (auth()->user()->root) {
            // consulto Grupos
            $groups  = Group::get();
        } else {
            // consulto Grupos
            $groups  = Group::where('display_only_root', 0)->get();
        }

        $element = User::with('groups')->where('id', $uuid)->first();
        $user_groups = $element->groups()->pluck('id')->toArray();
        return view('Dashboard::admin.users.edit', [
            'groups' => $groups,
            'element' => $element,
            'user_groups' => $user_groups,
            '__admin_active' => 'Dashboard::admin.user'
        ]);
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
        $item = User::find($id);
        if ($request->password != null && $request->password != '') {
            $item->password = bcrypt($request->password);
        }
        $item->name       = $request->name;
        $item->username   = $request->username;
        $item->email      = $request->email;
        $item->root       = $request->root;
        //$item->sucursal_id  = intval($request->sucursal_id);
        $item->save();
        $item->groups()->sync($request->groups);
        return redirect()->route('admin.user')->with('success', 'Se ha editado un <strong>Usuario</strong> con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->forceDelete();
        //return redirect()->route('admin.user')->with('success', 'Se ha eliminado un <strong>Usuario</strong> con éxito.');
        return ['status' => 'success'];

    }
    public function trash()
    {
        $data = User::onlyTrashed()->get();
        return view('Dashboard::admin.users.index', [
            'data' => $data,
            'trash'=> true,
            '__admin_active' => 'Dashboard::admin.user'
        ]);
    }
    public function restore($id)
    {
        $item = User::withTrashed()->find($id);
        $item->deleted_at = null;
        $item->save();
        return redirect()->route('Dashboard::admin.user.trash')->with('success', 'Se ha restaurado un <strong>Usuario</strong> con éxito.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permission($id)
    {
        return view('Dashboard::admin.users.permission', [
            'element' => User::with('permissions')->find($id),
            'permissions' => Permission::get(),
            '__admin_active' => 'Dashboard::admin.user'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePermission(Request $request, $id)
    {
        $item = User::find($id);
        $item->permissions()->sync($request->permissions);
        return redirect()->route('Dashboard::admin.user')->with('success', 'Se ha editado un <strong>Usuario</strong> con éxito.');
    }

}
