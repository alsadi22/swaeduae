<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index() { return view('teams.index'); }
    public function create() { return view('teams.create'); }
    public function store(Request $request) { return redirect()->route('teams.index'); }
    public function show($id) { return view('teams.show', compact('id')); }
    public function edit($id) { return view('teams.edit', compact('id')); }
    public function update(Request $request, $id) { return redirect()->route('teams.index'); }
    public function destroy($id) { return redirect()->route('teams.index'); }
}
