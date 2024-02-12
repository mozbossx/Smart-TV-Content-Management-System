<?php
$user_type = $_SESSION['user_type'];

@include 'config_connection.php';

// get the user's username from the session
$full_name = $_SESSION['full_name'];

// fetch user data from the database
$sql = "SELECT full_name AS full_name, password, department, email, user_type FROM users_tb WHERE full_name = '$full_name'";
$result = mysqli_query($conn, $sql);

// Check if user data is found
if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
} else {
    $error[] = "Error: No user data found";
}
?>

<div class="sidebar">
    <nav class="navbar">
        <div class="line-separator"></div>
        <a href="user_home.php" <?php echo $current_page === 'user_home.php' ? 'class="active-sidebar-content"' : ''; ?>><i class="fa fa-home" style="padding-right: 8px"></i>Home</a>
        <a href="create_post.php" <?php echo ($current_page === 'create_post.php' || $current_page === 'create_an_announcement.php' || $current_page === 'create_an_event.php' || $current_page === 'create_a_news.php' || $current_page === 'create_a_promotional_material.php' || $current_page === 'create_a_general_info.php' || $current_page === 'add_new_feature.php') ? 'class="active-sidebar-content"' : ''; ?>><i class="fa fa-pencil-square" style="padding-right: 8px"></i>Create Post</a>
        <a href="notifications.php" <?php echo $current_page === 'notifications.php' ? 'class="active-sidebar-content"' : ''; ?>><i class="fa fa-bell" style="padding-right: 8px"></i>Notifications</a>
        <?php if ($user_type === 'Admin') { ?>
            <a href="admin_options.php" <?php echo $current_page === 'admin_options.php' || $current_page === 'manage_users.php' || $current_page === 'manage_smart_tvs.php' || $current_page === 'manage_posts.php'? 'class="active-sidebar-content"' : ''; ?>><i class="fa fa-user-secret" style="padding-right: 8px"></i>Admin Options</a>
        <?php } ?>
        <a href="profile.php" <?php echo $current_page === 'profile.php' ? 'class="active-sidebar-content"' : ''; ?>><i class="fa fa-user" style="padding-right: 8px"></i>My Profile</a>
        <a href="settings.php" <?php echo $current_page === 'settings.php' ? 'class="active-sidebar-content"' : ''; ?>><i class="fa fa-gear" style="padding-right: 8px"></i>Settings</a>
        <a href="logout.php" onclick="return confirmLogout();" style="margin-bottom: 80px"><i class="fa fa-sign-out" style="padding-right: 8px"></i>Logout</a>
    </nav>
</div>

<script type="text/javascript">
    const toggle = document.querySelector('.toggle');
    const sidebar = document.querySelector('.sidebar');
    const logo = document.getElementById('logo'); // Get the logo element

    toggle.addEventListener('click', () => {
        toggle.classList.toggle('active');
        sidebar.classList.toggle('active');
            
        // Add or remove the hide-logo class to fade out or display the logo
        if (logo.classList.contains('hide-logo')) {
            logo.classList.remove('hide-logo');
        } else {
            logo.classList.add('hide-logo');
        }
    });

    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }
</script>
