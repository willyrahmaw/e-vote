<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Audit Trail & Log Keamanan')]
class AuditLogIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $actionFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($this->search, function (Builder $query, string $search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($this->actionFilter, function (Builder $query, string $action) {
                $query->where('action', $action);
            })
            ->latest()
            ->paginate(20);

        $distinctActions = AuditLog::distinct()->pluck('action');

        return view('livewire.admin.audit-log-index', [
            'logs' => $logs,
            'distinctActions' => $distinctActions,
        ]);
    }
}
