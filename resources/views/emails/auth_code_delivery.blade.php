@extends('layouts.mails')
@section('content')
    <div style="padding: 3rem; background: aliceblue; color: #064292;">
        <h2>Domain Transfer Auth Code</h2>
        <p>
            Dear {{ $recipientEmail }}, <br> <br>Please find the requested transfer Authorization Code for your domain
            listed below.
            <br>
            The transfer Authorization Code (sometimes referred to as EPP or Authorization Key) is a security key generated
            by the current Registrar and verified through the global registries. <br>
            These codes are updated periodically for security reasons, thus this key will only be valid for a limited time.


            <br><b>Your Authorization Code is:</b> &nbsp; <strong> {{ $authCode }}</strong>
            <br>
            Please use this code in the new registrar's transfer process.
            <br>
            <em>If you did not request this code, contact the current
                owner {{ $owner->name }} at {{ $owner->email }} or registrar immediately.</em>
            <br>
            <strong>Domain</strong>: {{ $domainName['name'] }} <br>
            <strong>Sent to</strong>: {{ $recipientEmail }} <br>
            <strong>Registrar</strong>: {{ $domainName['registrar'] }} at {{ env('EPP_HOST') }} <br>
            <hr>
            Visit our website <a href="https://bluhub.rw">BLUHUB.RW</a>
        </p>

    </div>
@endsection
