<?php

// TODO: make handling of CONSTANTS and getSomethingColor and getSomethingOptions consistent.
// CONSTANTS should be array only, lower snake case.
// getSomethingOptions should be associative array, key is lower case

namespace App\Constants;



class SyllabusConstants
{
    /**
     * Action verbs for course outcomes and learning outcomes
     */
    public const ACTION_VERBS = [
        'Remember' => [
            'remember' => 'Remember',
            'cite' => 'Cite',
            'define' => 'Define',
            'describe' => 'Describe',
            'draw' => 'Draw',
            'enumerate' => 'Enumerate',
            'identify' => 'Identify',
            'index' => 'Index',
            'indicate' => 'Indicate',
            'label' => 'Label',
            'list' => 'List',
            'match' => 'Match',
            'meet' => 'Meet',
            'name' => 'Name',
            'outline' => 'Outline',
            'point' => 'Point',
            'quote' => 'Quote',
            'read' => 'Read',
            'recall' => 'Recall',
            'recite' => 'Recite',
            'recognize' => 'Recognize',
            'record' => 'Record',
            'repeat' => 'Repeat',
            'reproduce' => 'Reproduce',
            'review' => 'Review',
            'select' => 'Select',
            'state' => 'State',
            'study' => 'Study',
            'tabulate' => 'Tabulate',
            'trace' => 'Trace',
            'write' => 'Write'
        ],
        'Understand' => [
            'understand' => 'Understand',
            'add' => 'Add',
            'approximate' => 'Approximate',
            'articulate' => 'Articulate',
            'associate' => 'Associate',
            'characterize' => 'Characterize',
            'clarify' => 'Clarify',
            'classify' => 'Classify',
            'compare' => 'Compare',
            'compute' => 'Compute',
            'contrast' => 'Contrast',
            'convert' => 'Convert',
            'defend' => 'Defend',
            'describe' => 'Describe',
            'detail' => 'Detail',
            'differentiate' => 'Differentiate',
            'discuss' => 'Discuss',
            'distinguish' => 'Distinguish',
            'elaborate' => 'Elaborate',
            'estimate' => 'Estimate',
            'example' => 'Example',
            'explain' => 'Explain',
            'express' => 'Express',
            'extend' => 'Extend',
            'extrapolate' => 'Extrapolate',
            'factor' => 'Factor',
            'generalize' => 'Generalize',
            'give' => 'Give',
            'infer' => 'Infer',
            'interact' => 'Interact',
            'interpolate' => 'Interpolate',
            'interpret' => 'Interpret',
            'observe' => 'Observe',
            'paraphrase' => 'Paraphrase',
            'picture_graphically' => 'Picture graphically',
            'predict' => 'Predict',
            'review' => 'Review',
            'rewrite' => 'Rewrite',
            'subtract' => 'Subtract',
            'summarize' => 'Summarize',
            'translate' => 'Translate',
            'visualize' => 'Visualize'
        ],
        'Apply' => [
            'apply' => 'Apply',
            'acquire' => 'Acquire',
            'adapt' => 'Adapt',
            'allocate' => 'Allocate',
            'alphabetize' => 'Alphabetize',
            'ascertain' => 'Ascertain',
            'assign' => 'Assign',
            'attain' => 'Attain',
            'avoid' => 'Avoid',
            'back_up' => 'Back up',
            'calculate' => 'Calculate',
            'capture' => 'Capture',
            'change' => 'Change',
            'classify' => 'Classify',
            'complete' => 'Complete',
            'compute' => 'Compute',
            'construct' => 'Construct',
            'customize' => 'Customize',
            'demonstrate' => 'Demonstrate',
            'depreciate' => 'Depreciate',
            'derive' => 'Derive',
            'determine' => 'Determine',
            'diminish' => 'Diminish',
            'discover' => 'Discover',
            'draw' => 'Draw',
            'employ' => 'Employ',
            'examine' => 'Examine',
            'exercise' => 'Exercise',
            'explore' => 'Explore',
            'expose' => 'Expose',
            'express' => 'Express',
            'factor' => 'Factor',
            'figure' => 'Figure',
            'graph' => 'Graph',
            'handle' => 'Handle',
            'illustrate' => 'Illustrate',
            'interconvert' => 'Interconvert',
            'investigate' => 'Investigate',
            'manipulate' => 'Manipulate',
            'modify' => 'Modify',
            'operate' => 'Operate',
            'personalize' => 'Personalize',
            'plot' => 'Plot',
            'practice' => 'Practice',
            'predict' => 'Predict',
            'prepare' => 'Prepare',
            'price' => 'Price',
            'process' => 'Process',
            'produce' => 'Produce',
            'project' => 'Project',
            'provide' => 'Provide',
            'relate' => 'Relate',
            'round_off' => 'Round off',
            'sequence' => 'Sequence',
            'show' => 'Show',
            'simulate' => 'Simulate',
            'sketch' => 'Sketch',
            'solve' => 'Solve',
            'subscribe' => 'Subscribe',
            'tabulate' => 'Tabulate',
            'transcribe' => 'Transcribe',
            'translate' => 'Translate',
            'use' => 'Use'
        ],
        'Analyze' => [
            'analyze' => 'Analyze',
            'audit' => 'Audit',
            'blueprint' => 'Blueprint',
            'breadboard' => 'Breadboard',
            'break_down' => 'Break down',
            'characterize' => 'Characterize',
            'classify' => 'Classify',
            'compare' => 'Compare',
            'confirm' => 'Confirm',
            'contrast' => 'Contrast',
            'correlate' => 'Correlate',
            'detect' => 'Detect',
            'diagnose' => 'Diagnose',
            'diagram' => 'Diagram',
            'differentiate' => 'Differentiate',
            'discriminate' => 'Discriminate',
            'dissect' => 'Dissect',
            'distinguish' => 'Distinguish',
            'document' => 'Document',
            'ensure' => 'Ensure',
            'examine' => 'Examine',
            'explain' => 'Explain',
            'explore' => 'Explore',
            'figure_out' => 'Figure out',
            'file' => 'File',
            'group' => 'Group',
            'identify' => 'Identify',
            'illustrate' => 'Illustrate',
            'infer' => 'Infer',
            'interrupt' => 'Interrupt',
            'inventory' => 'Inventory',
            'investigate' => 'Investigate',
            'layout' => 'Layout',
            'manage' => 'Manage',
            'maximize' => 'Maximize',
            'minimize' => 'Minimize',
            'optimize' => 'Optimize',
            'order' => 'Order',
            'outline' => 'Outline',
            'point_out' => 'Point out',
            'prioritize' => 'Prioritize',
            'proofread' => 'Proofread',
            'query' => 'Query',
            'relate' => 'Relate',
            'select' => 'Select',
            'separate' => 'Separate',
            'subdivide' => 'Subdivide',
            'train' => 'Train',
            'transform' => 'Transform'
        ],
        'Evaluate' => [
            'evaluate' => 'Evaluate',
            'appraise' => 'Appraise',
            'assess' => 'Assess',
            'compare' => 'Compare',
            'conclude' => 'Conclude',
            'contrast' => 'Contrast',
            'counsel' => 'Counsel',
            'criticize' => 'Criticize',
            'critique' => 'Critique',
            'defend' => 'Defend',
            'determine' => 'Determine',
            'discriminate' => 'Discriminate',
            'estimate' => 'Estimate',
            'explain' => 'Explain',
            'grade' => 'Grade',
            'hire' => 'Hire',
            'interpret' => 'Interpret',
            'judge' => 'Judge',
            'justify' => 'Justify',
            'measure' => 'Measure',
            'predict' => 'Predict',
            'prescribe' => 'Prescribe',
            'rank' => 'Rank',
            'rate' => 'Rate',
            'recommend' => 'Recommend',
            'release' => 'Release',
            'select' => 'Select',
            'summarize' => 'Summarize',
            'support' => 'Support',
            'test' => 'Test',
            'validate' => 'Validate',
            'verify' => 'Verify'
        ],
        'Create' => [
            'create' => 'Create',
            'abstract' => 'Abstract',
            'animate' => 'Animate',
            'arrange' => 'Arrange',
            'assemble' => 'Assemble',
            'budget' => 'Budget',
            'categorize' => 'Categorize',
            'code' => 'Code',
            'combine' => 'Combine',
            'compile' => 'Compile',
            'compose' => 'Compose',
            'construct' => 'Construct',
            'cope' => 'Cope',
            'correspond' => 'Correspond',
            'cultivate' => 'Cultivate',
            'debug' => 'Debug',
            'depict' => 'Depict',
            'design' => 'Design',
            'develop' => 'Develop',
            'devise' => 'Devise',
            'dictate' => 'Dictate',
            'enhance' => 'Enhance',
            'explain' => 'Explain',
            'facilitate' => 'Facilitate',
            'format' => 'Format',
            'formulate' => 'Formulate',
            'generalize' => 'Generalize',
            'generate' => 'Generate',
            'handle' => 'Handle',
            'import' => 'Import',
            'improve' => 'Improve',
            'incorporate' => 'Incorporate',
            'integrate' => 'Integrate',
            'interface' => 'Interface',
            'join' => 'Join',
            'lecture' => 'Lecture',
            'model' => 'Model',
            'modify' => 'Modify',
            'network' => 'Network',
            'organize' => 'Organize',
            'outline' => 'Outline',
            'overhaul' => 'Overhaul',
            'plan' => 'Plan',
            'portray' => 'Portray',
            'prepare' => 'Prepare',
            'prescribe' => 'Prescribe',
            'produce' => 'Produce',
            'program' => 'Program',
            'rearrange' => 'Rearrange',
            'reconstruct' => 'Reconstruct',
            'relate' => 'Relate',
            'reorganize' => 'Reorganize',
            'revise' => 'Revise',
            'rewrite' => 'Rewrite',
            'specify' => 'Specify',
            'summarize' => 'Summarize'
        ]
    ];

