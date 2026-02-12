<?php

namespace App\Services;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VisitationService
{
    /**
     * Check if a member needs visitation urgently.
     * Criteria: Status is 'weak' (Yếu đuối) AND last_visited_at > 14 days ago (or null).
     *
     * @param Member $member
     * @return bool
     */
    public function needsVisitation(Member $member): bool
    {
        if ($member->status !== 'weak') {
            return false;
        }

        if (!$member->last_visited_at) {
            return true; // Never visited and weak -> Urgent
        }

        return $member->last_visited_at->diffInDays(Carbon::now()) > 14;
    }

    /**
     * Get a list of top priority members for visitation.
     *
     * @param int $limit
     * @return Collection
     */
    public function getPriorityVisitations(int $limit = 5): Collection
    {
        return Member::where('status', 'weak')
            ->where(function ($query) {
                $query->whereNull('last_visited_at')
                      ->orWhere('last_visited_at', '<', Carbon::now()->subDays(14));
            })
            ->orderByRaw('last_visited_at IS NULL DESC') // Nulls first (never visited)
            ->orderBy('last_visited_at', 'asc') // Oldest visits next
            ->limit($limit)
            ->get();
    }
}
