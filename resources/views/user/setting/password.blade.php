@extends('user.layout')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow" x-data="passwordForm()">
    <h2 class="text-xl font-semibold text-green-800 mb-4">Update Password</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="current_password" class="block font-medium">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="w-full p-2 border border-gray-300 rounded">
        </div>

        <div>
            <label for="new_password" class="block font-medium">New Password</label>
            <input type="password" name="new_password" id="new_password" class="w-full p-2 border border-gray-300 rounded"
                x-model="newPassword" @input="checkStrength">

            <div class="mt-1 text-sm" :class="strengthColor">
                Strength: <span x-text="strengthLabel"></span>
            </div>
        </div>

        <div>
            <label for="new_password_confirmation" class="block font-medium">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                class="w-full p-2 border border-gray-300 rounded" x-model="confirmPassword">
            <div class="mt-1 text-sm text-red-500" x-show="newPassword && confirmPassword && newPassword !== confirmPassword">
                Passwords do not match.
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded"
                :disabled="!canSubmit"
                :class="{ 'opacity-50 cursor-not-allowed': !canSubmit }"
            >
                Update Password
            </button>
        </div>
    </form>
</div>

<script>
function passwordForm() {
    return {
        newPassword: '',
        confirmPassword: '',
        strengthLabel: '',
        strengthColor: '',
        get canSubmit() {
            return this.newPassword.length >= 8 &&
                   this.newPassword === this.confirmPassword;
        },
        checkStrength() {
            const len = this.newPassword.length;
            if (len === 0) {
                this.strengthLabel = '';
                this.strengthColor = '';
            } else if (len < 6) {
                this.strengthLabel = 'Weak';
                this.strengthColor = 'text-red-500';
            } else if (len < 10) {
                this.strengthLabel = 'Moderate';
                this.strengthColor = 'text-yellow-500';
            } else {
                this.strengthLabel = 'Strong';
                this.strengthColor = 'text-green-600';
            }
        }
    }
}
</script>
@endsection
