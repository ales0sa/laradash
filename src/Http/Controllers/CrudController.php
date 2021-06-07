<?php

namespace Ales0sa\Laradash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Ales0sa\Laradash\Models\Content;
use Ales0sa\Laradash\Models\ContentMeta;
//use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ales0sa\Laradash\Models\User;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Crypt;

class CrudController extends Controller
{
    private $model;
    private $tablename;
    private $table;
    private $inputs;
    private $subForm;

    public function __construct()
    {
        $this->middleware('auth');
        //$user = auth()->guard('web');
        // $user->assignRole('developer');
        // dd(auth()->user()->roles);
      // dd($user);

 
        $this->tablename = request()->route()->parameters()['tablename'];

        if($this->tablename) {
            $dirPath  = app_path('Dashboard');
            $filePath = $dirPath . '/' . $this->tablename . '.json';

            if (file_exists($filePath)) {
                $content      = json_decode(file_get_contents($filePath));
                $this->table  = $content->table;

                //dd($content->inputs);
                $this->inputs = $content->inputs;



            }
//            dd($temp);

            $className = str_replace(['_', '-', '.'], ' ', $this->tablename);
            $className = ucwords($className);
            $className = str_replace(' ', '', $className);
            //$className = Str::singular($className);
            $this->model = "\\App\\Models\\" . $className;

        }

    }


    public function getInput($id, $input, $content, $relations, $galleries, $subForm, $item) {

        $found = false;

        if ($input->type == 'select' && $input->valueoriginselector == 'table') {
            $found = true;

            if($this->table->softDeletes == 1){
                $relations[$input->tabledata] = DB::table($input->tabledata)->whereNull('deleted_at')->pluck($input->tabletextcolumn, $input->tablekeycolumn);
            }else{
                $relations[$input->tabledata] = DB::table($input->tabledata)->pluck($input->tabletextcolumn, $input->tablekeycolumn);
            }

            if ($item) {
                $content[$input->columnname] = $item->{$input->columnname};
            }
        }

        if ($input->type == 'subForm') {
           // dd($input);
            //dd("HERE");
            $found = true;
            $dirPath  = app_path('Dashboard');
            $filePath = $dirPath . '/' . $input->tabledata . '.json';
            //dd($filePath);

            $className = str_replace(['_', '-', '.'], ' ', $input->tabledata);
            $className = ucwords($className);
            $className = str_replace(' ', '', $className);
            $subModel = "\\App\\Models\\" . $className;
            //dd($subModel);
            //dd($subModel);
            if (file_exists($filePath)) {
                $subFormLayout      = json_decode(file_get_contents($filePath));
                $subForm[$input->columnname] = [
                    'table'  => $subFormLayout->table,
                    'inputs' => $subFormLayout->inputs
                ];

                foreach ($subFormLayout->inputs as $subInputKey => $subInput) {


                    
                    $results = $this->getInput(
                        false, // ID false
                        $subInput,
                        null,
                        $relations,
                        $galleries,
                        $subForm,
                        null
                    );


                    $relations = $results['relations'];
                    //$galleries = $results['galleries'];
                    $subForm   = $results['subForm'];
                }        

                if ($id) {
                    $subItems = $subModel::where($input->tablekeycolumn, $item->id);
                    if (Schema::hasColumn($subFormLayout->table->tablename, 'order_index')) {
                        $subItems = $subItems->orderBy('order_index', 'asc');
                    }
                    //dd($input->columnname);
                    //dd($input);
                    $subForm[$input->columnname]['content'] = $subItems->get();

                    foreach ($subForm[$input->columnname]['content'] as $itemKey => $item) {
                        foreach ($subFormLayout->inputs as $subkKy => $subInputs) {
                            if ($subInputs->type == 'multimedia_file') {
                                $file = Multimedia::find($item->{$subInputs->columnname . '_id'});
                                if ($file) {
                                    $subForm[$input->columnname]['content'][$itemKey]->{$subInputs->columnname} = [
                                        'url'  => asset(Storage::url($file->path)),
                                        'path' => $file->path,
                                        'id'   => $file->id,
                                        'type' => Storage::mimeType($file->path)
                                    ];
                                }                    
                            }
                        }
                    }
                }            
            }
            // $input->tablekeycolumn id para buscar
        }
        if (!$found && $item) {
            $content[$input->columnname] = $item->{$input->columnname};
        }
        return [
            'input'     => $input,
            'content'   => $content,
            'relations' => $relations,
            'galleries' => $galleries,
            'subForm'   => $subForm,
        ];
    }


