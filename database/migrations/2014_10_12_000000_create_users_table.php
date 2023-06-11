<?php

use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('serial_number')->unique();
            $table->string('departement')->nullable();
            $table->longText('skills')->nullable();
            $table->longText('image')->nullable();
            $table->longText('biography')->nullable();
            $table->longText('id_photo')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->tinyInteger('type')->default(UserTypes::EMPLOYEE);
            $table->tinyInteger('status')->default(EmployeeStatus::ON_DUTY);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
