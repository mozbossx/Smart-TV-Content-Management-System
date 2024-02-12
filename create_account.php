<?php
session_start();
@include 'config_connection.php';
require 'phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $full_name = $_POST["full_name"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmpassword"];
    $user_type = $_POST["user_type"];
    $department = $_POST["department"];

    // Check for existing email or name
    $check_email_query = mysqli_query($conn, "SELECT * FROM users_tb WHERE email = '$email'");
    $check_name_query = mysqli_query($conn, "SELECT * FROM users_tb WHERE full_name = '$full_name'");
    $rowCountEmail = mysqli_num_rows($check_email_query);
    $rowCountName = mysqli_num_rows($check_name_query);

    // Check if full_name contains symbols and numbers
    $symbolCheck = preg_match('/[~`!1@2#3\$4%5\^6&7\*8\(9\)0_\-+=:;"<>,.\/?\|]/', $full_name);

    if (!empty($email) && !empty($password) && $password === $confirmPassword) {
        if ($rowCountEmail > 0) {
            $error[] = "User with this USC email already exists!";
        } elseif ($rowCountName > 0) {
            $error[] = "User with this name already exists!";
        } elseif ($symbolCheck) {
            $error[] = "Full Name must not contain symbols and numbers";
        } else {
            if (strlen($password) < 6) {
                $error[] = "Password should be at least 6 characters long.";
            } elseif ($department === "") {
                $error[] = "Please select a department.";
            } else {
                $password_hash = md5($password);

                // Store user details in the session instead of inserting into the database
                $_SESSION['registration_details'] = [
                    'full_name' => $full_name,
                    'email' => $email,
                    'password' => $password_hash,
                    'department' => $department,
                    'user_type' => $user_type,
                ];

                // Generate OTP for testing purposes
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['mail'] = $email;

                // Initialize PHPMailer
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';

                $mail->Username = 'usc.smarttvcms@gmail.com'; // Email to be used for sending OTP Code
                $mail->Password = 'nrul yhwc mmrk hwdp'; // Password of the email (app password)

                $mail->setFrom('usc.smarttvcms@gmail.com', 'USC CMS');
                $mail->addAddress($_POST["email"]);

                $mail->isHTML(true);
                $mail->Subject = "Your verification code";
                $mail->Body = "<p>From USC CMS,</p>
                <h3>Your verification OTP code is $otp</h3>
                <br><br>
                <p>Regards,<br>University of San Carlos</p>";

                if (!$mail->send()) {
                    $error[] = "Registration Failed! Error sending email. Try again";
                } else {
                    $successMessage = "OTP sent to: <br><strong>" .$email;
                    echo "<script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var modal = document.getElementById('myModal');
                                var proceedBtn = document.getElementById('proceedBtn');
                                var cancelBtn = document.getElementById('cancelBtn');
                                
                                modal.style.display = 'flex';
                                
                                proceedBtn.onclick = function() {
                                    window.location.replace('verify_account.php');
                                }
                                
                                cancelBtn.onclick = function() {
                                    modal.style.display = 'none';
                                }
                                
                                window.onclick = function(event) {
                                    if (event.target == modal) {
                                        modal.style.display = 'none';
                                    }
                                }
                            });
                          </script>";
                }
            }
        }
    } else {
        $error[] = "Passwords do not match!";
    }
}

else {
    // Initialize variables to store the form values
    $full_name = "";
    $email = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="images/usc_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Create Account</title>
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
                <p class="page-title">Create Account</p>
                <?php include('error_message.php'); ?>
                <div class="floating-label-container">
                    <input type="text" name="full_name" value="<?php echo $full_name; ?>" required placeholder=" " class="floating-label-input">
                    <label for="full_name" class="floating-label">Full Name</label>
                </div>
                <div class="floating-label-container">
                    <input type="email" name="email" value="<?php echo $email; ?>" required placeholder=" " class="floating-label-input">
                    <label for="email" class="floating-label">USC Email</label>
                </div>
                <div class="floating-label-container">
                    <input type="password" name="password" required placeholder=" " class="floating-label-input">
                    <label for="password" class="floating-label">Password</label>
                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                </div>
                <div class="floating-label-container">
                    <input type="password" name="confirmpassword" required placeholder=" " class="floating-label-input">
                    <label for="confirmpassword" class="floating-label">Confirm Password</label>
                    <i class="bi bi-eye-slash" id="toggleConfirmPassword"></i>
                </div>
                <div class="floating-label-container">
                    <select name="department" class="floating-label-input">
                        <option value="">~</option>
                        <option value="COMPUTER ENGINEERING">Department of Computer Engineering</option>
                        <option value="CHEMICAL ENGINEERING">Department of Chemical Engineering</option>
                        <option value="CIVIL ENGINEERING">Department of Civil Engineering</option>
                        <option value="INDUSTRIAL ENGINEERING">Department of Industrial Engineering</option>
                        <option value="ELECTRICAL ENGINEERING">Department of Electrical Engineering</option>
                        <option value="MECHANICAL ENGINEERING">Department of Mechanical Engineering</option>
                        <option value="ELECTRONICS ENGINEERING">Department of Electronics Engineering</option>
                    </select>
                    <label for="department" class="floating-label">Department</label>
                </div>
                <div class="floating-label-container">
                    <select name="user_type" class="floating-label-input">
                        <option value="">~</option>
                        <option value="Faculty">Faculty</option>
                        <option value="Staff">Staff</option>
                        <option value="Student">Student</option>
                    </select>
                    <label for="user_type" class="floating-label">User Type</label>
                </div>
                <div class="line-separator"></div>
                <div class="button-flex">
                    <div class="left-side-button">
                        <a href="index.php" class="cancel-button">Cancel</a>
                    </div>
                    <div class="right-side-button">
                        <input type="submit" name="submit" value="Create Account" class="create-account-button">
                    </div>
                </div>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <p style="text-align: center"><?php echo $successMessage; ?></p>
                        <div class="button-flex" style="margin-top: 15px">
                            <button id="proceedBtn" class="create-account-button">Proceed</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // JavaScript to toggle password visibility and show/hide labels
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("[name='password']");
        const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
        const confirmpassword = document.querySelector("[name='confirmpassword']");
        const floatingInput = document.querySelector(".floating-label-input");

        togglePassword.addEventListener("click", function () {
            // Toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // Toggle the icon
            this.classList.toggle("bi-eye-slash");
            this.classList.toggle("bi-eye");
        });

        toggleConfirmPassword.addEventListener("click", function () {
            // Toggle the type attribute
            const type = confirmpassword.getAttribute("type") === "password" ? "text" : "password";
            confirmpassword.setAttribute("type", type);
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