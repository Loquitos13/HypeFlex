<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../SignIn.html');
    exit;
} else {

    $adminID = $_SESSION['admin_id'];

}

?>

<input type="hidden" id="adminID" value="<?php echo htmlspecialchars($adminID); ?>">

<!DOCTYPE html>
<html lang="pt-pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print & Go - Settings</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/Dashboard.css">
  <link rel="stylesheet" href="css/Settings.css">
</head>

<style>
  body {
    margin: 0;
    padding: 0;
    overflow: auto;
    align-items: center;
  }

  #menu-mobile {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 80%;
    height: 100%;
    background-color: white;
    transform: translateX(-10%);
    transition: transform 2000ms ease;
    overflow: hidden;
    z-index: 999;
    flex-direction: column-reverse;
    margin-top: 0;
    justify-content: flex-end;
    gap: 20px;
    align-items: center;
    padding-top: 100px;
  }

  .fixed-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: white;
    z-index: 999;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  #menu-mobile.open {
    display: flex;
    transform: translateX(0);
  }

  #menu-mobile a {
    color: #4F46E5;
    text-decoration: none;
    font-size: 1.5rem;
    margin: 15px 0;
  }

  #menu-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1000;
    font-size: 2rem;
    background: none;
    border: none;
    color: black;
    cursor: pointer;
  }

  #logo-header-mobile {
    display: none;
    background: none;
    border: none;
  }

  .alert {
    border-radius: 8px;
    border: none;
    font-weight: 500;
  }

  .alert-success {
    background-color: #d4edda;
    color: #155724;
  }

  .alert-danger {
    background-color: #f8d7da;
    color: #721c24;
  }

  .loading-spinner {
    display: none;
    text-align: center;
    padding: 20px;
  }

  .loading-spinner.show {
    display: block;
  }

  .spinner-border-sm {
    width: 1rem;
    height: 1rem;
  }

  .team-card {
    transition: transform 0.2s;
    cursor: pointer;
  }

  .team-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .team-member-item {
    transition: background-color 0.2s;
  }

  .team-member-item:hover {
    background-color: #f8f9fa;
  }

  @media (max-width: 1200px) {
    body {
      width: 100%;
      padding-top: 70px;
    }

    .header-desktop {
      display: none !important;
    }

    #a-logo-header-mobile {
      display: flex;
      width: max-content;
      justify-content: center;
      padding-top: 20px;
      padding-bottom: 20px;
      width: 100%;
    }

    #logo-header-mobile {
      display: block;
      width: 100px;
      height: auto;
      margin: 0 auto;
    }

    #menu-toggle {
      display: block;
      top: 0;
    }

    #menu-mobile {
      position: fixed;
      height: 100%;
      overflow: hidden;
    }

    #menu-mobile li {
      list-style: none;
    }

    #menu-mobile .social {
      display: flex;
      height: 100%;
      width: 100%;
      align-items: center;
      align-self: flex-end;
      justify-content: center;
    }

    .container1 {
      display: none;
    }

    .left,
    .right {
      width: 100%;
      justify-content: center;
    }

    .left a {
      width: auto;
    }

    #containerHeroe {
      flex-direction: column;
      height: auto;
      padding: 20px 20px;
      width: 100%;
    }

    .esqHeroe,
    .dirHeroe {
      width: 100%;
      text-align: center;
    }

    .dirHeroe img {
      position: static;
      height: auto;
      width: 80%;
    }

    #prodDestaques {
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .containerDestaques {
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;
      gap: 20px;
    }

    .feedback-carousel-container {
      max-width: 90%;
      max-height: 400px;
    }

    #featureSection {
      padding: 5vh;
    }

    #containerFeatures {
      flex-direction: column;
    }

    .featureBox p {
      width: 90%;
    }

    #cta h3 {
      text-align: center;
      font-size: 100%;
    }

    #cta p {
      text-align: center;
      font-size: 90%;
    }

    #containerFooter {
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }
  }
</style>

