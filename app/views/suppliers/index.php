<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container mt-0 pt-3">
    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Suppliers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['total_suppliers']) ? $data['total_suppliers'] : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['active_suppliers']) ? $data['active_suppliers'] : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['pending_suppliers']) ? $data['pending_suppliers'] : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                On Hold
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($data['onhold_suppliers']) ? $data['onhold_suppliers'] : '0'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="row">
        <div class="col-md-6">
            <h1>Suppliers</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/suppliers/add" class="btn btn-primary float-right">
                <i class="fa fa-plus"></i> Add Supplier
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mt-3 mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-search"></i> Search Suppliers
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" id="globalSearch" class="form-control form-control-lg"
                            placeholder="Search by name, contact person, phone, email, address, or GST number...">
                        <div class="input-group-append">
                            <button type="button" id="clearSearch" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Search across all fields simultaneously. Results update as you type.
                    </small>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center h-100">
                        <span class="text-muted">
                            <span id="resultCount"><?php echo count($data['suppliers']); ?></span> suppliers shown
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="theme-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="sortable" data-column="0" style="cursor: pointer; user-select: none;">
                        ID <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th class="sortable" data-column="1" style="cursor: pointer; user-select: none;">
                        Name <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th class="sortable" data-column="2" style="cursor: pointer; user-select: none;">
                        Contact Person <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th class="sortable" data-column="3" style="cursor: pointer; user-select: none;">
                        Phone <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th class="sortable" data-column="4" style="cursor: pointer; user-select: none;">
                        Email <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th class="sortable" data-column="5" style="cursor: pointer; user-select: none;">
                        Address <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th class="sortable" data-column="6" style="cursor: pointer; user-select: none;">
                        GST Number <i class="fas fa-sort text-muted"></i>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['suppliers'])): ?>
                    <?php foreach ($data['suppliers'] as $supplier): ?>
                        <tr>
                            <td><?php echo $supplier->supplier_id; ?></td>
                            <td><?php echo htmlspecialchars($supplier->supplier_name); ?></td>
                            <td><?php echo htmlspecialchars($supplier->contact_person ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($supplier->phone ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($supplier->email ?? '-'); ?></td>
                            <td>
                                <?php
                                $address = $supplier->address ?? '';
                                echo htmlspecialchars($address ? (strlen($address) > 50 ? substr($address, 0, 50) . '...' : $address) : '-');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($supplier->gst_number ?? '-'); ?></td>
                            <td>
                                <a href="<?php echo URLROOT; ?>/suppliers/edit/<?php echo $supplier->supplier_id; ?>"
                                    class="btn btn-dark btn-sm">Edit</a>
                                <form class="d-inline"
                                    action="<?php echo URLROOT; ?>/suppliers/delete/<?php echo $supplier->supplier_id; ?>"
                                    method="post" style="display:inline;">
                                    <input type="submit" value="Delete" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this supplier?')">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No suppliers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div> <!-- End theme-table -->
</div> <!-- End theme-container -->

<style>
    .sortable:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }

    .sortable:active {
        background-color: #e9ecef;
    }

    .sortable i {
        margin-left: 5px;
        font-size: 0.8em;
    }

    .table thead th.sortable {
        position: relative;
        padding-right: 30px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Single global search functionality
        const globalSearch = document.getElementById('globalSearch');
        const clearButton = document.getElementById('clearSearch');
        const tableRows = document.querySelectorAll('tbody tr');
        const resultCount = document.getElementById('resultCount');

        // Add event listeners
        globalSearch.addEventListener('input', filterTable);
        globalSearch.addEventListener('keyup', filterTable);

        // Clear search button
        clearButton.addEventListener('click', function () {
            globalSearch.value = '';
            filterTable();
            globalSearch.focus();
        });

        // Add keyboard shortcut (Ctrl+F or Cmd+F)
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                globalSearch.focus();
                globalSearch.select();
            }

            // Escape key to clear search
            if (e.key === 'Escape' && document.activeElement === globalSearch) {
                globalSearch.value = '';
                filterTable();
                globalSearch.blur();
            }
        });

        function filterTable() {
            const searchTerm = globalSearch.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                // Skip the "no suppliers found" row
                if (row.cells.length === 1 && row.cells[0].colSpan === 8) {
                    return;
                }

                let showRow = false;

                if (searchTerm === '') {
                    // Show all rows if search is empty
                    showRow = true;
                } else {
                    // Search across all relevant columns (skip ID and Actions columns)
                    const searchableColumns = [1, 2, 3, 4, 5, 6]; // Name, Contact, Phone, Email, Address, GST

                    for (let i = 0; i < searchableColumns.length; i++) {
                        const cellIndex = searchableColumns[i];
                        const cellValue = row.cells[cellIndex].textContent.toLowerCase().trim();

                        if (cellValue.includes(searchTerm)) {
                            showRow = true;
                            break; // Found match, no need to check other columns
                        }
                    }
                }

                // Show/hide row with smooth transition
                if (showRow) {
                    row.style.display = '';
                    row.style.opacity = '1';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                    row.style.opacity = '0.5';
                }
            });

            // Update result counter
            resultCount.textContent = visibleCount;

            // Show/hide "no results" message
            const tbody = document.querySelector('tbody');
            let noResultsRow = document.getElementById('noResultsRow');

            if (visibleCount === 0 && searchTerm !== '') {
                // Show "no results found" message
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2"></i><br>
                        <strong>No suppliers found for "${escapeHtml(globalSearch.value)}"</strong><br>
                        <small>Try searching with different keywords</small>
                    </td>
                `;
                    tbody.appendChild(noResultsRow);
                } else {
                    // Update the search term in existing message
                    noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2"></i><br>
                        <strong>No suppliers found for "${escapeHtml(globalSearch.value)}"</strong><br>
                        <small>Try searching with different keywords</small>
                    </td>
                `;
                }
                noResultsRow.style.display = '';
            } else {
                // Hide "no results found" message
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            }

            // Update search box styling based on results
            if (searchTerm !== '') {
                if (visibleCount > 0) {
                    globalSearch.classList.remove('is-invalid');
                    globalSearch.classList.add('is-valid');
                } else {
                    globalSearch.classList.remove('is-valid');
                    globalSearch.classList.add('is-invalid');
                }
            } else {
                globalSearch.classList.remove('is-valid', 'is-invalid');
            }
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Table sorting functionality
        let currentSortColumn = -1;
        let sortDirection = 'asc';

        // Add click handlers to sortable headers
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function () {
                const column = parseInt(this.dataset.column);
                sortTable(column);
                updateSortIcons(column);
            });
        });

        function sortTable(column) {
            const table = document.querySelector('.table tbody');
            const rows = Array.from(table.querySelectorAll('tr')).filter(row => {
                // Exclude "no suppliers found" and "no results" rows
                return row.cells.length > 1 && !row.id;
            });

            // Determine sort direction
            if (currentSortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortDirection = 'asc';
                currentSortColumn = column;
            }

            // Sort rows
            rows.sort((a, b) => {
                let aVal = a.cells[column].textContent.trim();
                let bVal = b.cells[column].textContent.trim();

                // Handle empty values
                if (aVal === '-') aVal = '';
                if (bVal === '-') bVal = '';

                // Special handling for ID column (numeric sort)
                if (column === 0) {
                    aVal = parseInt(aVal) || 0;
                    bVal = parseInt(bVal) || 0;
                    return sortDirection === 'asc' ? aVal - bVal : bVal - aVal;
                }

                // String comparison for other columns
                if (sortDirection === 'asc') {
                    return aVal.localeCompare(bVal, undefined, { numeric: true, sensitivity: 'base' });
                } else {
                    return bVal.localeCompare(aVal, undefined, { numeric: true, sensitivity: 'base' });
                }
            });

            // Re-append sorted rows
            rows.forEach(row => table.appendChild(row));

            // Reapply current search filter after sorting
            filterTable();
        }

        function updateSortIcons(activeColumn) {
            // Reset all icons
            document.querySelectorAll('.sortable i').forEach(icon => {
                icon.className = 'fas fa-sort text-muted';
            });

            // Update active column icon
            const activeHeader = document.querySelector(`[data-column="${activeColumn}"] i`);
            if (sortDirection === 'asc') {
                activeHeader.className = 'fas fa-sort-up text-primary';
            } else {
                activeHeader.className = 'fas fa-sort-down text-primary';
            }
        }

        // Initial focus on search box
        setTimeout(() => {
            globalSearch.focus();
        }, 100);
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>