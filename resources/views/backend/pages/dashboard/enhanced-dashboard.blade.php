@extends('backend.layout.sidenav-layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-4 py-4">
                <!-- Header Section -->
                <div class="row justify-content-between align-items-center mb-4">
                    <div class="col-md-8">
                        <h3 class="mb-2">📊 Event Analytics Dashboard</h3>
                        <p class="text-muted mb-0">Comprehensive insights into your event performance and audience engagement</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary me-2" onclick="refreshDashboard()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#compareEventsModal">
                            <i class="bi bi-bar-chart"></i> Compare Events
                        </button>
                    </div>
                </div>

                <!-- Core Statistics & KPIs -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h5 class="mb-3">📈 Core Statistics & KPIs</h5>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-gradient-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Total Events</h6>
                                        <h2 id="totalEvents" class="mb-1">0</h2>
                                        <small id="eventsGrowth" class="opacity-75">+0% from last month</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-calendar-event fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-gradient-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Total Registrations</h6>
                                        <h2 id="totalRegistrations" class="mb-1">0</h2>
                                        <small id="registrationsTrend" class="opacity-75">📈 Trending up</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-gradient-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Avg Participants</h6>
                                        <h2 id="avgParticipants" class="mb-1">0</h2>
                                        <small class="opacity-75">Per event</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-person-lines-fill fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-gradient-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title mb-1">Check-in Rate</h6>
                                        <h2 id="checkInRate" class="mb-1">0%</h2>
                                        <small class="opacity-75">Attendance rate</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-person-check fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popularity Insights -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h5 class="mb-3">🔥 Popularity Insights</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">🏆 Most Popular Event</h6>
                            </div>
                            <div class="card-body" id="mostPopularEvent">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">📅 Top 5 Upcoming Events</h6>
                            </div>
                            <div class="card-body" id="topUpcomingEvents">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">📈 Trending Events</h6>
                            </div>
                            <div class="card-body" id="trendingEvents">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">👥 Most Active Participants</h6>
                            </div>
                            <div class="card-body" id="activeParticipants">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visualizations -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h5 class="mb-3">📊 Data Visualizations</h5>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">📊 Top 5 Events by Registrations</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="eventsBarChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">📈 Registration Growth Over Time</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="registrationLineChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">🥧 Event Categories Distribution</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="categoriesPieChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">🏅 Participant Leaderboard</h6>
                            </div>
                            <div class="card-body" id="participantLeaderboard">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">⚡ Quick Actions</h5>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ url('/user-management/event-participants') }}" class="btn btn-outline-primary">
                                <i class="bi bi-person-check"></i> View Event Participants
                            </a>
                            <a href="{{ url('/user-management/events-created') }}" class="btn btn-outline-success">
                                <i class="bi bi-calendar-plus"></i> View Events Created
                            </a>
                            <button class="btn btn-outline-info" onclick="exportAnalytics()">
                                <i class="bi bi-download"></i> Export Analytics
                            </button>
                            <button class="btn btn-outline-warning" onclick="scheduleReport()">
                                <i class="bi bi-clock"></i> Schedule Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Real-time Recent Activity -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">🔄 Recent Activity</h5>
                            <small class="text-muted">
                                <i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i>
                                Live updates every 30 seconds
                            </small>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div id="recentActivity">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading recent activity...</p>
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

<!-- Event Comparison Modal -->
<div class="modal fade" id="compareEventsModal" tabindex="-1" aria-labelledby="compareEventsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compareEventsModalLabel">📊 Event Performance Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="compareEventsForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="event1Select" class="form-label">Select First Event</label>
                            <select class="form-select" id="event1Select" required>
                                <option value="">Choose an event...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="event2Select" class="form-label">Select Second Event</label>
                            <select class="form-select" id="event2Select" required>
                                <option value="">Choose an event...</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-bar-chart"></i> Compare Events
                        </button>
                    </div>
                </form>
                <div id="comparisonResults" class="mt-4" style="display: none;">
                    <!-- Comparison results will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let charts = {};
let activityInterval;

// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadEventOptions();
    startRealtimeUpdates();
});

