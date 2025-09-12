<form method="get" class="row g-2 align-items-end mb-3">
  <div class="col-md-4">
    <label class="form-label">Search</label>
    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Type to search...">
  </div>
  <div class="col-md-3">
    <label class="form-label">Sort</label>
    <select name="sort" class="form-select">
      <option value="created_at" @selected(request('sort')=='created_at')>Created</option>
      <option value="name" @selected(request('sort')=='name')>Name/Title</option>
      <option value="email" @selected(request('sort')=='email')>Email (when applicable)</option>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Direction</label>
    <select name="dir" class="form-select">
      <option value="desc" @selected(request('dir')=='desc')>Desc</option>
      <option value="asc" @selected(request('dir')=='asc')>Asc</option>
    </select>
  </div>
  <div class="col-md-2 d-flex gap-2">
    <button class="btn btn-primary w-100">Filter</button>
    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>
