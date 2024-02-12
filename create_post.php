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
    <title>Create Post</title>
</head>
<body>
    <div class="main-section" id="all-content">
        <?php include('top-navigation-bar.php'); ?>
        <?php include('sidebar.php'); ?>
        <div class="main-container">
            <div class="column1">
                <div class="content-inside-form">
                    <p class="display-info-form"><i class="fa fa-pencil-square" style="padding-right: 6px"></i>Create Post</p>
                    <?php include('error_message.php'); ?>
                    <div class="button-flex" style="padding: 5px">
                        <div class="button-container">
                            <a href="create_an_announcement.php" class="content-button">
                                <div class="button-icon"><i class="fa fa-bullhorn"></i></div>
                                <div class="button-text">Announcement</div>
                            </a>
                        </div>
                        <div class="button-container">
                            <a href="create_an_event.php" class="content-button">
                                <div class="button-icon"><i class="fa fa-calendar-check-o"></i></div>
                                <div class="button-text">Upcoming Event</div>
                            </a>
                        </div>
                        <div class="button-container">
                            <a href="create_a_news.php" class="content-button">
                                <div class="button-icon"><i class="fa fa-newspaper-o"></i></div>
                                <div class="button-text">News</div>
                            </a>
                        </div>
                        <div class="button-container">
                            <a href="create_a_promotional_material.php" class="content-button">
                                <div class="button-icon"><i class="fa fa-object-group"></i></div>
                                <div class="button-text">Promotional Material</div>
                            </a>
                        </div>
                        <?php if ($user_type === 'Admin'){ ?>
                            <div class="button-container">
                                <a href="create_a_general_information.php" class="content-button">
                                    <div class="button-icon"><i class="fa fa-sitemap"></i></div>
                                    <div class="button-text">General Information</div>
                                </a>
                            </div>
                            <div class="button-container">
                                <a href="add_new_feature.php" class="content-button">
                                    <div class="button-icon-2"><i class="fa fa-plus"></i></div>
                                    <div class="button-text-2">Add New Feature</div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>