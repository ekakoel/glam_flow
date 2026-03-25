<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE bookings
            INNER JOIN services ON services.id = bookings.service_id
            SET bookings.end_time = ADDTIME(bookings.booking_time, SEC_TO_TIME(services.duration * 60))
            WHERE bookings.end_time IS NULL
        ');
    }

    public function down(): void
    {
        // no-op
    }
};
