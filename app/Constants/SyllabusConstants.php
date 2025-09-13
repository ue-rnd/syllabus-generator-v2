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
     * Course types available
     */
    public const COURSE_TYPES = [
        'pure_onsite' => 'Pure Onsite',
        'pure_offsite' => 'Pure Offsite',
        'hybrid' => 'Hybrid',
        'hyflex' => 'HyFlex',
        'blended' => 'Blended Learning',
        'online_synchronous' => 'Online Synchronous',
        'online_asynchronous' => 'Online Asynchronous',
        'competency_based' => 'Competency-Based',
        'project_based' => 'Project-Based',
        'internship' => 'Internship/Practicum',
        'research' => 'Research-Based',
        'laboratory' => 'Laboratory/Studio',
        'field_work' => 'Field Work',
        'independent_study' => 'Independent Study',
        'others' => 'Others'
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
     * Syllabus statuses
     */
    public const STATUSES = [
        'draft' => 'Draft',
        'pending_approval' => 'Pending Approval',
        'rejected' => 'Rejected',
        'for_revisions' => 'For Revisions',
        'approved' => 'Approved',
    ];

    /**
     * Get action verbs as options for select fields
     */
    public static function getActionVerbOptions(): array
    {
        return array_combine(self::ACTION_VERBS, array_map('ucfirst', self::ACTION_VERBS));
    }

    /**
     * Get course types as options for select fields
     */
    public static function getCourseTypeOptions(): array
    {
        return self::COURSE_TYPES;
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
}
