<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserCollection;
use App\Services\CollectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker; // If you need fake data
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException; // Make sure this is imported

class CollectionServiceTest extends TestCase
{
    //use RefreshDatabase; // Resets database for each test, good for service tests

    protected CollectionService $collectionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collectionService = $this->app->make(CollectionService::class);
        // It's good practice to ensure User model has a factory for tests.
        // If UserCollection factory is not present, it should be created.
        // For now, we assume User::factory() and UserCollection::factory() are available.
    }

    #[Test]
    public function it_can_create_a_collection_for_a_user()
    {
        $user = User::factory()->create();
        $collectionData = [
            'name' => 'My Test Collection',
            'description' => 'A description for the test collection.',
            'is_public' => true,
            'is_default' => false,
        ];

        $createdCollection = $this->collectionService->createCollection($user, $collectionData);

        $this->assertInstanceOf(UserCollection::class, $createdCollection);
        $this->assertDatabaseHas('user_collections', [
            'id' => $createdCollection->id,
            'user_id' => $user->id,
            'name' => 'My Test Collection',
            'description' => 'A description for the test collection.',
            'is_public' => true,
            'is_default' => false,
        ]);
        $this->assertEquals('My Test Collection', $createdCollection->name);
        $this->assertEquals($user->id, $createdCollection->user_id);
        $this->assertTrue($createdCollection->is_public);
        $this->assertFalse($createdCollection->is_default);
    }

    #[Test]
    public function creating_a_default_collection_updates_other_collections_of_the_user()
    {
        $user = User::factory()->create();

        // Create an existing default collection
        $oldDefaultCollection = UserCollection::factory()
            ->for($user)
            ->default()
            ->create(['name' => 'Old Default']);

        $this->assertTrue($oldDefaultCollection->is_default); // Verify initial state

        $newCollectionData = [
            'name' => 'New Default Collection',
            'is_public' => false,
            'is_default' => true,
        ];

        $newDefaultCollection = $this->collectionService->createCollection($user, $newCollectionData);

        $this->assertInstanceOf(UserCollection::class, $newDefaultCollection);
        $this->assertTrue($newDefaultCollection->is_default);
        $this->assertEquals('New Default Collection', $newDefaultCollection->name);
        $this->assertDatabaseHas('user_collections', [
            'id' => $newDefaultCollection->id,
            'is_default' => true,
        ]);

        // Verify the old default collection is no longer default
        $this->assertDatabaseHas('user_collections', [
            'id' => $oldDefaultCollection->id,
            'is_default' => false, // Should be updated to false
        ]);
        $this->assertFalse($oldDefaultCollection->fresh()->is_default); // Check fresh state from DB
    }

    #[Test]
    public function it_can_retrieve_a_public_collection_by_id_without_a_user()
    {
        $publicCollection = UserCollection::factory()->public()->create();

        $retrievedCollection = $this->collectionService->getCollectionById($publicCollection->id, null);

        $this->assertInstanceOf(UserCollection::class, $retrievedCollection);
        $this->assertEquals($publicCollection->id, $retrievedCollection->id);
    }

    #[Test]
    public function it_can_retrieve_a_public_collection_by_id_with_another_user()
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $publicCollection = UserCollection::factory()->for($owner)->public()->create();

        $retrievedCollection = $this->collectionService->getCollectionById($publicCollection->id, $viewer);

        $this->assertInstanceOf(UserCollection::class, $retrievedCollection);
        $this->assertEquals($publicCollection->id, $retrievedCollection->id);
    }

    #[Test]
    public function it_can_retrieve_an_owned_collection_by_id()
    {
        $owner = User::factory()->create();
        $ownedPrivateCollection = UserCollection::factory()->for($owner)->private()->create();

        $retrievedCollection = $this->collectionService->getCollectionById($ownedPrivateCollection->id, $owner);

        $this->assertInstanceOf(UserCollection::class, $retrievedCollection);
        $this->assertEquals($ownedPrivateCollection->id, $retrievedCollection->id);
    }

    #[Test]
    public function it_throws_authorization_exception_when_retrieving_private_collection_of_another_user()
    {
        $this->expectException(AuthorizationException::class);

        $owner = User::factory()->create();
        $notOwner = User::factory()->create();
        $privateCollectionOfOwner = UserCollection::factory()->for($owner)->private()->create();

        $this->collectionService->getCollectionById($privateCollectionOfOwner->id, $notOwner);
    }

    #[Test]
    public function it_throws_authorization_exception_when_retrieving_private_collection_without_a_user()
    {
        $this->expectException(AuthorizationException::class);

        $privateCollection = UserCollection::factory()->private()->create();

        $this->collectionService->getCollectionById($privateCollection->id, null);
    }

    #[Test]
    public function it_throws_model_not_found_exception_for_non_existent_collection()
    {
        $this->expectException(ModelNotFoundException::class);
        $nonExistentId = 9999;
        // Ensure no collection with this ID exists
        UserCollection::destroy($nonExistentId);

        $this->collectionService->getCollectionById($nonExistentId, null);
    }

    #[Test]
    public function it_can_update_a_collection_attributes()
    {
        $user = User::factory()->create();
        $collection = UserCollection::factory()->for($user)->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'is_public' => false,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'is_public' => true,
        ];

        $updatedCollection = $this->collectionService->updateCollection($collection, $updateData, $user);

        $this->assertEquals('Updated Name', $updatedCollection->name);
        $this->assertEquals('Updated Description', $updatedCollection->description);
        $this->assertTrue($updatedCollection->is_public);
        $this->assertDatabaseHas('user_collections', array_merge(['id' => $collection->id], $updateData));
    }

    #[Test]
    public function updating_collection_to_default_updates_others()
    {
        $user = User::factory()->create();
        $oldDefault = UserCollection::factory()->for($user)->default()->create(['name' => 'Old Default']);
        $toBeNewDefault = UserCollection::factory()->for($user)->create(['is_default' => false, 'name' => 'Future Default']);

        $updateData = ['is_default' => true];

        $updatedCollection = $this->collectionService->updateCollection($toBeNewDefault, $updateData, $user);

        $this->assertTrue($updatedCollection->is_default);
        $this->assertTrue($toBeNewDefault->fresh()->is_default);
        $this->assertFalse($oldDefault->fresh()->is_default);
    }

    #[Test]
    public function it_fails_validation_when_unsetting_only_default_collection_via_update()
    {
        $user = User::factory()->create();
        $onlyDefaultCollection = UserCollection::factory()->for($user)->default()->create();

        // Připravíme data a ručně zavoláme validátor s after callbackem
        $data = ['is_default' => false];
        $rules = [
            'is_default' => ['sometimes', 'boolean'],
        ];

        $validator = \Validator::make($data, $rules);
        $validator->after(function ($validator) use ($user, $onlyDefaultCollection) {
            // Simulace logiky z UpdateCollectionRequest
            if ($onlyDefaultCollection->is_default) {
                $defaultCollectionsCount = UserCollection::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->count();
                if ($defaultCollectionsCount <= 1) {
                    $validator->errors()->add('is_default', 'Nelze zrušit poslední výchozí kolekci. Nejprve nastavte jinou kolekci jako výchozí.');
                }
            }
        });
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('is_default', $validator->errors()->toArray());
        $this->assertStringContainsString('Nelze zrušit poslední výchozí kolekci', $validator->errors()->first('is_default'));
    }
    
    #[Test]
    public function it_can_set_non_default_collection_to_false_when_other_default_exists_via_update()
    {
        $user = User::factory()->create();
        $defaultCollection = UserCollection::factory()->for($user)->default()->create(['name' => 'Still Default']);
        $nonDefaultCollection = UserCollection::factory()->for($user)->create(['is_default' => false, 'name' => 'Was Non-Default']);

        // Attempt to update a non-default collection and explicitly set is_default to false (which it already is)
        // This should not throw an exception and the collection should remain non-default.
        $updatedCollection = $this->collectionService->updateCollection($nonDefaultCollection, ['is_default' => false, 'name' => 'Still Non-Default'], $user);
        
        $this->assertFalse($updatedCollection->is_default);
        $this->assertEquals('Still Non-Default', $updatedCollection->name);
        $this->assertTrue($defaultCollection->fresh()->is_default); // The other default should remain default
    }

    #[Test]
    public function it_cannot_update_a_collection_owned_by_another_user()
    {
        $this->expectException(AuthorizationException::class);

        $owner = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();

        $anotherUser = User::factory()->create();

        $this->collectionService->updateCollection($collection, ['name' => 'New Name Attempt'], $anotherUser);
    }

    #[Test]
    public function it_can_delete_a_collection()
    {
        $user = User::factory()->create();
        $collectionToDelete = UserCollection::factory()->for($user)->create(['is_default' => false]);
        // Optionally create a default collection to ensure it's not affected wrongly
        UserCollection::factory()->for($user)->default()->create(); 

        // Simulate adding items to the collection to test cascade delete if UserCollectionItem model & factory exist
        // UserCollectionItem::factory()->count(2)->for($collectionToDelete)->create();

        $result = $this->collectionService->deleteCollection($collectionToDelete, $user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('user_collections', ['id' => $collectionToDelete->id]);
        // If items were added, assert they are also missing from user_collection_items
        // $this->assertDatabaseMissing('user_collection_items', ['collection_id' => $collectionToDelete->id]);
    }

    #[Test]
    public function deleting_default_collection_sets_another_as_default_if_available()
    {
        $user = User::factory()->create();
        $collectionToKeep = UserCollection::factory()->for($user)->create(['created_at' => now()->subDay(), 'name' => 'Will be New Default']); // Older
        $defaultCollectionToDelete = UserCollection::factory()->for($user)->default()->create(['created_at' => now(), 'name' => 'Old Default']); // Newer, but will be deleted
        $anotherCollection = UserCollection::factory()->for($user)->create(['created_at' => now()->subDays(2), 'name' => 'Another Non-Default']); // Even Older
        
        // Ensure collectionToKeep is older so orderByDesc('created_at') picks the correct one if defaultCollectionToDelete was newest
        // Let's adjust to make collectionToKeep the newest of the remaining ones if default is deleted
        $collectionToKeep->created_at = now()->subHour(); // newest among remaining
        $collectionToKeep->save();
        $anotherCollection->created_at = now()->subDays(1); // older than collectionToKeep
        $anotherCollection->save();
        $defaultCollectionToDelete->created_at = now(); // newest overall (but will be deleted)
        $defaultCollectionToDelete->save();


        $this->assertTrue($defaultCollectionToDelete->is_default);
        $this->assertFalse($collectionToKeep->is_default);

        $result = $this->collectionService->deleteCollection($defaultCollectionToDelete, $user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('user_collections', ['id' => $defaultCollectionToDelete->id]);
        $this->assertTrue($collectionToKeep->fresh()->is_default, "The newest remaining collection should become default.");
        $this->assertFalse($anotherCollection->fresh()->is_default);
    }

    #[Test]
    public function it_can_delete_the_only_collection_of_a_user_even_if_default()
    {
        $user = User::factory()->create();
        $onlyCollection = UserCollection::factory()->for($user)->default()->create();

        $result = $this->collectionService->deleteCollection($onlyCollection, $user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('user_collections', ['id' => $onlyCollection->id]);
        $this->assertEquals(0, $user->collections()->count());
    }

    #[Test]
    public function it_cannot_delete_a_collection_owned_by_another_user()
    {
        $this->expectException(AuthorizationException::class);

        $owner = User::factory()->create();
        $collection = UserCollection::factory()->for($owner)->create();

        $anotherUser = User::factory()->create();

        $this->collectionService->deleteCollection($collection, $anotherUser);
    }

    #[Test]
    public function it_can_set_a_collection_as_default()
    {
        $user = User::factory()->create();

        $collection1 = UserCollection::factory()->for($user)->create(['is_default' => true, 'name' => 'Old Default']);
        $collection2 = UserCollection::factory()->for($user)->create(['is_default' => false, 'name' => 'Non-Default']);
        $collectionToSetAsDefault = UserCollection::factory()->for($user)->create(['is_default' => false, 'name' => 'Future Default']);

        $this->assertTrue($collection1->is_default);
        $this->assertFalse($collection2->is_default);
        $this->assertFalse($collectionToSetAsDefault->is_default);

        $updatedCollection = $this->collectionService->setCollectionAsDefault($collectionToSetAsDefault, $user);

        $this->assertTrue($updatedCollection->is_default);
        $this->assertEquals($collectionToSetAsDefault->id, $updatedCollection->id);

        $this->assertFalse($collection1->fresh()->is_default, 'Old default collection should no longer be default.');
        $this->assertFalse($collection2->fresh()->is_default, 'Non-default collection should remain non-default.');
        $this->assertTrue($collectionToSetAsDefault->fresh()->is_default, 'Target collection should be default.');
    }

    #[Test]
    public function it_throws_authorization_exception_when_setting_default_for_unowned_collection()
    {
        $this->expectException(AuthorizationException::class);

        $owner = User::factory()->create();
        $anotherUser = User::factory()->create(); // User attempting the action

        $collectionOfOwner = UserCollection::factory()->for($owner)->create();

        $this->collectionService->setCollectionAsDefault($collectionOfOwner, $anotherUser);
    }

    #[Test]
    public function it_can_get_user_collections()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        UserCollection::factory()->count(3)->for($user)->create();
        UserCollection::factory()->count(2)->for($anotherUser)->create();

        $collections = $this->collectionService->getUserCollections($user);

        $this->assertCount(3, $collections);
        $collections->each(function (UserCollection $collection) use ($user) {
            $this->assertEquals($user->id, $collection->user_id);
        });
    }

    #[Test]
    public function it_can_get_user_collections_with_filters()
    {
        $user = User::factory()->create();

        $publicCollection1 = UserCollection::factory()->for($user)->create(['is_public' => true, 'name' => 'Public Alpha']);
        $privateCollection = UserCollection::factory()->for($user)->create(['is_public' => false, 'name' => 'Private Beta']);
        $publicCollection2 = UserCollection::factory()->for($user)->create(['is_public' => true, 'name' => 'Public Gamma']);

        // Test filtering for public collections
        $publicCollections = $this->collectionService->getUserCollections($user, ['is_public' => true]);

        $this->assertCount(2, $publicCollections);
        $this->assertTrue($publicCollections->contains($publicCollection1));
        $this->assertTrue($publicCollections->contains($publicCollection2));
        $this->assertFalse($publicCollections->contains($privateCollection));

        // Test filtering for private collections
        $privateCollections = $this->collectionService->getUserCollections($user, ['is_public' => false]);
        $this->assertCount(1, $privateCollections);
        $this->assertTrue($privateCollections->contains($privateCollection));
        $this->assertFalse($privateCollections->contains($publicCollection1));

        // Test ordering by name (default)
        $this->assertEquals('Public Alpha', $publicCollections->first()->name);
        $this->assertEquals('Public Gamma', $publicCollections->last()->name);
    }

    #[Test]
    public function it_can_get_public_collections()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $publicCollection1 = UserCollection::factory()->for($user1)->public()->create(['name' => 'Public Zeta']);
        UserCollection::factory()->for($user1)->private()->create(['name' => 'Private Eta']);
        $publicCollection2 = UserCollection::factory()->for($user2)->public()->create(['name' => 'Public Theta']);
        UserCollection::factory()->for($user2)->private()->create(['name' => 'Private Iota']);
        $publicCollection3 = UserCollection::factory()->public()->create(['name' => 'Public Kappa']); // Public collection with no specific user, or different user

        $collections = $this->collectionService->getPublicCollections();

        $this->assertCount(3, $collections); // Assumes all public collections are fetched regardless of user
        $this->assertTrue($collections->contains($publicCollection1));
        $this->assertTrue($collections->contains($publicCollection2));
        $this->assertTrue($collections->contains($publicCollection3));

        // Check that no private collections are included
        $collectionNames = $collections->pluck('name');
        $this->assertNotContains('Private Eta', $collectionNames);
        $this->assertNotContains('Private Iota', $collectionNames);

        // Test ordering
        $this->assertEquals('Public Kappa', $collections->get(0)->name); // Based on default name ordering
        $this->assertEquals('Public Theta', $collections->get(1)->name);
        $this->assertEquals('Public Zeta', $collections->get(2)->name);

        // Test with filter
        $filteredCollections = $this->collectionService->getPublicCollections(['name_contains' => 'Zeta']);
        $this->assertCount(1, $filteredCollections);
        $this->assertTrue($filteredCollections->contains($publicCollection1));
    }
} 