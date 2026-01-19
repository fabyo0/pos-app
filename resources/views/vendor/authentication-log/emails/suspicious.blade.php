@component('mail::message')
# @lang('Hello!')

@lang('We detected suspicious activity on your :app account.', ['app' => config('app.name')])

@if (!empty($suspiciousActivities))
@foreach ($suspiciousActivities as $activity)
- **{{ $activity['type'] === 'multiple_failed_logins' ? __('Multiple Failed Logins') : ($activity['type'] === 'rapid_location_change' ? __('Rapid Location Change') : __('Unusual Login Time')) }}:** {{ $activity['message'] }}
@endforeach
@endif

> **@lang('Account:')** {{ $account->email }}<br/>
> **@lang('Time:')** {{ $time->toDateTimeString() }}<br/>
> **@lang('IP Address:')** {{ $ipAddress }}<br/>
> **@lang('Browser:')** {{ $browser }}<br/>
@if ($location && $location['default'] === false)
> **@lang('Location:')** {{ $location['city'] ?? __('Unknown City') }}, {{ $location['state'] ?? __('Unknown State') }}
@endif

@lang('If this was not you, please secure your account immediately by changing your password.')

@lang('Regards,')<br/>
{{ config('app.name') }}
@endcomponent
