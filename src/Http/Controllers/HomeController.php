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
        $files = \File::files($dirPath);
        
        $userId = auth()->user()->id;
        $user = User::find($userId);
        $submenu = [];


        foreach ($files as $fileKey => $file) {

            
            //dd($file->getPathname());
            
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
            if(isset($content->table->menu_parent) && $content->table->menu_show
            && (isset($content->table->whoCan) && $user->hasAnyRole([$content->table->whoCan]) || $user->root == 1 )
            ){
                $menu[$content->table->menu_parent]['items'][] = [
                            'label' => $content->table->name->es,
                            'icon' => $content->table->icon,
                            'to' => '/crud/'.$content->table->tablename,
                            //'items' => [ $menu[$content->table->tablename]]
                            ];

            }
        }

        

        if($user->hasAnyRole(['developer'])  || $user->root == 1 ){
            $menu['users'] = ['label' => 'Usuarios', 'icon' => 'pi pi-user', 'to' => '/users'];
            
        }
        
        $cmPath     = $dirPath . '/menu/custom.json';
        if (file_exists($cmPath)) {
            $customMenu      = json_decode(file_get_contents($cmPath));
            //$this->table  = $content->table;
            //dd($customMenu);
            $menu[] = $customMenu;

            //$this->inputs = $content->inputs;

        }

        return($menu);

    }

}