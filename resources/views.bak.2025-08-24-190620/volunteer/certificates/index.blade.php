@extends('layouts.app')

@section('title', 'My Certificates')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">My Certificates</h1>

    @if($certs->count() === 0)
        <div class="alert alert-info">No certificates yet.</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Code</th>
                        <th>Issued</th>
                        <th>Opportunity</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($certs as $c)
                        @php
                            $code = $c->code ?? $c->verification_code ?? null;
                            $fileUrl = null;
                            if ($c->file_path) {
                                $fileUrl = filter_var($c->file_path, FILTER_VALIDATE_URL)
                                    ? $c->file_path
                                    : url($c->file_path);
                            }
                        @endphp
                        <tr>
                            <td class="fw-medium">{{ $c->title ?? 'Certificate' }}</td>
                            <td>
                                @if($code)
                                    <code>{{ $code }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $c->issued_at ?? $c->issued_date ?? '—' }}</td>
                            <td>{{ optional($c->opportunity)->title ?? '—' }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    @if($fileUrl)
                                        <a class="btn btn-sm btn-outline-primary" href="{{ $fileUrl }}" target="_blank" rel="noopener">PDF</a>
                                    @endif
                                    @if($code)
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('qr.verify', ['code' => $code]) }}">Verify</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $certs->links() }}
    @endif
</div>
@endsection
