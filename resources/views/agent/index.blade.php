<!doctype html><html lang="en"><meta charset="utf-8">
<title>Agent Report</title>
<style>body{font:14px/1.45 system-ui,Segoe UI,Arial;margin:20px}code,pre{background:#f6f8fa;padding:8px 10px;border-radius:8px}</style>
<h1>Agent Report</h1>
<p>env: <strong>{{ config('app.env') }}</strong></p>
<p>Generated at: <strong>{{ data_get($data,'meta.finished_at','n/a') }}</strong></p>
<h2>Issues</h2><pre>@json($data['issues'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)</pre>
<h2>Fixes</h2><pre>@json($data['fixes'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)</pre>
<h2>Smoke</h2><pre>@json($data['smoke'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)</pre>
