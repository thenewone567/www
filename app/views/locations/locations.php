<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Header -->
    <div class="row align-items-center theme-header">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Inventory
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1><i class="fas fa-map-marker-alt"></i> Warehouse Locations</h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('location_message'); ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-warehouse"></i> Total Locations</h6>
                </div>
                <div class="card-body text-center">
                    <h3 class="mb-0"><?php echo count($data['locations']); ?></h3>
                    <small class="text-muted">Active locations</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-layer-group"></i> Rack Count</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                    $racks = array_unique(array_column($data['locations'], 'rack'));
                    ?>
                    <h3 class="mb-0"><?php echo count($racks); ?></h3>
                    <small class="text-muted">Different racks</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-boxes"></i> Bulk Storage</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                    $bulkLocations = array_filter($data['locations'], function ($loc) {
                        return strpos($loc->location_name, 'B-') === 0;
                    });
                    ?>
                    <h3 class="mb-0"><?php echo count($bulkLocations); ?></h3>
                    <small class="text-muted">Bulk locations</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-archive"></i> Regular Storage</h6>
                </div>
                <div class="card-body text-center">
                    <?php
                    $regularLocations = array_filter($data['locations'], function ($loc) {
                        return strpos($loc->location_name, 'B-') !== 0;
                    });
                    ?>
                    <h3 class="mb-0"><?php echo count($regularLocations); ?></h3>
                    <small class="text-muted">Regular locations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-secondary-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-tools"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/locations/addlocation" class="btn btn-success btn-block">
                                <i class="fa-solid fa-plus"></i> Add New Location
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-info btn-block" data-toggle="modal"
                                data-target="#bulkAddModal">
                                <i class="fa-solid fa-layer-group"></i> Bulk Add Locations
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-light-theme">
                    <h6 class="mb-0"><i class="fas fa-filter"></i> Filter & Search</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" id="searchLocation" class="form-control"
                                placeholder="Search locations...">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select id="filterRack" class="form-control">
                                <option value="">All Racks</option>
                                <?php foreach (array_unique(array_column($data['locations'], 'rack')) as $rack): ?>
                                    <option value="<?php echo htmlspecialchars($rack); ?>">
                                        Rack <?php echo htmlspecialchars($rack); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select id="filterType" class="form-control">
                                <option value="">All Types</option>
                                <option value="bulk">Bulk Storage (B-)</option>
                                <option value="regular">Regular Storage</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> All Locations</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-light mr-2" id="locationCount"><?php echo count($data['locations']); ?>
                            locations</span>
                        <select id="itemsPerPage" class="form-control form-control-sm"
                            style="width: auto; min-width: 100px;">
                            <option value="25">Show 25</option>
                            <option value="50" selected>Show 50</option>
                            <option value="100">Show 100</option>
                            <option value="250">Show 250</option>
                            <option value="all">Show All</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['locations'])): ?>
                        <div class="theme-table">
                            <table class="table table-hover" id="locationsTable">
                                <thead>
                                    <tr>
                                        <th>Location ID</th>
                                        <th>Location Name</th>
                                        <th>Rack</th>
                                        <th>Shelf</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['locations'] as $location): ?>
                                        <tr class="location-row" data-name="<?php echo strtolower($location->location_name); ?>"
                                            data-rack="<?php echo strtolower($location->rack); ?>"
                                            data-type="<?php echo (strpos($location->location_name, 'B-') === 0) ? 'bulk' : 'regular'; ?>">
                                            <td>
                                                <span
                                                    class="badge badge-secondary">#<?php echo $location->location_id; ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($location->location_name); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">Rack
                                                    <?php echo htmlspecialchars($location->rack); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">Shelf
                                                    <?php echo htmlspecialchars($location->shelf); ?></span>
                                            </td>
                                            <td>
                                                <?php if (strpos($location->location_name, 'B-') === 0): ?>
                                                    <span class="badge badge-warning">Bulk Storage</span>
                                                <?php else: ?>
                                                    <span class="badge badge-primary">Regular</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="theme-action-group">
                                                    <a href="<?php echo URLROOT; ?>/locations/editlocation/<?php echo $location->location_id; ?>"
                                                        class="btn btn-outline-primary btn-sm" title="Edit Location">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/locations/viewlocation/<?php echo $location->location_id; ?>"
                                                        class="btn btn-outline-info btn-sm" title="View Items">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="confirmDelete(<?php echo $location->location_id; ?>)"
                                                        title="Delete Location">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Controls -->
                        <div class="d-flex justify-content-between align-items-center mt-3" id="paginationControls">
                            <div>
                                <small class="text-muted" id="showingInfo">Showing 1-50 of 50 locations</small>
                            </div>
                            <div>
                                <nav aria-label="Location pagination">
                                    <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                        <!-- Pagination buttons will be generated by JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fa-solid fa-info-circle fa-3x mb-3"></i>
                            <h5>No Warehouse Locations Found</h5>
                            <p>Start by adding your first warehouse location to organize your inventory.</p>
                            <a href="<?php echo URLROOT; ?>/locations/addlocation" class="btn btn-success">
                                <i class="fa-solid fa-plus"></i> Add First Location
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Add Modal -->
<div class="modal fade" id="bulkAddModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-layer-group"></i> Bulk Add Locations</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkAddForm">
                    <div class="form-group">
                        <label>Location Name Pattern</label>
                        <input type="text" class="form-control" id="namePattern"
                            placeholder="e.g., A1- (will create A1-1, A1-2, etc.)">
                    </div>
                    <div class="form-group">
                        <label>Rack</label>
                        <input type="text" class="form-control" id="rackName" placeholder="e.g., A">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Number</label>
                                <input type="number" class="form-control" id="startNum" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Number</label>
                                <input type="number" class="form-control" id="endNum" value="10" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> This will create multiple locations with sequential
                            numbers.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="bulkAddLocations()">
                    <i class="fas fa-plus"></i> Create Locations
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for filtering and functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchLocation');
        const rackFilter = document.getElementById('filterRack');
        const typeFilter = document.getElementById('filterType');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const itemsPerPageSelect = document.getElementById('itemsPerPage');
        const locationRows = document.querySelectorAll('.location-row');
        const locationCount = document.getElementById('locationCount');
        const showingInfo = document.getElementById('showingInfo');
        const paginationNav = document.getElementById('paginationNav');

        let currentPage = 1;
        let itemsPerPage = 50;
        let filteredRows = Array.from(locationRows);

        function filterLocations() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedRack = rackFilter.value.toLowerCase();
            const selectedType = typeFilter.value;

            filteredRows = Array.from(locationRows).filter(row => {
                const name = row.dataset.name;
                const rack = row.dataset.rack;
                const type = row.dataset.type;

                const matchesSearch = !searchTerm || name.includes(searchTerm);
                const matchesRack = !selectedRack || rack === selectedRack;
                const matchesType = !selectedType || type === selectedType;

                return matchesSearch && matchesRack && matchesType;
            });

            currentPage = 1; // Reset to first page when filtering
            applyPagination();
        }

        function applyPagination() {
            const totalItems = filteredRows.length;
            const totalPages = itemsPerPage === 'all' ? 1 : Math.ceil(totalItems / itemsPerPage);

            // Hide all rows first
            locationRows.forEach(row => row.style.display = 'none');

            // Show filtered and paginated rows
            if (itemsPerPage === 'all') {
                filteredRows.forEach(row => row.style.display = '');
                showingInfo.textContent = `Showing all ${totalItems} locations`;
            } else {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, totalItems);

                for (let i = startIndex; i < endIndex; i++) {
                    if (filteredRows[i]) {
                        filteredRows[i].style.display = '';
                    }
                }

                const showingStart = totalItems > 0 ? startIndex + 1 : 0;
                const showingEnd = endIndex;
                showingInfo.textContent = `Showing ${showingStart}-${showingEnd} of ${totalItems} locations`;
            }

            locationCount.textContent = `${totalItems} locations`;
            generatePagination(totalPages, totalItems);
        }

        function generatePagination(totalPages, totalItems) {
            paginationNav.innerHTML = '';

            if (itemsPerPage === 'all' || totalPages <= 1) {
                return;
            }

            // Previous button
            const prevBtn = createPaginationButton('Previous', currentPage > 1, () => {
                if (currentPage > 1) {
                    currentPage--;
                    applyPagination();
                }
            });
            paginationNav.appendChild(prevBtn);

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            if (startPage > 1) {
                const firstBtn = createPaginationButton('1', true, () => {
                    currentPage = 1;
                    applyPagination();
                });
                paginationNav.appendChild(firstBtn);

                if (startPage > 2) {
                    const ellipsis = document.createElement('li');
                    ellipsis.className = 'page-item disabled';
                    ellipsis.innerHTML = '<span class="page-link">...</span>';
                    paginationNav.appendChild(ellipsis);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = createPaginationButton(i.toString(), true, () => {
                    currentPage = i;
                    applyPagination();
                }, i === currentPage);
                paginationNav.appendChild(pageBtn);
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('li');
                    ellipsis.className = 'page-item disabled';
                    ellipsis.innerHTML = '<span class="page-link">...</span>';
                    paginationNav.appendChild(ellipsis);
                }

                const lastBtn = createPaginationButton(totalPages.toString(), true, () => {
                    currentPage = totalPages;
                    applyPagination();
                });
                paginationNav.appendChild(lastBtn);
            }

            // Next button
            const nextBtn = createPaginationButton('Next', currentPage < totalPages, () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    applyPagination();
                }
            });
            paginationNav.appendChild(nextBtn);
        }

        function createPaginationButton(text, enabled, onClick, isActive = false) {
            const li = document.createElement('li');
            li.className = `page-item ${!enabled ? 'disabled' : ''} ${isActive ? 'active' : ''}`;

            const button = document.createElement('button');
            button.className = 'page-link';
            button.textContent = text;
            button.disabled = !enabled;

            if (enabled && onClick) {
                button.addEventListener('click', onClick);
            }

            li.appendChild(button);
            return li;
        }

        // Event listeners
        searchInput.addEventListener('input', filterLocations);
        rackFilter.addEventListener('change', filterLocations);
        typeFilter.addEventListener('change', filterLocations);

        itemsPerPageSelect.addEventListener('change', function () {
            itemsPerPage = this.value === 'all' ? 'all' : parseInt(this.value);
            currentPage = 1;
            applyPagination();
        });

        clearFiltersBtn.addEventListener('click', function () {
            searchInput.value = '';
            rackFilter.value = '';
            typeFilter.value = '';
            filterLocations();
        });

        // Initialize pagination
        filterLocations();
    });

    function confirmDelete(locationId) {
        if (confirm('Are you sure you want to delete this location? This action cannot be undone.')) {
            window.location.href = `<?php echo URLROOT; ?>/locations/deletelocation/${locationId}`;
        }
    }

    function bulkAddLocations() {
        const pattern = document.getElementById('namePattern').value;
        const rack = document.getElementById('rackName').value;
        const start = parseInt(document.getElementById('startNum').value);
        const end = parseInt(document.getElementById('endNum').value);

        if (!pattern || !rack || start > end) {
            alert('Please fill all fields correctly.');
            return;
        }

        // Here you would implement the bulk add functionality
        // For now, just show a message
        alert(`Would create ${end - start + 1} locations from ${pattern}${start} to ${pattern}${end} in rack ${rack}`);
        $('#bulkAddModal').modal('hide');
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>