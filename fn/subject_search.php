<?php
include 'dbcon.php'; // Include your database connection

// Check if a search query is provided
if (isset($_GET['query'])) {
    $search_query = $_GET['query'];
    
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $con->prepare("SELECT subject_code FROM subjects WHERE subject_code LIKE ?");
    $search_param = "%{$search_query}%"; // Use wildcard for partial matches
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any subjects were found
    if ($result->num_rows > 0) {
        echo "<h3>Search Results:</h3><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['subject_code']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No suasdsadbjects found matching your query.";
    }

    $stmt->close();
} else {
    echo "Please enter a search query.";
}
?>
