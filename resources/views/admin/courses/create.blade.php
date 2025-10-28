@extends('layouts.admin')
@section('title', 'Admin | Courses')
@section('page-headder')
@endsection
@section('content')
    <div class="py-2 my-auto mb-3 bg-white border shadow-md row align-items-center">
        <div class="col-sm-6"> <span class="my-auto h6 page-headder"> @yield('page-headder') </span>
            <ol class="bg-white breadcrumb">
                <li class="breadcrumb-item"> <a href="{{ route('admin.dashboard') }}" class="text-primary"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item text-dark"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create New Courses</li>
            </ol>
        </div>
        <div class="text-right col-sm-6">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-primary btn-sm"> <i class="fa fa-arrow-left"></i> Back to Courses List</a>
        </div>
    </div>
    <div class="row">
        <div class="p-0 bg-white card">
            <div class="container px-4">
                @include('admin.courses.partials.create-form')
            </div>
        </div>
    </div>

    <style>
    .drag-drop-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .drag-drop-area:hover,
    .drag-drop-area.dragover {
        border-color: #007bff;
        background: #e7f3ff;
    }

    .drag-drop-area i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .module-card,
    .content-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .module-card:hover,
    .content-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .module-header,
    .content-header {
        background: #f8f9fa;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        cursor: move;
    }

    .module-body,
    .content-body {
        padding: 15px;
    }

    .sortable-ghost {
        opacity: 0.4;
        background: #c8ebfb;
    }

    .btn-add {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-add:hover {
        background: #218838;
    }

    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .btn-remove:hover {
        background: #c82333;
    }

    .invalid-feedback {
        display: none;
    }

    .was-validated .form-control:invalid~.invalid-feedback {
        display: block;
    }

    .content-type-section {
        display: none;
    }

    .content-type-section.active {
        display: block;
    }

    .preview-image {
        max-width: 200px;
        max-height: 150px;
        margin-top: 10px;
        border-radius: 4px;
    }
</style>



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>



@endsection
