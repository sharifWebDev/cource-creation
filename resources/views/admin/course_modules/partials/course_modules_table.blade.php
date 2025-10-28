<div class="container">
    <table id="CourseModule" class="table table-hover"
        data-index-url="{{ url('api/v1/course-modules') }}"
        data-create-url="{{ url('admin/course-modules/create') }}"
        data-edit-url="{{ route('admin.course-modules.edit', ':id') }}"
        data-delete-url="{{ url('api/v1/course-modules/destroy') }}/:id"
        data-show-url="{{ route('admin.course-modules.show', ':id') }}"
        data-fields='["course_id", "title", "description", "order", "is_published"]'>
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@include('components.datatables');
