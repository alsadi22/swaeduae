<footer class="py-4 mt-5" style="background:#0f172a;color:#cbd5e1">
  <div class="container d-flex justify-content-between">
    <div>&copy; {{ date("Y") }} {{ config("app.name","SwaedUAE") }}</div>
    <div class="d-flex gap-3">
      <a class="text-decoration-none" style="color:#cbd5e1" href="/privacy">{{ __("Privacy") }}</a>
      <a class="text-decoration-none" style="color:#cbd5e1" href="/terms">{{ __("Terms") }}</a>
    </div>
  </div>
</footer>
