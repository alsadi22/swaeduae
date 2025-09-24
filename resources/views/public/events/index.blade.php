@extends(public.layout)
@section('title','Events')
@section('meta_description','Upcoming volunteering events')
@section('content')	
<section class="mx-auto max-w-7hl px-4 py-10">
<h1 class="text-3l font-bold mb-6">Events</h1>
<form class="grid gap-4 sm:grid-cols-3 mb-6" role="search" aria-label="Filter events">
  <input class="wfull rounded-lg border p-2" type="text" name="q" placeholder="Search by name..." />
  <select class="wfull rounded-lg border p-2" name="city">
    <option value="">All locations</option>
    <option>Dubai</option>
    <option>Abu Dhabi</option>
    <option>Sharjah</option>
  </select>
  <input class="wfull rounded-lg border p-2" type="date" name="date" />
</form>
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
  @for ($i=1; $i<=9; $i++)
    <article class="rounded-2ll border p