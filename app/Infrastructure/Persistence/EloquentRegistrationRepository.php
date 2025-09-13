<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Registration\Entities\Registration;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use App\Domain\Registration\ValueObjects\RegistrationDate;
use App\Domain\Registration\ValueObjects\ParticipantName;
use App\Domain\Registration\ValueObjects\ParticipantMobile;
use App\Domain\Registration\ValueObjects\ParticipantEmail;
use App\Domain\Registration\ValueObjects\RegistrationRemark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EloquentRegistrationRepository implements RegistrationRepositoryInterface
{
    public function findById(int $id): ?Registration
    {
        try {
            $data = DB::table('registrations')
                ->leftJoin('events', 'registrations.event_id', '=', 'events.id')
                ->leftJoin('users', 'registrations.user_id', '=', 'users.id')
                ->select(
                    'registrations.*',
                    'events.title as event_title',
                    'users.firstName as participant_user_name'
                )
                ->where('registrations.id', $id)
                ->first();

            return $data ? $this->mapToEntity($data) : null;
        } catch (\Exception $e) {
            Log::error('Error finding registration by ID: ' . $e->getMessage());
            return null;
        }
    }

    public function findByEventOwnerId(int $ownerId): array
    {
        try {
            $results = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->leftJoin('users', 'registrations.user_id', '=', 'users.id')
                ->select(
                    'registrations.*',
                    'events.title as event_title',
                    'users.firstName as participant_user_name'
                )
                ->where('events.user_id', $ownerId)
                ->orderBy('registrations.created_at', 'desc')
                ->get();

            return $this->mapToEntities($results);
        } catch (\Exception $e) {
            Log::error('Error finding registrations by event owner ID: ' . $e->getMessage());
            return [];
        }
    }

    public function findByEventIdAndOwnerId(int $eventId, int $ownerId): array
    {
        try {
            $results = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->leftJoin('users', 'registrations.user_id', '=', 'users.id')
                ->select(
                    'registrations.*',
                    'events.title as event_title',
                    'users.firstName as participant_user_name'
                )
                ->where('registrations.event_id', $eventId)
                ->where('events.user_id', $ownerId)
                ->orderBy('registrations.created_at', 'desc')
                ->get();

            return $this->mapToEntities($results);
        } catch (\Exception $e) {
            Log::error('Error finding registrations by event ID and owner ID: ' . $e->getMessage());
            return [];
        }
    }

    public function updateCheckInStatus(int $registrationId, bool $checkedIn, int $ownerId): bool
    {
        try {
            // First verify that the user owns the event for this registration
            if (!$this->userOwnsEventForRegistration($registrationId, $ownerId)) {
                return false;
            }

            $updateData = [
                'checked_in' => $checkedIn,
                'updated_at' => now()
            ];

            if ($checkedIn) {
                $updateData['checked_in_at'] = now();
            } else {
                $updateData['checked_in_at'] = null;
            }

            $affected = DB::table('registrations')
                ->where('id', $registrationId)
                ->update($updateData);

            return $affected > 0;
        } catch (\Exception $e) {
            Log::error('Error updating check-in status: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteByIdAndOwnerId(int $registrationId, int $ownerId): bool
    {
        try {
            // First verify that the user owns the event for this registration
            if (!$this->userOwnsEventForRegistration($registrationId, $ownerId)) {
                return false;
            }

            $affected = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->where('registrations.id', $registrationId)
                ->where('events.user_id', $ownerId)
                ->delete();

            return $affected > 0;
        } catch (\Exception $e) {
            Log::error('Error deleting registration: ' . $e->getMessage());
            return false;
        }
    }

    public function save(Registration $registration): Registration
    {
        try {
            $data = [
                'date' => $registration->getDate()->getFormattedDate(),
                'name' => $registration->getName()->getValue(),
                'mobile' => $registration->getMobile()->getValue(),
                'email' => $registration->getEmail() ? $registration->getEmail()->getValue() : null,
                'remark' => $registration->getRemark() ? $registration->getRemark()->getValue() : null,
                'event_id' => $registration->getEventId(),
                'user_id' => $registration->getUserId(),
                'checked_in' => $registration->isCheckedIn(),
                'checked_in_at' => $registration->getCheckedInAt(),
                'updated_at' => now()
            ];

            if ($registration->getId() > 0) {
                // Update existing registration
                DB::table('registrations')
                    ->where('id', $registration->getId())
                    ->update($data);
            } else {
                // Create new registration
                $data['created_at'] = now();
                $id = DB::table('registrations')->insertGetId($data);
                
                // Return updated entity with new ID
                return $this->findById($id);
            }

            return $registration;
        } catch (\Exception $e) {
            Log::error('Error saving registration: ' . $e->getMessage());
            throw $e;
        }
    }

    public function userOwnsEventForRegistration(int $registrationId, int $userId): bool
    {
        try {
            $count = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->where('registrations.id', $registrationId)
                ->where('events.user_id', $userId)
                ->count();

            return $count > 0;
        } catch (\Exception $e) {
            Log::error('Error checking event ownership: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get participant statistics for a specific event
     */
    public function getEventParticipantStats(int $eventId): array
    {
        try {
            $stats = DB::table("registrations")
                ->where("event_id", $eventId)
                ->selectRaw("COUNT(*) as total_participants")
                ->selectRaw("SUM(CASE WHEN checked_in = 1 THEN 1 ELSE 0 END) as checked_in")
                ->selectRaw("SUM(CASE WHEN checked_in = 0 OR checked_in IS NULL THEN 1 ELSE 0 END) as not_checked_in")
                ->first();
            
            return [
                "total_participants" => (int) ($stats->total_participants ?? 0),
                "checked_in" => (int) ($stats->checked_in ?? 0),
                "not_checked_in" => (int) ($stats->not_checked_in ?? 0)
            ];
        } catch (\Exception $e) {
            Log::error("Error getting event participant stats: " . $e->getMessage());
            return [
                "total_participants" => 0,
                "checked_in" => 0,
                "not_checked_in" => 0
            ];
        }
    }

    /**
     * Get participants statistics for multiple events (global stats - no filters)
     */
    public function getParticipantsStatistics(array $eventIds): array
    {
        try {
            if (empty($eventIds)) {
                return [
                    "total_participants" => 0,
                    "checked_in" => 0,
                    "not_checked_in" => 0
                ];
            }
            
            $stats = DB::table("registrations")
                ->whereIn("event_id", $eventIds)
                ->selectRaw("COUNT(*) as total_participants")
                ->selectRaw("SUM(CASE WHEN checked_in = 1 THEN 1 ELSE 0 END) as checked_in")
                ->selectRaw("SUM(CASE WHEN checked_in = 0 OR checked_in IS NULL THEN 1 ELSE 0 END) as not_checked_in")
                ->first();
            
            return [
                "total_participants" => (int) ($stats->total_participants ?? 0),
                "checked_in" => (int) ($stats->checked_in ?? 0),
                "not_checked_in" => (int) ($stats->not_checked_in ?? 0)
            ];
        } catch (\Exception $e) {
            Log::error("Error getting participants statistics: " . $e->getMessage());
            return [
                "total_participants" => 0,
                "checked_in" => 0,
                "not_checked_in" => 0
            ];
        }
    }

    /**
     * Find participants by event IDs with pagination, filtering, and search
     */
    public function findParticipantsByEventIds(
        array $eventIds,
        ?int $eventId = null,
        ?string $status = null,
        ?string $search = null,
        int $page = 1,
        int $perPage = 10
    ): array {
        try {
            if (empty($eventIds)) {
                return [];
            }

            $query = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->leftJoin('users', 'registrations.user_id', '=', 'users.id')
                ->select(
                    'registrations.id',
                    'registrations.name',
                    'registrations.email',
                    'registrations.mobile',
                    'registrations.remark',
                    'registrations.checked_in',
                    'registrations.checked_in_at',
                    'registrations.created_at',
                    'registrations.updated_at',
                    'events.id as event_id',
                    'events.title as event_title',
                    'events.date as event_date',
                    'events.location as event_location',
                    'users.firstName as participant_user_name'
                )
                ->whereIn('registrations.event_id', $eventIds);

            // Apply event filter if specified
            if ($eventId) {
                $query->where('registrations.event_id', $eventId);
            }

            // Apply status filter if specified
            if ($status === 'checked_in') {
                $query->where('registrations.checked_in', 1);
            } elseif ($status === 'not_checked_in') {
                $query->where(function($q) {
                    $q->where('registrations.checked_in', 0)
                      ->orWhereNull('registrations.checked_in');
                });
            }

            // Apply search filter if specified
            if ($search && trim($search) !== '') {
                $searchTerm = '%' . trim($search) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('registrations.name', 'LIKE', $searchTerm)
                      ->orWhere('registrations.email', 'LIKE', $searchTerm)
                      ->orWhere('events.title', 'LIKE', $searchTerm);
                });
            }

            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $results = $query
                ->orderBy('registrations.created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Convert to array format expected by frontend
            return $results->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'email' => $participant->email ?? '',
                    'mobile' => $participant->mobile ?? '',
                    'remark' => $participant->remark ?? '',
                    'checked_in' => (bool) $participant->checked_in,
                    'checked_in_at' => $participant->checked_in_at,
                    'created_at' => $participant->created_at,
                    'updated_at' => $participant->updated_at,
                    'event_id' => $participant->event_id,
                    'event_title' => $participant->event_title,
                    'event_date' => $participant->event_date,
                    'event_location' => $participant->event_location,
                    'participant_user_name' => $participant->participant_user_name ?? 'Unknown User'
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('Error finding participants by event IDs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count participants by event IDs with filtering and search
     */
    public function countParticipantsByEventIds(
        array $eventIds,
        ?int $eventId = null,
        ?string $status = null,
        ?string $search = null
    ): int {
        try {
            if (empty($eventIds)) {
                return 0;
            }

            $query = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->whereIn('registrations.event_id', $eventIds);

            // Apply event filter if specified
            if ($eventId) {
                $query->where('registrations.event_id', $eventId);
            }

            // Apply status filter if specified
            if ($status === 'checked_in') {
                $query->where('registrations.checked_in', 1);
            } elseif ($status === 'not_checked_in') {
                $query->where(function($q) {
                    $q->where('registrations.checked_in', 0)
                      ->orWhereNull('registrations.checked_in');
                });
            }

            // Apply search filter if specified
            if ($search && trim($search) !== '') {
                $searchTerm = '%' . trim($search) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('registrations.name', 'LIKE', $searchTerm)
                      ->orWhere('registrations.email', 'LIKE', $searchTerm)
                      ->orWhere('events.title', 'LIKE', $searchTerm);
                });
            }

            return $query->count();

        } catch (\Exception $e) {
            Log::error('Error counting participants by event IDs: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * NEW: Get filtered participants statistics (respects all filters including search)
     * This is the key method that makes statistics follow the filters!
     */
    public function getFilteredParticipantsStatistics(
        array $eventIds,
        ?int $eventId = null,
        ?string $status = null,
        ?string $search = null
    ): array {
        try {
            if (empty($eventIds)) {
                return [
                    "total_participants" => 0,
                    "checked_in" => 0,
                    "not_checked_in" => 0
                ];
            }

            $query = DB::table('registrations')
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->whereIn('registrations.event_id', $eventIds);

            // Apply event filter if specified
            if ($eventId) {
                $query->where('registrations.event_id', $eventId);
            }

            // Apply search filter if specified (but NOT status filter for statistics)
            if ($search && trim($search) !== '') {
                $searchTerm = '%' . trim($search) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('registrations.name', 'LIKE', $searchTerm)
                      ->orWhere('registrations.email', 'LIKE', $searchTerm)
                      ->orWhere('events.title', 'LIKE', $searchTerm);
                });
            }

            // Get statistics for the filtered results
            $stats = $query
                ->selectRaw("COUNT(*) as total_participants")
                ->selectRaw("SUM(CASE WHEN registrations.checked_in = 1 THEN 1 ELSE 0 END) as checked_in")
                ->selectRaw("SUM(CASE WHEN registrations.checked_in = 0 OR registrations.checked_in IS NULL THEN 1 ELSE 0 END) as not_checked_in")
                ->first();
            
            return [
                "total_participants" => (int) ($stats->total_participants ?? 0),
                "checked_in" => (int) ($stats->checked_in ?? 0),
                "not_checked_in" => (int) ($stats->not_checked_in ?? 0)
            ];

        } catch (\Exception $e) {
            Log::error("Error getting filtered participants statistics: " . $e->getMessage());
            return [
                "total_participants" => 0,
                "checked_in" => 0,
                "not_checked_in" => 0
            ];
        }
    }

    private function mapToEntity($data): Registration
    {
        $registration = new Registration(
            id: $data->id,
            date: new RegistrationDate($data->date),
            name: new ParticipantName($data->name),
            mobile: new ParticipantMobile($data->mobile),
            email: $data->email ? new ParticipantEmail($data->email) : null,
            remark: $data->remark ? new RegistrationRemark($data->remark) : null,
            eventId: $data->event_id,
            userId: $data->user_id,
            checkedIn: (bool) ($data->checked_in ?? false),
            checkedInAt: $data->checked_in_at ? new \DateTime($data->checked_in_at) : null,
            createdAt: new \DateTime($data->created_at),
            updatedAt: new \DateTime($data->updated_at)
        );

        // Set additional display properties
        if (isset($data->event_title)) {
            $registration->setEventTitle($data->event_title);
        }
        
        if (isset($data->participant_user_name)) {
            $registration->setParticipantUserName($data->participant_user_name);
        }

        return $registration;
    }

    private function mapToEntities($results): array
    {
        $entities = [];
        
        foreach ($results as $data) {
            try {
                $entities[] = $this->mapToEntity($data);
            } catch (\Exception $e) {
                Log::error('Error mapping registration entity, skipping: ' . $e->getMessage());
                continue;
            }
        }
        
        return $entities;
    }
}
