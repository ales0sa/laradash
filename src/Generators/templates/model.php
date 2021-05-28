<?php print '<?php' ?>

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<?php if($this->table->softDeletes): ?>
use Illuminate\Database\Eloquent\SoftDeletes;
<?php endif ?>
use Illuminate\Support\Str;
use Ales0sa\Laradash\Models\CrudBase;

<?php if(isset($this->table->generateUser) && $this->table->generateUser): ?>
use Ales0sa\Laradash\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
<?php endif ?>


class <?php print $className ?> extends Model
{
<?php if($this->table->softDeletes): ?>
    use SoftDeletes;
<?php endif ?>
    use CrudBase;

    protected $table = '<?php print $this->table->tablename ?>';

    protected $fillable = [
        'id',
<?php foreach ($this->inputs as $key => $input): ?>
<?php
if ($input->type == 'card-header') {
    continue;
}
?>
        '<?php print $input->columnname ?>',
<?php endforeach ?>

<?php
//        'kkkk'  => 'date:d-m-Y',
//        'xxx' => 'datetime:d-m-Y H:00',
?>
    ];
    protected $casts = [

    ];
    public static function boot() {
        parent::boot();
<?php if($this->table->uuid): ?>
        self::creating(function ($model) {
            $model->uuid = __uuid();
        });
<?php endif ?>

<?php if(isset($this->table->generateUser) && $this->table->generateUser): ?>
        self::saved(function ($model) {
            if(!User::where("username","=", $model->username)->exists())
            {
                

                $newUser = new User;
                $newUser->name     = $model->name;
                $newUser->username = $model->username;
                $newUser->password = \Hash::make($model->password);
                $newUser->email    = $model->username.'@laradash.com';

                $role = \DB::table('roles')->where('name', $model->table)->first();

                if(!$role){                
                    $newrole = Role::create(['name' => $model->table]);
                }
                    $newUser->syncRoles($model->table);

                $newUser->save();
                
            } 

        });
<?php endif ?>

    }
}