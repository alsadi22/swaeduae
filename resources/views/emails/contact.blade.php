<p>You have received a new contact form submission:</p>
<ul>
  @foreach($data as $k => $v)
    <li><strong>{{ $k }}</strong>: {{ $v }}</li>
  @endforeach
</ul>