    /**
     * Learning activity modalities
     */
    public const LEARNING_MODALITIES = [
        'onsite' => 'Onsite',
        'offsite_asynchronous' => 'Offsite Asynchronous',
        'offsite_synchronous' => 'Offsite Synchronous'
    ];

    public const OUTCOMES_ADDRESSED = [
        'introduced' => 'Introduced',
        'enhanced' => 'Enhanced',
        'demonstrated' => 'Demonstrated',
    ];

    public const APPROVAL_STATUSES = [
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
    ];

    /**
     * Assessment types for weekly assessments
     */
    public const ASSESSMENT_TYPES = [
        'Formative Assessments' => [
            'quiz' => 'Quiz',
            'short_quiz' => 'Short Quiz',
            'pop_quiz' => 'Pop Quiz',
            'homework' => 'Homework',
            'assignment' => 'Assignment',
            'worksheet' => 'Worksheet',
            'discussion_board' => 'Discussion Board',
            'peer_review' => 'Peer Review',
            'self_assessment' => 'Self Assessment',
            'reflection' => 'Reflection',
            'journal' => 'Journal',
            'portfolio_entry' => 'Portfolio Entry'
        ],
        'Practical Assessments' => [
            'laboratory_exercise' => 'Laboratory Exercise',
            'practical_exam' => 'Practical Exam',
            'demonstration' => 'Demonstration',
            'simulation' => 'Simulation',
            'case_study' => 'Case Study',
            'problem_solving' => 'Problem Solving',
            'hands_on_activity' => 'Hands-on Activity'
        ],
        'Project Based Assessments' => [
            'project' => 'Project',
            'group_project' => 'Group Project',
            'individual_project' => 'Individual Project',
            'research_project' => 'Research Project',
            'presentation' => 'Presentation',
            'poster_presentation' => 'Poster Presentation',
            'oral_presentation' => 'Oral Presentation'
        ],
        'Formal Assessments' => [
            'midterm_exam' => 'Midterm Exam',
            'final_exam' => 'Final Exam',
            'comprehensive_exam' => 'Comprehensive Exam',
            'oral_exam' => 'Oral Exam',
            'written_exam' => 'Written Exam',
            'take_home_exam' => 'Take-home Exam'
        ],
        'Performance Assessments' => [
            'performance_task' => 'Performance Task',
            'skill_demonstration' => 'Skill Demonstration',
            'competency_assessment' => 'Competency Assessment',
            'authentic_assessment' => 'Authentic Assessment',
            'rubric_based_assessment' => 'Rubric-based Assessment'
        ],
        'Collaborative Assessments' => [
            'group_work' => 'Group Work',
            'collaborative_project' => 'Collaborative Project',
            'team_presentation' => 'Team Presentation',
            'peer_evaluation' => 'Peer Evaluation'
        ],
        'Creative Assessments' => [
            'creative_work' => 'Creative Work',
            'artistic_creation' => 'Artistic Creation',
            'design_project' => 'Design Project',
            'innovation_challenge' => 'Innovation Challenge'
        ],
        'Field Based Assessments' => [
            'field_work' => 'Field Work',
            'internship_evaluation' => 'Internship Evaluation',
            'practicum_assessment' => 'Practicum Assessment',
            'service_learning' => 'Service Learning'
        ],
        'Technology Enhanced Assessments' => [
            'e_portfolio' => 'E-Portfolio',
            'digital_storytelling' => 'Digital Storytelling',
            'multimedia_project' => 'Multimedia Project',
            'online_simulation' => 'Online Simulation',
            'virtual_lab' => 'Virtual Lab'
        ],
        'Other Assessments' => [
            'attendance' => 'Attendance',
            'participation' => 'Participation',
            'engagement' => 'Engagement',
            'no_assessment' => 'No Assessment'
        ]
    ];

