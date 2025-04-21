<div class="row">
    <div class="col-md-8">
        <div class="form-group required {{ $errors->has('period') ? 'has-error' : '' }}">
            <label for="period">Renewal Period in Years<span class="text-danger">
                    *</span></label>
            {{-- <input type="number" id="period" name="period" class="form-control"
                wire:model='periode' required min="1" max="10"
                wire:change='handleCartPeriodCount()'>
            @if ($errors->has('period'))
                <p class="help-block text-danger">
                    {{ $errors->first('period') }}
                </p>
            @endif --}}

            <input type="number" id="period" name="period" class="form-control" wire:model='periode' required
                min="1" max="10" wire:change='handleCartPeriodCount()'>
            @if ($errors->has('period'))
                <p class="help-block text-danger">
                    {{ $errors->first('period') }}
                </p>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group required {{ $errors->has('period') ? 'has-error' : '' }}">
            <label class="float-right ">Price/Yr</label>

            <label class="float-right pt-2 small" style="width: 100%;text-align:right">
                {{$cartitem['price']}}
            </label>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="period">Total Price <span class="text-danger"></span></label>

            <label class="float-right ">{{ $total }}</label>

        </div>
    </div>
</div>
