@extends('layouts.app')

@section('title', __('My Certificates'))

@section('content')
<div class="container py-4">
    <h1 class="mb-3">{{ __('My Certificates') }} / {{ __('شهاداتي') }}</h1>
    @if($certs->count() === 0)
        <div class="alert alert-info">{{ __('No certificates yet.') }} / {{ __('لا توجد شهادات بعد.') }}</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Issued Date') }} / {{ __('تاريخ الإصدار') }}</th>
                        <th>{{ __('Opportunity') }} / {{ __('الفرصة') }}</th>
                        <th>{{ __('Code') }} / {{ __('الرمز') }}</th>
                        <th class="text-end">{{ __('Actions') }} / {{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($certs as $c)
                    <tr>
                        <td>{{ $c->issued_date ? \Illuminate\Support\Carbon::parse($c->issued_date)->format('Y-m-d') : ($c->issued_at? $c->issued_at->format('Y-m-d'): '-') }}</td>
                        <td>{{ optional($c->opportunity)->title ?? __('(Untitled)') }}</td>
                        <td><code>{{ $c->code ?? $c->verification_code }}</code></td>
                        <td class="text-end">
                            @if($c->file_path)
                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ url($c->file_path) }}">{{ __('Download') }} / {{ __('تحميل') }}</a>
                            @endif
                            <a class="btn btn-sm btn-outline-success" href="{{ url('/verify/' . urlencode($c->code ?? $c->verification_code)) }}">{{ __('Verify') }} / {{ __('تحقق') }}</a>
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
