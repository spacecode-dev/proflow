@component('mail::message')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<h2 class="title"> Join {{ $name }} on ProFlow! </h2>

Once you create your account, you'll be the newest person of the {{ $companyName }} workspace on ProFlow

@component('mail::button', ['url' => $singUpUrl])
Sign Up
@endcomponent


Regards, <br/>
{{ config('app.name') }} Team.


{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
