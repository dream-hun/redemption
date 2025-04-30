@extends('layouts.mails')
@section('content')
    <div style="padding: 3rem; background: aliceblue; color: #064292;">
        <h2> Transfer Invitation Sent</h2>
        <p>

            Dear {{ $invitation->sender->name }},
            <br>
            You have successfully sent a transfer invitation for the domain <b>{{ $domainName }}</b> to
            <b>{{ $recipientEmail }}</b>.
            <br>
            <br>
            <strong>Domain</strong>: {{ $domainName }} <br>
            <strong>Auth Code</strong>: {{ $authCode }} <br>
            <strong>Sent to</strong>: {{ $recipientEmail }} <br>
            <strong>Registrar</strong>:  {{ env('EPP_HOST') }} <br>

            The recipient has been notified and can accept the transfer using the provided auth code. You will be notified
            once the transfer is completed.

            Please contact our support team at <a href="{{ env('APP_URL') }}/support">{{ env('APP_URL') }}/support</a> if
            you have any questions. 
            <br>
            Thank you.
            <br>
            Sincerely, <br>
            Team {{ env('APP_NAME') }} <br>
            <a href="{{ env('APP_URL') }}">{{ env('APP_URL') }}</a> <br> <br>

            ----------------------------------------------------------------------
        </p>
    </div>
@endsection
