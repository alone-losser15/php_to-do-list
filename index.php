<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "student_management";

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to add a new student
function addStudent($student_id, $name, $email, $subject, $marks) {
    global $mysqli;
    $query = "INSERT INTO students (student_id, name, email, subject, marks) VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("isssi", $student_id, $name, $email, $subject, $marks);
    return $stmt->execute();
}

// Function to get all students
function getAllStudents() {
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

// Function to delete a student by id
function deleteStudent($id) {
    global $mysqli;
    $query = "DELETE FROM students WHERE student_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Function to get student by id
function getStudentById($id) {
    global $mysqli;
    $query = "SELECT * FROM students WHERE student_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to update student information
function updateStudent($student_id, $name, $email, $subject, $marks) {
    global $mysqli;
    $query = "UPDATE students SET name=?, email=?, subject=?, marks=? WHERE student_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssii", $name, $email, $subject, $marks, $student_id);
    return $stmt->execute();
}

// Check if form is submitted for adding or editing a student
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "add") {
            $student_id = $_POST['student_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $subject = $_POST['subject'];
            $marks = $_POST['marks'];
            addStudent($student_id, $name, $email, $subject, $marks);
        } elseif ($_POST['action'] == "edit") {
            $student_id = $_POST['student_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $subject = $_POST['subject'];
            $marks = $_POST['marks'];
            updateStudent($student_id, $name, $email, $subject, $marks);
        }
    }
}

// Check if delete action is requested
if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
    $id = $_GET['id'];
    deleteStudent($id);
    header("Location: index.php");
    exit();
}

// Check if edit action is requested
if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $student = getStudentById($id);
    if (!$student) {
        echo "Student not found";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <!-- <link rel="stylesheet" href="/style.css"> -->
    <style>
        
body, h1, h2, h3, h4, h5, h6, p, ul, ol, li, figure, figcaption, blockquote, dl, dd {
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background-color: #222; /* Dark background color */
    color: #fff; /* Light text color */
    line-height: 1.6;
}

.container {
    max-width: 1200px; /* Adjust according to your layout */
    margin: 0 auto;
    padding: 20px;
    display: flex;
}

.left-section {
    width: 30%;
    margin-right: 20px;
}

.right-section {
    width: 68%;
}

h1, h2, h3, h4, h5, h6 {
    color: #fff; /* Light heading text color */
}

h2 {
    margin-bottom: 20px;
}

form {
    background-color: #333; /* Dark form background */
    padding: 20px;
    border-radius: 8px;
}

form label {
    display: block;
    margin-bottom: 10px;
    color: #ddd; /* Light label text color */
}

form input[type="number"],
form input[type="text"],
form input[type="email"] {
    width: calc(100% - 22px);
    padding: 8px;
    margin-bottom: 10px;
    border: none;
    border-radius: 4px;
    background-color: #444; /* Dark input background */
    color: #fff; /* Light input text color */
}

form button {
    background-color: #4CAF50; /* Green button color */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

form button:hover {
    background-color: #45a049; /* Darker green on hover */
}

form button:active {
    background-color: #3e8e41; /* Darker green on click */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #555; /* Dark table header background */
    color: #fff; /* Light table header text color */
}

tr:nth-child(even) {
    background-color: #444; /* Dark table row background */
}

tr:hover {
    background-color: #333; /* Darker table row on hover */
}

button {
    background-color: #333; /* Dark button color */
    color: #fff; /* Light button text color */
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}
#delete{
    background-color: red; /* Dark button color */
    color: #fff; 
}
button:hover {
    background-color: #555; /* Darker button on hover */
}

    </style>
</head>
<body>
    <div class="container">
        <!-- Add Form Section -->
        <div class="left-section">
            <h2>Add Student</h2>
            <form action="index.php" method="POST">
                <?php if (isset($student)): ?>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                <?php else: ?>
                    <input type="hidden" name="action" value="add">
                    <label for="student_id">Student ID:</label>
                    <input type="number" id="student_id" name="student_id" required><br>
                <?php endif; ?>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($student) ? $student['name'] : ''; ?>" required><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($student) ? $student['email'] : ''; ?>" required><br>
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" value="<?php echo isset($student) ? $student['subject'] : ''; ?>" required><br>
                <label for="marks">Marks:</label>
                <input type="number" id="marks" name="marks" value="<?php echo isset($student) ? $student['marks'] : ''; ?>" required><br>
                <button type="submit"><?php echo isset($student) ? 'Update Student' : 'Add Student'; ?></button>
            </form>
        </div>

        <!-- Display Section -->
        <div class="right-section">
            <h2>View Students</h2>
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Action</th>
                </tr>
                <?php
                $students = getAllStudents();
                foreach ($students as $student) {
                    echo "<tr>";
                    echo "<td>" . $student['student_id'] . "</td>";
                    echo "<td>" . $student['name'] . "</td>";
                    echo "<td>" . $student['email'] . "</td>";
                    echo "<td>" . $student['subject'] . "</td>";
                    echo "<td>" . $student['marks'] . "</td>";
                    echo "<td>";
                    echo "<form style='display: inline-block;' action='index.php' method='GET'>";
                    echo "<input type='hidden' name='action' value='edit'>";
                    echo "<input type='hidden' name='id' value='" . $student['student_id'] . "'>";
                    echo "<button type='submit'>Edit</button>";
                    echo "</form>";
                    echo "<form style='display: inline-block;' action='index.php' method='GET'>";
                    echo "<input type='hidden' name='action' value='delete'>";
                    echo "<input type='hidden' name='id' value='" . $student['student_id'] . "'>";
                    echo "<button type='submit' id='delete'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