    public function attachInput($item, $input, $data)
    {

        try {

            if ($input->type == 'file') {
                if(isset($data[$input->columnname])){

                    $path = $data[$input->columnname]->store($this->tablename, 'public');
                    // dd($path);
                    $item->{$input->columnname} = '/storage/'.$path;

                }else{
                    
                    if(!$item->{$input->columnname}){

                        $item->{$input->columnname} = null;
                    }

                }


                $item->save();
                return true;
            }

        } catch (\Throwable $th) {
            dd($th);
        }

        if ($input->type == 'subForm') {
            $dirPath  = app_path('Dashboard');
            $filePath = $dirPath . '/' . $input->tabledata . '.json';
            
            if (file_exists($filePath)) {
                $content   = json_decode(file_get_contents($filePath));
                $subTable  = $content->table;
                $subInputs = $content->inputs;
            }
            
            $className = str_replace(['_', '-', '.'], ' ', $input->tabledata);
            $className = ucwords($className);
            $className = str_replace(' ', '', $className);
            $subModel = "\\App\\Models\\" . $className;
            //dd($subModel);
            $subModel::where(''.$input->tablekeycolumn.'', $item->id)->delete();

            try {
                if (array_key_exists($input->columnname, $data)) {


                    //dd($data);
                  if($data[$input->columnname]){
                    foreach ($data[$input->columnname] as $subFormKey =>  $subFormItem) {

                        //if ( array_key_exists('id', $subFormItem) ) {
                         //  $subItem = $subModel::withTrashed()->find($subFormItem['id']);
/*
                        } else {*/
                            $subItem = new $subModel;
                       /* }

                        if (Schema::hasColumn($subTable->tablename, 'order_index')) {
                            $subItem->order_index = $subFormKey;
                        }*/

                        $item->save();

                        foreach ($subInputs as $subInputKey => $subInput) {

                            $subFormItem[$input->tablekeycolumn] = $item->id;

                            $this->attachInput($subItem, $subInput, $subFormItem);
                        }

                        $subItem->deleted_at = null;
                        $subItem->save();
                    }
                  }
                }
            } catch (\Throwable $th) {
                //echo $input->columnname;
                dd($th);

            }

            return true;
        }

        /*if ($input->type == 'password') {
            //dd('password');
            $item->{$input->columnname} = \Hash::make($data[$input->columnname]);
            $item->save();
            return true;
        }*/

        if ($input->type == 'boolean') {

            if($data[$input->columnname] == 'true'){
                $item->{$input->columnname} = 1;
            }else{
                $item->{$input->columnname} = 0;
            }
            $item->save();
            return true;
        }
        

        $item->{$input->columnname} = $data[$input->columnname];



        
    }

