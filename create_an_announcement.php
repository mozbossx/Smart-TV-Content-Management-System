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

$sql_tvs = "SELECT tv_name, device_id FROM smart_tvs_tb";
$result_tv = mysqli_query($conn, $sql_tvs);
$options_tv = '';

// Check if data is found
if (mysqli_num_rows($result_tv) > 0) {
    while ($row = mysqli_fetch_assoc($result_tv)) {
        // generate options for select based on TV names where tv_display is 'Classrooms'
        $options_tv .= '<option value="' . $row['tv_name'] . '" data-device-id="' . $row['device_id'] . '">' . $row['tv_name'] . '</option>';
    }
}

// Check if user data is found
if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
} else {
    $error[] = "No user data found";
}

if (isset($_POST['post'])) {
    $ann_author = $_SESSION['full_name'];
    $user_type = $_SESSION['user_type'];
    $department = $_SESSION['department'];
    $ann_body = mysqli_real_escape_string($conn, $_POST['ann_body']);
    $display_time = mysqli_real_escape_string($conn, $_POST['display_time']);
    $expiration_date = mysqli_real_escape_string($conn, $_POST['expiration_date']);
    $expiration_time = mysqli_real_escape_string($conn, $_POST['expiration_time']);
    $schedule_date = mysqli_real_escape_string($conn, $_POST['schedule_date']);
    $schedule_time = mysqli_real_escape_string($conn, $_POST['schedule_time']);
    $tv_display = mysqli_real_escape_string($conn, $_POST['tv_display']);
    $category = "Announcement";

    // Check if the user_type is not 'Admin'
    if ($user_type !== 'Admin') {
        $status = "Pending";
    } else {
        // Set a default status if user_type is 'Admin'
        $status = "Approved"; // Change this to the default status you want for Admin
    }

    // if user uploaded media (image, video)
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $media_tmp = $_FILES['media']['tmp_name'];
        $media_name = $_FILES['media']['name'];
        $media_type = $_FILES['media']['type'];
        $timestamp = time();
        $media_path = "announcements_media/" . $media_name . "_" . $timestamp;

        move_uploaded_file($media_tmp, $media_path);
        $sql = "INSERT INTO announcements_tb (ann_author, user_type, department, ann_body, display_time, expiration_date, expiration_time, schedule_date, schedule_time, tv_display, category, media_path, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssssss", $ann_author, $user_type, $department, $ann_body, $display_time, $expiration_date, $expiration_time, $schedule_date, $schedule_time, $tv_display, $category, $media_path, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // if no media uploaded
    else {
        $sql = "INSERT INTO announcements_tb (ann_author, user_type, department, ann_body, display_time, expiration_date, expiration_time, schedule_date, schedule_time, tv_display, category, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssss", $ann_author, $user_type, $department, $ann_body, $display_time, $expiration_date, $expiration_time, $schedule_date, $schedule_time, $tv_display, $category, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
} else {
    // Initialize variables to store the form values
    $ann_body = "";
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
    <link rel="stylesheet" href="style_tv.css">
    <title>Create an Announcement</title>
</head>
<body>
    <div class="main-section" id="all-content">
        <?php include('top-navigation-bar.php'); ?>
        <?php include('sidebar.php'); ?>
        <div class="main-container">
            <div class="column1">
                <div class="content-inside-form">
                    <p class="display-info-form"><i class="fa fa-bullhorn" style="padding-right: 6px"></i>Create an Announcement</p>
                    <div class="content-form">
                        <form id="announcementForm" method="POST" action="create_an_announcement.php" enctype="multipart/form-data">
                            <div class="button-flex-space-between">
                                <div class="left-side-button">
                                    <a href="create_post.php" class="back-button"><i class="fa fa-arrow-left" style="padding-right: 5px"></i>Back</a>
                                </div>
                                <div class="right-side-button-preview">
                                    <button type="button" name="preview" id="previewButton" class="preview-button" onclick="validateAndOpenPreviewModal()">
                                        Preview <i class="fa fa-eye" style="padding-left: 5px"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="line-separator"></div>
                            <?php include('error_message.php'); ?>
                            <div class="floating-label-container">
                                <textarea name="ann_body" rows="3" required placeholder=" " style="background: #FFFF" class="floating-label-input-text-area" id="autoExpand1" <?php echo $ann_body; ?>></textarea>
                                <label for="ann_body" class="floating-label-text-area">Announcement Body</label>
                            </div>
                            <div class="right-flex">
                                <div class="rounded-container-media">
                                    <p class="input-container-label">Upload Media (Optional)</p>
                                    <input type="file" name="media" id="media" accept="video/*, image/*" onchange="previewMedia()" hidden>
                                    <label for="media" class="choose-file-button">Choose File (.mp4, .jpg, .png)</label>
                                    <div class="preview-media" style="border: #000 1px solid; border-radius: 5px; background: white; text-align: center; width: 100%; height: 350px; display: none; justify-content: center; align-items: center; margin-top: 15px">
                                        <video id="video-preview" width="100%" height="350px" controls style="display:none; border-radius: 5px; background: #000;"></video>
                                        <img id="image-preview" style="display:none; max-width: 100%; max-height: 100%;">
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-container">
                                <div class="left">
                                    <div class="rounded-container-column">
                                        <p class="input-container-label">Expiration Date & Time</p>
                                        <div class="left-flex">
                                            <input type="date" name="expiration_date" class="input-date">
                                        </div>
                                        <div class="right-flex">
                                            <input type="time" name="expiration_time" class="input-time">
                                        </div>
                                    </div>
                                </div>
                                <div class="right">
                                <div class="rounded-container-column">
                                        <p class="input-container-label">Schedule Post Date & Time (Optional)</p>
                                        <div class="left-flex">
                                            <input type="date" name="schedule_date" class="input-date">
                                        </div>
                                        <div class="right-flex">
                                            <input type="time" name="schedule_time" class="input-time">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="floating-label-container">
                                <select name="display_time" class="floating-label-input" required style="background: #FFFF">
                                    <option value="">~</option>
                                    <option value="10">10 seconds</option>
                                    <option value="11">11 seconds</option>
                                    <option value="12">12 seconds</option>
                                    <option value="13">13 seconds</option>
                                    <option value="14">14 seconds</option>
                                    <option value="15">15 seconds</option>
                                    <option value="16">16 seconds</option>
                                    <option value="17">17 seconds</option>
                                    <option value="18">18 seconds</option>
                                    <option value="19">19 seconds</option>
                                    <option value="20">20 seconds</option>
                                    <option value="21">21 seconds</option>
                                    <option value="22">22 seconds</option>
                                    <option value="23">23 seconds</option>
                                    <option value="24">24 seconds</option>
                                    <option value="25">25 seconds</option>
                                    <option value="26">26 seconds</option>
                                    <option value="27">27 seconds</option>
                                    <option value="28">28 seconds</option>
                                    <option value="29">29 seconds</option>
                                    <option value="30">30 seconds</option>
                                </select>
                                <label for="display_time" class="floating-label">Display Time (seconds)</label>
                            </div>
                            <div class="floating-label-container">
                                <select name="tv_display" class="floating-label-input" style="background: #FFFF" id="tv_account_select">
                                    <option value="">~</option>
                                    <?php echo $options_tv;?>
                                    <option value="All Smart TVs">All Smart TVs</option>
                                </select>
                                <label for="tv_display" class="floating-label">TV Display</label>
                            </div>
                            <div id="previewModal" class="modal">
                                <div class="modal-content-preview">
                                    <p class="display-info-form"><i class="fa fa-eye" style="padding-right: 5px"></i> Preview</p>
                                    <div class="flex-preview-content">
                                        <div class="preview-website" id="externalProjectPreview">
                                            <div class="topbar">
                                                <div class="device-id">
                                                    
                                                </div>
                                                <h1 class="tv-name">
                                                    
                                                </h1>
                                                <div class="date-time">
                                                    <span id="live-clock"></span>
                                                    <span id="live-date"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="preview-content" id="previewContent"></div>
                                    </div>
                                    
                                    <!-- Operation buttons -->
                                    <div class="flex-button-modal" style="margin-top: 10px">
                                        <button type="button" id="cancelButton" class="close-button" onclick="closePreviewModal()">Cancel</button>
                                        <button type="submit" name="post" class="submit-button">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <div id="errorModal" class="modal">
                                <div class="modal-error-message">
                                    <p class="display-info-form"><i class="fa fa-exclamation-triangle" style="padding-right: 5px"></i> Error</p>
                                    <p id="errorText" style="margin-left: 8px; margin-top: 8px; margin-right: 8px"></p>
                                    <button type="button" id="okayButton" class="okay-button" onclick="closeErrorModal()">Okay</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('cancelButton').addEventListener('click', function() {
            closePreviewModal();
        });

        document.getElementById('okayButton').addEventListener('click', function() {
            closeErrorModal();
        });
        
        function validateAndOpenPreviewModal() {
            var annBody = document.querySelector('[name="ann_body"]').value;
            var displayTime = document.querySelector('[name="display_time"]').value;
            var tvDisplay = document.querySelector('[name="tv_display"]').value;
            var expirationDate = document.querySelector('[name="expiration_date"]').value;
            var expirationTime = document.querySelector('[name="expiration_time"]').value;
            var scheduleDate = document.querySelector('[name="schedule_date"]').value;
            var scheduleTime = document.querySelector('[name="schedule_time"]').value;

            // Check if any of the required fields is empty
            if (annBody.trim() === "" || displayTime === "" || tvDisplay === "" || expirationDate === "" || expirationTime === "") {
                // If conditions are not met, show error message
                errorModalMessage("Please fill the necessary fields.");
            } else {
                // Check if expiration date and time are in the past
                var expirationDateTime = new Date(expirationDate + ' ' + expirationTime);
                var currentDateTime = new Date();

                if (expirationDateTime < currentDateTime) {
                    errorModalMessage("Expiration date and time should not be behind the present time.");
                } else {
                    // Check if schedule date and time are in the past
                    if (scheduleDate !== "" && scheduleTime !== "") {
                        var scheduleDateTime = new Date(scheduleDate + ' ' + scheduleTime);

                        if (scheduleDateTime < currentDateTime) {
                            errorModalMessage("Schedule date and time should not be behind the present time.");
                            return;
                        }
                    }

                    // If conditions are met, enable the button and open the preview modal
                    openPreviewModal();
                }
            }
        }

        function errorModalMessage(errorMessage) {
            var modal = document.getElementById('errorModal');
            modal.style.display = 'flex';

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            // Display error message
            document.getElementById('errorText').textContent = errorMessage;

            // Okay Button click event
            document.getElementById('okayButton').addEventListener('click', function () {
                modal.style.display = 'none';
            });
        }
        
        document.getElementById('autoExpand1').addEventListener('input', autoExpand1);
        function autoExpand1() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        }

        // Function to preview selected video or image
        function previewMedia() {
            var mediaInput = document.getElementById('media');
            var videoPreview = document.getElementById('video-preview');
            var imagePreview = document.getElementById('image-preview');
            var previewMedia = document.querySelector('.preview-media'); // Selecting the preview-media element

            if (mediaInput.files && mediaInput.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var fileType = mediaInput.files[0].type;

                    if (fileType.startsWith('video/')) {
                        // Display video preview
                        videoPreview.src = e.target.result;
                        videoPreview.style.display = 'block';
                        imagePreview.style.display = 'none';
                    } else if (fileType.startsWith('image/')) {
                        // Display image preview
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        videoPreview.style.display = 'none';
                    }

                    // Show the preview-media container
                    previewMedia.style.display = 'flex';
                };

                reader.readAsDataURL(mediaInput.files[0]);
            }
        }

        floatingInput.addEventListener("input", function () {
            // Trigger the focus event when the input value is not empty
            if (this.value.trim() !== "") {
                this.focus();
            }
        });

        // Function to open the preview modal
        function openPreviewModal() {
            var modal = document.getElementById('previewModal');
            modal.style.display = 'flex';

            // Get the selected tv_name and device_id from the dropdown
            var selectedOption = document.querySelector('[name="tv_display"]');
            var selectedTvName = selectedOption.value;
            var selectedDeviceId = selectedOption.options[selectedOption.selectedIndex].getAttribute('data-device-id');

            // Display the tv_name and device_id in the modal
            document.querySelector('.tv-name').textContent = selectedTvName;

            // Clear previous content and append the new content for Device ID
            var deviceIdContainer = document.querySelector('.device-id');
            deviceIdContainer.innerHTML = ''; // Clear previous content

            // Add a new paragraph for "Device ID: "
            var deviceLabelText = document.createElement('p');
            deviceLabelText.textContent = 'Device ID: ';
            deviceIdContainer.appendChild(deviceLabelText);

            // Append the device_id after the text
            var deviceIdParagraph = document.createElement('p');
            deviceIdParagraph.textContent = selectedDeviceId;
            deviceIdContainer.appendChild(deviceIdParagraph);

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            // Display the preview content in the modal
            document.getElementById('previewContent').innerHTML = getPreviewContent();

            // Start the clock when modal opens
            updateClock();
        }

        function submitForm() {
            // Trigger form submission
            document.getElementById('announcementForm').submit();
        }

        // Function to close the preview modal
        function closePreviewModal() {
            var modal = document.getElementById('previewModal');
            modal.style.display = 'none';
        }

        // Function to close the preview modal
        function closeErrorModal() {
            var modal = document.getElementById('errorModal');
            modal.style.display = 'none';
        }

        // Function to get the preview content
        function getPreviewContent() {
            // Function to format date and time
            function formatDateTime(dateString, timeString) {
                const dateTime = new Date(dateString + ' ' + timeString);
                const options = {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: true
                };
                return new Intl.DateTimeFormat('en-US', options).format(dateTime);
            }

            var previewContent = '';
            previewContent += '<p class="preview-input"><strong>Display Time: </strong><br>' + document.querySelector('[name="display_time"]').value + ' seconds</p>';
            previewContent += '<p class="preview-input"><strong>Expiration Date & Time: </strong><br>' + formatDateTime(document.querySelector('[name="expiration_date"]').value, document.querySelector('[name="expiration_time"]').value) + '</p>';
            previewContent += '<p class="preview-input"><strong>Schedule Post Date & Time: </strong><br>' + (document.querySelector('[name="schedule_date"]').value ? formatDateTime(document.querySelector('[name="schedule_date"]').value, document.querySelector('[name="schedule_time"]').value) : 'Not scheduled') + '</p>';
            previewContent += '<p class="preview-input"><strong>TV Display: </strong><br>' + document.querySelector('[name="tv_display"]').value + '</p>';
            return previewContent;
        }
        
        // Function to update the clock
        function updateClock() {
            const now = new Date();
            const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12; // Convert to 12-hour format
            const dayOfWeek = daysOfWeek[now.getDay()]; // Get day of the week
            const month = months[now.getMonth()]; // Get full month name
            const day = now.getDate().toString().padStart(2, '0');
            const year = now.getFullYear();

            document.getElementById('live-clock').textContent = hours + ':' + minutes + ' ' + ampm;
            document.getElementById('live-date').textContent = dayOfWeek + ', ' + month + ' ' + day + ', ' + year;
        }

        // Update the clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial update
    </script>
</body>
</html>