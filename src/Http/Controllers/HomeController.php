<?php

namespace AporteWeb\Dashboard\Http\Controllers;

use AporteWeb\Dashboard\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB;

class HomeController extends \AporteWeb\Dashboard\Http\Controllers\Controller
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




        foreach ($files as $fileKey => $file) {

          $content = json_decode(file_get_contents($file->getPathname()));
          
          if($content->table->menu_show && (isset($content->table->singlepage) && $content->table->singlepage == true)){ 

            $editornew = '/1/edit';
            $mm = DB::table($content->table->tablename)->first();

            if(!$mm){
                $editornew == '/create';
            }

            $menu[] = [ 'label' => $content->table->name->es, 'icon' => $content->table->icon, 'to' => '/crud/'.$content->table->tablename.$editornew];


          }elseif($content->table->menu_show){

            $menu[] = [ 'label' => $content->table->name->es, 'icon' => $content->table->icon, 'to' => '/crud/'.$content->table->tablename ];
          }

        }



        $menu[] = [ 'label' => 'Configuraciones', 'icon' => 'pi pi-cog', 


                      'items' => [ 

                                    ['label' => 'Sitio web', 'icon' => 'pi pi-globe', 'to' => '/company-data'],
                                    ['label' => 'Usuarios', 'icon' => 'pi pi-user', 'to' => '/users']

                                ]

                  ];


        return($menu);

    }

}