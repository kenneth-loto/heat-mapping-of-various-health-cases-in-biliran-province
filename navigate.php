<?php
include 'function.php';
include_once 'session.php';
Session::init();

$function = new Functions();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-create-municipality-user'])) {
        // Call the createApplicant function and pass the form data
        $flag = $function->createMunicipalityUser($_POST);

        if ($flag == 1) {
            // Success message and redirect
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Your access request has been successfully sent! <br> Please check your email for a response. </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 5000); // 5000 milliseconds = 2 seconds
            </script>");

            unset($_SESSION['form_data']);

            header("Location: municipality_user/municipality_user_login.php"); // Redirect to login page
            exit;
        } else {
            // Store form data in session for repopulation
            $_SESSION['form_data'] = $_POST;

            // Handle error cases
            if ($flag == -1) {
                // Email already exists
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Email already exists </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } elseif ($flag == -2) {
                // Passwords do not match
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Passwords do not match </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } elseif ($flag == -3) {
                // Password complexity issue
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Password does not meet complexity requirements </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } else {
                // General error
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Something went wrong </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            }

            // Redirect back to the applicant creation form
            header("Location: municipality_user/municipality_user_create_account.php");
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-add-disease'])) {
        $data = $_POST; // Get all POST data

        // Call the addDisease function and store the result
        $result = $function->addDisease($data); // Ensure you call the correct function

        // Check the result returned from addDisease
        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Disease added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: admin/diseases.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // General error occurred during the insert process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> There was an error adding the disease. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the form
            header("Location: admin/add_disease.php"); 
            exit;
        } elseif ($result === -1) {
            // Error: Disease name already exists
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> The disease name already exists in the database. Please use a different name.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the form
            header("Location: admin/add_disease.php");
            exit;
        } elseif ($result === -2) {
            // Error: Category name already exists
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> The category name already exists. Please use a different category name.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the form
            header("Location: admin/add_disease.php");
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-update-disease'])) {
        $data = $_POST; // Get all POST data
        $id = $_POST['id'];
    
        // Call the updateDisease function and store the result
        $result = $function->updateDisease($id, $data); // Ensure you call the correct function
    
        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'>
                <center> Disease updated successfully!</center>
            </div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
            </script>");
    
            unset($_SESSION['form_data']);
            header("Location: admin/diseases.php"); // Redirect to the diseases dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the update
            $_SESSION['form_data'] = $_POST;
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> There was an error updating the disease. Please try again.</center>
            </div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 3 seconds
            </script>");
    
            header("Location: admin/update_disease.php?id=" . $id); // Redirect back to the update disease form
            exit;
        } elseif ($result === -1) {
            // Error: Disease name already exists
            $_SESSION['form_data'] = $_POST;
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> The disease name already exists. Please choose a different name.</center>
            </div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 3 seconds
            </script>");
    
            header("Location: admin/update_disease.php?id=" . $id); // Redirect back to the update disease form
            exit;
        } elseif ($result === -2) {
            // Error: Category name already exists
            $_SESSION['form_data'] = $_POST;
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> The selected category already exists. Please choose a different category.</center>
            </div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 3 seconds
            </script>");
    
            header("Location: admin/update_disease.php?id=" . $id); // Redirect back to the update disease form
            exit;
        }
    }    

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['btn-delete-disease'])){		
	
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $flag = $function->deleteDisease($id);
            if ($flag == 1) {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Disease deleted successfully! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Something went wrong! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Invalid Request! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
        header("Location: admin/diseases.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-add-patient'])) {
        $data = $_POST; // Get all POST data

        // Call the insertApprovedRequest function and store the result
        $result = $function->addPatient($data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Patient added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: admin/disease_data.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error adding the patient. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: admin/add_patient.php"); // Change to your review form URL
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-update-patient'])) {
        $data = $_POST; // Get all POST data
        $id = $_POST['id'];

        // Call the insertApprovedRequest function and store the result
        $result = $function->updatePatient($id, $data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Patient data updated successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: admin/disease_data.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST;
            $_SESSION['id'] = $id; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error updating the patients data. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: admin/update_patient.php"); // Change to your review form URL
            exit;
        }
    }

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['btn-delete-patient'])){		
	
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $flag = $function->deletePatient($id);
            if ($flag == 1) {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Patient deleted successfully! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Something went wrong! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Invalid Request! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
        header("Location: admin/disease_data.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-add-municipality'])) {
        $data = $_POST; // Get all POST data
    
        // Call the addMunicipality function and store the result
        $result = $function->addMunicipality($data); // Ensure you call the correct function
    
        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Municipality added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            unset($_SESSION['form_data']);
            header("Location: admin/municipalities.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Invalid GeoJSON error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Invalid GeoJSON format. Please check the map data.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_municipality.php"); // Change to your form URL
            exit;
        } elseif ($result === -1) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_municipality.php"); // Change to your form URL
            exit;
        } elseif ($result === -2) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Invalid GeoJSON Format. Please try again. </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_municipality.php"); // Change to your form URL
            exit;
        }
    }    

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-update-municipality'])) {
        $data = $_POST; // Get all POST data
        $id = $_POST['id'];
    
        // Call the updateMunicipality function and store the result
        $result = $function->updateMunicipality($id, $data); // Ensure you call the correct function
    
        // Check the result and handle accordingly
        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'>
                <center> Municipality data updated successfully!</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 2 seconds
                </script>");
    
            unset($_SESSION['form_data']);
            header("Location: admin/municipalities.php"); // Redirect to the municipalities dashboard
            exit;
        } elseif ($result === 0) {
            // General error occurred (e.g., invalid GeoJSON)
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> There was an error updating the municipality data. Please try again.</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 2 seconds
                </script>");
    
            // Redirect back to the update municipality form
            header("Location: admin/update_municipality.php?id=" . $id); // Change to your review form URL
            exit;
        } elseif ($result === -1) {
            // Error: Geometry not provided
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 2 seconds
                </script>");
    
            // Redirect back to the update municipality form
            header("Location: admin/update_municipality.php?id=" . $id); // Change to your review form URL
            exit;
        } elseif ($result === -2) {
            // Error: Geometry not provided
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> Invalid GeoJSON Format. Please try again.</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 2 seconds
                </script>");
    
            // Redirect back to the update municipality form
            header("Location: admin/update_municipality.php?id=" . $id); // Change to your review form URL
            exit;
        }
    }
    

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['btn-delete-municipality'])){		
	
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $flag = $function->deleteMunicipality($id);
            if ($flag == 1) {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Municipality deleted successfully! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Something went wrong! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Invalid Request! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
        header("Location: admin/municipalities.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-add-barangay'])) {
        $data = $_POST; // Get all POST data

        // Call the insertApprovedRequest function and store the result
        $result = $function->addBarangay($data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Barangay added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: admin/barangays.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error adding the barangay. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: admin/add_barangay.php"); // Change to your review form URL
            exit;
        } elseif ($result === -1) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_barangay.php"); // Change to your form URL
            exit;
        } elseif ($result === -2) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Invalid GeoJSON Format. Please try again. </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_barangay.php"); // Change to your form URL
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-update-barangay'])) {
        $data = $_POST; // Get all POST data
        $id = $_POST['id'];

        // Call the insertApprovedRequest function and store the result
        $result = $function->updateBarangay($id, $data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Barangay data updated successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: admin/barangays.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error updating the barangay data. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: admin/update_barangay.php?id=" . $id); // Change to your review form URL
            exit;
        } elseif ($result === -1) {
            // Error: Geometry not provided
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 2 seconds
                </script>");
    
            // Redirect back to the update municipality form
            header("Location: admin/update_barangay.php?id=" . $id); // Change to your review form URL
            exit;
        } elseif ($result === -2) {
            // Error: Geometry not provided
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'>
                <center> Invalid GeoJSON Format. Please try again.</center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // Show message for 2 seconds
                </script>");
    
            // Redirect back to the update municipality form
            header("Location: admin/update_barangay.php?id=" . $id); // Change to your review form URL
            exit;
        }
    }

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['btn-delete-barangay'])){		
	
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $flag = $function->deleteBarangay($id);
            if ($flag == 1) {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Barangay deleted successfully! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Something went wrong! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Invalid Request! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
        header("Location: admin/barangays.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-create-admin'])) {
        // Call the createApplicant function and pass the form data
        $flag = $function->createAdmin($_POST);
    
        if ($flag == 1) {
            // Success message and redirect
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Registration Complete! <br> Please check your email for validation.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 2000 milliseconds = 2 seconds
            </script>");
    
            header("Location: admin/admin_login.php"); // Redirect to login page
            exit;
        } else {
            // Store form data in session for repopulation
            $_SESSION['form_data'] = $_POST;
    
            // Handle error cases
            if ($flag == -1) {
                // Email already exists
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> Email already exists! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } elseif ($flag == -2) {
                // Passwords do not match
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> Passwords do not match! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } elseif ($flag == -3) {
                // Password complexity issue
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> Password does not meet complexity requirements! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } else {
                // General error
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> Something went wrong! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            }
    
            // Redirect back to the applicant creation form
            header("Location: admin/admin_create_account.php");
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-login-admin'])) {
        $email = $_POST['email']; // Get email from POST
        $password = $_POST['password']; // Get password from POST
        
        $flag = $function->authenticateAdmin($email, $password);
    
        if ($flag === 1) {
            // Password is correct, set session variables or redirect
            $_SESSION['email'] = $email;
            header("Location: admin/index.php"); // Updated to a more specific dashboard page
            exit;
        } else {
            $_SESSION['form_data'] = $_POST;
            // Set error message based on login failure
            $msg = "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> Invalid username or password! </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 2000 milliseconds = 2 seconds
            </script>";
            Session::set("msg", $msg);
    
            // Redirect back to the login page
            header("Location: admin/admin_login.php"); // Change to your updated login page URL
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-add-cases'])) {
        $data = $_POST; // Get all POST data

        // Call the insertApprovedRequest function and store the result
        $result = $function->addCases($data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Patient added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: admin/disease_data.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error adding the patient. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: admin/add_patient.php"); // Change to your review form URL
            exit;
        } elseif ($result === -1) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_patient.php"); // Change to your form URL
            exit;
        } elseif ($result === -2) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Invalid GeoJSON Format. Please try again. </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: admin/add_patient.php"); // Change to your form URL
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-create-municipality_user'])) {
        // Call the createApplicant function and pass the form data
        $flag = $function->createMunicipalityUser($_POST);
    
        if ($flag == 1) {
            // Success message and redirect
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Account created successfully </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 2000 milliseconds = 2 seconds
            </script>");
    
            header("Location: admin/municipality_users.php"); // Redirect to login page
            exit;
        } else {
            // Store form data in session for repopulation
            $_SESSION['form_data'] = $_POST;
    
            // Handle error cases
            if ($flag == -1) {
                // Email already exists
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Email already exists </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } elseif ($flag == -2) {
                // Passwords do not match
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Passwords do not match </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } elseif ($flag == -3) {
                // Password complexity issue
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Password does not meet complexity requirements </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            } else {
                // General error
                Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Something went wrong </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000);</script>");
            }
    
            // Redirect back to the applicant creation form
            header("Location: admin/add_municipality_user.php");
            exit;
        }
    }

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['btn-delete-municipality-user'])){		
	
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $flag = $function->deleteMunicipalityUser($id);
            if ($flag == 1) {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Municipality User deleted successfully! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Something went wrong! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
            } else {
                Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center> Invalid Request! </center></div><br><script>
                setTimeout(function() {
                    document.getElementById('error-msg').style.display = 'none';
                }, 2000); // 1000 milliseconds = 1 second
                </script>");
            }
        header("Location: admin/municipality_users.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-login-municipality-user'])) {
    $email = $_POST['email']; // Get email from POST
    $password = $_POST['password']; // Get password from POST
    
    $flag = $function->authenticateMunicipalityUser($email, $password);

    if ($flag === 1) {
        // Password is correct and account is approved
        $_SESSION['email'] = $email;
        header("Location: municipality_user/header.php"); // Redirect to dashboard page
        exit;
    } else {
        $_SESSION['form_data'] = $_POST; // Save form data for repopulating the form
        // Set error message based on login failure
        if ($flag === 2) {
            // Incorrect email or password for security reasons
            $msg = "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Incorrect email or password ! </center></div><br>";
        } elseif ($flag === 4) {
            // Account is not approved
            $msg = "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Accounts not Approved ! </center></div><br>";
        } else {
            // User does not exist
            $msg = "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> User does not exist ! </center></div><br>";
        }
        
        // Display the error message for a set duration
        $msg .= "<script>
        setTimeout(function() {
            document.getElementById('error-msg').style.display = 'none';
        }, 2000); // 2000 milliseconds = 2 seconds
        </script>";

        Session::set("msg", $msg);

        // Redirect back to the login page
        header("Location: municipality_user/municipality_user_login.php"); // Change to your updated login page URL
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-municipality-user-add-barangay'])) {
        $data = $_POST; // Get all POST data

        // Call the insertApprovedRequest function and store the result
        $result = $function->addBarangay($data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Barangay added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: municipality_user/barangays.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error adding the barangay. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: municipality_user/add_barangay.php"); // Change to your review form URL
            exit;
        } elseif ($result === -1) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: municipality_user/add_barangay.php"); // Change to your form URL
            exit;
        } elseif ($result === -2) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Invalid GeoJSON Format. Please try again. </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: municipality_user/add_barangay.php"); // Change to your form URL
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-municipality-user-add-cases'])) {
        $data = $_POST; // Get all POST data

        // Call the insertApprovedRequest function and store the result
        $result = $function->addCases($data); // Ensure you call the correct function

        if ($result === 1) {
            // Success message and redirect for approval
            Session::set("msg", "<div id='error-msg' style='background-color: #9fdf9f; color:black; border: solid #9fdf9f 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-check'></i> Patient added successfully!</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // 1000 milliseconds = 1 second
            </script>");

            unset($_SESSION['form_data']);
            header("Location: municipality_user/disease_data.php"); // Redirect to the dashboard
            exit;
        } elseif ($result === 0) {
            // Error occurred during the approval process
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user

            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center><i class='fa fa-warning'></i> There was an error adding the patient. Please try again.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 3 seconds
            </script>");

            // Redirect back to the review form
            header("Location: municipality_user/add_patient.php"); // Change to your review form URL
            exit;
        } elseif ($result === -1) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Geometry (map coordinates) is required. Please provide the map data.</center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: municipality_user/add_patient.php"); // Change to your form URL
            exit;
        } elseif ($result === -2) {
            // Missing geometry error
            $_SESSION['form_data'] = $_POST; // Preserve form data for the user
    
            Session::set("msg", "<div id='error-msg' style='background-color: #ED4337; color:white; border: solid #ED4337 1px; border-radius: 5px; padding: 10px;'><center> Invalid GeoJSON Format. Please try again. </center></div><br><script>
            setTimeout(function() {
                document.getElementById('error-msg').style.display = 'none';
            }, 2000); // Show message for 2 seconds
            </script>");
    
            // Redirect back to the add municipality form
            header("Location: municipality_user/add_patient.php"); // Change to your form URL
            exit;
        }
    }
}
?>