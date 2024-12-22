<?php include_once("header.php"); ?>

<?php
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php"); // Redirect to the sign-in page if not logged in
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Initialize variables for error/success messages
$error = "";
$success = "";

// Fetch the existing data of the user from the database
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch user data
    $user = mysqli_fetch_assoc($result);
    $profile_image = $user['profile_image'];
} else {
    $error = "Error fetching user data: " . mysqli_error($conn);
    exit();
}

// Handle image upload and update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $target_dir = "admin/uploads/";
    $target_file = $target_dir . uniqid() . "_" . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $valid_extensions)) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            // Update profile image in the database
            $sql = "UPDATE users SET profile_image = '$target_file' WHERE user_id = $user_id";
            if (mysqli_query($conn, $sql)) {
                $success = "Profile image updated successfully!";
                $profile_image = $target_file; // Update the variable for display
            } else {
                $error = "Database update error: " . mysqli_error($conn);
            }
        } else {
            $error = "Error uploading the file.";
        }
    } else {
        $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    }
}

// Handle form submission to update the user's data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['number']));
    $country = mysqli_real_escape_string($conn, trim($_POST['country']));
    $web_url = mysqli_real_escape_string($conn, trim($_POST['url']));
    $birthday = mysqli_real_escape_string($conn, trim($_POST['date']));
    $gender = mysqli_real_escape_string($conn, trim($_POST['gender']));

    $sql = "UPDATE users 
            SET 
                username = '$username', 
                full_name = '$full_name', 
                user_email = '$email', 
                profile_image = '$profile_image', 
                phone = '$phone', 
                country = '$country', 
                web_url = '$web_url', 
                birthday = '$birthday', 
                gender = '$gender'
            WHERE 
                user_id = $user_id";

    if (mysqli_query($conn, $sql)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . mysqli_error($conn);
    }

    // Refresh the user data after update
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    }
}

// Change password logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = trim($_POST['password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New password and confirm password do not match.";
    } else {
        $sql = "SELECT user_pass FROM users WHERE user_id = $user_id";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $userData = mysqli_fetch_assoc($result);
            $hashedPassword = $userData['user_pass'];

            if (password_verify($currentPassword, $hashedPassword)) {
                $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateSql = "UPDATE users SET user_pass = '$newHashedPassword' WHERE user_id = $user_id";
                if (mysqli_query($conn, $updateSql)) {
                    $success = "Password updated successfully.";
                } else {
                    $error = "Error updating the password. Please try again.";
                }
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $error = "User not found.";
        }
    }

    // Ensure user data is refreshed even if there's an error
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    }
}

