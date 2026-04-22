<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BackendAuditLogService;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantManagementController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly PlanService $planService,
        private readonly BackendAuditLogService $auditLogService
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));

        $tenants = User::query()
            ->where('role', '!=', 'super_admin')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->with('subscription')
            ->withCount(['services', 'customers', 'bookings'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('backend.tenants.index', [
            'tenants' => $tenants,
            'plans' => $this->planService->allowedPlans(),
            'roles' => $this->allowedRoles(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('backend.tenants.create', [
            'plans' => $this->planService->allowedPlans(),
            'roles' => $this->allowedRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in($this->allowedRoles())],
            'plan' => ['required', 'string', Rule::in($this->planService->allowedPlans())],
        ]);

        $tenant = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make((string) $data['password']),
            'role' => $data['role'],
        ]);

        $this->subscriptionService->upgradePlan((int) $tenant->id, (string) $data['plan']);
        $this->auditLogService->log(
            action: 'tenant_created',
            targetType: User::class,
            targetId: (int) $tenant->id,
            targetLabel: $tenant->email,
            meta: [
                'role' => $tenant->role,
                'plan' => $data['plan'],
            ],
            request: $request
        );

        return redirect()
            ->route('backend.tenants.edit', $tenant)
            ->with('success', 'Tenant baru berhasil dibuat.');
    }

    public function edit(User $tenant): View
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $tenant->loadCount(['services', 'customers', 'bookings']);
        $subscription = $this->subscriptionService->ensureUserSubscription((int) $tenant->id);

        return view('backend.tenants.edit', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'plans' => $this->planService->allowedPlans(),
            'roles' => $this->allowedRoles(),
        ]);
    }

    public function update(Request $request, User $tenant): RedirectResponse
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($tenant->id)],
            'role' => ['required', 'string', Rule::in($this->allowedRoles())],
        ]);

        $oldRole = $tenant->role;

        $tenant->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ])->save();

        $this->auditLogService->log(
            action: 'tenant_updated',
            targetType: User::class,
            targetId: (int) $tenant->id,
            targetLabel: $tenant->email,
            meta: [
                'old_role' => $oldRole,
                'new_role' => $tenant->role,
            ],
            request: $request
        );

        return redirect()
            ->route('backend.tenants.edit', $tenant)
            ->with('success', 'Data tenant berhasil diperbarui.');
    }

    public function destroy(Request $request, User $tenant): RedirectResponse
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $tenantLabel = $tenant->email;
        $tenantId = (int) $tenant->id;

        $tenant->delete();

        $this->auditLogService->log(
            action: 'tenant_deleted',
            targetType: User::class,
            targetId: $tenantId,
            targetLabel: $tenantLabel,
            request: $request
        );

        return redirect()
            ->route('backend.tenants.index')
            ->with('success', 'Tenant berhasil dihapus.');
    }

    public function updateSubscription(Request $request, User $tenant): RedirectResponse
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $data = $request->validate([
            'plan' => ['required', 'string', Rule::in($this->planService->allowedPlans())],
            'expired_at' => ['nullable', 'date'],
            'bookings_consumed_total' => ['nullable', 'integer', 'min:0'],
        ]);

        $expiresAt = isset($data['expired_at']) && $data['expired_at'] !== null
            ? Carbon::parse($data['expired_at'])->endOfDay()
            : null;

        $this->subscriptionService->upgradePlan((int) $tenant->id, (string) $data['plan'], $expiresAt);

        if (array_key_exists('bookings_consumed_total', $data) && $data['bookings_consumed_total'] !== null) {
            Subscription::query()
                ->where('user_id', $tenant->id)
                ->update([
                    'bookings_consumed_total' => (int) $data['bookings_consumed_total'],
                ]);
        }

        $this->auditLogService->log(
            action: 'tenant_subscription_updated',
            targetType: User::class,
            targetId: (int) $tenant->id,
            targetLabel: $tenant->email,
            meta: [
                'plan' => $data['plan'],
                'expired_at' => $expiresAt?->toDateTimeString(),
                'bookings_consumed_total' => isset($data['bookings_consumed_total']) ? (int) $data['bookings_consumed_total'] : null,
            ],
            request: $request
        );

        return redirect()
            ->route('backend.tenants.edit', $tenant)
            ->with('success', 'Subscription tenant berhasil diperbarui.');
    }

    public function updateRole(Request $request, User $tenant): RedirectResponse
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $data = $request->validate([
            'role' => ['required', 'string', Rule::in($this->allowedRoles())],
        ]);

        $oldRole = $tenant->role;
        $tenant->forceFill(['role' => $data['role']])->save();

        $this->auditLogService->log(
            action: 'tenant_role_updated',
            targetType: User::class,
            targetId: (int) $tenant->id,
            targetLabel: $tenant->email,
            meta: [
                'old_role' => $oldRole,
                'new_role' => $tenant->role,
            ],
            request: $request
        );

        return redirect()
            ->route('backend.tenants.edit', $tenant)
            ->with('success', 'Role tenant berhasil diperbarui.');
    }

    public function updateSuspend(Request $request, User $tenant): RedirectResponse
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $data = $request->validate([
            'is_suspended' => ['required', 'boolean'],
            'suspended_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $suspend = (bool) $data['is_suspended'];

        $tenant->forceFill([
            'is_suspended' => $suspend,
            'suspended_at' => $suspend ? now() : null,
            'suspended_reason' => $suspend ? (trim((string) ($data['suspended_reason'] ?? '')) ?: 'Disuspend oleh super admin.') : null,
        ])->save();

        if ($suspend) {
            DB::table('sessions')
                ->where('user_id', $tenant->id)
                ->delete();
        }

        $this->auditLogService->log(
            action: $suspend ? 'tenant_suspended' : 'tenant_unsuspended',
            targetType: User::class,
            targetId: (int) $tenant->id,
            targetLabel: $tenant->email,
            meta: [
                'reason' => $tenant->suspended_reason,
            ],
            request: $request
        );

        return redirect()
            ->route('backend.tenants.edit', $tenant)
            ->with('success', $suspend ? 'Tenant berhasil disuspend.' : 'Tenant berhasil diaktifkan kembali.');
    }

    public function resetPassword(Request $request, User $tenant): RedirectResponse
    {
        abort_if($tenant->isSuperAdmin(), 404);

        $data = $request->validate([
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $newPassword = trim((string) ($data['password'] ?? ''));
        if ($newPassword === '') {
            $newPassword = Str::password(12);
        }

        $tenant->forceFill([
            'password' => Hash::make($newPassword),
        ])->save();

        $this->auditLogService->log(
            action: 'tenant_password_reset',
            targetType: User::class,
            targetId: (int) $tenant->id,
            targetLabel: $tenant->email,
            request: $request
        );

        return redirect()
            ->route('backend.tenants.edit', $tenant)
            ->with('success', 'Password tenant berhasil direset. Password baru: '.$newPassword);
    }

    /**
     * @return array<int, string>
     */
    private function allowedRoles(): array
    {
        return ['tenant', 'manager', 'staff'];
    }
}
