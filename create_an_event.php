<?php
// Start the session and include the configuration
session_start();
include 'config_connection.php';

// Fetch announcements and events content and assign it to $result
// header("Cache-Control: no-cache");
header("Connection: keep-alive");

// Add cache control headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['full_name'])) {
    header('location: index.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

// After successfully logging out
$_SESSION['logged_out'] = true;

// Get the user's department from the session
$department = $_SESSION['department'];

// Fetch user data for the currently logged-in user
$full_name = $_SESSION['full_name'];
$sql = "SELECT full_name AS full_name, password, department, email, user_type FROM users_tb WHERE full_name = '$full_name'";
$result = mysqli_query($conn, $sql);

// Check if user data is found
if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
} else {
    $error[] = "No user data found";
}

// Check if the user is an Admin
if ($user_data['user_type'] === 'Admin') {
    $loggedInUserIsAdmin = true;
} else {
    $loggedInUserIsAdmin = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link rel="icon" type="image/png" href="images/usc_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Create an Event</title>
</head>
<body>
    <div class="main-section" id="all-content">
        <?php include('top-navigation-bar.php'); ?>
        <?php include('sidebar.php'); ?>
        <div class="main-container">
            <div class="column1">
                <div class="content-inside-form">
                    <p class="display-info-form"><i class="fa fa-calendar-check-o" style="padding-right: 6px"></i>Create an Upcoming Event</p>
                    <div class="content-form">
                        <form method="POST" action="add-announcement.php">
                            <div class="button-flex-space-between">
                                <div class="left-side-button">
                                    <a href="create_post.php" class="back-button"><i class="fa fa-arrow-left" style="padding-right: 5px"></i>Back</a>
                                </div>
                                <div class="right-side-button-preview">
                                    <a href="create_post.php" class="preview-button">Preview<i class="fa fa-eye" style="padding-left: 5px"></i></a>
                                </div>
                            </div>
                            <div class="line-separator"></div>
                            <?php include('error_message.php'); ?>
                            
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('autoExpand1').addEventListener('input', autoExpand1);
        function autoExpand1() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        }

        // Function to preview selected video
        function previewVideo() {
            var videoInput = document.getElementById('video');
            var videoPreview = document.getElementById('video-preview');
            var linebreak = document.getElementById('line-break');

            if (videoInput.files && videoInput.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    videoPreview.src = e.target.result;
                    videoPreview.style.display = 'block';
                    linebreak.style.display = 'block';
                };

                reader.readAsDataURL(videoInput.files[0]);
            }
        }

        floatingInput.addEventListener("input", function () {
            // Trigger the focus event when the input value is not empty
            if (this.value.trim() !== "") {
                this.focus();
            }
        });
        
        
    </script>
</body>
</html>