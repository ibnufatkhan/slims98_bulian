<?php
/**
 * Made by lovely form Ibnu Fatkhan ibnufatkhan@gmail.com
 *
 * Migration: create plugin_web_visitor_stats table for all-time OPAC web visitors.
 */

use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateWebVisitorStatsTable extends \SLiMS\Migration\Migration
{
    function up()
    {
        Schema::create('plugin_web_visitor_stats', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->number('id')->notNull()->default('1');
            $table->bigNumber('total')->notNull()->default('0');
            $table->datetime('updated_at')->nullable();
            $table->primary('id');
        });
    }

    function down()
    {
        Schema::drop('plugin_web_visitor_stats');
    }
}
