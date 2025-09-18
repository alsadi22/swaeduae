{{-- Simple, CSS-hidden trap + render timestamp --}}
<input type="text" name="_hp" value="" autocomplete="off" tabindex="-1"
       style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true">
<input type="hidden" name="_hpt" value="{{ now()->timestamp }}">
