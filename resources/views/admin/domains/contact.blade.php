<div class="col-12">
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                @foreach($contacts as $type => $contact)
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" href="#{{ $type }}-tab" data-toggle="tab">
                        {{ ucfirst($type) }} Contact
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                @foreach($contacts as $type => $contact)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $type }}-tab">
                    <form action="{{ route('admin.contacts.update', ['domain' => $domain, 'type' => $type]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{ $type }}_name">Full Name</label>
                                    <input type="text" name="contact[name]" id="{{ $type }}_name" class="form-control"
                                        value="{{ old('contact.name', $contact?->name) }}">
                                    @error('contact.name')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{ $type }}_organization">Organization</label>
                                    <input type="text" name="contact[organization]" id="{{ $type }}_organization" class="form-control"
                                        value="{{ old('contact.organization', $contact?->organization) }}">
                                    @error('contact.organization')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{ $type }}_street1">Street Address</label>
                                    <input type="text" name="contact[streets][]" id="{{ $type }}_street1" class="form-control"
                                        value="{{ old('contact.streets.0', $contact?->street1) }}">
                                    @error('contact.streets.0')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="{{ $type }}_street2">Street Address Line 2</label>
                                    <input type="text" name="contact[streets][]" id="{{ $type }}_street2" class="form-control"
                                        value="{{ old('contact.streets.1', $contact?->street2) }}">
                                    @error('contact.streets.1')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="{{ $type }}_city">City</label>
                                    <input type="text" name="contact[city]" id="{{ $type }}_city" class="form-control"
                                        value="{{ old('contact.city', $contact?->city) }}">
                                    @error('contact.city')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="{{ $type }}_province">Province/State</label>
                                    <input type="text" name="contact[province]" id="{{ $type }}_province" class="form-control"
                                        value="{{ old('contact.province', $contact?->province) }}">
                                    @error('contact.province')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="{{ $type }}_postal_code">Postal Code</label>
                                    <input type="text" name="contact[postal_code]" id="{{ $type }}_postal_code" class="form-control"
                                        value="{{ old('contact.postal_code', $contact?->postal_code) }}">
                                    @error('contact.postal_code')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="{{ $type }}_country_code">Country</label>
                                    <select name="contact[country_code]" id="{{ $type }}_country_code" class="form-control">
                                        @foreach($countries as $code => $name)
                                            <option value="{{ $code }}" {{ old('contact.country_code', $contact?->country_code) == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contact.country_code')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="{{ $type }}_voice">Phone Number</label>
                                    <input type="tel" name="contact[voice]" id="{{ $type }}_voice" class="form-control"
                                        value="{{ old('contact.voice', $contact?->voice) }}">
                                    @error('contact.voice')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="{{ $type }}_email">Email Address</label>
                                    <input type="email" name="contact[email]" id="{{ $type }}_email" class="form-control"
                                        value="{{ old('contact.email', $contact?->email) }}">
                                    @error('contact.email')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Update {{ ucfirst($type) }} Contact</button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
