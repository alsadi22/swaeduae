@php
  // accept $rows or $paginator; gracefully handle collections/arrays
  $paginator = $paginator ?? null;
  if (!$paginator && isset($rows) && $rows instanceof \Illuminate\Contracts\Pagination\Paginator) {
    $paginator = $rows;
  }
  if ($paginator) {
    $data = method_exists($paginator,'items') ? $paginator->items() : $paginator->toArray()['data'] ?? [];
  } else {
    $data = $rows ?? $items ?? $list ?? $data ?? $users ?? $organizations ?? $opportunities ?? $applicants ?? $certificates ?? [];
    if ($data instanceof \Illuminate\Contracts\Support\Arrayable) $data = $data->toArray();
    if ($data instanceof \Illuminate\Support\Collection) $data = $data->all();
    if (!is_iterable($data)) $data = [];
  }
@endphp

<form method="GET" class="mb-3">
  <div class="input-group">
    <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search...">
    <button class="btn btn-outline-secondary">Search</button>
  </div>
</form>

@if(empty($data))
  <div class="alert alert-info">No records yet.</div>
@else
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          @php $first = (array) reset($data); @endphp
          @foreach(array_keys($first) as $key)
            <th class="text-nowrap">{{ $key }}</th>
          @endforeach
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data as $row)
          @php $r = (array) $row; @endphp
          @if(request('q'))
            @php
              $match=false;
              foreach($r as $v){ if(is_scalar($v) && stripos((string)$v, request('q'))!==false){ $match=true; break; } }
              if(!$match) continue;
            @endphp
          @endif
          <tr>
            @foreach($r as $v)
              <td class="text-truncate" style="max-width:280px">{{ is_scalar($v) ? $v : json_encode($v) }}</td>
            @endforeach
            <td class="text-end">
              @yield('row-actions', '')
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($paginator)
    <div class="mt-3">{{ $paginator->withQueryString()->links() }}</div>
  @endif
@endif
