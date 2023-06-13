<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TwentyTwoDigital\CashierFastspring\Helper\ConfigHelper;
use TwentyTwoDigital\CashierFastspring\Helper\DynamicTableName;

class CreateSubscriptionsTableForCashierFastspring extends Migration
{
    private $table;

    public function __construct()
    {
        $this->table = DynamicTableName::getTableName('Subscription') ?? 'subscriptions';
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userId = ConfigHelper::getBillableModelRelationalKey();

        Schema::create($this->table, function (Blueprint $table) use ($userId) {
            $table->increments('id');
            $table->unsignedInteger($userId);
            $table->string('name');
            $table->string('fastspring_id')->nullable();
            $table->string('plan');
            $table->string('state');
            $table->integer('quantity');
            $table->string('currency');
            $table->string('interval_unit');
            $table->integer('interval_length');
            $table->string('swap_to')->nullable();
            $table->datetime('swap_at')->nullable();
            $table->timestamps();

            $table->foreign($userId)->references('id')->on('users')->onDelete('cascade');
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
