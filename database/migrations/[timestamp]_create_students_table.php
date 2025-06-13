Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('matric_id')->unique();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->boolean('is_first_login')->default(true);
    $table->string('phone')->nullable();
    $table->timestamps();
}); 