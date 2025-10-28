<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

<div class="py-4 container-fluid">
    <!-- Header -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0 h3">Create a Course</h1>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Course Page
                </a>
            </div>
        </div>
    </div>

    <form id="courseForm" class="needs-validation" action="{{ route('admin.courses.store') }}" method="POST" novalidate
        enctype="multipart/form-data">
        @csrf

        <!-- Course Basic Information -->
        <div class="row">
            <div class="col-md-8">
                <!-- Course Title -->
                <div class="form-group">
                    <label for="courseTitle" class="font-weight-bold">Course Title *</label>
                    <input type="text" class="form-control" id="courseTitle" name="title" required>
                    <div class="invalid-feedback">Please enter a course title.</div>
                </div>

                <!-- Feature Video -->
                <div class="form-group">
                    <label for="featureVideo" class="font-weight-bold">Feature Video</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="featureVideo" name="feature_video_path"
                            accept="video/*">
                        <label class="custom-file-label" for="featureVideo">Choose video file...</label>
                    </div>
                    <small class="form-text text-muted">Supported formats: MP4, AVI, MOV, WMV (Max: 100MB)</small>
                </div>

                <!-- Course Summary -->
                <div class="form-group">
                    <label for="courseSummary" class="font-weight-bold">Course Summary *</label>
                    <div class="p-2 mb-2 border rounded bg-light">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" data-command="bold"><i
                                    class="fas fa-bold"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-command="italic"><i
                                    class="fas fa-italic"></i></button>
                            <button type="button" class="btn btn-outline-secondary"
                                data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-command="insertOrderedList"><i
                                    class="fas fa-list-ol"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-command="createLink"><i
                                    class="fas fa-link"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-command="unlink"><i
                                    class="fas fa-unlink"></i></button>
                        </div>
                    </div>
                    <textarea class="form-control" id="courseSummary" name="description" rows="6" required></textarea>
                    <div class="invalid-feedback">Please enter a course summary.</div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Level, Category, Price -->
                <div class="form-group">
                    <label for="courseLevel" class="font-weight-bold">Level *</label>
                    <select class="form-control" id="courseLevel" name="level" required>
                        <option value="">Choose...</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                    <div class="invalid-feedback">Please select a course level.</div>
                </div>

                <div class="form-group">
                    <label for="courseCategory" class="font-weight-bold">Category *</label>
                    <select class="form-control" id="courseCategory" name="category_id" required>
                        <option value="">Choose...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Please select a category.</div>
                </div>

                <div class="form-group">
                    <label for="coursePrice" class="font-weight-bold">Course Price *</label>
                    <input type="number" class="form-control" id="coursePrice" name="price" min="0"
                        step="0.01" value="0" required>
                    <small class="form-text text-muted">
                        If the course price is 0, the user can enroll in this course for free; otherwise, the user needs
                        to pay.
                    </small>
                    <div class="invalid-feedback">Please enter a valid course price.</div>
                </div>

                <!-- Feature Image -->
                <div class="form-group">
                    <label class="font-weight-bold">Feature Image</label>
                    <div class="drag-drop-area" id="featureImageDropArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="mb-2">Drag and drop a file here or click</p>
                        <button type="button" class="btn btn-outline-primary btn-sm">Browse Files</button>
                        <input type="file" id="featureImage" name="feature_video_thumbnail" accept="image/*"
                            hidden>
                        <div id="featureImagePreview" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules Section -->
        <div class="mt-5 row">
            <div class="col-12">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Course Modules</h4>
                    <button type="button" class="btn btn-success" id="addModuleBtn">
                        <i class="fas fa-plus"></i> Add Module
                    </button>
                </div>

                <div id="modulesContainer" class="sortable-container">
                    <!-- Modules will be dynamically added here -->
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-5 row">
            <div class="col-12">
                <div class="gap-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> Save Course
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- Module Template (Hidden) -->
<div id="moduleTemplate" class="d-none">
    <div class="module-card sortable-item" data-module-index="__INDEX__">
        <div class="module-header d-flex justify-content-between align-items-center" data-toggle="collapse"
            data-target="#moduleDetails__INDEX__" aria-expanded="true" aria-controls="moduleDetails__INDEX__">
            <h6 class="mb-0 module-title-display">Module __INDEX__</h6>
            <div class="gap-2 d-flex">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse"
                    data-target="#moduleDetails__INDEX__" aria-expanded="true"
                    aria-controls="moduleDetails__INDEX__">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <button type="button" class="btn-remove remove-module" style="z-index: 50 !important;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div id="moduleDetails__INDEX__" class="collapse show">
            <div class="module-body">
                <div class="form-group">
                    <label>Module Title *</label>
                    <input type="text" class="form-control module-title" name="modules[__INDEX__][title]"
                        required>
                    <div class="invalid-feedback">Please enter a module title.</div>
                </div>
                <div class="form-group">
                    <label>Module Description</label>
                    <textarea class="form-control" name="modules[__INDEX__][description]" rows="2"></textarea>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Module Contents</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary add-content-btn">
                        <i class="fas fa-plus"></i> Add Content
                    </button>
                </div>

                <div class="px-4 contents-container sortable-container" data-module-index="__INDEX__">
                    <!-- Contents will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Content Template (Hidden) -->
