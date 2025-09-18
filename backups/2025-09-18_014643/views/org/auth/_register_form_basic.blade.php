<form method="POST" action="{{ route('org.register.submit') }}" class="max-w-2xl mx-auto space-y-4 p-6 bg-white rounded-xl shadow">
  @csrf
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="block text-sm mb-1">Organization Name</label><input name="organization_name" type="text" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Trade License / Reg. No.</label><input name="registration_number" type="text" class="w-full border rounded px-3 py-2"></div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="block text-sm mb-1">Contact Email</label><input name="email" type="email" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Contact Phone</label><input name="phone" type="tel" class="w-full border rounded px-3 py-2"></div>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label class="block text-sm mb-1">Password</label><input name="password" type="password" class="w-full border rounded px-3 py-2" required></div>
    <div><label class="block text-sm mb-1">Confirm Password</label><input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" required></div>
  </div>
  <button type="submit" class="px-4 py-2 rounded bg-black text-white">Register Organization</button>
</form>
