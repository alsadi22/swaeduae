<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventInfoRequest;
use Illuminate\Http\Request;

class EventInfoFormController extends Controller
{
    public function edit(Event $event)
    {
        $infoRequest = EventInfoRequest::firstOrNew(['event_id' => $event->id]);
        return view('admin.events.info_form', compact('event', 'infoRequest'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'form_fields' => 'required|array',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.name' => 'required|string',
            'form_fields.*.type' => 'required|string',
            'form_fields.*.required' => 'boolean',
        ]);

        $infoRequest = EventInfoRequest::updateOrCreate(
            ['event_id' => $event->id],
            ['form_fields' => $request->form_fields]
        );

        return redirect()->route('admin.events.info_form.edit', $event)->with('success', 'Form updated.');
    }
}
