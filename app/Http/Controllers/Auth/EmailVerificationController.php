<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyMail;
use App\Models\User;
use App\Traits\SendMail;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\UserActivityLog\Traits\LogActivity;

class EmailVerificationController extends Controller
{
    use SendMail;
    public function __construct()
    {
        $this->middleware('maintenance_mode');
    }
    public function emailVerify(Request $request){
        $verify_code = \Illuminate\Support\Facades\Request::get('code');
        $user = User::where('verify_code',$verify_code)->firstOrFail();

        if ($user->is_verified ==1){

            Toastr::warning(__('common.account_already_verified').' '.__('common.admin_contact_with_you') , __('common.warning'));
            return redirect(route('seller.dashboard'));
        }
        else{
            $user->is_verified = 1;
            $user->save();

            Toastr::success(__('common.account_verified_successfully'), __('common.success'));
            LogActivity::addLoginLog(Auth::user()->id, Auth::user()->first_name.' - logged in at : '. Carbon::now());
            return redirect(route('seller.dashboard'));
        }
    }
    public function resendMail(Request $request){
        $user = User::where('verify_code',$request->verify_code)->firstOrFail();

        if($user->is_verified==0){
            $code = '<a class="btn btn-success" href="'.url('/verify?code=').$user['verify_code'].'">Click Here To Verify Your Account</a>';
            $this->sendVerificationMail($user,$code);

            Toastr::success(__('common.verification_link_resend_successfully'), __('common.success'));
            return redirect()->back();
        }else{

            Toastr::warning(__('common.account_already_verified'), __('common.warning'));
            return redirect()->back();
        }

    }
}
