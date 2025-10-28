<div class="px-4 pt-4 pb-2">
<div class="pagination d-flex justify-content-between">
    <div class="d-flex">
        <label for="per_page">Entries per Page:</label>

        <select name="per_page" id="per_page" onchange="updateQueryString()">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>

        <div class="pl-2 dataTables_info">
            Showing
            <span id="showing-entries-from">{{ $query->firstItem() }}</span>
            to
            <span id="showing-entries-to">{{ $query->lastItem() }}</span>
            of
            <span id="total-entries">{{ $query->total() }}</span>
            entries
        </div>
    </div>
    {{ $query->links('components.pagination.default') }}
</div>
</div>