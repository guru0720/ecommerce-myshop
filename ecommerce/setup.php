
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$sqlFile = 'mysql/ecommerce1.sql'; // Path to your SQL file

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    die("Failed to read SQL file: $sqlFile");
}

$queries = array_filter(array_map('trim', explode(';', $sql)));

foreach ($queries as $query) {
    if (!empty($query)) {
        if (!$conn->query($query)) {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
}

echo "<h3 style='color:green;'>✅ Database 'ecommerce1' setup completed successfully!</h3>";
$conn->close();
?>
