<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stage = $_POST['stage'];
    $email = $_POST['email'] ?? null;
    $reset_code = $_POST['reset_code'] ?? null;
    $new_password = $_POST['new_password'] ?? null;

    if ($stage === 'request_code') {
        // Generate reset code and store it
        $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $reset_code = rand(100000, 999999);
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $insert = $conn->prepare("INSERT INTO password_reset (email, reset_code, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE reset_code = VALUES(reset_code), expires_at = VALUES(expires_at)");
            $insert->bind_param("sss", $email, $reset_code, $expires_at);
            $insert->execute();

            echo "Your reset code is: " . $reset_code; // Debugging purpose only
        } else {
            echo "No account found with that email.";
        }
    } elseif ($stage === 'verify_code') {
        // Verify the code
        $query = $conn->prepare("SELECT * FROM password_reset WHERE email = ? AND reset_code = ? AND expires_at > NOW()");
        $query->bind_param("ss", $email, $reset_code);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            echo "verified";
        } else {
            echo "Invalid or expired reset code.";
        }
    } elseif ($stage === 'reset_password') {
        // Reset the password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed_password, $email);
        $update->execute();

        $delete = $conn->prepare("DELETE FROM password_reset WHERE email = ?");
        $delete->bind_param("s", $email);
        $delete->execute();

        echo "Password reset successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showVerificationField() {
            document.getElementById('verification-field').style.display = 'block';
        }

        function showResetPasswordField() {
            document.getElementById('reset-password-field').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <!-- Request Reset Code -->
        <form action="reset_password_request.php" method="POST" id="request-code-form">
            <input type="hidden" name="stage" value="request_code">
            <label for="email">Enter your email:</label>
            <input type="email" name="email" required>
            <button type="submit" onclick="showVerificationField()">Request Reset Code</button>
        </form>

        <!-- Verify Reset Code -->
        <form action="reset_password_request.php" method="POST" id="verification-field" style="display: none;">
            <input type="hidden" name="stage" value="verify_code">
            <input type="hidden" name="email" id="email-verification">
            <label for="reset_code">Enter Reset Code:</label>
            <input type="text" name="reset_code" required>
            <button type="submit" onclick="showResetPasswordField()">Verify Code</button>
        </form>

        <!-- Reset Password -->
        <form action="reset_password_request.php" method="POST" id="reset-password-field" style="display: none;">
            <input type="hidden" name="stage" value="reset_password">
            <input type="hidden" name="email" id="email-reset">
            <label for="new_password">Enter New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
