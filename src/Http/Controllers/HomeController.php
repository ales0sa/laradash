<?php

namespace AporteWeb\Dashboard\Http\Controllers;

use AporteWeb\Dashboard\Models\User;
use Illuminate\Http\Request;
use Auth;

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
        $groups      = $user->groups()->get();
        $permissions = $user->permissions()->get();
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
          
          if($content->table->menu_show && $content->table->singlepage == true){ 

            $menu[] = [ 'label' => $content->table->name->es, 'icon' => $content->table->icon, 'to' => '/crud/'.$content->table->tablename.'/1/edit'];

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