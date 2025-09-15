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
                        <!-- Email validation message container - ENHANCED -->
                        <div id="emailValidationMessage" class="text-danger mt-1" style="display: none; font-size: 14px; font-weight: 500;"></div>
                        
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
    let isCheckingEmail = false;

    // ENHANCED: Real-time email validation with immediate feedback
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(emailCheckTimeout);
        
        // Hide validation message initially
        hideEmailError();
        
        // Reset email validity
        isEmailValid = true;
        updateSubmitButton();
        
        // Only check if email is not empty and is valid format
        if (email && isValidEmailFormat(email)) {
            // Debounce the email check (wait 800ms after user stops typing)
            emailCheckTimeout = setTimeout(() => {
                checkEmailAvailability(email, eventId);
            }, 800);
        }
    });

    // ENHANCED: Form submission handler with immediate validation
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default first
        
        const email = emailInput.value.trim();
        const eventId = document.getElementById('event_id').value;
        
        // If email is empty, allow submission
        if (!email) {
            submitForm();
            return;
        }
        
        // If email format is invalid, show error
        if (!isValidEmailFormat(email)) {
            showEmailError('Please enter a valid email address');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        // Check email availability immediately before submission
        checkEmailAvailabilityForSubmission(email, eventId);
    });

    // ENHANCED: Check email availability via AJAX for real-time feedback
    function checkEmailAvailability(email, eventId) {
        if (isCheckingEmail) return; // Prevent multiple simultaneous requests
        
        isCheckingEmail = true;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value || '';
        
        fetch('/check-email-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                email: email,
                event_id: eventId
            })
        })
        .then(response => response.json())
        .then(data => {
            isCheckingEmail = false;
            
            if (!data.available) {
                showEmailError(data.message || 'This email has already been used, please use another email');
                isEmailValid = false;
            } else {
                hideEmailError();
                isEmailValid = true;
            }
            updateSubmitButton();
        })
        .catch(error => {
            isCheckingEmail = false;
            console.error('Error checking email:', error);
            // On error, allow submission to proceed
            isEmailValid = true;
            hideEmailError();
            updateSubmitButton();
        });
    }

    // ENHANCED: Check email availability specifically for form submission
    function checkEmailAvailabilityForSubmission(email, eventId) {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value || '';
        
        fetch('/check-email-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                email: email,
                event_id: eventId
            })
        })
        .then(response => response.json())
        .then(data => {
            setLoadingState(false);
            
            if (!data.available) {
                // Show error message under email field
                showEmailError(data.message || 'This email has already been used, please use another email');
                isEmailValid = false;
                updateSubmitButton();
            } else {
                // Email is available, proceed with form submission
                hideEmailError();
                isEmailValid = true;
                submitForm();
            }
        })
        .catch(error => {
            setLoadingState(false);
            console.error('Error checking email:', error);
            // On error, allow submission to proceed
            submitForm();
        });
    }

    // Submit the form
    function submitForm() {
        setLoadingState(true);
        form.submit();
    }

    // Validate email format
    function isValidEmailFormat(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // ENHANCED: Show email error message with better styling
    function showEmailError(message) {
        emailValidationMessage.textContent = message;
        emailValidationMessage.style.display = 'block';
        emailValidationMessage.style.color = '#dc3545';
        emailValidationMessage.style.fontWeight = '500';
        emailValidationMessage.style.marginTop = '5px';
        
        // Add red border to email input
        emailInput.style.borderColor = '#dc3545';
        emailInput.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
        
        isEmailValid = false;
        updateSubmitButton();
    }

    // Hide email error message
    function hideEmailError() {
        emailValidationMessage.style.display = 'none';
        emailValidationMessage.textContent = '';
        
        // Remove red border from email input
        emailInput.style.borderColor = '';
        emailInput.style.boxShadow = '';
    }

    // Update submit button state
    function updateSubmitButton() {
        if (isEmailValid && !isCheckingEmail) {
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
            updateSubmitButton();
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

/* ENHANCED: Better error message styling */
#emailValidationMessage {
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #dc3545 !important;
    margin-top: 5px !important;
    padding: 5px 0;
    animation: fadeInError 0.3s ease-in-out;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Error state for email input */
.form-control.error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInError {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: fadeIn 0.3s ease-in-out;
}

/* Better visual feedback */
.text-danger {
    display: block !important;
    margin-top: 5px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
}
</style>
