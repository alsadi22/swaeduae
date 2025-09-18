<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrgOpportunityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /** List opportunities owned by the current organizer */
    public function index()
    {
        $userId = Auth::id();
        $opps = Opportunity::query()
            ->where('organizer_id', $userId)
            ->latest('created_at')
            ->paginate(12);

        return view('org.opps.index', ['opps' => $opps]);
    }

    /** Show create form */
    public function create()
    {
        return view('org.opps.create');
    }

    /** Persist new opportunity owned by current organizer */
    public function store(Request $r)
    {
        $data = $this->validateData($r);

        $opp = new Opportunity();
        $opp->title       = $data['title'];
        $opp->category    = $data['category'] ?? null;
        $opp->city        = $data['city'] ?? null;
        $opp->location    = $data['location'] ?? null;
        $opp->starts_at   = $data['starts_at'] ?? null;
        $opp->ends_at     = $data['ends_at'] ?? null;
        $opp->description = $data['description'] ?? null;

        // Required linkage
        $opp->organizer_id   = Auth::id();

        // Tokens if columns exist in your schema
        if (isset($opp->checkin_token) && empty($opp->checkin_token))  $opp->checkin_token  = Str::random(32);
        if (isset($opp->checkout_token) && empty($opp->checkout_token)) $opp->checkout_token = Str::random(32);

        $opp->save();

        return redirect()->route('org.opps.index')->with('success', __('Opportunity created.'));
    }

    /** Show edit form (own opp only) */
    public function edit(Opportunity $opportunity)
    {
        $this->authorizeOwner($opportunity);
        return view('org.opps.edit', ['op' => $opportunity]);
    }

    /** Update (own opp only) */
    public function update(Request $r, Opportunity $opportunity)
    {
        $this->authorizeOwner($opportunity);
        $data = $this->validateData($r);

        $opportunity->title       = $data['title'];
        $opportunity->category    = $data['category'] ?? null;
        $opportunity->city        = $data['city'] ?? null;
        $opportunity->location    = $data['location'] ?? null;
        $opportunity->starts_at   = $data['starts_at'] ?? null;
        $opportunity->ends_at     = $data['ends_at'] ?? null;
        $opportunity->description = $data['description'] ?? null;
        $opportunity->save();

        return redirect()->route('org.opps.index')->with('success', __('Opportunity updated.'));
    }

    /** Delete (own opp only) */
    public function destroy(Opportunity $opportunity)
    {
        $this->authorizeOwner($opportunity);
        $opportunity->delete();

        return redirect()->route('org.opps.index')->with('success', __('Opportunity deleted.'));
    }

    /** Ensure the logged-in user owns the record */
    private function authorizeOwner(Opportunity $op)
    {
        if ((int)$op->organizer_id !== (int)Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    /** Validation rules (lenient, datetime optional) */
    private function validateData(Request $r): array
    {
        return $r->validate([
            'title'       => ['required','string','max:255'],
            'category'    => ['nullable','string','max:120'],
            'city'        => ['nullable','string','max:120'],
            'location'    => ['nullable','string','max:255'],
            'starts_at'   => ['nullable','date'],
            'ends_at'     => ['nullable','date','after_or_equal:starts_at'],
            'description' => ['nullable','string'],
        ]);
    }
}
