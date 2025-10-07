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
    margin-bottom: 0;
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
<!-- End Site Title
================================================== -->
