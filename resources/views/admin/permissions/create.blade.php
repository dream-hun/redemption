<x-admin-layout>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.permission.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("admin.permissions.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                    <label for="title">{{ trans('cruds.permission.fields.title') }}*</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($permission) ? $permission->title : '') }}" required>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.permission.fields.title_helper') }}
                    </p>
                </div>
                <div>
                    <input class="btn btn-primary" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
