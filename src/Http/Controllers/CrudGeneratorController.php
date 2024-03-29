<?php

namespace Ales0sa\Laradash\Http\Controllers;

use App\Http\Controllers\Controller;


//use Ales0sa\Laradash\Requests\GroupCreateRequest;
//use Ales0sa\Laradash\Requests\GroupEditRequest;

use Ales0sa\Laradash\Models\User;

use Ales0sa\Laradash\Generators\Generator;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

use Symfony\Component\Console\Output\StreamOutput;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class CrudGeneratorController extends Controller
{

    protected $user;

    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            //dd($this->user);
            if($this->user->root == 1){

                return $next($request);
            }else{
                return response()->json(['error' => 'Not authorized.'], 403);
            }

        });

    }

    public function index()
    {
       
       $userId = auth()->user()->id;       
       $user   = User::find($userId);

       if($user->root == 1){ //$user->hasAnyRoles('root')){

            $dirPath = __crudFolder();
            $data = File::allFiles($dirPath);            
            $jsonfiles = array();
            
            foreach($data as $f){
                $jsonfiles[] = $f->getfilename();
            }

            return $jsonfiles;        
        
        }else{
        
            return response()->json(['error' => 'Not authorized.'], 403);

        }

    }

    public function dbtables()
    {
        $dirPath = __crudFolder();
        $data = File::allFiles($dirPath);

        // Remove Laravel Tables from Array.
        $files = array();
        $files[] = 'users';
        $files[] = 'migrations';
        $files[] = 'password_resets';
        $files[] = 'model_has_permissions';
        $files[] = 'model_has_roles';
        $files[] = 'permissions';
        $files[] = 'role_has_permissions';
        $files[] = 'roles';
        $files[] = 'seo';
        $files[] = 'translations';
        $files[] = 'failed_jobs';

        
        // Remove Laradash Tables from Array.
        $files[] = 'config_vars';


        foreach ($data as $key => $value) {

            $filename = $value->getFilename();
            $noext = str_replace('.json','',$filename);
            $files[] = $noext;
        }
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        $difftables = array_diff($tables, $files);

        return ['status' => 'success', 'tables' => $difftables, 'jsonfiles' => $files, 'alltables' => $tables];
    }

    public function dbgetcols($tablename)
    {
        $data = [];
        $sm = \Schema::getConnection()->getDoctrineSchemaManager();
        $table = $sm->listTableDetails($tablename);

        foreach ($table->getColumns() as $column) {

            $data[$column->getName()]  = [ 'type' => $column->getType()->getName(), 'props' => $column->toArray()];

        }   

        return ['status' => 'success', 'columns' => $data];
    }

    public function jsonfromdb(Request $request){

        $dirPath = __crudFolder();

        $data     = json_decode($request->data);
        $filePath = $dirPath . '/' . $data->table->tablename . '.json';


        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        return ['status' => 'success', 'message' => 'Succefully generated CRUD from DataBase'];

    }

    public function data($table = false)
    {
        $languages = [ 'es' => 'Español' ];
        $groups  = Role::get();

        $content = null;
        if($table) {
            $dirPath  = app_path('Dashboard');
            $filePath = $dirPath . '/' . $table;// . '.json';
            if (file_exists($filePath)) {
                $content = json_decode(file_get_contents($filePath));
            }
        }
        //foreach (LaravelLocalization::getLocalesOrder() as $key => $value) {
        //    $languages[$key] = $value['name'];
        //}
        return response()->json([
            'content'   => $content,
            'groups'    => $groups,
            'languages' => $languages,
        ]);
    }

    public function create()
    {
        return view('Dashboard::admin.crud-generator.create', [
            '__admin_active' => 'Dashboard::admin.crud-generator'
        ]);
    }

    public function store(Request $request)
    {

        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
          }
          
         $dirPath = __crudFolder();

        $data     = json_decode($request->data);
       // dd($data);
        $filePath = $dirPath . '/' . $data->table->tablename . '.json';


        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

       /* DB::table('permissions')->upsert([
            [
                'name'        => 'Crear ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-create',
                'description' => ''
            ],
            [
                'name'        => 'Editar ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-edit',
                'description' => ''
            ],
            [
                'name'        => 'Eliminar ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-delete',
                'description' => ''
            ],
            [
                'name'        => 'Restaurar ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-restore',
                'description' => ''
            ]
        ], ['slug'], ['name', 'description']);*/

       // dd($data->inputs);
       
       

       // Check if model exists.
       $className = str_replace(['_', '-', '.'], ' ',  $data->table->tablename);
       $className = ucwords($className);
       $filePath = app_path('Models/' . $className . '.php');
       $isExists = File::exists($filePath);

        if(!$isExists){
           (new Generator($data->table, $data->inputs))->crud();
        }else{
            
        }

        $stream = fopen("php://output", "w");
        
        if($data->migrate == 0){


            return response()->json([ 'status' => 'success', 'message' => 'Se guardo sin migrar.']);// 

        }else{
            
           // $migfile = (new Generator($data->table, $data->inputs))->migrate();


            //Artisan::call('migrate');
            // while (true) {
            //     if (database_path($migfile)) {
                 /* Artisan::call('migrate:refresh', [
                      '--path' => 'database/'.$migfile,
                      '--force' => true                
                  ]);*/
             //     break;
            //     }
            // }


            //$make

            Artisan::call('migrate:refresh', [
                '--path' => 'vendor/ales0sa/laradash/src/migrations/2020_11_23_000001_generate_crud_tables.php',
                '--force' => true                
            ]);
            $output = Artisan::output();
            //dd($output);

            
            //Artisan::call('migrate');
            return response()->json([ 'status' => 'success', 'message' => 'JSON & DB Updated. ', 'output' => $output ]);// 

        }


        

        //return Artisan::call('migrate:refresh --path=vendor/Ales0sa/dashboard/src/migrations/2020_11_23_000001_generate_crud_tables.php');
         

         // ]);// redirect()->route('admin.crud-generator')->with('success', 'Se añadio un <strong>Groupo</strong> con éxito.');
        /*
        $dirPath = __crudFolder();

        $data     = json_decode($request->data);
        $filePath = $dirPath . '/' . $data->table->tablename . '.json';
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        DB::table('permissions')->upsert([
            [
                'name'        => 'Crear ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-create',
                'description' => ''
            ],
            [
                'name'        => 'Editar ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-edit',
                'description' => ''
            ],
            [
                'name'        => 'Eliminar ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-delete',
                'description' => ''
            ],
            [
                'name'        => 'Restaurar ' . $data->table->name->{App::getLocale()},
                'slug'        => $data->table->tablename . '-restore',
                'description' => ''
            ]
        ], ['slug'], ['name', 'description']);

        (new Generator($data->table, $data->inputs))->crud();
        return Artisan::call('migrate:refresh --path=vendor/Ales0sa/dashboard/src/migrations/2020_11_23_000001_generate_crud_tables.php');
        return 1;
        return redirect()->route('Dashboard::admin.crud-generator')->with('success', 'Se añadio un <strong>Groupo</strong> con éxito.');*/
    }

    public function destroy($id)
    {
        $dirPath = __crudFolder();
        $filePath = $dirPath . '/' . $id;

        $tablename = str_replace('.json','',$id);

        \Schema::dropIfExists($tablename);

        \File::delete($filePath);

       // Check if model exists.
       $className = str_replace(['_', '-', '.'], ' ',  $tablename);
       $className = ucwords($className);
       $modelPath = app_path('Models/' . $className . '.php');
       $isExists = File::exists($modelPath);

       if($isExists){
            \File::delete($modelPath);
       }

       return response()->json([
            'status'   => 'success',
            'data'     => $this::index()
        ]);
        

    }
    public function trash()
    {
        $data = Group::onlyTrashed()->get();
        return $data;
    }
    public function restore($id)
    {
        $item = Group::withTrashed()->find($id);
        $item->deleted_at = null;
        $item->save();
        return $item;
    }
}
