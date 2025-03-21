@extends('layouts.admin')
@section('content')

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
                            {{ trans('cruds.domain.fields.auto_renew') }}
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
                    @foreach($domains as $key => $domain)
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
                                {{ App\Models\Domain::STATUS_SELECT[$domain->status] ?? '' }}
                            </td>
                            <td>
                                {{ $domain->registered_at ?? '' }}
                            </td>
                            <td>
                                {{ $domain->expires_at ?? '' }}
                            </td>
                            <td>
                                <span style="display:none">{{ $domain->auto_renew ?? '' }}</span>
                                <input type="checkbox" disabled="disabled" {{ $domain->auto_renew ? 'checked' : '' }}>
                            </td>
                            <td>
                                {{ $domain->owner->name ?? '' }}
                            </td>
                            <td>
                                @can('domain_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.domains.show', $domain->uuid) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('domain_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.domains.edit', $domain->uuid) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('domain_delete')
                                    <form action="{{ route('admin.domains.destroy', $domain->uuid) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
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
  let table = $('.datatable-Domain:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

})

</script>
@endsection
