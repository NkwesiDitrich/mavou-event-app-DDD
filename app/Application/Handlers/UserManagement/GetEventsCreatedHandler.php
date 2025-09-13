<?php

namespace App\Application\Handlers\UserManagement;

use App\Application\Queries\UserManagement\GetEventsCreatedQuery;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;

class GetEventsCreatedHandler
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly RegistrationRepositoryInterface $registrationRepository
    ) {}

    public function handle(GetEventsCreatedQuery $query): array
    {
        // Get events created by the user with filters
        $events = $this->eventRepository->findByUserIdWithFilters(
            $query->userId,
            $query->status,
            $query->search, // NEW: Pass search parameter
            $query->page,
            $query->perPage
        );

        // Get total count for pagination (with filters applied)
        $totalCount = $this->eventRepository->countByUserIdWithFilters(
            $query->userId,
            $query->status,
            $query->search // NEW: Pass search parameter
        );

        // Enhance events with participant statistics
        $eventsWithStats = [];
        $filteredEventIds = []; // Track filtered event IDs for statistics
        
        foreach ($events as $event) {
            $participantStats = $this->registrationRepository->getEventParticipantStats($event->getId());
            $filteredEventIds[] = $event->getId();
            
            $eventsWithStats[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle()->getValue(),
                'description' => $event->getDescription()->getValue(),
                'date' => $event->getDate()->getFormattedDate(),
                'time' => $event->getTime()->getValue(),
                'location' => $event->getLocation()->getValue(),
                'type' => $event->getType()->getValue(),
                'image' => $event->getImage(),
                'category_id' => $event->getCategoryId(),
                'created_at' => $event->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $event->getUpdatedAt()->format('Y-m-d H:i:s'),
                'is_upcoming' => $event->isUpcoming(),
                'is_past' => $event->isPast(),
                'is_today' => $event->isToday(),
                'days_until_event' => $event->getDaysUntilEvent(),
                'participant_stats' => [
                    'total_participants' => $participantStats['total_participants'],
                    'checked_in' => $participantStats['checked_in'],
                    'not_checked_in' => $participantStats['not_checked_in'],
                    'check_in_rate' => $participantStats['total_participants'] > 0 
                        ? round(($participantStats['checked_in'] / $participantStats['total_participants']) * 100, 1)
                        : 0
                ]
            ];
        }

        // FIXED: Get statistics for FILTERED events only (not all user events)
        $overallStats = !empty($filteredEventIds) 
            ? $this->registrationRepository->getParticipantsStatistics($filteredEventIds)
            : [
                'total_participants' => 0,
                'checked_in' => 0,
                'not_checked_in' => 0
            ];

        // FIXED: Calculate statistics based on FILTERED events
        $upcomingCount = count(array_filter($eventsWithStats, fn($e) => $e['is_upcoming']));
        $pastCount = count(array_filter($eventsWithStats, fn($e) => $e['is_past']));
        $todayCount = count(array_filter($eventsWithStats, fn($e) => $e['is_today']));

        return [
            'events' => $eventsWithStats,
            'total' => $totalCount,
            'statistics' => [
                'total_events' => $totalCount, // Total filtered events
                'upcoming_events' => $upcomingCount, // Upcoming filtered events
                'past_events' => $pastCount, // Past filtered events
                'today_events' => $todayCount, // Today filtered events
                'overall_participants' => $overallStats // Participants from filtered events only
            ],
            'pagination' => [
                'current_page' => $query->page,
                'per_page' => $query->perPage,
                'total_pages' => ceil($totalCount / $query->perPage),
                'has_more' => ($query->page * $query->perPage) < $totalCount
            ]
        ];
    }
}
