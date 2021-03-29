<?php

namespace AporteWeb\Dashboard\Http\Controllers;

use Illuminate\Http\Request;

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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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
          //dd($content);
          if($content->table->menu_show == 1 && $content->table->singlepage == true){ 

            $menu[] = [ 'label' => $content->table->name->es, 'icon' => $content->table->icon, 'to' => '/crud/'.$content->table->tablename.'/1/edit'];

          }else{
            $menu[] = [ 'label' => $content->table->name->es, 'icon' => $content->table->icon, 'to' => '/crud/'.$content->table->tablename ];
          }

        }

        return($menu);

    }

}