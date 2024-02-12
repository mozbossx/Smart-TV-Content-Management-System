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

// Check if the user is not 'Admin', redirect to the user_home
if ($_SESSION['user_type'] !== 'Admin') {
    header('location: user_home.php');
    exit;
}

// Fetch data from the smart_tvs_tb table
$sqlAllSmartTVs = "SELECT * FROM smart_tvs_tb";
$resultAllSmartTVs = mysqli_query($conn, $sqlAllSmartTVs);

if (!$resultAllSmartTVs) {
    $error[] = "Error: " . mysqli_error($conn);
}

// Check if user data is found
if ($resultAllSmartTVs->num_rows > 0) {
    // Move the loop inside the if statement
} else {
    $error[] = "No user data found";
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
    <title>Manage Smart TVs</title>
</head>
<body>
    <div class="main-section" id="all-content">
        <?php include('top-navigation-bar.php'); ?>
        <?php include('sidebar.php'); ?>
        <div class="main-container">
            <div class="column1">
                <div class="content-inside-form">
                    <p class="display-info-form"><i class="fa fa-tv" style="padding-right: 6px"></i>Manage Smart TVs</p>
                    <div class="content-form">
                        <form method="POST" action="add-announcement.php">
                            <div class="button-flex-space-between">
                                <div class="left-side-button">
                                    <a href="admin_options.php" class="back-button"><i class="fa fa-arrow-left" style="padding-right: 5px"></i>Back</a>
                                </div>                                
                            </div>
                            <div class="line-separator"></div>
                            <?php include('error_message.php'); ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>TV Name</th>
                                        <th>TV Brand</th>
                                        <th>Device ID</th>
                                        <th>Operations</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Output data of each row for all users
                                    while ($row = $resultAllSmartTVs->fetch_assoc()) {
                                        echo "<tr>
                                                <td style=\"text-align:center;\">{$row['id']}</td>
                                                <td style=\"text-align:center;\">{$row['tv_name']}</td>
                                                <td style=\"text-align:center;\">{$row['tv_brand']}</td>
                                                <td style=\"text-align:center;\">{$row['device_id']}</td>
                                                <td style=\"text-align:center;\">";
                                        // Display appropriate operation buttons based on user status
                                            echo '<button type="button" class="approve-button"><i class="fa fa-check" style="margin-right: 5px"></i>Edit</button>
                                                  <button type="button" class="decline-button"><i class="fa fa-times" style="margin-right: 5px"></i>Delete</button>';
                                        
                                        echo "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            
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