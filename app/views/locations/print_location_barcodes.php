<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/locations/location_barcodes" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Location Barcodes
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-print"></i> <?php echo $data['title']; ?></h2>
        </div>
    </div>

    <!-- Print Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-cog"></i> Print Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="label-size" class="form-label">Label Size:</label>
                            <select id="label-size" class="form-control">
                                <option value="small">Small (2" x 1")</option>
                                <option value="medium" selected>Medium (3" x 2")</option>
                                <option value="large">Large (4" x 3")</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="copies-per-location" class="form-label">Copies per Location:</label>
                            <input type="number" id="copies-per-location" class="form-control" value="1" min="1"
                                max="10">
                        </div>
                        <div class="col-md-3">
                            <label for="include-text" class="form-label">Include Text:</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" id="include-text" class="form-check-input" checked>
                                <label class="form-check-label" for="include-text">Show location name</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mt-4">
                                <button class="btn btn-primary" onclick="printLabels()">
                                    <i class="fa-solid fa-print"></i> Print Labels
                                </button>
                                <button class="btn btn-outline-secondary" onclick="showPreview()">
                                    <i class="fa-solid fa-eye"></i> Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Location Print -->
    <?php if (isset($data['location']) && isset($data['barcodes'])): ?>
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="card-header bg-success-theme text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-qrcode"></i> Location Barcode Label</h5>
                    </div>
                    <div class="card-body">
                        <div id="print-content">
                            <?php foreach ($data['barcodes'] as $barcode): ?>
                                <div class="barcode-label medium-label text-center mb-3 p-3 border">
                                    <div class="barcode-image mb-2">
                                        <img src="data:image/png;base64,<?php
                                        require_once APPROOT . DS . 'vendor' . DS . 'autoload.php';
                                        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                                        echo base64_encode($generator->getBarcode($barcode['barcode_value'], $generator::TYPE_CODE_128));
                                        ?>" alt="Barcode" style="width: 200px; height: 60px;">
                                    </div>
                                    <div class="location-name">
                                        <strong><?php echo htmlspecialchars($data['location']->location_name); ?></strong>
                                    </div>
                                    <div class="location-details">
                                        Rack: <?php echo htmlspecialchars($data['location']->rack); ?> |
                                        Shelf: <?php echo htmlspecialchars($data['location']->shelf); ?>
                                    </div>
                                    <div class="barcode-value">
                                        <small><?php echo $barcode['barcode_value']; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multiple Locations Print -->
    <?php elseif (isset($data['location_barcodes'])): ?>
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="card-header bg-success-theme text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-qrcode"></i> All Location Barcode Labels</h5>
                    </div>
                    <div class="card-body">
                        <div id="print-content">
                            <div class="row">
                                <?php foreach ($data['location_barcodes'] as $barcode): ?>
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <div class="barcode-label medium-label text-center p-3 border">
                                            <div class="barcode-image mb-2">
                                                <img src="data:image/png;base64,<?php
                                                require_once APPROOT . DS . 'vendor' . DS . 'autoload.php';
                                                $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                                                echo base64_encode($generator->getBarcode($barcode->barcode_value, $generator::TYPE_CODE_128));
                                                ?>" alt="Barcode" style="width: 180px; height: 50px;">
                                            </div>
                                            <div class="location-name">
                                                <strong><?php echo htmlspecialchars($barcode->location_name); ?></strong>
                                            </div>
                                            <div class="location-details">
                                                <?php echo htmlspecialchars($barcode->rack); ?> -
                                                <?php echo htmlspecialchars($barcode->shelf); ?>
                                            </div>
                                            <div class="barcode-value">
                                                <small><?php echo $barcode->barcode_value; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Print Styles */
    .barcode-label {
        page-break-inside: avoid;
        border: 2px solid #000 !important;
        background: white;
    }

    .small-label {
        width: 2in;
        height: 1in;
        font-size: 8px;
    }

    .medium-label {
        width: 3in;
        height: 2in;
        font-size: 10px;
    }

    .large-label {
        width: 4in;
        height: 3in;
        font-size: 12px;
    }

    .small-label img {
        width: 120px !important;
        height: 30px !important;
    }

    .medium-label img {
        width: 180px !important;
        height: 50px !important;
    }

    .large-label img {
        width: 240px !important;
        height: 70px !important;
    }

    @media print {

        .container-fluid,
        .theme-card,
        .card-header,
        .btn,
        .form-control,
        .form-label {
            display: none !important;
        }

        #print-content {
            display: block !important;
        }

        .barcode-label {
            margin: 0.25in;
            float: left;
        }

        body {
            margin: 0;
            padding: 0;
        }
    }
</style>

<script>
    function printLabels() {
        window.print();
    }

    function showPreview() {
        const printContent = document.getElementById('print-content').innerHTML;
        const previewWindow = window.open('', '_blank', 'width=800,height=600');
        previewWindow.document.write(`
        <html>
        <head>
            <title>Print Preview - Location Barcodes</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .barcode-label { 
                    border: 2px solid #000; 
                    padding: 10px; 
                    margin: 10px; 
                    text-align: center; 
                    display: inline-block;
                    width: 3in;
                    height: 2in;
                }
            </style>
        </head>
        <body>
            <h2>Print Preview - Location Barcodes</h2>
            ${printContent}
        </body>
        </html>
    `);
        previewWindow.document.close();
    }

    // Adjust label sizes
    document.getElementById('label-size').addEventListener('change', function () {
        const size = this.value;
        const labels = document.querySelectorAll('.barcode-label');

        labels.forEach(label => {
            label.className = `barcode-label ${size}-label text-center p-3 border mb-3`;
        });
    });

    // Toggle text display
    document.getElementById('include-text').addEventListener('change', function () {
        const showText = this.checked;
        const textElements = document.querySelectorAll('.location-name, .location-details, .barcode-value');

        textElements.forEach(element => {
            element.style.display = showText ? 'block' : 'none';
        });
    });

    // Update copies
    document.getElementById('copies-per-location').addEventListener('change', function () {
        const copies = parseInt(this.value);
        // Implementation for multiple copies would go here
        // For now, we'll just show the current labels
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>