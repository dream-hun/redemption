<x-admin-layout>
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.role.title_singular') }}
        </div>

        <div class="card-body">
            <form action="{{ route("admin.roles.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                    <label for="title">{{ trans('cruds.role.fields.title') }}*</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($role) ? $role->title : '') }}" required>
                    @if($errors->has('title'))
                        <p class="help-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.role.fields.title_helper') }}
                    </p>
                </div>
                <div class="form-group {{ $errors->has('permissions') ? 'has-error' : '' }}">
                    <label for="permissions">{{ trans('cruds.role.fields.permissions') }}*
                        <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span></label>
                    <select name="permissions[]" id="permissions" class="form-control select2" multiple="multiple" required>
                        @foreach($permissions as $id => $permissions)
                            <option value="{{ $id }}" {{ (in_array($id, old('permissions', [])) || isset($role) && $role->permissions->contains($id)) ? 'selected' : '' }}>{{ $permissions }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('permissions'))
                        <p class="help-block">
                            {{ $errors->first('permissions') }}
                        </p>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.role.fields.permissions_helper') }}
                    </p>
                </div>
                <div>
                    <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
