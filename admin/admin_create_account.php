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
  <title>RPTMTCS</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-5">
            <div class="card mb-0">
              <div class="card-body">
                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="../assets/images/logos/logo.png" width="200" alt="">
                </a>
                <h5 class="text-center">WELCOME TO <strong>RPTMTCS</strong></h5>
                <?php
                  $msg = Session::get("msg");
                  if (isset($msg)) {
                      echo $msg;
                      Session::set("msg", NULL);
                  }
                ?>
                <form method="post" action="../navigate.php" class="mt-4" enctype="multipart/form-data">

                <h6 class="fw-bold mb-4 mt-5">NAME OF DECLARANT</h6>

                <!-- Row 1: Full Name -->
                <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" placeholder="e.g. Juan" value="<?php echo isset($_SESSION['form_data']['first_name']) ? htmlspecialchars($_SESSION['form_data']['first_name']) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Middle Name (Optional)</label>
                    <input type="text" class="form-control" name="middle_name" placeholder="e.g. Dela" value="<?php echo isset($_SESSION['form_data']['middle_name']) ? htmlspecialchars($_SESSION['form_data']['middle_name']) : ''; ?>">
                </div>
                </div>

                <!-- Row 2: Middle Name (optional) and Suffix (optional) -->
                <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" placeholder="e.g. Cruz" value="<?php echo isset($_SESSION['form_data']['last_name']) ? htmlspecialchars($_SESSION['form_data']['last_name']) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Suffix (Optional)</label>
                    <select class="form-select" name="suffix">
                    <option value="">Select Suffix</option>
                    <option value="Jr." <?php echo isset($_SESSION['form_data']['suffix']) && $_SESSION['form_data']['suffix'] == 'Jr.' ? 'selected' : ''; ?>>Jr.</option>
                    <option value="Jra." <?php echo isset($_SESSION['form_data']['suffix']) && $_SESSION['form_data']['suffix'] == 'Jra.' ? 'selected' : ''; ?>>Jra.</option>
                    <option value="Sr." <?php echo isset($_SESSION['form_data']['suffix']) && $_SESSION['form_data']['suffix'] == 'Sr.' ? 'selected' : ''; ?>>Sr.</option>
                    </select>
                </div>
                </div>

                <h6 class="fw-bold mb-4 mt-4">CONTACT INFORMATION</h6>

                <!-- Row 3: Email and Contact Number -->
                <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" placeholder="e.g. example@gmail.com" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label" for="contact_number"><strong>Contact Number <span class="text-danger">*</span></strong></label>
                    <div class="input-group">
                        <span class="input-group-text">+63</span>
                        <input type="tel" class="form-control" id="contact_number" name="contact_number" maxlength="10" pattern="9[0-9]{9}" 
                        value="<?php echo isset($_SESSION['form_data']['contact_number']) ? htmlspecialchars(substr($_SESSION['form_data']['contact_number'], 3)) : ''; ?>"
                        placeholder="9123456789" required>
                    </div>
                </div>
                </div>

                <h6 class="fw-bold mb-4 mt-4">ADDRESS</h6>

                <!-- Province, Municipality, and Barangay Dropdowns -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                      <label class="form-label" for="province">Province <span class="text-danger">*</span></label>
                      <!-- Make the input field readonly and set the value to the name of the province (Biliran) -->
                      <input type="text" class="form-control" id="province" name="province" value="Biliran" readonly>
                      <input type="hidden" id="provinceCode" name="provinceCode" value="Biliran"> <!-- Hidden field to store the province code -->
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="municipality">Municipality <span class="text-danger">*</span></label>
                        <select class="form-select" id="municipality" name="municipality" required>
                            <option value="">Select Municipality</option>
                        </select>
                        <input type="hidden" id="municipalityName" name="municipalityName">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="barangay">Barangay <span class="text-danger">*</span></label>
                        <select class="form-select" id="barangay" name="barangay" required>
                            <option value="">Select Barangay</option>
                        </select>
                        <input type="hidden" id="barangayName" name="barangayName">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="street">Street</label>
                        <input type="text" class="form-control" name="street" value="<?php echo isset($_SESSION['form_data']['street']) ? htmlspecialchars($_SESSION['form_data']['street']) : ''; ?>" placeholder="e.g. St. Garcia">
                    </div>
                </div>

                <h6 class="fw-bold mb-4 mt-4">PASSWORD</h6>

                <!-- Row 5: Password and Confirm Password -->
                <div class="row"> 
                <div class="col-md-6 mb-4">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Minimum of 8 or more characters"
                            value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>" required>
                    <span class="input-group-text" onclick="togglePasswordVisibility('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                    </div>
                    <small class="form-text text-muted">
                        Password must be in uppercase, lowercase, number, and a character.
                    </small>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Re-enter your password"
                            value="<?php echo isset($_SESSION['form_data']['confirmpassword']) ? htmlspecialchars($_SESSION['form_data']['confirmpassword']) : ''; ?>" required>
                    <span class="input-group-text" onclick="togglePasswordVisibility('confirmpassword', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                    </div>
                </div>
                </div>

                  <h6 class="fw-bold mb-4 mt-4">VALIDATION</h6>

                  <div class="row">
                    <div class="col-md-12 mb-4">
                        <label for="valid_id">Valid ID <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="valid_id" name="valid_id" accept="image/*" required>
                        <small class="form-text text-muted">Upload a clear image of your valid ID. Have it Landscape if possible.</small>

                        <!-- Image Carousel Container -->
                        <div id="imageCarousel" class="carousel slide mt-3" data-bs-ride="carousel" style="display: none;">
                            <div class="carousel-inner" id="carousel-inner"></div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>

                  <div class="d-flex justify-content-center">
                    <button name="btn-create-admin" type="submit" class="btn btn-dark w-50 py-2 fs-4 mb-4 rounded-2" 
                    onclick="return confirm('Are you sure you want to sign up? Please review your details before proceeding.');" 
                    title="Sign Up">Sign Up
                    </button>
                  </div>
                  
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Already have an account?</p>
                    <a class="text-primary fw-bold ms-2" href="applicant_login.php">Sign In</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('valid_id').addEventListener('change', function(event) {
        const carouselInner = document.getElementById('carousel-inner');
        const carouselContainer = document.getElementById('imageCarousel');
        carouselInner.innerHTML = ''; // Clear previous previews
        carouselContainer.style.display = 'none'; // Hide carousel initially

        Array.from(event.target.files).forEach((file, index) => {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'carousel-item' + (index === 0 ? ' active' : '');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'd-block w-100';
                    img.style.width = '100%'; // Set fixed width
                    img.style.height = '300px'; // Set fixed height

                    itemDiv.appendChild(img);
                    carouselInner.appendChild(itemDiv);
                };
                reader.readAsDataURL(file);
            }
        });

        if (event.target.files.length > 0) {
            carouselContainer.style.display = 'block'; // Show carousel when there are images
        }
    });
