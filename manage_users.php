<?php
// Start the session and include the configuration
session_start();
include 'config_connection.php';

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['full_name'])) {
    header('location: index.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);

// After successfully logging out
$_SESSION['logged_out'] = true;

// Fetch the current user's data
$sqlCurrentUser = "SELECT * FROM users_tb";
$resultCurrentUser = mysqli_query($conn, $sqlCurrentUser);

if (!$resultCurrentUser) {
    $error[] = "Error: " . mysqli_error($conn);
}

// Check if the user is not 'Admin', redirect to the user_home
if ($_SESSION['user_type'] !== 'Admin') {
    header('location: user_home.php');
    exit;
}

// Fetch data from the users_tb table
$sqlAllUsers = "SELECT * FROM users_tb";
$resultAllUsers = mysqli_query($conn, $sqlAllUsers);

if (!$resultAllUsers) {
    $error[] = "Error: " . mysqli_error($conn);
}

// Check if user data is found
if ($resultAllUsers->num_rows > 0) {
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
    <title>Manage Users</title>
</head>
<body>
    <div class="main-section" id="all-content">
        <?php include('top-navigation-bar.php'); ?>
        <?php include('sidebar.php'); ?>
        <div class="main-container">
            <div class="column1">
                <div class="content-inside-form">
                    <p class="display-info-form"><i class="fa fa-users" style="padding-right: 6px"></i>Manage Users</p>
                    <div class="content-form">
                        <form method="POST">
                            <div class="button-flex-space-between">
                                <div class="left-side-button">
                                    <a href="admin_options.php" class="back-button"><i class="fa fa-arrow-left" style="padding-right: 5px"></i>Back</a>
                                </div>
                                <div class="right-side-button-preview">
                                    <a href="create_post.php" class="preview-button">Add a User<i class="fa fa-plus" style="padding-left: 5px"></i></a>
                                </div>
                            </div>
                            <div class="line-separator"></div>
                            <?php include('error_message.php'); ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Operations</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Output data of each row for all users
                                    while ($row = $resultAllUsers->fetch_assoc()) {
                                        echo "<tr>
                                                <td style=\"text-align:center;\">{$row['id']}</td>
                                                <td>{$row['full_name']}</td>
                                                <td>{$row['email']}</td>
                                                <td style=\"text-align:center;\">{$row['user_type']}</td>
                                                <td style=\"text-align:center;\">{$row['department']}</td>
                                                <td style=\"text-align:center;\">{$row['status']}</td>
                                                
                                                <td style=\"text-align:center;\">";
                                        // Display operation buttons based on user status
                                        if ($row['status'] === 'Pending') {
                                            echo '<button type="button" class="approve-button"><i class="fa fa-check" style="margin-right: 5px"></i>Approve</button>
                                                  <button type="button" class="decline-button"><i class="fa fa-times" style="margin-right: 5px"></i>Decline</button>';
                                        } elseif ($row['status'] === 'Approved') {
                                            echo '<button type="button" class="edit-button"><i class="fa fa-pencil-square-o" style="margin-right: 5px"></i>Edit</button>
                                                  <button type="button" class="delete-button"><i class="fa fa-trash" style="margin-right: 5px"></i>Delete</button>';
                                        }
                                        echo "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <div id="editModal" class="modal">
                                <div class="modal-content-preview">
                                    <span class="close">&times;</span>
                                    <h2>Edit User</h2>
                                    <form id="editForm">
                                        <label for="fullName">Full Name:</label>
                                        <input type="text" id="fullName" name="fullName" required>
                                        
                                        <label for="userType">User Type:</label>
                                        <input type="text" id="userType" name="userType" required>
                                        
                                        <label for="department">Department:</label>
                                        <input type="text" id="department" name="department" required>
                                        
                                        <label for="status">Status:</label>
                                        <input type="text" id="status" name="status" required>
                                        
                                        <button type="submit">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Get the modal
        var modal = document.getElementById('editModal');

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName('close')[0];

        // Get the edit button elements
        var editButtons = document.getElementsByClassName('edit-button');

        // Function to open the modal with user data
        function openEditModal(fullName, userType, department, status) {
            document.getElementById('fullName').value = fullName;
            document.getElementById('userType').value = userType;
            document.getElementById('department').value = department;
            document.getElementById('status').value = status;
            modal.style.display = 'flex';
        }

        // Function to close the modal
        function closeEditModal() {
            modal.style.display = 'none';
        }

        // Add click event listeners to edit buttons
        for (var i = 0; i < editButtons.length; i++) {
            editButtons[i].addEventListener('click', function() {
                // Fetch the user data from the row
                var row = this.parentNode.parentNode;
                var fullName = row.children[1].textContent;
                var userType = row.children[3].textContent;
                var department = row.children[4].textContent;
                var status = row.children[5].textContent;

                // Open the modal with the user data
                openEditModal(fullName, userType, department, status);
            });
        }

        // Add click event listener to close button
        span.addEventListener('click', closeEditModal);

        // Add click event listener to close modal when clicking outside the modal
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                closeEditModal();
            }
        });

        // Add submit event listener to form
        document.getElementById('editForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Handle form submission and update the user data
            // (You may need to use AJAX to send the data to the server and update the database)
            // After updating, close the modal
            closeEditModal();
        });

    </script>
</body>
</html>
