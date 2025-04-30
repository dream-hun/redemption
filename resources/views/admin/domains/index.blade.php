@extends('layouts.admin')

@section('page-title')
    {{ trans('cruds.domain.title_singular') }} {{ trans('global.list') }}
@endsection

@section('content')
    @if (session('message'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('domains.index') }}">
                Register Domain
            </a>
            <!-- Add Transfer Domain Button -->
            <a href="{{ route('transfer.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-right-circle"></i> Transfer IN a Domain
            </a>


        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.domain.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Domain">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.domain.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.domain.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.domain.fields.status') }}
                            </th>
                            <th>
                                {{ trans('cruds.domain.fields.registered_at') }}
                            </th>
                            <th>
                                {{ trans('cruds.domain.fields.expires_at') }}
                            </th>

                            <th>
                                {{ trans('cruds.domain.fields.owner') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($domains as $key => $domain)
                            <tr data-entry-id="{{ $domain->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $domain->id ?? '' }}
                                </td>
                                <td>
                                    {{ $domain->name ?? '' }}
                                </td>
                                <td>
                                    {{ $domain->status ?? '' }}
                                </td>
                                <td>
                                    {{ $domain->registered_at ?? '' }}
                                </td>
                                <td>
                                    {{ $domain->expires_at ?? '' }}
                                </td>

                                <td>
                                    {{ $domain->owner->name ?? '' }}
                                </td>
                                <td>
                                    @can('domain_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.domains.edit', $domain->uuid) }}">
                                            <i class="bi bi-pencil"></i>
                                            Manage Domain
                                        </a>
                                    @endcan
                                    {{-- <a href="{{ route('domains.auth_code.generate', $domain) }}"
                                        class="btn btn-xs btn-primary">Transfer this domain</a> <br> --}}
                                    <a href="{{ route('domains.transfer.invitation', $domain) }}"
                                        class="btn btn-xs btn-primary">Change Owner</a>

                                    @can('domain_delete')
                                        @if ($domain->owner_id === auth()->id())
                                            <form action="{{ route('admin.domains.destroy', $domain->uuid) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this domain? This action cannot be undone and will remove the domain from the registry.');"
                                                style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-xs btn-danger">
                                                    <i class="bi bi-trash"></i> {{ trans('global.delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)


            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Domain:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
