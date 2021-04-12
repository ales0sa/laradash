<?php

namespace AporteWeb\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;

class EnvController extends Controller {
    
    /**
     * Calls the method 
     */
    public function something(){
        // some code
        $env_update = $this->changeEnv([
            'DB_DATABASE'   => 'new_db_name',
            'DB_USERNAME'   => 'new_db_user',
            'DB_HOST'       => 'new_db_host'
        ]);
        if($env_update){
            // Do something
        } else {
            // Do something else
        }
        // more code
    }
    
}