@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang("We're Sorry, Something Went Wrong")
@else
# @lang('Greetings!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
Regards,<br>
IAPHS
{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:displayableActionUrl](:actionURL)',
    [
        'actionText' => $actionText,
        'actionURL' => $actionUrl,
        'displayableActionUrl' => $displayableActionUrl,
    ]
)
@endslot
@endisset
@endcomponent
