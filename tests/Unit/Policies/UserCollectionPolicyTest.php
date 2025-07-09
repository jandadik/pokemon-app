<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\UserCollection;
use App\Policies\UserCollectionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserCollectionPolicyTest extends TestCase
{
    //use RefreshDatabase;

    protected UserCollectionPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserCollectionPolicy();
    }

    #[Test]
    public function viewAny_returns_true_for_any_authenticated_user()
    {
        $user = User::factory()->create();
        $this->assertTrue($this->policy->viewAny($user));
        // Note: Adjust if viewAny logic changes based on roles/permissions
    }

    #[Test]
    public function view_returns_true_for_public_collection_for_guest()
    {
        $collection = UserCollection::factory()->public()->create();
        $this->assertTrue($this->policy->view(null, $collection));
    }

    #[Test]
    public function view_returns_true_for_public_collection_for_other_user()
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->public()->create();
        $this->assertTrue($this->policy->view($viewer, $collection));
    }

    #[Test]
    public function view_returns_true_for_private_collection_for_owner()
    {
        $owner = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->private()->create();
        $this->assertTrue($this->policy->view($owner, $collection));
    }

    #[Test]
    public function view_returns_false_for_private_collection_for_guest()
    {
        $collection = UserCollection::factory()->private()->create();
        $this->assertFalse($this->policy->view(null, $collection));
    }

    #[Test]
    public function view_returns_false_for_private_collection_for_other_user()
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->private()->create();
        $this->assertFalse($this->policy->view($viewer, $collection));
    }

    #[Test]
    public function create_returns_true_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->assertTrue($this->policy->create($user));
        // Note: Adjust if create logic changes based on roles/permissions
    }

    #[Test]
    public function update_returns_true_for_owner()
    {
        $owner = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();
        $this->assertTrue($this->policy->update($owner, $collection));
    }

    #[Test]
    public function update_returns_false_for_other_user()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();
        $this->assertFalse($this->policy->update($otherUser, $collection));
    }

    #[Test]
    public function delete_returns_true_for_owner()
    {
        $owner = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();
        $this->assertTrue($this->policy->delete($owner, $collection));
    }

    #[Test]
    public function delete_returns_false_for_other_user()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();
        $this->assertFalse($this->policy->delete($otherUser, $collection));
    }

    #[Test]
    public function setDefault_returns_true_for_owner()
    {
        $owner = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();
        $this->assertTrue($this->policy->setDefault($owner, $collection));
    }

    #[Test]
    public function setDefault_returns_false_for_other_user()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();
        $this->assertFalse($this->policy->setDefault($otherUser, $collection));
    }
}
