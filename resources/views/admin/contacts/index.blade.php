<x-admin-layout>
    @can('domain_pricing_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.contacts.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.contact.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

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
                                {{ $contact->name ?? '' }}
                            </td>
                            <td>
                                {{ $contact->email ?? '' }}
                            </td>
                            <td>
                                {{ $contact->voice ?? '' }}
                            </td>
                            <td>
                                {{ $contact->organization ?? '' }}
                            </td>
                            <td>
                               {{ $contact->created_at ?? '' }}
                            </td>
                            <td>
                                @can('contact_edit')
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.contacts.edit', $contact) }}">
                                        <i class="bi bi-pencil"></i> {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('contact_delete')
                                    <form action="{{ route('admin.contacts.destroy', $contact->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> {{ trans('global.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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


</x-admin-layout>

