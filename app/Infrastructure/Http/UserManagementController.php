<?php

namespace App\Infrastructure\Http;

use App\Http\Controllers\Controller;
use App\Application\Commands\Registration\CheckInParticipantCommand;
use App\Application\Commands\Registration\UnattendParticipantCommand;
use App\Application\Queries\Registration\GetUserRegistrationsQuery;
use App\Application\Handlers\Registration\CheckInParticipantHandler;
use App\Application\Handlers\Registration\UnattendParticipantHandler;
use App\Application\Handlers\Registration\GetUserRegistrationsHandler;
use App\Infrastructure\Persistence\EloquentEventRepository;

// NEW: Import the enhanced User Management handlers
use App\Application\Handlers\UserManagement\GetEventParticipantsHandler;
use App\Application\Handlers\UserManagement\GetEventsCreatedHandler;
use App\Application\Queries\UserManagement\GetEventParticipantsQuery;
use App\Application\Queries\UserManagement\GetEventsCreatedQuery;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function __construct(
        private CheckInParticipantHandler $checkInHandler,
        private UnattendParticipantHandler $unattendHandler,
        private GetUserRegistrationsHandler $getUserRegistrationsHandler,
        private EloquentEventRepository $eventRepository,
        // NEW: Add the enhanced handlers
        private GetEventParticipantsHandler $getEventParticipantsHandler,
        private GetEventsCreatedHandler $getEventsCreatedHandler
    ) {}

    // ========================================
    // EXISTING METHODS (Keep as they are)
    // ========================================

    /**
     * Display the User Management page (EXISTING - Keep this)
     */
    public function UserManagementPage(Request $request): View
    {
        try {
            $userId = auth()->id();
            $eventId = $request->get('event_id');

            // Get registrations for this user's events
            $query = new GetUserRegistrationsQuery($userId, $eventId);
            $registrations = $this->getUserRegistrationsHandler->handle($query);

            // Get user's events for the filter dropdown
            $events = $this->getViewCompatibleEvents($userId);

            return view('backend.pages.dashboard.user-management-page', compact('registrations', 'events', 'eventId'));
        } catch (\Exception $e) {
            Log::error('User Management page error: ' . $e->getMessage());
            
            // Provide fallback data
            $registrations = [];
            $events = collect([]);
            $eventId = null;
            
            return view('backend.pages.dashboard.user-management-page', compact('registrations', 'events', 'eventId'))
                ->with('error', 'Error loading user management data');
        }
    }

    /**
     * Get list of registrations for API (ENHANCED - Now supports search_user parameter)
     */
    public function RegistrationList(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $eventId = $request->get('event_id');
            $searchUser = $request->get('search_user'); // NEW: Support for searching specific user

            $query = new GetUserRegistrationsQuery($userId, $eventId);
            $registrations = $this->getUserRegistrationsHandler->handle($query);

            // Filter registrations by specific user if search_user is provided
            if ($searchUser) {
                $registrations = array_filter($registrations, function($registration) use ($searchUser) {
                    $name = $registration->getName()->getValue();
                    $email = $registration->getEmail() ? $registration->getEmail()->getValue() : '';
                    
                    // Match by email (exact) or name (partial)
                    return (strcasecmp($email, $searchUser) === 0) || 
                           (stripos($name, $searchUser) !== false);
                });
            }

            // Convert to array format expected by frontend
            $registrationsArray = array_map(function($registration) {
                return [
                    'id' => $registration->getId(),
                    'name' => $registration->getName()->getValue(),
                    'email' => $registration->getEmail() ? $registration->getEmail()->getValue() : '',
                    'mobile' => $registration->getMobile()->getValue(),
                    'remark' => $registration->getRemark() ? $registration->getRemark()->getValue() : '',
                    'checked_in' => $registration->isCheckedIn(),
                    'checked_in_at' => $registration->getCheckedInAt() ? $registration->getCheckedInAt()->format('Y-m-d H:i:s') : null,
                    'created_at' => $registration->getCreatedAt()->format('Y-m-d H:i:s'),
                    'event_title' => $registration->getEventTitle() ?? 'Unknown Event',
                    'participant_user_name' => $registration->getParticipantUserName() ?? 'Unknown User'
                ];
            }, $registrations);

            return response()->json([
                'status' => 'success',
                'data' => array_values($registrationsArray) // Re-index array after filtering
            ]);
        } catch (\Exception $e) {
            Log::error('Registration list error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load registrations'
            ], 500);
        }
    }

    // ========================================
    // NEW ENHANCED METHODS
    // ========================================

    /**
     * NEW: User Management Dashboard (Dropdown entry point)
     */
    public function UserManagementDashboard(): View
    {
        return view('backend.pages.dashboard.user-management-dashboard');
    }

    /**
     * NEW: Event Participants Page (Dropdown option 1)
     */
    public function EventParticipantsPage(): View
    {
        return view('backend.pages.dashboard.event-participants-page');
    }

    /**
     * NEW: Events Created Page (Dropdown option 2)
     */
    public function EventsCreatedPage(): View
    {
        return view('backend.pages.dashboard.events-created-page');
    }

    /**
     * NEW: Get Event Participants (AJAX endpoint) - UPDATED with search support
     */
    public function GetEventParticipants(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $eventId = $request->get('event_id') ? (int) $request->get('event_id') : null;
            $status = $request->get('status');
            $search = $request->get('search'); // NEW: Add search parameter
            $page = (int) $request->get('page', 1);
            $perPage = (int) $request->get('per_page', 10);

            $query = new GetEventParticipantsQuery($userId, $eventId, $status, $search, $page, $perPage);
            $result = $this->getEventParticipantsHandler->handle($query);

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Get event participants error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load event participants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ENHANCED: Get Events Created (AJAX endpoint) - NOW with search support
     */
    public function GetEventsCreated(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $status = $request->get('status');
            $search = $request->get('search'); // NEW: Add search parameter
            $page = (int) $request->get('page', 1);
            $perPage = (int) $request->get('per_page', 12);

            $query = new GetEventsCreatedQuery($userId, $status, $search, $page, $perPage);
            $result = $this->getEventsCreatedHandler->handle($query);

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Get events created error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load events created: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * NEW: Get Event Participants by Event ID (for modal)
     */
    public function GetEventParticipantsByEventId(Request $request, $eventId): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $query = new GetEventParticipantsQuery($userId, (int) $eventId, null, null, 1, 100);
            $result = $this->getEventParticipantsHandler->handle($query);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'participants' => $result['participants'] ?? []
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get event participants by ID error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load event participants'
            ], 500);
        }
    }

    // ========================================
    // EXISTING METHODS (Keep as they are)
    // ========================================

    /**
     * Check in a participant (EXISTING - Keep this, but enhance it)
     */
    public function CheckInParticipant(Request $request): JsonResponse
    {
        try {
            $registrationId = $request->input('registration_id');
            $checkIn = $request->input('check_in', true);
            $userId = auth()->id();

            if (!$registrationId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registration ID is required'
                ], 400);
            }

            $command = new CheckInParticipantCommand($registrationId, $userId, $checkIn);
            $success = $this->checkInHandler->handle($command);

            if ($success) {
                $action = $checkIn ? 'checked in' : 'checked out';
                return response()->json([
                    'status' => 'success',
                    'message' => "Participant successfully {$action}"
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update participant status'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Check-in participant error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update participant status'
            ], 500);
        }
    }

    /**
     * Remove a participant (unattend) (EXISTING - Keep this)
     */
    public function UnattendParticipant(Request $request): JsonResponse
    {
        try {
            $registrationId = $request->input('registration_id');
            $userId = auth()->id();

            if (!$registrationId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registration ID is required'
                ], 400);
            }

            $command = new UnattendParticipantCommand($registrationId, $userId);
            $success = $this->unattendHandler->handle($command);

            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Participant successfully removed'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to remove participant'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Unattend participant error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove participant'
            ], 500);
        }
    }

    // ========================================
    // HELPER METHODS (Keep as they are)
    // ========================================

    /**
     * Get events in a format compatible with the view
     */
    private function getViewCompatibleEvents(int $userId)
    {
        try {
            $events = $this->eventRepository->findByUserId($userId);
            
            return collect($events)->map(function($event) {
                return (object) [
                    'id' => $event->getId(),
                    'title' => $event->getTitle()->getValue(),
                    'date' => $event->getDate()->getFormattedDate(),
                    'location' => $event->getLocation()->getValue()
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting view compatible events: ' . $e->getMessage());
            return collect([]);
        }
    }
}
