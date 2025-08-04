<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->string('invoice_type')->default('Sale Invoice');
            $table->date('date_of_supply');
            $table->time('time_of_supply')->nullable();
            $table->integer('posting')->default(0);
            $table->string('fbr_invoice_no')->nullable();
	    $table->text('response')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
