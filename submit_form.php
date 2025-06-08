<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // STEP 1: Create connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mental_health";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // STEP 2: Validate and sanitize inputs
    $required = ['Name', 'Email', 'Phone', 'Age', 'Gender', 'Q1', 'Q2', 'Q3', 'Q4', 'Q5'];
    $missing = [];

    foreach ($required as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        echo "<h3>Please complete all fields in the survey.</h3>";
        echo "<p>Missing fields:</p><ul>";
        foreach ($missing as $miss) {
            echo "<li>$miss</li>";
        }
        echo "</ul>";
        $conn->close();
        exit();
    }

    // STEP 3: Sanitize input values
    $name = htmlspecialchars(trim($_POST['Name']));
    $email = filter_var(trim($_POST['Email']), FILTER_VALIDATE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST['Phone']);
    $age = intval($_POST['Age']);
    $gender = htmlspecialchars(trim($_POST['Gender']));
    $q1 = intval($_POST['Q1']);
    $q2 = intval($_POST['Q2']);
    $q3 = intval($_POST['Q3']);
    $q4 = intval($_POST['Q4']);
    $q5 = intval($_POST['Q5']);

    if (!$email) {
        die("Invalid email format.");
    }

    // STEP 4: Score calculation
    $total_score = $q1 + $q2 + $q3 + $q4 + $q5;

    // Store in session
    $_SESSION['form1_answers'] = [
        'Name' => $name,
        'Email' => $email,
        'Phone' => $phone,
        'Age' => $age,
        'Gender' => $gender,
        'Q1' => $q1,
        'Q2' => $q2,
        'Q3' => $q3,
        'Q4' => $q4,
        'Q5' => $q5,
        'total_score' => $total_score
    ];

    // Optional: Insert into database
    /*
    $stmt = $conn->prepare("INSERT INTO mental_health_survey (name, email, phone, age, gender, q1, q2, q3, q4, q5, total_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissiiiii", $name, $email, $phone, $age, $gender, $q1, $q2, $q3, $q4, $q5, $total_score);
    $stmt->execute();
    $stmt->close();
    */

    $conn->close();

    // Redirect based on score
    if ($total_score >= 15) {
        header("Location: form2_1.html");
    } else {
        header("Location: form2_2.html");
    }
    exit();

} else {
    echo "<p>Form not submitted properly.</p>";
}
?>
