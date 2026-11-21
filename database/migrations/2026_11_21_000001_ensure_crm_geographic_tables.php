<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS crm;');
        DB::statement('CREATE SCHEMA IF NOT EXISTS master;');
        DB::statement('CREATE SCHEMA IF NOT EXISTS finance;');
        DB::statement('CREATE SCHEMA IF NOT EXISTS admissions;');

        DB::unprepared("
            -- 1. Geographic Tables
            CREATE TABLE IF NOT EXISTS crm.countries (
                country_id integer NOT NULL PRIMARY KEY,
                country_code character varying(10) NOT NULL,
                country_name character varying(100),
                country_phonecode character varying(100),
                lock_status integer DEFAULT 0,
                log_userid character varying(20),
                log_timestamp timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                log_ipaddress character varying(100)
            );

            CREATE TABLE IF NOT EXISTS crm.states (
                state_id integer NOT NULL PRIMARY KEY,
                state_code character varying(20),
                state_name character varying(100),
                country_id integer,
                lock_status integer DEFAULT 0,
                log_userid character varying(20),
                log_timestamp timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                log_ipaddress character varying(100)
            );

            CREATE TABLE IF NOT EXISTS crm.district (
                \"DISTRICT_ID\" integer NOT NULL PRIMARY KEY,
                \"DISTRICT_CODE\" character varying(20) NOT NULL,
                \"DISTRICT_NAME\" character varying(100),
                \"STATE_ID\" integer NOT NULL,
                \"COUNTRY_ID\" integer NOT NULL,
                \"LOCK_STATUS\" integer DEFAULT 0,
                \"LOG_USERID\" character varying(20),
                \"LOG_TIMESTAMP\" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                \"LOG_IPADDRESS\" character varying(100)
            );

            CREATE SEQUENCE IF NOT EXISTS crm.states_state_id_seq OWNED BY crm.states.state_id;
            SELECT setval('crm.states_state_id_seq', COALESCE((SELECT MAX(state_id) FROM crm.states), 0) + 1, false);
            ALTER TABLE crm.states ALTER COLUMN state_id SET DEFAULT nextval('crm.states_state_id_seq');

            CREATE SEQUENCE IF NOT EXISTS crm.district_district_id_seq OWNED BY crm.district.\"DISTRICT_ID\";
            SELECT setval('crm.district_district_id_seq', COALESCE((SELECT MAX(\"DISTRICT_ID\") FROM crm.district), 0) + 1, false);
            ALTER TABLE crm.district ALTER COLUMN \"DISTRICT_ID\" SET DEFAULT nextval('crm.district_district_id_seq');

            -- 2. Student Registration Core
            CREATE SEQUENCE IF NOT EXISTS crm.student_registration_applinano_seq START WITH 1;

            CREATE TABLE IF NOT EXISTS crm.student_registration (
                applinano character varying(20) NOT NULL PRIMARY KEY,
                name character varying(100) NOT NULL,
                phoneno character varying(20) NOT NULL,
                campuscode character varying(50),
                emailid character varying(255),
                countryid integer,
                stateid integer,
                districtid integer,
                programlevel character varying(50),
                programid integer,
                branchid integer,
                ableto boolean DEFAULT true,
                crmid character varying(50),
                stage1 character varying(20) DEFAULT 'not_paid',
                stage2 character varying(20) DEFAULT 'NA',
                stage3 character varying(20) DEFAULT 'NA',
                stage4 character varying(20) DEFAULT 'NA',
                stage5 character varying(20) DEFAULT 'NA',
                stage6 character varying(20) DEFAULT 'NA',
                stage7 character varying(20) DEFAULT 'NA',
                stage8 character varying(20) DEFAULT 'NA',
                std_status character varying(30),
                log_userid character varying(50),
                log_ipaddress character varying(255),
                log_timestamp timestamp without time zone DEFAULT now()
            );

            -- 3. Student Personal Info & Academic Details
            CREATE TABLE IF NOT EXISTS crm.student_personal_info (
                crmid VARCHAR(50) PRIMARY KEY,
                applino VARCHAR(20) NOT NULL,
                phoneno VARCHAR(20) NOT NULL,
                dob DATE,
                gender VARCHAR(20),
                parent_name VARCHAR(255),
                parentphono VARCHAR(20),
                caste_category VARCHAR(50),
                referedby VARCHAR(255),
                referedempcode VARCHAR(50),
                country VARCHAR(100),
                state VARCHAR(100),
                district VARCHAR(100),
                city_mandal VARCHAR(100),
                village VARCHAR(100),
                pincode VARCHAR(10),
                terms_accepted VARCHAR(10) DEFAULT 'no',
                log_userid VARCHAR(50),
                log_ipaddress VARCHAR(255),
                log_timestamp TIMESTAMP DEFAULT now()
            );

            CREATE TABLE IF NOT EXISTS crm.student_academic_details (
                crmid VARCHAR(50) PRIMARY KEY,
                applino VARCHAR(20) NOT NULL,
                phoneno VARCHAR(20) NOT NULL,
                tenth_board VARCHAR(100),
                tenth_school_name VARCHAR(255),
                tenth_school_state VARCHAR(100),
                tenth_school_district VARCHAR(100),
                tenth_passed_year VARCHAR(10),
                tenth_grade_type VARCHAR(20),
                tenth_grade_value VARCHAR(20),
                pu2_board VARCHAR(100),
                pu2_college_name VARCHAR(255),
                pu2_college_state VARCHAR(100),
                pu2_college_district VARCHAR(100),
                pu2_passed_year VARCHAR(10),
                pu2_grade_type VARCHAR(20),
                pu2_grade_value VARCHAR(20),
                ug_university VARCHAR(255),
                ug_college_name VARCHAR(255),
                ug_passed_year VARCHAR(10),
                ug_grade_type VARCHAR(20),
                ug_grade_value VARCHAR(20),
                pg_university VARCHAR(255),
                pg_college_name VARCHAR(255),
                pg_passed_year VARCHAR(10),
                pg_grade_type VARCHAR(20),
                pg_grade_value VARCHAR(20),
                log_userid VARCHAR(50),
                log_ipaddress VARCHAR(255),
                log_timestamp TIMESTAMP DEFAULT now()
            );

            -- 4. CRM Leads
            CREATE TABLE IF NOT EXISTS crm.leads (
                phoneno bigint NOT NULL PRIMARY KEY,
                phnostatus character varying(255),
                enquirydate date,
                log_userid character varying(100),
                log_ipaddress character varying(255),
                log_timestamp timestamp without time zone,
                applino bigint,
                empcode character varying(50),
                allocated_at timestamp without time zone
            );

            -- 5. Finance Exam Schedule
            CREATE TABLE IF NOT EXISTS finance.exam_schedule (
                applino VARCHAR(30) NOT NULL PRIMARY KEY,
                crmid VARCHAR(50),
                phoneno VARCHAR(20),
                examdate DATE,
                exam_slot VARCHAR(50),
                exam_status INTEGER DEFAULT 0,
                loguserid VARCHAR(50),
                logdatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ipaddress VARCHAR(50)
            );

            -- 6. Master Communication & SMS Logs
            CREATE TABLE IF NOT EXISTS master.communication_logs (
                id bigserial PRIMARY KEY,
                channel varchar(20) DEFAULT 'whatsapp',
                wauser varchar(100),
                waregisterno varchar(50),
                wapptophone varchar(20),
                jiotraceid varchar(100),
                template_name varchar(100),
                wapptext text,
                var1 text,
                var2 text,
                status varchar(50) DEFAULT 'SUBMITTED',
                ipaddr varchar(45),
                response_payload jsonb,
                created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
            );

            CREATE OR REPLACE VIEW master.wapp_logs AS SELECT * FROM master.communication_logs;
            CREATE OR REPLACE VIEW master.wapplogs AS SELECT * FROM master.communication_logs;

            CREATE TABLE IF NOT EXISTS master.smslogs (
                \"smsId\" bigserial PRIMARY KEY,
                smsuser varchar(100),
                smsregisterno varchar(50),
                smstophone varchar(20),
                jiotraceid varchar(120),
                smstext text,
                datetime timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                ipaddr varchar(50),
                status varchar(50),
                var1 text,
                var2 text,
                var3 text,
                var4 text,
                var5 text
            );
        ");

        try {
            $count = DB::table('crm.countries')->count();
            if ($count === 0) {
                $masterSql = database_path('sql/vignan_master_data.sql');
                if (File::exists($masterSql)) {
                    DB::unprepared(File::get($masterSql));
                }
            }
        } catch (\Throwable $e) {}

        try {
            $districtCount = DB::table('crm.district')->count();
            if ($districtCount === 0) {
                $intlSql = database_path('sql/seed_international_states_and_districts.sql');
                if (File::exists($intlSql)) {
                    DB::unprepared(File::get($intlSql));
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retain core database tables
    }
};
