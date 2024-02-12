<?php
session_start();
@include 'config_connection.php';

if (isset($_SESSION['mail'])) {
    $email = $_SESSION['mail'];
} else {
    $error[] = "No email found in the session";
    header('location: create_account.php');
    exit;
}

if (isset($_POST["submit"])) {
    $otp = $_SESSION['otp'];
    $email = $_SESSION['mail'];
    $otp_code = $_POST['otp_code'];

    if ($otp != $otp_code) {
        echo "<script>alert('Invalid OTP code');</script>";
    } else {
        // Retrieve user details from session set in register-form.php
        $userDetails = $_SESSION['registration_details']; // Fetch the stored registration details
        $full_name = $userDetails['full_name'];
        $email = $userDetails['email'];
        $password = $userDetails['password'];
        $department = $userDetails['department'];
        $user_type = $userDetails['user_type'];

        $status = 'Pending';

        // Insert user data into the database
        $insertQuery = "INSERT INTO users_tb (full_name, email, password, department, user_type, status) VALUES ('$full_name', '$email', '$password', '$department', '$user_type', '$status')";
        
        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Verify account done. You may sign in now.');</script>";
            // Redirect to login or homepage after successful registration
            echo "<script>window.location.replace('index.php');</script>";
            session_unset();
            session_destroy();
            exit;
        } else {
            echo "<script>alert('Failed to insert data. Please try again.');</script>";
        }
    }
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
    <link rel="stylesheet" href="style.css">
    <title>Email Verification</title>
</head>
<body class="main-body">
    <div class="container-body">
        <div class="container-flex top">
            <div class="logo-flex">
                <div class="left">
                    <img src="images/usc_icon.png" class="usc-logo">
                </div>
                <div class="right">
                    <img src="images/soe_icon.png" class="soe-logo">
                </div>
            </div>
        </div>
        <div class="container-flex bottom">
            <form action="" method="post">
                <p class="page-title">Email Verification</p>
                <?php include('error_message.php'); ?>
                <p style="text-align: center; user-select: none">Please enter the 6-digit code we sent via your USC email:</p>
                <br>
                <p class="display-email"><?php echo $email; ?></p>
                <br>
                <p style="text-align: center; user-select: none">We want to make sure it is a valid USC email</p>
                <div class="floating-label-container">
                    <input type="text" name="otp_code" required placeholder=" " class="floating-label-input">
                    <label for="otp_code" class="floating-label">6-digit OTP Code</label>
                </div>
                <div class="line-separator"></div>
                <input type="submit" name="submit" value="Submit Code" class="login-button">
            </form>
        </div>
    </div>
    <script>
        // JavaScript to toggle password visibility and show/hide labels
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("[name='password']");
        const floatingInput = document.querySelector(".floating-label-input");

        togglePassword.addEventListener("click", function () {
            // Toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // Toggle the icon
            this.classList.toggle("bi-eye-slash");
            this.classList.toggle("bi-eye");
        });

        floatingInput.addEventListener("input", function () {
            // Trigger the focus event when the input value is not empty
            if (this.value.trim() !== "") {
                this.focus();
            }
        });
    </script>

</body>
</html>