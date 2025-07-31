<?php
session_start();
require_once 'app/config.php';

echo "<h1>Dashboard Admin Panel Button Test</h1>";

// Test current session
echo "<h2>Current Session Status:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<ul>";
    echo "<li>User ID: " . $_SESSION['user_id'] . "</li>";
    echo "<li>Username: " . $_SESSION['user_username'] . "</li>";
    echo "<li>Role: " . $_SESSION['user_role'] . "</li>";
    echo "</ul>";

    echo "<h3>Admin Panel Button Logic Test:</h3>";
    if ($_SESSION['user_role'] === 'admin') {
        echo "✅ <strong>Admin Panel button SHOULD appear</strong><br>";
        echo "Button code: <code>&lt;a href='" . URLROOT . "/admin' class='btn btn-success'&gt;&lt;i class='fas fa-cog'&gt;&lt;/i&gt; Admin Panel&lt;/a&gt;</code><br>";
    } else {
        echo "❌ Admin Panel button will NOT appear (user role: " . $_SESSION['user_role'] . ")<br>";
    }
} else {
    echo "❌ No user logged in<br>";
}

echo "<h2>Dashboard Test Links:</h2>";
echo "<ul>";
echo "<li><a href='" . URLROOT . "/dashboard' target='_blank'>Dashboard Page</a></li>";
echo "<li><a href='" . URLROOT . "/pages/index' target='_blank'>Main Dashboard (Pages)</a></li>";
echo "<li><a href='" . URLROOT . "/admin' target='_blank'>Admin Panel</a></li>";
echo "</ul>";

echo "<h2>Expected Result:</h2>";
echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
echo "<h4>For Admin Users (like sukhdev):</h4>";
echo "<ul>";
echo "<li>✅ Green 'Admin Panel' button appears in top right corner</li>";
echo "<li>✅ Button is positioned before the date dropdown</li>";
echo "<li>✅ Button links to /admin page</li>";
echo "<li>✅ Uses Font Awesome cog icon</li>";
echo "</ul>";

echo "<h4>For Non-Admin Users:</h4>";
echo "<ul>";
echo "<li>❌ No Admin Panel button shown</li>";
echo "<li>✅ Only date dropdown and refresh button visible</li>";
echo "</ul>";
echo "</div>";

echo "<h2>Button Styling:</h2>";
echo "<ul>";
echo "<li><strong>Color:</strong> Green (btn-success)</li>";
echo "<li><strong>Icon:</strong> Font Awesome cog (fas fa-cog)</li>";
echo "<li><strong>Position:</strong> Top right corner, before date dropdown</li>";
echo "<li><strong>Responsive:</strong> Inline-block with margin-right</li>";
echo "</ul>";

if (!isset($_SESSION['user_id'])) {
    echo "<p><strong>Note:</strong> <a href='" . URLROOT . "/users/login'>Login as sukhdev</a> to test the admin panel button functionality.</p>";
}

?>