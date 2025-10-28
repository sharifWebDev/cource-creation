<div class="container">
    <table id="Course" class="table table-hover"
        data-index-url="{{ url('api/v1/courses') }}"
        data-create-url="{{ url('admin/courses/create') }}"
        data-edit-url="{{ route('admin.courses.edit', ':id') }}"
        data-delete-url="{{ url('api/v1/courses/destroy') }}/:id"
        data-show-url="{{ route('admin.courses.show', ':id') }}"
        data-fields='["title", "price", "description", "feature_video_path", "feature_video_thumbnail", "status"]'
        data-image-fields='["feature_video_thumbnail", "feature_video_path"]'>
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@include('components.datatables')
