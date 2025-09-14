<?php

namespace App\Constants;

class SyllabusConstants
{
    /**
     * Action verbs for course outcomes and learning outcomes
     */
    public const ACTION_VERBS = [
        // Knowledge (Remembering)
        'define', 'describe', 'identify', 'label', 'list', 'match', 'name', 'recall', 'recognize', 'reproduce', 'select', 'state',
        
        // Comprehension (Understanding)
        'classify', 'compare', 'contrast', 'demonstrate', 'explain', 'extend', 'illustrate', 'infer', 'interpret', 'outline', 'relate', 'rephrase', 'show', 'summarize', 'translate',
        
        // Application (Applying)
        'apply', 'build', 'choose', 'construct', 'develop', 'experiment', 'interview', 'make', 'model', 'organize', 'plan', 'select', 'solve', 'utilize',
        
        // Analysis (Analyzing)
        'analyze', 'break down', 'categorize', 'compare', 'contrast', 'debate', 'deconstruct', 'differentiate', 'discriminate', 'distinguish', 'examine', 'question', 'test',
        
        // Synthesis (Evaluating)
        'appraise', 'argue', 'assess', 'attach', 'choose', 'compare', 'defend', 'estimate', 'evaluate', 'judge', 'predict', 'rate', 'score', 'select', 'support', 'value',
        
        // Evaluation (Creating)
        'assemble', 'compile', 'compose', 'create', 'design', 'develop', 'devise', 'formulate', 'generate', 'make', 'originate', 'plan', 'produce', 'role-play', 'synthesize'
    ];

    /**
     * Learning activity modalities
     */
    public const LEARNING_MODALITIES = [
        'onsite' => 'Onsite',
        'offsite_asynchronous' => 'Offsite Asynchronous',
        'offsite_synchronous' => 'Offsite Synchronous'
    ];

