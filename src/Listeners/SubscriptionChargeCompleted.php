<?php
/**
 * This file implements Subscription Charge Completed.
 *
 * @author    Bilal Gultekin <bilal@gultekin.me>
 * @author    Justin Hartman <justin@22digital.co.za>
 * @copyright 2019 22 Digital
 * @license   MIT
 * @since     v0.1
 */

namespace TwentyTwoDigital\CashierFastspring\Listeners;

use Carbon\Carbon;
 use Illuminate\Support\Str;
use TwentyTwoDigital\CashierFastspring\Events;
use TwentyTwoDigital\CashierFastspring\Helper\ConfigHelper;
use TwentyTwoDigital\CashierFastspring\Invoice;
use TwentyTwoDigital\CashierFastspring\Subscription;

/**
 * This class is a listener for subscription charge completed events.
 * It updates or creates related order model so that you can show payment
 * and bill details to your customers.
 *
 * IMPORTANT: This class handles expansion enabled webhooks.
 *
 * {@inheritdoc}
 */
class SubscriptionChargeCompleted extends Base
{
    /**
     * Create the event listener.
     *
     * @return null
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \TwentyTwoDigital\CashierFastspring\Events\SubscriptionChargeCompleted $event
     *
     * @return void
     */
    public function handle(Events\SubscriptionChargeCompleted $event)
    {
        // when subscription charge completed event is triggered
        // try to find that order on the database
        // if not exists then create one
        $data = $event->data;

        $invoice = Invoice::firstOrNew([
            'fastspring_id' => $data['order']['id'],
            'type'          => 'subscription',
        ]);

        // retrieve subscription to change state of it
        $subscription = Subscription::where('fastspring_id', $data['subscription']['id'])->first();

        // unfortunately fastspring does not provide subscription
        // dates with this event event their doc says it provides
        // we need to calculate ourselves
        $nextDate = Carbon::createFromTimestampUTC($data['subscription']['nextInSeconds']);
        $periodEndDate = $nextDate->subDay()->format('Y-m-d H:i:s');

        // yeap, weird way
        $methodName = 'sub' . Str::title($subscription->interval_unit) . 'sNoOverflow';
        $periodStartDate = $nextDate->$methodName($subscription->interval_length)->addDay()->format('Y-m-d H:i:s');
        $userId = ConfigHelper::getBillableModelRelationalKey();

        // fill the model
        $invoice->subscription_sequence = $data['subscription']['sequence'];
        $invoice->$userId = $this->getUserByFastspringId($data['account']['id'])->id;
        $invoice->subscription_display = $data['subscription']['display'];
        $invoice->subscription_product = $data['subscription']['product'];
        $invoice->invoice_url = $data['order']['invoiceUrl'];
        $invoice->total = $data['order']['total'];
        $invoice->tax = $data['order']['tax'];
        $invoice->subtotal = $data['order']['subtotal'];
        $invoice->discount = $data['order']['discount'];
        $invoice->currency = $data['order']['currency'];
        $invoice->payment_type = $data['order']['payment']['type'];
        $invoice->completed = $data['order']['completed'];
        $invoice->subscription_period_start_date = $periodStartDate;
        $invoice->subscription_period_end_date = $periodEndDate;

        // and save
        $invoice->save();

        if ($subscription) {
            $subscription->state = 'active';
            $subscription->save();
        }
    }
}
