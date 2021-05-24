<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Schema\Column;

class GenerateCrudTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dirPath = __crudFolder();
        $files = File::files($dirPath);
        foreach ($files as $fileKey => $file) {
            $content = json_decode(file_get_contents($file->getPathname()));
            if (Schema::hasTable($content->table->tablename)) {
                Schema::table($content->table->tablename, function (Blueprint $table) use ($content) {
                    $this->table($table, $content);
                });
            } else {
                Schema::create($content->table->tablename, function (Blueprint $table) use ($content) {
                    $this->table($table, $content);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }

    public function table($table, $content)
    {


        // check if table is al dope
        $cols   = array();


        // added support double to change
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }

        if (!Schema::hasColumn($content->table->tablename, 'id')) {
            $table->bigIncrements('id');
        }



        foreach ($content->inputs as $inputKey => $input) {
            $change = false;
            $delete = false;


            $cols[]  = $input->columnname;


            if(isset($input->nomigrate)) {
               
            }else{

           /* if($input->type = 'VueComponent'){
                continue;
            }*/


            if (Schema::hasColumn($content->table->tablename, $input->columnname)) {
                $change = true;
            }

            if($input->type == 'text' || $input->type == 'password') {

/*

            if (!Schema::hasColumn($content->table->tablename, $input->columnname.'_en')) {
                $col = $table->string($input->columnname.'_en')->nullable();                

            }


            if (!Schema::hasColumn($content->table->tablename, $input->columnname.'_pt')) {
                $col = $table->string($input->columnname.'_pt')->nullable();                

            }*/
                if($input->translatable == 1){

                    //$col = $table->string($input->columnname.'_en')->nullable();   
                    /*foreach (LaravelLocalization::getLocalesOrder() as $key => $value) {
               
                        if (!Schema::hasColumn($content->table->tablename, $input->columnname.'_'.$key)) {                         
                            $col = $table->string($input->columnname.'_'.$key)->nullable();     
                        }
                    }*/

                }

                $col = $table->string($input->columnname);



            }
            if($input->type == 'file') {
                $col = $table->string($input->columnname);
            }
            if($input->type == 'textarea') {

           /* if (!Schema::hasColumn($content->table->tablename, $input->columnname.'_pt')) {
                $col = $table->longText($input->columnname.'_pt')->nullable();                

            }

            if (!Schema::hasColumn($content->table->tablename, $input->columnname.'_en')) {
                $col = $table->longText($input->columnname.'_en')->nullable();                

            }*/

                $col = $table->longText($input->columnname);
            }
            if($input->type == 'date') {
                $col = $table->datetime($input->columnname);
            }
            if($input->type == 'time') {
                $col = $table->time($input->columnname);
            }
            if($input->type == 'number' || $input->type == 'money') {
                $col = $table->double($input->columnname);
            }
            if($input->type == 'boolean') {
                $col = $table->boolean($input->columnname)->nullable()->default(0);
            }
            if($input->type == 'select' || $input->type == 'radio' ) {
                //dd($input);
                //echo $input->columnname;
                if(isset($input->multiple) && $input->multiple == 'true'){
                    $col = $table->text($input->columnname)->nullable();
                }else{
                    $col = $table->unsignedBigInteger($input->columnname);
                }
            }
            if($input->type == 'checkbox') {
                $col = $table->text($input->columnname)->nullable();
                //$col = $table->jsonb($input->columnname);
            }
            if($input->type == 'VueComponent') {
                $col = $table->text($input->columnname)->nullable();
                //$col = $table->jsonb($input->columnname);
            }
            
            if($input->type == 'bigInteger') {
                $col = $table->bigInteger($input->columnname);
            }

            if($input->nullable) {
                $col->nullable();
            }else {
                $col->nullable(false);
            }

            if($input->default) {
                $col->default($input->default);
            }

            if ($change) {
                $col->change();
            }

            }
        }


        $colsInTable = Schema::getColumnListing($content->table->tablename);

        foreach ($colsInTable as $key => $value) {


            if ($value !== 'id' 
                && $value !== 'deleted_at' 
                && $value !== 'created_at' 
                && $value !== 'updated_at' 
                && $value !== 'uuid' 
                && $value !== 'created_by' 
                && !in_array($value, $cols)) {

                $table->dropColumn($value);
            }

        }

        if ($content->table->uuid) {

            if(!Schema::hasColumn($content->table->tablename, 'uuid')){
                $table->uuid('uuid');
            }

        }else{

            if(Schema::hasColumn($content->table->tablename, 'uuid')){
                $table->dropColumn('uuid');
            }

        }

        if (isset($content->table->onlyForUser) && $content->table->onlyForUser) {

            if(!Schema::hasColumn($content->table->tablename, 'created_by')){
               
                $table->bigInteger('created_by')->nullable();

            }else{
            }

        }

        if ($content->table->softDeletes) {

            if(!Schema::hasColumn($content->table->tablename, 'deleted_at')){
                $table->softDeletes();
            }

        }else{

            if(Schema::hasColumn($content->table->tablename, 'deleted_at')){
                $table->dropSoftDeletes();
            }

        }

        if ($content->table->timestamps) {

            if(!Schema::hasColumn($content->table->tablename, 'created_at')){
                $table->timestamps();
            }

        }else{

            if(Schema::hasColumn($content->table->tablename, 'created_at') && Schema::hasColumn($content->table->tablename, 'updated_at')){
                $table->dropTimestamps();
            }

        }



}

}
