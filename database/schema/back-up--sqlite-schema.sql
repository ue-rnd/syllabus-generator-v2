CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "roles_name_guard_name_unique" on "roles"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "courses"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar not null,
  "description" text,
  "outcomes" text,
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "college_id" integer not null,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "credit_units_lecture" numeric not null default '0',
  "credit_units_laboratory" numeric not null default '0',
  "course_type" varchar not null default 'pure_onsite',
  "prerequisite_courses" text,
  foreign key("college_id") references "colleges"("id") on delete cascade
);
CREATE UNIQUE INDEX "courses_name_unique" on "courses"("name");
CREATE UNIQUE INDEX "courses_code_unique" on "courses"("code");
CREATE TABLE IF NOT EXISTS "course_program"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "program_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("course_id") references "courses"("id") on delete cascade,
  foreign key("program_id") references "programs"("id") on delete cascade
);
CREATE UNIQUE INDEX "course_program_course_id_program_id_unique" on "course_program"(
  "course_id",
  "program_id"
);
CREATE TABLE IF NOT EXISTS "departments"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default('1'),
  "sort_order" integer not null default('0'),
  "college_id" integer not null,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "department_chair_id" integer,
  foreign key("college_id") references colleges("id") on delete cascade on update no action,
  foreign key("department_chair_id") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "departments_name_unique" on "departments"("name");
