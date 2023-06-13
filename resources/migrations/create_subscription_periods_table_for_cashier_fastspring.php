<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TwentyTwoDigital\CashierFastspring\Helper\DynamicTableName;


class CreateSubscriptionPeriodsTableForCashierFastspring extends Migration
{
    private $table;
    private $subscriptionRelationalId;

    public function __construct()
    {
        $this->table = DynamicTableName::getTableName('SubscriptionPeriod')?? 'subscription_periods';
        $subscriptionTableName = DynamicTableName::getTableName('Subscription') ?? 'subscriptions';
        $this->subscriptionRelationalId = Str::snake(Str::singular($subscriptionTableName)).'_id';

    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger($this->subscriptionRelationalId);

            $table->string('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign($this->subscriptionRelationalId)->references('id')->on('subscriptions')->onDelete('cascade');
            $table->unique([$this->subscriptionRelationalId, 'type', 'start_date', 'end_date'], 'subscription_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
