<?php
include 'header.php';

// Get disease ID from the query string
$id = $_GET['id'];

$municipalities = $function->getMunicipalitiesDB(); // Fetch municipalities
$barangays = $function->getBarangaysDB(); // Fetch barangays

// Fetch the disease data (assuming getADisease returns an associative array)
$municipality_user = $function->getAMunicipalityUser($id);

var_dump($municipality_user);
?>
<html>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-100 shadow-lg">
        <div class="card-body">
            <h3 class="text-center mt-4 mb-4 text-dark">New Municipality User</h3>

            <?php
                  $msg = Session::get("msg");
                  if (isset($msg)) {
                      echo $msg;
                      Session::set("msg", NULL);
                  }
                ?>
                <form method="post" action="../navigate.php" class="mt-4 text-dark" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?= htmlspecialchars($municipality_user->id); ?>">

                <h4 class="mb-3 mt-5">Name of Municipality User</h4>

                <!-- Row 1: Full Name -->
                <div class="row">
                <div class="col-md-3 mb-4">
                    <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" placeholder="e.g. Juan" value="<?= isset($_SESSION['form_data']['first_name']) ? htmlspecialchars($_SESSION['form_data']['first_name']) : htmlspecialchars($municipality_user->first_name); ?>" >
                </div>
                
                <div class="col-md-3 mb-4">
                    <label class="form-label">Middle Name (Optional)</label>
                    <input type="text" class="form-control" name="middle_name" placeholder="e.g. Dela" value="<?= isset($_SESSION['form_data']['middle_name']) ? htmlspecialchars($_SESSION['form_data']['middle_name']) : htmlspecialchars($municipality_user->middle_name); ?>">
                </div>

                <div class="col-md-3 mb-4">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" placeholder="e.g. Cruz" value="<?= isset($_SESSION['form_data']['last_name']) ? htmlspecialchars($_SESSION['form_data']['last_name']) : htmlspecialchars($municipality_user->middle_name); ?>" required>
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">Suffix (Optional)</label>
                    <select class="form-select" name="suffix">
                    <option value="">Select Suffix</option>
                    <option value="Jr." <?= isset($_SESSION['form_data']['suffix']) && $_SESSION['form_data']['suffix'] == 'Jr.' ? 'selected' : ''; ?>>Jr.</option>
                    <option value="Jra." <?= isset($_SESSION['form_data']['suffix']) && $_SESSION['form_data']['suffix'] == 'Jra.' ? 'selected' : ''; ?>>Jra.</option>
                    <option value="Sr." <?= isset($_SESSION['form_data']['suffix']) && $_SESSION['form_data']['suffix'] == 'Sr.' ? 'selected' : ''; ?>>Sr.</option>
                    </select>
                </div>
                </div>

                <h4 class="mb-3 mt-3">Address</h4>

                <!-- Province, Municipality, and Barangay Dropdowns -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <label class="form-label" for="province">Province <span class="text-danger">*</span></label>
                        <select class="form-select" id="province" name="province" required>
                            <option value="Biliran">Biliran</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-4">
                        <label class="form-label" for="municipality">Municipality <span class="text-danger">*</span></label>
                        <select class="form-select" id="municipality" name="municipality" required>
                            <option value="">Select Municipality</option>
                            <?php
                            foreach ($municipalities as $municipality) {
                                $selected = $municipality_user->municipality == $municipality->municipality_name ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($municipality->municipality_name) . '" ' . $selected . '>' . htmlspecialchars($municipality->municipality_name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-4">
                        <label class="form-label" for="barangay">Barangay <span class="text-danger">*</span></label>
                        <select class="form-select" id="barangay" name="barangay" required>
                            <option value="">Select Barangay</option>
                            <?php
                            foreach ($barangays as $barangay) {
                                $selected = $municipality_user->barangay == $barangay->barangay_name ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($barangay->barangay_name) . '" ' . $selected . '>' . htmlspecialchars($barangay->barangay_name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-4">
                        <label class="form-label" for="street">Street</label>
                        <input type="text" class="form-control" name="street" value="<?= isset($_SESSION['form_data']['street']) ? htmlspecialchars($_SESSION['form_data']['street']) : htmlspecialchars($municipality_user->street); ?>" placeholder="e.g. St. Garcia">
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Contact Information Section -->
                    <div class="col-md-6 mb-4">
                        <h4 class="mb-3">Contact Information</h4>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="e.g. example@gmail.com" value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : htmlspecialchars($municipality_user->email); ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="contact_number"><strong>Contact Number <span class="text-danger">*</span></strong></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number" maxlength="10" pattern="9[0-9]{9}" 
                                    value="<?= isset($_SESSION['form_data']['contact_number']) ? htmlspecialchars(substr($_SESSION['form_data']['contact_number'], 3)) : htmlspecialchars($municipality_user->contact_number); ?>"
                                    placeholder="9123456789" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="col-md-6 mb-4">
                        <h4 class="mb-3">Password</h4>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password must be at least 8"
                                            value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>" required>
                                    <span class="input-group-text" onclick="togglePasswordVisibility('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
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
                    </div>
                </div>

                <h4 class="mb-3 offset-md-2">Validation</h4>

                  <div class="row justify-content-center">
                    <div class="col-md-8 mb-4">
                        <label for="valid_id">Valid ID <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="valid_id" name="valid_id" accept="image/*" required>
                        <small class="form-text text-muted">Upload a clear image of your valid ID. Have it Landscape if possible.</small>

                        <!-- Image Carousel Container -->
                        <div id="imageCarousel" class="carousel slide mt-3" data-bs-ride="carousel" style="display: none;">
                            <div class="carousel-inner" id="carousel-inner"></div>
                        </div>
                    </div>

                <div class="d-flex justify-content-center">
                    <button name="btn-create-municipality_user" type="submit" class="btn btn-dark w-25 py-2 fs-4 mb-4 rounded-2" style="justify-content: center !important;"
                    onclick="return confirm('Are you sure you want to submit? Please review the details before proceeding.');" 
                    title="Submit">Submit
                    </button>
                </div>

                </form>
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
                    img.style.height = '500px'; // Set fixed height

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
document.getElementById('municipality').addEventListener('change', function() {
    const selectedMunicipality = this.value;
    const barangayDropdown = document.getElementById('barangay');
    
    // Clear existing options
    barangayDropdown.innerHTML = '<option value="">Select Barangay</option>';
    
    // Fetch barangays based on the selected municipality
    <?php
    $barangaysByMunicipality = json_encode($barangays);
    ?>

    const barangays = <?php echo $barangaysByMunicipality; ?>;
    const filteredBarangays = barangays.filter(barangay => barangay.municipality_name === selectedMunicipality);
    
    // Sort barangays alphabetically by name
    filteredBarangays.sort((a, b) => a.barangay_name.localeCompare(b.barangay_name));
    
    // Add sorted barangays to the dropdown
    filteredBarangays.forEach(function(barangay) {
        const option = document.createElement('option');
        option.value = barangay.barangay_name;
        option.textContent = barangay.barangay_name;
        barangayDropdown.appendChild(option);
    });
});
</script>

</body>
</html>

