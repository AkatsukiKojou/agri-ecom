@extends('user.layout')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #e8f5e9 0%, #f6fef6 100%);
    }
    .fluent-otp-card {
        max-width: 400px;
        margin: 48px auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(44,120,44,0.10);
        padding: 36px 32px 28px 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .fluent-otp-title {
        font-size: 2.1rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 6px;
        letter-spacing: 0.02em;
        text-align: center;
    }
    .fluent-otp-desc {
        color: #388e3c;
        font-size: 1.08rem;
        text-align: center;
        margin-bottom: 18px;
    }
    .fluent-otp-email {
        background: #e8f5e9;
        color: #1b5e20;
        font-size: 1.08rem;
        font-weight: 500;
        text-align: center;
        border-radius: 10px;
        padding: 10px 0;
        margin-bottom: 22px;
        width: 100%;
    }
    .fluent-otp-label {
        font-size: 1rem;
        color: #388e3c;
        font-weight: 500;
        margin-bottom: 8px;
        text-align: left;
        width: 100%;
    }
    .fluent-otp-input {
        width: 100%;
        padding: 12px 0;
        font-size: 1.3rem;
        text-align: center;
        border-radius: 8px;
        border: 1.5px solid #c8e6c9;
        outline: none;
        margin-bottom: 18px;
        transition: border-color 0.2s;
    }
    .fluent-otp-input:focus {
        border-color: #43a047;
        box-shadow: 0 0 0 2px #c8e6c9;
    }
    .fluent-otp-btn {
        width: 100%;
        background: linear-gradient(90deg, #43a047 0%, #388e3c 100%);
        color: #fff;
        font-weight: 600;
        font-size: 1.08rem;
        padding: 12px 0;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(44,120,44,0.08);
        cursor: pointer;
        transition: background 0.2s;
        margin-bottom: 8px;
    }
    .fluent-otp-btn:hover {
        background: linear-gradient(90deg, #388e3c 0%, #43a047 100%);
    }
    .fluent-otp-error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 18px;
        width: 100%;
        text-align: center;
    }
    .fluent-otp-footer {
        color: #888;
        font-size: 0.97rem;
        text-align: center;
        margin-top: 28px;
    }
</style>

<div class="fluent-otp-card">
    <div class="fluent-otp-title">Order Verification</div>
    <div class="fluent-otp-desc">Enter the 6-digit OTP code sent to your email to confirm your order.</div>
    @php
        $defaultAddress = auth()->user()->shippingAddresses()->where('is_default', true)->first();
    @endphp
    @if($defaultAddress)
        <div class="fluent-otp-email">
            Sent to: <span>{{ $defaultAddress->email }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="fluent-otp-error">
            {{ session('error') }}
        </div>
    @endif
    <form action="{{ route('checkout.verifyOtp') }}" method="POST">
        @csrf
        @foreach($selected_products as $productId)
            <input type="hidden" name="selected_products[]" value="{{ $productId }}">
        @endforeach
        <input type="hidden" name="payment_method" value="{{ $payment_method }}">
        <label for="otp" class="fluent-otp-label">OTP Code</label>
        <input id="otp" name="otp" type="text" maxlength="6" required class="fluent-otp-input" autocomplete="one-time-code" value="{{ old('otp') }}">
        @error('otp')
            <div class="fluent-otp-error" role="alert">{{ $message }}</div>
        @enderror
        <button type="submit" class="fluent-otp-btn">Verify & Place Order</button>
    </form>
    <div class="fluent-otp-footer">Need help? Contact support or check your spam folder if you didn't receive the code.</div>
</div>
@endsection
