@extends('layouts.mails')
@section('content')
    <div style="padding: 3rem; background: aliceblue; color: #064292;">
        <h2>Domain Transfer invitation </h2>
        <p>

            {{ env('APP_NAME') }} <br>
            Notice of Domain Ownership Change Invitation <br>
            Date: {{ date('Y-M-d') }} <br>
            ----------------------------------------------------------------------
            <br><br>
            Dear {{ $recipientEmail }}, Hi there, <br>
            <br>
            This notice is to inform you that the owner of the following domain(s) would like to invite you to take
            ownership of the domain(s): <br>
            <br>
            Domain Name: <strong> {{ $domainName }}</strong> <br>
            {{-- Authorization Code: <strong> {{ $authCode }} </strong> <br> --}}

            Click this link to accept the invitation: {{ $acceptUrl }}
            <br>
            Please contact our support team at <a href="{{ env('APP_URL') }}/support">{{ env('APP_URL') }}/support</a> if you
            have any questions.

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
