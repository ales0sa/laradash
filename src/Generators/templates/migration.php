
<?php print '<?php' ?>

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class <?php print ucwords($this->table->tablename).date('Y_m_d_His') ?>Table extends Migration
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
                
                Schema::table('<?php print $this->table->tablename ?>', function (Blueprint $table)  {
                    
                    <?php 
                    
                    foreach ($this->inputs as $inputKey => $input) {
                        //print $input->columnname;
                        ?>
                        $table-><?php print $input->type ?>('<?php print $input->columnname ?>');
                        <?php
                    }
                    ?>
                });
            <?php } else { ?>
                Schema::create('<?php print $this->table->tablename ?>', function (Blueprint $table)  {
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
