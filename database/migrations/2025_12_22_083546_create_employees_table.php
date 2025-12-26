<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('payroll_number'); // NOT unique
        $table->string('lastname');
        $table->string('firstname');
        $table->string('middle_initial', 5)->nullable();
        $table->string('grant_type_batch')->nullable();
        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};


