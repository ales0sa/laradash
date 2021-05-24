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
                $faker = \Faker\Factory::create();

                $vigiuser = new User;
                $vigiuser->name     = $model->username;
                $vigiuser->username = $model->username;
                $vigiuser->password = \Hash::make($model->password);
                $vigiuser->email    = $faker->unique()->email;
                $vigiuser->syncRoles($model->table);

                $vigiuser->save();


                
            } 

        });
<?php endif ?>

    }
}