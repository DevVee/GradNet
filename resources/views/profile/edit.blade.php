@extends('layouts.app')

@section('title', 'Edit Profile — GradNet')

@section('content')

    <div class="page-header">
        <a href="{{ route('profile.show', ['user' => $user->id]) }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="page-title">Edit Profile</h1>
    </div>

    {{-- ── Profile Picture ─────────────────────────────────────── --}}
    <div class="card mb-3">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-camera me-1 text-primary"></i> Profile Picture
            </h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.picture') }}" enctype="multipart/form-data">
                @csrf
                <div class="d-flex align-items-center gap-4 mb-3">
                    <img id="avatarPreview"
                         src="{{ $user->avatar_url }}"
                         alt="{{ $user->first_name }}"
                         class="avatar avatar-xl"
                         style="border:3px solid var(--primary);"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                    <div>
                        <label for="profile_picture" class="btn btn-surface btn-sm" style="cursor:pointer;">
                            <i class="fas fa-upload me-1"></i> Choose Image
                        </label>
                        <input type="file" id="profile_picture" name="profile_picture"
                               accept="image/*" style="display:none;"
                               onchange="previewAvatar(this)">
                        <div class="form-hint mt-1">JPG, PNG, GIF · max 4 MB</div>
                        @error('profile_picture')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save me-1"></i> Update Photo
                </button>
            </form>
        </div>
    </div>

    {{-- ── Personal Information ─────────────────────────────────── --}}
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PATCH')

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="section-title-sm">
                    <i class="fas fa-user me-1 text-primary"></i> Personal Information
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label required">First Name</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               required value="{{ old('first_name', $user->first_name) }}">
                        @error('first_name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label required">Last Name</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               required value="{{ old('last_name', $user->last_name) }}">
                        @error('last_name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control"
                               value="{{ old('middle_name', $user->middle_name) }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Suffix</label>
                        <input type="text" name="suffix" class="form-control"
                               value="{{ old('suffix', $user->suffix) }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label required">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               required value="{{ old('email', $user->email) }}">
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Academic & Career ──────────────────────────────────── --}}
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="section-title-sm">
                    <i class="fas fa-graduation-cap me-1 text-primary"></i> Academic &amp; Career
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Program</label>
                        <input type="text" name="program" class="form-control"
                               value="{{ old('program', $user->program) }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Graduation Year</label>
                        <input type="number" name="graduation_year" class="form-control"
                               min="1941" max="{{ date('Y') + 5 }}"
                               value="{{ old('graduation_year', $user->graduation_year) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Present Occupation</label>
                        <input type="text" name="present_occupation" class="form-control"
                               value="{{ old('present_occupation', $user->present_occupation) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Workplace / Company</label>
                        <input type="text" name="workplace" class="form-control"
                               value="{{ old('workplace', $user->workplace) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Location / City</label>
                        <input type="text" name="location" class="form-control"
                               value="{{ old('location', $user->location) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Social & Contact ─────────────────────────────────── --}}
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="section-title-sm">
                    <i class="fas fa-share-alt me-1 text-primary"></i> Social &amp; Contact
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Facebook / Messenger Account</label>
                        <input type="text" name="facebook_account" class="form-control"
                               value="{{ old('facebook_account', $user->facebook_account) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Facebook Profile URL</label>
                        <div class="input-wrap">
                            <i class="fab fa-facebook-f input-icon" style="color:#1877f2;"></i>
                            <input type="url" name="facebook_link" class="form-control"
                                   placeholder="https://facebook.com/…"
                                   value="{{ old('facebook_link', $user->facebook_link) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Instagram Profile URL</label>
                        <div class="input-wrap">
                            <i class="fab fa-instagram input-icon" style="color:#e4405f;"></i>
                            <input type="url" name="instagram_link" class="form-control"
                                   placeholder="https://instagram.com/…"
                                   value="{{ old('instagram_link', $user->instagram_link) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">LinkedIn Profile URL</label>
                        <div class="input-wrap">
                            <i class="fab fa-linkedin-in input-icon" style="color:#0a66c2;"></i>
                            <input type="url" name="linkedin_link" class="form-control"
                                   placeholder="https://linkedin.com/in/…"
                                   value="{{ old('linkedin_link', $user->linkedin_link) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Comments / Suggestions</label>
                        <textarea name="comments" class="form-control" rows="3"
                                  style="height:auto;">{{ old('comments', $user->comments) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="flash-alert danger mb-3">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0 ps-3 mt-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <button type="submit" class="btn btn-primary btn-wide btn-lg mb-4">
            <i class="fas fa-save me-1"></i> Save Changes
        </button>
    </form>

    {{-- ── Change Password ─────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-lock me-1 text-primary"></i> Change Password
            </h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                    @error('current_password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <span class="text-muted">(min. 8 chars)</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn btn-outline btn-wide">
                    <i class="fas fa-lock me-1"></i> Change Password
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