<body style="background-color: #E5E7EB;">
  <div class="d-flex">
    <?php include '../includes/header-desktop-admin.php'; ?>
    <?php include '../includes/header-mobile-admin.php'; ?>

    <div class="flex-grow-1 p-4" id="main-content">
      <div class="container-fluid">
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
              <div>
                <h1 class="mb-1">Settings</h1>
                <p class="text-muted">Manage your store preferences</p>
              </div>
              <div class="d-flex align-items-center">
                <img src="../../imagens/admin.png" alt="" id="img-admin"
                  style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
                <h6 id="admin_nome" class="mb-0 ms-3">Nome Admin</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Tabs Navigation -->
        <div class="row mb-4">
          <div class="col-12">
            <ul class="nav nav-tabs settings-tabs">
              <li class="nav-item">
                <a class="nav-link active" href="#" data-tab="general">General</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" data-tab="teams">Teams</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" data-tab="members">Team Members</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- General Tab Content -->
        <div id="general-content" class="tab-content">
          <div class="loading-spinner" id="general-loading">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading admin data...</p>
          </div>

          <div class="row gx-4">
            <div class="col-lg-8">
              <div class="card shadow-sm mb-4">
                <div class="card-body">
                  <h5 class="card-title mb-4">Personal Information</h5>

                  <form id="general-form">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="nome_admin" class="form-label">Name</label>
                        <input type="text" class="form-control" id="nome_admin" name="nome_admin" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="email_admin" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email_admin" name="email_admin" required>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="contacto_admin" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="contacto_admin" name="contacto_admin">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="funcao_admin" class="form-label">Function</label>
                        <input type="text" class="form-control" id="funcao_admin" name="funcao_admin">
                      </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-floppy me-2"></i>
                      Update Information
                    </button>
                  </form>
                </div>
              </div>

              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title mb-4">Change Password</h5>

                  <form id="password-form">
                    <div class="mb-3">
                      <label for="current_password" class="form-label">Current Password</label>
                      <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
                      </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-key me-2"></i>
                      Update Password
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title mb-4">Account Status</h5>

                  <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shield-check text-success me-3 fs-4"></i>
                    <div>
                      <p class="mb-0 fw-medium">Account Secure</p>
                      <p class="text-muted small mb-0">All security features enabled</p>
                    </div>
                  </div>

                  <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-check text-primary me-3 fs-4"></i>
                    <div>
                      <p class="mb-0 fw-medium">Profile Complete</p>
                      <p class="text-muted small mb-0">Keep your information updated</p>
                    </div>
                  </div>

                  <div class="d-flex align-items-center">
                    <i class="bi bi-clock-history text-warning me-3 fs-4"></i>
                    <div>
                      <p class="mb-0 fw-medium">Last Login</p>
                      <p class="text-muted small mb-0" id="last-login">Loading...</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Teams Tab Content -->
        <div id="teams-content" class="tab-content" style="display: none;">
          <div class="loading-spinner" id="teams-loading">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading teams...</p>
          </div>

          <div class="row gx-4">
            <div class="col-lg-8">
              <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">Teams</h5>
                  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTeamModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Create New Team
                  </button>
                </div>
                <div class="card-body">
                  <div id="teamsList">
                    <!-- Teams will be loaded here -->
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title mb-4">Team Statistics</h5>
                  <div class="text-center">
                    <div class="row text-center" id="teams-stats">
                      <div class="col-4">
                        <div class="text-primary fw-bold fs-4" id="total-teams">0</div>
                        <small class="text-muted">Total Teams</small>
                      </div>
                      <div class="col-4">
                        <div class="text-success fw-bold fs-4" id="active-teams">0</div>
                        <small class="text-muted">Active</small>
                      </div>
                      <div class="col-4">
                        <div class="text-warning fw-bold fs-4" id="total-team-members">0</div>
                        <small class="text-muted">Members</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Team Members Tab Content -->
        <div id="members-content" class="tab-content" style="display: none;">
          <div class="loading-spinner" id="members-loading">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading team members...</p>
          </div>

          <div class="row gx-4">
            <div class="col-lg-8">
              <div class="card shadow-sm mb-4">
                <div class="card-body">
                  <h5 class="card-title mb-4">Team Members</h5>
                  <div id="teamMembersList">
                    <!-- Team members will be loaded here -->
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="card-title mb-4">Add Team Member</h5>

    <form id="add-member-form">
      <div class="mb-3">
        <label for="member_select" class="form-label">Select User</label>
        <select class="form-select" id="member_select" name="id_cliente" required>
          <option value="">Select a user...</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="team_select" class="form-label">Select Team</label>
        <select class="form-select" id="team_select" name="id_team" required>
          <option value="">Select a team...</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="member_role" class="form-label">Role</label>
        <select class="form-select" id="member_role" name="role" required>
          <option value="member">Member</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-person-plus me-2"></i>
        Add Member
      </button>
    </form>
  </div>
</div>

              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title mb-4">Member Statistics</h5>
                  <div class="text-center">
                    <div class="row text-center" id="member-stats">
                      <div class="col-4">
                        <div class="text-primary fw-bold fs-4" id="total-members">0</div>
                        <small class="text-muted">Total</small>
                      </div>
                      <div class="col-4">
                        <div class="text-success fw-bold fs-4" id="active-members">0</div>
                        <small class="text-muted">Active</small>
                      </div>
                      <div class="col-4">
                        <div class="text-warning fw-bold fs-4" id="admin-members">0</div>
                        <small class="text-muted">Admins</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Create Team Modal -->
  <div class="modal fade" id="createTeamModal" tabindex="-1" aria-labelledby="createTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createTeamModalLabel">Create New Team</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="create-team-form">
            <div class="mb-3">
              <label for="team_name" class="form-label">Team Name *</label>
              <input type="text" class="form-control" id="team_name" name="nome_team" required minlength="3">
              <div class="form-text">Team name must be at least 3 characters long</div>
            </div>
            <div class="mb-3">
              <label for="team_description" class="form-label">Description</label>
              <textarea class="form-control" id="team_description" name="descricao_team" rows="3" placeholder="Optional team description"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" form="create-team-form" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Create Team
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../includes/footer-admin.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Mobile menu toggle
    document.getElementById('menu-toggle').addEventListener('click', function() {
      const mobileMenu = document.getElementById('menu-mobile');
      mobileMenu.classList.toggle('open');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      const mobileMenu = document.getElementById('menu-mobile');
      const menuToggle = document.getElementById('menu-toggle');
      
      if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
        mobileMenu.classList.remove('open');
      }
    });

    // Pass admin ID to JavaScript
    window.adminId = <?php echo $_SESSION['admin_id']; ?>;
  </script>
  <script src="js/Settings.js"></script>
</body>

</html>