<div id="contentTemplate" class="d-none">
    <div class="content-card sortable-item" data-content-index="__CONTENT_INDEX__">
        <div class="content-header d-flex justify-content-between align-items-center" data-toggle="collapse"
            data-target="#contentDetails__MODULE_INDEX____CONTENT_INDEX__" aria-expanded="false"
            aria-controls="contentDetails__MODULE_INDEX____CONTENT_INDEX__">
            <h6 class="mb-0">Content __CONTENT_INDEX__</h6>
            <div class="gap-2 d-flex">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse"
                    data-target="#contentDetails__MODULE_INDEX____CONTENT_INDEX__" aria-expanded="false"
                    aria-controls="contentDetails__MODULE_INDEX____CONTENT_INDEX__">
                    <i class="fas fa-chevron-down"></i>
                    <button type="button" class="btn-remove remove-content" style="z-index: 50 !important;">
                        <i class="fas fa-times"></i>
                    </button>
                </button>
            </div>
        </div>
        <div id="contentDetails__MODULE_INDEX____CONTENT_INDEX__" class="collapse">
            <div class="content-body">
                <div class="form-group">
                    <label>Content Title *</label>
                    <input type="text" class="form-control content-title"
                        name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][title]" required>
                    <div class="invalid-feedback">Please enter a content title.</div>
                </div>

                <div class="form-group">
                    <label>Content Type *</label>
                    <select class="form-control content-type-select"
                        name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_type_id]" required>
                        <option value="">Choose...</option>
                        @foreach ($contentTypes as $type)
                            <option value="{{ $type->id }}" data-slug="{{ $type->slug }}">{{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Please select a content type.</div>
                </div>

                <!-- Content Type Specific Fields -->
                <div class="content-type-sections">
                    <!-- Text Content -->
                    <div class="content-type-section" data-type="text">
                        <div class="form-group">
                            <label>Content Text *</label>
                            <textarea class="form-control" name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][text]"
                                rows="4"></textarea>
                            <div class="invalid-feedback">Please enter content text.</div>
                        </div>
                    </div>

                    <!-- In the content template's video section -->
                    <div class="content-type-section" data-type="video">
                        <div class="form-group">
                            <label>Video Source Type *</label>
                            <select class="form-control video-source-type"
                                name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][source_type]"
                                required>
                                <option value="">Choose...</option>
                                <option value="url">Video URL</option>
                                <option value="upload">Upload Video</option>
                            </select>
                            <div class="invalid-feedback">Please select video source type.</div>
                        </div>

                        <!-- Video URL Section -->
                        <div class="form-group video-url-section" style="display: none;">
                            <label>Video URL *</label>
                            <input type="url" class="form-control"
                                name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][video_url]"
                                placeholder="https://...">
                            <div class="invalid-feedback">Please enter a valid video URL.</div>
                        </div>

                        <!-- Video Upload Section -->
                        <div class="form-group video-upload-section" style="display: none;">
                            <label>Upload Video *</label>
                            <div class="custom-file">
                                <input type="file"
                                    name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][video_file]"
                                    class="custom-file-input video-file" accept="video/*">
                                <label class="custom-file-label">Choose video file...</label>
                            </div>
                            <div class="invalid-feedback">Please select a video file.</div>
                        </div>

                        <div class="form-group">
                            <label>Video Length (HH:MM:SS)</label>
                            <input type="text" class="form-control video-length"
                                name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][duration]"
                                placeholder="00:00:00" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}">
                            <div class="invalid-feedback">Please enter video length in HH:MM:SS format.</div>
                        </div>
                    </div>

                    <!-- Image Content -->
                    <div class="content-type-section" data-type="image">
                        <div class="form-group">
                            <label>Image Upload</label>
                            <div class="custom-file">
                                <input type="file"
                                    name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][image_file]"
                                    class="custom-file-input image-file" accept="image/*">
                                <label class="custom-file-label">Choose image file...</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Image Caption</label>
                            <input type="text" class="form-control"
                                name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][caption]">
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="content-type-section" data-type="document">
                        <div class="form-group">
                            <label>Document Upload</label>
                            <div class="custom-file">
                                <input type="file"
                                    name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][document_file]"
                                    class="custom-file-input document-file" accept=".pdf,.doc,.docx,.ppt,.pptx">
                                <label class="custom-file-label">Choose document file...</label>
                            </div>
                        </div>
                    </div>

                    <!-- Link Content -->
                    <div class="content-type-section" data-type="link">
                        <div class="form-group">
                            <label>URL *</label>
                            <input type="url" class="form-control"
                                name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][content_data][url]"
                                required>
                            <div class="invalid-feedback">Please enter a valid URL.</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Estimated Duration (minutes)</label>
                    <input type="number" class="form-control"
                        name="modules[__MODULE_INDEX__][contents][__CONTENT_INDEX__][estimated_duration]"
                        min="0" value="0">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    class CourseCreator {
        constructor() {
            this.moduleCount = 0;
            this.contentCounters = {};
            this.init();
        }

        init() {
            this.bindEvents();
            this.addModule(); // Add first module by default
        }

        bindEvents() {
            // Form submission
            $('#courseForm').on('submit', (e) => this.handleSubmit(e));

            // Add module
            $('#addModuleBtn').on('click', () => this.addModule());

            // Feature image drag & drop
            this.setupFileUpload('#featureImageDropArea', '#featureImage', '#featureImagePreview');

            // Rich text editor buttons
            $('[data-command]').on('click', (e) => this.handleTextEditorCommand(e));

            // Cancel button
            $('#cancelBtn').on('click', () => this.cancelForm());
        }

        addModule() {
            this.moduleCount++;
            const moduleIndex = this.moduleCount;
            this.contentCounters[moduleIndex] = 0; // Initialize content counter for the new module

            const moduleTemplate = $('#moduleTemplate').html()
                .replace(/__INDEX__/g, moduleIndex);

            $('#modulesContainer').append(moduleTemplate);

            // Expand the first module by default
            if (moduleIndex != 1) {
                //remove collapse show from previous module
                $(`[data-module-index="${moduleIndex - 1}"] #moduleDetails${moduleIndex - 1}`).collapse('hide');
            }

            // Bind module events
            this.bindModuleEvents(moduleIndex);

            return moduleIndex;
        }
        bindModuleEvents(moduleIndex) {
            const moduleElement = $(`[data-module-index="${moduleIndex}"]`);

            // Remove module
            moduleElement.find('.remove-module').on('click', () => this.removeModule(moduleIndex));

            // Add content
            moduleElement.find('.add-content-btn').on('click', () => this.addContent(moduleIndex));

            // // Module title update
            // moduleElement.find('.module-title').on('input', (e) => {
            //     moduleElement.find('.module-title-display').text(e.target.value || `Module ${moduleIndex}`);
            // });
        }

        addContent(moduleIndex) {
            this.contentCounters[moduleIndex]++;
            const contentIndex = this.contentCounters[moduleIndex];

            const contentTemplate = $('#contentTemplate').html()
                .replace(/__MODULE_INDEX__/g, moduleIndex)
                .replace(/__CONTENT_INDEX__/g, contentIndex);

            const contentsContainer = $(`[data-module-index="${moduleIndex}"] .contents-container`);
            contentsContainer.append(contentTemplate);

            // Collapse all content sections
            contentsContainer.find('.collapse').collapse('hide');

            // Expand the newly added content section
            const newContentHeader = contentsContainer.find(
                `[data-content-index="${contentIndex}"] .content-header`);
            newContentHeader.trigger('click'); // Trigger click to expand

            // Bind content events
            this.bindContentEvents(moduleIndex, contentIndex);
        }

        bindContentEvents(moduleIndex, contentIndex) {
            const contentElement = $(`[data-module-index="${moduleIndex}"] [data-content-index="${contentIndex}"]`);

            // Remove content
            contentElement.find('.remove-content').on('click', () => this.removeContent(moduleIndex, contentIndex));

            // Content type change
            contentElement.find('.content-type-select').on('change', (e) => this.handleContentTypeChange(e,
                moduleIndex, contentIndex));

            // Video source type change
            contentElement.find('.video-source-type').on('change', (e) => this.handleVideoSourceChange(e,
                moduleIndex, contentIndex));

            // File input changes
            contentElement.find('.custom-file-input').on('change', (e) => this.handleFileInputChange(e));
        }

        handleContentTypeChange(e, moduleIndex, contentIndex) {
            const contentElement = $(`[data-module-index="${moduleIndex}"] [data-content-index="${contentIndex}"]`);
            const selectedOption = $(e.target).find('option:selected');
            const contentType = selectedOption.data('slug');

            // Hide all content type sections
            contentElement.find('.content-type-section').removeClass('active');

            // Show selected content type section
            contentElement.find(`[data-type="${contentType}"]`).addClass('active');

            // Handle video source type visibility
            if (contentType === 'video') {
                this.handleVideoSourceChange(contentElement.find('.video-source-type')[0], moduleIndex,
                    contentIndex);
            }
        }

        handleVideoSourceChange(e, moduleIndex, contentIndex) {
            const contentElement = $(`[data-module-index="${moduleIndex}"] [data-content-index="${contentIndex}"]`);
            const sourceType = $(e.target).val(); // Changed from $(e).val() to $(e.target).val()

            // Hide both sections first
            contentElement.find('.video-url-section').hide();
            contentElement.find('.video-upload-section').hide();

            // Show relevant section based on selection
            if (sourceType === 'url') {
                contentElement.find('.video-url-section').show();
                // Make URL field required when URL option is selected
                contentElement.find('.video-url-section input').prop('required', true);
                contentElement.find('.video-upload-section input').prop('required', false);
            } else if (sourceType === 'upload') {
                contentElement.find('.video-upload-section').show();
                // Make file upload required when upload option is selected
                contentElement.find('.video-upload-section input').prop('required', true);
                contentElement.find('.video-url-section input').prop('required', false);
            }
        }

        removeModule(moduleIndex) {
            if (this.moduleCount > 1) {
                $(`[data-module-index="${moduleIndex}"]`).remove();
                delete this.contentCounters[moduleIndex];

                // Renumber remaining modules
                this.renumberModules();
            } else {
                Swal.fire('At least one module is required.');
            }
        }

        removeContent(moduleIndex, contentIndex) {
            const moduleElement = $(`[data-module-index="${moduleIndex}"]`);
            const contentElement = moduleElement.find(`[data-content-index="${contentIndex}"]`);

            if (moduleElement.find('.content-card').length > 1) {
                contentElement.remove();
                this.renumberContents(moduleIndex);
            } else {
                Swal.fire('Module must have at least one content item.');
            }
        }

        renumberModules() {
            let newIndex = 1;
            $('#modulesContainer .module-card').each((index, element) => {
                const $module = $(element);
                const oldIndex = $module.data('module-index');
                const newModuleIndex = newIndex++;

                $module.attr('data-module-index', newModuleIndex);
                $module.find('.module-title-display').text($module.find('.module-title').val() ||
                    `Module ${newModuleIndex}`);

                // Update all inputs with new index
                $module.find('[name]').each((i, input) => {
                    const name = $(input).attr('name').replace(/modules\[\d+\]/,
                        `modules[${newModuleIndex}]`);
                    $(input).attr('name', name);
                });

                // Update content container
                $module.find('.contents-container').attr('data-module-index', newModuleIndex);

                // Update content counters
                if (this.contentCounters[oldIndex]) {
                    this.contentCounters[newModuleIndex] = this.contentCounters[oldIndex];
                    delete this.contentCounters[oldIndex];
                }
            });

            this.moduleCount = newIndex - 1;
        }

        // Update the renumberContents method to ensure correct indexing
        renumberContents(moduleIndex) {
            let newContentIndex = 1;
            $(`[data-module-index="${moduleIndex}"] .content-card`).each((index, element) => {
                const $content = $(element);
                const newIndex = newContentIndex++;

                $content.attr('data-content-index', newIndex);
                $content.find('.content-header h6').text(`Content ${newIndex}`);

                // Update all inputs with new index
                $content.find('[name]').each((i, input) => {
                    const name = $(input).attr('name').replace(/contents\[\d+\]/,
                        `contents[${newIndex}]`);
                    $(input).attr('name', name);
                });
            });

            this.contentCounters[moduleIndex] = newContentIndex - 1; // Update the content counter for the module
        }


        setupFileUpload(dropAreaSelector, fileInputSelector, previewSelector) {
            const dropArea = $(dropAreaSelector)[0];
            const fileInput = $(fileInputSelector)[0];
            const preview = $(previewSelector)[0];

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropArea.classList.add('dragover');
            }

            function unhighlight() {
                dropArea.classList.remove('dragover');
            }

            dropArea.addEventListener('drop', handleDrop, false);
            dropArea.addEventListener('click', () => fileInput.click(), false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                handleFiles(files);
            }

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            const handleFiles = (files) => {
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML =
                                `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            };
        }

        handleTextEditorCommand(e) {
            e.preventDefault();
            const command = $(e.target).closest('button').data('command');
            const textarea = document.getElementById('courseSummary');

            if (command === 'createLink') {
                const url = prompt('Enter URL:');
                if (url) document.execCommand(command, false, url);
            } else {
                document.execCommand(command, false, null);
            }

            textarea.focus();
        }

        handleSubmit(e) {
            e.preventDefault();

            const $form = $(e.target);
            const $btn = $('#saveBtn');

            // Optional: validate form before submitting
            // if (this.validateForm()) {
            // this.submitForm($form, $btn);

            // Disable button and show loading spinner
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            const formData = new FormData($form[0]);

            $.ajax({
                url: "{{ route('admin.courses.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Course created successfully!',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.href = "{{ route('admin.courses.index') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to create course'
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Course');
                }
            });
            // }
        }


        validateForm() {
            const form = document.getElementById('courseForm');

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                // Scroll to first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalid.focus();
                }
                return false;
            }

            // Validate modules and contents
            let isValid = true;

            $('.module-card').each((index, module) => {
                const moduleElement = $(module);
                const moduleTitle = moduleElement.find('.module-title').val().trim();

                if (!moduleTitle) {
                    moduleElement.find('.module-title').addClass('is-invalid');
                    isValid = false;
                }

                let hasValidContent = false;
                moduleElement.find('.content-card').each((contentIndex, content) => {
                    const contentElement = $(content);
                    const contentTitle = contentElement.find('.content-title').val().trim();
                    const contentType = contentElement.find('.content-type-select').val();

                    if (!contentTitle || !contentType) {
                        contentElement.find('.content-title, .content-type-select').addClass(
                            'is-invalid');
                        isValid = false;
                    } else {
                        hasValidContent = true;
                    }
                });

                if (!hasValidContent) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: `Module "${moduleTitle || 'Untitled'}" must have at least one valid content.`
                    });
                    isValid = false;
                }
            });

            return isValid;
        }

        cancelForm() {
            if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
                window.location.href = '{{ route('admin.courses.index') }}';
            }
        }
    }

    // Initialize the course creator when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        new CourseCreator();
    });

    // Custom file input label update
    $(document).on('change', '.custom-file-input', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').text(fileName || 'Choose file...');
    });
</script>
