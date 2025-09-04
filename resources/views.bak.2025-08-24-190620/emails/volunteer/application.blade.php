@extends('layouts.app')
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.application_email_subject') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; padding: 20px; border: 1px solid #ddd;">
        <h2 style="color: #0d6efd;">{{ __('messages.application_email_greeting') }}</h2>
        <p>{{ __('messages.application_email_body', ['event' => $application->event->title]) }}</p>
        <a href="{{ route('public.opportunities.show', $application->event->id) }}"
           style="display: inline-block; padding: 10px 15px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px;">
           {{ __('messages.view_event') }}
        </a>
        <p style="margin-top: 20px;">{{ __('messages.thank_you') }}<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
