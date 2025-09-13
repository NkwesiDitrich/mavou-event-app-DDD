@extends('backend.layout.sidenav-layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between">
                    <div class="col-md-8">
                        <h4>Event Participants</h4>
                        <p class="text-muted">View and manage participants for your events</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary" onclick="refreshParticipants()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <hr class="bg-secondary"/>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Event</label>
                        <select class="form-select" id="eventFilter" onchange="filterParticipants()">
                            <option value="">All Events</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Status</label>
                        <select class="form-select" id="statusFilter" onchange="filterParticipants()">
                            <option value="">All Participants</option>
                            <option value="checked_in">Checked In</option>
                            <option value="not_checked_in">Not Checked In</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or event..." onkeyup="handleSearch()" autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                        <small class="text-muted">Search by participant name, email, or event title</small>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4" id="participantStats">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 id="totalParticipants">0</h3>
                                <p class="mb-0">Total Participants</p>
                                <small class="opacity-75" id="totalParticipantsNote">Based on current filters</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 id="checkedInCount">0</h3>
                                <p class="mb-0">Checked In</p>
                                <small class="opacity-75" id="checkedInNote">Based on current filters</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3 id="notCheckedInCount">0</h3>
                                <p class="mb-0">Not Checked In</p>
                                <small class="opacity-75" id="notCheckedInNote">Based on current filters</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 id="checkInRate">0%</h3>
                                <p class="mb-0">Check-in Rate</p>
                                <small class="opacity-75" id="checkInRateNote">Based on current filters</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <div class="row mb-3" id="activeFiltersRow" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-info py-2">
                            <strong>Active Filters:</strong>
                            <span id="activeFiltersText"></span>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="clearAllFilters()">
                                <i class="bi bi-x-circle"></i> Clear All Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Participants Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="participantsTable">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Event</th>
                                                <th>Registration Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="participantsTableBody">
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-2">Loading participants...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <span id="paginationInfo">Showing 0 of 0 participants</span>
                                    </div>
                                    <div>
                                        <nav>
                                            <ul class="pagination mb-0" id="paginationControls">
                                                <!-- Pagination buttons will be inserted here -->
                                            </ul>
                                        </nav>
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

<script>
    let currentPage = 1;
    let currentEventFilter = '';
    let currentStatusFilter = '';
    let currentSearch = '';
    let searchTimeout = null;

    // Load participants on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadParticipants();
    });

    async function loadParticipants(page = 1) {
        try {
            const params = new URLSearchParams({
                page: page,
                per_page: 10
            });

            if (currentEventFilter) params.append('event_id', currentEventFilter);
            if (currentStatusFilter) params.append('status', currentStatusFilter);
            if (currentSearch && currentSearch.trim() !== '') params.append('search', currentSearch.trim());

            // Use the correct API endpoint
            const response = await axios.get(`/user-management/api/event-participants?${params}`);
            
            if (response.data.status === 'success') {
                const data = response.data.data;
                updateParticipantsTable(data.participants || []);
                updateStatistics(data.statistics || {});
                updateEventFilter(data.events || []);
                updatePagination(data.pagination || {}, data.total || 0);
                updateActiveFilters();
                currentPage = page;
            } else {
                throw new Error(response.data.message || 'Failed to load participants');
            }
        } catch (error) {
            console.error('Error loading participants:', error);
            showError('Error loading participants: ' + (error.response?.data?.message || error.message));
            
            // Show empty state
            updateParticipantsTable([]);
            updateStatistics({});
            updateEventFilter([]);
        }
    }

    function updateParticipantsTable(participants) {
        const tbody = document.getElementById('participantsTableBody');
        
        if (!participants || participants.length === 0) {
            const hasFilters = currentEventFilter || currentStatusFilter || (currentSearch && currentSearch.trim() !== '');
            const emptyMessage = hasFilters ? 
                'No participants found matching your filters' : 
                'No participants found';
            
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-1"></i>
                        <p class="mt-2">${emptyMessage}</p>
                        ${hasFilters ? '<button class="btn btn-outline-primary btn-sm" onclick="clearAllFilters()"><i class="bi bi-funnel"></i> Clear Filters</button>' : ''}
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        participants.forEach(participant => {
            const statusBadge = participant.checked_in ? 
                '<span class="badge bg-success">Checked In</span>' : 
                '<span class="badge bg-warning">Not Checked In</span>';
            
            const checkInButton = participant.checked_in ?
                `<button class="btn btn-sm btn-outline-warning" onclick="toggleCheckIn(${participant.id}, false)" title="Check Out">
                    <i class="bi bi-person-dash"></i> Check Out
                </button>` :
                `<button class="btn btn-sm btn-outline-success" onclick="toggleCheckIn(${participant.id}, true)" title="Check In">
                    <i class="bi bi-person-check"></i> Check In
                </button>`;

            const eventDate = participant.event_date ? new Date(participant.event_date).toLocaleDateString() : 'N/A';
            const registrationDate = participant.created_at ? new Date(participant.created_at).toLocaleDateString() : 'N/A';

            // Highlight search terms
            let displayName = participant.name || 'N/A';
            let displayEmail = participant.email || 'N/A';
            let displayEventTitle = participant.event_title || 'Unknown Event';
            
            if (currentSearch && currentSearch.trim() !== '') {
                const searchTerm = currentSearch.trim();
                const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
                displayName = displayName.replace(regex, '<mark>$1</mark>');
                displayEmail = displayEmail.replace(regex, '<mark>$1</mark>');
                displayEventTitle = displayEventTitle.replace(regex, '<mark>$1</mark>');
            }

            html += `
                <tr>
                    <td>${displayName}</td>
                    <td>${displayEmail}</td>
                    <td>${participant.mobile || 'N/A'}</td>
                    <td>
                        <small class="text-muted">${displayEventTitle}</small><br>
                        <small class="text-info">${eventDate}</small>
                    </td>
                    <td>${registrationDate}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            ${checkInButton}
                            <button class="btn btn-sm btn-outline-danger" onclick="unattendParticipant(${participant.id})" title="Remove Participant">
                                <i class="bi bi-person-x"></i> Remove
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function updateStatistics(stats) {
        document.getElementById('totalParticipants').textContent = stats.total_participants || 0;
        document.getElementById('checkedInCount').textContent = stats.checked_in || 0;
        document.getElementById('notCheckedInCount').textContent = stats.not_checked_in || 0;
        
        const rate = stats.total_participants > 0 ? 
            Math.round((stats.checked_in / stats.total_participants) * 100) : 0;
        document.getElementById('checkInRate').textContent = rate + '%';

        // Update notes to indicate filtered statistics
        const hasFilters = currentEventFilter || currentStatusFilter || (currentSearch && currentSearch.trim() !== '');
        const noteText = hasFilters ? 'Filtered results' : 'All participants';
        
        document.getElementById('totalParticipantsNote').textContent = noteText;
        document.getElementById('checkedInNote').textContent = noteText;
        document.getElementById('notCheckedInNote').textContent = noteText;
        document.getElementById('checkInRateNote').textContent = noteText;
    }

    function updateEventFilter(events) {
        const select = document.getElementById('eventFilter');
        const currentValue = select.value;
        
        // Clear existing options except "All Events"
        select.innerHTML = '<option value="">All Events</option>';
        
        if (events && events.length > 0) {
            events.forEach(event => {
                const option = document.createElement('option');
                option.value = event.id;
                option.textContent = `${event.title} (${event.date})`;
                select.appendChild(option);
            });
        }
        
        // Restore previous selection
        select.value = currentValue;
    }

    function updatePagination(pagination, total) {
        const info = document.getElementById('paginationInfo');
        const controls = document.getElementById('paginationControls');
        
        const currentPage = pagination.current_page || 1;
        const perPage = pagination.per_page || 10;
        const totalPages = pagination.total_pages || 1;
        
        const start = total > 0 ? ((currentPage - 1) * perPage) + 1 : 0;
        const end = Math.min(currentPage * perPage, total);
        
        info.textContent = `Showing ${start}-${end} of ${total} participants`;
        
        let html = '';
        
        if (totalPages > 1) {
            // Previous button
            if (currentPage > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="loadParticipants(${currentPage - 1})">Previous</a>
                </li>`;
            }
            
            // Page numbers (show max 5 pages)
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, startPage + 4);
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += `<li class="page-item active">
                        <span class="page-link">${i}</span>
                    </li>`;
                } else {
                    html += `<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadParticipants(${i})">${i}</a>
                    </li>`;
                }
            }
            
            // Next button
            if (pagination.has_more) {
                html += `<li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="loadParticipants(${currentPage + 1})">Next</a>
                </li>`;
            }
        }
        
        controls.innerHTML = html;
    }

    function updateActiveFilters() {
        const activeFiltersRow = document.getElementById('activeFiltersRow');
        const activeFiltersText = document.getElementById('activeFiltersText');
        
        let filters = [];
        
        if (currentEventFilter) {
            const eventSelect = document.getElementById('eventFilter');
            const selectedOption = eventSelect.options[eventSelect.selectedIndex];
            filters.push(`Event: ${selectedOption.text}`);
        }
        
        if (currentStatusFilter) {
            const statusSelect = document.getElementById('statusFilter');
            const selectedOption = statusSelect.options[statusSelect.selectedIndex];
            filters.push(`Status: ${selectedOption.text}`);
        }
        
        if (currentSearch && currentSearch.trim() !== '') {
            filters.push(`Search: "${currentSearch.trim()}"`);
        }
        
        if (filters.length > 0) {
            activeFiltersText.textContent = filters.join(', ');
            activeFiltersRow.style.display = 'block';
        } else {
            activeFiltersRow.style.display = 'none';
        }
    }

    function filterParticipants() {
        currentEventFilter = document.getElementById('eventFilter').value;
        currentStatusFilter = document.getElementById('statusFilter').value;
        loadParticipants(1);
    }

    function handleSearch() {
        // Clear existing timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Set new timeout for search (debounce)
        searchTimeout = setTimeout(() => {
            currentSearch = document.getElementById('searchInput').value;
            loadParticipants(1);
        }, 500); // Wait 500ms after user stops typing
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        loadParticipants(1);
    }

    function clearAllFilters() {
        document.getElementById('eventFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchInput').value = '';
        currentEventFilter = '';
        currentStatusFilter = '';
        currentSearch = '';
        loadParticipants(1);
    }

    async function toggleCheckIn(registrationId, checkIn) {
        try {
            const response = await axios.post('/user-management/check-in', {
                registration_id: registrationId,
                check_in: checkIn
            });
            
            if (response.data.status === 'success') {
                showToast(response.data.message, 'success');
                loadParticipants(currentPage);
            } else {
                throw new Error(response.data.message || 'Failed to update check-in status');
            }
        } catch (error) {
            console.error('Error toggling check-in:', error);
            showToast('Error updating check-in status: ' + (error.response?.data?.message || error.message), 'error');
        }
    }

    async function unattendParticipant(registrationId) {
        if (!confirm('Are you sure you want to remove this participant?')) {
            return;
        }

        try {
            const response = await axios.post('/user-management/unattend', {
                registration_id: registrationId
            });
            
            if (response.data.status === 'success') {
                showToast(response.data.message, 'success');
                loadParticipants(currentPage);
            } else {
                throw new Error(response.data.message || 'Failed to remove participant');
            }
        } catch (error) {
            console.error('Error removing participant:', error);
            showToast('Error removing participant: ' + (error.response?.data?.message || error.message), 'error');
        }
    }

    function refreshParticipants() {
        showToast('Refreshing participants...', 'info');
        loadParticipants(currentPage);
    }

    function showToast(message, type = 'success') {
        // Create toast element if Toastify is not available
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#28a745" : type === 'error' ? "#dc3545" : "#17a2b8",
            }).showToast();
        } else {
            // Fallback to alert
            alert(message);
        }
    }

    function showError(message) {
        const tbody = document.getElementById('participantsTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                    <p class="mt-2">${message}</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="loadParticipants()">
                        <i class="bi bi-arrow-clockwise"></i> Retry
                    </button>
                </td>
            </tr>
        `;
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
</script>
@endsection
