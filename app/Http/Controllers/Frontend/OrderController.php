<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\PaymentGateway\Http\Controllers\StripeController;
use Modules\PaymentGateway\Http\Controllers\RazorpayController;
use Modules\PaymentGateway\Http\Controllers\PayPalController;
use Modules\PaymentGateway\Http\Controllers\PaystackController;
use Modules\PaymentGateway\Http\Controllers\PaytmController;
use Modules\PaymentGateway\Http\Controllers\InstamojoController;
use Modules\PaymentGateway\Http\Controllers\BankPaymentController;
use Modules\PaymentGateway\Http\Controllers\MidtransController;
use Modules\PaymentGateway\Http\Controllers\PayUmoneyController;
use Modules\PaymentGateway\Http\Controllers\FlutterwaveController;
use App\Models\DigitalFileDownload;
use App\Models\Order;
use Modules\PaymentGateway\Services\PaymentGatewayService;
use Modules\OrderManage\Repositories\CancelReasonRepository;
use Unicodeveloper\Paystack\Paystack;
use App\Services\OrderService;
use Modules\OrderManage\Repositories\DeliveryProcessRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use App\Traits\GeneratePdf;
use App\Traits\Otp;
use App\Traits\SendMail;
use Exception;
use Modules\GeneralSetting\Repositories\GeneralSettingRepository;
use Modules\UserActivityLog\Traits\LogActivity;
use Session;

