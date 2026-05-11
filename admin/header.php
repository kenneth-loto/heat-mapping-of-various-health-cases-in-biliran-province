<?php
session_start(); // Start the session
include_once '../session.php';
Session::init();
include '../function.php';
$function = new Functions();

// Ensure the user is logged in by calling the checkSession method
$function->checkSession(); // Call the session check method

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Heat Mapping of Various Health Cases</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/external.css">
  <link rel="stylesheet" href="../assets/css/headerr.css">

  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</head>

<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
</style>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex justify-content-center align-items-center mt-4">
          <a href="index.php" class="text-nowrap logo-img">
            <img src="../assets/images/logos/logo.png" width="150" alt="" />
          </a>
        </div>
        <div class="text-center mt-3"> <!-- Centering div -->
            <h3 class="fw-semibold">Administrator</h3>
        </div>
        <div>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" style="overflow-y: auto; height: calc(100vh - 220px);">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu text-light">Home</span>
            </li>

            <li class="sidebar-item">
                <a class="btn sidebar-link" href="index.php" aria-expanded="false">
                    <span>
                        <i class="fa-solid fa-sun"></i>
                    </span>
                    <span class="hide-menu fw-bold">Heat Map</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="btn sidebar-link" href="chloropleth_map.php" aria-expanded="false">
                    <span>
                        <i class="fa-solid fa-earth-americas"></i>
                    </span>
                    <span class="hide-menu fw-bold">Chloropleth Map</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="btn sidebar-link" href="statistics.php" aria-expanded="false">
                    <span>
                        <i class="fa-solid fa-chart-pie"></i>
                    </span>
                    <span class="hide-menu fw-bold">Statistics</span>
                </a>
            </li>

            <li class="nav-small-cap mt-4">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu text-light">Diseases</span>
            </li>

            <li class="sidebar-item">
              <a class="btn sidebar-link" href="diseases.php" aria-expanded="false">
                <span>
                  <i class="fa-solid fa-virus"></i>
                </span>
                <span class="hide-menu fw-bold">Types of Diseases</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="btn sidebar-link" href="disease_data.php" aria-expanded="false">
                <span>
                  <i class="fa-solid fa-heartbeat"></i>
                </span>
                <span class="hide-menu fw-bold">Disease Data</span>
              </a>
            </li>

            <li class="nav-small-cap mt-4">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu text-light">Locations</span>
            </li>

            <li class="sidebar-item">
              <a class="btn sidebar-link" href="municipalities.php" aria-expanded="false">
                <span>
                  <i class="fa-solid fa-city"></i>
                </span>
                <span class="hide-menu fw-bold">Municipalities</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="btn sidebar-link" href="barangays.php" aria-expanded="false">
                <span>
                  <i class="fa-solid fa-home"></i>
                </span>
                <span class="hide-menu fw-bold">Barangays</span>
              </a>
            </li>

            <li class="nav-small-cap mt-4">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu text-light">Users</span>
            </li>

            <li class="sidebar-item">
                <a class="btn sidebar-link" href="municipality_users.php" aria-expanded="false">
                    <span>
                        <i class="fa-solid fa-user-check"></i> <!-- New icon for Staff -->
                    </span>
                    <span class="hide-menu fw-bold">Municipality Users</span>
                </a>
            </li>

            <li class="nav-small-cap mt-4">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu text-light">Logout</span>
            </li>

            <li class="sidebar-item mb-4">
              <a class="btn sidebar-link" href="../login.php" aria-expanded="false">
                <span>
                  <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </span>
                <span class="hide-menu fw-bold">Logout</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">             
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-settings fs-6"></i>
                      <p class="mb-0 fs-3">Settings</p>
                    </a>
                    <a href="../login.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->

<div class="container-fluid">
  