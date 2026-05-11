<?php
  include_once '../session.php';
  Session::init();
  include '../function.php';
  $function = new Functions();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Heat Mapping of Various Health Cases</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
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
                <h4 class="text-center mb-4 mt-2"><strong>Heat Mapping of Various Health Cases<br>(HMVHC)</strong></h4>

                <?php
                      // Display session message if set
                      $msg = Session::get("msg");
                      if (isset($msg)) {
                        echo $msg;
                        Session::set("msg", NULL); // Clear the message after displaying it
                      }
                ?>

                <form method="post" action="../navigate.php">
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="e.g. example@gmail.com" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                </div>
                <div class="mb-4">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>" required>
                    <span class="input-group-text" onclick="togglePasswordVisibility('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                    <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                    <label class="form-check-label text-dark" for="flexCheckChecked" style="font-size: 13px;">
                        Remember this Device
                    </label>
                    </div>
                    <a class="fw-bold" href="forgot_password.php" style="font-size: 13px; color: #006666">Forgot Password?</a>
                </div>

                <button name="btn-login-municipality-user" type="submit" class="btn w-100 py-8 fs-4 mb-4 rounded-2 text-light" style="background-color: #006666">Sign In</button>

                <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">New to <strong>HMVHC</strong>?</p>
                    <a class="fw-bold ms-2" href="municipality_user_create_account.php" style="color: #006666">Request Access</a>
                </div>
                </form>
              </div>
              <a class="fw-bold ms-2 mb-4 d-flex align-items-center justify-content-center" href="../logout.php" style="margin-bottom: 20px; color: #006666">RETURN</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
  function togglePasswordVisibility(inputId, icon) {
    const input = document.getElementById(inputId);
    const iconElement = icon.querySelector('i');
    
    if (input.type === "password") {
      input.type = "text";
      iconElement.classList.remove('fa-eye');
      iconElement.classList.add('fa-eye-slash');
    } else {
      input.type = "password";
      iconElement.classList.remove('fa-eye-slash');
      iconElement.classList.add('fa-eye');
    }
  }
</script>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
