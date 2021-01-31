<?php
/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 17:06
 */

namespace App\Http\Controllers\Frontend;

use App\Models\Email;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Service;
use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Subscription;
use Stripe\StripeClient;
use Cart;

class StripeController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function subscription()
    {
        if(auth()->user()->subscription) {
            abort(403, 'You are already have a subscription.');
        }

        $this->request->validate([
            'planId' => 'required|integer',
            'stripeToken' => 'required|string',
        ]);

        $service = Service::findOrFail($this->request->input('planId'));

        $stripe = new StripeClient(config('settings.payment_stripe_test_mode') ? config('settings.payment_stripe_test_key') : env('STRIPE_SECRET_API'));

        $product = $stripe->products->create([
            'name' => $service->title,
        ]);


        $plan = $stripe->plans->create([
            "amount" => in_array(config('settings.currency', 'USD'), config('payment.currency_decimals')) ? ($service->price  * 100) : (intval($service->price) * 100),
            "interval" => "month",
            'product' => $product->id,
            "currency" => config('settings.currency', 'USD')
        ]);

        $customer = $stripe->customers->create([
            "email" => auth()->user()->email,
            "source" => config('settings.payment_stripe_test_mode') ? 'tok_visa' : $this->request->input('stripeToken')
        ]);

        if($service->trial) {
            switch ($service->trial_period_format) {
                case 'D':
                    $trial_end = Carbon::now()->addDay($service->trial_period);
                    break;
                case 'W':
                    $trial_end = Carbon::now()->addWeek($service->trial_period);
                    break;
                case 'M':
                    $trial_end = Carbon::now()->addMonth($service->trial_period);
                    break;
                case 'Y':
                    $trial_end = Carbon::now()->addYear($service->trial_period);
                    break;
                default:
                    $trial_end = 'now';
            }
        } else {
            $trial_end = 'now';
        }

        $stripe_subscription = $stripe->subscriptions->create([
            'customer' => $customer->id,
            'items' => [
                ['plan' => $plan->id],
            ],
            'trial_end' => ($trial_end == 'now' ? $trial_end : $trial_end->timestamp),
        ]);

        if($stripe_subscription->id) {
            $subscription = new Subscription();
            $subscription->gate = 'stripe';
            $subscription->user_id = auth()->user()->id;
            $subscription->service_id = $service->id;
            $subscription->payment_status = 1;
            $subscription->transaction_id = $stripe_subscription->id;
            $subscription->token = $stripe_subscription->id;
            $subscription->next_billing_date = Carbon::parse($stripe_subscription->current_period_end);
            $subscription->trial_end = ($trial_end == 'now' ? Carbon::now() : $trial_end);
            $subscription->amount = $service->price;
            $subscription->currency = config('settings.currency', 'USD');
            if($stripe_subscription->status == 'active') {
                $subscription->cycles = $stripe_subscription->plan->interval_count;
                $subscription->last_payment_date = Carbon::now();
            }

            $subscription->save();

            (new Email)->subscriptionReceipt(auth()->user(), $subscription);

            return response()->json($subscription);

        } else {
            return response()->json([
                'message' => 'Payment failed',
                'errors' => array('message' => array(__('web.PAYMENT_FAILED_DESCRIPTION')))
            ], 500);
        }
    }

    public function purchase()
    {
        Cart::session(auth()->user()->id);

        if(Cart::isEmpty()) {
            abort(500,'Cart is empty');
        }

        $this->request->validate([
            'stripeToken' => 'required|string',
        ]);

        $description = "";

        foreach (Cart::getContent() as $item) {
           $description .= $item->id . '|';
        }

        $stripe = new StripeClient(config('settings.payment_stripe_test_mode') ? config('settings.payment_stripe_test_key') : env('STRIPE_SECRET_API'));

        $charge = $stripe->charges->create([
            'amount' => (Cart::getTotal() * 100),
            "currency" => config('settings.currency', 'USD'),
            "source" => config('settings.payment_stripe_test_mode') ? 'tok_visa' : $this->request->input('stripeToken'),
            'description' => $description,
        ]);

        if($charge->id) {
            foreach (Cart::getContent() as $item) {
                $order = new Order();
                $order->user_id = auth()->user()->id;
                $order->orderable_id = $item->attributes->orderable_id;
                $order->orderable_type = $item->attributes->orderable_type;
                $order->payment = 'stripe';
                $order->amount = $item->price;
                $order->currency = config('settings.currency', 'USD');
                $order->payment_status = $charge->captured ? 1 : 0;
                $order->transaction_id = $charge->id;
                $order->save();
            }

            Cart::clear();

            return response()->json($charge);

        } else {
            return response()->json([
                'message' => 'Payment failed',
                'errors' => array('message' => array(__('web.PAYMENT_FAILED_DESCRIPTION')))
            ], 500);
        }
    }
}