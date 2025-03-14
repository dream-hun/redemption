@extends('layouts.admin')
@section('content')
    @can('domain_pricing_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.domain-pricings.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.contact.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.contact.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Contact">
                    <thead>
                    <tr>

                        <th>
                            {{ trans('cruds.contact.fields.id') }}
                        </th>
                        <th>
                            Contact ID
                        </th>
                        <th>
                            {{ trans('cruds.contact.fields.contact_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.contact.fields.name') }}
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Mobile
                        </th>
                        <th>
                            {{ trans('cruds.contact.fields.organization') }}
                        </th>
                        <th>
                            {{ trans('cruds.contact.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($contacts as $key => $contact)
                        <tr data-entry-id="{{ $contact->id }}">

                            <td>
                                {{ $contact->id ?? '' }}
                            </td>
                            <td>
                                {{ $contact->contact_id ?? '' }}
                            </td>
                            <td>
                                {{ $contact->contact_type ?? '' }}
                            </td>
                            <td>
                                {{ $contact->name ?? '' }}
                            </td>
                            <td>
                                {{ $contact->email ?? '' }}
                            </td>
                            <td>
                                {{ $contact->fax_number ?? '' }}
                            </td>
                            <td>
                                {{ $contact->organization ?? '' }}
                            </td>
                            <td>
                               {{ $contact->created_at ?? '' }}
                            </td>
                            <td>



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
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [[ 1, 'desc' ]],
                pageLength: 100,
            });
            let table = $('.datatable-Contact:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })

    </script>
@endsection
