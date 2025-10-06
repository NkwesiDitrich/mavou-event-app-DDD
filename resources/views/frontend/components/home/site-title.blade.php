<!-- Begin Site Title
================================================== -->
<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Time to Swing Into Action</h1>
            <p class="hero-subtitle">
                Discover amazing events and experiences. Join us today!
            </p>
        </div>
    </div>
</div>

<!-- Begin Search Section
================================================== -->
<div class="search-section">
    <div class="container">
        <div class="search-container">
            <h2 class="search-title">Find Your Perfect Event</h2>
            <form id="eventSearchForm" class="event-search-form">
                <div class="search-input-wrapper">
                    <i class="fa fa-search search-icon-left"></i>
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="search-input" 
                        placeholder="Search by event name, location, category, or type..."
                        autocomplete="off"
                    >
                    <button type="button" id="clearSearch" class="clear-search" style="display: none;">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="search-filters">
                    <div class="filter-group">
                        <label><i class="fa fa-calendar"></i> Date Range</label>
                        <select id="dateFilter" class="filter-select">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="next_month">Next Month</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fa fa-tag"></i> Event Type</label>
                        <select id="typeFilter" class="filter-select">
                            <option value="">All Types</option>
                            <option value="feature">Featured</option>
                            <option value="recent">Recent</option>
                        </select>
                    </div>
                    <button type="button" id="resetFilters" class="reset-filters-btn">
                        <i class="fa fa-refresh"></i> Reset Filters
                    </button>
                </div>
            </form>
            <div id="searchResults" class="search-results-info" style="display: none;">
                <span id="resultsCount"></span>
            </div>
        </div>
    </div>
</div>
<!-- End Search Section
================================================== -->

<style>
/* Hero Section Styles */
.hero-section {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    padding: 80px 0 60px;
    margin-top: -20px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    text-align: center;
    position: relative;
    z-index: 1;
}

.hero-title {
    color: white;
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    animation: fadeInUp 0.8s ease-out;
}

.hero-subtitle {
    color: rgba(255, 255, 255, 0.95);
    font-size: 1.3rem;
    font-weight: 400;
    max-width: 600px;
    margin: 0 auto;
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

/* Search Section Styles */
.search-section {
    background: white;
    padding: 40px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: relative;
    z-index: 10;
}

.search-container {
    max-width: 900px;
    margin: 0 auto;
}

.search-title {
    text-align: center;
    color: #1e3a8a;
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 30px;
}

.event-search-form {
    background: white;
}

.search-input-wrapper {
    position: relative;
    margin-bottom: 25px;
}

.search-icon-left {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #3b82f6;
    font-size: 1.2rem;
    z-index: 2;
}

.search-input {
    width: 100%;
    padding: 18px 55px 18px 55px;
    border: 2px solid #e5e7eb;
    border-radius: 50px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.clear-search {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: #ef4444;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 2;
}

.clear-search:hover {
    background: #dc2626;
    transform: translateY(-50%) scale(1.1);
}

.search-filters {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    color: #374151;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.filter-group label i {
    color: #3b82f6;
    margin-right: 5px;
}

.filter-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    background: #f9fafb;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.reset-filters-btn {
    padding: 12px 25px;
    background: #6b7280;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.reset-filters-btn:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.reset-filters-btn i {
    margin-right: 5px;
}

.search-results-info {
    text-align: center;
    margin-top: 20px;
    padding: 12px;
    background: #eff6ff;
    border-radius: 10px;
    color: #1e3a8a;
    font-weight: 600;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .search-title {
        font-size: 1.4rem;
    }
    
    .search-filters {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .reset-filters-btn {
        width: 100%;
    }
    
    .search-input {
        padding: 15px 50px 15px 50px;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 60px 0 40px;
    }
    
    .hero-title {
        font-size: 1.8rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const dateFilter = document.getElementById('dateFilter');
    const typeFilter = document.getElementById('typeFilter');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const searchResults = document.getElementById('searchResults');
    const resultsCount = document.getElementById('resultsCount');
    
    // Show/hide clear button
    searchInput.addEventListener('input', function() {
        clearSearchBtn.style.display = this.value ? 'flex' : 'none';
        performSearch();
    });
    
    // Clear search
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        performSearch();
    });
    
    // Filter changes
    dateFilter.addEventListener('change', performSearch);
    typeFilter.addEventListener('change', performSearch);
    
    // Reset filters
    resetFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        dateFilter.value = '';
        typeFilter.value = '';
        clearSearchBtn.style.display = 'none';
        performSearch();
    });
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const dateValue = dateFilter.value;
        const typeValue = typeFilter.value.toLowerCase();
        
        // Get all event cards
        const eventCards = document.querySelectorAll('.event-card-container');
        let visibleCount = 0;
        
        eventCards.forEach(card => {
            let shouldShow = true;
            
            // Search term filter
            if (searchTerm) {
                const title = card.querySelector('.event-card-title')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.fa-map-marker')?.parentElement.textContent.toLowerCase() || '';
                const organizer = card.querySelector('.fa-user')?.parentElement.textContent.toLowerCase() || '';
                const teaser = card.querySelector('.event-card-teaser')?.textContent.toLowerCase() || '';
                
                const matchesSearch = title.includes(searchTerm) || 
                                    location.includes(searchTerm) || 
                                    organizer.includes(searchTerm) ||
                                    teaser.includes(searchTerm);
                
                if (!matchesSearch) {
                    shouldShow = false;
                }
            }
            
            // Type filter
            if (typeValue && shouldShow) {
                const section = card.closest('section');
                if (typeValue === 'feature' && !section.classList.contains('featured-posts')) {
                    shouldShow = false;
                } else if (typeValue === 'recent' && !section.classList.contains('recent-posts')) {
                    shouldShow = false;
                }
            }
            
            // Date filter (basic implementation - can be enhanced)
            if (dateValue && shouldShow) {
                const dateText = card.querySelector('.fa-calendar')?.parentElement.textContent || '';
                // This is a simplified date filter - you can enhance it based on actual date parsing
                if (dateValue === 'today') {
                    const today = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    if (!dateText.includes(today.split(',')[0])) {
                        // Basic check - can be improved
                    }
                }
            }
            
            // Show/hide card
            if (shouldShow) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update results count
        if (searchTerm || dateValue || typeValue) {
            searchResults.style.display = 'block';
            resultsCount.textContent = `Found ${visibleCount} event${visibleCount !== 1 ? 's' : ''}`;
        } else {
            searchResults.style.display = 'none';
        }
        
        // Show/hide section titles if no results in section
        updateSectionVisibility();
    }
    
    function updateSectionVisibility() {
        const sections = document.querySelectorAll('.featured-posts, .recent-posts');
        
        sections.forEach(section => {
            const visibleCards = section.querySelectorAll('.event-card-container:not([style*="display: none"])');
            const sectionTitle = section.querySelector('.section-title');
            
            if (visibleCards.length === 0) {
                if (sectionTitle) sectionTitle.style.display = 'none';
            } else {
                if (sectionTitle) sectionTitle.style.display = '';
            }
        });
    }
});
</script>
<!-- End Site Title
================================================== -->
