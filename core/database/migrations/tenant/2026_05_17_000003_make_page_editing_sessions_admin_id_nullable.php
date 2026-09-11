<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Same root cause as T22: API routes use 'api' middleware (no session),
        // so Auth::guard('admin')->id() returns null. The page_editing_sessions
        // table was created with admin_id NOT NULL, causing React's startSession
        // call to fail with SQLSTATE[23000], which throws in JS and causes the
        // initialization useEffect catch block to replace saved canvas content
        // with a default empty container.
        Schema::table('page_editing_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('page_editing_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable(false)->change();
        });
    }
};