class OrderController extends Controller
{
    use GeneratePdf, SendMail, Otp;
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('maintenance_mode');
    }

    public function my_purchase_order_index(Request $request)
    {
        if ($request->has('rn')) {
            $data['orders'] = $this->orderService->myPurchaseOrderListwithRN($request->rn);
            $data['rn'] = $request->rn;
        } else {
            $data['orders'] = $this->orderService->myPurchaseOrderList();
        }
        $cancelReasonRepo = new CancelReasonRepository;
        $data['cancel_reasons'] = $cancelReasonRepo->getAll();
        $data['no_paid_orders'] = $this->orderService->myPurchaseOrderListNotPaid();
        $data['to_shippeds'] = $this->orderService->myPurchaseOrderPackageListShipped();
        $data['to_recieves'] = $this->orderService->myPurchaseOrderPackageListRecieved();

        if (auth()->user()->role_id != 4) {
            return view('backEnd.pages.customer_data.order', $data);
        } else {
            return view(theme('pages.profile.order'), $data);
        }
    }

    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'customer_shipping_address' => auth()->check() ? 'required' : '',
            'customer_billing_address' => auth()->check() ? 'required' : '',
            'customer_email' => auth()->check() ? 'required' : '',
            'customer_phone' => auth()->check() ? 'required' : '',
            'payment_method' => 'required',
            'grand_total' => 'required',
            'sub_total' => 'required',
            'number_of_package' => 'required',
            'number_of_item' => 'required',
            'shipping_cost' => 'required',
            'shipping_method' => 'required',
            'delivery_date' => 'required',
            'guest_shipping_phone' => !auth()->check() ? 'required' : '',
            'guest_shipping_email' => !auth()->check() ? 'required' : '',
        ]);

        if (isModuleActive('Otp') && otp_configuration('otp_activation_for_order') && $request->payment_method == 1) {
            try {
                if (!$this->sendOtpForOrder($request)) {
                    Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
                    return back();
                }
                Session::put('request', $request->all());
                return view(theme('pages.order_otp'), compact('request'));
            } catch (Exception $e) {
                LogActivity::errorLog($e->getMessage());
                Toastr::error(__('otp.something_wrong_on_otp_send'), __('common.error'));
                return back();
            }
        }
        DB::beginTransaction();

        try {
            $order = $this->orderService->orderStore($request->except('_token'));

            DB::commit();
            if (app('business_settings')->where('type', 'mail_notification')->first()->status == 1) {
                $this->sendInvoiceMail($order->order_number, $order);
            }
            Toastr::success(__('order.oredre_created_successfully'), __('common.success'));
            LogActivity::successLog('order store successful.');
            return redirect()->route('frontend.order.summary_after_checkout', encrypt($order->id));
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            DB::rollback();
            Toastr::error(__('order.order_submit_failed'));
            return back();
        }
    }

    public function payment(Request $request)
    {
        session()->put('order_payment', '1');
        if ($request->method == "Stripe") {
            $data['gateway_id'] = encrypt(4);
            $stripeController = new StripeController;
            $response = $stripeController->stripePost($request->all());
        }
        if ($request->method == "RazorPay") {
            $data['gateway_id'] = encrypt(6);
            $razorpayController = new RazorpayController;
            $response = $razorpayController->payment($request->all());
        }
        if ($request->method == "Paypal") {
            $data['gateway_id'] = encrypt(3);
            $paypalController = new PayPalController;
            $response = $paypalController->payment($request->all());
        }
        if ($request->method == "Paystack") {
            $data['gateway_id'] = encrypt(5);
            $paystack = new Paystack(env('PAYSTACK_SECRET'), env('PAYSTACK_PAYMENT_URL'));
            return $paystack->getAuthorizationUrl()->redirectNow();
        }
        if ($request->method == "BankPayment") {
            $data['gateway_id'] = encrypt(7);
            $bankController = new BankPaymentController;
            $response = $bankController->store($request->all());
        }
        if ($request->method == "PayTm") {
            $paytm = new PaytmController;
            return $paytm->payment($request->all());
        }
        if ($request->method == "Instamojo") {
            $instamojo = new InstamojoController;
            return $instamojo->paymentProcess($request->all());
        }
        if ($request->method == "Midtrans") {
            $midtrans = new MidtransController;
            return $midtrans->paymentProcess($request->all());
        }
        if ($request->method == "PayUMoney") {
            $PayUMoney = new PayUmoneyController;
            return $PayUMoney->payment($request->all());
        }
        if ($request->method == "flutterwave") {
            $flutterWaveController = new FlutterwaveController;
            return $flutterWaveController->payment($request->all());
        }
        $data['payment_id'] = encrypt($response);
        return redirect()->route('frontend.checkout', $data);
    }

    public function my_purchase_order_detail($id)
    {
        try {
            $data['order'] = $this->orderService->orderFindByID(decrypt($id));
            $orderDeliveryRepo = new DeliveryProcessRepository;
            $data['processes'] = $orderDeliveryRepo->getAll();
            $cancelReasonRepo = new CancelReasonRepository;
            $data['cancel_reasons'] = $cancelReasonRepo->getAll();
            if (auth()->check()) {
                return view(theme('pages.profile.order_details'), $data);
            } else {
                return view(theme('pages.profile.order_details_for_guest'), $data);
            }
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    public function my_purchase_order_pdf($id)
    {
        try {
            $data = $this->orderService->orderFindByID(decrypt($id));
            return $this->generate_pdf(theme('pages.profile.order_pdf'), $data);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return response()->json($e);
        }
    }

    public function my_purchase_order_cancel(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'reason' => 'required',
        ]);
        try {
            $data = $this->orderService->orderFindByID($request->order_id);
            if (auth()->user()->id == $data->customer_id && $data->is_confirmed != 1) {
                $data->is_cancelled = 1;
                $data->cancel_reason_id = $request->reason;
                $data->save();
                LogActivity::successLog('Purchase order cancel successful for ' . auth()->user()->first_name);
                Toastr::success(__('order.your_order_has_been_cancelled'));
            } else {
                Toastr::error(__('order.order_cancellation_failed'));
            }

            return back();
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('order.your_order_cancellation_has_been_failed_try_again'));
            return back();
        }
    }

    public function my_purchase_order_package_cancel(Request $request)
    {
        try {
            $data = $this->orderService->orderPackageFindByID($request->order_id);
            if ($data->order->is_confirmed != 1) {
                $data->is_cancelled = 1;
                $data->cancel_reason_id = $request->reason;
                $data->save();

                Toastr::success(__('order.your_package_order_has_been_cancelled'));
                LogActivity::successLog('My purchase order package cancel successful.');
            } else {
                Toastr::error(__('order.package_order_cancellation_failed'));
            }
            return back();
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('order.your_order_cancellation_has_been_failed_try_again'));
            return back();
        }
    }

    public function order_summary($id)
    {
        try {
            $data['order'] = $this->orderService->orderFindByID(decrypt($id));
            return view(theme('pages.checkout_summary'), $data);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return response()->json($e);
        }
    }

    public function track_order()
    {
        return view(theme('pages.track_order'));
    }

    public function track_order_find(Request $request)
    {
        $request->validate([
            'order_number' => 'required'
        ]);

        try {
            if (auth()->check()) {
                $data['order'] = $this->orderService->orderFindByOrderNumber($request->except('_token'), auth()->user());
            } else {
                $data['order'] = $this->orderService->orderFindByOrderNumber($request->except('_token'), null);
            }

            if ($data['order'] == "Invalid Tracking ID") {
                Toastr::error($data['order']);
                return back();
            } elseif ($data['order'] == "Invalid Secret ID") {
                Toastr::error($data['order']);
                return back();
            } elseif ($data['order'] == "Phone Number didn't match") {
                Toastr::error($data['order']);
                return back();
            } else {
                return redirect()->route('frontend.my_purchase_order_detail', encrypt($data['order']->id));
            }
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return back();
        }
    }

    public function digital_product_index()
    {
        $data['digital_products'] = DigitalFileDownload::where('customer_id', auth()->user()->id)->latest()->paginate(10);
        if (auth()->user()->role_id != 4) {
            return view('backEnd.pages.customer_data.digital_purchased', $data);
        } else {
            return view(theme('pages.profile.digital_purchased'), $data);
        }
    }
}
