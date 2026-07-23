<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('category_name');
            $table->string('category_color')->default('#008040')->after('is_featured');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('product_cost');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_featured');
            $table->dropColumn('category_color');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};