    /**
     * Assessment types for weekly assessments
     */
    public const ASSESSMENT_TYPES = [
        // Formative Assessments
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
        'portfolio_entry' => 'Portfolio Entry',
        
        // Practical Assessments
        'laboratory_exercise' => 'Laboratory Exercise',
        'practical_exam' => 'Practical Exam',
        'demonstration' => 'Demonstration',
        'simulation' => 'Simulation',
        'case_study' => 'Case Study',
        'problem_solving' => 'Problem Solving',
        'hands_on_activity' => 'Hands-on Activity',
        
        // Project-Based Assessments
        'project' => 'Project',
        'group_project' => 'Group Project',
        'individual_project' => 'Individual Project',
        'research_project' => 'Research Project',
        'presentation' => 'Presentation',
        'poster_presentation' => 'Poster Presentation',
        'oral_presentation' => 'Oral Presentation',
        
        // Formal Assessments
        'midterm_exam' => 'Midterm Exam',
        'final_exam' => 'Final Exam',
        'comprehensive_exam' => 'Comprehensive Exam',
        'oral_exam' => 'Oral Exam',
        'written_exam' => 'Written Exam',
        'take_home_exam' => 'Take-home Exam',
        
        // Performance Assessments
        'performance_task' => 'Performance Task',
        'skill_demonstration' => 'Skill Demonstration',
        'competency_assessment' => 'Competency Assessment',
        'authentic_assessment' => 'Authentic Assessment',
        'rubric_based_assessment' => 'Rubric-based Assessment',
        
        // Collaborative Assessments
        'group_work' => 'Group Work',
        'collaborative_project' => 'Collaborative Project',
        'team_presentation' => 'Team Presentation',
        'peer_evaluation' => 'Peer Evaluation',
        
        // Creative Assessments
        'creative_work' => 'Creative Work',
        'artistic_creation' => 'Artistic Creation',
        'design_project' => 'Design Project',
        'innovation_challenge' => 'Innovation Challenge',
        
        // Field-Based Assessments
        'field_work' => 'Field Work',
        'internship_evaluation' => 'Internship Evaluation',
        'practicum_assessment' => 'Practicum Assessment',
        'service_learning' => 'Service Learning',
        
        // Technology-Enhanced Assessments
        'e_portfolio' => 'E-Portfolio',
        'digital_storytelling' => 'Digital Storytelling',
        'multimedia_project' => 'Multimedia Project',
        'online_simulation' => 'Online Simulation',
        'virtual_lab' => 'Virtual Lab',
        
        // Other Assessments
        'attendance' => 'Attendance',
        'participation' => 'Participation',
        'engagement' => 'Engagement',
        'no_assessment' => 'No Assessment'
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
        // Knowledge (Remembering) - Blue shades
        $knowledge = ['define', 'describe', 'identify', 'label', 'list', 'match', 'name', 'recall', 'recognize', 'reproduce', 'select', 'state'];
        
        // Comprehension (Understanding) - Green shades
        $comprehension = ['classify', 'compare', 'contrast', 'demonstrate', 'explain', 'extend', 'illustrate', 'infer', 'interpret', 'outline', 'relate', 'rephrase', 'show', 'summarize', 'translate'];
        
        // Application (Applying) - Yellow/Orange shades
        $application = ['apply', 'build', 'choose', 'construct', 'develop', 'experiment', 'interview', 'make', 'model', 'organize', 'plan', 'select', 'solve', 'utilize'];
        
        // Analysis (Analyzing) - Purple shades
        $analysis = ['analyze', 'break down', 'categorize', 'compare', 'contrast', 'debate', 'deconstruct', 'differentiate', 'discriminate', 'distinguish', 'examine', 'question', 'test'];
        
        // Synthesis (Evaluating) - Red shades
        $synthesis = ['appraise', 'argue', 'assess', 'attach', 'choose', 'compare', 'defend', 'estimate', 'evaluate', 'judge', 'predict', 'rate', 'score', 'select', 'support', 'value'];
        
        // Evaluation (Creating) - Pink shades
        $evaluation = ['assemble', 'compile', 'compose', 'create', 'design', 'develop', 'devise', 'formulate', 'generate', 'make', 'originate', 'plan', 'produce', 'role-play', 'synthesize'];

        if (in_array($verb, $knowledge)) return 'primary';
        if (in_array($verb, $comprehension)) return 'success';
        if (in_array($verb, $application)) return 'warning';
        if (in_array($verb, $analysis)) return 'purple';
        if (in_array($verb, $synthesis)) return 'danger';
        if (in_array($verb, $evaluation)) return 'pink';
        
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

    /**
     * Get assessment type color for badges
     */
    public static function getAssessmentTypeColor(string $assessment): string
    {
        // Formative assessments - Blue/Info colors
        $formative = ['quiz', 'short_quiz', 'pop_quiz', 'homework', 'assignment', 'worksheet', 'discussion_board', 'peer_review', 'self_assessment', 'reflection', 'journal', 'portfolio_entry'];
        
        // Practical assessments - Green colors
        $practical = ['laboratory_exercise', 'practical_exam', 'demonstration', 'simulation', 'case_study', 'problem_solving', 'hands_on_activity'];
        
        // Project-based assessments - Purple colors
        $project = ['project', 'group_project', 'individual_project', 'research_project', 'presentation', 'poster_presentation', 'oral_presentation'];
        
        // Formal assessments - Red colors
        $formal = ['midterm_exam', 'final_exam', 'comprehensive_exam', 'oral_exam', 'written_exam', 'take_home_exam'];
        
        // Performance assessments - Orange colors
        $performance = ['performance_task', 'skill_demonstration', 'competency_assessment', 'authentic_assessment', 'rubric_based_assessment'];

        if (in_array($assessment, $formative)) return 'primary';
        if (in_array($assessment, $practical)) return 'success';
        if (in_array($assessment, $project)) return 'purple';
        if (in_array($assessment, $formal)) return 'danger';
        if (in_array($assessment, $performance)) return 'warning';
        
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
        return array_combine(self::ACTION_VERBS, array_map('ucfirst', self::ACTION_VERBS));
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
        return self::ASSESSMENT_TYPES;
    }

    /**
     * Get syllabus statuses as options for select fields
     */
    public static function getStatusOptions(): array
    {
        return self::STATUSES;
    }

    public static function renderVerbAndContent($verb, $content) {
        $verb = ucfirst($verb);

        // Remove the first HTML tag from $content if present
        $contentStripped = preg_replace('/^<[^>]+>/', '', $content);

        return "<p>{$verb} {$contentStripped}</p>";
    }
}
