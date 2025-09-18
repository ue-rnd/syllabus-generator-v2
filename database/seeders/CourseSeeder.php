<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\College;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Find the College of Computer Studies and Systems
        $ccss = College::where('code', 'CCSS')->first();
        
        if (!$ccss) {
            throw new \Exception('College of Computer Studies and Systems not found.');
        }

        // Get programs to attach courses to
        $bscs = Program::where('code', 'BSCS')->first();
        $bsds = Program::where('code', 'BSDS')->first();
        $bsit = Program::where('code', 'BSIT')->first();
        $bsemcgd = Program::where('code', 'BSEMC-GD')->first();
        $bsemcda = Program::where('code', 'BSEMC-DA')->first();

        $courses = [
            [
                'name' => 'Introduction to Computing',
                'code' => 'CIC1101',
                'programs' => [$bscs, $bsds, $bsit, $bsemcgd, $bsemcda], 
                'course_type' => 'hybrid',
                'prerequisite_courses' => [],
                'description' => "<p>This course teaches the essential ideas of computing principles. This covers better understanding of computer and other computing devices, software,network, Internet, Web page creation, cloud computing, digital security, machine learning and special topics. The special topics are designed to providethestudents framework of their chosen degree program.</p>",
                'outcomes' => '[{"verb":"explain","content":"<p>the development of digital devices and software development.</p>"},{"verb":"differentiate","content":"<p>the web from the Internet, and describe the relationship among the web.</p>"},{"verb":"differentiate","content":"<p>between wired and wireless network technologies.</p>"},{"verb":"describe","content":"<p>digital security risk associated with virus and other malware.</p>"},{"verb":"articulate","content":"<p>the concept of cloud computing.</p>"}]',
                'credit_units_lecture' => 3,
                'credit_units_laboratory' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Data Analytics',
                'code' => 'CDT1101',
                'programs' => [$bscs, $bsds, $bsit],
                'course_type' => 'hybrid',
                'prerequisite_courses' => [],
                'description' => "<p>The course provides students with an overview of the current trends in information technology that drives today’s business.  The course will provide understanding on data management techniques that can help an organization achieve its business goals and address operational challenges.  This will also introduce different tools and methods used in business analytics to provide the students with opportunities to apply these techniques in simulations in a computer laboratory.</p>",
                'outcomes' => '[{"verb":"identify","content":"<p>practical business situations where analytics can be helpful<\/p>"},{"verb":"construct","content":"<p>statistical and machine learning models that solve real-world problems<\/p>"},{"verb":"develop","content":"<p>reports to inform stakeholders about the results of the statistical analysis and machine learning models<\/p>"}]',
                'credit_units_lecture' => 3,
                'credit_units_laboratory' => 1,
                'sort_order' => 1,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create([
                'name' => $courseData['name'],
                'code' => $courseData['code'],
                'description' => $courseData['description'],
                'outcomes' => $courseData['outcomes'],
                'course_type' => $courseData['course_type'],
                'prerequisite_courses' => $courseData['prerequisite_courses'],
                'credit_units_lecture'=> $courseData['credit_units_lecture'],
                'credit_units_laboratory'=> $courseData['credit_units_laboratory'],
                'is_active' => true,
                'sort_order' => $courseData['sort_order'],
                'college_id' => $ccss->id,
            ]);

            // Attach the course to multiple programs (many-to-many relationship)
            foreach ($courseData['programs'] as $program) {
                if ($program) {
                    $course->programs()->attach($program->id);
                }
            }
        }
    }
}
