@component('mail::message')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot




{{-- Body --}}
<div class="pf-title text-center mb-50">
    <span class="font-weight-bold"> {{ ucfirst($title) }}<br/></span>
    @if(isset($subTitle)) <span>{{ $subTitle}}</span> <br/> @endif 
    @if(isset($thirdTitle)) <span class="font-weight-bold">Updating UX Processes</span> <br/> @endif
</div>
@if(isset($subLine1))
<p class="text-center mb-0">
{{$subLine1}}
</p>
@endif

@if(isset($subLine2))
<div class="text-center">
    <a href="{{$url}}" class="no-underline"> {{$subLine2}}</a>
</div>
@endif

@if(isset($commentSubLine))
<p class="text-center font-weight-bold">“@jack Please review and let me know your thoughts”</p>
@endif


@if(isset($subLine3))
<p class="text-center font-weight-bold"> {{ $subLine3 }}</p>
@endif

<div class="btn-center">
    @component('mail::button', ['url' => $url])
    {{ $buttonText}}
    @endcomponent
</div>


@if(isset($footerLine1))
<p class="text-center mb-50">
   {{$footerLine1}}
</p>
@endif
@if(isset($footerLine2))
<p class="text-center mb-50">
    Once finished, you can mark it as complete, propose additional <br/> next steps and comment on the issue.
</p>
@endif

<div class="divider mb-25"></div>



<p class="pf-footer">
If you have any questions, simply reply to this email. We'd love to help.
</p>
<br/>

<p class="pf-footer">
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
