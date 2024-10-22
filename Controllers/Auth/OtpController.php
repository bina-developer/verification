<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtpController extends Controller
{
    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService=$smsService;
    }
    public function showVerifyForm(Request $request)
    {
        //برای تست اینکار انجام شده چون سیستم پیامکی واقعی وجود ندارد
        $numberr=Cache::get('otp_' .  session('phone'));
        return view('auth.verify',compact('numberr')); 
    }

    public function handleOtp(Request $request)
    {
        $phone = session('phone');
        
        //اعتبار سنجی شماره موبایل در هنگام ذخیره سازی انجام شود

        // گرفتن تعداد تلاش‌های کاربر برای درخواست مجدد یا تأیید OTP
        $attempts = Cache::get('attempts_' . $phone, 0);
        // اگر تعداد تلاش‌ها بیشتر از ۳ بار باشد، محدودیت زمانی اعمال شود
        if ($attempts >= 30) {
            return response()->json(['error' => 'Too many attempts. Please try again later.'], 429);
        }
        // گرفتن OTP از کش
        $otp = Cache::get('otp_' . $request->phone);
        // اگر کاربر OTP وارد کرده و می‌خواهد آن را تأیید کند
        if ($request->has('otp')) {
            if ($otp && $otp == $request->otp) {
                // اگر OTP صحیح بود، اطلاعات کاربر تأیید می‌شود
                Cache::forget('otp_' . $phone); // حذف OTP از کش
                Cache::forget('attempts_' . $phone); // ریست کردن تعداد تلاش‌ها
                return response()->json(['message' => 'OTP verified']);
            } else { 
                // اگر OTP غلط بود
                Cache::put('attempts_' . $phone, $attempts + 1, now()->addMinutes(10));
                return response()->json(['error' => 'Invalid OTP'], 400);
            }
        } 
        else {
            // اگر OTP وارد نشده و کاربر درخواست ارسال مجدد کرده است
            $newOtp = rand(100000, 999999);
            // ذخیره OTP جدید در کش
            Cache::put('otp_' . $phone, $newOtp, now()->addMinutes(5));
            Cache::put('attempts_' . $phone, $attempts + 1, now()->addMinutes(10));
            // ارسال OTP جدید
            $this->sendOtp($phone, $newOtp);
            return response()->json(['message' => 'OTP resent']);   
        }
    }
    // تابع ارسال OTP به کاربر
    private function sendOtp($phone, $otp)
    {
        // استفاده از سرویس پیامک برای ارسال OTP
         $this->smsService->sendSms($phone, 'Your OTP is: ' . $otp);
        return true;
    }
}