    /**
     * Syllabus statuses with enhanced approval workflow
     */
    public const STATUSES = [
        'draft' => 'Draft',
        'pending_approval' => 'Pending Approval',
        'dept_chair_review' => 'Department Chair Review',
        'assoc_dean_review' => 'Associate Dean Review',
        'dean_review' => 'Dean Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'for_revisions' => 'For Revisions',
        'archived' => 'Archived',
    ];

    /**
     * User roles for approval workflow
     */
    public const APPROVAL_ROLES = [
        'faculty' => 'Faculty',
        'department_chair' => 'Department Chair',
        'associate_dean' => 'Associate Dean',
        'dean' => 'Dean',
        'superadmin' => 'Super Admin',
    ];

    /**
     * Status transitions mapping
     */
    public const STATUS_TRANSITIONS = [
        'draft' => ['pending_approval'],
        'pending_approval' => ['dept_chair_review', 'for_revisions'],
        'dept_chair_review' => ['assoc_dean_review', 'rejected'],
        'assoc_dean_review' => ['dean_review', 'rejected'],
        'dean_review' => ['approved', 'rejected'],
        'approved' => ['archived'],
        'rejected' => ['for_revisions'],
        'for_revisions' => ['pending_approval'],
        'archived' => [],
    ];

    /**
     * Get status color for badges
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'draft' => 'gray',
            'pending_approval' => 'warning',
            'dept_chair_review' => 'info',
            'assoc_dean_review' => 'primary',
            'dean_review' => 'purple',
            'approved' => 'success',
            'rejected' => 'danger',
            'for_revisions' => 'orange',
            'archived' => 'gray',
            default => 'gray',
        };
    }

    public static function getApprovalStatusColor(string $status): string
    {
        return match ($status) {
            'submitted' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get role color for badges
     */
    public static function getRoleColor(string $role): string
    {
        return match ($role) {
            'faculty' => 'primary',
            'department_chair' => 'success',
            'associate_dean' => 'warning',
            'dean' => 'danger',
            'superadmin' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get action verb color for badges
     */
    public static function getActionVerbColor(string $verb): string
    {
        // Remember - Blue shades
        $remember = [
            'remember',
            'cite',
            'define',
            'describe',
            'draw',
            'enumerate',
            'identify',
            'index',
            'indicate',
            'label',
            'list',
            'match',
            'meet',
            'name',
            'outline',
            'point',
            'quote',
            'read',
            'recall',
            'recite',
            'recognize',
            'record',
            'repeat',
            'reproduce',
            'review',
            'select',
            'state',
            'study',
            'tabulate',
            'trace',
            'write'
        ];

        // Understand - Green shades
        $understand = [
            'understand',
            'add',
            'approximate',
            'articulate',
            'associate',
            'characterize',
            'clarify',
            'classify',
            'compare',
            'compute',
            'contrast',
            'convert',
            'defend',
            'describe',
            'detail',
            'differentiate',
            'discuss',
            'distinguish',
            'elaborate',
            'estimate',
            'example',
            'explain',
            'express',
            'extend',
            'extrapolate',
            'factor',
            'generalize',
            'give',
            'infer',
            'interact',
            'interpolate',
            'interpret',
            'observe',
            'paraphrase',
            'picture graphically',
            'predict',
            'review',
            'rewrite',
            'subtract',
            'summarize',
            'translate',
            'visualize'
        ];

        // Apply - Yellow/Orange shades
        $apply = [
            'apply',
            'acquire',
            'adapt',
            'allocate',
            'alphabetize',
            'ascertain',
            'assign',
            'attain',
            'avoid',
            'back up',
            'calculate',
            'capture',
            'change',
            'classify',
            'complete',
            'compute',
            'construct',
            'customize',
            'demonstrate',
            'depreciate',
            'derive',
            'determine',
            'diminish',
            'discover',
            'draw',
            'employ',
            'examine',
            'exercise',
            'explore',
            'expose',
            'express',
            'factor',
            'figure',
            'graph',
            'handle',
            'illustrate',
            'interconvert',
            'investigate',
            'manipulate',
            'modify',
            'operate',
            'personalize',
            'plot',
            'practice',
            'predict',
            'prepare',
            'price',
            'process',
            'produce',
            'project',
            'provide',
            'relate',
            'round off',
            'sequence',
            'show',
            'simulate',
            'sketch',
            'solve',
            'subscribe',
            'tabulate',
            'transcribe',
            'translate',
            'use'
        ];

        // Analyze - Purple shades
        $analyze = [
            'analyze',
            'audit',
            'blueprint',
            'breadboard',
            'break down',
            'characterize',
            'classify',
            'compare',
            'confirm',
            'contrast',
            'correlate',
            'detect',
            'diagnose',
            'diagram',
            'differentiate',
            'discriminate',
            'dissect',
            'distinguish',
            'document',
            'ensure',
            'examine',
            'explain',
            'explore',
            'figure out',
            'file',
            'group',
            'identify',
            'illustrate',
            'infer',
            'interrupt',
            'inventory',
            'investigate',
            'layout',
            'manage',
            'maximize',
            'minimize',
            'optimize',
            'order',
            'outline',
            'point out',
            'prioritize',
            'proofread',
            'query',
            'relate',
            'select',
            'separate',
            'subdivide',
            'train',
            'transform'
        ];

        // Evaluate - Red shades
        $evaluate = [
            'evaluate',
            'appraise',
            'assess',
            'compare',
            'conclude',
            'contrast',
            'counsel',
            'criticize',
            'critique',
            'defend',
            'determine',
            'discriminate',
            'estimate',
            'explain',
            'grade',
            'hire',
            'interpret',
            'judge',
            'justify',
            'measure',
            'predict',
            'prescribe',
            'rank',
            'rate',
            'recommend',
            'release',
            'select',
            'summarize',
            'support',
            'test',
            'validate',
            'verify'
        ];

        // Create - Pink shades
        $create = [
            'create',
            'abstract',
            'animate',
            'arrange',
            'assemble',
            'budget',
            'categorize',
            'code',
            'combine',
            'compile',
            'compose',
            'construct',
            'cope',
            'correspond',
            'cultivate',
            'debug',
            'depict',
            'design',
            'develop',
            'devise',
            'dictate',
            'enhance',
            'explain',
            'facilitate',
            'format',
            'formulate',
            'generalize',
            'generate',
            'handle',
            'import',
            'improve',
            'incorporate',
            'integrate',
            'interface',
            'join',
            'lecture',
            'model',
            'modify',
            'network',
            'organize',
            'outline',
            'overhaul',
            'plan',
            'portray',
            'prepare',
            'prescribe',
            'produce',
            'program',
            'rearrange',
            'reconstruct',
            'relate',
            'reorganize',
            'revise',
            'rewrite',
            'specify',
            'summarize'
        ];

        // Convert verb to lowercase for comparison
        $lowerVerb = strtolower($verb);

        if (in_array($lowerVerb, $remember))
            return 'primary';
        if (in_array($lowerVerb, $understand))
            return 'success';
        if (in_array($lowerVerb, $apply))
            return 'warning';
        if (in_array($lowerVerb, $analyze))
            return 'purple';
        if (in_array($lowerVerb, $evaluate))
            return 'danger';
        if (in_array($lowerVerb, $create))
            return 'pink';

        return 'gray';
    }

    /**
     * Get learning modality color for badges
     */
    public static function getLearningModalityColor(string $modality): string
    {
        return match ($modality) {
            'onsite' => 'success',
            'offsite_asynchronous' => 'primary',
            'offsite_synchronous' => 'warning',
            default => 'gray',
        };
    }

    public static function getOutcomesAddressedColor(string $modality): string
    {
        return match ($modality) {
            'introduced' => 'success',
            'enhanced' => 'primary',
            'demonstrated' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Get assessment type color for badges
     */
    public static function getAssessmentTypeColor(string $assessment): string
    {
        // Formative Assessments - Blue/Info colors
        $formative = ['quiz', 'short_quiz', 'pop_quiz', 'homework', 'assignment', 'worksheet', 'discussion_board', 'peer_review', 'self_assessment', 'reflection', 'journal', 'portfolio_entry'];

        // Practical Assessments - Green colors
        $practical = ['laboratory_exercise', 'practical_exam', 'demonstration', 'simulation', 'case_study', 'problem_solving', 'hands_on_activity'];

        // Project Based Assessments - Purple colors
        $project = ['project', 'group_project', 'individual_project', 'research_project', 'presentation', 'poster_presentation', 'oral_presentation'];

        // Formal Assessments - Red colors
        $formal = ['midterm_exam', 'final_exam', 'comprehensive_exam', 'oral_exam', 'written_exam', 'take_home_exam'];

        // Performance Assessments - Orange colors
        $performance = ['performance_task', 'skill_demonstration', 'competency_assessment', 'authentic_assessment', 'rubric_based_assessment'];

        // Collaborative Assessments - Teal colors
        $collaborative = ['group_work', 'collaborative_project', 'team_presentation', 'peer_evaluation'];

        // Creative Assessments - Pink colors
        $creative = ['creative_work', 'artistic_creation', 'design_project', 'innovation_challenge'];

        // Field Based Assessments - Indigo colors
        $fieldBased = ['field_work', 'internship_evaluation', 'practicum_assessment', 'service_learning'];

        // Technology Enhanced Assessments - Cyan colors
        $technologyEnhanced = ['e_portfolio', 'digital_storytelling', 'multimedia_project', 'online_simulation', 'virtual_lab'];

        // Other Assessments - Gray colors
        $other = ['attendance', 'participation', 'engagement', 'no_assessment'];

        if (in_array($assessment, $formative))
            return 'primary';
        if (in_array($assessment, $practical))
            return 'success';
        if (in_array($assessment, $project))
            return 'purple';
        if (in_array($assessment, $formal))
            return 'danger';
        if (in_array($assessment, $performance))
            return 'warning';
        if (in_array($assessment, $collaborative))
            return 'teal';
        if (in_array($assessment, $creative))
            return 'pink';
        if (in_array($assessment, $fieldBased))
            return 'indigo';
        if (in_array($assessment, $technologyEnhanced))
            return 'cyan';
        if (in_array($assessment, $other))
            return 'gray';

        return 'gray';
    }

    /**
     * Get next status based on user role
     */
    public static function getNextStatusForRole(string $currentStatus, string $userRole): ?string
    {
        $transitions = [
            'faculty' => [
                'draft' => 'pending_approval',
                'for_revisions' => 'pending_approval',
            ],
            'department_chair' => [
                'pending_approval' => 'dept_chair_review',
                'dept_chair_review' => 'assoc_dean_review',
            ],
            'associate_dean' => [
                'assoc_dean_review' => 'dean_review',
            ],
            'dean' => [
                'dean_review' => 'approved',
            ],
        ];

        return $transitions[$userRole][$currentStatus] ?? null;
    }

    /**
     * Get action verbs as options for select fields
     */
    public static function getActionVerbOptions(): array
    {
        $flattened = [];
    
        foreach (self::ACTION_VERBS as $category => $verbs) {
            foreach ($verbs as $key => $value) {
                $flattened[$key] = $value;
            }
        }
        
        return $flattened;
    }

    public static function getApprovalRoleOptions(): array
    {
        return self::APPROVAL_ROLES;
    }

    public static function getApprovalStatusOptions(): array
    {
        return self::APPROVAL_STATUSES;
    }

    public static function getOutcomesAddressedOptions(): array
    {
        return self::OUTCOMES_ADDRESSED;
    }

    /**
     * Get learning modalities as options for select fields
     */
    public static function getLearningModalityOptions(): array
    {
        return self::LEARNING_MODALITIES;
    }

    /**
     * Get assessment types as options for select fields
     */
    public static function getAssessmentTypeOptions(): array
    {
        $flattened = [];
    
        foreach (self::ASSESSMENT_TYPES as $category => $types) {
            foreach ($types as $key => $value) {
                $flattened[$key] = $value;
            }
        }
        
        return $flattened;
    }

    /**
     * Get syllabus statuses as options for select fields
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }

    public static function renderVerbAndContent($verb, $content)
    {
        $verb = ucfirst($verb);

        // Remove the first HTML tag from $content if present
        $contentStripped = preg_replace('/^<[^>]+>/', '', $content);
        // Remove the last HTML tag from $contentStripped if present
        $contentStripped = preg_replace('/<[^>]+>$/', '', $contentStripped);

        return "<p>{$verb} {$contentStripped}</p>";
    }
}
