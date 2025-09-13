<?php

namespace Tests\Feature;

use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Syllabus;
use App\Models\User;
use App\Services\SyllabusPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyllabusPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_generation_works()
    {
        // Create test data
        $user = User::factory()->create();
        
        $college = College::factory()->create([
            'name' => 'Test College',
        ]);
        
        $department = Department::factory()->create([
            'name' => 'Test Department',
            'college_id' => $college->id,
        ]);
        
        $course = Course::factory()->create([
            'name' => 'Test Course',
            'code' => 'TEST101',
            'college_id' => $college->id,
            'department_id' => $department->id,
        ]);
        
        $syllabus = Syllabus::factory()->create([
            'name' => 'Test Syllabus',
            'course_id' => $course->id,
            'principal_prepared_by' => $user->id,
            'status' => 'draft',
            'course_outcomes' => [
                [
                    'verb' => 'analyze',
                    'content' => 'Test outcome content',
                ]
            ],
            'learning_matrix' => [
                [
                    'week_range' => [
                        'start' => 1,
                        'end' => 1,
                        'is_range' => false,
                    ],
                    'learning_outcomes' => [
                        [
                            'verb' => 'understand',
                            'content' => 'Basic concepts',
                        ]
                    ],
                    'learning_activities' => [
                        [
                            'modality' => ['onsite'],
                            'reference' => 'Textbook Chapter 1',
                            'description' => 'Introduction lecture',
                        ]
                    ],
                    'assessments' => ['quiz'],
                ]
            ],
        ]);
        
        // Test PDF service
        $pdfService = new SyllabusPdfService();
        $pdf = $pdfService->generatePdf($syllabus);
        
        $this->assertNotNull($pdf);
        $this->assertInstanceOf(\Barryvdh\DomPDF\PDF::class, $pdf);
    }

    public function test_pdf_controller_requires_authentication()
    {
        $syllabus = Syllabus::factory()->create();
        
        $response = $this->get(route('syllabus.pdf.view', $syllabus));
        $response->assertStatus(403);
        
        $response = $this->get(route('syllabus.pdf.download', $syllabus));
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_pdf()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $college = College::factory()->create();
        $department = Department::factory()->create(['college_id' => $college->id]);
        $course = Course::factory()->create([
            'college_id' => $college->id,
            'department_id' => $department->id,
        ]);
        $syllabus = Syllabus::factory()->create(['course_id' => $course->id]);
        
        $response = $this->get(route('syllabus.pdf.view', $syllabus));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
