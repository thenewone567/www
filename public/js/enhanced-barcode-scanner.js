/**
 * Enhanced Barcode Scanner Library
 * Provides comprehensive barcode scanning functionality with fallbacks
 */

class EnhancedBarcodeScanner {
    constructor(options = {}) {
        this.options = {
            formats: [
                'code_128', 'code_39', 'code_93', 'codabar',
                'ean_13', 'ean_8', 'itf', 'upc_a', 'upc_e',
                'pdf417', 'aztec', 'data_matrix', 'qr_code'
            ],
            videoElement: null,
            onScan: null,
            onError: null,
            onStatusChange: null,
            scanInterval: 100,
            scanCooldown: 1000,
            autoStop: true,
            ...options
        };

        this.isScanning = false;
        this.scanner = null;
        this.stream = null;
        this.lastScanTime = 0;
        this.currentCamera = 0;
        this.availableCameras = [];
        this.supportsBarcodeDetection = 'BarcodeDetector' in window;

        this.init();
    }

    async init() {
        try {
            // Enumerate available cameras
            await this.enumerateCameras();

            // Initialize appropriate scanner
            if (this.supportsBarcodeDetection) {
                await this.initNativeScanner();
            } else {
                await this.initFallbackScanner();
            }

            this.updateStatus('Scanner initialized');
        } catch (error) {
            this.handleError('Initialization failed', error);
        }
    }

    async enumerateCameras() {
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
                throw new Error('Camera enumeration not supported');
            }

            const devices = await navigator.mediaDevices.enumerateDevices();
            this.availableCameras = devices.filter(device => device.kind === 'videoinput');

            console.log(`Found ${this.availableCameras.length} camera(s)`);
        } catch (error) {
            console.warn('Camera enumeration failed:', error);
            this.availableCameras = [];
        }
    }

    async initNativeScanner() {
        try {
            this.scanner = new BarcodeDetector({
                formats: this.options.formats
            });
            console.log('Native BarcodeDetector initialized');
        } catch (error) {
            console.warn('Native BarcodeDetector initialization failed:', error);
            await this.initFallbackScanner();
        }
    }

    async initFallbackScanner() {
        // Load ZXing library if available, otherwise use manual input
        if (window.ZXing) {
            this.scanner = new ZXing.BrowserMultiFormatReader();
            console.log('ZXing scanner initialized');
        } else {
            console.warn('No barcode scanning library available, using manual input fallback');
            this.scanner = null;
        }
    }

    async startScanning() {
        if (this.isScanning) {
            return;
        }

        try {
            this.updateStatus('Starting camera...');

            const video = this.options.videoElement || document.getElementById('barcodeScannerVideo');
            if (!video) {
                throw new Error('Video element not found');
            }

            // Get camera constraints
            const constraints = this.getCameraConstraints();

            // Request camera access
            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = this.stream;

            await new Promise((resolve, reject) => {
                video.onloadedmetadata = resolve;
                video.onerror = reject;
                setTimeout(reject, 5000); // 5 second timeout
            });

            await video.play();

            this.isScanning = true;
            this.updateStatus('Scanner active');

            // Start scanning loop
            this.scanLoop();

        } catch (error) {
            this.handleError('Failed to start camera', error);
        }
    }

    getCameraConstraints() {
        const constraints = {
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: { ideal: 'environment' }
            }
        };

        // Use specific camera if available
        if (this.availableCameras[this.currentCamera]) {
            constraints.video.deviceId = this.availableCameras[this.currentCamera].deviceId;
        }

        return constraints;
    }

    async scanLoop() {
        if (!this.isScanning) return;

        try {
            const video = this.options.videoElement || document.getElementById('barcodeScannerVideo');

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                await this.detectBarcodes(video);
            }
        } catch (error) {
            console.warn('Scan iteration failed:', error);
        }

        // Continue scanning
        if (this.isScanning) {
            setTimeout(() => this.scanLoop(), this.options.scanInterval);
        }
    }

    async detectBarcodes(video) {
        try {
            let barcodes = [];

            if (this.scanner && this.supportsBarcodeDetection) {
                barcodes = await this.scanner.detect(video);
            } else if (this.scanner && window.ZXing) {
                // ZXing implementation
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);

                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const result = this.scanner.decodeFromImageData(imageData);

                if (result) {
                    barcodes = [{ rawValue: result.text }];
                }
            }

            if (barcodes.length > 0) {
                const now = Date.now();
                if (now - this.lastScanTime > this.options.scanCooldown) {
                    this.processScanResult(barcodes[0]);
                    this.lastScanTime = now;
                }
            }
        } catch (error) {
            // Ignore individual scan errors to keep scanning
            console.warn('Barcode detection error:', error);
        }
    }

    processScanResult(barcode) {
        const result = {
            value: barcode.rawValue || barcode.text,
            format: barcode.format || 'unknown',
            timestamp: new Date().toISOString()
        };

        // Visual feedback
        this.showScanSuccess();

        // Audio feedback
        this.playBeepSound();

        // Callback
        if (this.options.onScan) {
            this.options.onScan(result);
        }

        // Auto-stop if enabled
        if (this.options.autoStop) {
            setTimeout(() => this.stopScanning(), 1000);
        }
    }

    showScanSuccess() {
        const overlay = document.querySelector('.scanner-overlay');
        if (overlay) {
            overlay.style.background = 'rgba(40, 167, 69, 0.3)';
            setTimeout(() => {
                overlay.style.background = 'rgba(0, 0, 0, 0.1)';
            }, 500);
        }
    }

    playBeepSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'square';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (error) {
            console.warn('Audio feedback failed:', error);
        }
    }

    stopScanning() {
        this.isScanning = false;

        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }

        const video = this.options.videoElement || document.getElementById('barcodeScannerVideo');
        if (video) {
            video.srcObject = null;
        }

        this.updateStatus('Scanner stopped');
    }

    async switchCamera() {
        if (this.availableCameras.length <= 1) {
            this.updateStatus('No additional cameras available');
            return;
        }

        this.currentCamera = (this.currentCamera + 1) % this.availableCameras.length;

        if (this.isScanning) {
            this.stopScanning();
            await new Promise(resolve => setTimeout(resolve, 500));
            await this.startScanning();
        }
    }

    manualEntry() {
        const barcode = prompt('Enter barcode manually:');
        if (barcode && barcode.trim()) {
            this.processScanResult({
                rawValue: barcode.trim(),
                format: 'manual'
            });
        }
    }

    updateStatus(message) {
        const statusElement = document.getElementById('scannerStatus');
        if (statusElement) {
            statusElement.textContent = message;
        }

        if (this.options.onStatusChange) {
            this.options.onStatusChange(message);
        }

        console.log('Scanner status:', message);
    }

    handleError(message, error) {
        const fullMessage = `${message}: ${error.message || error}`;
        this.updateStatus(fullMessage);

        if (this.options.onError) {
            this.options.onError(fullMessage, error);
        }

        console.error('Scanner error:', fullMessage, error);
    }

    // Public API methods
    isSupported() {
        return navigator.mediaDevices && navigator.mediaDevices.getUserMedia;
    }

    hasNativeSupport() {
        return this.supportsBarcodeDetection;
    }

    getCameraCount() {
        return this.availableCameras.length;
    }

    getCurrentCameraInfo() {
        if (this.availableCameras[this.currentCamera]) {
            return {
                deviceId: this.availableCameras[this.currentCamera].deviceId,
                label: this.availableCameras[this.currentCamera].label || `Camera ${this.currentCamera + 1}`
            };
        }
        return null;
    }

    // Event handlers for UI integration
    onScan(callback) {
        this.options.onScan = callback;
    }

    onError(callback) {
        this.options.onError = callback;
    }

    onStatusChange(callback) {
        this.options.onStatusChange = callback;
    }
}

