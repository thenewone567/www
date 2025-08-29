<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<?php
// Defensive programming - ensure data structure is correct
$stats = isset($data['stats']) && is_object($data['stats']) ? $data['stats'] : (object) [
    'total_locations'     => 0,
    'dock_locations'      => 0,
    'receiving_locations' => 0,
    'storage_locations'   => 0,
    'bin_locations'       => 0
];

$locations = isset($data['locations']) && is_array($data['locations']) ? $data['locations'] : [];
$locationsByType = isset($data['locationsByType']) && is_array($data['locationsByType']) ? $data['locationsByType'] : [];
?>

<!-- Theme System Styles -->
<?php
/**
 * Parse location code according to various formats
 * Supports: s1-a1-c3, w1-a1-c1, S1-B15-C3, A01-A02-01, etc.
 */
function parseLocationCode($code)
{
    if (empty($code)) {
        return null;
    }

    $code = trim($code);

    // Handle format: s1-a1-c3 or w1-a1-c1 (section-aisle-column)
    if (preg_match('/^([sw])(\d+)-([a-zA-Z])(\d+)-([a-zA-Z])(\d+)$/i', $code, $matches)) {
        return [
            'section' => $matches[2],
            'aisle'   => strtoupper($matches[3]),
            'rack'    => $matches[4],
            'column'  => strtoupper($matches[5]),
            'bin'     => $matches[6],
            'format'  => 'section_aisle_column'
        ];
    }

    // Handle format: S1-B15-C3 (shop-aisle+rack-column+bin)
    if (preg_match('/^S(\d+)-([A-Z])(\d+)-([A-Z])(\d+)$/i', $code, $matches)) {
        return [
            'section' => $matches[1],
            'aisle'   => $matches[2],
            'rack'    => $matches[3],
            'column'  => $matches[4],
            'bin'     => $matches[5],
            'format'  => 'shop_format'
        ];
    }

    // Handle legacy format: A01-A02-01 (aisle-shelf-bin)
    if (preg_match('/^([A-Z])(\d+)-([A-Z])(\d+)-(\d+)$/i', $code, $matches)) {
        return [
            'section' => '1', // Default section
            'aisle'   => $matches[1],
            'rack'    => $matches[2],
            'column'  => $matches[3],
            'bin'     => $matches[5],
            'format'  => 'legacy'
        ];
    }

    // Handle simple codes like DOCK-A, RCV-01, BULK-A
    return [
        'simple' => $code,
        'format' => 'simple'
    ];
}

/**
 * Format location display according to the parsed code
 */
function formatLocationDisplay($location)
{
    $parsed = parseLocationCode($location->standardized_address ?? $location->location_code);

    if (!$parsed) {
        return [
            'code'        => $location->location_code,
            'description' => $location->location_name,
            'badge'       => getBadgeType($location)
        ];
    }

    switch ($parsed['format']) {
        case 'section_aisle_column':
        case 'shop_format':
            $formattedCode = "S{$parsed['section']}-{$parsed['aisle']}{$parsed['rack']}-{$parsed['column']}{$parsed['bin']}";
            $description = "Section {$parsed['section']}, Aisle {$parsed['aisle']}, Rack {$parsed['rack']}, Bin {$parsed['column']}{$parsed['bin']}";
            return [
                'code'        => $formattedCode,
                'description' => $description,
                'badge'       => 'Shop'
            ];

        case 'legacy':
            $formattedCode = "S{$parsed['section']}-{$parsed['aisle']}{$parsed['rack']}-{$parsed['column']}{$parsed['bin']}";
            $description = "Section {$parsed['section']}, Aisle {$parsed['aisle']}, Rack {$parsed['rack']}, Bin {$parsed['column']}{$parsed['bin']}";
            return [
                'code'        => $formattedCode,
                'description' => $description,
                'badge'       => 'Shop'
            ];

        case 'simple':
        default:
            return [
                'code'        => $parsed['simple'],
                'description' => $location->location_name,
                'badge'       => getBadgeType($location)
            ];
    }
}

/**
 * Get appropriate badge text based on location type and code
 */
function getBadgeType($location)
{
    // Check if location code starts with patterns that indicate storage locations
    $code = $location->standardized_address ?? $location->location_code;
    if (
        preg_match('/^[sw]\d+-[a-zA-Z]\d+-[a-zA-Z]\d+$/i', $code) ||
        preg_match('/^S\d+-[A-Z]\d+-[A-Z]\d+$/i', $code)
    ) {
        return 'Shop';
    }

    switch ($location->location_type) {
        case 'dock':
            return 'Dock';
        case 'receiving':
            return 'Receiving';
        case 'storage':
            return 'Storage';
        case 'bin':
            return 'Bin';
        default:
            return ucfirst($location->location_type);
    }
}
?>