CREATE TABLE IF NOT EXISTS "programs"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "level" varchar,
  "code" varchar not null,
  "description" text,
  "outcomes" text,
  "objectives" text,
  "is_active" tinyint(1) not null default('1'),
  "sort_order" integer not null default('0'),
  "department_id" integer not null,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("department_id") references departments("id") on delete cascade on update no action
);
CREATE UNIQUE INDEX "programs_code_unique" on "programs"("code");
CREATE UNIQUE INDEX "programs_name_unique" on "programs"("name");
CREATE TABLE IF NOT EXISTS "colleges"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar not null,
  "description" text,
  "mission" text,
  "vision" text,
  "core_values" text,
  "objectives" text,
  "is_active" tinyint(1) not null default('1'),
  "sort_order" integer not null default('0'),
  "logo_path" varchar,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "dean_id" integer,
  "associate_dean_id" integer,
  foreign key("associate_dean_id") references users("id") on delete set null on update no action,
  foreign key("dean_id") references users("id") on delete set null on update no action
);
CREATE UNIQUE INDEX "colleges_code_unique" on "colleges"("code");
CREATE UNIQUE INDEX "colleges_name_unique" on "colleges"("name");
CREATE TABLE IF NOT EXISTS "settings"(
  "key" varchar not null,
  "label" varchar not null,
  "value" text,
  "attributes" text,
  "type" varchar not null,
  "sort_order" integer,
  "category" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "lastname" varchar,
  "firstname" varchar,
  "middlename" varchar,
  "position" varchar,
  "is_active" tinyint(1) not null default('1'),
  "last_login_at" datetime,
  "last_login_ip" varchar,
  "deleted_at" datetime,
  "college_id" integer,
  "department_id" integer,
  foreign key("college_id") references colleges("id") on delete set null on update no action,
  foreign key("department_id") references "departments"("id") on delete set null
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "blooms_taxonomy_verbs"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "label" varchar not null,
  "category" varchar check("category" in('Remember', 'Understand', 'Apply', 'Analyze', 'Evaluate', 'Create')) not null,
  "sort_order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "blooms_taxonomy_verbs_category_sort_order_index" on "blooms_taxonomy_verbs"(
  "category",
  "sort_order"
);
CREATE INDEX "blooms_taxonomy_verbs_is_active_category_index" on "blooms_taxonomy_verbs"(
  "is_active",
  "category"
);
CREATE UNIQUE INDEX "blooms_taxonomy_verbs_key_unique" on "blooms_taxonomy_verbs"(
  "key"
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" varchar not null,
  "type" varchar not null,
  "notifiable_type" varchar not null,
  "notifiable_id" integer not null,
  "data" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications"(
  "notifiable_type",
  "notifiable_id"
);
CREATE TABLE IF NOT EXISTS "syllabi"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "course_id" integer not null,
  "course_outcomes" text,
  "learning_matrix" text,
  "textbook_references" text,
  "adaptive_digital_solutions" text,
  "online_references" text,
  "other_references" text,
  "grading_system" text,
  "classroom_policies" text,
  "consultation_hours" text,
  "principal_prepared_by" integer not null,
  "prepared_by" text,
  "reviewed_by" integer,
  "recommending_approval" integer,
  "approved_by" integer,
  "sort_order" integer not null default('0'),
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "default_lecture_hours" numeric not null default('0'),
  "default_laboratory_hours" numeric not null default('0'),
  "status" varchar not null default('draft'),
  "submitted_at" datetime,
  "dept_chair_reviewed_at" datetime,
  "assoc_dean_reviewed_at" datetime,
  "dean_approved_at" datetime,
  "approval_history" text,
  "rejection_comments" text,
  "rejected_by_role" varchar,
  "rejected_at" datetime,
  "version" integer not null default('1'),
  "parent_syllabus_id" integer,
  "week_prelim" integer not null default('0'),
  "week_midterm" integer not null default('0'),
  "week_final" integer not null default('0'),
  "ay_start" integer not null default('0'),
  "ay_end" integer not null default('0'),
  "program_outcomes" text,
  "qa_reviewed_at" datetime,
  "qa_reviewed_by" integer,
  foreign key("parent_syllabus_id") references syllabi("id") on delete set null on update no action,
  foreign key("course_id") references courses("id") on delete cascade on update no action,
  foreign key("principal_prepared_by") references users("id") on delete cascade on update no action,
  foreign key("reviewed_by") references users("id") on delete set null on update no action,
  foreign key("recommending_approval") references users("id") on delete set null on update no action,
  foreign key("approved_by") references users("id") on delete set null on update no action,
  foreign key("qa_reviewed_by") references "users"("id") on delete set null
);
CREATE INDEX "syllabi_course_id_index" on "syllabi"("course_id");
CREATE INDEX "syllabi_course_id_version_index" on "syllabi"(
  "course_id",
  "version"
);
CREATE INDEX "syllabi_parent_syllabus_id_index" on "syllabi"(
  "parent_syllabus_id"
);
CREATE INDEX "syllabi_status_submitted_at_index" on "syllabi"(
  "status",
  "submitted_at"
);
CREATE TABLE IF NOT EXISTS "syllabus_suggestions"(
  "id" integer primary key autoincrement not null,
  "syllabus_id" integer not null,
  "suggested_by" integer not null,
  "field_name" varchar not null,
  "current_value" text,
  "suggested_value" text not null,
  "reason" text,
  "status" varchar check("status" in('pending', 'approved', 'rejected')) not null default 'pending',
  "reviewed_at" datetime,
  "reviewed_by" integer,
  "review_comments" text,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("syllabus_id") references "syllabi"("id") on delete cascade,
  foreign key("suggested_by") references "users"("id") on delete cascade,
  foreign key("reviewed_by") references "users"("id") on delete set null
);
CREATE INDEX "syllabus_suggestions_syllabus_id_status_index" on "syllabus_suggestions"(
  "syllabus_id",
  "status"
);
CREATE INDEX "syllabus_suggestions_suggested_by_status_index" on "syllabus_suggestions"(
  "suggested_by",
  "status"
);
CREATE INDEX "syllabus_suggestions_field_name_index" on "syllabus_suggestions"(
  "field_name"
);
CREATE TABLE IF NOT EXISTS "quality_standards"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text not null,
  "type" varchar check("type" in('institutional', 'accreditation', 'departmental', 'program', 'course')) not null,
  "category" varchar check("category" in('content', 'structure', 'assessment', 'learning_outcomes', 'resources', 'policies')) not null,
  "criteria" text,
  "minimum_score" numeric not null default '0',
  "weight" numeric not null default '1',
  "is_mandatory" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "institution_id" integer,
  "college_id" integer,
  "department_id" integer,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("college_id") references "colleges"("id") on delete cascade,
  foreign key("department_id") references "departments"("id") on delete cascade
);
CREATE INDEX "quality_standards_type_is_active_index" on "quality_standards"(
  "type",
  "is_active"
);
CREATE INDEX "quality_standards_category_is_active_index" on "quality_standards"(
  "category",
  "is_active"
);
CREATE INDEX "quality_standards_college_id_is_active_index" on "quality_standards"(
  "college_id",
  "is_active"
);
CREATE INDEX "quality_standards_department_id_is_active_index" on "quality_standards"(
  "department_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "standards_compliances"(
  "id" integer primary key autoincrement not null,
  "syllabus_id" integer not null,
  "quality_standard_id" integer not null,
  "compliance_status" varchar check("compliance_status" in('not_assessed', 'compliant', 'partially_compliant', 'non_compliant')) not null default 'not_assessed',
  "score" numeric,
  "notes" text,
  "checked_by" integer,
  "checked_at" datetime,
  "evidence" text,
  "remediation_required" tinyint(1) not null default '0',
  "remediation_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("syllabus_id") references "syllabi"("id") on delete cascade,
  foreign key("quality_standard_id") references "quality_standards"("id") on delete cascade,
  foreign key("checked_by") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "standards_compliances_syllabus_id_quality_standard_id_unique" on "standards_compliances"(
  "syllabus_id",
  "quality_standard_id"
);
CREATE INDEX "standards_compliances_compliance_status_index" on "standards_compliances"(
  "compliance_status"
);
CREATE INDEX "standards_compliances_checked_at_index" on "standards_compliances"(
  "checked_at"
);
CREATE INDEX "standards_compliances_remediation_required_index" on "standards_compliances"(
  "remediation_required"
);
CREATE TABLE IF NOT EXISTS "quality_checklists"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "type" varchar check("type" in('basic', 'comprehensive', 'accreditation', 'custom')) not null,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "college_id" integer,
  "department_id" integer,
  "created_by" integer,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("college_id") references "colleges"("id") on delete cascade,
  foreign key("department_id") references "departments"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete set null
);
CREATE INDEX "quality_checklists_type_is_active_index" on "quality_checklists"(
  "type",
  "is_active"
);
CREATE INDEX "quality_checklists_college_id_is_active_index" on "quality_checklists"(
  "college_id",
  "is_active"
);
CREATE INDEX "quality_checklists_department_id_is_active_index" on "quality_checklists"(
  "department_id",
  "is_active"
);
CREATE TABLE IF NOT EXISTS "quality_audit_actions"(
  "id" integer primary key autoincrement not null,
  "quality_audit_id" integer not null,
  "quality_audit_finding_id" integer,
  "title" varchar not null,
  "description" text not null,
  "action_type" varchar check("action_type" in('corrective', 'preventive', 'improvement', 'training', 'documentation', 'process_change')) not null,
  "priority" varchar check("priority" in('critical', 'high', 'medium', 'low')) not null,
  "assigned_to" integer,
  "due_date" date,
  "status" varchar check("status" in('pending', 'in_progress', 'completed', 'cancelled', 'on_hold')) not null default 'pending',
  "progress_percentage" integer not null default '0',
  "completion_date" datetime,
  "notes" text,
  "evidence" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("quality_audit_id") references "quality_audits"("id") on delete cascade,
  foreign key("quality_audit_finding_id") references "quality_audit_findings"("id") on delete cascade,
  foreign key("assigned_to") references "users"("id") on delete set null
);
CREATE INDEX "quality_audit_actions_quality_audit_id_status_index" on "quality_audit_actions"(
  "quality_audit_id",
  "status"
);
CREATE INDEX "quality_audit_actions_assigned_to_status_index" on "quality_audit_actions"(
  "assigned_to",
  "status"
);
CREATE INDEX "quality_audit_actions_priority_status_index" on "quality_audit_actions"(
  "priority",
  "status"
);
CREATE INDEX "quality_audit_actions_due_date_status_index" on "quality_audit_actions"(
  "due_date",
  "status"
);
CREATE TABLE IF NOT EXISTS "quality_audit_findings"(
  "id" integer primary key autoincrement not null,
  "quality_audit_id" integer not null,
  "syllabus_id" integer,
  "title" varchar not null,
  "description" text not null,
  "severity" varchar check("severity" in('critical', 'high', 'medium', 'low', 'info')) not null,
  "category" varchar check("category" in('content', 'structure', 'compliance', 'quality', 'documentation', 'process')) not null,
  "evidence" text,
  "recommendation" text,
  "status" varchar check("status" in('open', 'in_progress', 'resolved', 'closed')) not null default 'open',
  "assigned_to" integer,
  "due_date" date,
  "resolved_at" datetime,
  "resolution_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("quality_audit_id") references "quality_audits"("id") on delete cascade,
  foreign key("syllabus_id") references "syllabi"("id") on delete cascade,
  foreign key("assigned_to") references "users"("id") on delete set null
);
CREATE INDEX "quality_audit_findings_quality_audit_id_status_index" on "quality_audit_findings"(
  "quality_audit_id",
  "status"
);
CREATE INDEX "quality_audit_findings_syllabus_id_status_index" on "quality_audit_findings"(
  "syllabus_id",
  "status"
);
CREATE INDEX "quality_audit_findings_severity_status_index" on "quality_audit_findings"(
  "severity",
  "status"
);
CREATE INDEX "quality_audit_findings_due_date_status_index" on "quality_audit_findings"(
  "due_date",
  "status"
);
CREATE TABLE IF NOT EXISTS "quality_audits"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "audit_type" varchar check("audit_type" in('compliance', 'quality_improvement', 'accreditation', 'internal', 'external')) not null,
  "scope" varchar check("scope" in('institution', 'college', 'department', 'program', 'course')) not null,
  "start_date" date not null,
  "end_date" date,
  "status" varchar check("status" in('planned', 'in_progress', 'completed', 'cancelled')) not null default 'planned',
  "auditor_id" integer,
  "college_id" integer,
  "department_id" integer,
  "criteria" text,
  "summary" text,
  "recommendations" text,
  "follow_up_required" tinyint(1) not null default '0',
  "follow_up_date" date,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("auditor_id") references "users"("id") on delete set null,
  foreign key("college_id") references "colleges"("id") on delete cascade,
  foreign key("department_id") references "departments"("id") on delete cascade
);
CREATE INDEX "quality_audits_audit_type_status_index" on "quality_audits"(
  "audit_type",
  "status"
);
CREATE INDEX "quality_audits_scope_status_index" on "quality_audits"(
  "scope",
  "status"
);
CREATE INDEX "quality_audits_start_date_end_date_index" on "quality_audits"(
  "start_date",
  "end_date"
);
CREATE INDEX "quality_audits_college_id_status_index" on "quality_audits"(
  "college_id",
  "status"
);
CREATE INDEX "quality_audits_department_id_status_index" on "quality_audits"(
  "department_id",
  "status"
);
CREATE TABLE IF NOT EXISTS "quality_checklist_items"(
  "id" integer primary key autoincrement not null,
  "quality_checklist_id" integer not null,
  "title" varchar not null,
  "description" text,
  "field_to_check" varchar not null,
  "validation_rule" varchar check("validation_rule" in('required', 'min_length', 'max_length', 'contains_keywords', 'array_min_items', 'array_max_items', 'numeric_range', 'date_range', 'format_check', 'completeness')) not null,
  "validation_parameters" text,
  "weight" numeric not null default '1',
  "is_mandatory" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("quality_checklist_id") references "quality_checklists"("id") on delete cascade
);
CREATE INDEX "quality_checklist_items_quality_checklist_id_is_active_index" on "quality_checklist_items"(
  "quality_checklist_id",
  "is_active"
);
CREATE INDEX "quality_checklist_items_validation_rule_index" on "quality_checklist_items"(
  "validation_rule"
);
CREATE INDEX "quality_checklist_items_is_mandatory_index" on "quality_checklist_items"(
  "is_mandatory"
);
CREATE TABLE IF NOT EXISTS "syllabus_quality_checks"(
  "id" integer primary key autoincrement not null,
  "syllabus_id" integer not null,
  "quality_checklist_id" integer not null,
  "checked_by" integer,
  "checked_at" datetime,
  "overall_score" numeric,
  "status" varchar check("status" in('in_progress', 'completed', 'passed', 'requires_improvement', 'failed')) not null default 'in_progress',
  "item_results" text,
  "notes" text,
  "auto_generated" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("syllabus_id") references "syllabi"("id") on delete cascade,
  foreign key("quality_checklist_id") references "quality_checklists"("id") on delete cascade,
  foreign key("checked_by") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "syllabus_quality_checks_syllabus_id_quality_checklist_id_unique" on "syllabus_quality_checks"(
  "syllabus_id",
  "quality_checklist_id"
);
CREATE INDEX "syllabus_quality_checks_status_index" on "syllabus_quality_checks"(
  "status"
);
CREATE INDEX "syllabus_quality_checks_checked_at_index" on "syllabus_quality_checks"(
  "checked_at"
);
CREATE INDEX "syllabus_quality_checks_auto_generated_index" on "syllabus_quality_checks"(
  "auto_generated"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_09_12_045932_create_colleges_table',1);
INSERT INTO migrations VALUES(5,'2025_09_12_050121_create_departments_table',1);
INSERT INTO migrations VALUES(6,'2025_09_12_050147_create_permission_tables',1);
INSERT INTO migrations VALUES(7,'2025_09_12_051157_create_programs_table',1);
INSERT INTO migrations VALUES(8,'2025_09_12_051244_create_courses_table',1);
INSERT INTO migrations VALUES(9,'2025_09_12_054403_add_user_properties',1);
INSERT INTO migrations VALUES(10,'2025_09_12_075755_create_course_program_table',1);
INSERT INTO migrations VALUES(11,'2025_09_13_025621_add_syllabus_fields_to_courses_table',1);
INSERT INTO migrations VALUES(12,'2025_09_13_025824_add_dean_fields_to_colleges_table',1);
INSERT INTO migrations VALUES(13,'2025_09_13_030024_create_syllabi_table',1);
INSERT INTO migrations VALUES(14,'2025_09_13_040659_update_syllabi_table_structure',1);
INSERT INTO migrations VALUES(15,'2025_09_13_064725_add_department_chair_to_departments_table',1);
INSERT INTO migrations VALUES(16,'2025_09_13_064836_add_status_to_syllabi_table',1);
INSERT INTO migrations VALUES(17,'2025_09_13_102500_add_approval_workflow_to_syllabi_table',1);
INSERT INTO migrations VALUES(18,'2025_09_13_131231_add_college_attribute_to_users',1);
INSERT INTO migrations VALUES(19,'2025_09_13_161210_change_programs_level_column_to_nullable_string',1);
INSERT INTO migrations VALUES(20,'2025_09_13_174333_convert_college_rich_editor_fields_to_text',1);
INSERT INTO migrations VALUES(21,'2025_09_15_021829_add_prelimmidtermfinals_acadyear__to_syllabus',1);
INSERT INTO migrations VALUES(22,'2025_09_15_072758_add_program_outcomes_to_syllabi',1);
INSERT INTO migrations VALUES(23,'2025_09_15_084213_create_settings_table',1);
INSERT INTO migrations VALUES(24,'2025_09_16_064806_finalize_syllabi_table',1);
INSERT INTO migrations VALUES(25,'2025_09_18_120000_normalize_program_outcomes_case',1);
INSERT INTO migrations VALUES(26,'2025_09_18_155545_add_department_to_users',1);
INSERT INTO migrations VALUES(27,'2025_09_21_155438_create_blooms_taxonomy_verbs_table',2);
INSERT INTO migrations VALUES(28,'2025_09_21_155926_create_notifications_table',3);
INSERT INTO migrations VALUES(29,'2025_09_21_162036_add_qa_fields_to_syllabi_table',4);
INSERT INTO migrations VALUES(30,'2025_09_21_163432_create_syllabus_suggestions_table',5);
INSERT INTO migrations VALUES(31,'2025_09_22_050628_create_quality_standards_table',6);
INSERT INTO migrations VALUES(32,'2025_09_22_050644_create_standards_compliances_table',6);
INSERT INTO migrations VALUES(33,'2025_09_22_050651_create_quality_checklists_table',6);
INSERT INTO migrations VALUES(34,'2025_09_22_050656_create_quality_audit_actions_table',6);
INSERT INTO migrations VALUES(35,'2025_09_22_050656_create_quality_audit_findings_table',6);
INSERT INTO migrations VALUES(36,'2025_09_22_050656_create_quality_audits_table',6);
INSERT INTO migrations VALUES(37,'2025_09_22_050656_create_quality_checklist_items_table',6);
INSERT INTO migrations VALUES(38,'2025_09_22_050656_create_syllabus_quality_checks_table',6);
