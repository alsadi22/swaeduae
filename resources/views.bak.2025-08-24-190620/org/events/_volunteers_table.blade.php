<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Volunteer') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th class="text-center">{{ __('Applied') }}</th>
                        <th class="text-center">{{ __('Approved') }}</th>
                        <th class="text-center">{{ __('Attended') }}</th>
                        <th class="text-end">{{ __('Minutes') }}</th>
                        <th class="text-end">{{ __('Update Minutes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->email }}</td>
                            <td class="text-center">@if($r->applied) ✅ @else ❌ @endif</td>
                            <td class="text-center">@if($r->approved) ✅ @else ❌ @endif</td>
                            <td class="text-center">@if($r->attended) ✅ @else ❌ @endif</td>
                            <td class="text-end">{{ (int)($r->minutes ?? 0) }}</td>
                            <td class="text-end">
                                <form method="POST"
                                      action="{{ route('org.attendances.minutes.update', ['attendance' => $r->attendance_id]) }}"
                                      class="d-inline-flex align-items-center gap-2">
                                    @csrf
                                    <input type="number"
                                           name="minutes"
                                           min="0" step="1"
                                           value="{{ (int)($r->minutes ?? 0) }}"
                                           class="form-control form-control-sm"
                                           style="width: 110px;">
                                    <button class="btn btn-sm btn-outline-primary">
                                        {{ __('Save') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted p-4">{{ __('No volunteers found for this event.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
