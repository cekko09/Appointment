<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('postcode')->nullable();  // Randevu adresi posta kodu
            $table->datetime('appointment_date');  // Randevu tarihi
            $table->string('client_name');  // Müşteri adı
            $table->string('client_email');  // Müşteri email
            $table->string('client_phone');  // Müşteri telefon numarası
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');  // İlgilenecek emlakçı
            $table->decimal('location_lat', 10, 7);  // Randevu konum lat değeri
            $table->decimal('location_lng', 10, 7);  // Randevu konum lng değeri
            $table->string('distance');  // Ofis ile randevu adresi arası mesafe
            $table->string('duration');  // Tahmini seyahat süresi
            $table->time('available_time')->nullable()->after('duration');
            $table->timestamps();
        });
    }
    
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
