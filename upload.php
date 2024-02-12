<?php
session_start();
include_once('db_connection.php');
$errorMsg = '';
$isSuspiciousOrRejected = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['filename'])) {
        $allowedFileTypes = array('jpg', 'png', 'pdf', 'docx');
        $uploadFileType = strtolower(pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($uploadFileType, $allowedFileTypes)) {
          $isSuspiciousOrRejected = true;
          $errorMsg = "Sorry, only JPG, PNG, PDF, and DOCX files are allowed.";
        }
        
        // Set maximum file size to 5MB
        $maxFileSize = 5 * 1024 * 1024;  
        if ($_FILES['filename']['size'] > $maxFileSize) {
          $isSuspiciousOrRejected = true;
          $errorMsg = "Sorry, your file is too large.";
        }
        
        $fileName = basename($_FILES['filename']['name']);
        $fileName = preg_replace("/[^a-zA-Z0-9-_\.]/", "", $fileName); 
        $uniqueFileName  = uniqid() . '_' . $fileName; 
        $uploadDirectory = 'uploads/'; 
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true); 
        }
        // Move uploaded file to upload directory
        $targetPath = $uploadDirectory . $uniqueFileName ;
        if (move_uploaded_file($_FILES['filename']['tmp_name'], $targetPath)) {
          $errorMsg = "File uploaded successfully.";
        } else {
          $isSuspiciousOrRejected = true;
          $errorMsg = "Failed to upload file.";
        }
      // Log any suspicious or rejected uploads
          if ($isSuspiciousOrRejected) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $uploadTimestamp = date('Y-m-d H:i:s');
            $userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; 
            $insertLogsQuery = "INSERT INTO logs (user_id, file_name, ip_address,is_suspicious_or_rejected, upload_timestamp) VALUES ('$userID', '$uniqueFileName', '$ipAddress','1', '$uploadTimestamp')";

            if (mysqli_query($connection, $insertLogsQuery)) {
              $errorMsg = "Upload logged as suspicious or rejected.";
            } else {
              $errorMsg =  "Error: " . $insertLogsQuery . "<br>" . mysqli_error($connection);
            }
          }
          else{
    $uploadTimestamp = date('Y-m-d H:i:s');
    $userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; 

    // Insert into uploads table
    $insertUploadsQuery = "INSERT INTO uploads (user_id, file_name, upload_timestamp) VALUES ('$userID', ' $uniqueFileName', '$uploadTimestamp')";
    if (mysqli_query($connection, $insertUploadsQuery)) {
      $errorMsg =  'success insertion';
    } else {
      $errorMsg =  "Error: " . $insertUploadsQuery . "<br>" . mysqli_error($connection);
    }

    // Insert into logs table
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $insertLogsQuery = "INSERT INTO logs (user_id, file_name, ip_address,is_suspicious_or_rejected, upload_timestamp) VALUES ('$userID', '$uniqueFileName', '$ipAddress','0', '$uploadTimestamp')";
    if (mysqli_query($connection, $insertLogsQuery)) {
      $errorMsg = "log Success";
    } else {
      $errorMsg = "Error: " . $insertLogsQuery . "<br>" . mysqli_error($connection);
    }

   } } else {
      $errorMsg = "No file uploaded";
    }
}
?>





<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
  </head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h2 class="text-center mb-4">Upload File</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div><?php echo $errorMsg; ?></div> <!-- Display error message here -->
  <div class="form-group">
    <label for="img"> Choose File:</label><br>
    <input type="file" name="filename" required />
  </div>
  <input type="submit" class="btn btn-primary" value="Submit">
</form>
</div>
</div>
</div>
</body>
</html>


