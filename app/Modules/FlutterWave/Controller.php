<?php

namespace App\Modules\FlutterWave;

use App\Models\Order;
use App\Models\Service;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use App\Models\Email;
use App\Models\Subscription;
use \App\Modules\FlutterWave\Rave;
use Illuminate\Support\Facades\Log;

class Controller
{
    protected $publicKey;
    protected $secretKey;
    protected $paymentMethod = 'both';
    protected $customLogo;
    protected $customTitle;
    protected $secretHash;
    protected $txref;
    protected $integrityHash;
    protected $env = 'staging';
    protected $transactionPrefix;
    protected $urls = [
        "live" => "https://api.ravepay.co",
        "others" => "https://ravesandboxapi.flutterwave.com",
    ];
    protected $baseUrl;
    protected $transactionData;
    protected $overrideTransactionReference;
    protected $verifyCount = 0;
    protected $request;
    protected $unirestRequest;
    protected $body;

    protected $amount;
    protected $description;
    protected $country;
    protected $currency;
    protected $email;
    protected $firstName;
    protected $lastName;
    protected $phoneNumber;
    protected $payButtonText;
    protected $redirectUrl;
    protected $handler;
    protected $meta;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $prefix = env('RAVE_PREFIX');
        $overrideRefWithPrefix = false;
        $this->publicKey = config('payment.gateway.flutterwave.public_key');
        $this->secretKey = config('payment.gateway.flutterwave.secret_key');
        $this->env = config('payment.gateway.flutterwave.environment');
        $this->customLogo = env('RAVE_LOGO');
        $this->customTitle = env('RAVE_TITLE');
        $this->secretHash = env('RAVE_SECRET_HASH');
        $this->transactionPrefix = $prefix . '_';
        $this->overrideTransactionReference = $overrideRefWithPrefix;


        Log::notice('Generating Reference Number....');
        if ($this->overrideTransactionReference) {
            $this->txref = $this->transactionPrefix;
        } else {
            $this->txref = uniqid($this->transactionPrefix);
        }
        Log::notice('Generated Reference Number....' . $this->txref);

        $this->baseUrl = $this->urls[($this->env === "live" ? "$this->env" : "others")];

