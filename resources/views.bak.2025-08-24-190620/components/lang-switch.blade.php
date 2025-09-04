@php $cur = app()->getLocale(); @endphp
<div class="lang-switch">
  <a href="{{ route('locale.switch','ar') }}" @class(['fw-bold'=> $cur==='ar'])>العربية</a> |
  <a href="{{ route('locale.switch','en') }}" @class(['fw-bold'=> $cur==='en'])>English</a>
</div>
