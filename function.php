<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "root1@0#";
$database = "student_management";

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// CRUD operations
function addStudent($name, $email, $subject, $marks) {
    global $mysqli;
    $query = "INSERT INTO students (name, email, subject, marks) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $subject, $marks);
    $stmt->execute();
    $stmt->close();
}

function getStudents() {
    global $mysqli;
    $query = "SELECT * FROM students";
    $result = $mysqli->query($query);
    $students = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    return $students;
}

function deleteStudent($id) {
    global $mysqli;
    $query = "DELETE FROM students WHERE student_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>
