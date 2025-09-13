@extends('backend.layout.sidenav-layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between">
                    <div class="col-md-8">
                        <h4>Events Created</h4>
                        <p class="text-muted">View your events and their participant statistics</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary" onclick="refreshEvents()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
                <hr class="bg-secondary"/>

                <!-- Active Filters Display -->
                <div id="activeFiltersAlert" class="alert alert-info d-none mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Active Filters:</strong>
                            <span id="activeFiltersList"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearAllFilters()">
                            Clear All Filters
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Filter by Status</label>
                        <select class="form-select" id="statusFilter" onchange="filterEvents()">
                            <option value="">All Events</option>
                            <option value="upcoming">Upcoming Events</option>
                            <option value="past">Past Events</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search Events</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by title, description, or location..." onkeyup="debounceSearch()">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" onclick="clearSearch()" style="display: none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">View Mode</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="viewMode" id="cardView" value="card" checked onchange="toggleViewMode()">
                            <label class="btn btn-outline-primary" for="cardView">
                                <i class="bi bi-grid"></i> Cards
                            </label>
                            <input type="radio" class="btn-check" name="viewMode" id="tableView" value="table" onchange="toggleViewMode()">
                            <label class="btn btn-outline-primary" for="tableView">
                                <i class="bi bi-list"></i> Table
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Per Page</label>
                        <select class="form-select" id="perPageSelect" onchange="changePerPage()">
                            <option value="6">6 per page</option>
                            <option value="12" selected>12 per page</option>
                            <option value="24">24 per page</option>
                            <option value="48">48 per page</option>
                        </select>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4" id="eventStats">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 id="totalEvents">0</h3>
                                <p class="mb-0">Total Events</p>
                                <small id="totalEventsLabel" class="opacity-75">All events</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 id="upcomingEvents">0</h3>
                                <p class="mb-0">Upcoming Events</p>
                                <small id="upcomingEventsLabel" class="opacity-75">All upcoming</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 id="totalParticipants">0</h3>
                                <p class="mb-0">Total Participants</p>
                                <small id="totalParticipantsLabel" class="opacity-75">All participants</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3 id="checkedInParticipants">0</h3>
                                <p class="mb-0">Checked In</p>
                                <small id="checkedInParticipantsLabel" class="opacity-75">All checked in</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events Display -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- Card View -->
                        <div id="cardViewContainer" class="row">
                            <div class="col-12 text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading events...</p>
                            </div>
                        </div>

                        <!-- Table View -->
                        <div id="tableViewContainer" class="card d-none">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Event</th>
                                                <th>Date & Time</th>
                                                <th>Location</th>
                                                <th>Participants</th>
                                                <th>Check-in Rate</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="eventsTableBody">
                                            <!-- Table rows will be inserted here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span id="paginationInfo">Showing 0 of 0 events</span>
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

<!-- Event Participants Modal -->
<div class="modal fade" id="participantsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participantsModalTitle">Event Participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="participantsModalContent">
                    <!-- Participants will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentStatusFilter = '';
    let currentSearch = '';
    let currentViewMode = 'card';
    let currentPerPage = 12;
    let searchTimeout = null;

    // Load events on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadEvents();
    });

    // FIXED: Use correct API endpoint
    async function loadEvents(page = 1) {
        try {
            showLoadingState();
            
            const params = new URLSearchParams({
                page: page,
                per_page: currentPerPage
            });

            if (currentStatusFilter) params.append('status', currentStatusFilter);
            if (currentSearch && currentSearch.trim() !== '') params.append('search', currentSearch.trim());

            // FIXED: Use the correct API endpoint
            const response = await axios.get(`/user-management/api/events-created?${params}`);
            
            if (response.data.status === 'success') {
                const data = response.data.data;
                updateEventsDisplay(data.events);
                updateStatistics(data.statistics);
                updatePagination(data.pagination, data.total);
                updateActiveFilters();
                currentPage = page;
            } else {
                throw new Error(response.data.message || 'Failed to load events');
            }
        } catch (error) {
            console.error('Error loading events:', error);
            showErrorState('Error loading events: ' + (error.response?.data?.message || error.message));
        }
    }

    function showLoadingState() {
        const container = document.getElementById('cardViewContainer');
        container.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading events...</p>
            </div>
        `;
    }

    function showErrorState(message) {
        const container = document.getElementById('cardViewContainer');
        container.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> ${message}
                    <br><br>
                    <button class="btn btn-outline-danger" onclick="loadEvents()">
                        <i class="bi bi-arrow-clockwise"></i> Retry
                    </button>
                </div>
            </div>
        `;
    }

    function updateEventsDisplay(events) {
        if (currentViewMode === 'card') {
            updateCardView(events);
        } else {
            updateTableView(events);
        }
    }

    function updateCardView(events) {
        const container = document.getElementById('cardViewContainer');
        
        if (!events || events.length === 0) {
            const hasFilters = currentStatusFilter || (currentSearch && currentSearch.trim() !== '');
            const emptyMessage = hasFilters 
                ? 'No events match your current filters' 
                : 'No events found';
            const emptySubMessage = hasFilters 
                ? 'Try adjusting your filters or search terms' 
                : 'Create your first event to get started';
                
            container.innerHTML = `
                <div class="col-12 text-center py-4 text-muted">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2 mb-1">${emptyMessage}</p>
                    <small>${emptySubMessage}</small>
                </div>
            `;
            return;
        }

        let html = '';
        events.forEach(event => {
            const statusBadge = event.is_upcoming ? 
                '<span class="badge bg-success">Upcoming</span>' : 
                event.is_today ? '<span class="badge bg-warning">Today</span>' :
                '<span class="badge bg-secondary">Past</span>';
            
            const checkInRate = event.participant_stats.check_in_rate;
            const progressColor = checkInRate >= 70 ? 'success' : checkInRate >= 40 ? 'warning' : 'danger';

            // Highlight search terms
            const highlightedTitle = highlightSearchTerm(event.title, currentSearch);
            const highlightedDescription = highlightSearchTerm(event.description.substring(0, 100), currentSearch);
            const highlightedLocation = highlightSearchTerm(event.location, currentSearch);

            html += `
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        ${event.image ? `<img src="/uploads/${event.image}" class="card-img-top" style="height: 200px; object-fit: cover;">` : ''}
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title">${highlightedTitle}</h5>
                                ${statusBadge}
                            </div>
                            <p class="card-text text-muted small">${highlightedDescription}${event.description.length > 100 ? '...' : ''}</p>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> ${event.date}<br>
                                    <i class="bi bi-geo-alt"></i> ${highlightedLocation}<br>
                                    <i class="bi bi-clock"></i> ${event.time}
                                </small>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small>Participants: ${event.participant_stats.total_participants}</small>
                                    <small>Check-in: ${checkInRate}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-${progressColor}" style="width: ${checkInRate}%"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="viewParticipants(${event.id}, '${event.title.replace(/'/g, "\\'")}')">
                                    <i class="bi bi-people"></i> View Participants
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    function updateTableView(events) {
        const tbody = document.getElementById('eventsTableBody');
        
        if (!events || events.length === 0) {
            const hasFilters = currentStatusFilter || (currentSearch && currentSearch.trim() !== '');
            const emptyMessage = hasFilters 
                ? 'No events match your current filters' 
                : 'No events found';
                
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <p class="mt-2">${emptyMessage}</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        events.forEach(event => {
            const statusBadge = event.is_upcoming ? 
                '<span class="badge bg-success">Upcoming</span>' : 
                event.is_today ? '<span class="badge bg-warning">Today</span>' :
                '<span class="badge bg-secondary">Past</span>';
            
            const checkInRate = event.participant_stats.check_in_rate;
            const progressColor = checkInRate >= 70 ? 'success' : checkInRate >= 40 ? 'warning' : 'danger';

            // Highlight search terms
            const highlightedTitle = highlightSearchTerm(event.title, currentSearch);
            const highlightedDescription = highlightSearchTerm(event.description.substring(0, 50), currentSearch);
            const highlightedLocation = highlightSearchTerm(event.location, currentSearch);

            html += `
                <tr>
                    <td>
                        <div class="fw-bold">${highlightedTitle}</div>
                        <small class="text-muted">${highlightedDescription}${event.description.length > 50 ? '...' : ''}</small>
                    </td>
                    <td>
                        <div>${event.date}</div>
                        <small class="text-muted">${event.time}</small>
                    </td>
                    <td>${highlightedLocation}</td>
                    <td>
                        <div>${event.participant_stats.total_participants} total</div>
                        <small class="text-success">${event.participant_stats.checked_in} checked in</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-${progressColor}" style="width: ${checkInRate}%"></div>
                            </div>
                            <small>${checkInRate}%</small>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewParticipants(${event.id}, '${event.title.replace(/'/g, "\\'")}')">
                            <i class="bi bi-people"></i> View
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function updateStatistics(stats) {
        const hasFilters = currentStatusFilter || (currentSearch && currentSearch.trim() !== '');
        const filterLabel = hasFilters ? 'Filtered results' : 'All events';
        
        document.getElementById('totalEvents').textContent = stats.total_events || 0;
        document.getElementById('upcomingEvents').textContent = stats.upcoming_events || 0;
        document.getElementById('totalParticipants').textContent = stats.overall_participants.total_participants || 0;
        document.getElementById('checkedInParticipants').textContent = stats.overall_participants.checked_in || 0;
        
        // Update labels to show if filtered
        document.getElementById('totalEventsLabel').textContent = filterLabel;
        document.getElementById('upcomingEventsLabel').textContent = filterLabel;
        document.getElementById('totalParticipantsLabel').textContent = filterLabel;
        document.getElementById('checkedInParticipantsLabel').textContent = filterLabel;
    }

    function updateActiveFilters() {
        const activeFilters = [];
        const alertDiv = document.getElementById('activeFiltersAlert');
        const filtersList = document.getElementById('activeFiltersList');
        
        if (currentStatusFilter) {
            const statusText = currentStatusFilter.charAt(0).toUpperCase() + currentStatusFilter.slice(1);
            activeFilters.push(`Status: ${statusText}`);
        }
        
        if (currentSearch && currentSearch.trim() !== '') {
            activeFilters.push(`Search: "${currentSearch.trim()}"`);
        }
        
        if (activeFilters.length > 0) {
            filtersList.textContent = activeFilters.join(', ');
            alertDiv.classList.remove('d-none');
        } else {
            alertDiv.classList.add('d-none');
        }
    }

    function updatePagination(pagination, total) {
        const info = document.getElementById('paginationInfo');
        const controls = document.getElementById('paginationControls');
        
        const start = total > 0 ? ((pagination.current_page - 1) * pagination.per_page) + 1 : 0;
        const end = Math.min(pagination.current_page * pagination.per_page, total);
        
        info.textContent = `Showing ${start}-${end} of ${total} events`;
        
        let html = '';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="loadEvents(${pagination.current_page - 1})">Previous</a>
            </li>`;
        }
        
        // Page numbers (show max 5 pages)
        const maxPages = Math.min(pagination.total_pages, 5);
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.total_pages, startPage + maxPages - 1);
        
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active">
                    <span class="page-link">${i}</span>
                </li>`;
            } else {
                html += `<li class="page-item">
                    <a class="page-link" href="javascript:void(0)" onclick="loadEvents(${i})">${i}</a>
                </li>`;
            }
        }
        
        // Next button
        if (pagination.has_more) {
            html += `<li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="loadEvents(${pagination.current_page + 1})">Next</a>
            </li>`;
        }
        
        controls.innerHTML = html;
    }

    function toggleViewMode() {
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        const cardContainer = document.getElementById('cardViewContainer');
        const tableContainer = document.getElementById('tableViewContainer');

        if (cardView.checked) {
            currentViewMode = 'card';
            cardContainer.classList.remove('d-none');
            tableContainer.classList.add('d-none');
        } else {
            currentViewMode = 'table';
            cardContainer.classList.add('d-none');
            tableContainer.classList.remove('d-none');
        }
        
        loadEvents(currentPage);
    }

    function filterEvents() {
        currentStatusFilter = document.getElementById('statusFilter').value;
        loadEvents(1);
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        const searchValue = document.getElementById('searchInput').value;
        
        // Show/hide clear button
        const clearBtn = document.getElementById('clearSearchBtn');
        if (searchValue.trim() !== '') {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }
        
        searchTimeout = setTimeout(() => {
            currentSearch = searchValue;
            loadEvents(1);
        }, 500); // 500ms delay
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('clearSearchBtn').style.display = 'none';
        currentSearch = '';
        loadEvents(1);
    }

    function clearAllFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchInput').value = '';
        document.getElementById('clearSearchBtn').style.display = 'none';
        currentStatusFilter = '';
        currentSearch = '';
        loadEvents(1);
    }

    function changePerPage() {
        currentPerPage = parseInt(document.getElementById('perPageSelect').value);
        loadEvents(1);
    }

    function highlightSearchTerm(text, searchTerm) {
        if (!searchTerm || searchTerm.trim() === '') {
            return text;
        }
        
        const regex = new RegExp(`(${escapeRegex(searchTerm.trim())})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    async function viewParticipants(eventId, eventTitle) {
        try {
            const modal = new bootstrap.Modal(document.getElementById('participantsModal'));
            document.getElementById('participantsModalTitle').textContent = `Participants - ${eventTitle}`;
            document.getElementById('participantsModalContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading participants...</p>
                </div>
            `;
            
            modal.show();

            const response = await axios.get(`/user-management/api/event/${eventId}/participants`);
            
            if (response.data.status === 'success') {
                const participants = response.data.data.participants;
                updateParticipantsModal(participants);
            }
        } catch (error) {
            console.error('Error loading participants:', error);
            document.getElementById('participantsModalContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> Error loading participants: ${error.response?.data?.message || error.message}
                </div>
            `;
        }
    }

    function updateParticipantsModal(participants) {
        const container = document.getElementById('participantsModalContent');
        
        if (!participants || participants.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-1"></i>
                    <p class="mt-2">No participants found for this event</p>
                </div>
            `;
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        
        participants.forEach(participant => {
            const statusBadge = participant.checked_in ? 
                '<span class="badge bg-success">Checked In</span>' : 
                '<span class="badge bg-warning">Not Checked In</span>';
            
            const checkInButton = participant.checked_in ?
                `<button class="btn btn-sm btn-outline-warning" onclick="toggleCheckInModal(${participant.id}, false)">Check Out</button>` :
                `<button class="btn btn-sm btn-outline-success" onclick="toggleCheckInModal(${participant.id}, true)">Check In</button>`;

            html += `
                <tr>
                    <td>${participant.name}</td>
                    <td>${participant.email}</td>
                    <td>${statusBadge}</td>
                    <td>${checkInButton}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    async function toggleCheckInModal(registrationId, checkIn) {
        try {
            const response = await axios.post('/user-management/check-in', {
                registration_id: registrationId,
                check_in: checkIn
            });
            
            if (response.data.status === 'success') {
                showToast(response.data.message, 'success');
                // Refresh the modal content and main page
                loadEvents(currentPage);
                // Re-load participants modal if it's open
                const modal = document.getElementById('participantsModal');
                if (modal.classList.contains('show')) {
                    const eventId = modal.getAttribute('data-event-id');
                    if (eventId) {
                        viewParticipants(eventId, '');
                    }
                }
            }
        } catch (error) {
            console.error('Error toggling check-in:', error);
            showToast('Error updating check-in status: ' + (error.response?.data?.message || error.message), 'error');
        }
    }

    function refreshEvents() {
        showToast('Refreshing events...', 'info');
        loadEvents(currentPage);
    }

    function showToast(message, type = 'success') {
        // Create toast element
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toastId = 'toast-' + Date.now();
        
        const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
        return container;
    }
</script>
@endsection
