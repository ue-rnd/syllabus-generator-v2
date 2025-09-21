<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Seeder;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colleges = [
            [
                'name' => 'Graduate School',
                'code' => 'GS',
                'logo_path' => 'images/logos/logo_gs.png',
                'sort_order' => 1,
                'mission' => '<p>The University of the East Graduate School develops experts and leaders for Business, Education, Science and Technology.</p>',
                'vision' => '<p>The University of the East Graduate School evolves as a center of scholarly quests and knowledge generation.</p>',
                'objectives' => '<p>The University of the East Graduate School aims to produce graduates with the following attributes:</p><ul><li>Disciplined competency for independent and scholarly pursuits of knowledge.</li><li>Superior skills for teaching, production, management, research and leadership in both public and private entities.</li><li>Highest personal and professional standards in delivering educational portfolio.</li><li>Analytical mind for problem solving and decision making.</li>',
            ],
            [
                'name' => 'College of Law',
                'code' => 'CLaw',
                'logo_path' => 'images/logos/logo_claw.png',
                'sort_order' => 2,
                'mission' => '<p>Every member of the faculty shall pursue the objectives of the college with the following mission statement in mind:</p><p>As we recognize the changes around us, our emphasis will not only pertain to mere material accomplishments but also to the pursuit for excellence in the legal profession. We shall shape and nurture men of ideas and values, not only to be academic combatants with razor-sharp arguments, but also with a wisdom that pierces the soul — committed to peace, love, truth and justice.</p>',
                'vision' => '<p>To prepare the students in the area of litigation, mediation, arbitration or other alternative dispute resolution, advocacy, and academic or institutional leadership, as well as manager and administrator in different enterprises such as banking, real estate, insurance, investment, etc.</p>',
                'objectives' => '<p>The observance of the rule of law and its practice in a free and democratic society must result in what is right and just. This is the perspective which the College of Law upholds – that the rule of law is a vehicle for the attainment of the highest human aspirations and the maintenance of political, economic and social progress and, therefore, must be regarded with zeal in its study.</p><p>The College of Law stands firm on the commitment that it must inculcate in its students, through a faculty of proven competence and unquestioned integrity, a proper sense of justice and fairness and a passion for truth.</p>',
            ],
            [
                'name' => 'College of Dentistry',
                'code' => 'CDent',
                'logo_path' => 'images/logos/logo_cdent.png',
                'sort_order' => 3,
                'mission' => '<p>The College of Dentistry commits itself to productive and quality dental educational leadership that enables the youth to develop professional competencies essential for the oral health advancement of local and global communities.</p>',
                'vision' => '<p>The College of Dentistry is envisioned to be locally and internationally recognized as a premier dental education center that is responsive to the professional and moral demands of globally-competitive and empowered dental practitioners in a world with changing oral health needs.</p>',
                'objectives' => '<p>A graduate of Doctor of Dental Medicine is expected to:</p><ul><li>Pass the Dentistry Licensure Examination and practice the profession ethically, legally and morally as a general dentist, academician or researcher.</li><li>Deliver comprehensive oral health care in intra– and inter-professional health community through collaboration.</li><li>Pursue continuing education in Dentistry through postgraduate education, clinical training or membership in specialty organization.</li><li>Participate in local and international research activities.</li><li>Contribute to the advancement of dental public health through outreach programs.</li></ul>',
            ],
            [
                'name' => 'College of Arts and Sciences',
                'code' => 'CAS',
                'logo_path' => 'images/logos/logo_cas.png',
                'sort_order' => 4,
                'mission' => '<p>The College of Arts and Sciences is committed to the harmonious development of the total person. It believes with John Stuart Mills that “men are men before they are lawyers, or physicians, or manufacturers, and if [they are made] capable and sensible men they will make themselves capable and sensible lawyers or physicians”. The CAS believes that a solid base in the arts and sciences, with knowledge and training that will develop and expand to the fullest a student’s intellectual, spiritual, moral, artistic and physical powers, is the best preparation that a student can bring to whatever professional endeavor he may decide later on. The CAS supports its belief in the interrelationship between the liberal and technical fields through its course offerings, through its methods, and most especially in the spirit with which it imbues its operation.</p><p>The CAS subscribes to the belief that education contributes to the realization of national development goals by helping the country develop its human resources, i.e., by producing individuals with the desire for national destiny, political freedom, participation stability, and that education contributes to the safeguarding of the security and liberties of the state.</p>',
                'vision' => '<p>To develop a truly humane person whose desire for personal growth is tempered with moral and spiritual values, ethics, self−discipline, and integrity.</p>',
                'objectives' => '<ul><li>To equip the student with professional competence within a field of specialization in the humanities, the natural sciences or the social sciences so that he becomes a productive member of his community and the nation as a whole.</li><li>To instill a sense of citizenship by making the student aware of the thrust in the development of Filipino society and his potential contribution to his development through the practice of his profession.</li><li>To develop an integrated personality able to withstand pressures and able to function adequately in a world marked by rapid scientific, technological and social changes.</li><li>To instill in the student a desire for precise thinking as well as correct and appropriate means of expression.</li></ul>',
            ],
            [
                'name' => 'College of Business Administration',
                'code' => 'CBA',
                'logo_path' => 'images/logos/logo_cba.png',
                'sort_order' => 5,
                'mission' => '<p>The UE College of Business Administration commits itself to prepare the youth through relevant, responsive and ethical curricular programs into morally upright, competent and well-rounded entrepreneurs, accountants and business managers.</p>',
                'vision' => '<p>The UE College of Business Administration of the University of the East aims to be recognized as one of the top five producers of highly competent graduates who possess the knowledge, skills, values and attitudes that prepare them to compete on an even keel in both the local and global workplace in the 21st century.</p>',
                'objectives' => '<p>The UE College of Business Administration is committed to provide equal opportunities in quality business education in consonance with the UE Mission and our national aspiration, and geared towards global interdependence.</p><ul><li>To continuously upgrade curricula in all levels of discipline to make them responsive to the needs of business and industry;</li><li>To ensure the quality of programs through continuous learning process;</li><li>To inculcate in every student academic excellence, professional and social responsibility, as well as entrepreneurship;</li><li>To ensure that its faculty members and administrative staff conduct themselves with honesty and integrity guided by the core values articulated by the University;</li><li>To promote culture of research among the faculty, the administrative staff, and the studentry;</li><li>To develop a healthy alliance with local and international higher educational institutions, business and industry, and government agencies;</li><li>To ensure employability of graduates by providing appropriate career development programs;</li><li>To extend relevant community outreach programs.</li></ul>',
            ],
            [
                'name' => 'College of Computer Studies and Systems',
                'code' => 'CCSS',
                'logo_path' => 'images/logos/logo_ccss.png',
                'sort_order' => 6,
                'mission' => '<p>The College of Computer Studies and Systems aims to promote computer studies and the applications of computer technology in education, government and industry.</p>',
                'vision' => '<p>College of Computer Studies and Systems envisions itself as the preferred ICT education provider and a byword in the field guided by the University’s core values.</p>',
                'objectives' => '<ul><li>To train computer professionals competent in meeting the growing demand for software developers and IT experts in government, business, industry and education.</li><li>To continuously update and upgrade training programs attuned to the rapid global developments in computer technology.</li><li>To motivate its faculty and students to help advance the frontiers of computer research and development in the country.</li><li>To provide sufficient exposure to relate academic instructions to actual practices in the world of computers.</li></ul>',
            ],
            [
                'name' => 'College of Education',
                'code' => 'CEduc',
                'logo_path' => 'images/logos/logo_ceduc.png',
                'sort_order' => 7,
                'mission' => '<p>The College of Education is a leading institution in teacher education and nutrition-dietetics in the service of youth, country, and God.</p>',
                'vision' => '<p>Developing a culture of lifelong learning.</p>',
                'objectives' => '<ul><li>Critical and Creative thinkers</li><li>Humane persons</li><li>Active members of their communities</li><li>Models of integrity</li><li>Patriotic Filipinos</li><li>Seekers of knowledge</li></ul>',
            ],
            [
                'name' => 'College of Engineering',
                'code' => 'CEng\'g',
                'logo_path' => 'images/logos/logo_cengg.png',
                'sort_order' => 8,
                'mission' => '<p>The College of Engineering affirms its role to develop globally competent and professional engineers imbued with proper values, committed to serve the industry and society and contribute to national development.</p>',
                'vision' => '<p>The College of Engineering is envisioned to be the “National College of Choice” with the highest quality of academic programs equipped with modern and latest technology for research, computational methods, and experimentation.</p>',
                'objectives' => '<ul><li>To produce engineering graduates who are most preferred by the industry, well prepared to pass the professional board examinations and trained to become potential leaders and professionals in the engineering fields.</li><li>To obtain the best state of the art engineering facilities and laboratory equipments and maintain modern engineering research and testing centers.</li><li>To develop faculty members with a highest level of knowledge and excellent teaching capabilities.</li><li>To strengthen linkages, with related industry to fund research, support grants and to be aware of the needs and demands in the industries.</li><li>To be involved with the local community by continuously providing technical skills and engineering consultancy services.</li></ul>',
            ],
            [
                'name' => 'Basic Education Department',
                'code' => 'BasicEd.',
                'logo_path' => 'images/logos/logo_be.png',
                'sort_order' => 9,
                'mission' => '<p>The Basic Education Department is committed to providing quality education that develops the intellectual, moral, and physical capabilities of students from elementary to high school levels.</p>',
                'vision' => '<p>To be a leading basic education institution that nurtures young minds and prepares them for higher education and life challenges.</p>',
                'objectives' => '<ul><li>To provide comprehensive basic education that meets national standards</li><li>To develop critical thinking and problem-solving skills</li><li>To instill moral values and character development</li><li>To prepare students for higher education and future careers</li></ul>',
            ],
        ];

        foreach ($colleges as $college) {
            College::create([
                'name' => $college['name'],
                'code' => $college['code'],
                'description' => $college['description'] ?? 'College description',
                'mission' => $college['mission'] ?? 'College mission statement',
                'vision' => $college['vision'] ?? 'College vision statement',
                'core_values' => $college['core_values'] ?? 'College core values',
                'objectives' => $college['objectives'] ?? ['College objectives'],
                'is_active' => true,
                'sort_order' => $college['sort_order'],
                'logo_path' => $college['logo_path'],
            ]);
        }
    }
}
