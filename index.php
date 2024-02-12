<?php
@include 'config_connection.php';
session_start();

// Check if the user has explicitly logged out
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    unset($_SESSION['full_name']);
    header('location: index.php'); // Redirect to the login page
    exit;
}

// Check if the user is already logged in, redirect to user-home.php
if (isset($_SESSION['full_name'])) {
    header('location: user_home.php');
    exit;
}

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error[] = "Password should be at least 6 characters long.";
    } else {
        $pass = md5($password);

        $select = "SELECT * FROM users_tb WHERE email = '$email' && password = '$pass'";
        $result = mysqli_query($conn, $select);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);

            // Fetch the user type from the database
            $user_type = $row['user_type'];

            // Store user details in session
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['department'] = $row['department'];
            $_SESSION['user_type'] = $user_type; // Set the user_type in the session

            header('location: user_home.php');
            exit;
        } else {
            $error[] = 'These credentials do not match our records!';
        }
    }
} else {
    $email = "";
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
    <title>Login</title>
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
                <p class="page-title">Sign In</p>
                <?php include('error_message.php'); ?>
                <div class="floating-label-container">
                    <input type="email" name="email" required placeholder=" " class="floating-label-input" value="<?php echo $email; ?>">
                    <label for="email" class="floating-label">USC Email</label>
                </div>
                <div class="floating-label-container">
                    <input type="password" name="password" required placeholder=" " class="floating-label-input">
                    <label for="password" class="floating-label">Password</label>
                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                </div>
                <p style="text-align: right">
                    <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                </p>
                <br>
                <input type="submit" name="submit" value="Login" class=login-button>
                <div class="line-separator"></div>
                <p style="text-align: center; user-select: none">Not a member? <a href="create_account.php" class="create-account-link">Create Account</a></p>
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