<div class="row">
    <div class="col-md-6">
        <label>Agency Name *</label>
        <input type="text" name="name" class="form-control" required value="{{ old('name', $agency->name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>Contact Person</label>
        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $agency->contact_person ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $agency->phone ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $agency->whatsapp ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $agency->email ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $agency->country ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $agency->city ?? '') }}">
    </div>
    <div class="col-md-6">
        <label>Services</label>
        <select name="services[]" class="form-control" multiple size="4">
            @foreach(['visa', 'umrah', 'hotel', 'transport', 'tickets'] as $s)
                <option value="{{ $s }}"
                    {{ in_array($s, old('services', $agency->services ?? [])) ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $agency->address ?? '') }}</textarea>
    </div>
</div>
<br>