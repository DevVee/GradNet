{{-- Shared event form partial. Variables: $event (optional, for edit mode) --}}
<div class="form-card">

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-600" for="title">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror"
                   id="title" name="title" value="{{ old('title', $event->title ?? '') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-600" for="event_datetime">Date &amp; Time <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control form-control-sm @error('event_datetime') is-invalid @enderror"
                   id="event_datetime" name="event_datetime"
                   value="{{ old('event_datetime', isset($event) ? $event->event_datetime->format('Y-m-d\TH:i') : '') }}" required>
            @error('event_datetime') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label fw-600" for="location">Location <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm @error('location') is-invalid @enderror"
                   id="location" name="location" value="{{ old('location', $event->location ?? '') }}" required>
            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-600" for="description">Description <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                      id="description" name="description" rows="6" required>{{ old('description', $event->description ?? '') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-600">Cover Image</label>
            @isset($event)
                @if ($event->image_path && !str_starts_with($event->image_path, 'images/'))
                    <div class="mb-2">
                        <img src="{{ $event->image_url ?? '' }}" alt="current" class="img-fluid rounded" style="max-height:120px;object-fit:cover;">
                        <div class="text-muted" style="font-size:0.75rem;">Current image.</div>
                    </div>
                @endif
            @endisset
            <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror"
                   name="image" accept="image/*" onchange="previewImg(this)">
            <img id="imgPreview" src="" alt="preview" class="mt-2 rounded" style="max-height:120px;display:none;object-fit:cover;">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">JPEG, PNG or GIF. Max 4 MB.</div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> {{ isset($event) ? 'Update Event' : 'Create Event' }}
        </button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
    </div>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('imgPreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
