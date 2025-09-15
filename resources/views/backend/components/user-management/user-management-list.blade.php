<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between">
                    <div class="align-items-center col">
                        <h4>User Management</h4>
                        <p class="text-muted">Manage participants for your events</p>
                    </div>
                    <div class="align-items-center col-auto">
                        <div class="d-flex align-items-center">
                            <!-- Event Filter -->
                            <select id="eventFilter" class="form-select me-3" style="min-width: 200px;">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Refresh Button -->
                            <button id="refreshBtn" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                
                @if(isset($error))
                    <div class="alert alert-warning">
                        {{ $error }}
                    </div>
                @endif

                <hr class="bg-secondary"/>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Registrations</h5>
                                <h3 id="totalRegistrations">{{ count($registrations) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Checked In</h5>
                                <h3 id="checkedInCount">
                                    {{ collect($registrations)->filter(function($reg) { return $reg->isCheckedIn(); })->count() }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Not Checked In</h5>
                                <h3 id="notCheckedInCount">
                                    {{ collect($registrations)->filter(function($reg) { return !$reg->isCheckedIn(); })->count() }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Events</h5>
                                <h3>{{ $events->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registrations Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="registrationsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Event</th>
                                <th>Registration Date</th>
                                <th>Check-in Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="registrationsTableBody">
                            @forelse($registrations as $registration)
                                <tr data-registration-id="{{ $registration->getId() }}">
                                    <td>
                                        <strong>{{ $registration->getName()->getValue() }}</strong>
                                    </td>
                                    <td>{{ $registration->getMobile()->getValue() }}</td>
                                    <td>{{ $registration->getEmail() ? $registration->getEmail()->getValue() : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $registration->getEventTitle() ?? 'Unknown Event' }}</span>
                                    </td>
                                    <td>{{ $registration->getFormattedDate() }}</td>
                                    <td>
                                        @if($registration->isCheckedIn())
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Checked In
                                            </span>
                                            @if($registration->getCheckedInAt())
                                                <br><small class="text-muted">{{ $registration->getCheckedInAt()->format('M j, Y H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Not Checked In
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-user-btn" 
                                                data-registration-id="{{ $registration->getId() }}"
                                                data-participant-name="{{ $registration->getName()->getValue() }}"
                                                data-participant-email="{{ $registration->getEmail() ? $registration->getEmail()->getValue() : 'N/A' }}"
                                                data-participant-mobile="{{ $registration->getMobile()->getValue() }}"
                                                data-participant-username="{{ $registration->getParticipantUserName() ?? 'N/A' }}"
                                                title="View User Details">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataRow">
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <br>No registrations found for your events
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="userDetailsContent">
                    <!-- User details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100" 
     style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token - FIXED to handle missing meta tag gracefully
    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        
        // Fallback: try to get from Laravel's global variable
        if (typeof window.Laravel !== 'undefined' && window.Laravel.csrfToken) {
            return window.Laravel.csrfToken;
        }
        
        // Last resort: try to get from any form on the page
        const csrfInput = document.querySelector('input[name="_token"]');
        if (csrfInput) {
            return csrfInput.value;
        }
        
        console.warn('CSRF token not found. Some requests may fail.');
        return '';
    }

    // Event filter change handler
    document.getElementById('eventFilter').addEventListener('change', function() {
        const eventId = this.value;
        const url = new URL(window.location);
        
        if (eventId) {
            url.searchParams.set('event_id', eventId);
        } else {
            url.searchParams.delete('event_id');
        }
        
        window.location.href = url.toString();
    });

    // Refresh button handler
    document.getElementById('refreshBtn').addEventListener('click', function() {
        window.location.reload();
    });

    // View user button handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-user-btn')) {
            const btn = e.target.closest('.view-user-btn');
            const registrationId = btn.dataset.registrationId;
            const participantName = btn.dataset.participantName;
            const participantEmail = btn.dataset.participantEmail;
            const participantMobile = btn.dataset.participantMobile;
            const participantUsername = btn.dataset.participantUsername;
            
            showUserDetails({
                registrationId: registrationId,
                name: participantName,
                email: participantEmail,
                mobile: participantMobile,
                username: participantUsername
            });
        }
    });

    function showUserDetails(userData) {
        // Set modal title
        document.getElementById('userDetailsModalLabel').textContent = `User Details - ${userData.name}`;
        
        // Show loading state
        document.getElementById('userDetailsContent').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading user details...</p>
            </div>
        `;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
        modal.show();
        
        // Load user events
        loadUserEvents(userData);
    }

    async function loadUserEvents(userData) {
        try {
            const csrfToken = getCSRFToken();
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            
            // Only add CSRF token if we have one
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            // FIXED: Use the correct search parameter that the controller now supports
            const response = await fetch('/user-management/registrations?' + new URLSearchParams({
                search_user: userData.email !== 'N/A' ? userData.email : userData.name
            }), {
                headers: headers
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.status === 'success') {
                displayUserDetails(userData, data.data || []);
            } else {
                throw new Error(data.message || 'Failed to load user events');
            }
        } catch (error) {
            console.error('Error loading user events:', error);
            displayUserDetails(userData, [], error.message);
        }
    }

    function displayUserDetails(userData, userRegistrations, errorMessage = null) {
        const content = document.getElementById('userDetailsContent');
        
        // ENHANCED: Better filtering logic for user registrations
        const filteredRegistrations = userRegistrations.filter(reg => {
            // Match by email (exact, case-insensitive) if both have valid emails
            if (userData.email !== 'N/A' && reg.email && reg.email.trim() !== '') {
                return reg.email.toLowerCase() === userData.email.toLowerCase();
            }
            
            // Fallback to name matching (case-insensitive, partial match)
            return reg.name.toLowerCase().includes(userData.name.toLowerCase()) ||
                   userData.name.toLowerCase().includes(reg.name.toLowerCase());
        });
        
        // FIXED: Calculate statistics correctly
        const totalEvents = filteredRegistrations.length;
        const totalCheckins = filteredRegistrations.filter(reg => reg.checked_in).length;
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-person"></i> User Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>${userData.name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>${userData.email}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>${userData.mobile}</td>
                                </tr>
                                ${userData.username !== 'N/A' ? `
                                <tr>
                                    <td><strong>Username:</strong></td>
                                    <td>${userData.username}</td>
                                </tr>
                                ` : ''}
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-graph-up"></i> Registration Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary">${totalEvents}</h4>
                                        <small class="text-muted">Total Events</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">${totalCheckins}</h4>
                                    <small class="text-muted">Total Checkin</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-calendar-event"></i> Registered Events</h6>
                        </div>
                        <div class="card-body">
        `;
        
        if (errorMessage) {
            html += `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Warning:</strong> ${errorMessage}
                </div>
            `;
        }
        
        if (filteredRegistrations.length === 0) {
            html += `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">No event registrations found for this user</p>
                    ${errorMessage ? '<small class="text-muted">There may have been an error loading the data.</small>' : ''}
                </div>
            `;
        } else {
            html += `
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>Check-in Date</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            filteredRegistrations.forEach(registration => {
                const statusBadge = registration.checked_in ? 
                    '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Checked In</span>' : 
                    '<span class="badge bg-warning"><i class="bi bi-clock"></i> Not Checked In</span>';
                
                const checkinDate = registration.checked_in_at ? 
                    new Date(registration.checked_in_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'N/A';
                
                const registrationDate = new Date(registration.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                html += `
                    <tr>
                        <td>
                            <strong>${registration.event_title || 'Unknown Event'}</strong>
                        </td>
                        <td>${registrationDate}</td>
                        <td>${statusBadge}</td>
                        <td>${checkinDate}</td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        html += `
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        content.innerHTML = html;
    }

    function showLoading() {
        document.getElementById('loadingOverlay').classList.remove('d-none');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.add('d-none');
    }

    function showToast(title, message, type) {
        // Create toast element
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        // Add to toast container (create if doesn't exist)
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '10000';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Show the toast
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        // Remove from DOM after hiding
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    function updateStatistics() {
        const rows = document.querySelectorAll('#registrationsTableBody tr[data-registration-id]');
        const total = rows.length;
        let checkedIn = 0;
        
        rows.forEach(row => {
            const badge = row.querySelector('.badge');
            if (badge && badge.textContent.includes('Checked In')) {
                checkedIn++;
            }
        });
        
        document.getElementById('totalRegistrations').textContent = total;
        document.getElementById('checkedInCount').textContent = checkedIn;
        document.getElementById('notCheckedInCount').textContent = total - checkedIn;
        
        // Show/hide no data row
        const noDataRow = document.getElementById('noDataRow');
        if (total === 0 && !noDataRow) {
            document.getElementById('registrationsTableBody').innerHTML = `
                <tr id="noDataRow">
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <br>No registrations found for your events
                    </td>
                </tr>
            `;
        } else if (total > 0 && noDataRow) {
            noDataRow.remove();
        }
    }
});
</script>

<style>
.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-size: 0.75em;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 2px;
        margin-right: 0;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
