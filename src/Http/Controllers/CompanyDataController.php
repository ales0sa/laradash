<?php

namespace Ales0sa\Laradash\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Ales0sa\Laradash\Models\ConfigVar;
use Str;
//use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CompanyDataController extends Controller
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



    public function data()
    {
        $languages = [];
        $content = null;
        $vars = ConfigVar::get()->pluck('config_value', 'config_key');
        $images = [
            'admin_logo',
            'admin_favicon',
            'footer_logo',
            'header_logo',
            'header_portrait'
        ];
        
        $content = [];
        foreach ($vars as $var_key => $var_value) {
            if (in_array($var_key, $images)) {
                if($var_value != null && $var_value != '') {
                    if (filter_var($var_value, FILTER_VALIDATE_URL)) {

                        $content[$var_key] = [
                            'url'  => $var_value,
                            'path' => $var_value,
                            'type' => ''
                        ];

                    } else {

                        $var_value = Str::replaceFirst('/storage/', '', $var_value);
                        /*if (!Storage::exists($var_value) && Str::startsWith($var_value, '/storage/')) {
                        }*/

                        //if (Storage::exists($var_value)) {

                            $content[$var_key] = [
                                'url'  => 'storage/'.$var_value,//asset(Storage::url($var_value)),
                                'path' => $var_value,
                                'type' => 'image'//Storage::mimeType($var_value)
                            ];
                       // }

                    }
                }
            } else {
                if ($var_value != null && $var_value != ''){
                    $content[$var_key] = $var_value;
                }
            }
        }
        //foreach (LaravelLocalization::getLocalesOrder() as $key => $value) {
            $languages['es'] = 'Español';//$value['name'];
        //}
        return response()->json([
            'languages' => $languages,
            'config' => [],
            'content' => $content,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $all = request()->all();
        $keys = [
            'admin_logo',
            'admin_favicon',
            'footer_logo',
            'header_portrait',
            'header_logo'
        ];
        foreach ($keys as $key) {
            if(array_key_exists($key, $all) && $all[$key] != null){
                $var = ConfigVar::firstOrNew(['config_key' => $key]);
                if(is_string($all[$key])) {
                    if ($all[$key] == '--remove--') {
                        $var->config_value = null;
                    }
                } else {
                    //$path = $all[$key]->store('general/', env('DEFAULT_STORAGE_DISK', 'local'));
                    $path = $all[$key]->store('config_vars', 'public');
                    //Storage::disk(env('DEFAULT_STORAGE_DISK', 'local'))->setVisibility($path, 'public');
                    $var->config_value = $path; //Storage::disk(env('DEFAULT_STORAGE_DISK', 'public'))->url($path);
                }
                $var->save();
            }
        }

        $keys = [
            'recaptcha_publickey',
            'recaptcha_privatekey',
            'thousands_separator',
            'decimal_separator',
            'header_networks',
            'header_info',
            'footer_networks',
            'footer_info',
            'admin_footer_text',
            'contact_target_send_email',
        ];
        foreach ($keys as $key) {
            if(array_key_exists($key, $all) && $all[$key] != null){
                $var = ConfigVar::firstOrNew(['config_key' => $key]);
                dump($all[$key]);
                $var->config_value = $all[$key];
                $var->save();
            }
        }
        return response()->json([
            'message' => 'guardo',
        ]);
    }
}
