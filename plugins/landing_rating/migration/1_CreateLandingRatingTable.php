<?php
/**
 * Migration: create landing_rating table
 */

use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateLandingRatingTable extends \SLiMS\Migration\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    function up()
    {
        Schema::create('landing_rating', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->autoIncrement('id');
            $table->string('visitor_name', 100)->notNull();
            $table->text('comment')->notNull();
            $table->tinynumber('rating')->notNull()->default('5');
            $table->tinynumber('is_hidden')->notNull()->default('0');
            $table->string('ip_address', 45)->nullable();
            $table->datetime('created_at')->notNull();
            $table->datetime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    function down()
    {
        Schema::drop('landing_rating');
    }
}
