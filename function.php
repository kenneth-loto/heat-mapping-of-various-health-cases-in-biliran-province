<?php

require 'PHPMailer-6.9.2/src/Exception.php';
require 'PHPMailer-6.9.2/src/PHPMailer.php';
require 'PHPMailer-6.9.2/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'conn.php';

class Functions
{
    private $db;

    public function __construct()
    {
        $this->db = new conn();
    }


    // Session

    // Function to start a session and check login status
    public function checkSession()
    {
        if (!isset($_SESSION['email'])) {
            header("Location: ../login.php");
            exit; // Stop further execution
        }
    }

    // Account Creation

    public function createAdmin($data)
    {
        // Check if email already exists
        $sqlCheckEmail = "SELECT * FROM admin WHERE email = :email";
        $stmtCheckEmail = $this->db->conn->prepare($sqlCheckEmail);
        $stmtCheckEmail->execute([':email' => $data['email']]);

        if ($stmtCheckEmail->rowCount() > 0) {
            return -1; // Email already exists
        }

        // Check if password and confirm password match
        if ($data['password'] !== $data['confirmpassword']) {
            return -2; // Passwords do not match
        }

        // Check password complexity
        if (
            !preg_match('/[A-Z]/', $data['password']) ||
            !preg_match('/[a-z]/', $data['password']) ||
            !preg_match('/[0-9]/', $data['password']) ||
            !preg_match('/[\W_]/', $data['password']) ||
            strlen($data['password']) < 8
        ) {
            return -3; // Password does not meet complexity requirements
        }

        // Handling valid ID upload
        $valid_id = [];
        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "assets/images/uploads/valid_id/"; // Directory to store uploaded images
            $fileName = basename($_FILES["valid_id"]["name"]);
            $targetFile = $targetDir . $fileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["valid_id"]["tmp_name"], $targetFile)) {
                $valid_id[] = $targetFile; // Add file path to the valid_id array
            } else {
                echo "Sorry, there was an error uploading the file: " . $fileName;
            }
        }

        // Store file paths as a JSON array in the 'valid_id' field
        $valid_idJSON = json_encode($valid_id);


        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert new user data into the database
        $sql = "INSERT INTO admin (first_name, middle_name, last_name, suffix, province, municipality, barangay, street, email, contact_number, valid_id, password) 
                VALUES (:first_name, :middle_name, :last_name, :suffix, :province, :municipalityName, :barangay, :street, :email, :contact_number, :valid_id, :password)";

        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'],
            ':last_name' => $data['last_name'],
            ':suffix' => $data['suffix'],
            ':province' => $data['province'],
            ':municipalityName' => $data['municipalityName'],
            ':barangay' => $data['barangay'],
            ':street' => $data['street'],
            ':email' => $data['email'],
            ':contact_number' => $data['contact_number'],
            ':valid_id' => $valid_idJSON,
            ':password' => $hashedPassword
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function authenticateAdmin($email, $password)
    {
        // Query to retrieve the hashed password associated with the provided email
        $sql = "SELECT * FROM admin WHERE email = :email";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        // Check if the user exists
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashed_password = $row['password'];

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                return 1; // Password is correct
            } else {
                return 2; // Incorrect password
            }
        } else {
            return 3; // User does not exist
        }
    }

    //Diseases

    public function addDisease($data)
    {
        // Check if the name of disease already exists in the database
        $checkDiseaseSql = "SELECT COUNT(*) FROM diseases WHERE name_of_disease = :name_of_disease";
        $stmt = $this->db->conn->prepare($checkDiseaseSql);
        $stmt->execute([':name_of_disease' => $data['name_of_disease']]);
        $diseaseExists = $stmt->fetchColumn();

        if ($diseaseExists) {
            return -1; // Error: Disease name already exists
        }

        // If the selected category is "not_in_category" and a new category is provided
        if ($data['disease_category'] === 'not_in_category' && !empty($data['new_category'])) {
            // Check if the new category already exists
            $checkCategorySql = "SELECT COUNT(*) FROM diseases WHERE disease_category = :disease_category";
            $stmt = $this->db->conn->prepare($checkCategorySql);
            $stmt->execute([':disease_category' => $data['new_category']]);
            $categoryExists = $stmt->fetchColumn();

            if ($categoryExists) {
                return -2; // Error: Category name already exists
            }

            // Set the new category as the disease category
            $data['disease_category'] = $data['new_category'];
        }

        // Insert new disease data into the database
        $sql = "INSERT INTO diseases (name_of_disease, short_description, disease_category) 
                VALUES (:name_of_disease, :short_description, :disease_category)";

        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([
            ':name_of_disease' => $data['name_of_disease'],
            ':short_description' => $data['short_description'],
            ':disease_category' => $data['disease_category'],
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }


    public function getAllDiseases()
    {
        // Prepare the SQL query to retrieve all diseases
        $sql = 'SELECT * FROM diseases ORDER BY name_of_disease ASC';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute(); // Execute the query

        // Fetch all rows as objects
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Return the fetched data
        return $data;
    }

    public function getADisease($id)
    {
        // Prepare the SQL query to retrieve a single applicant by ID
        $sql = 'SELECT * FROM diseases WHERE id = :id LIMIT 1';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
        $stmt->execute(); // Execute the query

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return $data;
    }

    public function updateDisease($id, $data)
    {
        // Step 1: Check if the new disease name already exists in the database
        $checkDiseaseSql = "SELECT COUNT(*) FROM diseases WHERE name_of_disease = :name_of_disease AND id != :id";
        $stmt = $this->db->conn->prepare($checkDiseaseSql);
        $stmt->execute([
            ':name_of_disease' => $data['name_of_disease'],
            ':id' => $id  // Exclude the current record from the check
        ]);
        $diseaseExists = $stmt->fetchColumn();

        if ($diseaseExists) {
            return -1; // Error: Disease name already exists
        }

        // Step 2: Check if the new category (if provided) already exists
        if ($data['disease_category'] === 'not_in_category' && !empty($data['new_category'])) {
            // Check if the new category already exists in the database
            $checkCategorySql = "SELECT COUNT(*) FROM diseases WHERE disease_category = :disease_category";
            $stmt = $this->db->conn->prepare($checkCategorySql);
            $stmt->execute([':disease_category' => $data['new_category']]);
            $categoryExists = $stmt->fetchColumn();

            if ($categoryExists) {
                return -2; // Error: Category name already exists
            }

            // If the category does not exist, set it to the new category
            $data['disease_category'] = $data['new_category'];
        }

        // Step 3: Update the disease record in the 'diseases' table
        $sql = "UPDATE diseases SET name_of_disease = :name_of_disease, short_description = :short_description, disease_category = :disease_category, updated_at = NOW() WHERE id = :id";

        $stmtUpdateDisease = $this->db->conn->prepare($sql);
        $resultUpdateDisease = $stmtUpdateDisease->execute([
            ':id' => $id,
            ':name_of_disease' => $data['name_of_disease'],
            ':short_description' => $data['short_description'],
            ':disease_category' => $data['disease_category']
        ]);

        if ($resultUpdateDisease) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }


    public function deleteDisease($id)
    {
        $sql = 'DELETE FROM diseases WHERE id=:id';
        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([':id' => $id]);
        if ($r) {
            return 1;
        } else {
            return 0;
        }
    }

    public function addPatient($data)
    {
        // Load the JSON files for barangay and municipality coordinates
        $municipalityCoordinates = json_decode(file_get_contents('municipality_coordinates.json'), true);
        $barangayCoordinates = json_decode(file_get_contents('barangay_coordinates.json'), true);

        // Find the geometry for the specified municipalityCode and barangayCode
        $municipalityGeom = null;
        $barangayGeom = null;
        $barangayLatitude = null;
        $barangayLongitude = null;

        // Search for the municipality geometry
        foreach ($municipalityCoordinates as $municipality) {
            if ($municipality['code'] === $data['municipalityCode']) {
                $municipalityGeom = json_encode($municipality['geom']); // Store as JSON
                break;
            }
        }

        // Search for the barangay geometry and latitude/longitude
        foreach ($barangayCoordinates as $barangay) {
            if ($barangay['code'] === $data['barangayCode']) {
                $barangayGeom = json_encode($barangay['geom']); // Store as JSON
                $barangayLatitude = $barangay['latitude'];  // Assuming latitude is in the JSON data
                $barangayLongitude = $barangay['longitude']; // Assuming longitude is in the JSON data
                break;
            }
        }

        // If either geometry is not found, return an error
        if ($municipalityGeom === null || $barangayGeom === null) {
            return 0; // Failure due to missing geometry data
        }

        // Insert new patient data into the database, including the geometry and latitude/longitude data
        $sql = "INSERT INTO patients 
                (name_of_patient, municipalityCode, municipality, municipality_geom, barangayCode, barangay, barangay_geom, latitude, longitude, patients_disease, status, created_at) 
                VALUES 
                (:name_of_patient, :municipalityCode, :municipalityName, :municipality_geom, :barangayCode, :barangayName, :barangay_geom, :latitude, :longitude, :patients_disease, :status, NOW())";

        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([
            ':name_of_patient' => $data['name_of_patient'],
            ':municipalityCode' => $data['municipalityCode'],
            ':municipalityName' => $data['municipalityName'],
            ':municipality_geom' => $municipalityGeom,  // JSON-encoded geometry
            ':barangayCode' => $data['barangayCode'],
            ':barangayName' => $data['barangayName'],
            ':barangay_geom' => $barangayGeom,  // JSON-encoded geometry
            ':latitude' => $barangayLatitude,
            ':longitude' => $barangayLongitude,
            ':patients_disease' => $data['patients_disease'],
            ':status' => $data['status'],
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function getAllPatients()
    {
        // Prepare the SQL query to retrieve all requests
        $sql = 'SELECT * FROM patients ORDER BY id ASC';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute(); // No need for parameters since we are fetching all records
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as associative array
        return $data;
    }

    public function getAPatient($id)
    {
        // Prepare the SQL query to retrieve a single applicant by ID
        $sql = 'SELECT * FROM patients WHERE id = :id LIMIT 1';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
        $stmt->execute(); // Execute the query

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return $data;
    }

    public function updatePatient($id, $data)
    {
        $municipalityName = $_POST['municipalityName'];
        $barangayName = $_POST['barangayName'];

        // Load the JSON files for barangay and municipality coordinates
        $municipalityCoordinates = json_decode(file_get_contents('municipality_coordinates.json'), true);
        $barangayCoordinates = json_decode(file_get_contents('barangay_coordinates.json'), true);

        // Find the geometry for the specified municipalityCode and barangayCode
        $municipalityGeom = null;
        $barangayGeom = null;
        $barangayLatitude = null;
        $barangayLongitude = null;

        // Search for the municipality geometry
        foreach ($municipalityCoordinates as $municipality) {
            if ($municipality['code'] === $data['municipalityCode']) {
                $municipalityGeom = $municipality['geom'];  // Assuming it's in JSON format
                break;
            }
        }

        // Search for the barangay geometry and latitude/longitude
        foreach ($barangayCoordinates as $barangay) {
            if ($barangay['code'] === $data['barangayCode']) {
                $barangayGeom = $barangay['geom'];  // Assuming it's in JSON format
                $barangayLatitude = $barangay['latitude'];  // Assuming latitude is in the JSON data
                $barangayLongitude = $barangay['longitude']; // Assuming longitude is in the JSON data
                break;
            }
        }

        // If either geometry is not found, return an error
        if ($municipalityGeom === null || $barangayGeom === null) {
            return 0; // Failure due to missing geometry data
        }

        // Update the patient data in the database, including the geometry and latitude/longitude data
        $sql = "UPDATE patients 
                SET name_of_patient = :name_of_patient, 
                    municipalityCode = :municipalityCode, 
                    municipality = :municipalityName, 
                    municipality_geom = :municipality_geom,  -- No ST_GeomFromText needed
                    barangayCode = :barangayCode, 
                    barangay = :barangayName, 
                    barangay_geom = :barangay_geom,  -- No ST_GeomFromText needed
                    latitude = :latitude,
                    longitude = :longitude,
                    patients_disease = :patients_disease, 
                    status = :status, 
                    updated_at = NOW() 
                WHERE id = :id";

        $stmt = $this->db->conn->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':name_of_patient' => $data['name_of_patient'],
            ':municipalityCode' => $data['municipalityCode'],
            ':municipalityName' => $municipalityName,
            ':municipality_geom' => json_encode($municipalityGeom),  // Ensure the geometry is encoded as JSON
            ':barangayCode' => $data['barangayCode'],
            ':barangayName' => $barangayName,
            ':barangay_geom' => json_encode($barangayGeom),  // Ensure the geometry is encoded as JSON
            ':latitude' => $barangayLatitude,
            ':longitude' => $barangayLongitude,
            ':patients_disease' => $data['patients_disease'],
            ':status' => $data['status']
        ]);

        if ($result) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function deletePatient($id)
    {
        $sql = 'DELETE FROM patients WHERE id=:id';
        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([':id' => $id]);
        if ($r) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getFilteredPatients($disease = '', $municipality = '')
    {
        // Start with basic query
        $sql = "SELECT * FROM cases WHERE 1";
        $params = [];

        // Add condition for disease if selected and it's not "All"
        if ($disease !== '' && $disease !== 'All') {
            $sql .= " AND name_of_disease = :disease";
            $params[':disease'] = $disease;
        }

        // Add condition for municipality if selected
        if ($municipality !== '') {
            $sql .= " AND municipality_name = :municipality";
            $params[':municipality'] = $municipality;
        }

        // Optionally, add ordering (e.g., by name or date created)
        $sql .= " ORDER BY municipality_name ASC";

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Fetch and return the results
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $patients; // Return an array of patients, or an empty array if no results
    }

    public function getMunicipalities()
    {
        $sql = "SELECT DISTINCT municipality_name FROM cases WHERE municipality_name IS NOT NULL ORDER BY municipality_name ";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDiseases()
    {
        $sql = "SELECT DISTINCT name_of_disease FROM cases WHERE name_of_disease IS NOT NULL ORDER BY name_of_disease ";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMunicipality($data)
    {
        // Check if 'geometry' is empty or missing
        if (empty($data['geometry'])) {
            return -1; // Geometry is required but missing
        }

        // Validate GeoJSON format
        $geometry = json_decode($data['geometry']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If decoding fails, it's not a valid GeoJSON
            return 2; // Invalid GeoJSON
        }

        // SQL query to insert new municipality data into the database
        $sql = "INSERT INTO municipalities (municipal_code, municipality_name, no_of_population, geom) 
                VALUES (:municipal_code, :municipality_name, :no_of_population, :geom)";

        $stmt = $this->db->conn->prepare($sql);

        // Execute query with bound values
        $r = $stmt->execute([
            ':municipal_code' => $data['municipal_code'],
            ':municipality_name' => $data['municipality_name'],
            ':no_of_population' => $data['no_of_population'],
            ':geom' => $data['geometry'] // Pass the GeoJSON string directly
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function getAllTheMunicipalities()
    {
        // Prepare the SQL query to retrieve all municipalities along with their geom data
        $sql = 'SELECT * FROM municipalities ORDER BY municipality_name ASC';

        // Prepare the statement
        $stmt = $this->db->conn->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the data with municipality name and its geometry
        return $data;
    }

    public function getAllMunicipalities()
    {
        // Prepare the SQL query to retrieve all municipalities along with their geom data
        $sql = 'SELECT municipality_name, geom FROM municipalities ORDER BY municipality_name ASC';

        // Prepare the statement
        $stmt = $this->db->conn->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode the 'geom' field from JSON to an array, if necessary
        foreach ($data as &$row) {
            $row['geom'] = json_decode($row['geom']);
        }

        // Return the data with municipality name and its geometry
        return $data;
    }


    public function getAMunicipality($id)
    {
        // Prepare the SQL query to retrieve a single applicant by ID
        $sql = 'SELECT * FROM municipalities WHERE id = :id LIMIT 1';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
        $stmt->execute(); // Execute the query

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return $data;
    }

    public function updateMunicipality($id, $data)
    {
        // Step 1: Check if the geometry (coordinates) is provided
        if (empty($data['geometry'])) {
            return -1; // Return -1 to indicate that geometry is required
        }

        // Step 2: Validate GeoJSON format (optional but recommended)
        $geometry = json_decode($data['geometry']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return 2; // Invalid GeoJSON format, return 0
        }

        // Step 3: Update the municipality record in the 'municipalities' table
        $sql = "UPDATE municipalities 
                SET municipality_code = :municipality_code, 
                    municipality_name = :municipality_name, 
                    no_of_population = :no_of_population, 
                    geom = :geom, 
                    updated_at = NOW() 
                WHERE id = :id";

        // Prepare the SQL statement
        $stmtUpdateMunicipality = $this->db->conn->prepare($sql);

        // Execute the query with bound values, including the updated geometry
        $resultUpdateMunicipality = $stmtUpdateMunicipality->execute([
            ':id' => $id,
            ':municipality_code' => $data['municipality_code'],
            ':municipality_name' => $data['municipality_name'],
            ':no_of_population' => $data['no_of_population'],
            ':geom' => $data['geometry'] // Updated GeoJSON data
        ]);

        // Return success or failure based on the query result
        if ($resultUpdateMunicipality) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function deleteMunicipality($id)
    {
        $sql = 'DELETE FROM municipalities WHERE id=:id';
        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([':id' => $id]);
        if ($r) {
            return 1;
        } else {
            return 0;
        }
    }

    public function addBarangay($data)
    {
        // Check if 'geometry' is empty or missing
        if (empty($data['geometry'])) {
            return -1; // Geometry is required but missing
        }

        // Validate GeoJSON format
        $geometry = json_decode($data['geometry']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If decoding fails, it's not a valid GeoJSON
            return 2; // Invalid GeoJSON
        }

        // Insert new barangay data into the database
        $sql = "INSERT INTO barangays (municipality_name, barangay_code, barangay_name, no_of_population, geom) 
                VALUES (:municipality_name, :barangay_code, :barangay_name, :no_of_population, :geom)";

        $stmt = $this->db->conn->prepare($sql);

        // Correct the syntax by adding the missing commas
        $r = $stmt->execute([
            ':municipality_name' => $data['municipality_name'],
            ':barangay_code' => $data['barangay_code'],
            ':barangay_name' => $data['barangay_name'],
            ':no_of_population' => $data['no_of_population'],
            ':geom' => $data['geometry']
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function getAllBarangays()
    {
        // Prepare the SQL query to retrieve all requests
        $sql = 'SELECT * FROM barangays ORDER BY barangay_name ASC';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute(); // No need for parameters since we are fetching all records
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as associative array
        return $data;
    }

    public function getABarangay($id)
    {
        // Prepare the SQL query to retrieve a single applicant by ID
        $sql = 'SELECT * FROM barangays WHERE id = :id LIMIT 1';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
        $stmt->execute(); // Execute the query

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return $data;
    }

    public function getMunicipalitiesAndBarangays($municipality_name = null)
    {
        // Prepare the SQL query to retrieve all municipalities
        $sql = 'SELECT * FROM municipalities ORDER BY municipality_name ASC';

        // Prepare and execute the statement to get municipalities
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();

        // Fetch all municipalities as objects
        $municipalities = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Initialize an empty array for barangays
        $barangays = [];

        // If a municipality name is provided, fetch barangays for that municipality
        if ($municipality_name) {
            $sql_barangays = 'SELECT * FROM barangays WHERE municipality_name = :municipality_name ORDER BY barangay_name ASC';
            $stmt_barangays = $this->db->conn->prepare($sql_barangays);

            // Bind the municipality name to the query
            $stmt_barangays->bindParam(':municipality_name', $municipality_name, PDO::PARAM_STR);

            // Execute the barangay query
            $stmt_barangays->execute();

            // Fetch all barangays as objects
            $barangays = $stmt_barangays->fetchAll(PDO::FETCH_OBJ);
        }

        // Return both municipalities and barangays as objects
        return ['municipalities' => $municipalities, 'barangays' => $barangays];
    }

    public function updateBarangay($id, $data)
    {
        // Step 1: Check if the geometry (coordinates) is provided
        if (empty($data['geometry'])) {
            return -1; // Return -1 to indicate that geometry is required
        }

        // Step 2: Validate GeoJSON format (optional but recommended)
        $geometry = json_decode($data['geometry']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return 2; // Invalid GeoJSON format, return 0
        }

        // Step 1: Update the disease record in the 'diseases' table
        $sql = "UPDATE barangays SET municipality_name = :municipality_name, barangay_code = :barangay_code, barangay_name = :barangay_name, no_of_population = :no_of_population, no_of_population = :no_of_population, updated_at = NOW() WHERE id = :id";

        $stmtUpdateDisease = $this->db->conn->prepare($sql);
        $resultUpdateDisease = $stmtUpdateDisease->execute([
            ':id' => $id,
            ':municipality_name' => $data['municipality_name'],
            ':barangay_code' => $data['barangay_code'],
            ':barangay_name' => $data['barangay_name'],
            ':no_of_population' => $data['no_of_population'],
            ':geom' => $data['geometry']
        ]);

        if ($resultUpdateDisease) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function deleteBarangay($id)
    {
        $sql = 'DELETE FROM barangays WHERE id=:id';
        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([':id' => $id]);
        if ($r) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getFilteredBarangays($municipality = '')
    {
        // Start with basic query
        $sql = "SELECT * FROM barangays WHERE 1";
        $params = [];

        // Add condition for municipality if selected
        if ($municipality !== '') {
            $sql .= " AND municipality_name = :municipality";
            $params[':municipality'] = $municipality;
        }

        // Optionally, add ordering (e.g., by barangay name or population)
        $sql .= " ORDER BY municipality_name ASC";

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Fetch and return the results
        $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $barangays; // Return an array of barangays, or an empty array if no results
    }

    // Function to get distinct municipalities
    public function getDistinctMunicipalities()
    {
        $stmt = $this->db->conn->prepare("SELECT DISTINCT municipality FROM patients");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to get patients by disease and optionally by municipality
    public function getPatientsByDisease($disease, $municipality = '', $fromDate = '', $toDate = '')
    {
        $sql = "SELECT 
                    id, 
                    name_of_patient, 
                    latitude, 
                    longitude, 
                    patients_disease,
                    status,
                    municipality_geom,
                    barangay_geom,
                    created_at,
                    updated_at
                FROM patients
                WHERE patients_disease = :disease";

        // Add municipality condition if provided
        if (!empty($municipality)) {
            $sql .= " AND municipality = :municipality";
        }

        // Add date range condition if provided
        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND created_at BETWEEN :fromDate AND :toDate";
        } elseif (!empty($fromDate)) {
            $sql .= " AND created_at >= :fromDate";
        } elseif (!empty($toDate)) {
            $sql .= " AND created_at <= :toDate";
        }

        // Prepare the statement
        $stmt = $this->db->conn->prepare($sql);

        // Bind the parameters
        $params = ['disease' => $disease];
        if (!empty($municipality)) {
            $params['municipality'] = $municipality;
        }
        if (!empty($fromDate)) {
            $params['fromDate'] = $fromDate;
        }
        if (!empty($toDate)) {
            $params['toDate'] = $toDate;
        }

        // Execute the statement
        $stmt->execute($params);

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchCases()
    {
        // Prepare the SQL query to fetch cases from the last 30 days
        $sql = "
            SELECT 
                id,
                patients_id,
                municipality_name,
                barangay_name,
                street,
                name_of_disease,
                geom,
                created_at
            FROM 
                cases
            WHERE 
                created_at >= NOW() - INTERVAL 30 DAY
            ORDER BY 
                created_at DESC
        ";

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();

        // Fetch all results as associative arrays
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $cases;
    }

    public function getHeatmapData($cases)
    {
        ini_set('memory_limit', '256M');

        $heatmapData = [];
        $dateFormat = 'Y-m-d'; // Standard date format for comparison

        // Define time range for the last 30 days
        $currentDate = new DateTime();
        $thirtyDaysAgo = (new DateTime())->modify('-30 days');

        // Intensity thresholds for different diseases
        $intensityThresholds = [
            'Diabetes' => ['high' => 5, 'medium' => 3, 'low' => 0],
            'Pneumonia' => ['high' => 5, 'medium' => 3, 'low' => 0],
            'Monkey Fox' => ['high' => 5, 'medium' => 3, 'low' => 0],
            'Tuberculosis' => ['high' => 5, 'medium' => 3, 'low' => 0],
            'HFMD' => ['high' => 5, 'medium' => 3, 'low' => 0],
        ];

        // Initialize array to store daily case counts for each barangay and disease
        $dailyBarangayCounts = [];

        // Loop through cases to aggregate data by barangay
        foreach ($cases as $case) {
            // Extract relevant case data
            $coordinates = json_decode($case['geom'], true); // Assuming it's always valid GeoJSON
            $longitude = $coordinates[0][0] ?? null; // Ensure you're accessing the right part of the GeoJSON structure
            $latitude = $coordinates[0][1] ?? null;

            // Skip if latitude or longitude is not valid
            if (empty($latitude) || empty($longitude)) {
                continue;
            }

            // Get disease, barangay, and created date from case
            $disease = $case['name_of_disease'];
            $barangayName = $case['barangay_name'];
            $createdAt = new DateTime($case['created_at']);
            $dailyKey = $createdAt->format($dateFormat);

            // Initialize daily count for barangay and disease if not set
            if (!isset($dailyBarangayCounts[$barangayName])) {
                $dailyBarangayCounts[$barangayName] = [];
            }
            if (!isset($dailyBarangayCounts[$barangayName][$disease])) {
                $dailyBarangayCounts[$barangayName][$disease] = [];
            }

            // Increment daily case count for the specific barangay and disease
            $dailyBarangayCounts[$barangayName][$disease][$dailyKey] =
                ($dailyBarangayCounts[$barangayName][$disease][$dailyKey] ?? 0) + 1;
        }

        // Now generate the heatmap data
        foreach ($cases as $case) {
            // Extract case data again
            $coordinates = json_decode($case['geom'], true);
            $longitude = $coordinates[0][0] ?? null;
            $latitude = $coordinates[0][1] ?? null;

            // Skip if latitude or longitude is not valid
            if (empty($latitude) || empty($longitude)) {
                continue;
            }

            // Get disease, barangay, and created date from case
            $disease = $case['name_of_disease'];
            $barangayName = $case['barangay_name'];
            $createdAt = new DateTime($case['created_at']);
            $dailyKey = $createdAt->format($dateFormat);

            // Get the number of cases in the same barangay on the same day for this disease
            $dailyCount = $dailyBarangayCounts[$barangayName][$disease][$dailyKey] ?? 0;

            // Calculate intensity based on the number of cases on the same day in the same barangay
            $intensity = '1'; // Default to stable
            if ($dailyCount >= $intensityThresholds[$disease]['high']) {
                $intensity = '0.6'; // High intensity
            } elseif ($dailyCount >= $intensityThresholds[$disease]['medium']) {
                $intensity = '0.2'; // Medium intensity
            }

            // Store heatmap data with latitude, longitude, intensity, and additional details
            $heatmapData[] = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'intensity' => $intensity,
            ];
        }

        // Return the heatmap data
        return $heatmapData;
    }

    public function getDiseaseCategories()
    {
        // Prepare the SQL query to retrieve distinct disease categories
        $sql = 'SELECT DISTINCT disease_category FROM diseases';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute(); // Execute the query

        // Fetch all results as objects
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $categories;
    }

    // Function to fetch municipalities
    public function getMunicipalitiesDB()
    {
        $query_municipalities = "SELECT DISTINCT municipality_name FROM municipalities";
        $stmt_municipalities = $this->db->conn->query($query_municipalities); // Using $this->db->conn
        return $stmt_municipalities->fetchAll(PDO::FETCH_OBJ); // Return as objects
    }

    // Function to fetch barangays
    public function getBarangaysDB()
    {
        $query_barangays = "SELECT barangay_name, municipality_name FROM barangays";
        $stmt_barangays = $this->db->conn->query($query_barangays); // Using $this->db->conn
        return $stmt_barangays->fetchAll(PDO::FETCH_OBJ); // Return as objects
    }

    public function getNameOfDiseases()
    {
        // Prepare the SQL query to retrieve distinct disease categories
        $sql = 'SELECT DISTINCT name_of_disease FROM diseases';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute(); // Execute the query

        // Fetch all results as objects
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $categories;
    }

    public function getAllBarangaysGeom()
    {
        // Prepare the SQL query to retrieve barangay names and their geom data
        $sql = 'SELECT barangay_name, geom, municipality_name FROM barangays ORDER BY barangay_name ASC'; // Adjust the table and fields as needed

        // Prepare the statement
        $stmt = $this->db->conn->prepare($sql);

        // Execute the query
        $stmt->execute();

        // Fetch all rows as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode the 'geom' field from JSON to an array, if necessary
        foreach ($data as &$row) {
            $row['geom'] = json_decode($row['geom']);
        }

        // Return the data with barangay name, municipality name, and geometry
        return $data;
    }

    public function addCases($data)
    {
        // Check if 'geometry' is empty or missing
        if (empty($data['geometry'])) {
            return -1; // Geometry is required but missing
        }

        // Validate GeoJSON format
        $geometry = json_decode($data['geometry']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If decoding fails, it's not a valid GeoJSON
            return 2; // Invalid GeoJSON
        }

        // SQL query to insert new municipality data into the database
        $sql = "INSERT INTO cases (patients_id, municipality_name, barangay_name, street, name_of_disease, geom) 
                VALUES (:patients_id, :municipality_name, :barangay_name, :street, :name_of_disease, :geom)";

        $stmt = $this->db->conn->prepare($sql);

        // Execute query with bound values
        $r = $stmt->execute([
            ':patients_id' => $data['patients_id'],
            ':municipality_name' => $data['municipality_name'],
            ':barangay_name' => $data['barangay_name'],
            ':street' => $data['street'],
            ':name_of_disease' => $data['name_of_disease'],
            ':geom' => $data['geometry'] // Pass the GeoJSON string directly
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function getACase($id)
    {
        // Prepare the SQL query to retrieve a single applicant by ID
        $sql = 'SELECT * FROM cases WHERE id = :id LIMIT 1';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
        $stmt->execute(); // Execute the query

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return $data;
    }

    public function getDistinctMunicipalitiesCases()
    {
        $stmt = $this->db->conn->prepare("SELECT DISTINCT municipality_name FROM cases ORDER BY municipality_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDistinctDiseasesCases()
    {
        $stmt = $this->db->conn->prepare("SELECT DISTINCT name_of_disease FROM cases ORDER BY name_of_disease ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDistinctDiseases()
    {
        $stmt = $this->db->conn->prepare("SELECT DISTINCT name_of_disease FROM diseases ORDER BY name_of_disease ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientsByDiseaseCasesWithDays($disease = '', $dateRange = '')
    {
        // Start with a base SQL query
        $sql = "SELECT 
                    id, 
                    patients_id,  
                    municipality_name,
                    barangay_name,
                    street,
                    name_of_disease,
                    geom,
                    created_at,
                    updated_at
                FROM cases
                WHERE 1=1"; // Ensures the WHERE clause is always valid

        // Initialize parameters array
        $params = [];

        // Add condition for disease if specified
        if (!empty($disease)) {
            $sql .= " AND name_of_disease = :disease";
            $params['disease'] = $disease;
        }

        // Add condition for date range if specified
        if ($dateRange === 'last_7_days') {
            $sql .= " AND created_at >= :fromDate";
            $params['fromDate'] = date('Y-m-d H:i:s', strtotime('-7 days'));
        } elseif ($dateRange === 'all') {
            // If the date range is 'all', we don't add any date filter.
            // This could be optional depending on your business logic.
        }

        // Prepare and execute the statement
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Return results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchBarangayAndCases($disease = '', $fromDate = '')
    {
        // Initialize SQL query with basic structure
        $sql = "SELECT 
                    b.barangay_name, 
                    b.municipality_name, 
                    b.geom, 
                    c.name_of_disease, 
                    COUNT(c.patients_id) AS case_count
                FROM 
                    barangays b
                LEFT JOIN 
                    cases c ON b.barangay_name = c.barangay_name AND b.municipality_name = c.municipality_name";

        // Initialize parameters array
        $params = [];

        // Add condition for disease if specified
        if (!empty($disease)) {
            $sql .= " AND c.name_of_disease = :disease";
            $params['disease'] = $disease;
        }

        // Add condition for date range if specified
        if ($fromDate === 'last_7_days') {
            $sql .= " AND c.created_at >= :fromDate";
            $params['fromDate'] = date('Y-m-d H:i:s', strtotime('-7 days'));
        } elseif ($fromDate === 'all') {
            // No date filter added if 'all' is selected
        }

        // Group by barangay and municipality to ensure we get counts even for barangays with no cases
        $sql .= " GROUP BY 
            b.barangay_name, b.municipality_name, b.geom, c.name_of_disease";

        // Prepare and execute the SQL statement
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params); // Bind parameters when executing
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array for the resulting data
        $barangays = [];

        // Create a map to store case counts per barangay and municipality
        $barangay_case_map = [];
        if ($results) {
            foreach ($results as $row) {
                // Use both barangay_name and municipality_name as the key
                $barangay_key = $row['barangay_name'] . '-' . $row['municipality_name'];

                // Decode geom if it's in JSON format
                $geom_data = json_decode($row['geom'], true);

                // Check if geom_data is valid, if not, skip this entry
                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue; // or handle the error as needed
                }

                // Store case count for each barangay, municipality, and disease combination
                $barangay_case_map[$barangay_key][$row['name_of_disease']] = (int) $row['case_count'];
            }
        }

        // Now, make sure all barangays are included with a case count of 0 for diseases with no cases
        $sql_all_barangays = "SELECT barangay_name, municipality_name, geom FROM barangays";
        $stmt = $this->db->conn->prepare($sql_all_barangays);
        $stmt->execute();
        $all_barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through all barangays and create the final result
        foreach ($all_barangays as $barangay) {
            // Use both barangay_name and municipality_name as the key
            $barangay_key = $barangay['barangay_name'] . '-' . $barangay['municipality_name'];

            // Decode geom if it's in JSON format
            $geom_data = json_decode($barangay['geom'], true);

            // Check if geom_data is valid, if not, skip this entry
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue; // or handle the error as needed
            }

            // Add barangay data, including disease case count (or 0 if no cases for selected disease)
            $barangays[] = [
                'type' => 'Feature',
                'properties' => [
                    'barangay_name' => $barangay['barangay_name'],
                    'municipality_name' => $barangay['municipality_name'],
                    'name_of_disease' => $disease,
                    'case_count' => isset($barangay_case_map[$barangay_key][$disease]) ? $barangay_case_map[$barangay_key][$disease] : 0,
                ],
                'geometry' => [
                    'type' => 'Polygon', // Adjust to 'MultiPolygon' if applicable
                    'coordinates' => $geom_data
                ]
            ];
        }

        // Return the filtered data
        return $barangays;
    }

    // Function to get patients by disease and optionally by municipality
    public function getPatientsByDiseaseCases($disease = '', $municipality = '', $fromDate = '', $toDate = '')
    {
        $sql = "SELECT 
                    id, 
                    patients_id,  
                    municipality_name,
                    barangay_name,
                    street,
                    name_of_disease,
                    geom,
                    created_at,
                    updated_at
                FROM cases
                WHERE 1=1"; // Ensures the WHERE clause is always valid

        // Initialize parameters array
        $params = [];

        // Add condition for disease if specified
        if (!empty($disease)) {
            $sql .= " AND name_of_disease = :disease";
            $params['disease'] = $disease;
        }

        // Add condition for municipality if specified
        if (!empty($municipality)) {
            $sql .= " AND municipality_name = :municipality";
            $params['municipality'] = $municipality;
        }

        // Add condition for fromDate if specified
        if (!empty($fromDate)) {
            $sql .= " AND created_at >= :fromDate";
            $params['fromDate'] = $fromDate . ' 00:00:00'; // Ensure start of day for the range
        }

        // Add condition for toDate if specified
        if (!empty($toDate)) {
            $sql .= " AND created_at <= :toDate";
            $params['toDate'] = $toDate . ' 23:59:59'; // Ensure end of day for the range
        }

        // Prepare and execute the statement
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Return results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientsByDiseaseCasesStatistics($disease = '', $municipality = '', $timePeriod = '')
    {
        $sql = "SELECT 
                    id, 
                    patients_id,  
                    municipality_name,
                    barangay_name,
                    street,
                    name_of_disease,
                    geom,
                    created_at,
                    updated_at,
                    DATE(created_at) as case_date  -- Extract the date part of created_at
                FROM cases
                WHERE 1=1"; // Ensures the WHERE clause is always valid

        // Initialize parameters array
        $params = [];

        // Add condition for disease if specified
        if (!empty($disease)) {
            $sql .= " AND name_of_disease = :disease";
            $params['disease'] = $disease;
        }

        // Add condition for municipality if specified
        if (!empty($municipality)) {
            $sql .= " AND municipality_name = :municipality";
            $params['municipality'] = $municipality;
        }

        // Add conditions for time period if specified
        if (!empty($timePeriod)) {
            switch ($timePeriod) {
                case 'daily':
                    // Filter results for the last 7 days
                    $sql .= " AND created_at >= NOW() - INTERVAL 1 YEAR"; // Last 7 days
                    break;
                case 'weekly':
                    // Filter results for the last week
                    $sql .= " AND created_at >= NOW() - INTERVAL 1 WEEK";
                    break;
                case 'monthly':
                    // Filter results for the last month
                    $sql .= " AND created_at >= NOW() - INTERVAL 1 MONTH";
                    break;
                case 'yearly':
                    // Filter results for the last year
                    $sql .= " AND created_at >= NOW() - INTERVAL 1 YEAR";
                    break;
                default:
                    // If no valid time period is selected, ignore the condition
                    break;
            }
        }

        // Prepare and execute the statement
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Return results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientsByMunicipalityCasesStatistics($municipality)
    {
        // Build and execute the query to fetch data
        // Assuming a database connection is available and a `patients` table with a `municipality` column exists
        $query = "SELECT * FROM cases WHERE municipality_name = :municipality";
        $statement = $this->db->conn->prepare($query);  // Assuming `$this->db` is a PDO instance or database connection
        $statement->bindParam(':municipality', $municipality, PDO::PARAM_STR);
        $statement->execute();

        // Fetch and return the results
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getChartDataByTimePeriod($municipality, $time_period, $selectedDisease = null)
    {
        // Fetch patient data based on the municipality filter (all diseases included)
        $patients = $this->getPatientsByMunicipalityCasesStatistics($municipality);

        // Prepare data for the chart
        $chartData = [
            'categories' => [], // Time period labels (e.g., dates, months)
            'values' => [],     // Case counts per disease
            'diseases' => []    // Disease names
        ];

        // Get the start date based on the selected time period
        $startDate = null;
        switch ($time_period) {
            case 'daily':
                // Start from January 1st of the current year
                $startDate = strtotime(date('Y-01-01'));
                break;
            case 'weekly':
                // Start from the first week of the current year (week 1)
                $startDate = strtotime('first day of January ' . date('Y'));
                break;
            case 'monthly':
                // Start from January of the current year
                $startDate = strtotime(date('Y-01-01'));
                break;
            case 'yearly':
                // Start from the year 2014
                $startDate = strtotime('2024-01-01');
                break;
        }

        // Group the data by time period and disease
        foreach ($patients as $patient) {
            // If a specific disease is selected, filter patients by disease
            if ($selectedDisease && $patient['name_of_disease'] != $selectedDisease) {
                continue;
            }

            // Extract the date part (day/week/month/year) based on the selected time period
            $date = strtotime($patient['created_at']);

            // Only process data starting from the start date
            if ($date < $startDate) {
                continue;
            }

            switch ($time_period) {
                case 'daily':
                    $category = date('Y-m-d', $date);  // Full date (day)
                    break;
                case 'weekly':
                    $category = date('W', $date);  // Week number of the year
                    break;
                case 'monthly':
                    $category = date('F Y', $date);  // Month and year
                    break;
                case 'yearly':
                    $category = date('Y', $date);  // Year
                    break;
                default:
                    $category = date('Y-m-d', $date);  // Default to full date (daily)
                    break;
            }

            // Add the category if not already present
            if (!in_array($category, $chartData['categories'])) {
                $chartData['categories'][] = $category;
                $chartData['values'][] = [];  // Initialize an empty array for cases for this category
            }

            // Increment the case count for the corresponding disease in this category
            $index = array_search($category, $chartData['categories']);
            $disease = $patient['name_of_disease']; // Assuming 'name_of_disease' field contains the disease name

            // Initialize disease count if not set
            if (!isset($chartData['values'][$index][$disease])) {
                $chartData['values'][$index][$disease] = 0;
            }
            $chartData['values'][$index][$disease]++;

            // Track diseases
            if (!in_array($disease, $chartData['diseases'])) {
                $chartData['diseases'][] = $disease;
            }
        }

        return $chartData;
    }

    // Example method in Functions class
    public function getNumOfCases($disease, $municipality, $fromDate = '', $toDate = '')
    {
        // Build the query with the necessary conditions
        $query = "SELECT COUNT(*) as num_cases FROM cases WHERE 1=1";

        // Add condition for disease if specified
        if (!empty($disease)) {
            $query .= " AND name_of_disease = :disease";
        }

        // Add condition for municipality if specified
        if (!empty($municipality)) {
            $query .= " AND municipality_name = :municipality";
        }

        // Add condition for fromDate if specified
        if (!empty($fromDate)) {
            $query .= " AND created_at >= :fromDate";
        }

        // Add condition for toDate if specified
        if (!empty($toDate)) {
            $query .= " AND created_at <= :toDate";
        }

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($query);

        // Bind parameters
        if (!empty($disease)) {
            $stmt->bindValue(':disease', $disease);
        }

        if (!empty($municipality)) {
            $stmt->bindValue(':municipality', $municipality);
        }

        if (!empty($fromDate)) {
            $stmt->bindValue(':fromDate', $fromDate . ' 00:00:00'); // Ensure start of day for the range
        }

        if (!empty($toDate)) {
            $stmt->bindValue(':toDate', $toDate . ' 23:59:59'); // Ensure end of day for the range
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['num_cases'] ?? 0; // Return 0 if no result is found
    }


    public function filterCasesByMunicipalityAndDisease($municipality, $disease)
    {
        // Prepare the SQL query to filter cases based on municipality and disease
        $sql = 'SELECT * FROM cases WHERE municipality_name = :municipality AND name_of_disease = :disease';

        // Prepare the statement
        $stmt = $this->db->conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':municipality', $municipality, PDO::PARAM_STR);
        $stmt->bindParam(':disease', $disease, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Fetch all matching cases
        $cases = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Return the list of cases
        return $cases;
    }

    public function getCasesByCoordinates($latitude, $longitude)
    {
        // Convert the coordinates to JSON format
        $geom = json_encode([[floatval($longitude), floatval($latitude)]]);

        // Prepare the SQL query to filter cases based on geom
        $sql = 'SELECT * FROM cases WHERE geom = :geom';

        // Prepare the statement
        $stmt = $this->db->conn->prepare($sql);

        // Bind the geom parameter
        $stmt->bindParam(':geom', $geom, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Fetch all matching cases
        $cases = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Return the list of cases
        return $cases;
    }

    public function createMunicipalityUser($data)
    {
        // Check if email already exists
        $sqlCheckEmail = "SELECT * FROM municipality_users WHERE email = :email";
        $stmtCheckEmail = $this->db->conn->prepare($sqlCheckEmail);
        $stmtCheckEmail->execute([':email' => $data['email']]);

        if ($stmtCheckEmail->rowCount() > 0) {
            return -1; // Email already exists
        }

        // Check if password and confirm password match
        if ($data['password'] !== $data['confirmpassword']) {
            return -2; // Passwords do not match
        }

        // Check password complexity
        if (
            !preg_match('/[A-Z]/', $data['password']) ||
            !preg_match('/[a-z]/', $data['password']) ||
            !preg_match('/[0-9]/', $data['password']) ||
            !preg_match('/[\W_]/', $data['password']) ||
            strlen($data['password']) < 8
        ) {
            return -3; // Password does not meet complexity requirements
        }

        // Handling valid ID upload
        $valid_id = [];
        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "assets/images/uploads/valid_id/"; // Directory to store uploaded images
            $fileName = basename($_FILES["valid_id"]["name"]);
            $targetFile = $targetDir . $fileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["valid_id"]["tmp_name"], $targetFile)) {
                $valid_id[] = $targetFile; // Add file path to the valid_id array
            } else {
                echo "Sorry, there was an error uploading the file: " . $fileName;
            }
        }

        // Store file paths as a JSON array in the 'valid_id' field
        $valid_idJSON = json_encode($valid_id);

        // Auto-capitalize the first letter of names and street
        $firstName = ucfirst(strtolower($data['first_name']));
        $middleName = ucfirst(strtolower($data['middle_name']));
        $lastName = ucfirst(strtolower($data['last_name']));
        $street = ucfirst(strtolower($data['street']));

        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert new user data into the database
        $sql = "INSERT INTO municipality_users (first_name, middle_name, last_name, suffix, province, municipality, barangay, street, email, contact_number, valid_id_path, password) 
                VALUES (:first_name, :middle_name, :last_name, :suffix, :province, :municipality, :barangay, :street, :email, :contact_number, :valid_id_path, :password)";

        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([
            ':first_name' => $firstName,
            ':middle_name' => $middleName,
            ':last_name' => $lastName,
            ':suffix' => $data['suffix'],
            ':province' => $data['province'],
            ':municipality' => $data['municipality'],
            ':barangay' => $data['barangay'],
            ':street' => $street,
            ':email' => $data['email'],
            ':contact_number' => $data['contact_number'],
            ':valid_id_path' => $valid_idJSON,
            ':password' => $hashedPassword
        ]);

        if ($r) {
            return 1; // Success
        } else {
            return 0; // Failure
        }
    }

    public function getAllMunicipalityUsers()
    {
        // Prepare the SQL query to retrieve all diseases
        $sql = 'SELECT * FROM municipality_users ORDER BY municipality ASC';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute(); // Execute the query

        // Fetch all rows as objects
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Return the fetched data
        return $data;
    }

    public function getAMunicipalityUser($id)
    {
        // Prepare the SQL query to retrieve a single applicant by ID
        $sql = 'SELECT * FROM municipality_users WHERE id = :id LIMIT 1';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
        $stmt->execute(); // Execute the query

        $data = $stmt->fetch(PDO::FETCH_OBJ);

        return $data;
    }

    public function deleteMunicipalityUser($id)
    {
        $sql = 'DELETE FROM municipality_users WHERE id=:id';
        $stmt = $this->db->conn->prepare($sql);
        $r = $stmt->execute([':id' => $id]);
        if ($r) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getMunicipalityByEmail($email)
    {
        // Prepare the SQL query to retrieve the municipality based on the email
        $sql = 'SELECT municipality FROM municipality_users WHERE email = :email';
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);  // Bind the email parameter
        $stmt->execute(); // Execute the query

        // Fetch the result
        $municipality = $stmt->fetchColumn();

        // Return the fetched municipality, or an empty string if not found
        return $municipality ? $municipality : '';
    }

    public function authenticateMunicipalityUser($email, $password)
    {
        // Query to retrieve the hashed password associated with the provided email
        $sql = "SELECT * FROM municipality_users WHERE email = :email";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        // Check if the user exists
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashed_password = $row['password'];

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                return 1; // Password is correct
            } else {
                return 2; // Incorrect password
            }
        } else {
            return 3; // User does not exist
        }
    }


    public function getFilteredPatientsByMunicipality($disease = '', $municipality = '')
    {
        // Start with basic query
        $sql = "SELECT * FROM cases WHERE 1";
        $params = [];

        // Add condition for disease if selected and it's not "All"
        if ($disease !== '' && $disease !== 'All') {
            $sql .= " AND name_of_disease = :disease";
            $params[':disease'] = $disease;
        }

        // Add condition for municipality if selected
        if ($municipality !== '') {
            $sql .= " AND municipality_name = :municipality";
            $params[':municipality'] = $municipality;
        }

        // Optionally, add ordering (e.g., by name or date created)
        $sql .= " ORDER BY barangay_name ASC";

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Fetch and return the results
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $patients; // Return an array of patients, or an empty array if no results
    }

    public function getFilteredBarangaysByMunicipality($municipality = '')
    {
        // Start with basic query
        $sql = "SELECT * FROM barangays WHERE 1";
        $params = [];

        // Add condition for municipality if selected
        if ($municipality !== '') {
            $sql .= " AND municipality_name = :municipality";
            $params[':municipality'] = $municipality;
        }

        // Optionally, add ordering (e.g., by barangay name or population)
        $sql .= " ORDER BY barangay_name ASC";

        // Prepare and execute the query
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($params);

        // Fetch and return the results
        $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $barangays; // Return an array of barangays, or an empty array if no results
    }

    public function getBarangaysData()
    {
        // SQL query to fetch barangay data including the geom field
        $sql = 'SELECT barangay_name, municipality_name, no_of_population, geom FROM barangays';

        // Prepare the SQL statement
        $stmt = $this->db->conn->prepare($sql);

        // Execute the statement
        $stmt->execute();

        // Fetch all the results as objects (each record as an object)
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Return the data as an array of objects
        return $data;
    }

}
?>