@component('mail::message')
    
    <h3>Please use this  code to reset password</h3>
    @component('mail::panel')
    {{$code}}
    @endcomponent
 <p>This code is always valid only one hour</p>
@endcomponent