<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Enums\Constants;
use App\Model\CrOtpVerification;

use App\Exceptions\AppException;
use App\Model\User;
use App\Notifications\LoginEmailVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class OtpVerificationServices
{
    public static function emailVerificationRequest($request){

        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)){

            $user = User::getUserByEmail($request->email);
            $name = (isset($user->first_name)?ucwords(strtolower($user->first_name)):'N/A').' '.(isset($user->last_name)?ucwords(strtolower($user->last_name)):'N/A');
            $otp  = mt_rand(100000,999999);
            $user_id['user_id'] = $user->id;
            $message = array('OTP'=>$otp,'expiry_time'=> env('TIME_OUT_FOR_VERIFYING_THE_EMAIL'),'REALTIME'=>date("F d,Y"),'NAME'=>$name);
            $record = [ 'user_id'=>$user->id??null,
                        'type_of_verification'=>Constants::LOGIN_VERIFICATION,
                        'meta_data'=>$request->email,
                        'otp_code'=>$otp,
                        'verification_status'=>Constants::NOT_VERIFIED,
                    ];
            $verification_id['verification_id'] = CrOTPVerification::saveVerificationOtp($record);
            Notification::route('mail',$request->email)->notify(new LoginEmailVerification($message,'OtpVerification'));
            return $verification_id;

        } else {

            throw new AppException("Email address is not valid");

        }
    }

    public static function verifyEmailOtp($request)
    {
        $user = Auth::User();
        $otp_Verification = CrOTPVerification::verifyOtp($user->id,$request->email,$request->type_of_verification);
        if (!$otp_Verification) {
            throw new AppException("Invalid request");
        }

        if (Carbon::parse($otp_Verification->updated_at)->addMinutes(env('TIME_OUT_FOR_VERIFYING_THE_EMAIL'))->isPast()) {
            throw new AppException("Time out for verifying email.");
        }

        if($otp_Verification->meta_data ==$request->email) {
            if($otp_Verification->otp_code == $request->otp ) {
                $updates['verification_status'] = Constants::VERIFIED;
                CrOTPVerification::updateRecords($user->id,$request->type_of_verification,$request->otp,$updates);
            } else {
                throw new AppException("Incorrect OTP(One Time Password). Please try with valid OTP(One Time Password).");
            }
        } else {
            throw new AppException("Incorrect Email. Please try with valid Email.");
        }
    }

}