async function loadDashboardData() {
    try {
        showToast('Loading dashboard analytics...', 'info');
        
        const response = await axios.get('/dashboard/analytics');
        if (response.data.status === 'success') {
            const data = response.data.data;
            
            updateCoreStats(data.core_stats);
            updatePopularityInsights(data.popularity_insights);
            updateCharts(data.chart_data);
            updateParticipantLeaderboard(data.popularity_insights.most_active_participants);
            
            showToast('Dashboard loaded successfully!', 'success');
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showToast('Error loading dashboard data', 'error');
    }
}

function updateCoreStats(stats) {
    document.getElementById('totalEvents').textContent = stats.total_events.total;
    document.getElementById('eventsGrowth').textContent = 
        `${stats.total_events.growth_percentage >= 0 ? '+' : ''}${stats.total_events.growth_percentage}% from last month`;
    
    document.getElementById('totalRegistrations').textContent = stats.total_registrations.count;
    document.getElementById('registrationsTrend').textContent = 
        `📈 ${stats.total_registrations.trend.direction === 'up' ? 'Trending up' : 'Trending down'} ${stats.total_registrations.trend.percentage}%`;
    
    document.getElementById('avgParticipants').textContent = stats.average_participants;
    document.getElementById('checkInRate').textContent = `${stats.check_in_rate}%`;
}

function updatePopularityInsights(insights) {
    // Most Popular Event
    const mostPopularContainer = document.getElementById('mostPopularEvent');
    if (insights.most_popular_event) {
        const event = insights.most_popular_event;
        mostPopularContainer.innerHTML = `
            <div class="text-center">
                <h4 class="text-primary">${event.title}</h4>
                <p class="mb-2"><strong>${event.participants}</strong> participants</p>
                <p class="text-muted mb-1"><i class="bi bi-calendar"></i> ${event.date}</p>
                <p class="text-muted"><i class="bi bi-geo-alt"></i> ${event.location}</p>
            </div>
        `;
    } else {
        mostPopularContainer.innerHTML = '<p class="text-muted text-center">No events found</p>';
    }
    
    // Top 5 Upcoming Events
    const upcomingContainer = document.getElementById('topUpcomingEvents');
    if (insights.top_5_upcoming && insights.top_5_upcoming.length > 0) {
        let html = '<div class="list-group list-group-flush">';
        insights.top_5_upcoming.forEach((event, index) => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <h6 class="mb-1">${event.title}</h6>
                        <small class="text-muted">${event.date}</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">${event.participants}</span>
                </div>
            `;
        });
        html += '</div>';
        upcomingContainer.innerHTML = html;
    } else {
        upcomingContainer.innerHTML = '<p class="text-muted text-center">No upcoming events</p>';
    }
    
    // Trending Events
    const trendingContainer = document.getElementById('trendingEvents');
    if (insights.trending_events && insights.trending_events.length > 0) {
        let html = '<div class="list-group list-group-flush">';
        insights.trending_events.forEach(event => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <h6 class="mb-1">${event.title}</h6>
                        <small class="text-success">+${event.growth_rate}% growth</small>
                    </div>
                    <span class="badge bg-success rounded-pill">${event.participants}</span>
                </div>
            `;
        });
        html += '</div>';
        trendingContainer.innerHTML = html;
    } else {
        trendingContainer.innerHTML = '<p class="text-muted text-center">No trending events</p>';
    }
    
    // Most Active Participants
    const activeContainer = document.getElementById('activeParticipants');
    if (insights.most_active_participants && insights.most_active_participants.length > 0) {
        let html = '<div class="list-group list-group-flush">';
        insights.most_active_participants.forEach((participant, index) => {
            const medal = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : '🏅';
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <span class="me-2">${medal}</span>
                        <strong>${participant.name}</strong>
                    </div>
                    <span class="badge bg-info rounded-pill">${participant.events_attended} events</span>
                </div>
            `;
        });
        html += '</div>';
        activeContainer.innerHTML = html;
    } else {
        activeContainer.innerHTML = '<p class="text-muted text-center">No participant data</p>';
    }
}

function updateCharts(chartData) {
    // Destroy existing charts
    Object.values(charts).forEach(chart => chart.destroy());
    charts = {};
    
    // Bar Chart: Top 5 Events by Registrations
    const barCtx = document.getElementById('eventsBarChart').getContext('2d');
    charts.barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: chartData.bar_chart.labels,
            datasets: [{
                label: 'Registrations',
                data: chartData.bar_chart.data,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Line Chart: Registration Growth
    const lineCtx = document.getElementById('registrationLineChart').getContext('2d');
    charts.lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: chartData.line_chart.labels,
            datasets: [{
                label: 'Registrations',
                data: chartData.line_chart.data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Pie Chart: Event Categories
    const pieCtx = document.getElementById('categoriesPieChart').getContext('2d');
    charts.pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: chartData.pie_chart.labels,
            datasets: [{
                data: chartData.pie_chart.data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateParticipantLeaderboard(participants) {
    const container = document.getElementById('participantLeaderboard');
    if (participants && participants.length > 0) {
        let html = '<div class="table-responsive"><table class="table table-sm">';
        html += '<thead><tr><th>Rank</th><th>Participant</th><th>Events</th></tr></thead><tbody>';
        participants.forEach((participant, index) => {
            const rank = index + 1;
            const medal = rank === 1 ? '🥇' : rank === 2 ? '🥈' : rank === 3 ? '🥉' : rank;
            html += `
                <tr>
                    <td>${medal}</td>
                    <td>${participant.name}</td>
                    <td><span class="badge bg-primary">${participant.events_attended}</span></td>
                </tr>
            `;
        });
        html += '</tbody></table></div>';
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p class="text-muted text-center">No participant data available</p>';
    }
}

async function loadRecentActivity() {
    try {
        const response = await axios.get('/dashboard/recent-activities');
        if (response.data.status === 'success') {
            updateRecentActivity(response.data.data);
        }
    } catch (error) {
        console.error('Error loading recent activity:', error);
    }
}

function updateRecentActivity(activities) {
    const container = document.getElementById('recentActivity');
    
    if (!activities || activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="bi bi-clock-history fs-1"></i>
                <p class="mt-2">No recent activity</p>
            </div>
        `;
        return;
    }

    let html = '<div class="timeline">';
    activities.forEach(activity => {
        const timeAgo = moment(activity.timestamp).fromNow();
        html += `
            <div class="timeline-item mb-3">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-${activity.color} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="${activity.icon}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold">${activity.message}</div>
                        <small class="text-muted">${timeAgo}</small>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function startRealtimeUpdates() {
    // Load initial activity
    loadRecentActivity();
    
    // Update every 30 seconds
    activityInterval = setInterval(loadRecentActivity, 30000);
}

async function loadEventOptions() {
    try {
        const response = await axios.get('/user-management/api/events-created');
        if (response.data.status === 'success') {
            const events = response.data.data.events;
            const select1 = document.getElementById('event1Select');
            const select2 = document.getElementById('event2Select');
            
            events.forEach(event => {
                const option1 = new Option(event.title, event.id);
                const option2 = new Option(event.title, event.id);
                select1.add(option1);
                select2.add(option2);
            });
        }
    } catch (error) {
        console.error('Error loading event options:', error);
    }
}

document.getElementById('compareEventsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const event1 = document.getElementById('event1Select').value;
    const event2 = document.getElementById('event2Select').value;
    
    if (!event1 || !event2) {
        showToast('Please select both events to compare', 'error');
        return;
    }
    
    if (event1 === event2) {
        showToast('Please select different events to compare', 'error');
        return;
    }
    
    try {
        const response = await axios.post('/dashboard/compare-events', {
            event_1: event1,
            event_2: event2
        });
        
        if (response.data.status === 'success') {
            displayComparisonResults(response.data.data);
        }
    } catch (error) {
        console.error('Error comparing events:', error);
        showToast('Error comparing events', 'error');
    }
});

function displayComparisonResults(comparison) {
    const container = document.getElementById('comparisonResults');
    const event1 = comparison.event_1;
    const event2 = comparison.event_2;
    
    container.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">${event1.title}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">${event1.participants}</h4>
                                <small>Participants</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">${event1.check_in_rate.toFixed(1)}%</h4>
                                <small>Check-in Rate</small>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-1"><strong>Date:</strong> ${event1.date}</p>
                        <p class="mb-0"><strong>Location:</strong> ${event1.location}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">${event2.title}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">${event2.participants}</h4>
                                <small>Participants</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">${event2.check_in_rate.toFixed(1)}%</h4>
                                <small>Check-in Rate</small>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-1"><strong>Date:</strong> ${event2.date}</p>
                        <p class="mb-0"><strong>Location:</strong> ${event2.location}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <div class="alert alert-info">
                <strong>Winner:</strong> 
                ${event1.participants > event2.participants ? event1.title : event2.title} 
                has more participants, while 
                ${event1.check_in_rate > event2.check_in_rate ? event1.title : event2.title} 
                has a better check-in rate.
            </div>
        </div>
    `;
    
    container.style.display = 'block';
}

function refreshDashboard() {
    showToast('Refreshing dashboard...', 'info');
    loadDashboardData();
    loadRecentActivity();
}

function exportAnalytics() {
    showToast('Analytics export feature coming soon!', 'info');
}

function scheduleReport() {
    showToast('Report scheduling feature coming soon!', 'info');
}

function showToast(message, type = 'success') {
    Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: type === 'success' ? "#28a745" : type === 'error' ? "#dc3545" : "#17a2b8",
    }).showToast();
}

// Include moment.js for time formatting
if (typeof moment === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js';
    document.head.appendChild(script);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (activityInterval) {
        clearInterval(activityInterval);
    }
    Object.values(charts).forEach(chart => chart.destroy());
});
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8, #117a8b);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
}

.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 19px;
    top: 50px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #dee2e6;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: box-shadow 0.15s ease-in-out;
}

canvas {
    max-height: 300px;
}
</style>
@endsection
