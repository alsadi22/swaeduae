<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\OrgProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApprovalsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_admin_can_approve_org_profile(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $orgUser = User::factory()->create();
        $profile = OrgProfile::create([
            'user_id' => $orgUser->id,
            'org_name' => 'Org',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->from('/admin/approvals')
            ->post(route('admin.approvals.orgs.approve', $profile->id));

        $response->assertRedirect('/admin/approvals');
        $response->assertSessionHas('status', 'approved');

        $this->assertDatabaseHas('org_profiles', [
            'id' => $profile->id,
            'status' => 'approved',
        ]);
        $this->assertNotNull($profile->fresh()->approved_at);
    }

    public function test_admin_can_reject_org_profile(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $orgUser = User::factory()->create();
        $profile = OrgProfile::create([
            'user_id' => $orgUser->id,
            'org_name' => 'Org',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->from('/admin/approvals')
            ->post(route('admin.approvals.orgs.reject', $profile->id));

        $response->assertRedirect('/admin/approvals');
        $response->assertSessionHas('status', 'rejected');

        $this->assertDatabaseHas('org_profiles', [
            'id' => $profile->id,
            'status' => 'rejected',
        ]);
    }
}
