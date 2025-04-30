@extends('layouts.mails')
@section('content')
    <div style="padding: 3rem; background: aliceblue; color: #064292;">
        <h2>Domain Transfer Auth Code</h2>
        <p>
            Dear {{ $recipientEmail }}, You have been sent the auth code for the domain
            <b>{{ $domainName['name'] }}</b> to initiate a transfer to a new registrar.
            <br><b>Auth Code:</b> &nbsp; <strong> {{ $authCode }}</strong>
            <br>
            Please use this code in the new registrar's transfer process.
            <br>
            <em>If you did not request this code, contact the current
                owner {{$owner->name}} at {{$owner->email}} or registrar immediately.</em>
            <br>
            <strong>Domain</strong>: {{ $domainName['name'] }} <br>
            <strong>Sent to</strong>: {{ $recipientEmail }} <br>
            <strong>Registrar</strong>: {{ $domainName['registrar'] }} at {{ env('EPP_HOST') }} <br>
            <hr>
            Visit our website <a href="https://bluhub.rw">BLUHUB.RW</a>
        </p>

    </div>
@endsection
