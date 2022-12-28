<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c2brequests', function (Blueprint $table) {
            $table->id();
            $table->string('TransactionType');
            $table->string('TransID');
            $table->string('TransTime');
            $table->string('TransAmount');
            $table->string('BusinessShortCode');
            $table->string('BillRefNumber');
            $table->string('InvoiceNumber')->nullable();
            $table->string('OrgAccountBalance');
            $table->string('ThirdPartyTransID')->nullable();
            $table->string('MSISDN');
            $table->string('FirstName');
            $table->string('MiddleName')->nullable();
            $table->string('LastName')->nullable();

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
        Schema::dropIfExists('c2brequests');
    }
};
