<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS master');

        if (!Schema::hasTable('master.communication_logs')) {
            Schema::create('master.communication_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('channel', 20)->default('whatsapp');
                $table->string('wauser', 100)->nullable();
                $table->string('waregisterno', 50)->nullable();
                $table->string('wapptophone', 20)->index();
                $table->string('jiotraceid', 100)->nullable()->index();
                $table->string('template_name', 100)->nullable();
                $table->text('wapptext')->nullable();
                $table->text('var1')->nullable();
                $table->text('var2')->nullable();
                $table->string('status', 50)->default('SUBMITTED');
                $table->string('ipaddr', 45)->nullable();
                $table->jsonb('response_payload')->nullable();
                $table->timestamps();
            });
        }

        DB::statement('CREATE OR REPLACE VIEW master.wapplogs AS SELECT * FROM master.communication_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS master.wapplogs');
        Schema::dropIfExists('master.communication_logs');
    }
};