        Log::notice('Rave Class Initializes....');
    }

    public function createCheckSum($redirectURL)
    {
        if ($this->request->payment_method) {
            $this->paymentMethod = $this->request->payment_method; // value can be card, account or both
        }

        if ($this->request->logo) {
            $this->customLogo = $this->request->logo; // This might not be included if you have it set in your .env file
        }

        if ($this->request->pay_button_text) {
            $this->payButtonText = $this->request->pay_button_text; // This might not be included if you have it set in your .env file
        }

        if ($this->request->title) {
            $this->customTitle = $this->request->title; // This can be left blank if you have it set in your .env file
        }

        if ($this->request->ref) {
            $this->txref = $this->request->ref;
        }

        Log::notice('Generating Checksum....');
        $options = array(
            "PBFPubKey" => $this->publicKey,
            "amount" => $this->request->amount,
            "customer_email" => $this->request->email,
            "customer_firstname" => $this->request->firstname,
            "txref" => $this->txref,
            "payment_method" => $this->paymentMethod,
            "customer_lastname" => $this->request->lastname,
            "country" => $this->request->country,
            "currency" => $this->request->currency,
            "custom_description" => $this->request->description,
            "custom_logo" => $this->customLogo,
            "custom_title" => $this->customTitle,
            "customer_phone" => $this->request->phonenumber,
            "redirect_url" => $redirectURL,
            "pay_button_text" => $this->request->pay_button_text
        );

        if (!empty($this->request->paymentplan)) {
            $options["payment_plan"] = $this->request->paymentplan;
        }


        ksort($options);

        $this->transactionData = $options;

        $hashedPayload = '';

        foreach ($options as $value) {
            $hashedPayload .= $value;
        }

        $completeHash = $hashedPayload . $this->secretKey;

        $this->integrityHash = hash('sha256', $completeHash);
        return $this;
    }

    public function subscriptionAuthorization()
    {
        if(auth()->user()->subscription) {
            abort(403, 'You are already have a subscription.');
        }

        $service = Service::findOrFail($this->request->route('id'));

        echo "<form style='visibility: hidden;' name=\"payment\" id=\"payment\" method=\"POST\" action=\"" . route('frontend.flutterwave.subscription.authorization.post', ['id' => $service->id]) . "\">
    " . csrf_field() . "
    <input type=\"hidden\" name=\"amount\" value=\"" . round($service->price) . "\" />
    <input type=\"hidden\" name=\"payment_method\" value=\"both\" />
    <input type=\"hidden\" name=\"description\" value=\"" . $service->title . "\" />
    <input type=\"hidden\" name=\"country\" value=\"NG\" />
    <input type=\"hidden\" name=\"currency\" value=\"" . config('settings.currency', 'USD') . "\" />
    <input type=\"hidden\" name=\"email\" value=\"" . auth()->user()->email . "\" />
    <input type=\"hidden\" name=\"firstname\" value=\"" . auth()->user()->name . "\" />
    <input type=\"hidden\" name=\"metadata\" value=\"" . json_encode(array(array('metaname' => 'color', 'metavalue' => 'blue'), array('metaname' => 'size', 'metavalue' => 'big'))) . "\" >
    <input type=\"submit\" value=\"Buy\"  />
    <input type=\"hidden\" name=\"logo\" value=\"" . asset('skins/default/images/small-logo.png') . "\" />
    <input type=\"hidden\" name=\"title\" value=\"" . env('APP_NAME') . "\" />
</form>
<script type=\"text/javascript\">
            window.onload = function(){
              document.forms['payment'].submit();
            }
        </script>
        ";
        exit;
    }

    public function subscriptionAuthorizationPost(){
        $service = Service::findOrFail($this->request->route('id'));

        $meta = array();
        if (!empty($this->request->metadata)) {
            $meta = json_decode($this->request->metadata, true);
        }

        $subAccounts = array();
        if (!empty($this->request->subaccounts)) {
            $subAccounts = json_decode($this->request->subaccounts, true);
        }

        $this->createCheckSum(route('frontend.flutterwave.subscription.callback', ['id' => $service->id]));
        $this->transactionData = array_merge($this->transactionData, array('data-integrity_hash' => $this->integrityHash), array('meta' => $meta));

        if (!empty($subAccounts)) {
            $this->transactionData = array_merge($this->transactionData, array('subaccounts' => $subAccounts));
        }


        $json = json_encode($this->transactionData);

        echo '<html>';
        echo '<body>';
        echo '<center>Proccessing...<br /><img style="height: 50px;" src="https://media.giphy.com/media/swhRkVYLJDrCE/giphy.gif" /></center>';
        echo '<script type="text/javascript" src="' . $this->baseUrl . '/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>';
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'var data = JSON.parse(\'' . $json . '\');';
        echo 'getpaidSetup(data);';
        echo '});';
        echo '</script>';
        echo '</body>';
        echo '</html>';

        exit;
    }

    public function subscriptionCallback()
    {
        if(auth()->user()->subscription) {
            abort(403, 'You are already have a subscription.');
        }

        $service = Service::findOrFail($this->request->route('id'));

        $subscription = new Subscription();
        $subscription->gate = 'pdo';
        $subscription->user_id = auth()->user()->id;
        $subscription->service_id = $service->id;
        $subscription->payment_status = 1;
        $subscription->transaction_id = $this->request->input('TransID');
        $subscription->token = $this->request->input('TransactionToken');
        $subscription->trial_end = null;

        switch ($service->plan_period_format) {
            case 'D':
                $next_billing_date = Carbon::now()->addDay($service->plan_period)->format('Y-m-d\TH:i:s\Z');
                break;
            case 'W':
                $next_billing_date = Carbon::now()->addWeek($service->plan_period)->format('Y-m-d\TH:i:s\Z');
                break;
            case 'M':
                $next_billing_date = Carbon::now()->addMonth($service->plan_period)->format('Y-m-d\TH:i:s\Z');
                break;
            case 'Y':
                $next_billing_date = Carbon::now()->addYear($service->plan_period)->format('Y-m-d\TH:i:s\Z');
                break;
            default:
                $next_billing_date = date("Y-m-d\TH:i:s\Z", strtotime('+2 minute'));
        }

        $subscription->next_billing_date = $next_billing_date;
        $subscription->cycles = 1;
        $subscription->amount = $service->price;
        $subscription->currency = config('settings.currency', 'USD');

        if(! $service->trial) {
            $subscription->last_payment_date = Carbon::now();
        }

        $subscription->save();

        (new Email)->subscriptionReceipt(auth()->user(), $subscription);

        echo '<script type="text/javascript">
            var opener = window.opener;
            if(opener) {
                opener.Payment.subscriptionSuccess();
                window.close();
            }
            </script>';

        exit;
    }

    public function purchaseAuthorization()
    {
        Cart::session(auth()->user()->id);

        if(Cart::isEmpty()) {
            abort(500,'Cart is empty');
        }

        $items = array();
        foreach (Cart::getContent() as $product) {
            $item = $product->associatedModel->title;
            $items[] = $item;
        }

        echo "<form style='visibility: hidden;' name=\"payment\" id=\"payment\" method=\"POST\" action=\"" . route('frontend.flutterwave.purchase.authorization.post') . "\">
    " . csrf_field() . "
    <input type=\"hidden\" name=\"amount\" value=\"" . round(Cart::getTotal()) . "\" />
    <input type=\"hidden\" name=\"payment_method\" value=\"both\" />
    <input type=\"hidden\" name=\"description\" value=\"" . implode('|', $items) . "\" />
    <input type=\"hidden\" name=\"country\" value=\"NG\" />
    <input type=\"hidden\" name=\"currency\" value=\"" . config('settings.currency', 'USD') . "\" />
    <input type=\"hidden\" name=\"email\" value=\"" . auth()->user()->email . "\" />
    <input type=\"hidden\" name=\"firstname\" value=\"" . auth()->user()->name . "\" />
    <input type=\"hidden\" name=\"metadata\" value=\"" . json_encode(array(array('metaname' => 'color', 'metavalue' => 'blue'), array('metaname' => 'size', 'metavalue' => 'big'))) . "\" >
    <input type=\"submit\" value=\"Buy\"  />
    <input type=\"hidden\" name=\"logo\" value=\"" . asset('skins/default/images/small-logo.png') . "\" />
    <input type=\"hidden\" name=\"title\" value=\"" . env('APP_NAME') . "\" />
</form>
<script type=\"text/javascript\">
            window.onload = function(){
              document.forms['payment'].submit();
            }
        </script>
        ";
        exit;
    }

    public function purchaseAuthorizationPost(){

        $meta = array();
        if (!empty($this->request->metadata)) {
            $meta = json_decode($this->request->metadata, true);
        }

        $subAccounts = array();
        if (!empty($this->request->subaccounts)) {
            $subAccounts = json_decode($this->request->subaccounts, true);
        }

        $this->createCheckSum(route('frontend.flutterwave.purchase.callback'));
        $this->transactionData = array_merge($this->transactionData, array('data-integrity_hash' => $this->integrityHash), array('meta' => $meta));

        if (!empty($subAccounts)) {
            $this->transactionData = array_merge($this->transactionData, array('subaccounts' => $subAccounts));
        }


        $json = json_encode($this->transactionData);

        echo '<html>';
        echo '<body>';
        echo '<center>Proccessing...<br /><img style="height: 50px;" src="https://media.giphy.com/media/swhRkVYLJDrCE/giphy.gif" /></center>';
        echo '<script type="text/javascript" src="' . $this->baseUrl . '/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>';
        echo '<script>';
        echo 'document.addEventListener("DOMContentLoaded", function(event) {';
        echo 'var data = JSON.parse(\'' . $json . '\');';
        echo 'getpaidSetup(data);';
        echo '});';
        echo '</script>';
        echo '</body>';
        echo '</html>';

        exit;
    }

    public function purchaseCallback()
    {
        Cart::session(auth()->user()->id);

        foreach (Cart::getContent() as $item) {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->orderable_id = $item->attributes->orderable_id;
            $order->orderable_type = $item->attributes->orderable_type;
            $order->payment = 'pdo';
            $order->amount = $item->price;
            $order->currency = config('settings.currency', 'USD');
            $order->payment_status = 1;
            $order->transaction_id = $this->request->input('TransID');
            $order->save();
        }

        Cart::clear();

        echo '<script type="text/javascript">
                        var opener = window.opener;
                        if(opener) {
                            opener.Payment.purchaseSuccess();
                            window.close();
                        }
                        
                        </script>';
        exit();
    }
}