// Close the database connection
mysqli_close($conn);
?>


    <main class="container blog">

        <div class="grid">

            <div>
                <div style="width: 50%;">
                    <img src="<?php echo $user['profile_image']; ?>" alt="user"> <br><br>
                </div>
                <div>
                    <!-- Visible button/icon -->
                    <a href="#" onclick="document.getElementById('fileUpload').click();" style="float: left; margin-right: 30px;">
                        <i class="bi bi-pencil-square"></i>
                    </a>

                    <a href="#" onclick="document.getElementById('fileUploadbtn').click();">
                        <i class="bi bi-upload"></i>
                    </a>

                    <!-- Hidden file input -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="file" name="profile_image" id="fileUpload" style="display: none;" required>
                        <button type="submit" name="update" id="fileUploadbtn" style="display: none;"></button>
                    </form>    
                </div>
            </div> <!-- grid div  -->

            <div>
                <!-- Display success or error messages -->
                <?php if (!empty($success)): ?>
                    <article style="color: #32CD32;"><?php echo $success; ?></article>
                <?php elseif (!empty($error)): ?>
                    <article style="color: #ff6347;"><?php echo $error; ?></article>
                <?php endif; ?>

                <article>
                    <form action="" method="post">
                        <fieldset>
                            <label>
                                Username
                                <input name="username" placeholder="Username" value="<?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES); ?>" autocomplete="given-name" />
                            </label>
                            <label>
                                Full name
                                <input name="full_name" placeholder="Full name" value="<?php echo htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES); ?>" autocomplete="given-name" />
                            </label>
                            <label>
                                Email
                                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['user_email'] ?? '', ENT_QUOTES); ?>" autocomplete="email" />
                            </label>

                            <label>
                                Phone
                                <input type="number" name="number" placeholder="Number" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES); ?>" aria-label="Number">
                            </label>
                            <label>
                                Country
                                <select name="country" aria-label="Country" value="<?php echo htmlspecialchars($user['country'] ?? '', ENT_QUOTES); ?>" required>
                                    <option selected disabled value="">
                                        Country
                                    </option>

                                    <option value="Afghanistan" <?php echo $user['country'] == 'Afghanistan' ? 'selected' : ''; ?>>Afghanistan</option>
                                    <option value="Åland Islands" <?php echo $user['country'] == 'Åland Islands' ? 'selected' : ''; ?>>Åland Islands</option>
                                    <option value="Albania" <?php echo $user['country'] == 'Albania' ? 'selected' : ''; ?>>Albania</option>
                                    <option value="Algeria" <?php echo $user['country'] == 'Algeria' ? 'selected' : ''; ?>>Algeria</option>
                                    <option value="American Samoa" <?php echo $user['country'] == 'American Samoa' ? 'selected' : ''; ?>>American Samoa</option>
                                    <option value="Andorra" <?php echo $user['country'] == 'Andorra' ? 'selected' : ''; ?>>Andorra</option>
                                    <option value="Angola" <?php echo $user['country'] == 'Angola' ? 'selected' : ''; ?>>Angola</option>
                                    <option value="Anguilla" <?php echo $user['country'] == 'Anguilla' ? 'selected' : ''; ?>>Anguilla</option>
                                    <option value="Antarctica" <?php echo $user['country'] == 'Antarctica' ? 'selected' : ''; ?>>Antarctica</option>
                                    <option value="Antigua and Barbuda" <?php echo $user['country'] == 'Antigua and Barbuda' ? 'selected' : ''; ?>>Antigua and Barbuda</option>
                                    <option value="Argentina" <?php echo $user['country'] == 'Argentina' ? 'selected' : ''; ?>>Argentina</option>
                                    <option value="Armenia" <?php echo $user['country'] == 'Armenia' ? 'selected' : ''; ?>>Armenia</option>
                                    <option value="Aruba" <?php echo $user['country'] == 'Aruba' ? 'selected' : ''; ?>>Aruba</option>
                                    <option value="Australia" <?php echo $user['country'] == 'Australia' ? 'selected' : ''; ?>>Australia</option>
                                    <option value="Austria" <?php echo $user['country'] == 'Austria' ? 'selected' : ''; ?>>Austria</option>
                                    <option value="Azerbaijan" <?php echo $user['country'] == 'Azerbaijan' ? 'selected' : ''; ?>>Azerbaijan</option>
                                    <option value="Bahamas" <?php echo $user['country'] == 'Bahamas' ? 'selected' : ''; ?>>Bahamas</option>
                                    <option value="Bahrain" <?php echo $user['country'] == 'Bahrain' ? 'selected' : ''; ?>>Bahrain</option>
                                    <option value="Bangladesh" <?php echo $user['country'] == 'Bangladesh' ? 'selected' : ''; ?>>Bangladesh</option>
                                    <option value="Barbados" <?php echo $user['country'] == 'Barbados' ? 'selected' : ''; ?>>Barbados</option>
                                    <option value="Belarus" <?php echo $user['country'] == 'Belarus' ? 'selected' : ''; ?>>Belarus</option>
                                    <option value="Belgium" <?php echo $user['country'] == 'Belgium' ? 'selected' : ''; ?>>Belgium</option>
                                    <option value="Belize" <?php echo $user['country'] == 'Belize' ? 'selected' : ''; ?>>Belize</option>
                                    <option value="Benin" <?php echo $user['country'] == 'Benin' ? 'selected' : ''; ?>>Benin</option>
                                    <option value="Bermuda" <?php echo $user['country'] == 'Bermuda' ? 'selected' : ''; ?>>Bermuda</option>
                                    <option value="Bhutan" <?php echo $user['country'] == 'Bhutan' ? 'selected' : ''; ?>>Bhutan</option>
                                    <option value="Bolivia" <?php echo $user['country'] == 'Bolivia' ? 'selected' : ''; ?>>Bolivia</option>
                                    <option value="Bosnia and Herzegovina" <?php echo $user['country'] == 'Bosnia and Herzegovina' ? 'selected' : ''; ?>>Bosnia and Herzegovina</option>
                                    <option value="Botswana" <?php echo $user['country'] == 'Botswana' ? 'selected' : ''; ?>>Botswana</option>
                                    <option value="Bouvet Island" <?php echo $user['country'] == 'Bouvet Island' ? 'selected' : ''; ?>>Bouvet Island</option>
                                    <option value="Brazil" <?php echo $user['country'] == 'Brazil' ? 'selected' : ''; ?>>Brazil</option>
                                    <option value="British Indian Ocean Territory" <?php echo $user['country'] == 'British Indian Ocean Territory' ? 'selected' : ''; ?>>British Indian Ocean Territory</option>
                                    <option value="Brunei Darussalam" <?php echo $user['country'] == 'Brunei Darussalam' ? 'selected' : ''; ?>>Brunei Darussalam</option>
                                    <option value="Bulgaria" <?php echo $user['country'] == 'Bulgaria' ? 'selected' : ''; ?>>Bulgaria</option>
                                    <option value="Burkina Faso" <?php echo $user['country'] == 'Burkina Faso' ? 'selected' : ''; ?>>Burkina Faso</option>
                                    <option value="Burundi" <?php echo $user['country'] == 'Burundi' ? 'selected' : ''; ?>>Burundi</option>
                                    <option value="Cambodia" <?php echo $user['country'] == 'Cambodia' ? 'selected' : ''; ?>>Cambodia</option>
                                    <option value="Cameroon" <?php echo $user['country'] == 'Cameroon' ? 'selected' : ''; ?>>Cameroon</option>
                                    <option value="Canada" <?php echo $user['country'] == 'Canada' ? 'selected' : ''; ?>>Canada</option>
                                    <option value="Cape Verde" <?php echo $user['country'] == 'Cape Verde' ? 'selected' : ''; ?>>Cape Verde</option>
                                    <option value="Cayman Islands" <?php echo $user['country'] == 'Cayman Islands' ? 'selected' : ''; ?>>Cayman Islands</option>
                                    <option value="Central African Republic" <?php echo $user['country'] == 'Central African Republic' ? 'selected' : ''; ?>>Central African Republic</option>
                                    <option value="Chad" <?php echo $user['country'] == 'Chad' ? 'selected' : ''; ?>>Chad</option>
                                    <option value="Chile" <?php echo $user['country'] == 'Chile' ? 'selected' : ''; ?>>Chile</option>
                                    <option value="China" <?php echo $user['country'] == 'China' ? 'selected' : ''; ?>>China</option>
                                    <option value="Christmas Island" <?php echo $user['country'] == 'Christmas Island' ? 'selected' : ''; ?>>Christmas Island</option>
                                    <option value="Cocos (Keeling) Islands" <?php echo $user['country'] == 'Cocos (Keeling) Islands' ? 'selected' : ''; ?>>Cocos (Keeling) Islands</option>
                                    <option value="Colombia" <?php echo $user['country'] == 'Colombia' ? 'selected' : ''; ?>>Colombia</option>
                                    <option value="Comoros" <?php echo $user['country'] == 'Comoros' ? 'selected' : ''; ?>>Comoros</option>
                                    <option value="Congo" <?php echo $user['country'] == 'Congo' ? 'selected' : ''; ?>>Congo</option>
                                    <option value="Congo, The Democratic Republic of The" <?php echo $user['country'] == 'Congo, The Democratic Republic of The' ? 'selected' : ''; ?>>Congo, The Democratic Republic of The</option>
                                    <option value="Cook Islands" <?php echo $user['country'] == 'Cook Islands' ? 'selected' : ''; ?>>Cook Islands</option>
                                    <option value="Costa Rica" <?php echo $user['country'] == 'Costa Rica' ? 'selected' : ''; ?>>Costa Rica</option>
                                    <option value="Cote D'ivoire" <?php echo $user['country'] == "Cote D'ivoire" ? 'selected' : ''; ?>>Cote D'ivoire</option>
                                    <option value="Croatia" <?php echo $user['country'] == 'Croatia' ? 'selected' : ''; ?>>Croatia</option>
                                    <option value="Cuba" <?php echo $user['country'] == 'Cuba' ? 'selected' : ''; ?>>Cuba</option>
                                    <option value="Cyprus" <?php echo $user['country'] == 'Cyprus' ? 'selected' : ''; ?>>Cyprus</option>
                                    <option value="Czech Republic" <?php echo $user['country'] == 'Czech Republic' ? 'selected' : ''; ?>>Czech Republic</option>
                                    <option value="Denmark" <?php echo $user['country'] == 'Denmark' ? 'selected' : ''; ?>>Denmark</option>
                                    <option value="Djibouti" <?php echo $user['country'] == 'Djibouti' ? 'selected' : ''; ?>>Djibouti</option>
                                    <option value="Dominica" <?php echo $user['country'] == 'Dominica' ? 'selected' : ''; ?>>Dominica</option>
                                    <option value="Dominican Republic" <?php echo $user['country'] == 'Dominican Republic' ? 'selected' : ''; ?>>Dominican Republic</option>
                                    <option value="Ecuador" <?php echo $user['country'] == 'Ecuador' ? 'selected' : ''; ?>>Ecuador</option>
                                    <option value="Egypt" <?php echo $user['country'] == 'Egypt' ? 'selected' : ''; ?>>Egypt</option>
                                    <option value="El Salvador" <?php echo $user['country'] == 'El Salvador' ? 'selected' : ''; ?>>El Salvador</option>
                                    <option value="Equatorial Guinea" <?php echo $user['country'] == 'Equatorial Guinea' ? 'selected' : ''; ?>>Equatorial Guinea</option>
                                    <option value="Eritrea" <?php echo $user['country'] == 'Eritrea' ? 'selected' : ''; ?>>Eritrea</option>
                                    <option value="Estonia" <?php echo $user['country'] == 'Estonia' ? 'selected' : ''; ?>>Estonia</option>
                                    <option value="Ethiopia" <?php echo $user['country'] == 'Ethiopia' ? 'selected' : ''; ?>>Ethiopia</option>
                                    <option value="Falkland Islands (Malvinas)" <?php echo $user['country'] == 'Falkland Islands (Malvinas)' ? 'selected' : ''; ?>>Falkland Islands (Malvinas)</option>
                                    <option value="Faroe Islands" <?php echo $user['country'] == 'Faroe Islands' ? 'selected' : ''; ?>>Faroe Islands</option>
                                    <option value="Fiji" <?php echo $user['country'] == 'Fiji' ? 'selected' : ''; ?>>Fiji</option>
                                    <option value="Finland" <?php echo $user['country'] == 'Finland' ? 'selected' : ''; ?>>Finland</option>
                                    <option value="France" <?php echo $user['country'] == 'France' ? 'selected' : ''; ?>>France</option>
                                    <option value="French Guiana" <?php echo $user['country'] == 'French Guiana' ? 'selected' : ''; ?>>French Guiana</option>
                                    <option value="French Polynesia" <?php echo $user['country'] == 'French Polynesia' ? 'selected' : ''; ?>>French Polynesia</option>
                                    <option value="French Southern Territories" <?php echo $user['country'] == 'French Southern Territories' ? 'selected' : ''; ?>>French Southern Territories</option>
                                    <option value="Gabon" <?php echo $user['country'] == 'Gabon' ? 'selected' : ''; ?>>Gabon</option>
                                    <option value="Gambia" <?php echo $user['country'] == 'Gambia' ? 'selected' : ''; ?>>Gambia</option>
                                    <option value="Georgia" <?php echo $user['country'] == 'Georgia' ? 'selected' : ''; ?>>Georgia</option>
                                    <option value="Germany" <?php echo $user['country'] == 'Germany' ? 'selected' : ''; ?>>Germany</option>
                                    <option value="Ghana" <?php echo $user['country'] == 'Ghana' ? 'selected' : ''; ?>>Ghana</option>
                                    <option value="Gibraltar" <?php echo $user['country'] == 'Gibraltar' ? 'selected' : ''; ?>>Gibraltar</option>
                                    <option value="Greece" <?php echo $user['country'] == 'Greece' ? 'selected' : ''; ?>>Greece</option>
                                    <option value="Greenland" <?php echo $user['country'] == 'Greenland' ? 'selected' : ''; ?>>Greenland</option>
                                    <option value="Grenada" <?php echo $user['country'] == 'Grenada' ? 'selected' : ''; ?>>Grenada</option>
                                    <option value="Guadeloupe" <?php echo $user['country'] == 'Guadeloupe' ? 'selected' : ''; ?>>Guadeloupe</option>
                                    <option value="Guam" <?php echo $user['country'] == 'Guam' ? 'selected' : ''; ?>>Guam</option>
                                    <option value="Guatemala" <?php echo $user['country'] == 'Guatemala' ? 'selected' : ''; ?>>Guatemala</option>
                                    <option value="Guernsey" <?php echo $user['country'] == 'Guernsey' ? 'selected' : ''; ?>>Guernsey</option>
                                    <option value="Guinea" <?php echo $user['country'] == 'Guinea' ? 'selected' : ''; ?>>Guinea</option>
                                    <option value="Guinea-bissau" <?php echo $user['country'] == 'Guinea-bissau' ? 'selected' : ''; ?>>Guinea-bissau</option>
                                    <option value="Guyana" <?php echo $user['country'] == 'Guyana' ? 'selected' : ''; ?>>Guyana</option>
                                    <option value="Haiti" <?php echo $user['country'] == 'Haiti' ? 'selected' : ''; ?>>Haiti</option>
                                    <option value="Heard Island and Mcdonald Islands" <?php echo $user['country'] == 'Heard Island and Mcdonald Islands' ? 'selected' : ''; ?>>Heard Island and Mcdonald Islands</option>
                                    <option value="Holy See (Vatican City State)" <?php echo $user['country'] == 'Holy See (Vatican City State)' ? 'selected' : ''; ?>>Holy See (Vatican City State)</option>
                                    <option value="Honduras" <?php echo $user['country'] == 'Honduras' ? 'selected' : ''; ?>>Honduras</option>
                                    <option value="Hong Kong" <?php echo $user['country'] == 'Hong Kong' ? 'selected' : ''; ?>>Hong Kong</option>
                                    <option value="Hungary" <?php echo $user['country'] == 'Hungary' ? 'selected' : ''; ?>>Hungary</option>
                                    <option value="Iceland" <?php echo $user['country'] == 'Iceland' ? 'selected' : ''; ?>>Iceland</option>
                                    <option value="India" <?php echo $user['country'] == 'India' ? 'selected' : ''; ?>>India</option>
                                    <option value="Indonesia" <?php echo $user['country'] == 'Indonesia' ? 'selected' : ''; ?>>Indonesia</option>
                                    <option value="Iran, Islamic Republic of" <?php echo $user['country'] == 'Iran, Islamic Republic of' ? 'selected' : ''; ?>>Iran, Islamic Republic of</option>
                                    <option value="Iraq" <?php echo $user['country'] == 'Iraq' ? 'selected' : ''; ?>>Iraq</option>
                                    <option value="Ireland" <?php echo $user['country'] == 'Ireland' ? 'selected' : ''; ?>>Ireland</option>
                                    <option value="Isle of Man" <?php echo $user['country'] == 'Isle of Man' ? 'selected' : ''; ?>>Isle of Man</option>
                                    <option value="Israel" <?php echo $user['country'] == 'Israel' ? 'selected' : ''; ?>>Israel</option>
                                    <option value="Italy" <?php echo $user['country'] == 'Italy' ? 'selected' : ''; ?>>Italy</option>
                                    <option value="Jamaica" <?php echo $user['country'] == 'Jamaica' ? 'selected' : ''; ?>>Jamaica</option>
                                    <option value="Japan" <?php echo $user['country'] == 'Japan' ? 'selected' : ''; ?>>Japan</option>
                                    <option value="Jersey" <?php echo $user['country'] == 'Jersey' ? 'selected' : ''; ?>>Jersey</option>
                                    <option value="Jordan" <?php echo $user['country'] == 'Jordan' ? 'selected' : ''; ?>>Jordan</option>
                                    <option value="Kazakhstan" <?php echo $user['country'] == 'Kazakhstan' ? 'selected' : ''; ?>>Kazakhstan</option>
                                    <option value="Kenya" <?php echo $user['country'] == 'Kenya' ? 'selected' : ''; ?>>Kenya</option>
                                    <option value="Kiribati" <?php echo $user['country'] == 'Kiribati' ? 'selected' : ''; ?>>Kiribati</option>
                                    <option value="Korea, Democratic People's Republic of" <?php echo $user['country'] == "Korea, Democratic People's Republic of" ? 'selected' : ''; ?>>Korea, Democratic People's Republic of</option>
                                    <option value="Korea, Republic of" <?php echo $user['country'] == 'Korea, Republic of' ? 'selected' : ''; ?>>Korea, Republic of</option>
                                    <option value="Kuwait" <?php echo $user['country'] == 'Kuwait' ? 'selected' : ''; ?>>Kuwait</option>
                                    <option value="Kyrgyzstan" <?php echo $user['country'] == 'Kyrgyzstan' ? 'selected' : ''; ?>>Kyrgyzstan</option>
                                    <option value="Lao People's Democratic Republic" <?php echo $user['country'] == "Lao People's Democratic Republic" ? 'selected' : ''; ?>>Lao People's Democratic Republic</option>
                                    <option value="Latvia" <?php echo $user['country'] == "Latvia" ? 'selected' : ''; ?>>Latvia</option>
                                    <option value="Lebanon" <?php echo $user['country'] == "Lebanon" ? 'selected' : ''; ?>>Lebanon</option>
                                    <option value="Lesotho" <?php echo $user['country'] == "Lesotho" ? 'selected' : ''; ?>>Lesotho</option>
                                    <option value="Liberia" <?php echo $user['country'] == "Liberia" ? 'selected' : ''; ?>>Liberia</option>
                                    <option value="Libyan Arab Jamahiriya" <?php echo $user['country'] == "Libyan Arab Jamahiriya" ? 'selected' : ''; ?>>Libyan Arab Jamahiriya</option>
                                    <option value="Liechtenstein" <?php echo $user['country'] == "Liechtenstein" ? 'selected' : ''; ?>>Liechtenstein</option>
                                    <option value="Lithuania" <?php echo $user['country'] == "Lithuania" ? 'selected' : ''; ?>>Lithuania</option>
                                    <option value="Luxembourg" <?php echo $user['country'] == "Luxembourg" ? 'selected' : ''; ?>>Luxembourg</option>
                                    <option value="Macao" <?php echo $user['country'] == "Macao" ? 'selected' : ''; ?>>Macao</option>
                                    <option value="Macedonia, The Former Yugoslav Republic of" <?php echo $user['country'] == "Macedonia, The Former Yugoslav Republic of" ? 'selected' : ''; ?>>Macedonia, The Former Yugoslav Republic of</option>
                                    <option value="Madagascar" <?php echo $user['country'] == "Madagascar" ? 'selected' : ''; ?>>Madagascar</option>
                                    <option value="Malawi" <?php echo $user['country'] == "Malawi" ? 'selected' : ''; ?>>Malawi</option>
                                    <option value="Malaysia" <?php echo $user['country'] == "Malaysia" ? 'selected' : ''; ?>>Malaysia</option>
                                    <option value="Maldives" <?php echo $user['country'] == "Maldives" ? 'selected' : ''; ?>>Maldives</option>
                                    <option value="Mali" <?php echo $user['country'] == "Mali" ? 'selected' : ''; ?>>Mali</option>
                                    <option value="Malta" <?php echo $user['country'] == "Malta" ? 'selected' : ''; ?>>Malta</option>
                                    <option value="Marshall Islands" <?php echo $user['country'] == "Marshall Islands" ? 'selected' : ''; ?>>Marshall Islands</option>
                                    <option value="Martinique" <?php echo $user['country'] == "Martinique" ? 'selected' : ''; ?>>Martinique</option>
                                    <option value="Mauritania" <?php echo $user['country'] == "Mauritania" ? 'selected' : ''; ?>>Mauritania</option>
                                    <option value="Mauritius" <?php echo $user['country'] == "Mauritius" ? 'selected' : ''; ?>>Mauritius</option>
                                    <option value="Mayotte" <?php echo $user['country'] == "Mayotte" ? 'selected' : ''; ?>>Mayotte</option>
                                    <option value="Mexico" <?php echo $user['country'] == "Mexico" ? 'selected' : ''; ?>>Mexico</option>
                                    <option value="Micronesia, Federated States of" <?php echo $user['country'] == "Micronesia, Federated States of" ? 'selected' : ''; ?>>Micronesia, Federated States of</option>
                                    <option value="Moldova, Republic of" <?php echo $user['country'] == "Moldova, Republic of" ? 'selected' : ''; ?>>Moldova, Republic of</option>
                                    <option value="Monaco" <?php echo $user['country'] == "Monaco" ? 'selected' : ''; ?>>Monaco</option>
                                    <option value="Mongolia" <?php echo $user['country'] == "Mongolia" ? 'selected' : ''; ?>>Mongolia</option>
                                    <option value="Montenegro" <?php echo $user['country'] == "Montenegro" ? 'selected' : ''; ?>>Montenegro</option>
                                    <option value="Montserrat" <?php echo $user['country'] == "Montserrat" ? 'selected' : ''; ?>>Montserrat</option>
                                    <option value="Morocco" <?php echo $user['country'] == "Morocco" ? 'selected' : ''; ?>>Morocco</option>
                                    <option value="Mozambique" <?php echo $user['country'] == "Mozambique" ? 'selected' : ''; ?>>Mozambique</option>
                                    <option value="Myanmar" <?php echo $user['country'] == "Myanmar" ? 'selected' : ''; ?>>Myanmar</option>
                                    <option value="Namibia" <?php echo $user['country'] == "Namibia" ? 'selected' : ''; ?>>Namibia</option>
                                    <option value="Nauru" <?php echo $user['country'] == "Nauru" ? 'selected' : ''; ?>>Nauru</option>
                                    <option value="Nepal" <?php echo $user['country'] == "Nepal" ? 'selected' : ''; ?>>Nepal</option>
                                    <option value="Netherlands" <?php echo $user['country'] == "Netherlands" ? 'selected' : ''; ?>>Netherlands</option>
                                    <option value="Netherlands Antilles" <?php echo $user['country'] == "Netherlands Antilles" ? 'selected' : ''; ?>>Netherlands Antilles</option>
                                    <option value="New Caledonia" <?php echo $user['country'] == "New Caledonia" ? 'selected' : ''; ?>>New Caledonia</option>
                                    <option value="New Zealand" <?php echo $user['country'] == "New Zealand" ? 'selected' : ''; ?>>New Zealand</option>
                                    <option value="Nicaragua" <?php echo $user['country'] == "Nicaragua" ? 'selected' : ''; ?>>Nicaragua</option>
                                    <option value="Niger" <?php echo $user['country'] == "Niger" ? 'selected' : ''; ?>>Niger</option>
                                    <option value="Nigeria" <?php echo $user['country'] == "Nigeria" ? 'selected' : ''; ?>>Nigeria</option>
                                    <option value="Niue" <?php echo $user['country'] == "Niue" ? 'selected' : ''; ?>>Niue</option>
                                    <option value="Norfolk Island" <?php echo $user['country'] == "Norfolk Island" ? 'selected' : ''; ?>>Norfolk Island</option>
                                    <option value="Northern Mariana Islands" <?php echo $user['country'] == "Northern Mariana Islands" ? 'selected' : ''; ?>>Northern Mariana Islands</option>
                                    <option value="Norway" <?php echo $user['country'] == "Norway" ? 'selected' : ''; ?>>Norway</option>
                                    <option value="Oman" <?php echo $user['country'] == "Oman" ? 'selected' : ''; ?>>Oman</option>
                                    <option value="Pakistan" <?php echo $user['country'] == "Pakistan" ? 'selected' : ''; ?>>Pakistan</option>
                                    <option value="Palau" <?php echo $user['country'] == "Palau" ? 'selected' : ''; ?>>Palau</option>
                                    <option value="Palestinian Territory, Occupied" <?php echo $user['country'] == "Palestinian Territory, Occupied" ? 'selected' : ''; ?>>Palestinian Territory, Occupied</option>
                                    <option value="Panama" <?php echo $user['country'] == "Panama" ? 'selected' : ''; ?>>Panama</option>
                                    <option value="Papua New Guinea" <?php echo $user['country'] == "Papua New Guinea" ? 'selected' : ''; ?>>Papua New Guinea</option>
                                    <option value="Paraguay" <?php echo $user['country'] == "Paraguay" ? 'selected' : ''; ?>>Paraguay</option>
                                    <option value="Peru" <?php echo $user['country'] == "Peru" ? 'selected' : ''; ?>>Peru</option>
                                    <option value="Philippines" <?php echo $user['country'] == "Philippines" ? 'selected' : ''; ?>>Philippines</option>
                                    <option value="Pitcairn" <?php echo $user['country'] == "Pitcairn" ? 'selected' : ''; ?>>Pitcairn</option>
                                    <option value="Poland" <?php echo $user['country'] == "Poland" ? 'selected' : ''; ?>>Poland</option>
                                    <option value="Portugal" <?php echo $user['country'] == "Portugal" ? 'selected' : ''; ?>>Portugal</option>
                                    <option value="Puerto Rico" <?php echo $user['country'] == "Puerto Rico" ? 'selected' : ''; ?>>Puerto Rico</option>
                                    <option value="Qatar" <?php echo $user['country'] == "Qatar" ? 'selected' : ''; ?>>Qatar</option>
                                    <option value="Reunion" <?php echo $user['country'] == "Reunion" ? 'selected' : ''; ?>>Reunion</option>
                                    <option value="Romania" <?php echo $user['country'] == "Romania" ? 'selected' : ''; ?>>Romania</option>
                                    <option value="Russian Federation" <?php echo $user['country'] == "Russian Federation" ? 'selected' : ''; ?>>Russian Federation</option>
                                    <option value="Rwanda" <?php echo $user['country'] == "Rwanda" ? 'selected' : ''; ?>>Rwanda</option>
                                    <option value="Saint Helena" <?php echo $user['country'] == "Saint Helena" ? 'selected' : ''; ?>>Saint Helena</option>
                                    <option value="Saint Kitts and Nevis" <?php echo $user['country'] == "Saint Kitts and Nevis" ? 'selected' : ''; ?>>Saint Kitts and Nevis</option>
                                    <option value="Saint Lucia" <?php echo $user['country'] == "Saint Lucia" ? 'selected' : ''; ?>>Saint Lucia</option>
                                    <option value="Saint Pierre and Miquelon" <?php echo $user['country'] == "Saint Pierre and Miquelon" ? 'selected' : ''; ?>>Saint Pierre and Miquelon</option>
                                    <option value="Saint Vincent and The Grenadines" <?php echo $user['country'] == "Saint Vincent and The Grenadines" ? 'selected' : ''; ?>>Saint Vincent and The Grenadines</option>
                                    <option value="Samoa" <?php echo $user['country'] == 'Samoa' ? 'selected' : ''; ?>>Samoa</option>
                                    <option value="San Marino" <?php echo $user['country'] == 'San Marino' ? 'selected' : ''; ?>>San Marino</option>
                                    <option value="Sao Tome and Principe" <?php echo $user['country'] == 'Sao Tome and Principe' ? 'selected' : ''; ?>>Sao Tome and Principe</option>
                                    <option value="Saudi Arabia" <?php echo $user['country'] == 'Saudi Arabia' ? 'selected' : ''; ?>>Saudi Arabia</option>
                                    <option value="Senegal" <?php echo $user['country'] == 'Senegal' ? 'selected' : ''; ?>>Senegal</option>
                                    <option value="Serbia" <?php echo $user['country'] == 'Serbia' ? 'selected' : ''; ?>>Serbia</option>
                                    <option value="Seychelles" <?php echo $user['country'] == 'Seychelles' ? 'selected' : ''; ?>>Seychelles</option>
                                    <option value="Sierra Leone" <?php echo $user['country'] == 'Sierra Leone' ? 'selected' : ''; ?>>Sierra Leone</option>
                                    <option value="Singapore" <?php echo $user['country'] == 'Singapore' ? 'selected' : ''; ?>>Singapore</option>
                                    <option value="Slovakia" <?php echo $user['country'] == 'Slovakia' ? 'selected' : ''; ?>>Slovakia</option>
                                    <option value="Slovenia" <?php echo $user['country'] == 'Slovenia' ? 'selected' : ''; ?>>Slovenia</option>
                                    <option value="Solomon Islands" <?php echo $user['country'] == 'Solomon Islands' ? 'selected' : ''; ?>>Solomon Islands</option>
                                    <option value="Somalia" <?php echo $user['country'] == 'Somalia' ? 'selected' : ''; ?>>Somalia</option>
                                    <option value="South Africa" <?php echo $user['country'] == 'South Africa' ? 'selected' : ''; ?>>South Africa</option>
                                    <option value="South Georgia and The South Sandwich Islands" <?php echo $user['country'] == 'South Georgia and The South Sandwich Islands' ? 'selected' : ''; ?>>South Georgia and The South Sandwich Islands</option>
                                    <option value="Spain" <?php echo $user['country'] == 'Spain' ? 'selected' : ''; ?>>Spain</option>
                                    <option value="Sri Lanka" <?php echo $user['country'] == 'Sri Lanka' ? 'selected' : ''; ?>>Sri Lanka</option>
                                    <option value="Sudan" <?php echo $user['country'] == 'Sudan' ? 'selected' : ''; ?>>Sudan</option>
                                    <option value="Suriname" <?php echo $user['country'] == 'Suriname' ? 'selected' : ''; ?>>Suriname</option>
                                    <option value="Svalbard and Jan Mayen" <?php echo $user['country'] == 'Svalbard and Jan Mayen' ? 'selected' : ''; ?>>Svalbard and Jan Mayen</option>
                                    <option value="Swaziland" <?php echo $user['country'] == 'Swaziland' ? 'selected' : ''; ?>>Swaziland</option>
                                    <option value="Sweden" <?php echo $user['country'] == 'Sweden' ? 'selected' : ''; ?>>Sweden</option>
                                    <option value="Switzerland" <?php echo $user['country'] == 'Switzerland' ? 'selected' : ''; ?>>Switzerland</option>
                                    <option value="Syrian Arab Republic" <?php echo $user['country'] == 'Syrian Arab Republic' ? 'selected' : ''; ?>>Syrian Arab Republic</option>
                                    <option value="Taiwan" <?php echo $user['country'] == 'Taiwan' ? 'selected' : ''; ?>>Taiwan</option>
                                    <option value="Tajikistan" <?php echo $user['country'] == 'Tajikistan' ? 'selected' : ''; ?>>Tajikistan</option>
                                    <option value="Tanzania, United Republic of" <?php echo $user['country'] == 'Tanzania, United Republic of' ? 'selected' : ''; ?>>Tanzania, United Republic of</option>
                                    <option value="Thailand" <?php echo $user['country'] == 'Thailand' ? 'selected' : ''; ?>>Thailand</option>
                                    <option value="Timor-leste" <?php echo $user['country'] == 'Timor-leste' ? 'selected' : ''; ?>>Timor-leste</option>
                                    <option value="Togo" <?php echo $user['country'] == 'Togo' ? 'selected' : ''; ?>>Togo</option>
                                    <option value="Tokelau" <?php echo $user['country'] == 'Tokelau' ? 'selected' : ''; ?>>Tokelau</option>
                                    <option value="Tonga" <?php echo $user['country'] == 'Tonga' ? 'selected' : ''; ?>>Tonga</option>
                                    <option value="Trinidad and Tobago" <?php echo $user['country'] == 'Trinidad and Tobago' ? 'selected' : ''; ?>>Trinidad and Tobago</option>
                                    <option value="Tunisia" <?php echo $user['country'] == 'Tunisia' ? 'selected' : ''; ?>>Tunisia</option>
                                    <option value="Turkey" <?php echo $user['country'] == 'Turkey' ? 'selected' : ''; ?>>Turkey</option>
                                    <option value="Turkmenistan" <?php echo $user['country'] == 'Turkmenistan' ? 'selected' : ''; ?>>Turkmenistan</option>
                                    <option value="Turks and Caicos Islands" <?php echo $user['country'] == 'Turks and Caicos Islands' ? 'selected' : ''; ?>>Turks and Caicos Islands</option>
                                    <option value="Tuvalu" <?php echo $user['country'] == 'Tuvalu' ? 'selected' : ''; ?>>Tuvalu</option>
                                    <option value="Uganda" <?php echo $user['country'] == 'Uganda' ? 'selected' : ''; ?>>Uganda</option>
                                    <option value="Ukraine" <?php echo $user['country'] == 'Ukraine' ? 'selected' : ''; ?>>Ukraine</option>
                                    <option value="United Arab Emirates" <?php echo $user['country'] == 'United Arab Emirates' ? 'selected' : ''; ?>>United Arab Emirates</option>
                                    <option value="United Kingdom" <?php echo $user['country'] == 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                                    <option value="United States" <?php echo $user['country'] == 'United States' ? 'selected' : ''; ?>>United States</option>
                                    <option value="United States Minor Outlying Islands" <?php echo $user['country'] == 'United States Minor Outlying Islands' ? 'selected' : ''; ?>>United States Minor Outlying Islands</option>
                                    <option value="Uruguay" <?php echo $user['country'] == 'Uruguay' ? 'selected' : ''; ?>>Uruguay</option>
                                    <option value="Uzbekistan" <?php echo $user['country'] == 'Uzbekistan' ? 'selected' : ''; ?>>Uzbekistan</option>
                                    <option value="Vanuatu" <?php echo $user['country'] == 'Vanuatu' ? 'selected' : ''; ?>>Vanuatu</option>
                                    <option value="Venezuela" <?php echo $user['country'] == 'Venezuela' ? 'selected' : ''; ?>>Venezuela</option>
                                    <option value="Viet Nam" <?php echo $user['country'] == 'Viet Nam' ? 'selected' : ''; ?>>Viet Nam</option>
                                    <option value="Virgin Islands, British" <?php echo $user['country'] == 'Virgin Islands, British' ? 'selected' : ''; ?>>Virgin Islands, British</option>
                                    <option value="Virgin Islands, U.S." <?php echo $user['country'] == 'Virgin Islands, U.S.' ? 'selected' : ''; ?>>Virgin Islands, U.S.</option>
                                    <option value="Wallis and Futuna" <?php echo $user['country'] == 'Wallis and Futuna' ? 'selected' : ''; ?>>Wallis and Futuna</option>
                                    <option value="Western Sahara" <?php echo $user['country'] == 'Western Sahara' ? 'selected' : ''; ?>>Western Sahara</option>
                                    <option value="Yemen" <?php echo $user['country'] == 'Yemen' ? 'selected' : ''; ?>>Yemen</option>
                                    <option value="Zambia" <?php echo $user['country'] == 'Zambia' ? 'selected' : ''; ?>>Zambia</option>
                                    <option value="Zimbabwe" <?php echo $user['country'] == 'Zimbabwe' ? 'selected' : ''; ?>>Zimbabwe</option>
                                </select>
                            </label>

                            <label>
                                Website URL
                                <input type="url" name="url" placeholder="Url" value="<?php echo htmlspecialchars($user['web_url'] ?? '', ENT_QUOTES); ?>" aria-label="Url">
                            </label>

                            <label>
                                Bithday
                                <input type="date" name="date" value="<?php echo htmlspecialchars($user['birthday'] ?? '', ENT_QUOTES); ?>" aria-label="Date">
                            </label>
                        </fieldset>

                        <fieldset>
                            <legend>Gender: </legend>
                            <div class="grid">
                                <label>
                                    <input type="radio" name="gender" value="Male" <?php echo $user['gender'] === 'Female' ? 'checked' : ''; ?> checked />
                                    Male
                                </label>
                                <label>
                                    <input type="radio" name="gender" value="Female" <?php echo $user['gender'] === 'Female' ? 'checked' : ''; ?> />
                                    Female
                                </label>

                                <label>
                                    <input type="radio" name="gender" value="Other" <?php echo $user['gender'] === 'Other' ? 'checked' : ''; ?> />
                                    Other
                                </label>
                            </div>
                        </fieldset>

                        <input type="submit" name="update_profile" value="Update" />
                    </form>
                </article>

                <article>

                    <form action="" method="post">
                        <label>
                            Current Password
                            <input type="password" name="password" placeholder="Password" aria-label="Password">
                        </label>

                        <label>
                            New Password
                            <input type="password" name="new_password" placeholder="New Password"
                                aria-label="New Password">
                        </label>

                        <label>
                            Confirm Password
                            <input type="password" name="confirm_password" placeholder="Confirm Password"
                                aria-label="Confirm Password">
                        </label>

                        <input type="submit" name="update_password" value="Update Password" />
                    </form>
                    
                </article>

            </div> <!-- grid div -->

        </div>




    </main>

<?php include_once("footer.php") ?>    
