<?php
namespace Ales0sa\Laradash\Generators;

class Generator
{
    private $table;
    private $inputs;
    private $modelName;
    private $controllerName;

    public function __construct($table, $inputs)
    {
        $this->table  = $table;
        $this->inputs = $inputs;
    }
    public function model()
    {
        $path = realpath(__DIR__ . '/templates/model.php');

        $className = str_replace(['_', '-', '.'], ' ', $this->table->tablename);
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);
        $this->modelName = $className;

        ob_start();
        include($path);
        $body = ob_get_clean();
        $filePath = app_path('Models/' . $className . '.php');

        file_put_contents($filePath, $body);

        return true;
    }
    public function controller()
    {
        $path = realpath(__DIR__ . '/templates/controller.php');

        $className = str_replace(['_', '-', '.'], ' ', $this->table->tablename . ' Controller');
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);
        $this->controllerName = $className;

        ob_start();
        include($path);
        $body = ob_get_clean();
        $filePath = app_path('Http/Controllers/Admin/' . $className . '.php');

        file_put_contents($filePath, $body);

        return true;
    }
    public function crud()
    {
        $this->model();
        // $this->controller();
    }

    public function migrate()
    {
        $path = realpath(__DIR__ . '/templates/migration.php');

        $migName = str_replace(['_', '-', '.'], ' ', $this->table->tablename);
        $migName = str_replace(' ', '', $migName);

        ob_start();
        include($path);
        $body = ob_get_clean();
        $filePath = database_path('migrations/'.date('Y_m_d_His').'_'.$migName  . '_table.php');

        file_put_contents($filePath, $body);

        return true;


    }

}
