{{--
    Shared news form partial. Used by create.blade.php and edit.blade.php.
    Variables: $news (optional, for edit mode)
--}}
<div class="form-card">

    <div class="mb-3">
        <label class="form-label fw-600" for="title">Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror"
               id="title" name="title" value="{{ old('title', $news->title ?? '') }}" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600" for="description">Content <span class="text-danger">*</span></label>
        <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                  id="description" name="description" rows="8" required>{{ old('description', $news->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600">Cover Image</label>
        @isset($news)
            @if ($news->image_path && !str_starts_with($news->image_path, 'images/'))
                <div class="mb-2">
                    <img src="{{ $news->image_url ?? '' }}"
                         alt="current" class="img-fluid rounded" style="max-height:140px;object-fit:cover;">
                    <div class="text-muted" style="font-size:0.75rem;">Current image. Upload a new one to replace.</div>
                </div>
            @endif
        @endisset
        <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror"
               name="image" accept="image/*" onchange="previewImg(this)">
        <img id="imgPreview" src="" alt="preview" class="mt-2 rounded" style="max-height:120px;display:none;object-fit:cover;">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">JPEG, PNG or GIF. Max 4 MB. Leave blank to keep current.</div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-save me-1"></i> {{ isset($news) ? 'Update Article' : 'Publish Article' }}
        </button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
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
