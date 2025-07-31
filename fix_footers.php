<?php
// Script to fix all footer references in view files

$searchDir = 'app/views';
$footerReplacements = [
    // Pattern 1: Standard footer include
    '<?php require APPROOT . DS . \'app\' . DS . \'views\' . DS . \'layout\' . DS . \'footer.php\'; ?>',
    // Pattern 2: Alternative footer include
    '<?php require_once \'../app/views/layout/footer.php\'; ?>',
    // Pattern 3: Login footer include
    '<?php require APPROOT . DS . \'app\' . DS . \'views\' . DS . \'layout\' . DS . \'login_footer.php\'; ?>'
];

$replacement = '
            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>';

function getAllPhpFiles($dir)
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

function fixFooterInFile($filePath, $footerPatterns, $replacement)
{
    $content = file_get_contents($filePath);
    $originalContent = $content;

    foreach ($footerPatterns as $pattern) {
        $content = str_replace($pattern, $replacement, $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        return true;
    }
    return false;
}

echo "<h1>Footer Fix Script</h1>";
echo "<h2>Scanning for PHP files with footer references...</h2>";

$files = getAllPhpFiles($searchDir);
$fixedCount = 0;
$fixedFiles = [];

foreach ($files as $file) {
    if (fixFooterInFile($file, $footerReplacements, $replacement)) {
        $fixedCount++;
        $fixedFiles[] = $file;
        echo "✅ Fixed: " . str_replace('\\', '/', $file) . "<br>";
    }
}

echo "<h2>Summary:</h2>";
echo "<p>✅ Fixed {$fixedCount} files</p>";

if ($fixedCount > 0) {
    echo "<h3>Files Modified:</h3>";
    echo "<ul>";
    foreach ($fixedFiles as $file) {
        echo "<li>" . str_replace('\\', '/', $file) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No files needed fixing.</p>";
}

echo "<h3>Testing a few key files:</h3>";
$testFiles = [
    'app/views/reports/index.php',
    'app/views/pages/index.php',
    'app/views/dashboard/index.php'
];

foreach ($testFiles as $testFile) {
    if (file_exists($testFile)) {
        $content = file_get_contents($testFile);
        if (strpos($content, 'footer.php') !== false) {
            echo "❌ Still has footer reference: {$testFile}<br>";
        } else {
            echo "✅ Footer reference removed: {$testFile}<br>";
        }
    }
}

?>