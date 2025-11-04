<div id="updatePasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative">
        <button onclick="closePasswordModal()" class="absolute top-2 right-2 text-gray-400 hover:text-red-600 text-2xl font-bold">&times;</button>
        <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center gap-2"><i class="bi bi-key"></i> Update Password</h2>
        <form method="POST" action="{{ route('manageadmins.password.update', $admin->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="password" class="block text-green-900 font-semibold mb-1">New Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg focus:ring-green-500 focus:border-green-500" required minlength="6">
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block text-green-900 font-semibold mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:ring-green-500 focus:border-green-500" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Update Password</button>
        </form>
    </div>
</div>
<script>
function openPasswordModal() {
    document.getElementById('updatePasswordModal').classList.remove('hidden');
}
function closePasswordModal() {
    document.getElementById('updatePasswordModal').classList.add('hidden');
}
</script>
