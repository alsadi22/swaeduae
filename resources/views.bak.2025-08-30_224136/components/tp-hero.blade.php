<section class="position-relative" style="min-height:52vh;background:linear-gradient(180deg,#0b3a4a 0%,#0e5a6f 100%);">
  <div class="container position-relative" style="z-index:2;padding:6rem 1rem 3rem;">
    <h1 class="display-4 fw-bold text-white text-center mb-3">{{ __("Find Volunteer Opportunities in the UAE") }}</h1>
    <p class="lead text-white-50 text-center mb-4">{{ __("Join events, track your hours, and earn verified certificates.") }}</p>

    <!-- Search rail -->
    <form class="bg-white shadow-lg rounded-4 p-3 mx-auto" style="max-width:980px">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
          <input type="text" class="form-control form-control-lg" placeholder="{{ __("Where? (City / Emirate)") }}">
        </div>
        <div class="col-6 col-md-3">
          <select class="form-select form-select-lg js-select2">
            <option selected>{{ __("Category") }}</option>
            <option>{{ __("Environment") }}</option>
            <option>{{ __("Education") }}</option>
            <option>{{ __("Health") }}</option>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <select class="form-select form-select-lg js-select2">
            <option selected>{{ __("When") }}</option>
            <option>{{ __("This Week") }}</option>
            <option>{{ __("This Month") }}</option>
            <option>{{ __("Flexible") }}</option>
          </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary btn-lg" type="button">{{ __("Find Now") }}</button>
        </div>
      </div>
    </form>
  </div>

  <div class="position-absolute bottom-0 start-0 end-0" style="height:64px;background:linear-gradient(180deg,transparent,rgba(255,255,255,.9) 60%,#fff 100%);"></div>
</section>

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (window.jQuery && jQuery().select2) {
      jQuery(".js-select2").select2({ minimumResultsForSearch: 8, width: "100%" });
    }
  });
</script>
@endpush
