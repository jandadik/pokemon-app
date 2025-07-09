<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;

class CollectionControllerTest extends TestCase
{
    //use RefreshDatabase;
    // use WithFaker; // Můžeme odkomentovat, pokud budeme potřebovat faker pro názvy atd.

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->seed(); // Odstraněno/zakomentováno - spoléháme na factories
        $this->user = User::factory()->create();
        // Můžeme zde vytvořit i nějaké základní kolekce pro uživatele, pokud je to pro většinu testů potřeba.
    }

    #[Test]
    public function index_displays_user_collections_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $userCollections = UserCollection::factory()->count(3)->for($this->user)->create();
        $otherUser = User::factory()->create();
        UserCollection::factory()->count(2)->for($otherUser)->create(); // Jen pro vytvoření cizích, nebudeme je přímo ověřovat v props

        $response = $this->get(route('collections.index'));

        $response->assertOk();
        $response->assertInertia(
            function (Assert $page) use ($userCollections) {
                $page->component('Collections/Index')
                    ->has('collections', $userCollections->count()); // Ověří počet kolekcí
                    // ->has('collections.0.id') // Dočasně zakomentováno
                    // ->has('collections.0.name');

                // Ověření, že všechny zobrazené kolekce patří přihlášenému uživateli
                $displayedCollections = $page->toArray()['props']['collections'];
                if (is_array($displayedCollections)) {
                    foreach ($displayedCollections as $collection) {
                        $this->assertEquals($this->user->id, $collection['user_id'], "Zobrazená kolekce (ID: {$collection['id']}) nepatří přihlášenému uživateli.");
                    }
                }
                return $page; 
            }
        );
    }

    #[Test]
    public function index_redirects_to_login_for_guest(): void
    {
        $response = $this->get(route('collections.index'));

        $response->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function store_creates_new_collection_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $collectionData = [
            'name' => 'Moje nová testovací kolekce',
            'description' => 'Popis této úžasné kolekce.',
            'is_public' => true,
            'is_default' => false,
        ];

        $response = $this->post(route('collections.store'), $collectionData);

        // Získání poslední vytvořené kolekce pro ověření přesměrování
        $createdCollection = UserCollection::where('name', $collectionData['name'])->first();
        $this->assertNotNull($createdCollection, 'Kolekce nebyla vytvořena v databázi.');

        $response->assertRedirect(route('collections.show', $createdCollection->id));

        $this->assertDatabaseHas('user_collections', [
            'user_id' => $this->user->id,
            'name' => $collectionData['name'],
            'description' => $collectionData['description'],
            'is_public' => $collectionData['is_public'],
            'is_default' => $collectionData['is_default'],
        ]);

        // Pokud je is_default true, ověřit logiku pro ostatní default kolekce
        // Tento základní test to prozatím nepokrývá, lze přidat jako samostatný test
        // nebo rozšířit tento.
    }

    #[Test]
    public function store_handles_setting_new_collection_as_default(): void
    {
        $this->actingAs($this->user);

        // Vytvoření existující výchozí kolekce
        $oldDefaultCollection = UserCollection::factory()
            ->for($this->user)
            ->default()
            ->create(['name' => 'Stará výchozí']);

        $this->assertTrue($oldDefaultCollection->is_default); // Ověření počátečního stavu

        $newCollectionData = [
            'name' => 'Nová výchozí kolekce',
            'description' => 'Tato se stane výchozí.',
            'is_public' => false,
            'is_default' => true, // Nastavení nové kolekce jako výchozí
        ];

        $this->post(route('collections.store'), $newCollectionData);

        $this->assertDatabaseHas('user_collections', [
            'user_id' => $this->user->id,
            'name' => $newCollectionData['name'],
            'is_default' => true,
        ]);

        // Ověření, že stará výchozí kolekce již není výchozí
        $this->assertDatabaseHas('user_collections', [
            'id' => $oldDefaultCollection->id,
            'is_default' => false,
        ]);
        $this->assertFalse($oldDefaultCollection->fresh()->is_default);
    }

    #[Test]
    public function store_returns_validation_errors_for_invalid_data(): void
    {
        $this->actingAs($this->user);

        // Chybějící 'name' by mělo způsobit chybu validace
        $invalidData = [
            'description' => 'Popis bez názvu.',
            'is_public' => true,
            'is_default' => false,
        ];

        $response = $this->post(route('collections.store'), $invalidData);

        $response->assertSessionHasErrors('name'); // Ověření chyby pro pole 'name'
        // $response->assertStatus(302); // Inertia obvykle vrací 302 s chybami v session
                                        // Nebo ->assertInvalid(['name']); pokud používáte Laravel 9+ helper

        $this->assertDatabaseMissing('user_collections', [ // Ověření, že se nic neuložilo
            'user_id' => $this->user->id,
            'description' => $invalidData['description'],
        ]);
    }

    #[Test]
    public function store_redirects_guest_to_login(): void
    {
        $response = $this->post(route('collections.store'), [
            'name' => 'Pokusná kolekce hosta',
            'is_public' => false,
            'is_default' => false,
        ]);

        $response->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function show_displays_owned_collection_for_authenticated_user(): void
    {
        $collection = UserCollection::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('collections.show', $collection));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Collections/Show') // Zpět na Show
                ->has('collection')
                ->where('collection.id', $collection->id)
                ->where('collection.name', $collection->name)
        );
    }

    #[Test]
    public function show_displays_public_collection_of_another_user_for_authenticated_user(): void
    {
        $otherUser = User::factory()->create();
        $publicCollection = UserCollection::factory()->for($otherUser)->create(['is_public' => true]);

        $response = $this->actingAs($this->user)->get(route('collections.show', $publicCollection));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Collections/Show') // Zpět na Show
                ->has('collection')
                ->where('collection.id', $publicCollection->id)
        );
    }

    #[Test]
    public function show_returns_403_for_private_collection_of_another_user_for_authenticated_user(): void
    {
        $this->actingAs($this->user); // Přihlášen jako $this->user

        $otherUser = User::factory()->create();
        $privateCollectionOfOtherUser = UserCollection::factory()
            ->for($otherUser)
            ->private() // Vytvoření soukromé kolekce
            ->create([
                'name' => 'Soukromá kolekce jiného uživatele',
            ]);

        $response = $this->get(route('collections.show', $privateCollectionOfOtherUser));

        $response->assertForbidden(); // Očekáváme 403
    }

    #[Test]
    public function show_displays_public_collection_for_guest(): void
    {
        $otherUser = User::factory()->create();
        $publicCollection = UserCollection::factory()->for($otherUser)->create(['is_public' => true]);

        $response = $this->get(route('collections.show', $publicCollection));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Collections/Show') // Zpět na Show
                ->has('collection')
                ->where('collection.id', $publicCollection->id)
        );
    }

    #[Test]
    public function show_returns_403_for_private_collection_for_guest(): void
    {
        $owner = User::factory()->create();
        $privateCollection = UserCollection::factory()
            ->for($owner)
            ->private() // Soukromá kolekce
            ->create([
                'name' => 'Soukromá kolekce pro hosta - nepřístupná',
            ]);

        $response = $this->get(route('collections.show', $privateCollection));

        $response->assertForbidden(); // Očekáváme 403 na základě UserCollectionPolicy
    }

    #[Test]
    public function edit_displays_form_for_owned_collection(): void
    {
        $collection = UserCollection::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('collections.edit', $collection));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Collections/Edit') // Zpět na Edit
                ->has('collection')
                ->where('collection.id', $collection->id)
        );
    }

    #[Test]
    public function edit_returns_403_for_collection_of_another_user(): void
    {
        $this->actingAs($this->user); // Přihlášen jako $this->user

        $otherUser = User::factory()->create();
        $collectionOfOtherUser = UserCollection::factory()->for($otherUser)->create();

        $response = $this->get(route('collections.edit', $collectionOfOtherUser));

        $response->assertForbidden();
    }

    #[Test]
    public function edit_redirects_guest_to_login(): void
    {
        // Vytvoříme kolekci, ale pokusíme se k ní přistoupit jako nepřihlášený uživatel
        $collection = UserCollection::factory()->for($this->user)->create();

        $response = $this->get(route('collections.edit', $collection));

        $response->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function update_modifies_owned_collection_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $collection = UserCollection::factory()->for($this->user)->create([
            'name' => 'Původní Název Kolekce',
            'description' => 'Původní popis.',
            'is_public' => false,
            'is_default' => false,
        ]);

        $updatedData = [
            'name' => 'Aktualizovaný Název Kolekce',
            'description' => 'Aktualizovaný popis.',
            'is_public' => true,
        ];

        $response = $this->put(route('collections.update', $collection), $updatedData);

        $response->assertRedirect(route('collections.show', $collection->id));

        $this->assertDatabaseHas('user_collections', array_merge(['id' => $collection->id], $updatedData));

        $this->assertDatabaseMissing('user_collections', [
            'id' => $collection->id,
            'name' => 'Původní Název Kolekce',
            'is_public' => false,
        ]);
    }

    #[Test]
    public function update_handles_changing_default_collection_status(): void
    {
        $this->actingAs($this->user);

        // Vytvoříme starou výchozí kolekci
        $oldDefaultCollection = UserCollection::factory()->for($this->user)->create(['is_default' => true]);
        // Vytvoříme kolekci, která se stane novou výchozí
        $collectionToBecomeDefault = UserCollection::factory()->for($this->user)->create(['is_default' => false]);

        $response = $this->put(route('collections.update', $collectionToBecomeDefault), [
            'name' => $collectionToBecomeDefault->name, // Název neměníme, jen is_default
            'is_default' => true,
        ]);

        // Očekáváme přesměrování na detail aktualizované kolekce
        $response->assertRedirect(route('collections.show', $collectionToBecomeDefault->id));

        $this->assertTrue($collectionToBecomeDefault->fresh()->is_default, 'Nová kolekce by měla být výchozí.');
        $this->assertFalse($oldDefaultCollection->fresh()->is_default, 'Stará výchozí kolekce by již neměla být výchozí.');
    }

    #[Test]
    public function update_prevents_unsetting_only_default_collection_if_service_logic_throws_exception(): void
    {
        // Tento test předpokládá, že CollectionService (nebo FormRequest) vyhodí výjimku
        // pokud se uživatel pokusí zrušit is_default u jediné výchozí kolekce.
        // Pokud je logika jiná (např. tichá ignorace), test je třeba upravit.
        $this->actingAs($this->user);

        $onlyDefaultCollection = UserCollection::factory()
            ->for($this->user)
            ->default()
            ->create();

        // Očekáváme nějakou formu chyby - buď validační, nebo výjimku ze služby
        // Pokud služba vyhazuje např. LogicException, použijeme $this->expectException(LogicException::class);
        // Pro validační chybu by to bylo přes session errors.
        // Zde záleží na implementaci v UpdateCollectionRequest a CollectionService.
        // Prozatím předpokládejme session error pro jednoduchost, může být potřeba upravit.

        $response = $this->put(route('collections.update', $onlyDefaultCollection), [
            'name' => $onlyDefaultCollection->name,
            'is_default' => false, // Pokus o zrušení výchozího stavu
        ]);
        
        // Upravit assert podle skutečné implementace (např. assertSessionHasErrors, assertUnprocessable)
        // Pokud služba vyhazuje výjimku, test by měl mít $this->expectException(...)
        // a tato část by se neprovedla.
        // Prozatím předpokládám, že request vrátí chybu (např. přesměrování s chybou v session)
        $response->assertSessionHasErrors('is_default'); // Nebo jiný relevantní klíč chyby

        $this->assertTrue($onlyDefaultCollection->fresh()->is_default, 'Kolekce by měla zůstat výchozí.');
    }

    #[Test]
    public function update_returns_validation_errors_for_invalid_data(): void
    {
        $this->actingAs($this->user);
        $collection = UserCollection::factory()->for($this->user)->create();

        // Pokus o aktualizaci s prázdným názvem
        $invalidData = [
            'name' => '',
            'description' => 'Platný popis.',
        ];

        $response = $this->put(route('collections.update', $collection), $invalidData);

        $response->assertSessionHasErrors('name');
        // $response->assertInvalid(['name']); // Alternativa pro Laravel 9+

        // Ověření, že původní název zůstal nezměněn
        $this->assertEquals($collection->name, $collection->fresh()->name);
    }

    #[Test]
    public function update_returns_403_for_collection_of_another_user(): void
    {
        $this->actingAs($this->user); // Přihlášen jako $this->user

        $otherUser = User::factory()->create();
        $collectionOfOtherUser = UserCollection::factory()->for($otherUser)->create();

        $response = $this->put(route('collections.update', $collectionOfOtherUser), [
            'name' => 'Pokus o změnu názvu cizí kolekce',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function update_redirects_guest_to_login(): void
    {
        $collection = UserCollection::factory()->for($this->user)->create();
        $updatedData = [
            'name' => 'Aktualizovaný název hostem',
            'is_public' => true,
            'is_default' => false,
        ];

        $response = $this->put(route('collections.update', $collection), $updatedData);

        $response->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function destroy_deletes_owned_collection_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $collection = UserCollection::factory()->for($this->user)->create();

        $response = $this->delete(route('collections.destroy', $collection));

        // Předpokládáme přesměrování na index po úspěšném smazání
        $response->assertRedirect(route('collections.index'));

        $this->assertModelMissing($collection); // Ověření, že model již neexistuje v DB
        // Alternativa: $this->assertDatabaseMissing('user_collections', ['id' => $collection->id]);
    }

    #[Test]
    public function destroy_returns_403_for_collection_of_another_user(): void
    {
        $this->actingAs($this->user); // Přihlášen jako $this->user

        $otherUser = User::factory()->create();
        $collectionOfOtherUser = UserCollection::factory()->for($otherUser)->create();

        $response = $this->delete(route('collections.destroy', $collectionOfOtherUser));

        $response->assertForbidden();
        $this->assertModelExists($collectionOfOtherUser); // Ověření, že kolekce nebyla smazána
    }

    #[Test]
    public function destroy_redirects_guest_to_login(): void
    {
        $collection = UserCollection::factory()->for($this->user)->create();

        $response = $this->delete(route('collections.destroy', $collection));

        $response->assertRedirect(route('auth.login'));
    }
} 