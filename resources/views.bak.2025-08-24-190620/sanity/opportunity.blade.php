<!doctype html><html lang="{{ app()->getLocale() ?? 'en' }}"><meta charset="utf-8">
<title>Sanity Opportunity</title><meta name="viewport" content="width=device-width,initial-scale=1">
<body style="font-family:system-ui,Arial,sans-serif;padding:2rem">
<h1>Sanity: Opportunity #{{ $opportunity->id ?? '—' }}</h1>
<p><strong>Title:</strong> {{ $opportunity->title ?? 'Untitled' }}</p>
<p><strong>Summary:</strong> {{ $opportunity->summary ?? '—' }}</p>
<hr><small>Rendered {{ now() }}</small>
</body></html>
