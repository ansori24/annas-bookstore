<?php

namespace Tests\Feature;

use App\User;
use App\Author;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_returns_an_author_as_a_resource_object()
    {
        Passport::actingAs(factory(User::class)->create());
        $author = factory(Author::class)->create();

        $this->getJson(route('authors.show', $author))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $author->id,
                    'type' => 'authors',
                    'attributes' => [
                        'name' => $author->name,
                        'created_at' => $author->created_at->toJson(),
                        'updated_at' => $author->updated_at->toJson(),
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_returns_all_authors_as_a_collection_of_resource_objects()
    {
        Passport::actingAs(factory(User::class)->create());
        $authors = factory(Author::class, 2)->create();

        $this->getJson(route('authors.index'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $authors[0]->id,
                        'type' => 'authors',
                        'attributes' => [
                            'name' => $authors[0]->name,
                            'created_at' => $authors[0]->created_at->toJson(),
                            'updated_at' => $authors[0]->updated_at->toJson(),
                        ]
                    ], [
                        'id' => $authors[1]->id,
                        'type' => 'authors',
                        'attributes' => [
                            'name' => $authors[1]->name,
                            'created_at' => $authors[1]->created_at->toJson(),
                            'updated_at' => $authors[1]->updated_at->toJson(),
                        ]
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_can_create_an_author_from_a_resource_object()
    {
        Passport::actingAs(factory(User::class)->create());

        $this->postJson(route('authors.store'), [
            'data' => [
                'type' => 'authors',
                'attributes' => [
                    'name' => $name = $this->faker->name(),
                ]
            ]
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => '1',
                    'type' => 'authors',
                    'attributes' => [
                        'name' => $name,
                        'created_at' => now()->setMilliseconds(0)->toJson(),
                        'updated_at' => now()->setMilliseconds(0)->toJson(),
                    ]
                ]
            ])->assertHeader('Location', route('authors.show', 1));

        $this->assertDatabaseHas('authors', ['id' => 1, 'name' => $name]);
    }

    /** @test */
    public function it_can_update_an_author_from_a_resource_object()
    {
        Passport::actingAs(factory(User::class)->create());
        $author = factory(Author::class)->create();

        $this->patchJson(route('authors.update', $author), [
            'data' => [
                'id' => (string) $author->id,
                'type' => 'authors',
                'attributes' => [
                    'name' => $newName = $this->faker->name()
                ]
            ]
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $author->id,
                    'type' => 'authors',
                    'attributes' => [
                        'name' => $newName,
                        'created_at' => $author->created_at->toJson(),
                        'updated_at' => now()->setMilliseconds(0)->toJson(),
                    ]
                ]
            ]);
        $this->assertDatabaseMissing('authors', ['id' => 1, 'name' => $author->name]);
        $this->assertDatabaseHas('authors', ['id' => 1, 'name' => $newName]);
    }

    /** @test */
    public function it_can_delete_an_author_through_a_delete_request()
    {
        Passport::actingAs(factory(User::class)->create());
        $author = factory(Author::class)->create();

        $this->deleteJson(route('authors.destroy', $author), [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertStatus(204);

        $this->assertDatabaseMissing('authors', ['name' => $author->name]);
    }
}
