<?php
include_once '../session.php';
Session::init();
include '../function.php';
$function = new Functions();

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $email = $function->validateToken($token);

    if (!$email) {
        echo "Invalid or expired token.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newPassword = $_POST['password'];
        $result = $function->updatePassword($email, $newPassword); // Capture the result

        if ($result === 0) {
            // Password does not meet complexity requirements
            Session::set("msg", "<div id='error-msg' style='background-color: #ff9999; color:black; border: solid #ff9999 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-exclamation'></i> Password does not meet complexity requirements. Please try again.</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            // Redirect back to the same page with the token
            header("Location: reset_password.php?token=" . urlencode($token));
            exit();
        } else {
            // Password updated successfully
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Your password has been reset successfully! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            header("Location: applicant_login.php");
            exit();
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPTMTCS</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
         data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="../assets/images/logos/logo.png" width="200" alt="">
                                </a>
                                <h5 class="text-center mb-4"><strong>RPTMTCS</strong></h5>

                                <?php
                                    // Display session message if set
                                    $msg = Session::get("msg");
                                    if (isset($msg)) {
                                        echo $msg;
                                        Session::set("msg", NULL); // Clear the message after displaying it
                                    }
                                ?>

                                <?php if (isset($_GET['token'])): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                        <div class="mb-4">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="password" placeholder="Minimum of 8 or more characters" required>
                                        </div>
                                        <button type="submit" class="btn btn-dark w-100 py-2">Reset Password</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <a class="text-primary fw-bold ms-2 d-flex align-items-center justify-content-center" href="applicant_login.php" style="margin-bottom: 20px">RETURN</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
