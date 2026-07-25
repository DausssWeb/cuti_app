<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // leave_type: annual (cuti tahunan), sick (cuti sakit)
            // employee: annual + sick only
            // manager: annual + sick only (approved by HRD)
            $table->enum('leave_type', ['annual', 'sick']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->text('reason');
            $table->string('sick_note')->nullable(); // upload file for sick leave
            // status flow:
            // employee: pending -> manager_approved / manager_rejected -> hrd_approved / hrd_rejected
            // manager: pending -> hrd_approved / hrd_rejected (skip manager approval)
            $table->enum('status', [
                'pending',
                'manager_approved',
                'manager_rejected',
                'hrd_approved',
                'hrd_rejected'
            ])->default('pending');

            $table->foreignId('manager_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_approved_at')->nullable();
            $table->text('manager_notes')->nullable();

            $table->foreignId('hrd_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hrd_approved_at')->nullable();
            $table->text('hrd_notes')->nullable();

            $table->year('year'); // for quota tracking
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
