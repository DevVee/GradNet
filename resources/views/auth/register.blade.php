@extends('layouts.auth')

@section('title', 'Create Account — ICCBI Alumni')

@section('content')

{{-- Form header --}}
<div class="auth-form-header">
    <h2 class="auth-form-title">Create Account</h2>
    <p class="auth-form-subtitle">
        Join the ICCBI Alumni community. All information is protected under
        the <strong>Data Privacy Act of 2012</strong>.
    </p>
</div>

{{-- Validation errors --}}
@if($errors->any())
    <div class="flash-alert danger mb-4">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0;"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1 ps-3" style="font-size:var(--text-xs);">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('register') }}" id="signupForm">
    @csrf

    {{-- ══ SECTION 1 — Personal & Academic ════════════════════════════ --}}
    <div class="section-divider-label">Personal and Academic Information</div>

    {{-- Alumni Status --}}
    <div class="mb-3">
        <label class="form-label required">Alumni Status</label>
        <select name="alumni_status" class="form-control" required onchange="floatLabel(this)">
            <option value=""></option>
            @foreach(['Yes','No'] as $opt)
                <option value="{{ $opt }}" {{ old('alumni_status') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label class="form-label required">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="you@example.com"
                   required value="{{ old('email') }}">
        </div>
        @error('email')<div class="form-error">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label class="form-label required">Last Name</label>
            <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
        </div>
        <div class="col-sm-6">
            <label class="form-label required">First Name</label>
            <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
        </div>
        <div class="col-sm-6">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
        </div>
        <div class="col-sm-6">
            <label class="form-label">Suffix</label>
            <input type="text" name="suffix" class="form-control" placeholder="Jr., III…" value="{{ old('suffix') }}">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label class="form-label required">Sex</label>
            <select name="sex" class="form-control" required>
                <option value=""></option>
                @foreach(['Male','Female','Other'] as $opt)
                    <option value="{{ $opt }}" {{ old('sex') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label required">Civil Status</label>
            <select name="civil_status" id="civil_status" class="form-control" required
                    onchange="toggleSpouseName()">
                <option value=""></option>
                @foreach($civilStatuses as $opt)
                    <option value="{{ $opt }}" {{ old('civil_status') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Spouse Name (conditional) --}}
    <div class="mb-3 conditional-field" id="spouse_name_group">
        <label class="form-label required">Spouse Name</label>
        <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name') }}">
    </div>

    <div class="mb-3">
        <label class="form-label required">Religion</label>
        <select name="religion" class="form-control" required>
            <option value=""></option>
            @foreach($religions as $opt)
                <option value="{{ $opt }}" {{ old('religion') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label class="form-label required">Birthdate</label>
            <input type="date" name="birthday" id="birthday" class="form-control"
                   required value="{{ old('birthday') }}" oninput="calculateAge()">
        </div>
        <div class="col-sm-6">
            <label class="form-label required">Age</label>
            <select name="age" id="age" class="form-control" required>
                <option value=""></option>
                @foreach($ages as $a)
                    <option value="{{ $a }}" {{ old('age') == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label class="form-label required">Home Municipality</label>
            <select name="home_municipality" class="form-control" required>
                <option value=""></option>
                @foreach($batangasTowns as $town)
                    <option value="{{ $town }}" {{ old('home_municipality') == $town ? 'selected' : '' }}>{{ $town }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label required">Home Barangay</label>
            <input type="text" name="home_barangay" class="form-control" required value="{{ old('home_barangay') }}">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Permanent Address <span class="text-muted fw-400">(if outside Balayan)</span></label>
        <input type="text" name="permanent_address" class="form-control" value="{{ old('permanent_address') }}">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label class="form-label">Mobile Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="col-sm-6">
            <label class="form-label required">Preferred Contact</label>
            <select name="preferred_contact" class="form-control" required>
                <option value=""></option>
                @foreach($preferredContacts as $opt)
                    <option value="{{ $opt }}" {{ old('preferred_contact') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label required">Facebook / Messenger Account</label>
        <input type="text" name="facebook_account" class="form-control" required value="{{ old('facebook_account') }}">
    </div>

    <div class="mb-3">
        <label class="form-label required">Highest Degree at ICC Balayan</label>
        <select name="highest_degree" class="form-control" required>
            <option value=""></option>
            @foreach($highestDegrees as $opt)
                <option value="{{ $opt }}" {{ old('highest_degree') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label class="form-label required">Level</label>
            <select name="level" id="level" class="form-control" required
                    onchange="updatePrograms(); toggleCollegeSection();">
                <option value=""></option>
                @foreach(['Elementary','Junior High School','Senior High School','College'] as $opt)
                    <option value="{{ $opt }}" {{ old('level') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label required">Program</label>
            <select name="program" id="program" class="form-control" required>
                <option value=""></option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label required">Year Graduated</label>
        <select name="graduation_year" class="form-control" required>
            <option value=""></option>
            @foreach($graduationYears as $yr)
                <option value="{{ $yr }}" {{ old('graduation_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Honors at Graduation</label>
        <input type="text" name="honors" class="form-control" value="{{ old('honors') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Professional Board Exam Passed</label>
        <input type="text" name="board_exam" class="form-control" value="{{ old('board_exam') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Other Schools Attended</label>
        <textarea name="other_schools" class="form-control" rows="2">{{ old('other_schools') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label required">Present Occupation</label>
        <input type="text" name="present_occupation" class="form-control" required value="{{ old('present_occupation') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Other Work Experiences</label>
        <textarea name="other_experiences" class="form-control" rows="2">{{ old('other_experiences') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Company / Office Address</label>
        <input type="text" name="company_address" class="form-control" value="{{ old('company_address') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Comments / Suggestions</label>
        <textarea name="comments" class="form-control" rows="2">{{ old('comments') }}</textarea>
    </div>

    {{-- ══ SECTION 2 — College Alumni Only ════════════════════════════ --}}
    <div id="college_section" class="conditional-field">
        <div class="section-divider-label">For College Alumni Only</div>

        <div class="mb-3">
            <label class="form-label required">Self-rated Academic Performance</label>
            <select name="academic_performance" class="form-control">
                <option value=""></option>
                @foreach($academicPerfs as $opt)
                    <option value="{{ $opt }}" {{ old('academic_performance') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label required">Present Employment Status</label>
            <select name="employment_status" id="employment_status" class="form-control"
                    onchange="toggleEmploymentFields()">
                <option value=""></option>
                @foreach($employmentStatuses as $opt)
                    <option value="{{ $opt }}" {{ old('employment_status') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 conditional-field" id="employment_type_group">
            <label class="form-label required">Employment Type</label>
            <select name="employment_type" class="form-control">
                <option value=""></option>
                @foreach($employmentTypes as $opt)
                    <option value="{{ $opt }}" {{ old('employment_type') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 conditional-field" id="unemployment_reason_group">
            <label class="form-label required">Unemployment Reason</label>
            <input type="text" name="unemployment_reason" class="form-control" value="{{ old('unemployment_reason') }}">
        </div>

        <div class="mb-3">
            <label class="form-label required">Time to First Job <span class="text-muted fw-400">(e.g. 3 months)</span></label>
            <input type="text" name="time_to_first_job" class="form-control" value="{{ old('time_to_first_job') }}">
        </div>

        <div class="mb-3">
            <label class="form-label required">Is Job Related to Course?</label>
            <select name="job_related" class="form-control">
                <option value=""></option>
                @foreach($jobRelatedOptions as $opt)
                    <option value="{{ $opt }}" {{ old('job_related') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Changes to Enhance Competitiveness
                <span class="text-muted fw-400" style="font-size:var(--text-xs);">(Ctrl/Cmd + click for multiple)</span>
            </label>
            <select name="changes_needed[]" class="form-control" multiple style="height:auto;min-height:80px;padding:var(--sp-2);">
                @foreach($changesNeeded as $opt)
                    @php $selected = is_array(old('changes_needed')) && in_array($opt, old('changes_needed', [])); @endphp
                    <option value="{{ $opt }}" {{ $selected ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ══ SECTION 3 — Data Privacy ════════════════════════════════════ --}}
    <div class="section-divider-label">Data Privacy Consent</div>

    <p class="mb-3" style="font-size:var(--text-xs);color:var(--text-muted);line-height:1.7;
       background:var(--surface-2);padding:var(--sp-3);border-radius:var(--radius-sm);
       border-left:3px solid var(--primary-mid);">
        We comply with the <strong style="color:var(--text-body);">Data Privacy Act of 2012</strong> and its IRR.
        Do you consent to the processing of your personal data for alumni networking and communication purposes?
    </p>
    <div class="mb-4">
        <select name="consent" class="form-control" required>
            <option value=""></option>
            <option value="Yes" {{ old('consent') == 'Yes' ? 'selected' : '' }}>Yes, I consent</option>
            <option value="No"  {{ old('consent') == 'No'  ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- ══ SECTION 4 — Credentials ══════════════════════════════════════ --}}
    <div class="section-divider-label">Account Credentials</div>

    <div class="mb-3">
        <label class="form-label required">Password <span class="text-muted fw-400">(minimum 8 characters)</span></label>
        <div style="position:relative;">
            <input type="password" name="password" id="password" class="form-control"
                   placeholder="••••••••" required minlength="8"
                   autocomplete="new-password" style="padding-right:56px;">
            <button type="button" onclick="togglePwd('password', this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                           background:none;border:none;color:var(--primary);cursor:pointer;
                           font-size:var(--text-xs);font-weight:600;padding:4px 6px;">Show</button>
        </div>
    </div>
    <div class="mb-5">
        <label class="form-label required">Confirm Password</label>
        <div style="position:relative;">
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control" placeholder="••••••••" required minlength="8"
                   autocomplete="new-password" style="padding-right:56px;">
            <button type="button" onclick="togglePwd('password_confirmation', this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                           background:none;border:none;color:var(--primary);cursor:pointer;
                           font-size:var(--text-xs);font-weight:600;padding:4px 6px;">Show</button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-wide btn-lg">
        <i class="fas fa-user-plus me-2"></i> Create Account
    </button>
</form>

<div class="auth-footer-text">
    Already have an account? <a href="{{ route('login') }}">Sign in →</a>
</div>

@endsection

@push('scripts')
<script>
// ── Program lists (injected from controller) ──────────────────────
const PROGRAMS = {
    'College':            @json($collegePrograms),
    'Senior High School': @json($shsPrograms),
    'Junior High School': @json($jhsPrograms),
    'Elementary':         @json($elemPrograms),
};

function updatePrograms() {
    const level  = document.getElementById('level').value;
    const select = document.getElementById('program');
    const oldVal = "{{ old('program') }}";
    select.innerHTML = '<option value=""></option>';
    (PROGRAMS[level] || []).forEach(p => {
        select.appendChild(new Option(p, p, false, p === oldVal));
    });
}

function toggleCollegeSection() {
    const show = document.getElementById('level').value === 'College';
    document.getElementById('college_section').style.display = show ? 'block' : 'none';
    document.querySelectorAll(
        '#college_section [name="academic_performance"], #college_section [name="employment_status"], ' +
        '#college_section [name="time_to_first_job"], #college_section [name="job_related"]'
    ).forEach(f => f.required = show);
}

function toggleSpouseName() {
    const civil = document.getElementById('civil_status').value;
    const group = document.getElementById('spouse_name_group');
    const input = group.querySelector('input');
    const show  = civil === 'Married';
    group.style.display = show ? 'block' : 'none';
    input.required = show;
    if (!show) input.value = '';
}

function toggleEmploymentFields() {
    const status   = document.getElementById('employment_status')?.value || '';
    const typeGrp  = document.getElementById('employment_type_group');
    const unempGrp = document.getElementById('unemployment_reason_group');

    if (status === 'Employed') {
        typeGrp.style.display  = 'block';
        unempGrp.style.display = 'none';
        typeGrp.querySelector('select').required = true;
        unempGrp.querySelector('input').required = false;
    } else if (status === 'Unemployed') {
        typeGrp.style.display  = 'none';
        unempGrp.style.display = 'block';
        typeGrp.querySelector('select').required = false;
        unempGrp.querySelector('input').required = true;
    } else {
        typeGrp.style.display  = 'none';
        unempGrp.style.display = 'none';
        typeGrp.querySelector('select').required = false;
        unempGrp.querySelector('input').required = false;
    }
}

function calculateAge() {
    const bd  = document.getElementById('birthday').value;
    const sel = document.getElementById('age');
    if (!bd) return;
    const birth = new Date(bd), today = new Date();
    if (birth >= today) { sel.value = ''; return; }
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    sel.value = (age >= 5 && age <= 120) ? age : '';
}

function togglePwd(id, btn) {
    const input = document.getElementById(id);
    input.type  = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? 'Show' : 'Hide';
}

function validatePasswords() {
    const pw  = document.getElementById('password').value;
    const cpw = document.getElementById('password_confirmation');
    cpw.setCustomValidity(pw && cpw.value && pw !== cpw.value ? 'Passwords do not match' : '');
}

document.addEventListener('DOMContentLoaded', () => {
    updatePrograms();
    toggleCollegeSection();
    toggleSpouseName();
    toggleEmploymentFields();
    calculateAge();
    document.getElementById('password').addEventListener('input', validatePasswords);
    document.getElementById('password_confirmation').addEventListener('input', validatePasswords);
});
</script>
@endpush
