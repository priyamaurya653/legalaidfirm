{{--
    Superadmin -> Create / Edit Admin form fields
    Bilkul Sign Up form jaise fields  (users + user_profiles)
    Use: @include('frontlayout.admin-fields', ['mode' => 'create' | 'edit'])
--}}
@php
    $mode      = $mode ?? 'create';
    $isEdit    = $mode === 'edit';
    $genders   = config('advocate.genders', ['Male', 'Female', 'Other']);
    $states    = config('advocate.states', []);
    $practice  = array_column(config('practice', []), 'title');
    $heardFrom = \App\Http\Requests\UserRegisterRequest::HEARD_FROM;
    $req       = '<b class="req">*</b>';
@endphp

{{-- ================= 1. PERSONAL ================= --}}
<div class="adv-section">
    <div class="adv-sec-head">
        <span class="adv-sec-num">01</span>
        <div><h4>Personal Details</h4><small>Basic information of the admin</small></div>
    </div>

    <div class="photo-up" data-field="photo">
        <div class="photo-preview" data-photo-preview>
            <i class="fa-solid fa-user"></i>
            <img alt="" hidden>
        </div>
        <div>
            <label class="up-btn">
                <i class="fa-solid fa-camera"></i> {{ $isEdit ? 'Change Photo' : 'Upload Photo' }}
                <input type="file" name="photo" accept="image/png,image/jpeg,image/webp" hidden>
            </label>
            <small class="f-hint">Optional · JPG / PNG / WEBP · max 2MB</small>
        </div>
    </div>

    <div class="f-grid">
        <div class="f-group full" data-field="name">
            <label>Full Name {!! $req !!}</label>
            <input type="text" name="name" required minlength="3" maxlength="100" placeholder="e.g. Rahul Sharma">
        </div>
        <div class="f-group" data-field="gender">
            <label>Gender {!! $req !!}</label>
            <select name="gender" required>
                <option value="">Select</option>
                @foreach($genders as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>
        </div>
        <div class="f-group" data-field="dob">
            <label>Date of Birth {!! $req !!} <small class="f-hint">(18+)</small></label>
            <input type="date" name="dob" required max="{{ now()->subYears(18)->format('Y-m-d') }}" min="1920-01-02">
        </div>
        <div class="f-group full" data-field="occupation">
            <label>Occupation</label>
            <input type="text" name="occupation" maxlength="100" placeholder="e.g. Advocate, Legal Consultant">
        </div>
    </div>
</div>

{{-- ================= 2. CONTACT & ADDRESS ================= --}}
<div class="adv-section">
    <div class="adv-sec-head">
        <span class="adv-sec-num">02</span>
        <div><h4>Contact & Address</h4><small>How the admin can be reached</small></div>
    </div>

    <div class="f-grid">
        <div class="f-group full" data-field="email">
            <label>Email Address {!! $req !!} <small class="f-hint">(used for login)</small></label>
            <input type="email" name="email" required maxlength="150" placeholder="admin@example.com">
        </div>
        <div class="f-group" data-field="phone">
            <label>Mobile Number {!! $req !!}</label>
            <input type="tel" name="phone" required inputmode="numeric" maxlength="14" placeholder="10-digit mobile">
        </div>
        <div class="f-group" data-field="alt_phone">
            <label>Alternate Mobile</label>
            <input type="tel" name="alt_phone" inputmode="numeric" maxlength="14" placeholder="Optional">
        </div>
        <div class="f-group full" data-field="address">
            <label>Full Address {!! $req !!}</label>
            <textarea name="address" rows="2" required maxlength="255" placeholder="House no., street, area / locality"></textarea>
        </div>
        <div class="f-group" data-field="city">
            <label>City {!! $req !!}</label>
            <input type="text" name="city" required maxlength="80" placeholder="e.g. New Delhi">
        </div>
        <div class="f-group" data-field="state">
            <label>State / UT {!! $req !!}</label>
            <select name="state" required>
                <option value="">Select State</option>
                @foreach($states as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="f-group" data-field="pincode">
            <label>Pincode {!! $req !!}</label>
            <input type="text" name="pincode" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="6 digits">
        </div>
        <div class="f-group" data-field="interested_in">
            <label>Practice Area</label>
            <select name="interested_in">
                <option value="">Select practice area</option>
                @foreach($practice as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="f-group full" data-field="heard_from">
            <label>How did they hear about us?</label>
            <select name="heard_from">
                <option value="">Select</option>
                @foreach($heardFrom as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- ================= 3. LOGIN & ACCOUNT ================= --}}
<div class="adv-section">
    <div class="adv-sec-head">
        <span class="adv-sec-num">03</span>
        <div><h4>Login & Account</h4><small>{{ $isEdit ? 'Leave password blank to keep the current one' : 'Admin will log in with this email & password' }}</small></div>
    </div>

    <div class="f-grid">
        <div class="f-group" data-field="password">
            <label>Password {!! $isEdit ? '' : $req !!}</label>
            <div class="pw-wrap">
                <input type="password" name="password" minlength="8" {{ $isEdit ? '' : 'required' }} placeholder="{{ $isEdit ? 'Leave blank to keep' : 'Min 8, Aa + number' }}" autocomplete="new-password">
                <button type="button" class="pw-toggle" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>
            <div class="pw-meter"><i></i></div>
            <small class="f-hint">8+ characters with uppercase, lowercase and a number</small>
        </div>
        <div class="f-group" data-field="password_confirmation">
            <label>Confirm Password {!! $isEdit ? '' : $req !!}</label>
            <div class="pw-wrap">
                <input type="password" name="password_confirmation" minlength="8" {{ $isEdit ? '' : 'required' }} placeholder="Re-enter password" autocomplete="new-password">
                <button type="button" class="pw-toggle" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>
        <div class="f-group" data-field="status">
            <label>Account Status</label>
            <select name="status">
                <option value="active" selected>Active – can log in</option>
                <option value="pending">Pending – awaiting approval</option>
                <option value="inactive">Inactive – blocked</option>
            </select>
        </div>
    </div>
</div>