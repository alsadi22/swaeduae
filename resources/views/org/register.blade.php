<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Organization Registration — SwaedUAE</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial,sans-serif;background:#faf7ef;margin:0}
  .wrap{max-width:800px;margin:4rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
  h1{margin:0 0 1rem}
  label{display:block;margin:.75rem 0 .25rem}
  input,textarea,select{width:100%;padding:.7rem .8rem;border:1px solid #d7d7d7;border-radius:10px}
  textarea{min-height:120px}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
  .btn{display:inline-block;margin-top:1rem;padding:.7rem 1rem;border-radius:10px;border:0;background:#1e66f5;color:#fff;cursor:pointer}
  .error{background:#fff2f0;color:#b02121;padding:.6rem .8rem;border-radius:10px;margin:.5rem 0}
</style></head><body>
  <div class="wrap">
    <h1>Organization Registration</h1>

    @if ($errors->any())
      <div class="error">
        @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('org.register.submit') }}">
      @csrf
      <label>Organization Name *</label>
      <input type="text" name="org_name" value="{{ old('org_name') }}" required>

      <div class="grid">
        <div>
          <label>Contact Name *</label>
          <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div>
          <label>Business Email *</label>
          <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
      </div>

      <div class="grid">
        <div>
          <label>Password *</label>
          <input type="password" name="password" required>
        </div>
        <div>
          <label>Confirm Password *</label>
          <input type="password" name="password_confirmation" required>
        </div>
      </div>

      <div class="grid">
        <div>
          <label>Emirate</label>
          <select name="emirate">
            <option value="">– Select –</option>
            @foreach (['Abu Dhabi','Dubai','Sharjah','Ajman','Umm Al Quwain','Ras Al Khaimah','Fujairah'] as $e)
              <option value="{{ $e }}" @selected(old('emirate')===$e)>{{ $e }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label>Phone</label>
          <input type="text" name="phone" value="{{ old('phone') }}">
        </div>
      </div>

      <label>Website</label>
      <input type="text" name="website" value="{{ old('website') }}">

      <label>About</label>
      <textarea name="about">{{ old('about') }}</textarea>

      <button class="btn" type="submit">Submit for Approval</button>
    </form>
  </div>
</body></html>
