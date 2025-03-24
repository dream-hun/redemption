@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-6">

        </div>
        <div class="col-md-6">

        </div>
    </div>


    <!-- Domain Renewal History -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Renewal History</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Renewal Date</th>
                            <th>Period</th>
                            <th>Old Expiry Date</th>
                            <th>New Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($domain->last_renewal_at)
                            <tr>
                                <td>{{ $domain->last_renewal_at}}</td>
                                <td>{{ $domain->registration_period }} {{ Str::plural('Year', $domain->registration_period) }}</td>
                                <td>{{ $domain->last_renewal_at }}</td>
                                <td>{{ $domain->expires_at ? $domain->expires_at : 'Not set' }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" class="text-center">No renewal history available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
