<?php

namespace Ales0sa\Laradash\Http\Controllers;

use Ales0sa\Laradash\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;

use Spatie\Permission\Models\Role;


class HomeController extends \Ales0sa\Laradash\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function whoami()
    {
        $user = User::find(Auth::user()->id);
        $groups      = [1];//$user->groups()->get();
        $permissions = [1];//$user->permissions()->get();
        return [ 'user' => $user, 'groups' => $groups, 'perms' => $permissions ];
    }
    

    public function ccache()
    {

        \Artisan::call('optimize');

        return [ 'status' => 'success', 'message' => 'Cache cleaned.' ];
        
    }
    

    public function symlinkgenerator()
    {

        \Artisan::call('storage:link');

        return [ 'status' => 'success', 'message' => 'Se creo el symlink.' ];
        
    }
    

    public function index()
    {
        return view('Dashboard::admin.home', [
            '__admin_active' => 'admin.home'
        ]);
    }
    
    public function home()
    {
        return redirect()->route('home');
    }


    public function menu()
    {
        $menu = array();
        $dirPath = __crudFolder();
        $files = \File::allFiles($dirPath);

        /*$all_users_with_all_their_roles = User::with('roles')->get();
        $all_users_with_all_direct_permissions = User::with('permissions')->get();
        $all_roles_in_database = Role::all()->pluck('name');
        $users_without_any_roles = User::doesntHave('roles')->get();
        $all_roles_except_a_and_b = Role::whereNotIn('name', ['role A', 'role B'])->get();*/
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $submenu = [];


        foreach ($files as $fileKey => $file) {
            $content = json_decode(file_get_contents($file->getPathname()));

            //$submenu[$fileKey] = array();

                if($content->table->menu_show && (isset($content->table->singlepage) && $content->table->singlepage == true)){ 

                    $editornew = '/1/edit';
                    $mm = DB::table($content->table->tablename)->first();

                    if(!$mm){
                        $editornew == '/create';
                    }

                    $menu[$content->table->tablename] = [ 'label' => $content->table->name->es, 'icon' => $content->table->icon, 'to' => '/crud/'.$content->table->tablename.$editornew];


                }elseif($content->table->menu_show){
                    if(isset($content->table->whoCan) && $user->hasAnyRole([$content->table->whoCan]) || $user->root == 1 ){


                       if(isset($content->table->menu_parent)){
                   //         unset($menu[1]);
                           // dd($menu);
                            /*$submenu[$content->table->menu_parent] = [ 
                                    'label' => $content->table->name->es,
                                    'icon' => $content->table->icon,
                                    'to' => '/crud/'.$content->table->tablename ];*/

                        }else{

                            $menu[$content->table->tablename] = [ 'label' => $content->table->name->es,
                                    'icon' => $content->table->icon,
                                    'to' => '/crud/'.$content->table->tablename,
                                    //'items' => [ $menu[$content->table->tablename]]
                            ];

                        }






                    }
                }

        }

        
        foreach ($files as $fileKey => $file) {
            $content = json_decode(file_get_contents($file->getPathname()));
            if(isset($content->table->menu_parent)){
                $menu[$content->table->menu_parent]['items'][] = [
                            'label' => $content->table->name->es,
                            'icon' => $content->table->icon,
                            'to' => '/crud/'.$content->table->tablename,
                            //'items' => [ $menu[$content->table->tablename]]
                            ];

            }
        }

    //    dd($submenu);
        /*

        foreach ($files as $fileKey => $file) {
            $content = json_decode(file_get_contents($file->getPathname()));
            if(isset($content->table->menu_parent)){
                //dd($content->table->menu_parent);
                $submenu[$fileKey] = [ 
                        'label' => $content->table->name->es,
                        'icon' => $content->table->icon,
                        'to' => '/crud/'.$content->table->tablename ];

            }
        }*/

        

        if($user->hasAnyRole(['developer'])  || $user->root == 1 ){
            $menu[] = ['label' => 'Usuarios', 'icon' => 'pi pi-user', 'to' => '/users'];
            
        }
        
        /*
        if($user->hasAnyRole(['root', 'webmaster', 'admin', 'blogger', 'developer']) || $user->root == 1){
            $menu[] = ['label' => 'Sitio web', 'icon' => 'pi pi-globe', 'to' => '/company-data'];


            $menu[] = [ 'label' => 'Configuraciones', 'icon' => 'pi pi-cog', 


            'items' => [ 

                          ['label' => 'Sitio web', 'icon' => 'pi pi-globe', 'to' => '/company-data'],
                          //['label' => 'Usuarios', 'icon' => 'pi pi-user', 'to' => '/users']

                      ]

        ];
    }
    
    */


        return($menu);

    }

}