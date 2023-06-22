<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatchReciptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catch_recipts', function (Blueprint $table) {
            $table->id();
            $table -> integer('from_account');
            $table -> integer('to_account');
            $table -> string('client');
            $table -> decimal('amount');
            $table -> text('notes');
            $table -> date('date');
            $table -> string('docNumber');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catch_recipts');
    }
}