    public function sr($tablename, $id)
    {
        //return 'in dev';
        $item = $this->model::where('id', $id)->firstOrFail();

        return ['data' => $item];

    }


public function data($tablename, $id = false)
{

    $content   = null;
    $relations = [];
    $languages = [];
    $subForm   = [];
    $galleries = [];
    $item      = null;


    if ($id) {
        //dd($id);
        if($this->table->singlepage == 1){

            $item = $this->model::first();
            if(!$item){
                $item = new $this->model;
                $item->save();
            }

        }else{

            $item = $this->model::where('id', $id)->firstOrFail();

        }


        foreach ($this->inputs as $inputKey => $input) {

            $content[$input->columnname] = $item->{$input->columnname};



        }

    }



    foreach ($this->inputs as $inputKey => $input) {



        $results = $this->getInput(
            $id,
            $input,
            $content,
            $relations,
            $galleries,
            $subForm,
            $item
        );

        $input     = $results['input'];
        $content   = $results['content'];
        $relations = $results['relations'];
        $galleries = $results['galleries'];
        $subForm   = $results['subForm'];


    }


    $data = $this->model::get()->toArray();

    $newData = array();

    $textareas = array();
    $remove = array();
    
    foreach ($this->inputs as $inputKey => $input) {
        
        if ($input->type == 'subForm' ){
            $remove[] = $input->columnname;
        }

        if ($input->type == 'textarea' ){ //|| $input->type == 'text'
            $textareas[] = $input->columnname;
        }
        
        if ($input->type == 'select' && $input->valueoriginselector == 'table') {
            //dd($this->table);
            if($this->table->softDeletes == 1){
            $relations[$input->tabledata] = DB::table($input->tabledata)
                            ->whereNull('deleted_at')
                            ->pluck($input->tabletextcolumn, $input->tablekeycolumn);
            }    else {
                $relations[$input->tabledata] = DB::table($input->tabledata)
                            
                            ->pluck($input->tabletextcolumn, $input->tablekeycolumn);
            }
                        
        }

    }    
            
    foreach($data as $key => $val) {

        foreach($val as $k => $v){

            if (in_array($k, $textareas)) {

                $data[$key][$k] = Str::limit($v, 30);
            }



        }

    }

    
    return response()->json([
        'languages' => $languages,
        'locale'    => App::getLocale(),
        'tablename' => $this->tablename,
        'table'     => $this->table,
        'inputs'    => $this->inputs,
        'relations' => $relations,
        'content'   => $content,
        'subForm'   => $subForm,
        'data'      => $data
    ]);
}


    public function index($tablename)
    {
        $userId = auth()->user()->id;
        $user   = User::find($userId);



        $visible_columns = collect($this->inputs);

        $clean = $visible_columns->filter(function ($value, $key) {
            return $value->visible == true 
                && $value->type !== 'subForm'
                && $value->type !== 'VueLink';
        });


        $plucked = $clean->pluck('columnname');
        
        $plucked[] = 'id';

        if(isset($this->table->onlyForUser) && $this->table->onlyForUser){

            $data = $this->model::where('created_by', $userId)->get($plucked->all(), 'id')->toArray();

        }else{

            $data = $this->model::get($plucked->all(), 'id')->toArray();

        }

        if(isset($this->table->whoCan) && 
           !$user->hasAnyRole([$this->table->whoCan]
           //|| ( $user->root !== 1)
           )){
               
            if(!$user->root){
                return response()->json(['error' => 'Not authorized.'], 403);
            }
       
    
        }

        $content   = null;
        $relations = [];
        $languages = [];
        $subForm   = [];
        $galleries = [];
        $item      = null;

        $newData = array();

        $languages = [ 'es' => 'Español' ];
        $textareas = array();


        foreach ($this->inputs as $inputKey => $input) {
            
           
            if ($input->type == 'textarea' ){ //|| $input->type == 'text'
                $textareas[] = $input->columnname;
            }
            
            if ($input->type == 'select' && $input->valueoriginselector == 'table') {


                if($this->table->softDeletes == 1){

                    $relations[$input->tabledata] = DB::table($input->tabledata)
                    ->whereNull('deleted_at')
                    ->pluck($input->tabletextcolumn, $input->tablekeycolumn);


                }else{

                    $relations[$input->tabledata] = DB::table($input->tabledata)                    
                    ->pluck($input->tabletextcolumn, $input->tablekeycolumn);

                }




            }

        }    
                
        foreach($data as $key => $val) {
            //dd($val);
            foreach($val as $k => $v){
                if (in_array($k, $textareas)) {
                    //dd($k);
                    //array_push($newData[], 'ww');
                    $data[$key][$k] = Str::limit($v, 30);
                }

            }

        }
        


        foreach ($this->inputs as $inputKey => $input) {

            $results = $this->getInput(
                null,
                $input,
                $content,
                $relations,
                $galleries,
                $subForm,
                $item
            );

            $input     = $results['input'];
            $content   = $results['content'];
            $relations = $results['relations'];
            $subForm   = $results['subForm'];


        }

        
        return response()->json([
            'data'           => $data,
            'tablename'      => $this->tablename,
            'relations'      => $relations,
            'languages'      => $languages,
            'table'          => $this->table,
            'inputs'         => $this->inputs,
            'subForm'        => $subForm,
            'content'        => $content,
            '__admin_active' => 'admin.crud.' . $this->tablename
        ]);
    }

