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
        <a href="{{ route('admin.domains.transfer.index', $domain->uuid) }}" class="btn btn-primary" style="display: inline-block;">
            <i class="fas fa-exchange-alt"></i> Transfer Domain
        </a>

        <form action="{{ route('admin.domains.transfer.get-auth-code') }}" method="POST" style="display: inline-block;">
            @csrf
            <input type="hidden" name="uuid" value="{{ $domain->uuid }}" />
            <button type="submit" class="btn btn-info">
                <i class="fas fa-key"></i> Get Auth Code
            </button>
        </form>

    </div>
    <div class="modal fade" id="authCodeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Domain Auth Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Auth Code:</strong> <span id="authCodeValue">{{ session('auth_code') }}</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if (session('auth_code'))
        <script>
            $(document).ready(function() {
                $('#authCodeModal').modal('show');
            });
        </script>
    @endif
</div>
