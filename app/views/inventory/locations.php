<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<?php
// Defensive programming - ensure data structure is correct
$stats = isset($data['stats']) && is_object($data['stats']) ? $data['stats'] : (object) [
    'total_locations' => 0,
    'dock_locations' => 0,
    'receiving_locations' => 0,
    'storage_locations' => 0,
    'bin_locations' => 0
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
            'aisle' => strtoupper($matches[3]),
            'rack' => $matches[4],
            'column' => strtoupper($matches[5]),
            'bin' => $matches[6],
            'format' => 'section_aisle_column'
        ];
    }

    // Handle format: S1-B15-C3 (shop-aisle+rack-column+bin)
    if (preg_match('/^S(\d+)-([A-Z])(\d+)-([A-Z])(\d+)$/i', $code, $matches)) {
        return [
            'section' => $matches[1],
            'aisle' => $matches[2],
            'rack' => $matches[3],
            'column' => $matches[4],
            'bin' => $matches[5],
            'format' => 'shop_format'
        ];
    }

    // Handle legacy format: A01-A02-01 (aisle-shelf-bin)
    if (preg_match('/^([A-Z])(\d+)-([A-Z])(\d+)-(\d+)$/i', $code, $matches)) {
        return [
            'section' => '1', // Default section
            'aisle' => $matches[1],
            'rack' => $matches[2],
            'column' => $matches[3],
            'bin' => $matches[5],
            'format' => 'legacy'
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
            'code' => $location->location_code,
            'description' => $location->location_name,
            'badge' => getBadgeType($location)
        ];
    }

    switch ($parsed['format']) {
        case 'section_aisle_column':
        case 'shop_format':
            $formattedCode = "S{$parsed['section']}-{$parsed['aisle']}{$parsed['rack']}-{$parsed['column']}{$parsed['bin']}";
            $description = "Section {$parsed['section']}, Aisle {$parsed['aisle']}, Rack {$parsed['rack']}, Bin {$parsed['column']}{$parsed['bin']}";
            return [
                'code' => $formattedCode,
                'description' => $description,
                'badge' => 'Shop'
            ];

        case 'legacy':
            $formattedCode = "S{$parsed['section']}-{$parsed['aisle']}{$parsed['rack']}-{$parsed['column']}{$parsed['bin']}";
            $description = "Section {$parsed['section']}, Aisle {$parsed['aisle']}, Rack {$parsed['rack']}, Bin {$parsed['column']}{$parsed['bin']}";
            return [
                'code' => $formattedCode,
                'description' => $description,
                'badge' => 'Shop'
            ];

        case 'simple':
        default:
            return [
                'code' => $parsed['simple'],
                'description' => $location->location_name,
                'badge' => getBadgeType($location)
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

<style>
    .location-card {
        transition: all 0.2s ease;
        border-left: 4px solid var(--primary-color);
    }

    .location-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .location-type-dock {
        border-left-color: #e74c3c;
    }

    .location-type-receiving {
        border-left-color: #3498db;
    }

    .location-type-storage {
        border-left-color: #2ecc71;
    }

    .location-type-bin {
        border-left-color: #f39c12;
    }

    .location-badge {
        font-size: 0.85em;
        padding: 0.25rem 0.5rem;
    }

    .stats-card {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
        border: none;
        border-radius: 12px;
    }

    .location-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
</style>

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
                    <button class="btn-theme btn-primary-theme" data-toggle="modal" data-target="#addLocationModal">
                        <i class="fas fa-plus mr-2"></i>Add Location
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('inventory_message'); ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card-theme stats-card h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fas fa-map-marker-alt fa-2x"></i>
                            </div>
                            <h4 class="mb-0"><?php echo $stats->total_locations ?? 0; ?></h4>
                            <small class="text-muted">Total Locations</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card-theme stats-card h-100">
                        <div class="card-body text-center">
                            <div class="text-danger mb-2">
                                <i class="fas fa-truck fa-2x"></i>
                            </div>
                            <h4 class="mb-0"><?php echo $stats->dock_locations ?? 0; ?></h4>
                            <small class="text-muted">Dock Doors</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card-theme stats-card h-100">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="fas fa-inbox fa-2x"></i>
                            </div>
                            <h4 class="mb-0"><?php echo $stats->receiving_locations ?? 0; ?></h4>
                            <small class="text-muted">Receiving Areas</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card-theme stats-card h-100">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fas fa-warehouse fa-2x"></i>
                            </div>
                            <h4 class="mb-0"><?php echo $stats->storage_locations ?? 0; ?></h4>
                            <small class="text-muted">Storage Areas</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card-theme stats-card h-100">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                            <h4 class="mb-0"><?php echo $stats->bin_locations ?? 0; ?></h4>
                            <small class="text-muted">Bin Locations</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Types Tabs -->
            <div class="card">
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
                                            <div
                                                class="card-theme location-card location-type-<?php echo $location->location_type; ?>">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0">
                                                            <strong><?php echo htmlspecialchars($displayInfo['code']); ?></strong>
                                                        </h6>
                                                        <span
                                                            class="badge location-badge
                                                        <?php echo $location->location_type == 'dock' ? 'badge-danger' :
                                                            ($location->location_type == 'receiving' ? 'badge-info' :
                                                                ($location->location_type == 'storage' ? 'badge-success' : 'badge-warning')); ?>">
                                                            <?php echo htmlspecialchars($displayInfo['badge']); ?>
                                                        </span>
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
                                                            <div><strong>Zone:</strong> <?php echo htmlspecialchars($location->zone); ?>
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
                            'dock' => ['icon' => 'truck', 'color' => 'danger'],
                            'receiving' => ['icon' => 'inbox', 'color' => 'info'],
                            'storage' => ['icon' => 'warehouse', 'color' => 'success'],
                            'bin' => ['icon' => 'box', 'color' => 'warning']
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
                                                <div
                                                    class="card-theme location-card location-type-<?php echo $location->location_type; ?>">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title mb-0">
                                                                <strong><?php echo htmlspecialchars($displayInfo['code']); ?></strong>
                                                            </h6>
                                                            <span class="badge badge-<?php echo $config['color']; ?> location-badge">
                                                                <?php echo htmlspecialchars($displayInfo['badge']); ?>
                                                            </span>
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
                                                                <div><strong>Zone:</strong> <?php echo htmlspecialchars($location->zone); ?>
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

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content theme-card">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Create Locations
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addLocationForm" method="POST" action="<?php echo URLROOT; ?>/inventory/addLocationRange">
                <div class="modal-body">
                    <!-- Creation Mode Selector -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card-theme theme-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-cogs mr-2"></i>Creation Mode
                                    </h6>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="creation_mode"
                                            id="single_mode" value="single" checked>
                                        <label class="form-check-label" for="single_mode">
                                            Single Location
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="creation_mode"
                                            id="range_mode" value="range">
                                        <label class="form-check-label" for="range_mode">
                                            Location Range (Bulk Creation)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Type and Subtype -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_type">Location Category *</label>
                                <select class="form-theme" id="location_type" name="location_type" required>
                                    <option value="">Select Category</option>
                                    <option value="storage">Storage Areas</option>
                                    <option value="bin">Bin Locations</option>
                                    <option value="receiving">Receiving Areas</option>
                                    <option value="dock">Dock Doors</option>
                                    <option value="shipping">Shipping Areas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_subtype">Storage Type</label>
                                <select class="form-theme" id="location_subtype" name="location_subtype">
                                    <option value="general">General Storage</option>
                                    <option value="bulk">Bulk Storage</option>
                                    <option value="cold">Cold Storage</option>
                                    <option value="hazmat">Hazmat Storage</option>
                                    <option value="high_value">High Value Items</option>
                                    <option value="oversized">Oversized Items</option>
                                </select>
                                <small class="form-text text-muted">Only for storage/bin locations</small>
                            </div>
                        </div>
                    </div>

                    <!-- Location Format Guide -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle mr-2"></i>Location Format: S1-B15-C3</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>S1:</strong> Shop/Section 1<br>
                                <small>Building or major area</small>
                            </div>
                            <div class="col-md-4">
                                <strong>B15:</strong> Aisle B, Rack 15<br>
                                <small>Aisles A-Z, Racks 1-99</small>
                            </div>
                            <div class="col-md-4">
                                <strong>C3:</strong> Column C, Bin 3<br>
                                <small>Columns A-Z, Bins 1-99</small>
                            </div>
                        </div>
                    </div>

                    <!-- Single Location Form -->
                    <div id="single_form" class="location-form">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i>Single Location Details
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="single_section">Shop/Section *</label>
                                    <input type="text" class="form-theme" id="single_section" name="single_section"
                                        placeholder="S1" pattern="[Ss]\d+" maxlength="10">
                                    <small class="form-text text-muted">Format: S1, S2, etc.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="single_aisle_rack">Aisle/Rack *</label>
                                    <input type="text" class="form-theme" id="single_aisle_rack"
                                        name="single_aisle_rack" placeholder="B15" pattern="[A-Za-z]\d+" maxlength="10">
                                    <small class="form-text text-muted">Format: A1, B15, C99, etc.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="single_column_bin">Column/Bin *</label>
                                    <input type="text" class="form-theme" id="single_column_bin"
                                        name="single_column_bin" placeholder="C3" pattern="[A-Za-z]\d+" maxlength="10">
                                    <small class="form-text text-muted">Format: A1, C3, D5, etc.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="single_location_name">Location Name *</label>
                                    <input type="text" class="form-theme" id="single_location_name"
                                        name="single_location_name" placeholder="Auto-generated based on format">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="single_zone">Zone</label>
                                    <input type="text" class="form-theme" id="single_zone" name="single_zone"
                                        placeholder="e.g., STORAGE-A, BULK-1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Range Creation Form -->
                    <div id="range_form" class="location-form" style="display: none;">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-layer-group mr-2"></i>Location Range Creation
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-play mr-2"></i>Start of Range
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="start_section">Shop/Section *</label>
                                            <input type="text" class="form-theme" id="start_section"
                                                name="start_section" placeholder="S1" pattern="[Ss]\d+" maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label for="start_aisle_rack">Aisle/Rack *</label>
                                            <input type="text" class="form-theme" id="start_aisle_rack"
                                                name="start_aisle_rack" placeholder="A1" pattern="[A-Za-z]\d+"
                                                maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label for="start_column_bin">Column/Bin *</label>
                                            <input type="text" class="form-theme" id="start_column_bin"
                                                name="start_column_bin" placeholder="A1" pattern="[A-Za-z]\d+"
                                                maxlength="10">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-stop mr-2"></i>End of Range
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="end_section">Shop/Section *</label>
                                            <input type="text" class="form-theme" id="end_section" name="end_section"
                                                placeholder="S1" pattern="[Ss]\d+" maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label for="end_aisle_rack">Aisle/Rack *</label>
                                            <input type="text" class="form-theme" id="end_aisle_rack"
                                                name="end_aisle_rack" placeholder="D20" pattern="[A-Za-z]\d+"
                                                maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label for="end_column_bin">Column/Bin *</label>
                                            <input type="text" class="form-theme" id="end_column_bin"
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
                                <div class="card-theme theme-card">
                                    <div class="card-body">
                                        <h6>
                                            <i class="fas fa-eye mr-2"></i>Range Preview
                                        </h6>
                                        <div id="range_preview" class="text-muted">
                                            Fill in range values to see preview...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Common Properties -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="capacity_cubic_feet">Capacity (ft³)</label>
                                <input type="number" step="0.01" class="form-theme" id="capacity_cubic_feet"
                                    name="capacity_cubic_feet" placeholder="100.00" value="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="max_weight_kg">Max Weight (kg)</label>
                                <input type="number" step="0.01" class="form-theme" id="max_weight_kg"
                                    name="max_weight_kg" placeholder="500.00" value="500">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="climate_controlled"
                                        name="climate_controlled" value="1">
                                    <label class="form-check-label" for="climate_controlled">
                                        Climate Controlled
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-theme" id="notes" name="notes" rows="2"
                            placeholder="Additional information about these locations"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-theme btn-primary-theme">
                        <i class="fas fa-save mr-2"></i><span id="submit_text">Create Location</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Form mode switching
        const singleMode = document.getElementById('single_mode');
        const rangeMode = document.getElementById('range_mode');
        const singleForm = document.getElementById('single_form');
        const rangeForm = document.getElementById('range_form');
        const submitText = document.getElementById('submit_text');

        function switchMode() {
            if (rangeMode.checked) {
                singleForm.style.display = 'none';
                rangeForm.style.display = 'block';
                submitText.textContent = 'Create Location Range';
            } else {
                singleForm.style.display = 'block';
                rangeForm.style.display = 'none';
                submitText.textContent = 'Create Location';
            }
        }

        singleMode.addEventListener('change', switchMode);
        rangeMode.addEventListener('change', switchMode);

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

            if (section && aisleRack && columnBin) {
                let name = '';
                if (locationType === 'storage' || locationType === 'bin') {
                    const subtypeNames = {
                        'general': 'Storage Location',
                        'bulk': 'Bulk Storage',
                        'cold': 'Cold Storage',
                        'hazmat': 'Hazmat Storage',
                        'high_value': 'High Value Storage',
                        'oversized': 'Oversized Storage'
                    };
                    name = `${subtypeNames[subtype] || 'Storage Location'} ${aisleRack}-${columnBin}`;
                } else {
                    name = `${section.toUpperCase()}-${aisleRack}-${columnBin}`;
                }
                document.getElementById('single_location_name').value = name;
            }
        }

        // Update single location name when fields change
        ['single_section', 'single_aisle_rack', 'single_column_bin', 'location_type', 'location_subtype'].forEach(id => {
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
        function calculateRangeCount(startSection, startAisle, startColumn, endSection, endAisle, endColumn) {
            // Parse the components
            const parseLocationPart = (part) => {
                const match = part.match(/^([A-Za-z])(\d+)$/);
                if (match) {
                    return { letter: match[1].toUpperCase(), number: parseInt(match[2]) };
                }
                return null;
            };

            const startSectionNum = parseInt(startSection.replace(/[^\d]/g, ''));
            const endSectionNum = parseInt(endSection.replace(/[^\d]/g, ''));
            const startAisleParsed = parseLocationPart(startAisle);
            const endAisleParsed = parseLocationPart(endAisle);
            const startColumnParsed = parseLocationPart(startColumn);
            const endColumnParsed = parseLocationPart(endColumn);

            if (!startAisleParsed || !endAisleParsed || !startColumnParsed || !endColumnParsed) {
                return 0;
            }

            let count = 0;

            for (let section = startSectionNum; section <= endSectionNum; section++) {
                const aisleStart = (section === startSectionNum) ? startAisleParsed.letter : 'A';
                const aisleEnd = (section === endSectionNum) ? endAisleParsed.letter : 'Z';

                for (let aisleChar = aisleStart.charCodeAt(0); aisleChar <= aisleEnd.charCodeAt(0); aisleChar++) {
                    const aisle = String.fromCharCode(aisleChar);

                    const rackStart = (section === startSectionNum && aisle === aisleStart) ? startAisleParsed.number : 1;
                    const rackEnd = (section === endSectionNum && aisle === aisleEnd) ? endAisleParsed.number : 99;

                    for (let rack = rackStart; rack <= rackEnd; rack++) {
                        const colStart = (section === startSectionNum && aisle === aisleStart && rack === rackStart) ?
                            startColumnParsed.letter : 'A';
                        const colEnd = (section === endSectionNum && aisle === aisleEnd && rack === rackEnd) ?
                            endColumnParsed.letter : 'Z';

                        for (let colChar = colStart.charCodeAt(0); colChar <= colEnd.charCodeAt(0); colChar++) {
                            const col = String.fromCharCode(colChar);

                            const binStart = (section === startSectionNum && aisle === aisleStart &&
                                rack === rackStart && col === colStart) ? startColumnParsed.number : 1;
                            const binEnd = (section === endSectionNum && aisle === aisleEnd &&
                                rack === rackEnd && col === colEnd) ? endColumnParsed.number : 99;

                            count += (binEnd - binStart + 1);
                        }
                    }
                }
            }

            return count;
        }

        // Update range preview when fields change
        ['start_section', 'start_aisle_rack', 'start_column_bin',
            'end_section', 'end_aisle_rack', 'end_column_bin'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', updateRangePreview);
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