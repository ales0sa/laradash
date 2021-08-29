<?php print '<?php' ?>

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Laradash<?php print ucwords($this->table->tablename) ?>Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

            <?php if (Schema::hasTable($this->table->tablename)) { ?>
                Schema::table('<?php print $this->table->tablename ?>', function (Blueprint $table) use ($this) {
                    $this->table($table, $content);
                });
            <?php } else { ?>
                Schema::create('<?php print $this->table->tablename ?>', function (Blueprint $table) use ($this) {
                    $this->table($table, $content);
                });
            <?php } ?>
        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
