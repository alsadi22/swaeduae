@extends("public.layout")
@section("title","Opportunities · SwaedUAE")
@section("content")
<section class="page-hero">
  <div class="container">
    <h1>Opportunities</h1>
    <p class="sub">Find activities by emirate, type, interest, skill, language, gender, age, and status.</p>
  </div>
</section>

<section class="container filters">
  <form class="filters-grid" method="get" action="/opportunities">
    <label>Emirate
      <select name="emirate">
        <option value="">Any</option>
        <option>Abu Dhabi</option><option>Ajman</option><option>Dubai</option>
        <option>Fujairah</option><option>Ras Al Khaimah</option>
        <option>Sharjah</option><option>Umm Al Quwain</option>
      </select>
    </label>
    <label>Type
      <select name="type">
        <option value="">Any</option><option>Event</option><option>Program</option>
      </select>
    </label>
    <label>Interest
      <select name="interest"><option value="">Any</option></select>
    </label>
    <label>Skill
      <select name="skill"><option value="">Any</option></select>
    </label>
    <label>Language
      <select name="lang">
        <option value="">Any</option><option>Arabic</option>
        <option>English</option><option>Arabic & English</option>
      </select>
    </label>
    <label>Gender
      <select name="gender"><option value="">Any</option><option>Male</option><option>Female</option></select>
    </label>
    <label>Age
      <input type="number" min="0" name="age" placeholder="Any">
    </label>
    <label>Status
      <select name="status"><option value="">Any</option><option>Open</option><option>Full</option></select>
    </label>
    <div class="filters-actions">
      <button class="btn" type="submit">Apply</button>
      <a class="btn outline" href="/opportunities">Reset</a>
    </div>
  </form>

  <div class="muted" style="margin-top:12px">
    Listing grid will render here (next PR wires DB results).
  </div>
</section>
@endsection
