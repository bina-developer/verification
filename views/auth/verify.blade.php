@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>OTP Verification</h2>

        {{-- نمایش خطاها --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- بخش ورود شماره تلفن --}}
        <div id="phone-form">
            <h4>Enter your phone number</h4>
            {{$numberr}}
            <form action="{{ route('verify.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Send OTP</button>
            </form>
        </div>

        {{-- بخش تأیید OTP --}}
        <div id="otp-form">
            <h4>Enter OTP</h4>
            <form action="{{ route('verify.submit') }}" method="POST">
                @csrf
                <input type="hidden" id="phone-hidden" name="phone">
                <div class="form-group">
                    <label for="otp">OTP:</label>
                    <input type="text" id="otp" name="otp" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Verify OTP</button>
            </form>
        </div>
    </div>

@endsection

