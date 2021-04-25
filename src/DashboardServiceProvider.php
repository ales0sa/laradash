<?php

namespace Ales0sa\Laradash;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\ServiceProvider;

use Ales0sa\Laradash\Models\ConfigVar;
use Ales0sa\Laradash\Models\Content;
use Ales0sa\Laradash\View\Components\Messages;
use Ales0sa\Laradash\Dashboard;


use Illuminate\Support\Arr;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardServiceProvider extends ServiceProvider
{


    public function register()
    {

        $this->mergeConfigFrom(__DIR__.'/../config/dashboard.php', 'Dashboard');

    }


    public static function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
    }

    public static function updateDevPackageArray(array $packages)
    {

        return [
            "axios" => "^0.21.0",        
            "cross-env" => "^7.0",
            "laravel-mix" => "^6.0.6",
            "lodash" => "^4.17.19",
            "resolve-url-loader" => "^3.1.2",
            "postcss" => "^8.1",
            "sass" => "^1.20.1",
            "sass-loader" => "^8.0.2",
            "vue" => "^2.5.17",
            "vue-template-compiler" => "^2.6.10",
            "vue-style-loader" => "^4.1.3"
        ];


    }

    public static function updatePackageArray(array $packages)
    {

        return [
           
            "mitt" => "^2.1.0",
            "moment" => "^2.29.1",
            "primeflex" => "^2.0.0",
            "primeicons" => "^4.1.0",
            "primevue" => "^2.4.0",
            "quill" => "^1.3.7",
            "vue-router" => "^3.5.1",
            "vuelidate" => "^0.7.6",

        ];
    }

    public function boot()
    {

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Dashboard');
        $this->configurePublishing();
        $this->configureRoutes();


        Artisan::command('dashboard:user', function () {

        //$role = Role::create(['name' => 'root']);
        //$permission = Permission::create(['name' => 'root']);
        //$role->givePermissionTo($permission);
        //$permission->assignRole($role);
       // $user = ales0sa\laradash\src\Models\User::where('username', 'ales0sa')->delete();
        $user = \Ales0sa\Laradash\Models\User::where('username', 'root')->first();
        
        if(!$user){
            
        //
        

        $user = DB::table('users')->insert([
           // 'uuid'     => __uuid(),
            'name'     => 'Root User',
            'username' => 'root',
            'email'    => 'root@localhost',
            'password' => bcrypt('root'),
            'root'     => 1,
        ]);

        }

        $user->assignRole(['root']);

        $this->info("\nSe creo el usuario!");

        });

        Artisan::command('dashboard:init', function () {

            $bar = $this->output->createProgressBar(5);
            $bar->start();

            if ($this->confirm('Config .env DB Access?')) {

            // php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
            //$dbhost = $this->ask('Database Host? (localhost)');
            $dbname = $this->ask('Database Name?');
            $dbuser = $this->ask('Database User?');
            $dbpass = $this->secret('Database Password?');

            $env_update = DashboardServiceProvider::changeEnv([
                'DB_DATABASE'   => $dbname ? $dbname : 'laravel',
                'DB_USERNAME'   => $dbuser ? $dbuser : 'root',
                'DB_PASSWORD'   => $dbpass ? $dbpass : 'password',
                'DB_HOST'       => '127.0.0.1' //'localhost'
            ]);

            if($env_update){
                $this->info("Modify .env succesfully.");
                Artisan::call('key:generate');
            } else {
                // Do something else
            }

            }
            /*
            try {

            } catch (\Throwable $th) {
                $this->info($th);
            }
            */


            // more code
            


            $bar->advance();
            Artisan::call('storage:link');


            $bar->advance();

            /*Artisan::call('migrate:refresh', [
                '--seed' => false
            ]);*/

            $bar->advance();


            /*
            DB::table('users')->insert([
                'name'     => 'Ale Sosa',
                'username' => 'admin',
                'email'    => 'admin@donsistema.com',
                'password' => bcrypt('admin'),
                'root'     => 1,
            ]);*/

            $bar->advance();
            
            if (! file_exists(base_path('package.json'))) {
                return;
            }
    
            $configurationKey = 'dependencies'; //$dev ? 'devDependencies' : 'dependencies';
    
            $packages = json_decode(file_get_contents(base_path('package.json')), true);
    
            $packages[$configurationKey] = DashboardServiceProvider::updatePackageArray(
                array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
                $configurationKey
            );

            $configurationKey = 'devDependencies';
            $packages[$configurationKey] = DashboardServiceProvider::updateDevPackageArray(
                array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
                $configurationKey
            );
    

            ksort($packages[$configurationKey]);
    
            file_put_contents(
                base_path('package.json'),
                json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
            );

            copy(__DIR__.'/vue-stubs/webpack.mix.js', base_path('webpack.mix.js'));
            copy(__DIR__.'/vue-stubs/dashboard.js', resource_path('js/dashboard.js'));
            //copy(__DIR__.'/vue-stubs/dashboard.js', public_path('js/dashboard.js'));

            $bar->advance();


            Artisan::call('optimize');
            

            $bar->finish();
            $this->info("\nSe ejecutaron las migraciones, seeders y se creo el usuario admin!");
        });
        Artisan::command('dashboard:permissions', function () {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('permissions')->truncate();
            DB::table('permissions')->insert(config('permissions'));
            DB::statement('SET FOREIGN_KEY_CHECKS=1');    
            $this->info("\nSe actualizo el listado de permisos!");
        });

        if (env('FORCE_HTTPS') == true) {
            \URL::forceScheme('https');
        }
        Collection::macro('slug', function () {
            return $this->map(function ($value) {
                return Str::slug($value);
            });
        });
        if (config('app.debug')){
            $assets_version = hash('md5', rand());
        } else {
            $assets_version = '11';
            $path_file = base_path('.git/refs/heads/master');
            if (file_exists($path_file)) {
                // $assets_version = trim(exec('git log --pretty="%h" -n1 HEAD'));
                $assets_version = trim(substr(file_get_contents($path_file), 4));
            }
        }

        if (php_sapi_name() != 'cli') {
            view()->share([
                'assets_version' => $assets_version,
                'query_search'  => ''
            ]);

            view()->composer('*', function ($view) {
                $view->with('__admin_menu', 'admin.menu');
                $view->with('admin_default_image', asset('images/no-image.png'));
            });
        }
        /*
        example
        @exception
            code ......
        @catch
            {!! $e->getMessage() !!}
        @endexception
        */
        // Temporal hasta que se adapten los component a tags a Laravel 7
        /*if (version_compare(App::VERSION(), '7.0.0') >= 0) {
            Blade::withoutComponentTags();
        }*/
        /*config([
            'log-viewer.theme'         => 'bootstrap-4',
            'log-viewer.route.enabled' => false
        ]);*/
        Blade::component('dashboard-messages', Messages::class);
        Blade::directive('exception', function () {
            return '<?php try { ?>';
        });

        Blade::directive('catch', function () {
            return '<?php } catch (\Exception $e) { ?>';
            // use {!! $e->getMessage() !!}
        });

        Blade::directive('endexception', function () {
            return '<?php } ?>';
        });
        // Load Routes
        /*if(file_exists(__DIR__.'/routes/web.php')) {
            Route::middleware('web')
                ->namespace('\Ales0sa\Laradash\Controllers')
                ->group(__DIR__.'/routes/web.php');
        }*/
        /*if(file_exists(__DIR__.'/routes/breadcrumbs.php')) {
            include __DIR__.'/routes/breadcrumbs.php';
        }*/
        // Load the views
       /* if(is_dir(__DIR__.'/resources/views')) {
            $this->loadViewsFrom(__DIR__.'/resources/views', 'Dashboard');
        }*/
        // Load Translations
        if(is_dir(__DIR__.'/resources/lang')) {
            $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'Dashboard');
            // $this->publishes([
            //     __DIR__.'/lang' => resource_path('lang/vendor/Ales0sa/dashboard'),
            // ]);
        }
        // Load Migrations
        if(is_dir(__DIR__.'/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }
        global $__seeders;
        $seed = [];
        if (is_array($__seeders)) {
            array_merge($__seeders, $seed);
        } else {
            $__seeders = $seed;
        }
        /*
        if(file_exists(__DIR__.'/config/dynamic-content.php')) {
            config(['dynamic-content' => include __DIR__.'/config/dynamic-content.php']);
        }
        if(file_exists(__DIR__.'/config/seo.php')) {
            config(['seo' => include __DIR__.'/config/seo.php']);
        }
        */
        /*
        resolver este bug
        if(file_exists(__DIR__.'/config/permissions.php')) {
            config(['permissions' => include __DIR__.'/config/permissions.php']);
        }
        */
    }


    protected function configurePublishing()
    {


        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/dashboard.php' => config_path('dashboard.php'),
        ], 'dashboard');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/dashboard'),
        ], 'dashboard');


    }

    protected function configureRoutes()
    {


        if (Dashboard::$registersRoutes) {
            Route::middleware('web')
                ->namespace('\Ales0sa\Laradash\Http\Controllers')
                ->group(__DIR__.'/../routes/web.php');
        }

    }


}
