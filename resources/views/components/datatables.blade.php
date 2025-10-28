<style>
    .action-btn-info {
        background-color: rgba(13, 110, 253, 0.3) !important;
        border: 1px solid rgba(13, 110, 253) !important;
        color: rgb(8, 8, 8) !important;
    }

    .action-btn-danger {
        background-color: rgba(220, 53, 69, 0.3) !important;
        border: 1px solid rgba(220, 53, 69) !important;
        color: rgb(5, 5, 5) !important;
    }

    .action-btn-warning {
        background-color: rgba(255, 193, 7, 0.3) !important;
        border: 1px solid rgba(255, 193, 7) !important;
        color: rgb(7, 7, 7) !important;
    }

    .column-toggle-btn {
        background-color: rgba(108, 117, 125, 0.3) !important;
        border: 1px solid rgba(108, 117, 125) !important;
        color: rgb(8, 8, 8) !important;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .column-toggle-btn.active {
        background-color: rgba(13, 110, 253, 0.6) !important;
        border: 1px solid rgba(13, 110, 253) !important;
    }

    .columns-modal .modal-dialog {
        max-width: 500px;
    }

    .columns-list {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Image thumbnail styles */
    .table-img-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .table-img-thumbnail:hover {
        transform: scale(1.8);
        z-index: 1000;
        position: relative;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .img-placeholder {
        width: 60px;
        height: 60px;
        background-color: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 12px;
    }

    /* Image preview modal */
    .image-preview-modal .modal-dialog {
        max-width: 90%;
        max-height: 90%;
    }

    .image-preview-modal .modal-content {
        background: transparent;
        border: none;
    }

    .image-preview-modal .modal-body {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
    }

    .image-preview-modal .preview-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }

    #dataTable-length-wrapper, .dataTables_length {
        justify-content: center;
        align-items: center;
        padding-top: 5px !important;
    }

    #dataTable-length-wrapper label {
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    #dataTable-length-wrapper select {
        min-width: 60px !important;
        margin-left: 5px;
        margin-right: 5px;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        initializeDataTables();
    });

    function getTableId(table) {
        return $(table).attr('id');
    }

    function initializeDataTables() {
        $('table').each(function() {
            var tableName = getTableId(this);

            if (tableName) {
                var token = getCsrfToken();
                var table = $('#' + tableName).DataTable({
                    dom: getDataTableDom(),
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: true,
                    pageLength: 10,
                    select: true,
                    ajax: getAjaxConfig(token, tableName),
                    columns: getTableColumns(tableName),
                    order: [[0, 'desc']],
                    drawCallback: function(settings) {
                        handleDrawCallback.call(this, settings, tableName);
                        attachRowClickEvent(tableName);
                        attachImagePreviewEvents();
                    },
                    lengthMenu: getLengthMenuOptions(),
                    language: getLanguageOptions(),
                    stateSave: true,
                    stateDuration: 60 * 60 * 24 * 7
                });

                attachSearchInputListener(table, tableName);
                initializeColumnCustomization(table, tableName);
            }
        });
    }

    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function getAjaxConfig(token, tableName) {
        return {
            url: indexUrlBuilder(tableName),
            type: 'GET',
            data: function(d) {
                d.page = d.start / d.length + 1;
                d.search = $('#' + tableName + '_filter input').val();
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader("X-CSRF-TOKEN", token);
            },
            dataSrc: function(json) {
                if (json && json.data && json.data.data) {
                    json.recordsTotal = json.data.meta.total;
                    json.recordsFiltered = json.data.meta.total;
                    return json.data.data;
                }
                return [];
            }
        };
    }

    function getDataTableDom() {
        return `
            <"row py-2"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>
            <"row"<"col-sm-12"tr>>
            <"row py-2"
            <"col-sm-12 col-md-3"i>
            <"col-sm-12 col-md-2"l>
            <"col-sm-12 col-md-7"p>>
        `;
    }

    function getTableColumns(tableName) {
        return [
            getSLColumnConfig(),
            ...getColumns(tableName),
            getActions(tableName)
        ];
    }

    function getColumns(tableName) {
        let table = document.getElementById(tableName);
        if (!table) {
            console.error("Table not found:", tableName);
            return [];
        }

        let columnsData = table.getAttribute("data-fields");
        let imageColumnsData = table.getAttribute("data-image-fields");

        if (!columnsData) {
            console.warn("No data-fields attribute found on table:", tableName);
            return [];
        }

        try {
            let columns = JSON.parse(columnsData);
            let imageColumns = imageColumnsData ? JSON.parse(imageColumnsData) : [];

            if (!Array.isArray(columns)) {
                console.error("Invalid format for data-fields. Expected an array.");
                return [];
            }

            return columns.map(column => {
                const isImageColumn = imageColumns.includes(column);

                let columnConfig = {
                    data: column.trim(),
                    name: column.trim(),
                    title: column.trim().replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
                    visible: true
                };

                // Add custom renderer for image columns
                if (isImageColumn) {
                    columnConfig.render = function(data, type, row) {
                        if (data) {
                            // Check if data is a full URL or just a path
                            const imageUrl = data.startsWith('http') ? data : `/${data.replace(/^\//, '')}`;
                            return `
                                <img src="${imageUrl}"
                                     class="table-img-thumbnail"
                                     alt="${column}"
                                     title="Click to view full image"
                                     data-full-image="${imageUrl}">
                            `;
                        } else {
                            return `<div class="img-placeholder">No Image</div>`;
                        }
                    };
                }

                return columnConfig;
            });

        } catch (error) {
            console.error("Error parsing data-fields:", error);
            return [];
        }
    }

    function indexUrlBuilder(tableName) {
        return $('#' + tableName).data('index-url');
    }

    function viewUrlBuilder(tableName, id) {
        return $('#' + tableName).data('show-url').replace(':id', id);
    }

    function editUrlBuilder(tableName, id) {
        return $('#' + tableName).data('edit-url').replace(':id', id);
    }

    function deleteUrlBuilder(tableName, id) {
        return $('#' + tableName).data('delete-url').replace(':id', id);
    }

    function getActions(tableName) {
        return {
            data: 'id',
            name: 'actions',
            title: 'Actions',
            orderable: false,
            searchable: false,
            visible: true,
            render: function(data, type, row) {
                var view = viewUrlBuilder(tableName, row.id);
                var edit = editUrlBuilder(tableName, row.id);
                var deleteUrl = deleteUrlBuilder(tableName, row.id);
                return `
                <a href="${deleteUrl}" class="btn btn-sm action-btn-danger delete action-btn" data-id="${row.id}" data-action="delete">
                    <span class="default-text"><i class="fa fa-trash"></i></span>
                    <span class="spinner-border spinner-border-sm d-none"></span><span class="">&nbsp;Delete</span>
                </a>
            `;
            }
        };
    }

    function getSLColumnConfig() {
        return {
            title: 'SL',
            data: null,
            orderable: true,
            searchable: false,
            visible: true,
            render: function(data, type, row, meta) {
                var table = $('#' + getTableId(this)).DataTable();
                return generateSLNumber(meta, table);
            }
        };
    }

    function generateSLNumber(meta, table) {
        var pageInfo = table.page.info();
        var total = meta.settings.json.data.meta.total || 0;
        var start = meta.settings.json.data.meta.from - 1 || 0;

        return start + meta.row + 1;
    }

    function handleDrawCallback(settings, tableName) {
        var api = this.api();
        var json = api.ajax.json();
        if (json && json.data && json.data.meta) {
            var meta = json.data.meta;
            $('#' + tableName + '_info').html(
                `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total || 0} entries`
            );
        }
    }

    function getLengthMenuOptions() {
        return [
            [10, 20, 40, 50, 80, 100, 150, 200, 500, -1],
            [10, 20, 40, 50, 80, 100, 150, 200, 500, "All"]
        ];
    }

    function getLanguageOptions() {
        return {
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            },
            emptyTable: "No data available in table",
            loadingRecords: "Loading...",
            processing: "Processing...",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            lengthMenu: "Show _MENU_ entries",
            search: "Search:",
            zeroRecords: "No matching records found"
        };
    }

    function attachSearchInputListener(table, tableName) {
        $('#' + tableName + '_filter input').on('keyup', function() {
            table.ajax.reload();
        });
    }

    // Image Preview Functionality
    function attachImagePreviewEvents() {
        // Remove existing event handlers to prevent duplicates
        $('.table-img-thumbnail').off('click');

        // Attach click event to image thumbnails
        $('.table-img-thumbnail').on('click', function() {
            const fullImageUrl = $(this).data('full-image');
            if (fullImageUrl) {
                $('#imagePreviewModal .preview-image').attr('src', fullImageUrl);
                $('#imagePreviewModal').modal('show');
            }
        });
    }

    // Column Customization Functions
    function initializeColumnCustomization(table, tableName) {
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    text: '<i class="fa fa-cog"></i> Customize Columns',
                    className: 'btn btn-light column-customize-btn',
                    action: function(e, dt, node, config) {
                        showColumnsModal(table, tableName);
                    }
                }
            ]
        });

        table.buttons(0, null).container().appendTo($('#' + tableName + '_wrapper .dt-buttons'));
    }

    function showColumnsModal(table, tableName) {
        var columns = table.columns().header().toArray();
        var columnsList = $('#columnsList');
        columnsList.empty();

        // Skip first (SL) and last (Actions) columns
        for (var i = 1; i < columns.length - 1; i++) {
            var column = table.column(i);
            var header = $(columns[i]);
            var columnTitle = header.text() || 'Column ' + i;
            var isVisible = column.visible();

            var listItem = `
                <div class="list-group-item">
                    <div class="form-check">
                        <input class="form-check-input column-checkbox" type="checkbox"
                               data-column-index="${i}" ${isVisible ? 'checked' : ''}
                               id="column-${i}">
                        <label class="form-check-label" for="column-${i}">
                            ${columnTitle}
                        </label>
                    </div>
                </div>
            `;
            columnsList.append(listItem);
        }

        $('#columnsModal').modal('show');
        attachColumnModalEvents(table);
    }

    function attachColumnModalEvents(table) {
        $('#showAllColumns').off('click').on('click', function() {
            $('.column-checkbox').prop('checked', true);
        });

        $('#hideAllColumns').off('click').on('click', function() {
            $('.column-checkbox').prop('checked', false);
        });

        $('#applyColumns').off('click').on('click', function() {
            $('.column-checkbox').each(function() {
                var columnIndex = $(this).data('column-index');
                var isVisible = $(this).is(':checked');
                table.column(columnIndex).visible(isVisible);
            });

            $('#columnsModal').modal('hide');
            table.draw();
        });
    }

    function attachRowClickEvent(tableName) {
        $('#' + tableName + ' tbody').off("click").on("click", "tr", function(e) {
            // Don't trigger row click if clicking on images or action buttons
            if (!$(e.target).closest('.action-btn').length &&
                !$(e.target).hasClass('table-img-thumbnail') &&
                !$(e.target).closest('.img-placeholder').length) {
                let editButton = $(this).find('.action-btn-warning');
                let editUrl = editButton.attr("href");

                if (editUrl) {
                    window.location.href = editUrl;
                }
            }
        });
    }

    $(document).on("click", ".action-btn", function(e) {
        e.preventDefault();

        let $btn = $(this);
        let action = $btn.data("action");

        $btn.find(".default-text").addClass("d-none");
        $btn.find(".spinner-border").removeClass("d-none");
        $btn.prop("disabled", true);

        if (action === "delete") {
            let id = $btn.data("id");
            deleteItem(id, $btn);
        } else {
            window.location.href = $btn.attr("href");
        }
    });

    function deleteItem(id, button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $(button).attr("href"),
                    type: "DELETE",
                    data: {
                        _token: getCsrfToken()
                    },
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            'Your item has been deleted.',
                            'success'
                        );
                        $('#' + getTableId(button.closest('table'))).DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'An error occurred. Please try again.',
                            'error'
                        );
                        resetButtonState(button);
                    }
                });
            } else {
                resetButtonState(button);
            }
        });
    }

    function resetButtonState(button) {
        button.find(".default-text").removeClass("d-none");
        button.find(".spinner-border").addClass("d-none");
        button.prop("disabled", false);
    }

    $(window).on("pageshow", function(event) {
        $(".action-btn .default-text").removeClass("d-none");
        $(".action-btn .spinner-border").addClass("d-none");
        $(".action-btn").prop("disabled", false);
    });
</script>
