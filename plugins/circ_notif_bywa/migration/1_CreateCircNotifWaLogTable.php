<?php
/**
 * Migration: create circ_notif_wa_log table
 */

use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateCircNotifWaLogTable extends \SLiMS\Migration\Migration
{
    /**
     * Run the migrations.
     */
    function up()
    {
        Schema::create('circ_notif_wa_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->autoIncrement('id');
            $table->string('member_id', 35)->notNull();
            $table->string('member_name', 255)->notNull();
            $table->string('member_type', 255)->notNull();
            $table->string('member_phone', 255)->notNull();
            $table->datetime('transaction_date')->notNull();
            $table->string('transaction_id', 32)->notNull();
            $table->text('message')->notNull();
            $table->string('notif_type', 32)->notNull()->default('circulation');
            $table->datetime('created_at')->notNull();
            $table->index('member_id');
            $table->index('member_name');
            $table->index('member_phone');
            $table->index('transaction_id');
            $table->index('transaction_date');
            $table->index('notif_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    function down()
    {
        Schema::drop('circ_notif_wa_log');
    }
}
