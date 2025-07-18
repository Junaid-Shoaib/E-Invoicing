<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('value_of_goods', 12, 2);
            $table->decimal('sale_tax_rate', 5, 2)->nullable();
            $table->decimal('amount_of_saleTax', 12, 2)->nullable();
            $table->decimal('extra_tax', 12, 2)->nullable();
            $table->decimal('further_tax', 12, 2)->nullable();
            $table->decimal('total', 12, 2);
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
        Schema::dropIfExists('invoice_items');
    }
}
