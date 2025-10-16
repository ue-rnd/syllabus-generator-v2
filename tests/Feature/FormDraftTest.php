<?php

namespace Tests\Feature;

use App\Models\FormDraft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_form_draft()
    {
        $user = User::factory()->create();
        
        $draft = FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.1',
            'current_step' => 2,
            'data' => [
                'course_id' => '1',
                'name' => 'Test Syllabus',
                'ay_start' => 2024,
            ],
            'version' => 1,
        ]);

        $this->assertDatabaseHas('form_drafts', [
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.1',
            'current_step' => 2,
            'version' => 1,
        ]);

        $this->assertEquals('1', $draft->data['course_id']);
        $this->assertEquals('Test Syllabus', $draft->data['name']);
    }

    public function test_can_find_draft_by_user_and_form_key()
    {
        $user = User::factory()->create();
        
        FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.1',
            'current_step' => 3,
            'data' => ['test' => 'data'],
            'version' => 1,
        ]);

        $draft = FormDraft::forUserAndForm($user->id, 'syllabus.wizard.create.1')->first();
        
        $this->assertNotNull($draft);
        $this->assertEquals(3, $draft->current_step);
        $this->assertEquals(['test' => 'data'], $draft->data);
    }

    public function test_can_find_old_drafts()
    {
        $user = User::factory()->create();
        
        // Create an old draft
        $oldDraft = FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.1',
            'current_step' => 1,
            'data' => ['test' => 'old'],
            'version' => 1,
        ]);
        
        // Manually set created_at to 15 days ago
        $oldDraft->created_at = now()->subDays(15);
        $oldDraft->save();

        $oldDrafts = FormDraft::olderThan(14)->get();
        
        $this->assertCount(1, $oldDrafts);
        $this->assertEquals($oldDraft->id, $oldDrafts->first()->id);
    }

    public function test_unique_constraint_prevents_duplicate_drafts()
    {
        $user = User::factory()->create();
        
        FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.1',
            'current_step' => 1,
            'data' => ['test' => 'first'],
            'version' => 1,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.1',
            'current_step' => 2,
            'data' => ['test' => 'second'],
            'version' => 1,
        ]);
    }
}