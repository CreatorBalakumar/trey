<?php
// Connect to the database
$servername = "184.168.98.120";
$username = "littlehayagriva"; // Database username
$password = "Q,28_i5m=)lK"; // Database password
$dbname = "hayagriva"; // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = $conn->real_escape_string($_POST['name']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $address = $conn->real_escape_string($_POST['address']);
    $message = $conn->real_escape_string($_POST['message']);
    $enquiryType = $conn->real_escape_string($_POST['enquiryType']);

    // Handle file upload (optional)
    $fileName = null; // Variable to hold the file name
    if (isset($_FILES['uploadResume']) && $_FILES['uploadResume']['error'] == 0) {
        $targetDir = "assets/uploads/"; // Define the upload directory
        $fileName = time() . "_" . basename($_FILES["uploadResume"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow only certain file formats
        $allowedTypes = ['pdf', 'doc', 'docx'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["uploadResume"]["tmp_name"], $targetFilePath)) {
                // File uploaded successfully
            } else {
                echo "<script>alert('Failed to upload resume. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only PDF, DOC, and DOCX files are allowed.');</script>";
        }
    }

    // Check if the mobile number already exists in the database
    $check_mobile_sql = "SELECT COUNT(*) AS mobile_count FROM enquiryform WHERE mobile = '$mobile'";
    $result = $conn->query($check_mobile_sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['mobile_count'] > 0) {
            // Mobile number already exists
            echo "<script type='text/javascript'>
                alert('This mobile number is already submitted!');
                window.location.href = 'enquiry.html';
            </script>";
        } else {
            // Insert data into the database
            $sql = "INSERT INTO enquiryform (name, mobile, address, message, enquiryType, uploadResume) 
                    VALUES ('$name', '$mobile', '$address', '$message', '$enquiryType', '$fileName')";

            if ($conn->query($sql) === TRUE) {
                echo "<script type='text/javascript'>
                    alert('Enquiry submitted successfully!');
                    window.location.href = 'enquiry.html';
                </script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } else {
        echo "Error: Could not check mobile number existence. Please try again later.";
    }
}

$conn->close();
?>