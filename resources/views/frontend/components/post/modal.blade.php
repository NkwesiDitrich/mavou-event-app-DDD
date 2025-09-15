<div class="container">
     <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title">Join this Event: {{ $post->getTitle()->getValue() }}</h4>
            </div>
            <div class="modal-body">
                <!-- Display Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Registration Form -->
                <form id="registrationForm" action="{{ url('/event-registration') }}" method="POST">
                    @csrf
                    <!-- Name input -->
                    <div class="form-group col-md-12">
                        <label class="form-label" for="name">Name * </label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Name" 
                               value="{{ old('name') }}" required/>
                        <input type="hidden" id="event_id" name="event_id" value="{{ $post->getId() }}"/>
                        <input type="hidden" id="user_id" name="user_id" value="{{ $post->getUserId() }}"/>
                        
                        <label class="form-label" for="mobile">Mobile</label>
                        <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Mobile" 
                               value="{{ old('mobile') }}" />
                        
                        <label class="form-label" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" 
                               value="{{ old('email') }}"/>
                        <!-- Email validation message container -->
                        <div id="emailValidationMessage" class="text-danger mt-1" style="display: none;"></div>
                        
                        <label class="form-label" for="remarks">Remarks</label>
                        <textarea id="remarks" name="remarks" rows="2" class="form-control" placeholder="Remarks">{{ old('remarks') }}</textarea>
                    </div>
                    <!-- Submit button -->
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-block">
                        <span id="submitText">Save</span>
                        <span id="submitSpinner" class="spinner-border spinner-border-sm ml-2" style="display: none;" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const emailInput = document.getElementById('email');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    const emailValidationMessage = document.getElementById('emailValidationMessage');
    const eventId = document.getElementById('event_id').value;
    
    let emailCheckTimeout;
    let isEmailValid = true;

    // Real-time email validation
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(emailCheckTimeout);
        
        // Hide validation message initially
        emailValidationMessage.style.display = 'none';
        emailValidationMessage.textContent = '';
        
        // Reset email validity
        isEmailValid = true;
        updateSubmitButton();
        
        // Only check if email is not empty and is valid format
        if (email && isValidEmailFormat(email)) {
            // Debounce the email check (wait 500ms after user stops typing)
            emailCheckTimeout = setTimeout(() => {
                checkEmailAvailability(email, eventId);
            }, 500);
        }
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        // Prevent submission if email is not valid
        if (!isEmailValid) {
            e.preventDefault();
            showEmailError('Please use a different email address.');
            return false;
        }
        
        // Show loading state
        setLoadingState(true);
    });

    // Check email availability via AJAX
    function checkEmailAvailability(email, eventId) {
        // Create a simple check by making a HEAD request to see if we can detect duplicates
        // Since we don't have a dedicated API endpoint, we'll rely on server-side validation
        // This is a placeholder for potential future AJAX implementation
        
        // For now, we'll rely on server-side validation in the HomeController
        // The real validation happens when the form is submitted
    }

    // Validate email format
    function isValidEmailFormat(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Show email error message
    function showEmailError(message) {
        emailValidationMessage.textContent = message;
        emailValidationMessage.style.display = 'block';
        isEmailValid = false;
        updateSubmitButton();
    }

    // Update submit button state
    function updateSubmitButton() {
        if (isEmailValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    // Set loading state
    function setLoadingState(loading) {
        if (loading) {
            submitBtn.disabled = true;
            submitText.textContent = 'Registering...';
            submitSpinner.style.display = 'inline-block';
        } else {
            submitBtn.disabled = false;
            submitText.textContent = 'Save';
            submitSpinner.style.display = 'none';
        }
    }

    // Auto-show modal if there are validation errors (user was redirected back)
    @if(session('error') || old('name'))
        $('#myModal').modal('show');
    @endif

    // Auto-hide success message after 5 seconds
    @if(session('success'))
        setTimeout(function() {
            $('.alert-success').fadeOut();
        }, 5000);
    @endif
});
</script>

<style>
.alert {
    margin-bottom: 20px;
}

.alert i {
    margin-right: 8px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.text-danger {
    font-size: 0.875em;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: fadeIn 0.3s ease-in-out;
}
</style>
