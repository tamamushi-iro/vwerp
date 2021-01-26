@component('mail::message')
# {{ $mailData['title'] }}

{{ $mailData['message'] }}

@component('mail::button', ['url' => 'http://www.videowaves.in'])
Visit Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent