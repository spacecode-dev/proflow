@component('mail::message')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<div class="pf-title text-center mb-50">
    <span class="font-weight-bold">Your assigned next step due is overdue!<br/></span>
    <span>in</span>
    <span class="font-weight-bold">Updating UX Processes</span>
</div>

<p class="text-center mb-0">
    It looks like your team is waiting on you to complete a next step:
</p>

<div class="text-center">
    <a href="#" class="no-underline">Jack to finalize onboarding documents</a>
</div>
<p class="text-center font-weight-bold">“@jack Please review and let me know your thoughts”</p>

<div class="btn-center">
    @component('mail::button', ['url' => $url])
    {{ $buttonText}}
    @endcomponent
</div>

<p class="text-center mb-50">
    Help keep your team moving forward by taking action <br/> and completing your assigned next step.
</p>

<p class="text-center mb-50">
    Once finished, you can mark it as complete, propose additional <br/> next steps and comment on the issue.
</p>

<div class="divider mb-25"></div>

<!-- <h2 class="issue-title mb-0"> {{ ucfirst($title) }}</h2>
@if($subTitle ?? '') {{ $subTitle ?? '' }} <br/> @endif
in {!! $issueTitle !!}

{{$subLine1}}

<a href="{{ $url }}">{!! $subLine2 !!} </a>
@component('mail::button', ['url' => $url])
{{ $buttonText}}
@endcomponent

@switch($footerType)
    @case(1)
    Help keep your team moving forward by taking action now.<br/>
    You can respond, add additional next steps, and comment directly in ProFlow
    @break
    @case(2)
        Second case...
        @break
    @default
        Default case...
@endswitch -->

<p class="pf-footer text-light">
If you have any questions, simply reply to this email. We'd love to help.
</p>
<br/>

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
