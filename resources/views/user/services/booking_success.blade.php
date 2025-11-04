@extends('user.layout')

@section('content')
<style>
    .booking-success-container {
        min-height: 60vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e8f5e9 0%, #f6fef6 100%);
    }
    .booking-success-icon {
        width: 90px;
        height: 90px;
        margin-bottom: 24px;
        animation: pop 0.7s cubic-bezier(.17,.67,.83,.67);
    }
    @keyframes pop {
        0% { transform: scale(0.5); opacity: 0; }
        70% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); }
    }
    .booking-success-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 10px;
        text-align: center;
    }
    .booking-success-message {
        font-size: 1.15rem;
        color: #388e3c;
        margin-bottom: 18px;
        text-align: center;
    }
    .booking-success-redirect {
        font-size: 1rem;
        color: #888;
        margin-top: 18px;
        text-align: center;
    }
</style>
<div class="booking-success-container">
    <svg class="booking-success-icon" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="32" fill="#43a047"/><path d="M18 34l8 8 20-20" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <div class="booking-success-title">Booking Successful!</div>
    <div class="booking-success-message">Your training service booking has been placed.<br>Thank you for booking with us!</div>
    <div class="booking-success-redirect">Redirecting to your bookings...<br><span id="countdown">5</span> seconds</div>
</div>
<script>
    let seconds = 5;
    const countdown = document.getElementById('countdown');
    const redirectUrl = "{{ $redirectUrl ?? route('bookings.index') }}";
    setInterval(function() {
        seconds--;
        if (seconds <= 0) {
            window.location.href = redirectUrl;
        } else {
            countdown.textContent = seconds;
        }
    }, 1000);
</script>
@endsection
