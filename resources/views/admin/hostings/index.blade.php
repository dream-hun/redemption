<x-admin-layout>
    @section('page-title')
        Hosting Plans
    @endsection
    @can('hosting_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.hostings.create') }}">
                    {{ trans('global.add') }} Hosting plan
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            Hosting Plan List
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Hosting">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            Category
                        </th>
                        <th>
                            Name
                        </th>
                        <th>Price</th>
                        <th>Satus</th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($hostings as $key => $host)
                        <tr data-entry-id="{{ $host->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $host->category->name ?? '' }}
                            </td>
                            <td>
                                {{ $host->name ?? '' }}
                            </td>
                            <td>
                                {{ $host->formattedPricing() ?? '' }}
                            </td>
                            <td>
                                {{ $host->status ?? '' }}
                            </td>

                            <td>


                                @can('hosting_edit')
                                    <a class="btn  btn-info"
                                       href="{{ route('admin.hostings.edit', $host->id) }}">
                                        <span class="bi bi-pencil"></span>
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('hosting_delete')
                                    <form action="{{ route('admin.hostings.destroy', $host->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <button type="submit" class="btn btn-danger"
                                                value="{{ trans('global.delete') }}"><span class="bi bi-trash"></span>
                                            Delete
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
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 100,
                });
                $('.datatable-Hosting:not(.ajaxTable)').DataTable({
                    buttons: dtButtons
                })
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    $($.fn.dataTable.tables(true)).DataTable()
                        .columns.adjust();
                });
            })
        </script>
    @endsection

</x-admin-layout>




