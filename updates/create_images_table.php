<?php namespace Kosmoskosmos\Helper\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateImagesTable extends Migration
{

    public function up()
    {
        Schema::create('dasrotequadrat_imageuploader', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('item')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dasrotequadrat_imageuploader');
    }

}
