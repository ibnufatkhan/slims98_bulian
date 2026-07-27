<?php
/**
 * Migration: create plugin_star_rating table
 */

use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateStarRatingTable extends \SLiMS\Migration\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    function up()
    {
        Schema::create('plugin_star_rating', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->autoIncrement('id');
            $table->string('reviewer_name', 100)->notNull();
            $table->text('comment')->notNull();
            $table->tinynumber('rating')->notNull();
            $table->tinynumber('is_hidden')->default(0)->notNull();
            $table->string('ip_address', 45)->nullable();
            $table->datetime('created_at')->notNull();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    function down()
    {
        Schema::drop('plugin_star_rating');
    }
}
