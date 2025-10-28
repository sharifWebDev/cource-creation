<form method="POST" id="createcourse_modulesForm" action="{{ url('api/v1/course-modules') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="course_id">Course Id</label>
        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" id="create_course_id">
<option value="">Select Course Id</option>

</select>
        @error('course_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Enter Title..." id="create_title" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="create_description" required>{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="order">Order</label>
        <input type="number" name="order" min="0" class="form-control @error('order') is-invalid @enderror" value="{{ old('order') }}" placeholder="Enter Order..." id="create_order" required>
        @error('order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="is_published">Is Published</label>
        <input type="radio" name="is_published" id="create_is_published_yes" value="1" {{ old('is_published') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_published_yes">Is Published Yes</label>
<input type="radio" name="is_published" id="create_is_published_no" value="0" {{ old('is_published') == 0 ? "checked" : "" }}> 
                            <label for="create_is_published_no">Is Published No</label>
        @error('is_published')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger" href="{{ route('admin.course-modules.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>