@component('mail::message')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url') ])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<h2 class="title mb-50"> Welcome! </h2>

Thank you for signing up ProFlow.

Please verify your email address to complete the registration.

@component('mail::button', ['url' => $verifyUrl])
Verify Email
@endcomponent

<br/>
<p class="pf-footer text-light">
If you did not create an account, no further action is required. 
</p>

<p class="pf-footer text-light">
Regards, <br/>
{{ config('app.name') }} Team.
</p>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
