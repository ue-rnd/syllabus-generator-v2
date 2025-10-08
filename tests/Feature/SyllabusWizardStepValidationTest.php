<?php

namespace Tests\Feature;

use App\Models\FormDraft;
use App\Models\User;
use App\Models\Course;
use App\Models\Setting;
use App\Livewire\Client\Syllabi\CreateSyllabus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SyllabusWizardStepValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_loading_draft_with_incomplete_step_4_redirects_to_valid_step()
    {
        // Create test data
        $user = User::factory()->create();
        $course = Course::factory()->create();
        
        // Create settings
        Setting::create([
            'key' => 'default_ay_start', 
            'label' => 'Default AY Start',
            'value' => '2024',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_ay_end', 
            'label' => 'Default AY End',
            'value' => '2025',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_prelim', 
            'label' => 'Default Prelims Week',
            'value' => '6',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_midterm', 
            'label' => 'Default Midterms Week',
            'value' => '12',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_final', 
            'label' => 'Default Finals Week',
            'value' => '18',
            'type' => 'number',
            'category' => 'academic'
        ]);

        // Create a draft that was on step 5 but step 4 is incomplete
        $draftData = [
            'ay_start' => '2024',
            'ay_end' => '2025',
            'week_prelim' => '6',
            'week_midterm' => '12',
            'week_final' => '18',
            'course_id' => (string) $course->id,
            'name' => 'Test Syllabus',
            'description' => 'Test Description',
            'program_outcomes' => [
                [
                    'content' => 'Test program outcome',
                    'addressed' => 'introduced'
                ]
            ],
            'course_outcomes' => [
                [
                    'verb' => 'analyze',
                    'content' => 'Test course outcome'
                ]
            ],
            'learning_matrix' => [
                // Incomplete learning matrix - missing required fields
                [
                    'week_range' => [
                        'start' => null, // Missing start week
                        'end' => null,
                        'is_range' => false
                    ],
                    'content' => '', // Missing content
                    'learning_outcomes' => [],
                    'learning_activities' => [],
                    'assessments' => []
                ]
            ],
            'textbook_references' => 'Some references',
            'grading_system' => 'Test grading system',
            'classroom_policies' => 'Test policies',
            'principal_prepared_by' => (string) $user->id,
        ];

        FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.' . $user->id,
            'current_step' => 5, // Draft was on step 5
            'data' => $draftData,
            'version' => 1,
        ]);

        // Test the component
        $component = Livewire::actingAs($user)
            ->test(CreateSyllabus::class);

        // Should be redirected to step 3 (the last valid step) instead of step 5
        $this->assertEquals(3, $component->get('currentStep'));
        
        // Step 4 should not be in validated steps
        $this->assertNotContains(4, $component->get('validatedSteps'));
        $this->assertNotContains(5, $component->get('validatedSteps'));
    }

    public function test_loading_draft_with_complete_steps_allows_progression()
    {
        // Create test data
        $user = User::factory()->create();
        $course = Course::factory()->create();
        
        // Create settings
        Setting::create([
            'key' => 'default_ay_start', 
            'label' => 'Default AY Start',
            'value' => '2024',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_ay_end', 
            'label' => 'Default AY End',
            'value' => '2025',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_prelim', 
            'label' => 'Default Prelims Week',
            'value' => '6',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_midterm', 
            'label' => 'Default Midterms Week',
            'value' => '12',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_final', 
            'label' => 'Default Finals Week',
            'value' => '18',
            'type' => 'number',
            'category' => 'academic'
        ]);

        // Create a draft with complete data up to step 4
        $draftData = [
            'ay_start' => '2024',
            'ay_end' => '2025',
            'week_prelim' => '6',
            'week_midterm' => '12',
            'week_final' => '18',
            'course_id' => (string) $course->id,
            'name' => 'Test Syllabus',
            'description' => 'Test Description',
            'program_outcomes' => [
                [
                    'content' => 'Test program outcome',
                    'addressed' => 'introduced'
                ]
            ],
            'course_outcomes' => [
                [
                    'verb' => 'analyze',
                    'content' => 'Test course outcome'
                ]
            ],
            'learning_matrix' => [
                [
                    'week_range' => [
                        'start' => 1, // Complete data
                        'end' => 1,
                        'is_range' => false
                    ],
                    'content' => 'Test content', // Complete content
                    'learning_outcomes' => [],
                    'learning_activities' => [],
                    'assessments' => []
                ]
            ],
            'textbook_references' => 'Some references',
            'grading_system' => 'Test grading system',
            'classroom_policies' => 'Test policies',
            'principal_prepared_by' => (string) $user->id,
        ];

        FormDraft::create([
            'user_id' => $user->id,
            'form_key' => 'syllabus.wizard.create.' . $user->id,
            'current_step' => 4, // Draft was on step 4
            'data' => $draftData,
            'version' => 1,
        ]);

        // Test the component
        $component = Livewire::actingAs($user)
            ->test(CreateSyllabus::class);

        // Should be on step 4 since all previous steps are valid
        $this->assertEquals(4, $component->get('currentStep'));
        
        // Steps 1-4 should be in validated steps
        $validatedSteps = $component->get('validatedSteps');
        $this->assertContains(1, $validatedSteps);
        $this->assertContains(2, $validatedSteps);
        $this->assertContains(3, $validatedSteps);
        $this->assertContains(4, $validatedSteps);
    }

    public function test_cannot_manually_jump_to_invalid_step()
    {
        // Create test data
        $user = User::factory()->create();
        $course = Course::factory()->create();
        
        // Create settings
        Setting::create([
            'key' => 'default_ay_start', 
            'label' => 'Default AY Start',
            'value' => '2024',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_ay_end', 
            'label' => 'Default AY End',
            'value' => '2025',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_prelim', 
            'label' => 'Default Prelims Week',
            'value' => '6',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_midterm', 
            'label' => 'Default Midterms Week',
            'value' => '12',
            'type' => 'number',
            'category' => 'academic'
        ]);
        Setting::create([
            'key' => 'default_week_final', 
            'label' => 'Default Finals Week',
            'value' => '18',
            'type' => 'number',
            'category' => 'academic'
        ]);

        // Test the component
        $component = Livewire::actingAs($user)
            ->test(CreateSyllabus::class);

        // Try to jump to step 4 without completing previous steps
        $component->call('goToStep', 4);

        // Should still be on step 1 (can't jump to invalid step)
        $this->assertEquals(1, $component->get('currentStep'));
        
        // Should not have step 4 in validated steps
        $this->assertNotContains(4, $component->get('validatedSteps'));
    }
}
