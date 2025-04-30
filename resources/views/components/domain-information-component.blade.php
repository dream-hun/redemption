<div class="card card-primary card-outline">
    <div class="card-header">
        <div class="card-title">Domain Information</div>
    </div>
    <div class="card-body box-profile">
        <h3 class="profile-username text-center">{{ $domain->name }}</h3>
        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
                <b>Registered At</b> <a
                    class="float-right">{{ isset($eppInfo['crDate']) ? \Carbon\Carbon::parse($eppInfo['crDate'])->format('Y-m-d') : 'N/A' }}</a>
            </li>
            <li class="list-group-item">
                <b>Expires At</b> <a
                    class="float-right text-warning">{{ isset($eppInfo['exDate']) ? \Carbon\Carbon::parse($eppInfo['exDate'])->format('Y-m-d') : 'N/A' }}</a>
            </li>
            <li class="list-group-item">
                <b>Status</b>
                <a class="float-right">
                    @if (!empty($eppInfo['status']))
                        @foreach ($eppInfo['status'] as $status)
                            <span class="badge badge-success">Active</span>
                        @endforeach
                    @else
                        N/A
                    @endif
                </a>
            </li>
            <li class="list-group-item">
                <b>Last Renewal </b> <a
                    class="float-right text-info">{{ isset($eppInfo['upDate']) ? \Carbon\Carbon::parse($eppInfo['upDate'])->format('Y-m-d') : 'N/A' }}</a>
            </li>
        </ul>
    </div>

    <div class="card-footer">
        <form action="{{ route('admin.domains.renewal.addToCart', $domain->uuid) }}" method="POST"
            style="display: inline-block">
            @csrf
            <input type="hidden" name="period" value="1">
            <button type="submit" class="btn btn-success">Renew domain</button>
        </form>

        <a href="{{ route('domains.auth_code.generate', $domain) }}" class="btn btn-sm btn-primary">Get Domain's Auth Code</a>

    </div>
   
</div>
