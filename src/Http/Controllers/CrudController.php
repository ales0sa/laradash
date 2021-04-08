<?php

namespace AporteWeb\Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use AporteWeb\Dashboard\Models\Content;
use AporteWeb\Dashboard\Models\ContentMeta;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class CrudController extends Controller
{
    private $model;
    private $tablename;
    private $table;
    private $inputs;


    public function __construct()
    {
        $this->middleware('auth');

        $this->tablename = request()->route()->parameters()['tablename'];

        if($this->tablename) {
            $dirPath  = app_path('Dashboard');
            $filePath = $dirPath . '/' . $this->tablename . '.json';

            if (file_exists($filePath)) {
                $content      = json_decode(file_get_contents($filePath));
                $this->table  = $content->table;
                $this->inputs = $content->inputs;

            }
//            dd($temp);

            $className = str_replace(['_', '-', '.'], ' ', $this->tablename);
            $className = ucwords($className);
            $className = str_replace(' ', '', $className);
            $className = Str::singular($className);
            $this->model = "\\App\\Models\\" . $className;

        }

    }


    public function getInput($id, $input, $content, $relations, $galleries, $subForm, $item) {

        $found = false;

        if ($input->type == 'select' && $input->valueoriginselector == 'table') {
            $found = true;
            $relations[$input->tabledata] = DB::table($input->tabledata)->whereNull('deleted_at')->pluck($input->tabletextcolumn, $input->tablekeycolumn);
            if ($item) {
                $content[$input->columnname] = $item->{$input->columnname};
            }
        }
        if ($input->type == 'map-select-lat-lng') {
            $found = true;
            $content[$input->columnname . '_lat'] = $item->{$input->columnname . '_lat'};
            $content[$input->columnname . '_lng'] = $item->{$input->columnname . '_lng'};
        }

        if ($input->type == 'multimedia_file') {
            $found = true;
            $file = Multimedia::find($item->{$input->columnname . '_id'});
            if ($file) {
                $content[$input->columnname] = [
                    'url'  => asset(Storage::url($file->path)),
                    'path' => $file->path,
                    'id'   => $file->id,
                    'type' => Storage::mimeType($file->path)
                ];
            }
        }

        if ($input->type == 'gallery' && $item) {
            $found = true;
            $galleries[$input->columnname] = [];
            $gallery = Gallery::find($item->{$input->columnname});
            if ($gallery) {
                foreach ($gallery->items as $key => $item) {
                    $galleries[$input->columnname][] = [
                        'url'  => asset(Storage::url($item->path)),
                        'path' => $item->path,
                        'id'   => $item->id,
                        'type' => Storage::mimeType($item->path)
                    ];
                }
            }
        }
        if ($input->type == 'subForm') {

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
                    // $input     = $results['input'];
                    // $content   = $results['content'];
                    $relations = $results['relations'];
                    $galleries = $results['galleries'];
                    $subForm   = $results['subForm'];
                }        

                if ($id) {
                    $subItems = $subModel::where($input->tablekeycolumn, $item->id);
                    if (Schema::hasColumn($subFormLayout->table->tablename, 'order_index')) {
                        $subItems = $subItems->orderBy('order_index', 'asc');
                    }
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

        //dd($data);

        try {
            if ($input->type == 'card-header') {
                return true;
            }
        } catch (\Throwable $th) {
            dd($input);
        }

        try {

            if ($input->type == 'file') {




                if(isset($data[$input->columnname])){

                    $path = $data[$input->columnname]->store('public/content/' . $this->tablename . '/');
                    $item->{$input->columnname} = $path;

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


        if ($input->type == 'map-select-lat-lng') {
            $item->{$input->columnname . '_lat'} = $data[$input->columnname . '_lat'];
            $item->{$input->columnname . '_lng'} = $data[$input->columnname . '_lng'];
            $item->save();
            return true;
        }

        if ($input->type == 'multimedia_file') {
            try {
                if ($data[$input->columnname]->isValid()) {
                    $path = $data[$input->columnname]->store('public/content/' . $this->tablename . '/');
                    $multimedia = new Multimedia;
                    $multimedia->path          = $path;
                    $multimedia->order         = null;
                    $multimedia->filename      = null;
                    $multimedia->alt           = null;
                    $multimedia->caption       = null;
                    $multimedia->original_name = null;
                    $multimedia->disk          = null;
                    $multimedia->meta_value    = null;
                    $multimedia->save();
                    $item->{$input->columnname . '_id'} = $multimedia->id;
                    $item->save();
                }
            } catch (\Throwable $th) {
                if ( is_integer(intval($data[$input->columnname])) && intval($data[$input->columnname]) > 0) {
                    $item->{$input->columnname . '_id'} = intval($data[$input->columnname]);
                }
            }
            return true;
        }
        if ($input->type == 'gallery') {
            // Gallery
            // 
            if ($item->{$input->columnname}) {
                $gallery = Gallery::where('id', $item->{$input->columnname})->firstOrNew();
            } else {
                $gallery = new Gallery;
            }
            $gallery->description       = $this->tablename . ' gallery' . $item->id;
            $gallery->save();

            $item->{$input->columnname} = $gallery->id;
            $item->save();

            $ids = [];
            foreach ($data[$input->columnname] as $key => $value) {
                if(is_string($value)) {
                    $ids[$value] = [ 'order' => $key ];
                } else {
                    $path = $value->store('public/content/' . $this->tablename . '/');
                    $multimedia = new Multimedia;
                    $multimedia->path          = $path;
                    $multimedia->order         = null;
                    $multimedia->filename      = null;
                    $multimedia->alt           = null;
                    $multimedia->caption       = null;
                    $multimedia->original_name = null;
                    $multimedia->disk          = null;
                    $multimedia->meta_value    = null;
                    $multimedia->save();
                    $ids[$multimedia->id] = [ 'order' => $key ];
                }
            }
            $gallery->items()->sync($ids);
            return true;
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

            try {
                if (array_key_exists($input->columnname, $data)) {

                    $subModel::where(''.$input->tablekeycolumn.'', $item->id)->delete();

                    foreach ($data[$input->columnname] as $subFormKey =>  $subFormItem) {

                        if ( array_key_exists('id', $subFormItem) ) {
                           $subItem = $subModel::withTrashed()->find($subFormItem['id']);

                        } else {
                            $subItem = new $subModel;
                        }

                        if (Schema::hasColumn($subTable->tablename, 'order_index')) {
                            $subItem->order_index = $subFormKey;
                        }

                        $item->save();

                        foreach ($subInputs as $subInputKey => $subInput) {

                            $subFormItem[$input->tablekeycolumn] = $item->id;

                            $this->attachInput($subItem, $subInput, $subFormItem);
                        }

                        $subItem->deleted_at = null;
                        $subItem->save();
                    }
                }
            } catch (\Throwable $th) {
                dd($th);

            }

            return true;
        }
        if ($input->type == 'password') {
            $item->{$input->columnname} = bcrypt($data[$input->columnname]);
            $item->save();
            return true;
        }

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


    public function data($tablename, $id = false)
    {

        $content = null;
        $relations = [];
        $languages = [];

        foreach (LaravelLocalization::getLocalesOrder() as $key => $value) {
            $flag = $key;
            if($key == 'pt'){
                $flag = 'br';
            }
            if($key == 'en') {
                $flag = 'us';
            }

            $languages[] = [ 'key' => $value['name'], 'value' => $key, 'flag' => $flag];
        }

        if ($id) {

            if($this->table->singlepage == 1){

                $item = $this->model::where('id', $id)->firstOrCreate();

            }else{

                $item = $this->model::where('id', $id)->firstOrFail();

            }
            foreach ($this->inputs as $inputKey => $input) {
                $content[$input->columnname] = $item->{$input->columnname};
                if($input->translatable){

                    foreach (LaravelLocalization::getLocalesOrder() as $key => $value) {
                        if($key !== 'es'){
                            $content[$input->columnname.'_'.$key] = $item->{$input->columnname.'_'.$key};
                        }
                    }

                }

            }    
        }

        foreach ($this->inputs as $inputKey => $input) {
            
            if ($input->type == 'select' && $input->valueoriginselector == 'table' 
                ||
                $input->type == 'checkbox' && $input->valueoriginselector == 'table' 
                ) {
                $relations[$input->tabledata] = DB::table($input->tabledata)
                                ->whereNull('deleted_at')
                                ->pluck($input->tabletextcolumn, $input->tablekeycolumn);
            }

        }    

        return response()->json([
            'languages' => $languages,
            'locale'    => App::getLocale(),
            'tablename' => $this->tablename,
            'table'     => $this->table,
            'inputs'    => $this->inputs,
            'relations' => $relations,
            'content' => $content
        ]);
    }

    public function index($tablename)
    {
        $data = $this->model::get()->toArray();
        $relations = [];
        $languages = [];

        $newData = array();


        //dd($this->inputs['textarea']->type);
/*
        foreach ($data->toArray() as $key => $record) {    
            echo $key;
            echo "<hr>";
            $found_key = null;
            //dd($key);
            //$record['content'] = $this->getFirstParagraph($record['content']);
           //dd($this->inputs);
           // echo($key);
           // echo '-';
           // echo($this->inputs[$key]->type);
            foreach ($record as $wa => $value) {
                dd($wa);

                //dd($wa);
               // echo($this->inputs[$key]->type);
                # code...
               // echo $key;
               //dd($this->inputs[$key]);
               $found_key = array_search('textarea', array_column($this->inputs, 'type'));
               $index = $this->inputs[$found_key]->columnname;
               //dd($wa);
               if($wa == $index && $this->inputs[$found_key]->type == 'textarea'){
                   
                   echo $wa.' * '.$index. '  - '.$this->inputs[$found_key]->columnname;
                  // $newData[$key][$wa] = $value; //Str::limit($value, 50);
                    $newData[$key][$wa] = Str::limit($value, 30);
                   
                }else{

                    echo $wa;
                }
                
            }

        }
     die();
        //dd($newData);
        $data = $newData;*/

        foreach (LaravelLocalization::getLocalesOrder() as $key => $value) {

            $flag = 'es';
            if($key == 'pt'){
                $flag = 'br';
            }
            if($key == 'en') {
                $flag = 'us';
            }
            $languages[] = [ 'key' => $value['name'], 'value' => $key, 'flag' => $flag];
        }

        $textareas = array();
        
        foreach ($this->inputs as $inputKey => $input) {
            
           
            if ($input->type == 'textarea' || $input->type == 'text'){
                $textareas[] = $input->columnname;
            }
            
            if ($input->type == 'select' && $input->valueoriginselector == 'table') {
                $relations[$input->tabledata] = DB::table($input->tabledata)
                                ->whereNull('deleted_at')
                                ->pluck($input->tabletextcolumn, $input->tablekeycolumn);
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
        
        //dd($data);

        return response()->json([
            'data'           => $data,
            'tablename'      => $this->tablename,
            'relations'      => $relations,
            'languages'      => $languages,
            'table'          => $this->table,
            'inputs'         => $this->inputs,
            '__admin_active' => 'admin.crud.' . $this->tablename
        ]);
    }

    public function create()
    {
        return view('Dashboard::admin.crud.create', [
            'tablename'      => $this->tablename,
            'table'          => $this->table,
            'inputs'         => $this->inputs,
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

            if($input->type == 'number' || $input->type == 'money'){

                $validHelper[$input->columnname] = 'numeric';

            }

            if($input->nullable == 0 && $action == 'create'){

                    $validHelper[$input->columnname] = 'required';

               
            }



           
           
        }

        $validatedData = $request->validate($validHelper);

        //dd($validHelper);
        
            foreach ($this->inputs as $inputKey => $input) {

                if($request[$input->columnname] !== 'null'){

                    $this->attachInput($item, $input, $request->all());
                }
            
            }
            
            $item->save();

            return response()->json(['status' => 'success', 'message' => 'Se ' . $action . ' con éxito.',
            'content' => $item, 'inputs' => $this->inputs, 'action' => $action ]);



    }


    public function clean($tablename, $id, $column)
    {
        $item = $this->model::find($id);
        $item->{$column} = null;
        $item->save();
            return response()->json(['status' => 'success', 'message' => 'Se borro el archivo.',
            'content' => $item, 'inputs' => $this->inputs ]);
        
    }


    public function edit($tablename, $id)
    {
        $item = $this->model::find($id);
        return view('Dashboard::admin.crud.edit', [
            'item'           => $item,
            'tablename'      => $this->tablename,
            'table'          => $this->table,
            'inputs'         => $this->inputs,
            '__admin_active' => 'admin.crud.' . $this->tablename
        ]);
    }

    public function destroy($tablename, $id)
    {
        $item = $this->model::findOrFail($id);
        $item->delete();

        return response()->json(['status' => 'success', 'message' => 'Se  elimino con éxito.']);
       /* return redirect()->route('admin.crud', ['tablename' => $tablename, 'id' => $item->id])->with('status', 'Se elimino un <strong>item</strong> con éxito.');*/
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
        return redirect()->route('admin.crud.edit', ['tablename' => $tablename, 'id' => $new->id])->with('success', 'Se ha duplicado un <strong>item</strong> con éxito.');
    }
}
