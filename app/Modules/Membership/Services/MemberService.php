<?php

declare(strict_types=1);

namespace App\Modules\Membership\Services;

use App\Modules\Membership\Models\{Member, Family};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MemberService
{
    /**
     * Create a new member with optional family creation
     */
    public function createMember(array $data, ?TemporaryUploadedFile $avatar = null): Member
    {
        return DB::transaction(function () use ($data, $avatar) {
            // Handle family creation if needed
            if (!empty($data['new_family_name'])) {
                $family = Family::create([
                    'name' => $data['new_family_name'],
                    'address' => $data['new_family_address'] ?? null,
                ]);
                $data['family_id'] = $family->id;
            }

            // Handle avatar upload
            if ($avatar) {
                $data['avatar'] = $avatar->store('avatars', 'public');
            }

            // Create member
            $member = Member::create($data);

            // Sync groups if provided
            if (!empty($data['groups'])) {
                $this->syncMemberGroups($member, $data['groups']);
            }

            return $member;
        });
    }

    /**
     * Update an existing member
     */
    public function updateMember(int $memberId, array $data, ?TemporaryUploadedFile $avatar = null): Member
    {
        return DB::transaction(function () use ($memberId, $data, $avatar) {
            $member = Member::findOrFail($memberId);

            // Handle avatar upload
            if ($avatar) {
                // Delete old avatar if exists
                if ($member->avatar) {
                    Storage::disk('public')->delete($member->avatar);
                }
                $data['avatar'] = $avatar->store('avatars', 'public');
            }

            // Update member
            $member->update($data);

            // Sync groups if provided
            if (isset($data['groups'])) {
                $this->syncMemberGroups($member, $data['groups']);
            }

            return $member->fresh();
        });
    }

    /**
     * Sync member groups with roles and sub-groups
     */
    public function syncMemberGroups(Member $member, array $groupsData): void
    {
        $syncData = [];
        foreach ($groupsData as $groupId => $groupInfo) {
            $syncData[$groupId] = [
                'role' => $groupInfo['role'] ?? 'thành viên',
                'sub_group' => $groupInfo['sub_group'] ?? null,
            ];
        }
        $member->groups()->sync($syncData);
    }

    /**
     * Get members with eager loaded relationships
     */
    public function getMembersWithRelations(
        ?string $search = null,
        ?string $statusFilter = null,
        ?int $groupFilter = null,
        int $perPage = 10
    ) {
        $query = Member::with(['family', 'title', 'groups'])
            ->when($search, fn($q) => $q->search($search))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->when($groupFilter, function($q) use ($groupFilter) {
                $q->whereHas('groups', fn($g) => $g->where('groups.id', $groupFilter));
            });

        return $query->latest()->paginate($perPage);
    }

    /**
     * Confirm baptism for a member
     */
    public function confirmBaptism(
        int $memberId,
        string $baptismDate,
        string $baptizedBy,
        string $baptismPlace,
        ?string $note = null
    ): Member {
        return DB::transaction(function () use ($memberId, $baptismDate, $baptizedBy, $baptismPlace, $note) {
            $member = Member::findOrFail($memberId);

            // Find 'Tín hữu' title
            $memberTitleId = \App\Modules\Membership\Models\Title::where('name', 'like', '%Tín hữu%')
                ->where('name', 'not like', '%Tín hữu mới%')
                ->value('id');

            $updateData = [
                'date_baptism' => $baptismDate,
                'baptized_by' => $baptizedBy,
                'baptism_place' => $baptismPlace,
                'status' => 'active',
            ];

            if ($memberTitleId) {
                $updateData['title_id'] = $memberTitleId;
            }

            $member->update($updateData);

            // Record spiritual growth event
            \App\Modules\Membership\Models\SpiritualGrowth::create([
                'member_id' => $member->id,
                'type' => 'baptism',
                'event_date' => $baptismDate,
                'details' => "Bởi: {$baptizedBy} tại {$baptismPlace}. " . ($note ?? ''),
            ]);

            // Create activity log
            \App\Models\ActivityLog::create([
                'type' => 'success',
                'message' => 'Hội thánh có thêm 1 chi thể mới vừa báp-tem!',
                'payload' => ['member_id' => $member->id, 'name' => $member->full_name],
            ]);

            return $member->fresh();
        });
    }
}