    public function store(Request $request, $tablename, $id = false)
    {

            $validHelper = array();

            if($id){
                $item       = $this->model::where('id', $id)->firstOrFail();
                $action     = 'edit';
            } else {
                $item       = new $this->model;
                $action     = 'create';
            }

            foreach ($this->inputs as $inputKey => $input) {

                if( !$input->nullable){

                
                
                if($input->type == 'number' || $input->type == 'money'){

                    $validHelper[$input->columnname] = 'numeric';

                }

                if(!$input->nullable && $action == 'create'){

                    $validHelper[$input->columnname] = 'required';
                
                }

            }

        }

        $validatedData = $request->validate($validHelper);

            foreach ($this->inputs as $inputKey => $input) {


                

                if($request[$input->columnname] !== 'null'){
                    
                    if ($input->type == 'VueComponent' || $input->type == 'VueLink') {
                    
        
                    }else{
    
                        $this->attachInput($item, $input, $request->all());

                    }
                    
                }
            
            }
            
            if(isset($this->table->onlyForUser) && $this->table->onlyForUser){

                $item->created_by = auth()->user()->id;

            }

            $item->save();

            return response()->json(['status' => 'success', 'message' => 'Se ' . $action . ' con éxito.',
            'content' => $item, 'inputs' => $this->inputs, 'action' => $action ]);



    }

    public function upload($tablename, $id, $column, Request $request)
    {
        $item = $this->model::find($id);
        
        $path = $request->{$column}->store($this->tablename, 'public');
        // dd($path);
        $item->{$column} = '/storage/'.$path;

        $item->save();

        return response()->json(['status' => 'success', 'message' => 'Upload correct.',
        'file' => $item->{$column} ]);
        

        //dd($request->all());
        /*$item->save();
            */
        
    }


    public function clean($tablename, $id, $column)
    {
        $item = $this->model::find($id);
        $item->{$column} = null;
        $item->save();
            return response()->json(['status' => 'success', 'message' => 'Se borro el archivo.',
            'content' => $item, 'inputs' => $this->inputs ]);
        
    }


    public function destroy($tablename, $id)
    {
        $item = $this->model::findOrFail($id);
        $item->delete();

        return response()->json(['status' => 'success', 'message' => 'Se  elimino con éxito.']);

    }

    public function trash($tablename)
    {
        $data = $this->model::onlyTrashed()->get();
        return view('Dashboard::admin.crud.index', [
            'trash'          => true,
            'data'           => $data,
            'tablename'      => $this->tablename,
            'table'          => $this->table,
            'inputs'         => $this->inputs,
            '__admin_active' => 'admin.crud.' . $this->tablename
        ]);
    }


    public function restore($tablename, $id)
    {
        $item = $this->model::withTrashed()->findOrFail($id);
        $item->deleted_at = null;
        $item->save();
        return redirect()->route('admin.crud.trash', ['tablename' => $tablename])->with('success', 'Se ha restaurado un <strong>item</strong> con éxito.');
    }


    public function copy($tablename, $id)
    {
        $new = $this->model::findOrFail($id)->replicate();
        $new->save();

        return response()->json(['status' => 'success', 'message' => 'Se  duplico con éxito.']);
    }


}
