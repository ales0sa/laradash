<?php print '<?php' ?>

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<?php if($this->table->softDeletes): ?>
use Illuminate\Database\Eloquent\SoftDeletes;
<?php endif ?>

class <?php print $className ?> extends Model
{
<?php if($this->table->softDeletes): ?>
    use SoftDeletes;
<?php endif ?>
	protected $table = '<?php print $this->table->tablename ?>';

    protected $fillable = [
<?php foreach ($this->inputs as $key => $input): ?>
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


    public function translated($field){
        $locale = \App::getLocale();
        switch ($locale) {
            case 'en':              
                return $this->{$field.'_en'};
                break;
            case 'pt':                
                return $this->{$field.'_pt'};
                break;
            default:
                return $this->{$field};
                break;
        }
    }

    
}