<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('dogs', function (Blueprint $table) {
            $table
                ->foreignId('user_id')
                ->after('img_path')
                ->unsigned()
                ->references('id')
                ->on('users');
        });
    }


    public function down()
    {
        Schema::table('dogs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
