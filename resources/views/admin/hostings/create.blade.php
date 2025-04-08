@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Add New Hosting Plan</h3>
        </div>
        <div class="card-body">
            <form action="{{route('admin.hostings.store')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Name*</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($hosting) ? $hosting->name : '') }}" required>
                            @if($errors->has('name'))
                                <p class="help-block text-danger">
                                    {{ $errors->first('name') }}
                                </p>
                            @endif
                            <p class="helper-block text-muted small">
                               Ex: Premium Web Hosting
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('slug') ? 'has-error' : '' }}">
                            <label for="slug">Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', isset($hosting) ? $hosting->slug : '') }}">
                            @if($errors->has('slug'))
                                <p class="help-block text-danger">
                                    {{ $errors->first('slug') }}
                                </p>
                            @endif
                            <p class="helper-block text-muted small">
                               Leave empty to auto-generate from name
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('icon') ? 'has-error' : '' }}">
                            <label for="icon">Icon</label>
                            <input type="text" id="icon" name="icon" class="form-control" value="{{ old('icon', isset($hosting) ? $hosting->icon : '') }}" placeholder="fa-server">
                            @if($errors->has('icon'))
                                <p class="help-block text-danger">
                                    {{ $errors->first('icon') }}
                                </p>
                            @endif
                            <p class="helper-block text-muted small">
                               Font Awesome icon class (e.g., fa-server)
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required {{ $errors->has('status') ? 'has-error' : '' }}">
                            <label for="status">Status*</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" {{ old('status', isset($hosting) ? $hosting->status->value : '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', isset($hosting) ? $hosting->status->value : '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @if($errors->has('status'))
                                <p class="help-block text-danger">
                                    {{ $errors->first('status') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required {{ $errors->has('price') ? 'has-error' : '' }}">
                            <label for="price">Price (RWF)*</label>
                            <input type="number" id="price" name="price" class="form-control" value="{{ old('price', isset($hosting) ? $hosting->price : '') }}" required min="0">
                            @if($errors->has('price'))
                                <p class="help-block text-danger">
                                    {{ $errors->first('price') }}
                                </p>
                            @endif
                            <p class="helper-block text-muted small">
                               Price in Rwandan Francs (RWF)
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required {{ $errors->has('period') ? 'has-error' : '' }}">
                            <label for="period">Period (Months)*</label>
                            <input type="number" id="period" name="period" class="form-control" value="{{ old('period', isset($hosting) ? $hosting->period : '12') }}" required min="1">
                            @if($errors->has('period'))
                                <p class="help-block text-danger">
                                    {{ $errors->first('period') }}
                                </p>
                            @endif
                            <p class="helper-block text-muted small">
                               Billing period in months
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group required {{ $errors->has('category_id') ? 'has-error' : '' }}">
                    <label for="category_id">Category*</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', isset($hosting) ? $hosting->category_id : '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('category_id'))
                        <p class="help-block text-danger">
                            {{ $errors->first('category_id') }}
                        </p>
                    @endif

                    <p class="helper-block">
                        Ex: 50000
                    </p>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <span class="bi bi-save"></span> Save Hosting Plan
                    </button>
                    <a href="{{ route('admin.hostings.index') }}" class="btn btn-warning right">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('name').addEventListener('input', function() {
        if (!document.getElementById('slug').value) {
            document.getElementById('slug').value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9-]+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        }
    });
</script>
@endpush
