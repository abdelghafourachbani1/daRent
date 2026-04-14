<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('nom_ville');   
            $table->string('region');      
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('label');         
            $table->text('description');     
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('titre');    
            $table->text('description');
            $table->string('adress');
            $table->decimal('prix_mensuel', 10, 2);
            $table->string('type');
            $table->enum('status', ['available', 'rented', 'archived'])->default('available');
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            $table->boolean('availability')->default(true); 
            $table->date('date_envoie')->nullable();
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->text('url_fichier');     
            $table->string('type_fichier');  
            $table->timestamps();
        });

        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('nom_equipement'); 
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('property_equipement', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipement_id')->constrained()->onDelete('cascade');
            $table->primary(['property_id', 'equipement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_equipement');
        Schema::dropIfExists('equipements');
        Schema::dropIfExists('media');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('cities');
    }
};