</script>

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

<script>
// Fetch and sort provinces from local JSON file
fetch('../assets/json/provinces.json')
  .then(response => response.json())
  .then(data => {
    const provinceDropdown = document.getElementById('province');
    const sortedProvinces = data.sort((a, b) => a.name.localeCompare(b.name));

    // Set province value and auto-select Biliran
    sortedProvinces.forEach(province => {
      if (province.name === 'Biliran') {
        provinceDropdown.value = province.name;
        document.getElementById('provinceCode').value = province.code; // Set hidden field with province code
      }
    });

    // Populate municipalities for Biliran
    const biliranProvinceCode = sortedProvinces.find(province => province.name === 'Biliran').code;
    populateMunicipalities(biliranProvinceCode); // Auto-populate municipalities for Biliran
  })
  .catch(error => console.error('Error fetching provinces:', error));

// Function to populate the municipality dropdown based on selected province
function populateMunicipalities(provinceCode) {
  fetch('../assets/json/municipalities.json')
    .then(response => response.json())
    .then(data => {
      const municipalityDropdown = document.getElementById('municipality');
      municipalityDropdown.innerHTML = '<option value="">Select Municipality</option>';

      // Filter municipalities by provinceCode
      const filteredMunicipalities = data.filter(municipality => municipality.provinceCode === provinceCode);
      const sortedMunicipalities = filteredMunicipalities.sort((a, b) => a.name.localeCompare(b.name));

      sortedMunicipalities.forEach(municipality => {
        municipalityDropdown.innerHTML += `<option value="${municipality.code}">${municipality.name}</option>`;
      });

      // Reset barangay dropdown
      document.getElementById('barangay').innerHTML = '<option value="">Select Barangay</option>';
    })
    .catch(error => console.error('Error fetching municipalities:', error));
}

// Update hidden input when municipality is selected
document.getElementById('municipality').addEventListener('change', function () {
  const selectedOption = this.options[this.selectedIndex];
  const municipalityNameInput = document.getElementById('municipalityName');
  municipalityNameInput.value = selectedOption.text; // Set the name

  const municipalityCode = this.value;
  fetch('../assets/json/barangays.json')
    .then(response => response.json())
    .then(data => {
      const barangayDropdown = document.getElementById('barangay');
      barangayDropdown.innerHTML = '<option value="">Select Barangay</option>';

      // Filter barangays by municipalityCode
      const filteredBarangays = data.filter(barangay => barangay.municipalityCode === municipalityCode);
      const sortedBarangays = filteredBarangays.sort((a, b) => a.name.localeCompare(b.name));

      sortedBarangays.forEach(barangay => {
        barangayDropdown.innerHTML += `<option value="${barangay.code}">${barangay.name}</option>`;
      });
    })
    .catch(error => console.error('Error fetching barangays:', error));
});

// Update hidden input when barangay is selected
document.getElementById('barangay').addEventListener('change', function () {
  const selectedOption = this.options[this.selectedIndex];
  const barangayNameInput = document.getElementById('barangayName');
  barangayNameInput.value = selectedOption.text; // Set the name
});
</script>

  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
