@php
    $u = auth()->user();
    $avatarUrl = $u && $u->avatar_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($u->avatar_path)
        : asset('images/avatar-placeholder.svg');
@endphp

<div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
  <img src="{{ $avatarUrl }}" alt="Avatar"
       style="width:72px;height:72px;border-radius:9999px;object-fit:cover;border:1px solid #e5e7eb;">
  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    <form id="avatar-upload-form" action="{{ route('profile.avatar.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <input id="avatar-file" name="avatar" type="file" accept="image/*" style="display:none"
             onchange="document.getElementById('avatar-upload-form').submit();">
      <button type="button" onclick="document.getElementById('avatar-file').click()" class="btn btn-primary">
        Change Photo
      </button>
    </form>

    <form action="{{ route('profile.avatar.destroy') }}" method="POST"
          onsubmit="return confirm('Remove your photo?');" style="display:inline">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-outline-danger">Remove</button>
    </form>
  </div>
</div>
