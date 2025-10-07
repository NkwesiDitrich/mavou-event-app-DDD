@extends('frontend.layout.app')
@section('content')
    @include('frontend.components.home.site-title')
    
    <!-- All Events Section -->
    <section class="all-events-section">
        <div class="container">
            <div class="section-title">
                <h2><span>All Events</span></h2>
            </div>
            
            <!-- Filter Section -->
            <div class="events-filter-container">
                <div class="filter-row">
                    <div class="filter-item">
                        <input type="text" id="searchInput" class="filter-input" placeholder="Search by name...">
                    </div>
                    <div class="filter-item">
                        <select id="categoryFilter" class="filter-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-item">
                        <input type="text" id="locationFilter" class="filter-input" placeholder="Filter by location...">
                    </div>
                    <div class="filter-item">
                        <select id="typeFilter" class="filter-select">
                            <option value="">All Types</option>
                            <option value="Feature">Featured</option>
                            <option value="Recent">Recent</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <select id="dateRangeFilter" class="filter-select">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this_week">This Week</option>
                            <option value="this_weekend">This Weekend</option>
                            <option value="this_month">This Month</option>
                            <option value="next_month">Next Month</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <button id="clearFilters" class="btn-clear-filters">
                            <i class="fa fa-times"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="loading-indicator" style="display: none;">
                <i class="fa fa-spinner fa-spin"></i> Loading events...
            </div>
            
            <!-- Events Grid -->
            <div class="events-grid" id="eventsGrid">
                <!-- Events will be loaded here dynamically -->
            </div>
            
            <!-- No Results Message -->
            <div id="noResultsMessage" class="no-results-message" style="display: none;">
                <i class="fa fa-search"></i>
                <p>No events found matching your criteria.</p>
            </div>
            
            <!-- Load More Trigger (for infinite scroll) -->
            <div id="loadMoreTrigger" style="height: 50px; margin: 20px 0;"></div>
            
            <!-- Manual Load More Button (fallback) -->
            <div class="load-more-container" style="text-align: center; margin: 30px 0; display: none;" id="loadMoreContainer">
                <button id="loadMoreBtn" class="btn-load-more">
                    <span>Load More Events</span>
                    <i class="fa fa-chevron-down" style="margin-left: 8px;"></i>
                </button>
            </div>
        </div>
    </section>
    
    @include('frontend.components.home.footer')
    
    <!-- Inline Styles for All Events Page -->
    <style>
        .all-events-section {
            padding: 40px 0;
            min-height: 60vh;
        }
        
        .events-filter-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filter-item {
            flex: 1;
            min-width: 180px;
        }
        
        .filter-input,
        .filter-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #00ab6b;
            box-shadow: 0 0 0 3px rgba(0, 171, 107, 0.1);
        }
        
        .btn-clear-filters {
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-clear-filters:hover {
            background: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }
        
        .btn-see-more {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #00ab6b, #008a56);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 171, 107, 0.3);
        }
        
        .btn-see-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 171, 107, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-load-more {
            padding: 12px 30px;
            background: #00ab6b;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 171, 107, 0.3);
        }
        
        .btn-load-more:hover {
            background: #008a56;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 171, 107, 0.4);
        }
        
        .loading-indicator {
            text-align: center;
            padding: 20px;
            font-size: 16px;
            color: #00ab6b;
        }
        
        .no-results-message {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-results-message i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-results-message p {
            font-size: 18px;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }
            
            .filter-item {
                width: 100%;
                min-width: 100%;
            }
        }
    </style>
    
    <!-- JavaScript for Infinite Scroll and Filtering with Persistence -->
    <script>
        (function() {
            let currentPage = 1;
            let isLoading = false;
            let hasMoreEvents = true;
            let debounceTimer = null;
            const DEBOUNCE_DELAY = 500;
            const EVENTS_PER_PAGE = 12;
            const SCROLL_BATCH_SIZE = 10;
            
            // Get filter elements
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const locationFilter = document.getElementById('locationFilter');
            const typeFilter = document.getElementById('typeFilter');
            const dateRangeFilter = document.getElementById('dateRangeFilter');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const eventsGrid = document.getElementById('eventsGrid');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const noResultsMessage = document.getElementById('noResultsMessage');
            const loadMoreTrigger = document.getElementById('loadMoreTrigger');
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const loadMoreContainer = document.getElementById('loadMoreContainer');
            
            // Filter state management
            const STORAGE_KEY = 'eventFilters';
            
            // Load saved filters from sessionStorage
            function loadSavedFilters() {
                try {
                    const savedFilters = sessionStorage.getItem(STORAGE_KEY);
                    if (savedFilters) {
                        const filters = JSON.parse(savedFilters);
                        searchInput.value = filters.search || '';
                        categoryFilter.value = filters.category || '';
                        locationFilter.value = filters.location || '';
                        typeFilter.value = filters.type || '';
                        dateRangeFilter.value = filters.dateRange || '';
                        return true;
                    }
                } catch (e) {
                    console.error('Error loading saved filters:', e);
                }
                return false;
            }
            
            // Save filters to sessionStorage
            function saveFilters() {
                try {
                    const filters = {
                        search: searchInput.value.trim(),
                        category: categoryFilter.value,
                        location: locationFilter.value.trim(),
                        type: typeFilter.value,
                        dateRange: dateRangeFilter.value
                    };
                    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(filters));
                } catch (e) {
                    console.error('Error saving filters:', e);
                }
            }
            
            // Clear saved filters
            function clearSavedFilters() {
                try {
                    sessionStorage.removeItem(STORAGE_KEY);
                } catch (e) {
                    console.error('Error clearing saved filters:', e);
                }
            }
            
            // Load events function
            function loadEvents(reset = false) {
                if (isLoading || (!hasMoreEvents && !reset)) return;
                
                isLoading = true;
                loadingIndicator.style.display = 'block';
                noResultsMessage.style.display = 'none';
                
                if (reset) {
                    currentPage = 1;
                    hasMoreEvents = true;
                    eventsGrid.innerHTML = '';
                }
                
                // Save current filters
                saveFilters();
                
                const params = new URLSearchParams({
                    page: currentPage,
                    per_page: currentPage === 1 ? EVENTS_PER_PAGE : SCROLL_BATCH_SIZE,
                    search: searchInput.value.trim(),
                    category: categoryFilter.value,
                    location: locationFilter.value.trim(),
                    type: typeFilter.value,
                    date_range: dateRangeFilter.value
                });
                
                fetch(`{{ url('/api/events/all') }}?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.events.length === 0) {
                                if (currentPage === 1) {
                                    noResultsMessage.style.display = 'block';
                                }
                                hasMoreEvents = false;
                            } else {
                                renderEvents(data.events);
                                hasMoreEvents = data.has_more;
                                currentPage++;
                            }
                        } else {
                            console.error('Error loading events:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                    })
                    .finally(() => {
                        isLoading = false;
                        loadingIndicator.style.display = 'none';
                    });
            }
            
            // Render events function
            function renderEvents(events) {
                events.forEach(event => {
                    const eventCard = createEventCard(event);
                    eventsGrid.appendChild(eventCard);
                });
            }
            
            // Create event card element
            function createEventCard(event) {
                const card = document.createElement('div');
                card.className = 'event-card-container';
                
                const teaserHtml = event.teaser ? `
                    <div class="event-card-teaser">
                        ${escapeHtml(event.teaser)}
                    </div>
                ` : '';
                
                card.innerHTML = `
                    <div class="event-card-image">
                        <a href="${event.url}">
                            <img src="${event.image}" alt="${escapeHtml(event.title)}">
                        </a>
                    </div>
                    <div class="event-card-details">
                        <h3 class="event-card-title">
                            <a href="${event.url}">${escapeHtml(event.title)}</a>
                        </h3>
                        ${teaserHtml}
                        <div class="event-card-info">
                            <div class="event-info-item">
                                <i class="fa fa-calendar"></i>
                                <span>${escapeHtml(event.date)}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fa fa-clock-o"></i>
                                <span>${escapeHtml(event.time || 'Time TBA')}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fa fa-map-marker"></i>
                                <span>${escapeHtml(event.location)}</span>
                            </div>
                            <div class="event-info-item">
                                <i class="fa fa-user"></i>
                                <span>${escapeHtml(event.organizer || 'Unknown Organizer')}</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <a href="${event.url}" class="event-card-read-more" title="View Event Details">
                                View Details
                            </a>
                        </div>
                    </div>
                `;
                
                return card;
            }
            
            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Debounced filter function
            function debouncedFilter() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    loadEvents(true);
                }, DEBOUNCE_DELAY);
            }
            
            // Attach filter event listeners
            searchInput.addEventListener('input', debouncedFilter);
            categoryFilter.addEventListener('change', debouncedFilter);
            locationFilter.addEventListener('input', debouncedFilter);
            typeFilter.addEventListener('change', debouncedFilter);
            dateRangeFilter.addEventListener('change', debouncedFilter);
            
            // Clear filters
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                categoryFilter.value = '';
                locationFilter.value = '';
                typeFilter.value = '';
                dateRangeFilter.value = '';
                clearSavedFilters();
                loadEvents(true);
            });
            
            // Infinite scroll observer
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && hasMoreEvents && !isLoading) {
                        loadEvents();
                    }
                });
            }, {
                rootMargin: '100px'
            });
            
            observer.observe(loadMoreTrigger);
            
            // Manual load more button (fallback)
            loadMoreBtn.addEventListener('click', function() {
                loadEvents();
            });
            
            // Load saved filters and initial events
            const hadSavedFilters = loadSavedFilters();
            loadEvents();
        })();
    </script>
@endsection
