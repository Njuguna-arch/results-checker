<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['student_name']) && isset($_POST['admission_number'])) {
        $student_name = $_POST['student_name'];
        $admission_number = $_POST['admission_number'];

        $servername = "localhost";
        $username = "root";
        $password = "1221";
        $dbname = "school";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the student ID
        $student_sql = "SELECT student_id FROM students WHERE name='$student_name' AND admission_number='$admission_number'";
        $student_result = $conn->query($student_sql);

        // Check if the query was successful
        if ($student_result === false) {
            die("Error: " . $conn->error);
        }

        if ($student_result->num_rows > 0) {
            $student_row = $student_result->fetch_assoc();
            $student_id = $student_row['student_id'];

            // Retrieve subjects and grades for the student
            $subject_sql = "SELECT * FROM subjects WHERE student_id='$student_id'";
            $subject_result = $conn->query($subject_sql);

            // Check if the query was successful
            if ($subject_result === false) {
                die("Error: " . $conn->error);
            }

            if ($subject_result->num_rows > 0) {
                $grades = [];
                while($row = $subject_result->fetch_assoc()) {
                    $grades[] = $row["grade"];
                }

                // Calculate mean grade
                $mean_grade = array_sum($grades) / count($grades);

                // Function to convert numerical grade to letter grade
                function convertToLetterGrade($grade) {
                    if ($grade >= 80) return 'A';
                    if ($grade >= 70) return 'B';
                    if ($grade >= 60) return 'C';
                    if ($grade >= 50) return 'D';
                    return 'E';
                }

                echo "<html>";
                echo "<head>";
                echo "<style>";
                echo "body { font-family: Arial, sans-serif; background-color:rgb(211, 211, 211); text-align: center; }";
                echo ".container { display: inline-block; text-align: left; }";
                echo "table { width: 100%; border-collapse: collapse; margin: 20px auto; }";
                echo "th, td { border: 1px solid #fff; padding: 8px; text-align: center; }";
                echo "th { background-color: #4CAF50; color: white; }";
                echo "h1,h2, h3 { text-align: center; }";
                echo "</style>";
                echo "</head>";
                echo "<body>";

                echo "<div class='container'>";
                echo "<h1> JEREMY GROUP OF SCHOOLS</h1>";
                echo "<h2> Exam Provisional Results</h2>";
                echo "<h3>Admission Number: $admission_number - $student_name</h3>";
                echo "<h3>Mean Grade: " . convertToLetterGrade($mean_grade) . "</h3>";
                echo "<table>";
                echo "<tr><th>CODE</th><th>SUBJECT NAME</th><th>GRADE</th></tr>";

                // Define the subject codes
                $subject_codes = [
                    'Mathematics' => 121,
                    'English' => 101,
                    'Physics' => 232,
                    'Kiswahili' => 102,
                    'Biology' => 231,
                    'Business Studies' => 453,
                    'Geography' => 537,
                    'History' => 234,
                    'Chemistry' => 233,
                    'Christian Religious Education' => 543
                ];

                // Fetch grades again for displaying in the table
                $subject_result->data_seek(0);
                while($row = $subject_result->fetch_assoc()) {
                    $subject_code = isset($subject_codes[$row["subject_name"]]) ? $subject_codes[$row["subject_name"]] : 'N/A';
                    $letter_grade = convertToLetterGrade($row["grade"]);
                    echo "<tr><td>" . $subject_code . "</td><td>" . $row["subject_name"]. "</td><td>" . $letter_grade. "</td></tr>";
                }

                echo "</table>";
                echo "</div>";

                echo "</body>";
                echo "</html>";
            } else {
                echo "No results found";
            }
        } else {
            echo "No student found with the given name and admission number.";
        }

        $conn->close();
    } else {
        echo "Please fill out both the student name and admission number.";
    }
} else {
    echo "Invalid request method.";
}
?>
