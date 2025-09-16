<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use Carbon\Carbon;

class EnhancedDashboardController extends Controller
{
    private EventRepositoryInterface $eventRepository;
    private RegistrationRepositoryInterface $registrationRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        RegistrationRepositoryInterface $registrationRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->registrationRepository = $registrationRepository;
    }

    public function enhancedDashboard(): View
    {
        return view('backend.pages.dashboard.enhanced-dashboard');
    }

    public function getDashboardAnalytics(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $currentMonth = Carbon::now()->startOfMonth();
            $previousMonth = Carbon::now()->subMonth()->startOfMonth();
            
            // Get user's events
            $userEvents = $this->eventRepository->findByUserId($userId);
            $eventIds = collect($userEvents)->pluck('id')->toArray();
            
            // Core Statistics & KPIs
            $coreStats = $this->getCoreStatistics($userEvents, $eventIds, $currentMonth, $previousMonth);
            
            // Popularity Insights
            $popularityInsights = $this->getPopularityInsights($userEvents, $eventIds);
            
            // Engagement & Audience Analysis
            $engagementAnalysis = $this->getEngagementAnalysis($eventIds);
            
            // Chart Data
            $chartData = $this->getChartData($userEvents, $eventIds);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'core_stats' => $coreStats,
                    'popularity_insights' => $popularityInsights,
                    'engagement_analysis' => $engagementAnalysis,
                    'chart_data' => $chartData,
                    'last_updated' => Carbon::now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load dashboard analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRecentActivities(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $userEvents = $this->eventRepository->findByUserId($userId);
            $eventIds = collect($userEvents)->pluck('id')->toArray();
            
            // Get recent registrations (last 24 hours)
            $recentRegistrations = $this->getRecentRegistrations($eventIds);
            
            // Get recent check-ins (last 24 hours)
            $recentCheckIns = $this->getRecentCheckIns($eventIds);
            
            // Get upcoming events (next 7 days)
            $upcomingEvents = $this->getUpcomingEvents($userEvents);
            
            $activities = array_merge($recentRegistrations, $recentCheckIns, $upcomingEvents);
            
            // Sort by timestamp descending
            usort($activities, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            return response()->json([
                'status' => 'success',
                'data' => array_slice($activities, 0, 10) // Latest 10 activities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load recent activities: ' . $e->getMessage()
            ], 500);
        }
    }

    public function compareEvents(Request $request): JsonResponse
    {
        try {
            $eventId1 = $request->input('event_1');
            $eventId2 = $request->input('event_2');
            $userId = auth()->id();
            
            // Validate that user owns both events
            $event1 = $this->eventRepository->findById($eventId1);
            $event2 = $this->eventRepository->findById($eventId2);
            
            if (!$event1 || !$event2 || 
                !$event1->belongsToUser($userId) || 
                !$event2->belongsToUser($userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid events selected for comparison'
                ], 400);
            }
            
            $comparison = $this->getEventComparison($event1, $event2);
            
            return response()->json([
                'status' => 'success',
                'data' => $comparison
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to compare events: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCoreStatistics($userEvents, $eventIds, $currentMonth, $previousMonth): array
    {
        $currentMonthEvents = collect($userEvents)->filter(function($event) use ($currentMonth) {
            return Carbon::parse($event->getCreatedAt())->gte($currentMonth);
        })->count();
        
        $previousMonthEvents = collect($userEvents)->filter(function($event) use ($previousMonth, $currentMonth) {
            return Carbon::parse($event->getCreatedAt())->gte($previousMonth) && 
                   Carbon::parse($event->getCreatedAt())->lt($currentMonth);
        })->count();
        
        $growthPercentage = $previousMonthEvents > 0 ? 
            (($currentMonthEvents - $previousMonthEvents) / $previousMonthEvents) * 100 : 
            ($currentMonthEvents > 0 ? 100 : 0);
        
        // Get registration statistics
        $registrationStats = $this->registrationRepository->getParticipantsStatistics($eventIds);
        
        // Calculate average participants per event
        $totalEvents = count($userEvents);
        $avgParticipants = $totalEvents > 0 ? 
            ($registrationStats['total_participants'] ?? 0) / $totalEvents : 0;
        
        // Calculate check-in rate
        $totalParticipants = $registrationStats['total_participants'] ?? 0;
        $checkedIn = $registrationStats['checked_in'] ?? 0;
        $checkInRate = $totalParticipants > 0 ? ($checkedIn / $totalParticipants) * 100 : 0;
        
        return [
            'total_events' => [
                'current_month' => $currentMonthEvents,
                'previous_month' => $previousMonthEvents,
                'growth_percentage' => round($growthPercentage, 1),
                'total' => $totalEvents
            ],
            'total_registrations' => [
                'count' => $totalParticipants,
                'trend' => $this->getRegistrationTrend($eventIds)
            ],
            'average_participants' => round($avgParticipants, 1),
            'check_in_rate' => round($checkInRate, 1)
        ];
    }

    private function getPopularityInsights($userEvents, $eventIds): array
    {
        // Get events with participant counts
        $eventsWithStats = [];
        foreach ($userEvents as $event) {
            $stats = $this->registrationRepository->getEventParticipantStats($event->getId());
            $eventsWithStats[] = [
                'event' => $event,
                'participants' => $stats['total_participants'] ?? 0,
                'checked_in' => $stats['checked_in'] ?? 0
            ];
        }
        
        // Sort by participants count
        usort($eventsWithStats, function($a, $b) {
            return $b['participants'] - $a['participants'];
        });
        
        $mostPopular = $eventsWithStats[0] ?? null;
        $top5Upcoming = $this->getTop5UpcomingEvents($eventsWithStats);
        $trendingEvents = $this->getTrendingEvents($eventsWithStats);
        $mostActiveParticipants = $this->getMostActiveParticipants($eventIds);
        
        return [
            'most_popular_event' => $mostPopular ? [
                'title' => $mostPopular['event']->getTitle()->getValue(),
                'participants' => $mostPopular['participants'],
                'date' => $mostPopular['event']->getDate()->getFormattedDate(),
                'location' => $mostPopular['event']->getLocation()->getValue()
            ] : null,
            'top_5_upcoming' => $top5Upcoming,
            'trending_events' => $trendingEvents,
            'most_active_participants' => $mostActiveParticipants
        ];
    }

    private function getEngagementAnalysis($eventIds): array
    {
        // This would require additional data collection in a real implementation
        // For now, we'll provide basic engagement metrics
        
        $totalRegistrations = 0;
        $repeatParticipants = 0;
        $newParticipants = 0;
        
        // In a real implementation, you'd analyze participant email/phone patterns
        // to determine repeat vs new participants
        
        return [
            'repeat_vs_new' => [
                'repeat_participants' => $repeatParticipants,
                'new_participants' => $newParticipants,
                'repeat_percentage' => $totalRegistrations > 0 ? 
                    ($repeatParticipants / $totalRegistrations) * 100 : 0
            ],
            'demographics' => [
                'note' => 'Demographics data would require additional user profile fields'
            ]
        ];
    }

    private function getChartData($userEvents, $eventIds): array
    {
        // Bar Chart: Top 5 events by registrations
        $eventsWithStats = [];
        foreach (array_slice($userEvents, 0, 5) as $event) {
            $stats = $this->registrationRepository->getEventParticipantStats($event->getId());
            $eventsWithStats[] = [
                'label' => substr($event->getTitle()->getValue(), 0, 20) . '...',
                'value' => $stats['total_participants'] ?? 0
            ];
        }
        
        // Line Chart: Registrations growth over months
        $monthlyData = $this->getMonthlyRegistrationData($eventIds);
        
        // Pie Chart: Event categories participation
        $categoryData = $this->getCategoryParticipationData($userEvents);
        
        return [
            'bar_chart' => [
                'labels' => array_column($eventsWithStats, 'label'),
                'data' => array_column($eventsWithStats, 'value')
            ],
            'line_chart' => $monthlyData,
            'pie_chart' => $categoryData
        ];
    }

    private function getRegistrationTrend($eventIds): array
    {
        // Simple trend calculation - in real implementation, you'd analyze historical data
        return [
            'direction' => 'up', // 'up', 'down', 'stable'
            'percentage' => 15.5
        ];
    }

    private function getTop5UpcomingEvents($eventsWithStats): array
    {
        $upcoming = array_filter($eventsWithStats, function($item) {
            return $item['event']->isUpcoming();
        });
        
        usort($upcoming, function($a, $b) {
            return $b['participants'] - $a['participants'];
        });
        
        return array_slice(array_map(function($item) {
            return [
                'title' => $item['event']->getTitle()->getValue(),
                'participants' => $item['participants'],
                'date' => $item['event']->getDate()->getFormattedDate()
            ];
        }, $upcoming), 0, 5);
    }

    private function getTrendingEvents($eventsWithStats): array
    {
        // In a real implementation, you'd calculate registration velocity
        // For now, return recent events with good registration numbers
        return array_slice(array_map(function($item) {
            return [
                'title' => $item['event']->getTitle()->getValue(),
                'participants' => $item['participants'],
                'growth_rate' => rand(10, 50) // Mock growth rate
            ];
        }, $eventsWithStats), 0, 3);
    }

    private function getMostActiveParticipants($eventIds): array
    {
        // This would require complex queries to find participants across multiple events
        // Mock data for demonstration
        return [
            ['name' => 'John Doe', 'events_attended' => 5],
            ['name' => 'Jane Smith', 'events_attended' => 4],
            ['name' => 'Mike Johnson', 'events_attended' => 3]
        ];
    }

    private function getRecentRegistrations($eventIds): array
    {
        // Mock recent registrations - in real implementation, query recent registrations
        return [
            [
                'type' => 'registration',
                'message' => 'New registration for Tech Conference 2025',
                'participant' => 'Alice Johnson',
                'timestamp' => Carbon::now()->subHours(2)->toISOString(),
                'icon' => 'bi-person-plus',
                'color' => 'success'
            ]
        ];
    }

    private function getRecentCheckIns($eventIds): array
    {
        // Mock recent check-ins
        return [
            [
                'type' => 'checkin',
                'message' => 'Participant checked in to Business Summit',
                'participant' => 'Bob Wilson',
                'timestamp' => Carbon::now()->subHours(1)->toISOString(),
                'icon' => 'bi-person-check',
                'color' => 'info'
            ]
        ];
    }

    private function getUpcomingEvents($userEvents): array
    {
        $upcoming = array_filter($userEvents, function($event) {
            return $event->isUpcoming() && $event->getDaysUntilEvent() <= 7;
        });
        
        return array_map(function($event) {
            return [
                'type' => 'upcoming',
                'message' => 'Upcoming event: ' . $event->getTitle()->getValue(),
                'event' => $event->getTitle()->getValue(),
                'timestamp' => $event->getDate()->getFormattedDate(),
                'icon' => 'bi-calendar-event',
                'color' => 'warning'
            ];
        }, array_slice($upcoming, 0, 3));
    }

    private function getMonthlyRegistrationData($eventIds): array
    {
        // Mock monthly data - in real implementation, aggregate by month
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $data = [12, 19, 15, 25, 22, 30];
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getCategoryParticipationData($userEvents): array
    {
        // Mock category data - in real implementation, group by category
        return [
            'labels' => ['Technology', 'Business', 'Social', 'Education'],
            'data' => [35, 25, 20, 20]
        ];
    }

    private function getEventComparison($event1, $event2): array
    {
        $stats1 = $this->registrationRepository->getEventParticipantStats($event1->getId());
        $stats2 = $this->registrationRepository->getEventParticipantStats($event2->getId());
        
        return [
            'event_1' => [
                'title' => $event1->getTitle()->getValue(),
                'participants' => $stats1['total_participants'] ?? 0,
                'checked_in' => $stats1['checked_in'] ?? 0,
                'check_in_rate' => $stats1['total_participants'] > 0 ? 
                    (($stats1['checked_in'] ?? 0) / $stats1['total_participants']) * 100 : 0,
                'date' => $event1->getDate()->getFormattedDate(),
                'location' => $event1->getLocation()->getValue()
            ],
            'event_2' => [
                'title' => $event2->getTitle()->getValue(),
                'participants' => $stats2['total_participants'] ?? 0,
                'checked_in' => $stats2['checked_in'] ?? 0,
                'check_in_rate' => $stats2['total_participants'] > 0 ? 
                    (($stats2['checked_in'] ?? 0) / $stats2['total_participants']) * 100 : 0,
                'date' => $event2->getDate()->getFormattedDate(),
                'location' => $event2->getLocation()->getValue()
            ]
        ];
    }
}
