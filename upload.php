<?php
require_once('config.php');

// Set headers to allow cross-origin resource sharing (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK && isset($_POST['id'])) {
        $targetDir = "uploads/";
        $tempFile = $_FILES["image"]["tmp_name"];
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);


        $allowedTypes = array('jpg', 'jpeg', 'png');
        $fileExtensation = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        if (!in_array($fileExtensation, $allowedTypes)) {
            http_response_code(401);
            $response = array("success" => false, "message" => "Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
        } else {
            if (move_uploaded_file($tempFile, $targetFile)) {

                $query = "Update users 
                set profile_pic = '" . $targetFile . "'
                where id = " . $_POST['id'];
                $result = mysqli_query($con, $query);
                if (mysqli_affected_rows($con) != 0) {
                    http_response_code(200);
                    $response = array("success" => true, "message" => "Image uploaded successfully.", "profile_pic" => $targetFile);
                }
            } else {
                http_response_code(401);
                $response = array("success" => false, "message" => "Failed to move uploaded file.");
            }
        }

    } else {
        http_response_code(401);
        $response = array("success" => false, "message" => "No image file sent or upload error occurred.");
    }
} else {
    http_response_code(401);
    $response = array("success" => false, "message" => "Invalid request method.");
}

echo json_encode($response);
