<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Get departments
        $dcs = Department::where('name', 'Department of Computer Science')->first();
        $dit = Department::where('name', 'Department of Information Technology')->first();
        $demc = Department::where('name', 'Department of Entertainment and Multimedia Computing')->first();

        if (! $dcs || ! $dit || ! $demc) {
            throw new \Exception('Departments not found. Make sure to run DepartmentSeeder first.');
        }

        $programs = [
            // Computer Science Department Programs
            [
                'name' => 'Bachelor of Science in Computer Science',
                'code' => 'BSCS',
                'level' => 'bachelor',
                'department_id' => $dcs->id,
                'sort_order' => 1,
                'description' => '<p>The Bachelor of Science in Computer Science program emphasizes comprehension of the principles and concepts needed for designing and formulating new systems and applications. It encourages the inquisitive pursuit and investigation of new ideas and developments to prepare the student for a subsequent Master’s degree program. It is assured that students entering this degree program have higher level mathematical foundations for abstract algebra, mathematical logic, calculus, discrete mathematics, and statistics.</p>',
                'outcomes' => '<ul><li>Apply knowledge of computing fundamentals, knowledge of a computing specialization, and mathematics, science, and domain knowledge appropriate for the computing specialization to the abstraction and conceptualization of computing models from defined problems and requirements.</li><li>Identify, analyze, formulate, research literature, and solve complex computing problems and requirements reaching substantiated conclusions using fundamental principles of mathematics, computing sciences, and relevant domain disciplines</li><li>An ability to apply mathematical foundations, algorithmic principles and computer science theory in the modeling and design of computer-based systems in a way that demonstrates comprehension of the tradeoffs involved in design choices</li><li>Knowledge and understanding of information security issues in relation to the design, development and use of information systems</li><li>Design and evaluate solutions for complex computing problems, and design and evaluate systems, components, or processes that meet specified needs with appropriate consideration for public health and safety, cultural, societal, and environmental considerations.</li><li>Create, select, adapt and apply appropriate techniques, resources and modern computing tools to complex computing activities, with an understanding of the limitations to accomplish a common goal</li><li>Function effectively as an individual and as a member or leader in diverse teams and in multidisciplinary settings</li><li>Communicate effectively with the computing community and with society at large about complex computing activities by being able to comprehend and write effective reports, design documentation, make effective presentations, and give and understand clear instructions</li><li>An ability to recognize the legal, social, ethical and professional issues involved in the utilization of computer technology and be guided by the adoption of appropriate professional, ethical and legal practices</li><li>Recognize the need and have the ability to engage in independent learning for continual development as a computing professional.</li></ul>',
                'objectives' => '<p>No objectives provided.</p>',
            ],
            [
                'name' => 'Bachelor of Science in Data Science',
                'code' => 'BSDS',
                'level' => 'bachelor',
                'department_id' => $dcs->id,
                'sort_order' => 2,
                'description' => '<p>The BSDS program includes the study of data and the methodologies, processes, algorithms, and systems for collecting, refining, storing, and analyzing data to arrive at useful insights and knowledge. Data Science is a discipline in computing that benefits from developments in computer science, mathematics, statistics, business, and other disciplines.</p>',
                'outcomes' => '<ul><li>No outcome provided.</li></ul>',
                'objectives' => '<p>No objectives provided.</p>',
            ],

            // Information Technology Department Programs
            [
                'name' => 'Bachelor of Science in Information Technology',
                'code' => 'BSIT',
                'level' => 'bachelor',
                'department_id' => $dit->id,
                'sort_order' => 3,
                'description' => '<p>The Bachelor of Science in Information Technology program emphasizes the acquisition of concepts and technologies, preparing and enabling the student for the industrial practice of systems integration, systems administration, systems planning, systems implementation, and other activities that maintain the integrity and proper functionality of a system and its components. It is expected that a student graduating under this degree program had undergraduate or industry preparation that would have exposed him/her to programming concepts and skills as well as the operating environment of a network system.</p>',
                'outcomes' => '<ul><li>An ability to apply knowledge of computing, science, and mathematics appropriate to the discipline.</li><li>Understand best practices and standards and their applications.</li><li>An ability to analyze complex problems, and identify and define the computing requirements appropriate to its solution.</li><li>Identify and analyze user needs and take them into account in the selection, and creation, evaluation, and administration of computer-based systems.</li><li>An ability to design, implement, and evaluate computer-based systems, processes, components or programs to meet desired needs and requirements under various constraints.</li><li>Integrate IT-based solutions into the user environment effectively.</li><li>Apply knowledge through the use of current techniques, skills, tools, and practices necessary for the IT profession.</li><li>Function effectively as a member or leader of a development team recognizing the different roles within a team to accomplish a common goal.</li><li>Assist in the creation of an effective IT project plan.</li><li>Communicate effectively with the computing community and with society at large about complex computing activities through logical writing, presentations, and clear instructions.</li><li>Analyze the local and global impact of computing information technology on individuals, organizations, and society.</li><li>Understand professional, ethical, legal, security, and social issues and responsibilities in the utilization of information technology.</li><li>Recognize the need for an engagement in planning self-learning and improving performance as a foundation for continuing professional development.</li></ul>',
                'objectives' => '<ul><li>Pursue a successful career as computing professionals, utilizing the knowledge acquired in the program;</li><li>Maintain high professionalism and ethical standards as individuals or member of a team, in solving multidisciplinary projects related to Computer and Information Technology problems;</li><li>Demonstrate effective oral and written communication skills;</li><li>Demonstrate a good breadth of knowledge in the core areas of IT, so as to create products and solutions for real-life problems; and to make a positive impact on society, the global economy, and emerging technologies;</li><li>Enhance their professional skills by means of continuous education and professional development; and</li><li>Demonstrate professional and ethical responsibility towards their profession, society and the environment, as well as respect for diversity.</li></ul>',
            ],

            // EMC Department Programs
            [
                'name' => 'Bachelor of Science in Entertainment and Multimedia Computing with Specialization in Digital Animation',
                'code' => 'BSEMC-DA',
                'level' => 'bachelor',
                'department_id' => $demc->id,
                'sort_order' => 4,
                'description' => '<p>With specialization in Digital Animation, the Bachelor of Science in Entertainment and Multimedia Computing (BSEMC) program, with specialization in Digital Animation, aims to prepare students to be digital animation professionals, who are equipped with creative and technical knowledge, skills and values in conceptualizing, designing, and producing animation products and solutions, and in managing such projects over different technology platforms.</p>',
                'outcomes' => '<ul><li>No outcome provided.</li></ul>',
                'objectives' => '<p>No objectives provided.</p>',
            ],
            [
                'name' => 'Bachelor of Science in Entertainment and Multimedia Computing with Specialization in Game Development',
                'code' => 'BSEMC-GD',
                'level' => 'bachelor',
                'department_id' => $demc->id,
                'sort_order' => 4,
                'description' => '<p>With specialization in Game Development, the Bachelor of Science in Entertainment and Multimedia Computing (BSEMCG) program, with specialization n Game development professionals, who are equipped with creative and technical knowledge, skills and values in conceptualizing, designing, and producing digital games and tools, and in managing such projects over different technology platforms.</p>',
                'outcomes' => '<ul><li>No outcome provided.</li></ul>',
                'objectives' => '<p>No objectives provided.</p>',
            ],
        ];

        foreach ($programs as $program) {
            Program::create([
                'name' => $program['name'],
                'code' => $program['code'],
                'level' => $program['level'],
                'description' => $program['description'],
                'objectives' => $program['objectives'],
                'outcomes' => $program['outcomes'],
                'is_active' => true,
                'sort_order' => $program['sort_order'],
                'department_id' => $program['department_id'],
            ]);
        }
    }
}
