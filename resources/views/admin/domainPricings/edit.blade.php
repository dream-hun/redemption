@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.domainPricing.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.domain-pricings.update", [$domainPricing->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="tld">{{ trans('cruds.domainPricing.fields.tld') }}</label>
                <input class="form-control {{ $errors->has('tld') ? 'is-invalid' : '' }}" type="text" name="tld" id="tld" value="{{ old('tld', $domainPricing->tld) }}" required>
                @if($errors->has('tld'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tld') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.tld_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="register_price">{{ trans('cruds.domainPricing.fields.register_price') }}</label>
                <input class="form-control {{ $errors->has('register_price') ? 'is-invalid' : '' }}" type="number" name="register_price" id="register_price" value="{{ old('register_price', $domainPricing->register_price) }}" step="1" required>
                @if($errors->has('register_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('register_price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.register_price_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="transfer_price">{{ trans('cruds.domainPricing.fields.transfer_price') }}</label>
                <input class="form-control {{ $errors->has('transfer_price') ? 'is-invalid' : '' }}" type="number" name="transfer_price" id="transfer_price" value="{{ old('transfer_price', $domainPricing->transfer_price) }}" step="1" required>
                @if($errors->has('transfer_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('transfer_price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.transfer_price_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="renew_price">{{ trans('cruds.domainPricing.fields.renew_price') }}</label>
                <input class="form-control {{ $errors->has('renew_price') ? 'is-invalid' : '' }}" type="number" name="renew_price" id="renew_price" value="{{ old('renew_price', $domainPricing->renew_price) }}" step="1" required>
                @if($errors->has('renew_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('renew_price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.renew_price_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="grace">{{ trans('cruds.domainPricing.fields.grace') }}</label>
                <input class="form-control {{ $errors->has('grace') ? 'is-invalid' : '' }}" type="number" name="grace" id="grace" value="{{ old('grace', $domainPricing->grace) }}" step="1">
                @if($errors->has('grace'))
                    <div class="invalid-feedback">
                        {{ $errors->first('grace') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.grace_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="redemption_price">{{ trans('cruds.domainPricing.fields.redemption_price') }}</label>
                <input class="form-control {{ $errors->has('redemption_price') ? 'is-invalid' : '' }}" type="text" name="redemption_price" id="redemption_price" value="{{ old('redemption_price', $domainPricing->redemption_price) }}" required>
                @if($errors->has('redemption_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('redemption_price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.redemption_price_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.domainPricing.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\DomainPricing::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $domainPricing->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.domainPricing.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
