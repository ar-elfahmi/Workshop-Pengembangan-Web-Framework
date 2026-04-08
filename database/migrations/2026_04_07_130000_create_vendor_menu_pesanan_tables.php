<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor', function (Blueprint $table) {
            $table->bigIncrements('idvendor');
            $table->string('nama_vendor', 150);
            $table->timestamps();
        });

        Schema::create('menu', function (Blueprint $table) {
            $table->bigIncrements('idmenu');
            $table->string('nama_menu', 150);
            $table->unsignedBigInteger('harga');
            $table->string('path_gambar')->nullable();
            $table->unsignedBigInteger('idvendor');
            $table->timestamps();

            $table->foreign('idvendor', 'fk_menu_vendor')
                ->references('idvendor')
                ->on('vendor')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->bigIncrements('idpesanan');
            $table->unsignedBigInteger('idvendor');
            $table->unsignedBigInteger('user_id');
            $table->string('nama', 150);
            $table->timestamp('timestamp')->useCurrent();
            $table->unsignedBigInteger('total');
            $table->enum('metode_bayar', ['va', 'qris']);
            $table->string('status_bayar', 30)->default('Pending');
            $table->string('midtrans_order_id', 80)->unique();
            $table->string('midtrans_transaction_id', 100)->nullable()->index();
            $table->string('snap_token', 100)->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->json('payment_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('idvendor', 'fk_pesanan_vendor')
                ->references('idvendor')
                ->on('vendor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('user_id', 'fk_pesanan_user')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->bigIncrements('iddetail_pesanan');
            $table->unsignedBigInteger('idmenu');
            $table->unsignedBigInteger('idpesanan');
            $table->unsignedInteger('jumlah');
            $table->unsignedBigInteger('harga');
            $table->unsignedBigInteger('subtotal');
            $table->timestamp('timestamp')->useCurrent();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->foreign('idmenu', 'fk_detail_menu')
                ->references('idmenu')
                ->on('menu')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('idpesanan', 'fk_detail_pesanan')
                ->references('idpesanan')
                ->on('pesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('vendor');
    }
};
