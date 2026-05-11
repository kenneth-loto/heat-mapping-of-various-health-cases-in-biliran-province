<?php
include 'header.php';

$categories = $function->getDiseaseCategories();

?>
<html>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-50 shadow-lg">
        <div class="card-body">
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">New Disease</h3>

            <?php
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="border p-3" style="border: 1px solid #ccc; border-radius: 8px; background-color: #e9ecef;">

            <form method="post" action="../navigate.php" enctype="multipart/form-data">
                <div class="row text-dark">

                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="name_of_disease">Name of Disease <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_of_disease" value="<?php echo isset($_SESSION['form_data']['name_of_disease']) ? htmlspecialchars($_SESSION['form_data']['name_of_disease']) : ''; ?>" placeholder="e.g. Diabetes" required>
                    </div>
                </div>

                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="short_description">Short Description <span class="text-danger">*</span></label>
                        <textarea type="text" class="form-control" id="short_description" name="short_description" placeholder="Diabetes is a chronic condition that affects how the body processes blood sugar, leading to high glucose levels." required><?php echo isset($_SESSION['form_data']['short_description']) ? htmlspecialchars($_SESSION['form_data']['short_description']) : ''; ?></textarea>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label" for="disease_category">Disease Category <span class="text-danger">*</span></label>
                    <select class="form-select" name="disease_category" id="disease_category" onchange="toggleNewCategoryInput()" required>
                        <option value="" disabled selected>Select Category</option>
                        <?php
                            // Dynamically generate options for categories from the database
                            foreach ($categories as $category) {
                                // Access object properties using -> instead of array syntax
                                echo "<option value='" . htmlspecialchars($category->disease_category) . "' " . (($category->disease_category == $disease->disease_category) ? 'selected' : '') . ">" . htmlspecialchars($category->disease_category) . "</option>";
                            }
                        ?>
                        <!-- Option for "Not in Category", which will trigger the new category input -->
                        <option value="not_in_category" <?= ($category->disease_category == 'not_in_category') ? 'selected' : ''; ?>>Not in Category</option>
                    </select>
                </div>

                <!-- New category input field -->
                <div class="row text-dark" id="new_category_input" style="display: none;" <?= ($category->disease_category == 'not_in_category') ? 'block' : 'none'; ?>;">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="new_category">New Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="new_category" id="new_category" placeholder="Enter new category" value="<?php echo isset($_SESSION['form_data']['new_category']) ? htmlspecialchars($_SESSION['form_data']['new_category']) : ''; ?>">
                    </div>
                </div>

                <!-- Centered Buttons -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-add-disease" onclick="return confirm('Are you sure you want to submit? Please review the details before proceeding.');" 
                    title="Submit" style="justify-content: center !important;">Submit</button>
                    <a href="diseases.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" style="justify-content: center !important;" title="Cancel">Cancel</a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleNewCategoryInput() {
        const categorySelect = document.getElementById('disease_category');
        const newCategoryInput = document.getElementById('new_category_input');
        if (categorySelect.value === 'not_in_category') {
            newCategoryInput.style.display = 'block';
            document.getElementById('new_category').setAttribute('required', 'required');
        } else {
            newCategoryInput.style.display = 'none';
            document.getElementById('new_category').removeAttribute('required');
        }
    }

    // Run function on page load to check if "Not in Category" is already selected
    window.onload = toggleNewCategoryInput;
</script>

</body>
</html>