// Utility functions for barcode validation
class BarcodeValidator {
    static validateEAN13(barcode) {
        if (!/^\d{13}$/.test(barcode)) return false;

        const digits = barcode.split('').map(d => parseInt(d));
        const checkDigit = digits.pop();

        let sum = 0;
        for (let i = 0; i < digits.length; i++) {
            sum += digits[i] * (i % 2 === 0 ? 1 : 3);
        }

        const calculatedCheck = (10 - (sum % 10)) % 10;
        return calculatedCheck === checkDigit;
    }

    static validateUPC(barcode) {
        if (!/^\d{12}$/.test(barcode)) return false;

        const digits = barcode.split('').map(d => parseInt(d));
        const checkDigit = digits.pop();

        let sum = 0;
        for (let i = 0; i < digits.length; i++) {
            sum += digits[i] * (i % 2 === 0 ? 3 : 1);
        }

        const calculatedCheck = (10 - (sum % 10)) % 10;
        return calculatedCheck === checkDigit;
    }

    static validateCode128(barcode) {
        // Basic length and character validation for Code 128
        return /^[\x00-\x7F]+$/.test(barcode) && barcode.length >= 1;
    }

    static detectBarcodeType(barcode) {
        if (/^\d{13}$/.test(barcode)) return 'EAN-13';
        if (/^\d{12}$/.test(barcode)) return 'UPC-A';
        if (/^\d{8}$/.test(barcode)) return 'EAN-8';
        if (/^[0-9A-Z\-\.\s\$\/\+%]+$/.test(barcode)) return 'Code 39';
        return 'Unknown';
    }

    static validate(barcode, expectedFormat = null) {
        const detectedFormat = this.detectBarcodeType(barcode);

        if (expectedFormat && detectedFormat !== expectedFormat) {
            return { valid: false, format: detectedFormat, error: 'Format mismatch' };
        }

        let isValid = false;
        switch (detectedFormat) {
            case 'EAN-13':
                isValid = this.validateEAN13(barcode);
                break;
            case 'UPC-A':
                isValid = this.validateUPC(barcode);
                break;
            case 'Code 39':
            case 'Code 128':
                isValid = this.validateCode128(barcode);
                break;
            default:
                isValid = barcode.length > 0;
        }

        return {
            valid: isValid,
            format: detectedFormat,
            error: isValid ? null : 'Invalid checksum or format'
        };
    }
}

// Global scanner instance management
window.EnhancedBarcodeScanner = EnhancedBarcodeScanner;
window.BarcodeValidator = BarcodeValidator;

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { EnhancedBarcodeScanner, BarcodeValidator };
}
