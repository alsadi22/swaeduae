<form method="POST" action="{{ route('register') }}" class="max-w-xl mx-auto space-y-4 p-6 bg-white/70 rounded-xl shadow">
  @csrf
  <div>
    <label class="block text-sm font-medium mb-1">Full Name</label>
    <input name="name" type="text" class="w-full border rounded px-3 py-2" required>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Email</label>
    <input name="email" type="email" class="w-full border rounded px-3 py-2" required>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Password</label>
      <input name="password" type="password" class="w-full border rounded px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Confirm Password</label>
      <input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" required>
    </div>
  </div>
  <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-black text-white">Create account</button>
</form>