<!-- Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-map-marker-alt mr-2"></i>Warehouse Locations
                    </h1>
                    <p class="text-muted">Manage warehouse locations and storage areas</p>
                </div>
                <div>
                    <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
                    </a>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addLocationModal">
                        <i class="fas fa-plus mr-2"></i>Add Location
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('inventory_message'); ?>

            <!-- Statistics Cards -->
            <div class="stats-grid-5 mb-4">
                <div class="stats-card">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $stats->total_locations ?? 0; ?></h4>
                        <small class="text-muted">Total Locations</small>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="card-body text-center">
                        <div class="text-danger mb-2">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $stats->dock_locations ?? 0; ?></h4>
                        <small class="text-muted">Dock Doors</small>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="fas fa-inbox fa-2x"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $stats->receiving_locations ?? 0; ?></h4>
                        <small class="text-muted">Receiving Areas</small>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="fas fa-warehouse fa-2x"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $stats->storage_locations ?? 0; ?></h4>
                        <small class="text-muted">Storage Areas</small>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $stats->bin_locations ?? 0; ?></h4>
                        <small class="text-muted">Bin Locations</small>
                    </div>
                </div>
            </div>

            <!-- Location Types Tabs -->
            <div class="card theme-card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="locationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                <i class="fas fa-list mr-2"></i>All Locations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="dock-tab" data-toggle="tab" href="#dock" role="tab">
                                <i class="fas fa-truck mr-2"></i>Dock Doors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="receiving-tab" data-toggle="tab" href="#receiving" role="tab">
                                <i class="fas fa-inbox mr-2"></i>Receiving
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="storage-tab" data-toggle="tab" href="#storage" role="tab">
                                <i class="fas fa-warehouse mr-2"></i>Storage
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="bins-tab" data-toggle="tab" href="#bins" role="tab">
                                <i class="fas fa-box mr-2"></i>Bins
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="locationTabContent">
                        <!-- All Locations Tab -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div class="location-grid">
                                <?php if (!empty($locations) && is_array($locations)): ?>
                                    <?php foreach ($locations as $location): ?>
                                        <?php if (is_object($location)): ?>
                                            <?php $displayInfo = formatLocationDisplay($location); ?>
                                            <div class="theme-card location-card location-type-<?php echo $location->location_type; ?> clickable-card"
                                                data-location-id="<?php echo $location->location_id; ?>"
                                                data-location-code="<?php echo htmlspecialchars($displayInfo['code']); ?>"
                                                data-location-name="<?php echo htmlspecialchars($location->location_name); ?>"
                                                style="cursor: pointer;"
                                                onclick="showLocationItems(<?php echo $location->location_id; ?>, '<?php echo htmlspecialchars($displayInfo['code']); ?>', '<?php echo htmlspecialchars($location->location_name); ?>')">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="flex-grow-1">
                                                            <h6 class="card-title mb-0">
                                                                <strong><?php echo htmlspecialchars($displayInfo['code']); ?></strong>
                                                            </h6>
                                                        </div>
                                                        <div class="text-right">
                                                            <span
                                                                class="badge location-badge mb-1
                                                            <?php echo $location->location_type == 'dock' ? 'badge-danger' :
                                                                ($location->location_type == 'receiving' ? 'badge-info' :
                                                                    ($location->location_type == 'storage' ? 'badge-success' : 'badge-warning')); ?>">
                                                                <?php echo htmlspecialchars($displayInfo['badge']); ?>
                                                            </span>
                                                            <?php if (isset($location->item_count) && $location->item_count > 0): ?>
                                                                <div class="small text-muted">
                                                                    <i class="fas fa-boxes"></i>
                                                                    <?php echo number_format($location->item_count); ?> items
                                                                </div>
                                                                <div class="small text-muted">
                                                                    <i class="fas fa-cubes"></i>
                                                                    <?php echo number_format($location->total_quantity ?? 0); ?> qty
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="small text-muted">
                                                                    <i class="fas fa-inbox"></i> Empty
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <p class="card-text text-muted mb-2">
                                                        <?php
                                                        // Use clean name for storage locations, original name for others
                                                        if (preg_match('/^[sw]\d+-[a-zA-Z]\d+-[a-zA-Z]\d+$/i', $location->standardized_address ?? $location->location_code)) {
                                                            echo "Storage Location";
                                                        } else {
                                                            echo htmlspecialchars($location->location_name);
                                                        }
                                                        ?>
                                                    </p>
                                                    <div class="small text-muted">
                                                        <div><strong>Location:</strong>
                                                            <?php echo htmlspecialchars($displayInfo['description']); ?></div>
                                                        <?php if ($location->zone): ?>
                                                            <div><strong>Store Location:</strong> <?php echo htmlspecialchars($location->zone); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($location->capacity_cubic_feet): ?>
                                                            <div><strong>Capacity:</strong>
                                                                <?php echo number_format($location->capacity_cubic_feet, 1); ?> ft³
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($location->climate_controlled): ?>
                                                            <div><span class="badge badge-info badge-sm">Climate Controlled</span></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No locations found</h5>
                                            <p class="text-muted">Add your first warehouse location to get started.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Individual Type Tabs -->
                        <?php
                        $types = [
                            'dock'      => ['icon' => 'truck', 'color' => 'danger'],
                            'receiving' => ['icon' => 'inbox', 'color' => 'info'],
                            'storage'   => ['icon' => 'warehouse', 'color' => 'success'],
                            'bin'       => ['icon' => 'box', 'color' => 'warning']
                        ];
                        ?>

                        <?php foreach ($types as $type => $config): ?>
                            <div class="tab-pane fade" id="<?php echo $type == 'bin' ? 'bins' : $type; ?>" role="tabpanel">
                                <div class="location-grid">
                                    <?php
                                    $typeLocations = isset($locationsByType[$type]) && is_array($locationsByType[$type]) ? $locationsByType[$type] : [];
                                    if (!empty($typeLocations)): ?>
                                        <?php foreach ($typeLocations as $location): ?>
                                            <?php if (is_object($location)): ?>
                                                <?php $displayInfo = formatLocationDisplay($location); ?>
                                                <div class="theme-card location-card location-type-<?php echo $location->location_type; ?> clickable-card"
                                                    data-location-id="<?php echo $location->location_id; ?>"
                                                    data-location-code="<?php echo htmlspecialchars($displayInfo['code']); ?>"
                                                    data-location-name="<?php echo htmlspecialchars($location->location_name); ?>"
                                                    style="cursor: pointer;"
                                                    onclick="showLocationItems(<?php echo $location->location_id; ?>, '<?php echo htmlspecialchars($displayInfo['code']); ?>', '<?php echo htmlspecialchars($location->location_name); ?>')">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div class="flex-grow-1">
                                                                <h6 class="card-title mb-0">
                                                                    <strong><?php echo htmlspecialchars($displayInfo['code']); ?></strong>
                                                                </h6>
                                                            </div>
                                                            <div class="text-right">
                                                                <span
                                                                    class="badge badge-<?php echo $config['color']; ?> location-badge mb-1">
                                                                    <?php echo htmlspecialchars($displayInfo['badge']); ?>
                                                                </span>
                                                                <?php if (isset($location->item_count) && $location->item_count > 0): ?>
                                                                    <div class="small text-muted">
                                                                        <i class="fas fa-boxes"></i>
                                                                        <?php echo number_format($location->item_count); ?> items
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        <i class="fas fa-cubes"></i>
                                                                        <?php echo number_format($location->total_quantity ?? 0); ?> qty
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="small text-muted">
                                                                        <i class="fas fa-inbox"></i> Empty
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <p class="card-text text-muted mb-2">
                                                            <?php
                                                            // Use clean name for storage locations, original name for others
                                                            if (preg_match('/^[sw]\d+-[a-zA-Z]\d+-[a-zA-Z]\d+$/i', $location->standardized_address ?? $location->location_code)) {
                                                                echo "Storage Location";
                                                            } else {
                                                                echo htmlspecialchars($location->location_name);
                                                            }
                                                            ?>
                                                        </p>
                                                        <div class="small text-muted">
                                                            <div><strong>Location:</strong>
                                                                <?php echo htmlspecialchars($displayInfo['description']); ?></div>
                                                            <?php if ($location->zone): ?>
                                                                <div><strong>Store Location:</strong> <?php echo htmlspecialchars($location->zone); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($location->capacity_cubic_feet): ?>
                                                                <div><strong>Capacity:</strong>
                                                                    <?php echo number_format($location->capacity_cubic_feet, 1); ?> ft³
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($location->climate_controlled): ?>
                                                                <div><span class="badge badge-info badge-sm">Climate Controlled</span></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-<?php echo $config['icon']; ?> fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No <?php echo $type; ?> locations found</h5>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Items Modal -->
