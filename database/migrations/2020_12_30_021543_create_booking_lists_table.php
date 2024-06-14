<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('barangkesenian_id');
            $table->integer('user_id');
            $table->date('date'); 
            $table->text('alamat');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', array('PENDING', 'DISETUJUI', 'DIGUNAKAN', 'DITOLAK', 'EXPIRED', 'BATAL', 'SELESAI'))->default('PENDING');
            $table->softDeletes();
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
        Schema::dropIfExists('booking_lists');
    }
}
