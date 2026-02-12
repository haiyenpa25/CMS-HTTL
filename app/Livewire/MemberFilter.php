<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Modules\Membership\Models\Member;
use App\Modules\Organization\Models\Department;
use Illuminate\Support\Facades\Response;

class MemberFilter extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $ageRange = 'all'; // all, 18-30, 31-50, 51-65, 65+
    public $baptismStatus = 'all'; // all, yes, no
    public $status = 'all'; // all, active, inactive, etc (based on DB enum, assuming active/inactive for now)
    public $seniority = 'all'; // all, 6m, 1y, 2y, 5y
    public $selectedDepartmentId = '';

    public $departments;

    protected $queryString = [
        'search' => ['except' => ''],
        'ageRange' => ['except' => 'all'],
        'baptismStatus' => ['except' => 'all'],
        'selectedDepartmentId' => ['except' => ''],
    ];

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function applyQuickTag($tag)
    {
        $this->resetFilters();
        
        switch ($tag) {
            case 'unbaptized':
                $this->baptismStatus = 'no';
                break;
            case 'new_members': // < 6 months
                $this->seniority = 'new'; // Special case handling needed or just scope
                break;
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->ageRange = 'all';
        $this->baptismStatus = 'all';
        $this->status = 'all';
        $this->seniority = 'all';
        $this->selectedDepartmentId = '';
    }

    public function getFilteredMembersQuery()
    {
        $query = Member::query()->with(['family', 'departments']);

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->selectedDepartmentId) {
            $query->inDepartment($this->selectedDepartmentId);
        }

        // Age Filter
        if ($this->ageRange !== 'all') {
            switch ($this->ageRange) {
                case '0-18': $query->ageBetween(0, 18); break;
                case '19-30': $query->ageBetween(19, 30); break;
                case '31-50': $query->ageBetween(31, 50); break;
                case '51-65': $query->ageBetween(51, 65); break;
                case '65+': $query->ageBetween(65); break;
            }
        }

        // Baptism Filter
        if ($this->baptismStatus !== 'all') {
            $query->isBaptized($this->baptismStatus === 'yes');
        }

        // Status Filter
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Seniority Filter (Joined Date)
        if ($this->seniority !== 'all') {
            $now = now();
            switch ($this->seniority) {
                case '6m': // Joined more than 6 months ago? Or within last 6 months?
                           // Usually "Seniority > X" means joined before X ago.
                           // Requirement: "Thâm niên: Lọc theo joined_date (6 tháng, 1 năm...)"
                           // Let's assume matches > X duration.
                    $query->joinedBefore($now->subMonths(6)->toDateString());
                    break;
                case '1y':
                    $query->joinedBefore($now->subYear()->toDateString());
                    break;
                case '2y':
                    $query->joinedBefore($now->subYears(2)->toDateString());
                    break;
                case 'new': // Joined within last 6 months (Quick tag)
                    $query->where('joined_date', '>=', $now->subMonths(6)->toDateString());
                    break;
            }
        }

        return $query->latest();
    }

    public function export()
    {
        $members = $this->getFilteredMembersQuery()->get();
        $filename = 'danh-sach-tin-huu-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($members) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['ID', 'Họ Tiên', 'Số điện thoại', 'Ngày sinh', 'Đã Báp-tem', 'Ban ngành', 'Tình trạng']);

            foreach ($members as $member) {
                $departments = $member->departments->pluck('name')->implode(', ');
                
                fputcsv($file, [
                    $member->id,
                    $member->full_name,
                    $member->phone,
                    $member->birthday ? $member->birthday->format('d/m/Y') : '',
                    $member->date_baptism ? 'Rồi' : 'Chưa',
                    $departments,
                    $member->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.member-filter', [
            'members' => $this->getFilteredMembersQuery()->paginate(15)
        ])->layout('layouts.app');
    }
}
