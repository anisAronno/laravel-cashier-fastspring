<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpgradeUserTableForCashierFastspring extends Migration
{
    private $table;

    public function __construct()
    {
        $billableModel = getenv('FASTSPRING_MODEL') ?: config('services.fastspring.model', 'App\Models\User');
        $this->table = Str::snake(Str::plural(class_basename($billableModel)));
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function ($table) {
            $table->string('fastspring_id')->nullable();
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('language')->nullable();
            $table->string('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn(['fastspring_id', 'company', 'phone', 'language', 'country']);
        });
    }
}
