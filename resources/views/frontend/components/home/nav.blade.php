<!-- Begin Nav
================================================== -->
<nav class="navbar navbar-toggleable-md navbar-light bg-white fixed-top mediumnavigation">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="container">
        <!-- Begin Logo -->
        <a class="navbar-brand" href="{{ url('/') }}">
        <span class="brand-logo">M</span>
        <span class="brand-text">Mavou Consulting</span>
        </a>
        <!-- End Logo -->
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <!-- Begin Menu -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                <a class="nav-link" href="{{ url('/') }}">Events <span class="sr-only">(current)</span></a>
                </li>
            </ul>
            <!-- End Menu -->
        </div>
    </div>
    </nav>
    <!-- End Nav
    ================================================== -->

<!-- Password Modal -->
<div id="passwordModal" class="modal" style="display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog" style="position: relative; top: 50%; transform: translateY(-50%); margin: 0 auto; max-width: 400px;">
        <div class="modal-content" style="background-color: white; border-radius: 5px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="modal-header" style="border-bottom: 1px solid #dee2e6; padding-bottom: 15px; margin-bottom: 15px;">
                <h5 class="modal-title" style="margin: 0; font-weight: bold;">Admin Access</h5>
                <button type="button" class="close" onclick="hidePasswordModal()" style="background: none; border: none; font-size: 24px; float: right; cursor: pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px;">Please enter the admin password to continue:</p>
                <input type="password" id="adminPassword" class="form-control" placeholder="Enter password" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <div id="errorMessage" style="color: red; margin-top: 10px; display: none;">Sorry password incorrect, retry please</div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding-top: 15px; margin-top: 15px; text-align: right;">
                <button type="button" class="btn btn-secondary" onclick="hidePasswordModal()" style="margin-right: 10px; padding: 6px 12px; border: 1px solid #6c757d; background-color: #6c757d; color: white; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="checkPassword()" style="padding: 6px 12px; border: 1px solid #007bff; background-color: #007bff; color: white; border-radius: 4px; cursor: pointer;">Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
.navbar-brand {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 1.3rem;
}

.brand-logo {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: white;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.5rem;
    margin-right: 10px;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.3);
}

.brand-text {
    color: #1e3a8a;
    font-weight: 600;
}

.navbar-nav .nav-link {
    color: #1e3a8a !important;
    font-weight: 500;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #3b82f6 !important;
}
</style>

<script>
function showPasswordModal() {
    document.getElementById('passwordModal').style.display = 'block';
    document.getElementById('adminPassword').focus();
}

function hidePasswordModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('adminPassword').value = '';
    document.getElementById('errorMessage').style.display = 'none';
}

function checkPassword() {
    const password = document.getElementById('adminPassword').value;
    const correctPassword = 'MavouConsulting123';
    
    if (password === correctPassword) {
        // Redirect to login page
        window.location.href = '{{ route("login") }}';
    } else {
        // Show error message
        document.getElementById('errorMessage').style.display = 'block';
        document.getElementById('adminPassword').value = '';
        document.getElementById('adminPassword').focus();
    }
}

// Allow Enter key to submit password
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('adminPassword');
    if (passwordInput) {
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                checkPassword();
            }
        });
    }
});

// Close modal when clicking outside
const modal = document.getElementById('passwordModal');
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            hidePasswordModal();
        }
    });
}
</script>