<div class="modal fade" id="locationItemsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content theme-card">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-boxes mr-2"></i>
                    Items in <span id="modalLocationCode"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="locationItemsLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Loading items...</p>
                </div>
                <div id="locationItemsContent" style="display: none;">
                    <div class="mb-3">
                        <strong>Location:</strong> <span id="modalLocationName"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Total Value</th>
                                </tr>
                            </thead>
                            <tbody id="locationItemsTableBody">
                                <!-- Items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="locationItemsSummary" class="mt-3 p-3 bg-light rounded">
                        <!-- Summary will be loaded here -->
                    </div>
                </div>
                <div id="locationItemsEmpty" style="display: none;" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Items Found</h5>
                    <p class="text-muted">This location currently has no inventory items.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-larger" role="document">
        <div class="modal-content theme-card">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>Create New Locations
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addLocationForm" method="POST" action="<?php echo URLROOT; ?>/inventory/addLocationRange">
                <div class="modal-body">
                    <!-- Creation Mode Selector -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="theme-card bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-cogs mr-2"></i>Creation Mode
                                    </h6>
                                    <div class="creation-mode-selector">
                                        <div class="form-check form-check-inline mode-option">
                                            <input class="form-check-input" type="radio" name="creation_mode"
                                                id="single_mode" value="single" checked>
                                            <label class="form-check-label" for="single_mode">
                                                <div class="mode-card">
                                                    <i class="fas fa-map-marker-alt fa-2x text-success mb-2"></i>
                                                    <h6>Single Location</h6>
                                                    <small class="text-muted">Create one location at a time</small>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline mode-option">
                                            <input class="form-check-input" type="radio" name="creation_mode"
                                                id="range_mode" value="range">
                                            <label class="form-check-label" for="range_mode">
                                                <div class="mode-card">
                                                    <i class="fas fa-layer-group fa-2x text-info mb-2"></i>
                                                    <h6>Bulk Creation</h6>
                                                    <small class="text-muted">Create multiple locations in
                                                        ranges</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Type and Subtype -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_type" class="form-label">
                                    <i class="fas fa-tags mr-1"></i>Location Category *
                                </label>
                                <select class="form-control form-control-lg" id="location_type" name="location_type"
                                    required>
                                    <option value="">Select Category</option>
                                    <option value="storage">📦 Storage Areas</option>
                                    <option value="bin">🗃️ Bin Locations</option>
                                    <option value="receiving">📥 Receiving Areas</option>
                                    <option value="dock">🚛 Dock Doors</option>
                                    <option value="shipping">📤 Shipping Areas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_subtype" class="form-label">
                                    <i class="fas fa-layer-group mr-1"></i>Storage Type
                                </label>
                                <select class="form-control form-control-lg" id="location_subtype"
                                    name="location_subtype">
                                    <option value="general">🏪 General Storage</option>
                                    <option value="bulk">📦 Bulk Storage</option>
                                    <option value="cold">❄️ Cold Storage</option>
                                    <option value="hazmat">⚠️ Hazmat Storage</option>
                                    <option value="high_value">💎 High Value Items</option>
                                    <option value="oversized">📏 Oversized Items</option>
                                </select>
                                <small class="form-text text-muted">Only applies to storage/bin locations</small>
                            </div>
                        </div>
                    </div>

                    <!-- Location Format Guide -->
                    <div class="alert alert-info border-left-info">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-info-circle mr-2 fa-lg text-info"></i>
                            <h6 class="mb-0">Location Format Guide: S1-B15-C3</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="format-example">
                                    <span class="badge badge-primary">S1</span>
                                    <strong>Shop/Section 1</strong><br>
                                    <small class="text-muted">Building or major area</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="format-example">
                                    <span class="badge badge-success">B15</span>
                                    <strong>Aisle B, Rack 15</strong><br>
                                    <small class="text-muted">Aisles A-Z, Racks 1-99</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="format-example">
                                    <span class="badge badge-warning">C3</span>
                                    <strong>Column C, Bin 3</strong><br>
                                    <small class="text-muted">Columns A-Z, Bins 1-99</small>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-info">
                                <i class="fas fa-store mr-1"></i>
                                <strong>Store Locations:</strong> Each location will be assigned to one of your store locations (Kurukshetra, Ambala, or Panchkula) for multi-location inventory management.
                            </small>
                        </div>
                    </div>

                    <!-- Single Location Form -->
                    <div id="single_form" class="location-form">
                        <div class="theme-card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Single Location Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="single_section" class="form-label">
                                                <i class="fas fa-building mr-1"></i>Shop/Section *
                                            </label>
                                            <input type="text" class="form-control form-control-lg" id="single_section"
                                                name="single_section" placeholder="S1" pattern="[Ss]\d+" maxlength="10">
                                            <small class="form-text text-muted">Format: S1, S2, etc.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="single_aisle_rack" class="form-label">
                                                <i class="fas fa-road mr-1"></i>Aisle/Rack *
                                            </label>
                                            <input type="text" class="form-control form-control-lg"
                                                id="single_aisle_rack" name="single_aisle_rack" placeholder="B15"
                                                pattern="[A-Za-z]\d+" maxlength="10">
                                            <small class="form-text text-muted">Format: A1, B15, C99, etc.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="single_column_bin" class="form-label">
                                                <i class="fas fa-box mr-1"></i>Column/Bin *
                                            </label>
                                            <input type="text" class="form-control form-control-lg"
                                                id="single_column_bin" name="single_column_bin" placeholder="C3"
                                                pattern="[A-Za-z]\d+" maxlength="10">
                                            <small class="form-text text-muted">Format: A1, C3, D5, etc.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="single_location_name" class="form-label">
                                                <i class="fas fa-tag mr-1"></i>Location Name *
                                            </label>
                                            <input type="text" class="form-control form-control-lg"
                                                id="single_location_name" name="single_location_name"
                                                placeholder="Auto-generated based on format">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="single_zone" class="form-label">
                                                <i class="fas fa-map-marker-alt mr-1"></i>Store Location
                                            </label>
                                            <select class="form-control form-control-lg" id="single_zone" name="single_zone">
                                                <option value="">Select Store Location</option>
                                                <!-- Add new store locations here as you expand -->
                                                <option value="Kurukshetra">🏪 Kurukshetra Store</option>
                                                <option value="Ambala">🏪 Ambala Store</option>
                                                <option value="Panchkula">🏪 Panchkula Store</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Range Creation Form -->
                    <div id="range_form" class="location-form" style="display: none;">
                        <div class="theme-card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-layer-group mr-2"></i>Location Range Creation
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="theme-card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-play mr-2"></i>Start of Range
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="start_section" class="form-label">
                                                        <i class="fas fa-building mr-1"></i>Shop/Section *
                                                    </label>
                                                    <input type="text" class="form-control" id="start_section"
                                                        name="start_section" placeholder="S1" pattern="[Ss]\d+"
                                                        maxlength="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="start_aisle_rack" class="form-label">
                                                        <i class="fas fa-road mr-1"></i>Aisle/Rack *
                                                    </label>
                                                    <input type="text" class="form-control" id="start_aisle_rack"
                                                        name="start_aisle_rack" placeholder="A1" pattern="[A-Za-z]\d+"
                                                        maxlength="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="start_column_bin" class="form-label">
                                                        <i class="fas fa-box mr-1"></i>Column/Bin *
                                                    </label>
                                                    <input type="text" class="form-control" id="start_column_bin"
                                                        name="start_column_bin" placeholder="A1" pattern="[A-Za-z]\d+"
                                                        maxlength="10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="theme-card border-danger">
                                            <div class="card-header bg-danger text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-stop mr-2"></i>End of Range
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="end_section" class="form-label">
                                                        <i class="fas fa-building mr-1"></i>Shop/Section *
                                                    </label>
                                                    <input type="text" class="form-control" id="end_section"
                                                        name="end_section" placeholder="S1" pattern="[Ss]\d+"
                                                        maxlength="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="end_aisle_rack" class="form-label">
                                                        <i class="fas fa-road mr-1"></i>Aisle/Rack *
                                                    </label>
                                                    <input type="text" class="form-control" id="end_aisle_rack"
                                                        name="end_aisle_rack" placeholder="D20" pattern="[A-Za-z]\d+"
                                                        maxlength="10">
                                                </div>
                                                <div class="form-group">
                                                    <label for="end_column_bin" class="form-label">
                                                        <i class="fas fa-box mr-1"></i>Column/Bin *
                                                    </label>
                                                    <input type="text" class="form-control" id="end_column_bin"
                                                        name="end_column_bin" placeholder="C5" pattern="[A-Za-z]\d+"
                                                        maxlength="10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range Preview -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="theme-card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-eye mr-2"></i>Range Preview
                                                </h6>
                                                <div id="range_preview" class="text-muted bg-white p-3 rounded border">
                                                    📋 Fill in range values to see preview...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Common Properties -->
                    <div class="theme-card mt-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-cog mr-2"></i>Location Properties
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="capacity_cubic_feet" class="form-label">
                                            <i class="fas fa-cube mr-1"></i>Capacity (ft³)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control"
                                                id="capacity_cubic_feet" name="capacity_cubic_feet" placeholder="100.00"
                                                value="100">
                                            <div class="input-group-append">
                                                <span class="input-group-text">ft³</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_weight_kg" class="form-label">
                                            <i class="fas fa-weight-hanging mr-1"></i>Max Weight (kg)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="max_weight_kg"
                                                name="max_weight_kg" placeholder="500.00" value="500">
                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label d-block">
                                            <i class="fas fa-thermometer-half mr-1"></i>Special Features
                                        </label>
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input type="checkbox" class="custom-control-input" id="climate_controlled"
                                                name="climate_controlled" value="1">
                                            <label class="custom-control-label" for="climate_controlled">
                                                <i class="fas fa-snowflake mr-1"></i>Climate Controlled
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-sticky-note mr-1"></i>Notes
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                    placeholder="Additional information about these locations (optional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i><span id="submit_text">Create Location</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to show location items modal
    function showLocationItems(locationId, locationCode, locationName) {
        console.log('showLocationItems called with:', locationId, locationCode, locationName);

        // Check if modal exists
        const modal = document.getElementById('locationItemsModal');
        if (!modal) {
            console.error('Modal not found!');
            alert('Modal not found. Please refresh the page.');
            return;
        }

        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            console.error('jQuery not loaded!');
            alert('jQuery not loaded. Please refresh the page.');
            return;
        }

        // Show the modal
        $('#locationItemsModal').modal('show');

        // Update modal title and content
        document.getElementById('modalLocationCode').textContent = locationCode;
        document.getElementById('modalLocationName').textContent = locationName;

        // Show loading state
        document.getElementById('locationItemsLoading').style.display = 'block';
        document.getElementById('locationItemsContent').style.display = 'none';
        document.getElementById('locationItemsEmpty').style.display = 'none';

        // Fetch location items
        const apiUrl = '<?= URLROOT ?>/api/getLocationItems.php';
        console.log('Fetching from:', apiUrl);

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ location_id: locationId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateLocationItems(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load location items');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading location items: ' + error.message);
                $('#locationItemsModal').modal('hide');
            });
    }

    // Function to populate location items in modal
    function populateLocationItems(data) {
        document.getElementById('locationItemsLoading').style.display = 'none';

        if (data.items.length === 0) {
            document.getElementById('locationItemsEmpty').style.display = 'block';
            return;
        }

        document.getElementById('locationItemsContent').style.display = 'block';

        // Populate items table
        const tableBody = document.getElementById('locationItemsTableBody');
        tableBody.innerHTML = '';

        data.items.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${escapeHtml(item.product_name)}</strong>
                    ${item.category_name !== 'Uncategorized' ? '<br><small class="text-muted">' + escapeHtml(item.category_name) + '</small>' : ''}
                </td>
                <td>${escapeHtml(item.sku)}</td>
                <td class="text-right"><strong>${item.quantity.toLocaleString()}</strong></td>
                <td class="text-right">$${item.selling_price.toFixed(2)}</td>
                <td class="text-right"><strong>$${item.total_selling_value.toFixed(2)}</strong></td>
            `;
            tableBody.appendChild(row);
        });

        // Populate summary
        const summary = data.summary;
        document.getElementById('locationItemsSummary').innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="mb-0">${summary.total_items}</h5>
                        <small class="text-muted">Product Types</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="mb-0">${summary.total_quantity.toLocaleString()}</h5>
                        <small class="text-muted">Total Quantity</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="mb-0">$${summary.total_cost_value.toFixed(2)}</h5>
                        <small class="text-muted">Cost Value</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h5 class="mb-0">$${summary.total_selling_value.toFixed(2)}</h5>
                        <small class="text-muted">Selling Value</small>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Form mode switching
        const singleMode = document.getElementById('single_mode');
        const rangeMode = document.getElementById('range_mode');
        const singleForm = document.getElementById('single_form');
        const rangeForm = document.getElementById('range_form');
        const submitText = document.getElementById('submit_text');

        function switchMode() {
            console.log('switchMode called, rangeMode.checked:', rangeMode.checked);

            if (rangeMode.checked) {
                console.log('Showing range form, hiding single form');
                singleForm.style.display = 'none';
                rangeForm.style.display = 'block';
                submitText.textContent = 'Create Location Range';
            } else {
                console.log('Showing single form, hiding range form');
                singleForm.style.display = 'block';
                rangeForm.style.display = 'none';
                submitText.textContent = 'Create Location';
            }
        }

        singleMode.addEventListener('change', switchMode);
        rangeMode.addEventListener('change', switchMode);

        // Debug: Check if elements exist
        console.log('Form elements found:', {
            singleMode: !!singleMode,
            rangeMode: !!rangeMode,
            singleForm: !!singleForm,
            rangeForm: !!rangeForm,
            submitText: !!submitText
        });

        // Storage subtype visibility
        const locationTypeSelect = document.getElementById('location_type');
        const subtypeGroup = document.getElementById('location_subtype').closest('.form-group');

        locationTypeSelect.addEventListener('change', function () {
            if (this.value === 'storage' || this.value === 'bin') {
                subtypeGroup.style.display = 'block';
            } else {
                subtypeGroup.style.display = 'none';
            }
        });

        // Auto-generate location names for single mode
        function generateSingleLocationName() {
            const section = document.getElementById('single_section').value;
            const aisleRack = document.getElementById('single_aisle_rack').value;
            const columnBin = document.getElementById('single_column_bin').value;
            const locationType = document.getElementById('location_type').value;
            const subtype = document.getElementById('location_subtype').value;
            const storeLocation = document.getElementById('single_zone').value;

            if (section && aisleRack && columnBin) {
                let name = '';
                const locationPrefix = storeLocation ? `${storeLocation} - ` : '';
                
                if (locationType === 'storage' || locationType === 'bin') {
                    const subtypeNames = {
                        'general': 'Storage Location',
                        'bulk': 'Bulk Storage',
                        'cold': 'Cold Storage',
                        'hazmat': 'Hazmat Storage',
                        'high_value': 'High Value Storage',
                        'oversized': 'Oversized Storage'
                    };
                    name = `${locationPrefix}${subtypeNames[subtype] || 'Storage Location'} ${aisleRack}-${columnBin}`;
                } else {
                    name = `${locationPrefix}${section.toUpperCase()}-${aisleRack}-${columnBin}`;
                }
                document.getElementById('single_location_name').value = name;
            }
        }

        // Update single location name when fields change
        ['single_section', 'single_aisle_rack', 'single_column_bin', 'location_type', 'location_subtype', 'single_zone'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', generateSingleLocationName);
                element.addEventListener('change', generateSingleLocationName);
            }
        });

        // Range preview functionality
        function updateRangePreview() {
            const startSection = document.getElementById('start_section').value;
            const startAisleRack = document.getElementById('start_aisle_rack').value;
            const startColumnBin = document.getElementById('start_column_bin').value;
            const endSection = document.getElementById('end_section').value;
            const endAisleRack = document.getElementById('end_aisle_rack').value;
            const endColumnBin = document.getElementById('end_column_bin').value;

            const preview = document.getElementById('range_preview');

            if (!startSection || !startAisleRack || !startColumnBin ||
                !endSection || !endAisleRack || !endColumnBin) {
                preview.innerHTML = '<span class="text-muted">Fill in range values to see preview...</span>';
                return;
            }

            try {
                const count = calculateRangeCount(
                    startSection, startAisleRack, startColumnBin,
                    endSection, endAisleRack, endColumnBin
                );

                if (count > 0) {
                    const startCode = `${startSection.toUpperCase()}-${startAisleRack.toUpperCase()}-${startColumnBin.toUpperCase()}`;
                    const endCode = `${endSection.toUpperCase()}-${endAisleRack.toUpperCase()}-${endColumnBin.toUpperCase()}`;

                    preview.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <strong>Range:</strong> ${startCode} to ${endCode}<br>
                            <strong>Total Locations:</strong> <span class="badge badge-primary">${count}</span>
                        </div>
                        <div class="col-md-4 text-right">
                            ${count > 100 ? '<span class="badge badge-warning">Large Range!</span>' : ''}
                        </div>
                    </div>
                `;
                } else {
                    preview.innerHTML = '<span class="text-danger">Invalid range - check your start/end values</span>';
                }
            } catch (error) {
                preview.innerHTML = '<span class="text-danger">Error calculating range</span>';
            }
        }

        // Calculate how many locations will be created in the range
        function calculateRangeCount(startSection, startAisleRack, startColumnBin, endSection, endAisleRack, endColumnBin) {
            // Simple, direct calculation for range like S1-B1-A1 to S1-C5-C5
            const parseLocationPart = (part) => {
                const match = part.match(/^([A-Za-z])(\d+)$/);
                if (match) {
                    return { letter: match[1].toUpperCase(), number: parseInt(match[2]) };
                }
                return null;
            };

            // Parse all parts
            const startShop = parseInt(startSection.replace(/[^\d]/g, ''));
            const endShop = parseInt(endSection.replace(/[^\d]/g, ''));
            
            const startAisle = parseLocationPart(startAisleRack);
            const endAisle = parseLocationPart(endAisleRack);
            const startColumn = parseLocationPart(startColumnBin);
            const endColumn = parseLocationPart(endColumnBin);

            if (!startAisle || !endAisle || !startColumn || !endColumn || startShop !== endShop) {
                return 0;
            }

            // Calculate total combinations
            // For S1-B1-A1 to S1-C5-C5:
            // - Aisles: B to C (2 aisles)
            // - Racks: 1 to 5 (5 racks)  
            // - Columns: A to C (3 columns)
            // - Bins: 1 to 5 (5 bins)
            
            const aisleCount = (endAisle.letter.charCodeAt(0) - startAisle.letter.charCodeAt(0)) + 1;
            const rackCount = (endAisle.number - startAisle.number) + 1;
            const columnCount = (endColumn.letter.charCodeAt(0) - startColumn.letter.charCodeAt(0)) + 1;
            const binCount = (endColumn.number - startColumn.number) + 1;

            // Total locations = aisles × racks × columns × bins
            const totalLocations = aisleCount * rackCount * columnCount * binCount;
            
            console.log('Range calculation:', {
                aisles: `${startAisle.letter} to ${endAisle.letter} = ${aisleCount}`,
                racks: `${startAisle.number} to ${endAisle.number} = ${rackCount}`,
                columns: `${startColumn.letter} to ${endColumn.letter} = ${columnCount}`,
                bins: `${startColumn.number} to ${endColumn.number} = ${binCount}`,
                total: totalLocations
            });

            return totalLocations;
        }

        // Update range preview when fields change
        ['start_section', 'start_aisle_rack', 'start_column_bin',
            'end_section', 'end_aisle_rack', 'end_column_bin'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    console.log(`Adding event listener to ${id}`);
                    element.addEventListener('input', updateRangePreview);
                } else {
                    console.error(`Element not found: ${id}`);
                }
            });

        // Form validation
        document.getElementById('addLocationForm').addEventListener('submit', function (e) {
            if (rangeMode.checked) {
                // Validate range form
                const requiredFields = ['start_section', 'start_aisle_rack', 'start_column_bin',
                    'end_section', 'end_aisle_rack', 'end_column_bin'];

                for (let fieldId of requiredFields) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        e.preventDefault();
                        alert('Please fill in all range fields');
                        field.focus();
                        return;
                    }
                }

                // Create start_location and end_location fields for PHP backend
                const startLocation = `${document.getElementById('start_section').value.toUpperCase()}-${document.getElementById('start_aisle_rack').value.toUpperCase()}-${document.getElementById('start_column_bin').value.toUpperCase()}`;
                const endLocation = `${document.getElementById('end_section').value.toUpperCase()}-${document.getElementById('end_aisle_rack').value.toUpperCase()}-${document.getElementById('end_column_bin').value.toUpperCase()}`;

                // Add hidden fields to form
                const startLocationInput = document.createElement('input');
                startLocationInput.type = 'hidden';
                startLocationInput.name = 'start_location';
                startLocationInput.value = startLocation;
                this.appendChild(startLocationInput);

                const endLocationInput = document.createElement('input');
                endLocationInput.type = 'hidden';
                endLocationInput.name = 'end_location';
                endLocationInput.value = endLocation;
                this.appendChild(endLocationInput);

                // Check if range will create too many locations
                try {
                    const count = calculateRangeCount(
                        document.getElementById('start_section').value,
                        document.getElementById('start_aisle_rack').value,
                        document.getElementById('start_column_bin').value,
                        document.getElementById('end_section').value,
                        document.getElementById('end_aisle_rack').value,
                        document.getElementById('end_column_bin').value
                    );

                    if (count > 500) {
                        if (!confirm(`This will create ${count} locations. Are you sure you want to continue?`)) {
                            e.preventDefault();
                            return;
                        }
                    }
                } catch (error) {
                    e.preventDefault();
                    alert('Invalid range specification');
                    return;
                }
            } else {
                // Validate single form
                const requiredFields = ['single_section', 'single_aisle_rack', 'single_column_bin'];

                for (let fieldId of requiredFields) {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        e.preventDefault();
                        alert('Please fill in all location fields');
                        field.focus();
                        return;
                    }
                }

                // For single location, create start_location and end_location with same value
                const singleLocation = `${document.getElementById('single_section').value.toUpperCase()}-${document.getElementById('single_aisle_rack').value.toUpperCase()}-${document.getElementById('single_column_bin').value.toUpperCase()}`;

                // Add hidden fields to form
                const startLocationInput = document.createElement('input');
                startLocationInput.type = 'hidden';
                startLocationInput.name = 'start_location';
                startLocationInput.value = singleLocation;
                this.appendChild(startLocationInput);

                const endLocationInput = document.createElement('input');
                endLocationInput.type = 'hidden';
                endLocationInput.name = 'end_location';
                endLocationInput.value = singleLocation;
                this.appendChild(endLocationInput);

                // For single location, use single_zone value
                const zoneValue = document.getElementById('single_zone').value;
                if (zoneValue) {
                    const zoneInput = document.createElement('input');
                    zoneInput.type = 'hidden';
                    zoneInput.name = 'zone';
                    zoneInput.value = zoneValue;
                    this.appendChild(zoneInput);
                }
            }

            // Add loading state
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        });

        // Reset form when modal closes
        document.getElementById('addLocationModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('addLocationForm').reset();
            singleMode.checked = true;
            switchMode();
            document.getElementById('range_preview').innerHTML =
                '<span class="text-muted">Fill in range values to see preview...</span>';
        });

        // Initialize form
        switchMode();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>