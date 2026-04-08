<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('customer')->after('name');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('role');
            $table->index('role', 'idx_users_role');
            $table->foreign('vendor_id', 'fk_users_vendor')
                ->references('idvendor')
                ->on('vendor')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        DB::table('users')->where('email', 'admin@mail.com')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_users_vendor');
            $table->dropIndex('idx_users_role');
            $table->dropColumn(['role', 'vendor_id']);
        });
    }
};
