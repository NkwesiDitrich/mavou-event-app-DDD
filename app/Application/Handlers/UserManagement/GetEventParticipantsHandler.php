<?php

namespace App\Application\Handlers\UserManagement;

use App\Application\Queries\UserManagement\GetEventParticipantsQuery;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use App\Domain\Event\Repositories\EventRepositoryInterface;

class GetEventParticipantsHandler
{
    public function __construct(
        private readonly RegistrationRepositoryInterface $registrationRepository,
        private readonly EventRepositoryInterface $eventRepository
    ) {}

    public function handle(GetEventParticipantsQuery $query): array
    {
        // First, get all events owned by the user to ensure security
        $userEvents = $this->eventRepository->findByUserId($query->userId);
        $userEventIds = array_map(fn($event) => $event->getId(), $userEvents);

        if (empty($userEventIds)) {
            return [
                'participants' => [],
                'total' => 0,
                'events' => [],
                'statistics' => [
                    'total_participants' => 0,
                    'checked_in' => 0,
                    'not_checked_in' => 0
                ]
            ];
        }

        // Get participants for user's events only with search
        $participants = $this->registrationRepository->findParticipantsByEventIds(
            $userEventIds,
            $query->eventId,
            $query->status,
            $query->search,
            $query->page,
            $query->perPage
        );

        // Get total count for pagination with same filters
        $totalCount = $this->registrationRepository->countParticipantsByEventIds(
            $userEventIds,
            $query->eventId,
            $query->status,
            $query->search
        );

        // Get statistics with same filters applied (this is the key fix!)
        $statistics = $this->registrationRepository->getFilteredParticipantsStatistics(
            $userEventIds,
            $query->eventId,
            $query->status,
            $query->search
        );

        // Format events for dropdown
        $eventsForDropdown = array_map(function($event) {
            return [
                'id' => $event->getId(),
                'title' => $event->getTitle()->getValue(),
                'date' => $event->getDate()->getFormattedDate(),
                'location' => $event->getLocation()->getValue()
            ];
        }, $userEvents);

        return [
            'participants' => $participants,
            'total' => $totalCount,
            'events' => $eventsForDropdown,
            'statistics' => $statistics,
            'pagination' => [
                'current_page' => $query->page,
                'per_page' => $query->perPage,
                'total_pages' => ceil($totalCount / $query->perPage),
                'has_more' => ($query->page * $query->perPage) < $totalCount
            ]
        ];
    }
}
