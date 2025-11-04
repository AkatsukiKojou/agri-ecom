@extends('user.layout')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #e8f5e9 0%, #f6fef6 100%);
    }
    .success-card {
        max-width: 400px;
        margin: 60px auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(44,120,44,0.10);
        padding: 40px 32px 32px 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .checkmark {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #e8f5e9;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        position: relative;
    }
    .checkmark svg {
        width: 48px;
        height: 48px;
        stroke: #43a047;
        stroke-width: 4;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
        stroke-dasharray: 60;
        stroke-dashoffset: 60;
        animation: dash 0.7s ease forwards;
    }
    @keyframes dash {
        to { stroke-dashoffset: 0; }
    }
    .success-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 10px;
        text-align: center;
    }
    .success-desc {
        color: #388e3c;
        font-size: 1.08rem;
        text-align: center;
        margin-bottom: 18px;
    }
    .success-btn {
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
        width: 100%;
        margin-top: 18px;
    }
    .success-btn:hover {
        background: linear-gradient(90deg, #388e3c 0%, #43a047 100%);
    }
</style>
<div class="success-card">
    <div class="checkmark">
        <svg viewBox="0 0 52 52">
            <polyline points="14,28 22,36 38,18" />
        </svg>
    </div>
    <div class="success-title">Order Successfully Placed!</div>
    <div class="success-desc">Thank you for your purchase. Your order has been placed and is now being processed.</div>
    <div style="color:#388e3c; font-size:1rem; margin-bottom:10px; text-align:center;">You will be redirected to your orders in <span id="countdown">5</span> seconds...</div>
    </div>
<script>
    let seconds = 5;
    const countdown = document.getElementById('countdown');
    const interval = setInterval(() => {
        seconds--;
        countdown.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "{{ route('user.orders') }}";
        }
    }, 1000);
</script>
@endsection
