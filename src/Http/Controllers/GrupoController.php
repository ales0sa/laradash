<?php

namespace Ales0sa\Laradash\Http\Controllers;

use Illuminate\Http\Request;
use Ales0sa\Laradash\Requests\GroupCreateRequest;
use Ales0sa\Laradash\Requests\GroupEditRequest;
use App\Http\Controllers\Controller;
use Ales0sa\Laradash\Models\User;
//use Junges\ACL\Http\Models\Group;
//use Junges\ACL\Http\Models\Permission;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Group::get();
        return view('Dashboard::admin.grupos.index', [
            'data'           => $data,
            '__admin_active' => 'Dashboard::admin.grupo'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Dashboard::admin.grupos.create', [
            '__admin_active' => 'Dashboard::admin.grupo'
        ]);
    }

    public function store(Request $request)
    {
        //$item->name              = $request->name;
        //$item->slug              = Str::slug($request->name);
        //$item->description       = $request->description;
        //$item->display_only_root = $request->display_only_root;
        //$item->save();
        //return redirect()->route('admin.grupo')->with('success', 'Se añadio un <strong>Groupo</strong> con éxito.');
        $role = Role::create(['name' => $request->name]);

        return $role;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('Dashboard::admin.grupos.edit', [
            'element'        => Group::find($id),
            '__admin_active' => 'Dashboard::admin.grupo'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupEditRequest $request, $id)
    {
        $item = Group::find($id);
        $item->name              = $request->name;
        $item->slug              = Str::slug($request->name);
        $item->description       = $request->description;
        $item->display_only_root = $request->display_only_root;
        $item->save();
        return redirect()->route('admin.grupo')->with('success', 'Se ha editado un <strong>Groupo</strong> con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Group::find($id)->delete();
        return redirect()->route('admin.grupo')->with('success', 'Se ha eliminado un <strong>Groupo</strong> con éxito.');
    }
    public function trash()
    {
        $data = Group::onlyTrashed()->get();
        return view('Dashboard::admin.grupos.index', [
            'data'           => $data,
            'trash'          => true,
            '__admin_active' => 'Dashboard::admin.grupo'
        ]);
    }
    public function restore($id)
    {
        $item = Group::withTrashed()->find($id);
        $item->deleted_at = null;
        $item->save();
        return redirect()->route('Dashboard::admin.grupo.trash')->with('success', 'Se ha restaurado un <strong>Groupo</strong> con éxito.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permission($id)
    {
        return view('Dashboard::admin.grupos.permission', [
            'element'        => Group::with('permissions')->find($id),
            'permissions'    => Permission::get(),
            '__admin_active' => 'Dashboard::admin.grupo'
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
        $item = Group::find($id);
        $item->permissions()->sync($request->permissions);
        return redirect()->route('admin.grupo')->with('success', 'Se ha editado un <strong>Groupo</strong> con éxito.');
    }

}
