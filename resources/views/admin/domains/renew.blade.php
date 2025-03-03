<div class="card">
    <div class="card-header">
        <h3 class="card-title">Renew Domain</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.domains.renew', $domain) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="period">Renewal Period (Years)</label>
                <select name="period" id="period" class="form-control @error('period') is-invalid @enderror">
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ Str::plural('Year', $i) }}</option>
                    @endfor
                </select>
                @error('period')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Current Expiry Date</label>
                <p class="form-control-static">{{ $domain->expires_at ? $domain->expires_at : 'Not set' }}</p>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    Renew Domain
                </button>
            </div>
        </form>
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
                            <td>{{ $domain->last_renewal_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $domain->registration_period }} {{ Str::plural('Year', $domain->registration_period) }}</td>
                            <td>{{ $domain->last_renewal_at->format('Y-m-d') }}</td>
                            <td>{{ $domain->expires_at ? $domain->expires_at->format('Y-m-d') : 'Not set' }}</td>
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
