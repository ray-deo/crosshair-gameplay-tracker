<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('games', function (Blueprint $table) {
        $table->string('title')->nullable();
        $table->string('cover_url')->nullable();
    });
}

public function down()
{
    Schema::table('games', function (Blueprint $table) {
        $table->dropColumn(['title', 'cover_url']);
    });
}
};
