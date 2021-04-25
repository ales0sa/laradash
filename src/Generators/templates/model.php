<?php print '<?php' ?>

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<?php if($this->table->softDeletes): ?>
use Illuminate\Database\Eloquent\SoftDeletes;
<?php endif ?>
use Illuminate\Support\Str;
use Ales0sa\Laradash\Models\CrudBase;

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
    }
}