--
-- PostgreSQL database dump
--

\restrict lx5uSlDX4GCMLg4NrfJInjB9z9diculIjUSjbYS5h3S3u1FJpYniw244pm3IAy3

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: admissions; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA IF NOT EXISTS admissions;


--
-- Name: crm; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA IF NOT EXISTS crm;


--
-- Name: finance; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA IF NOT EXISTS finance;


--
-- Name: master; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA IF NOT EXISTS master;


--
-- Name: student_documents_doc_id_seq; Type: SEQUENCE; Schema: admissions; Owner: -
--

CREATE SEQUENCE admissions.student_documents_doc_id_seq
    START WITH 120
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: student_documents; Type: TABLE; Schema: admissions; Owner: -
--

CREATE TABLE admissions.student_documents (
    doc_id integer DEFAULT nextval('admissions.student_documents_doc_id_seq'::regclass) NOT NULL,
    application_no character varying(20) NOT NULL,
    register_no character varying(50),
    docno character varying(50),
    docname character varying(255),
    doctype character varying(50),
    docfile text,
    lock_status integer DEFAULT 0 NOT NULL,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: document; Type: VIEW; Schema: admissions; Owner: -
--

CREATE VIEW admissions.document AS
 SELECT doc_id,
    application_no,
    register_no,
    docno,
    docname,
    doctype,
    docfile,
    lock_status,
    log_userid,
    log_ipaddress,
    log_timestamp
   FROM admissions.student_documents;


--
-- Name: student_education_info; Type: TABLE; Schema: admissions; Owner: -
--

CREATE TABLE admissions.student_education_info (
    edu_id integer NOT NULL,
    application_no character varying(20) NOT NULL,
    register_no character varying(50),
    qual_level character varying(50),
    qual_name character varying(255),
    institution_name character varying(255),
    institution_type character varying(50),
    board_university character varying(255),
    affiliated_to character varying(255),
    medium character varying(50),
    study_mode character varying(50),
    start_year integer,
    end_year integer,
    year_of_passing integer,
    hall_ticket_no character varying(50),
    roll_no character varying(50),
    result_status character varying(20),
    marks_obtained numeric(10,2),
    max_marks numeric(10,2),
    percentage numeric(5,2),
    grade character varying(20),
    cgpa numeric(5,2),
    city character varying(100),
    state character varying(100),
    country character varying(100),
    remarks text,
    lock_status integer DEFAULT 0 NOT NULL,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: student_registrations; Type: TABLE; Schema: admissions; Owner: -
--

CREATE TABLE admissions.student_registrations (
    application_no character varying(20) NOT NULL,
    enrollment_no character varying(50),
    register_no character varying(50),
    student_name character varying(255),
    personal_email character varying(255),
    dob date,
    gender character varying(10),
    doj date,
    mobile character varying(20),
    ofc_mail character varying(255),
    regulation character varying(20),
    progid integer,
    electiveid integer,
    admyear integer,
    adm_batch_code character varying(50),
    aadhar_no character varying(12),
    abc_id character varying(50),
    deb_id character varying(50),
    feeid integer,
    std_type character varying(50),
    std_status character varying(50),
    lock_status integer DEFAULT 0 NOT NULL,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: admissionenquiry; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.admissionenquiry (
    phoneno bigint NOT NULL,
    phnostatus character varying(255),
    enquirydate date,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone,
    applino bigint
);


--
-- Name: countries; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.countries (
    country_id integer NOT NULL,
    country_code character varying(10) NOT NULL,
    country_name character varying(100),
    country_phonecode character varying(100),
    lock_status integer,
    log_userid character varying(20),
    log_timestamp timestamp without time zone,
    log_ipaddress character varying(100)
);


--
-- Name: district; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.district (
    "DISTRICT_ID" integer NOT NULL,
    "DISTRICT_CODE" character varying(20) NOT NULL,
    "DISTRICT_NAME" character varying(100),
    "STATE_ID" integer NOT NULL,
    "COUNTRY_ID" integer NOT NULL,
    "LOCK_STATUS" integer,
    "LOG_USERID" character varying(20),
    "LOG_TIMESTAMP" timestamp without time zone,
    "LOG_IPADDRESS" character varying(100)
);


--
-- Name: leads; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.leads (
    phoneno bigint CONSTRAINT admissionenquiry_phoneno_not_null NOT NULL,
    phnostatus character varying(255),
    enquirydate date,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone,
    applino bigint
);


--
-- Name: states; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.states (
    state_id integer NOT NULL,
    state_code character varying(20),
    state_name character varying(100),
    country_id integer,
    lock_status integer,
    log_userid character varying(20),
    log_timestamp timestamp without time zone,
    log_ipaddress character varying(100)
);


--
-- Name: student_registration_applinano_seq; Type: SEQUENCE; Schema: crm; Owner: -
--

CREATE SEQUENCE crm.student_registration_applinano_seq
    START WITH 2027000001
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: student_registration; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.student_registration (
    applinano character varying(20) DEFAULT ('20'::text || to_char(nextval('crm.student_registration_applinano_seq'::regclass), 'FM00000000'::text)) NOT NULL,
    name character varying(100) NOT NULL,
    phoneno character varying(20),
    campuscode character varying(50),
    emailid character varying(255),
    countryid integer,
    stateid integer,
    programlevel character varying(50),
    programid integer,
    branchid integer,
    ableto boolean DEFAULT true,
    stage1 character varying(20) DEFAULT 'not_paid'::character varying,
    stage2 character varying(20) DEFAULT 'NA'::character varying,
    stage3 character varying(20) DEFAULT 'NA'::character varying,
    stage4 character varying(20) DEFAULT 'NA'::character varying,
    stage5 character varying(20) DEFAULT 'NA'::character varying,
    stage6 character varying(20) DEFAULT 'NA'::character varying,
    stage7 character varying(20) DEFAULT 'NA'::character varying,
    stage8 character varying(20) DEFAULT 'NA'::character varying,
    std_status character varying(30),
    log_userid character varying(50),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: wapplogs; Type: TABLE; Schema: crm; Owner: -
--

CREATE TABLE crm.wapplogs (
    "waId" integer NOT NULL,
    wauser character varying(50) DEFAULT ''::character varying NOT NULL,
    waregisterno character varying(100) NOT NULL,
    wapptophone character varying(12) DEFAULT ''::character varying NOT NULL,
    jiotraceid character varying(120) DEFAULT ''::character varying NOT NULL,
    wapptext text NOT NULL,
    datetime timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    ipaddr character varying(50) NOT NULL,
    status character varying(50) DEFAULT NULL::character varying,
    var1 character varying(1000) DEFAULT NULL::character varying,
    var2 character varying(1000) DEFAULT NULL::character varying,
    var3 character varying(1000) DEFAULT NULL::character varying,
    var4 character varying(1000) DEFAULT NULL::character varying,
    var5 character varying(1000) DEFAULT NULL::character varying
);


--
-- Name: wapplogs_waId_seq; Type: SEQUENCE; Schema: crm; Owner: -
--

CREATE SEQUENCE crm."wapplogs_waId_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wapplogs_waId_seq; Type: SEQUENCE OWNED BY; Schema: crm; Owner: -
--

ALTER SEQUENCE crm."wapplogs_waId_seq" OWNED BY crm.wapplogs."waId";


--
-- Name: branches; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.branches (
    branch_id integer NOT NULL,
    program_id integer NOT NULL,
    campus_id character varying(50),
    branch_code character varying(50) NOT NULL,
    branch_short_name character varying(100),
    branch_long_name character varying(255) NOT NULL,
    branch_seprt_code character varying(50),
    branch_start_year integer,
    lock_status integer DEFAULT 0 NOT NULL,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: branches_branch_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.branches_branch_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: branches_branch_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.branches_branch_id_seq OWNED BY master.branches.branch_id;


--
-- Name: campuses; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.campuses (
    campus_id integer NOT NULL,
    campus_code character varying(50) NOT NULL,
    campus_short_name character varying(100),
    campus_long_name character varying(255) NOT NULL,
    address_line1 character varying(255),
    address_line2 character varying(255),
    city character varying(100),
    district character varying(100),
    state character varying(100),
    country character varying(100) DEFAULT 'India'::character varying,
    pincode character varying(20),
    latitude numeric(9,6),
    longitude numeric(9,6),
    phone character varying(30),
    email character varying(150),
    website character varying(255),
    established_date date,
    start_date date,
    approve_board character varying(255),
    approve_board_code character varying(50),
    approve_board_date date,
    accreditation character varying(255),
    accreditation_valid_till date,
    is_active boolean DEFAULT true NOT NULL,
    lock_status integer DEFAULT 0 NOT NULL,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: campuses_campus_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.campuses_campus_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campuses_campus_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.campuses_campus_id_seq OWNED BY master.campuses.campus_id;


--
-- Name: communication_logs; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.communication_logs (
    id bigint NOT NULL,
    channel character varying(20) DEFAULT 'whatsapp'::character varying NOT NULL,
    wauser character varying(100),
    waregisterno character varying(50),
    wapptophone character varying(20) NOT NULL,
    jiotraceid character varying(100),
    template_name character varying(100),
    wapptext text,
    var1 text,
    var2 text,
    status character varying(50) DEFAULT 'SUBMITTED'::character varying NOT NULL,
    ipaddr character varying(45),
    response_payload jsonb,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: communication_logs_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.communication_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: communication_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.communication_logs_id_seq OWNED BY master.communication_logs.id;


--
-- Name: employees; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.employees (
    emp_code character varying(50) NOT NULL,
    full_name character varying(255),
    ofc_mailid character varying(255),
    personal_mailid character varying(255),
    phone_primary character varying(20),
    phone_secondary character varying(20),
    designation_id integer,
    department_id integer,
    campus_id character varying(50),
    role_id integer,
    emp_type character varying(30),
    date_of_join date,
    is_active boolean DEFAULT true,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone
);


--
-- Name: login; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.login (
    usercode character varying(50) NOT NULL,
    phnno character varying(20),
    aadharno character varying(20),
    dob date,
    personalmail character varying(255),
    officialmail character varying(255),
    pwd character varying(255),
    pwd_changed_at timestamp without time zone,
    user_type integer NOT NULL,
    role_id integer,
    campus_id character varying(50),
    designation_id character varying(50),
    department_id character varying(50),
    is_active boolean DEFAULT true,
    last_login_at timestamp without time zone,
    lock_status integer DEFAULT 0,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: login_otp; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.login_otp (
    otp_id integer NOT NULL,
    usercode character varying(50),
    identifier character varying(255) NOT NULL,
    otp_code character varying(10) NOT NULL,
    purpose character varying(30) DEFAULT 'login'::character varying,
    is_used boolean DEFAULT false,
    expires_at timestamp without time zone NOT NULL,
    attempt_count integer DEFAULT 0,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: login_otp_otp_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.login_otp_otp_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: login_otp_otp_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.login_otp_otp_id_seq OWNED BY master.login_otp.otp_id;


--
-- Name: menu_access_roles; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.menu_access_roles (
    menu_code character varying(25) NOT NULL,
    menu_id integer NOT NULL,
    role_id integer NOT NULL,
    campus_id character varying(50),
    morder integer DEFAULT 0,
    mposition integer DEFAULT 1,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: menus; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.menus (
    menu_id integer NOT NULL,
    menu_code character varying(25) NOT NULL,
    module_id integer,
    menu_name character varying(255),
    controller_ref character varying(255),
    icon_name character varying(100),
    is_collapsible boolean DEFAULT false,
    lock_status integer DEFAULT 0,
    project_id integer,
    campus_id character varying(50),
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now(),
    parent_menu_id integer
);


--
-- Name: menus_menu_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.menus_menu_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: menus_menu_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.menus_menu_id_seq OWNED BY master.menus.menu_id;


--
-- Name: programs; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.programs (
    program_id integer NOT NULL,
    campus_id character varying(50),
    program_category character varying(100),
    program_code character varying(50) NOT NULL,
    program_duration integer,
    program_level character varying(50),
    program_long_name character varying(255) NOT NULL,
    program_mode character varying(50),
    program_seprt_code character varying(50),
    program_short_name character varying(100),
    program_start_year integer,
    lock_status integer DEFAULT 0,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: programs_program_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.programs_program_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: programs_program_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.programs_program_id_seq OWNED BY master.programs.program_id;


--
-- Name: roles; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.roles (
    role_id integer NOT NULL,
    campus_id character varying(50),
    project_id integer,
    role_name character varying(255) NOT NULL,
    role_status integer DEFAULT 1,
    log_userid character varying(100),
    log_ipaddress character varying(255),
    log_timestamp timestamp without time zone DEFAULT now()
);


--
-- Name: roles_assigned; Type: TABLE; Schema: master; Owner: -
--

CREATE TABLE master.roles_assigned (
    emp_id character varying(50) CONSTRAINT roles_assigned_employee_id_not_null NOT NULL,
    campus_id integer,
    roles_role_id bigint NOT NULL,
    start_date timestamp without time zone,
    end_date timestamp without time zone,
    last_access_date timestamp without time zone,
    log_userid character varying(50),
    log_timestamp timestamp without time zone,
    log_ipaddress character varying(45)
);


--
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: master; Owner: -
--

CREATE SEQUENCE master.roles_role_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: master; Owner: -
--

ALTER SEQUENCE master.roles_role_id_seq OWNED BY master.roles.role_id;


--
-- Name: wapplogs; Type: VIEW; Schema: master; Owner: -
--

CREATE VIEW master.wapplogs AS
 SELECT id,
    channel,
    wauser,
    waregisterno,
    wapptophone,
    jiotraceid,
    template_name,
    wapptext,
    var1,
    var2,
    status,
    ipaddr,
    response_payload,
    created_at,
    updated_at
   FROM master.communication_logs;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: wapplogs waId; Type: DEFAULT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.wapplogs ALTER COLUMN "waId" SET DEFAULT nextval('crm."wapplogs_waId_seq"'::regclass);


--
-- Name: branches branch_id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.branches ALTER COLUMN branch_id SET DEFAULT nextval('master.branches_branch_id_seq'::regclass);


--
-- Name: campuses campus_id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.campuses ALTER COLUMN campus_id SET DEFAULT nextval('master.campuses_campus_id_seq'::regclass);


--
-- Name: communication_logs id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.communication_logs ALTER COLUMN id SET DEFAULT nextval('master.communication_logs_id_seq'::regclass);


--
-- Name: login_otp otp_id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.login_otp ALTER COLUMN otp_id SET DEFAULT nextval('master.login_otp_otp_id_seq'::regclass);


--
-- Name: menus menu_id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menus ALTER COLUMN menu_id SET DEFAULT nextval('master.menus_menu_id_seq'::regclass);


--
-- Name: programs program_id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.programs ALTER COLUMN program_id SET DEFAULT nextval('master.programs_program_id_seq'::regclass);


--
-- Name: roles role_id; Type: DEFAULT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.roles ALTER COLUMN role_id SET DEFAULT nextval('master.roles_role_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: student_documents student_documents_pkey; Type: CONSTRAINT; Schema: admissions; Owner: -
--

ALTER TABLE ONLY admissions.student_documents
    ADD CONSTRAINT student_documents_pkey PRIMARY KEY (doc_id);


--
-- Name: student_education_info student_education_info_pkey; Type: CONSTRAINT; Schema: admissions; Owner: -
--

ALTER TABLE ONLY admissions.student_education_info
    ADD CONSTRAINT student_education_info_pkey PRIMARY KEY (edu_id);


--
-- Name: student_registrations student_registrations_pkey; Type: CONSTRAINT; Schema: admissions; Owner: -
--

ALTER TABLE ONLY admissions.student_registrations
    ADD CONSTRAINT student_registrations_pkey PRIMARY KEY (application_no);


--
-- Name: district PK_district_m; Type: CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.district
    ADD CONSTRAINT "PK_district_m" PRIMARY KEY ("DISTRICT_ID");


--
-- Name: admissionenquiry admissionenquiry_pkey; Type: CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.admissionenquiry
    ADD CONSTRAINT admissionenquiry_pkey PRIMARY KEY (phoneno);


--
-- Name: countries pk_countries; Type: CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.countries
    ADD CONSTRAINT pk_countries PRIMARY KEY (country_id);


--
-- Name: states pk_states; Type: CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.states
    ADD CONSTRAINT pk_states PRIMARY KEY (state_id);


--
-- Name: wapplogs wapplogs_pkey; Type: CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.wapplogs
    ADD CONSTRAINT wapplogs_pkey PRIMARY KEY ("waId");


--
-- Name: branches branches_branch_code_key; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.branches
    ADD CONSTRAINT branches_branch_code_key UNIQUE (branch_code);


--
-- Name: branches branches_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.branches
    ADD CONSTRAINT branches_pkey PRIMARY KEY (branch_id);


--
-- Name: campuses campuses_campus_code_key; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.campuses
    ADD CONSTRAINT campuses_campus_code_key UNIQUE (campus_code);


--
-- Name: campuses campuses_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.campuses
    ADD CONSTRAINT campuses_pkey PRIMARY KEY (campus_id);


--
-- Name: communication_logs communication_logs_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.communication_logs
    ADD CONSTRAINT communication_logs_pkey PRIMARY KEY (id);


--
-- Name: roles_assigned employee_roles_assigned_pk; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.roles_assigned
    ADD CONSTRAINT employee_roles_assigned_pk PRIMARY KEY (emp_id, roles_role_id);


--
-- Name: employees employees_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.employees
    ADD CONSTRAINT employees_pkey PRIMARY KEY (emp_code);


--
-- Name: login_otp login_otp_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.login_otp
    ADD CONSTRAINT login_otp_pkey PRIMARY KEY (otp_id);


--
-- Name: login login_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.login
    ADD CONSTRAINT login_pkey PRIMARY KEY (usercode);


--
-- Name: menu_access_roles menu_access_roles_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menu_access_roles
    ADD CONSTRAINT menu_access_roles_pkey PRIMARY KEY (menu_code, role_id);


--
-- Name: menus menus_menu_code_key; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menus
    ADD CONSTRAINT menus_menu_code_key UNIQUE (menu_code);


--
-- Name: menus menus_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menus
    ADD CONSTRAINT menus_pkey PRIMARY KEY (menu_id);


--
-- Name: programs programs_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.programs
    ADD CONSTRAINT programs_pkey PRIMARY KEY (program_id);


--
-- Name: programs programs_program_code_key; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.programs
    ADD CONSTRAINT programs_program_code_key UNIQUE (program_code);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: idx_std_docs_application_no; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_docs_application_no ON admissions.student_documents USING btree (application_no);


--
-- Name: idx_std_docs_docname; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_docs_docname ON admissions.student_documents USING btree (docname);


--
-- Name: idx_std_docs_doctype; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_docs_doctype ON admissions.student_documents USING btree (doctype);


--
-- Name: idx_std_docs_register_no; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_docs_register_no ON admissions.student_documents USING btree (register_no);


--
-- Name: idx_std_edu_appl_qual; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_edu_appl_qual ON admissions.student_education_info USING btree (application_no, qual_level);


--
-- Name: idx_std_edu_qual_level; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_edu_qual_level ON admissions.student_education_info USING btree (qual_level);


--
-- Name: idx_std_edu_register_no; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_edu_register_no ON admissions.student_education_info USING btree (register_no);


--
-- Name: idx_std_edu_year_passing; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_std_edu_year_passing ON admissions.student_education_info USING btree (year_of_passing);


--
-- Name: idx_studreg_electiveid; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_studreg_electiveid ON admissions.student_registrations USING btree (electiveid);


--
-- Name: idx_studreg_personal_email; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_studreg_personal_email ON admissions.student_registrations USING btree (personal_email);


--
-- Name: idx_studreg_progid; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_studreg_progid ON admissions.student_registrations USING btree (progid);


--
-- Name: idx_studreg_register_no; Type: INDEX; Schema: admissions; Owner: -
--

CREATE INDEX idx_studreg_register_no ON admissions.student_registrations USING btree (register_no);


--
-- Name: idx_branches_campus; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_branches_campus ON master.branches USING btree (campus_id);


--
-- Name: idx_branches_program; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_branches_program ON master.branches USING btree (program_id);


--
-- Name: idx_campuses_active; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_campuses_active ON master.campuses USING btree (is_active);


--
-- Name: idx_campuses_city; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_campuses_city ON master.campuses USING btree (city);


--
-- Name: idx_campuses_state; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_campuses_state ON master.campuses USING btree (state);


--
-- Name: idx_emp_active; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_active ON master.employees USING btree (is_active);


--
-- Name: idx_emp_department; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_department ON master.employees USING btree (department_id);


--
-- Name: idx_emp_designation; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_designation ON master.employees USING btree (designation_id);


--
-- Name: idx_emp_ofc_mailid; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_ofc_mailid ON master.employees USING btree (ofc_mailid);


--
-- Name: idx_emp_personal_mailid; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_personal_mailid ON master.employees USING btree (personal_mailid);


--
-- Name: idx_emp_phone; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_phone ON master.employees USING btree (phone_primary);


--
-- Name: idx_emp_role; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_emp_role ON master.employees USING btree (role_id);


--
-- Name: idx_login_campus; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_campus ON master.login USING btree (campus_id);


--
-- Name: idx_login_is_active; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_is_active ON master.login USING btree (is_active);


--
-- Name: idx_login_officialmail; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_officialmail ON master.login USING btree (officialmail);


--
-- Name: idx_login_otp_exp; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_otp_exp ON master.login_otp USING btree (expires_at);


--
-- Name: idx_login_otp_ident; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_otp_ident ON master.login_otp USING btree (identifier);


--
-- Name: idx_login_otp_usercode; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_otp_usercode ON master.login_otp USING btree (usercode);


--
-- Name: idx_login_personalmail; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_personalmail ON master.login USING btree (personalmail);


--
-- Name: idx_login_phone; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_phone ON master.login USING btree (phnno);


--
-- Name: idx_login_role; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_role ON master.login USING btree (role_id);


--
-- Name: idx_login_usertype; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_login_usertype ON master.login USING btree (user_type);


--
-- Name: idx_mar_campus; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_mar_campus ON master.menu_access_roles USING btree (campus_id);


--
-- Name: idx_mar_menu; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_mar_menu ON master.menu_access_roles USING btree (menu_id);


--
-- Name: idx_mar_role; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_mar_role ON master.menu_access_roles USING btree (role_id);


--
-- Name: idx_menus_campus; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_menus_campus ON master.menus USING btree (campus_id);


--
-- Name: idx_menus_module; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_menus_module ON master.menus USING btree (module_id);


--
-- Name: idx_menus_parent; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_menus_parent ON master.menus USING btree (parent_menu_id);


--
-- Name: idx_menus_project; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_menus_project ON master.menus USING btree (project_id);


--
-- Name: idx_programs_campus; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_programs_campus ON master.programs USING btree (campus_id);


--
-- Name: idx_roles_campus; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_roles_campus ON master.roles USING btree (campus_id);


--
-- Name: idx_roles_project; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX idx_roles_project ON master.roles USING btree (project_id);


--
-- Name: master_communication_logs_jiotraceid_index; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX master_communication_logs_jiotraceid_index ON master.communication_logs USING btree (jiotraceid);


--
-- Name: master_communication_logs_wapptophone_index; Type: INDEX; Schema: master; Owner: -
--

CREATE INDEX master_communication_logs_wapptophone_index ON master.communication_logs USING btree (wapptophone);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: student_documents fk_std_docs_studreg; Type: FK CONSTRAINT; Schema: admissions; Owner: -
--

ALTER TABLE ONLY admissions.student_documents
    ADD CONSTRAINT fk_std_docs_studreg FOREIGN KEY (application_no) REFERENCES admissions.student_registrations(application_no) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: student_education_info fk_std_edu_studreg; Type: FK CONSTRAINT; Schema: admissions; Owner: -
--

ALTER TABLE ONLY admissions.student_education_info
    ADD CONSTRAINT fk_std_edu_studreg FOREIGN KEY (application_no) REFERENCES admissions.student_registrations(application_no) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: student_registrations fk_studreg_program; Type: FK CONSTRAINT; Schema: admissions; Owner: -
--

ALTER TABLE ONLY admissions.student_registrations
    ADD CONSTRAINT fk_studreg_program FOREIGN KEY (progid) REFERENCES master.programs(program_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: district FK_district_m_country; Type: FK CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.district
    ADD CONSTRAINT "FK_district_m_country" FOREIGN KEY ("COUNTRY_ID") REFERENCES crm.countries(country_id);


--
-- Name: district FK_district_m_state; Type: FK CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.district
    ADD CONSTRAINT "FK_district_m_state" FOREIGN KEY ("STATE_ID") REFERENCES crm.states(state_id);


--
-- Name: states fk_states_country; Type: FK CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.states
    ADD CONSTRAINT fk_states_country FOREIGN KEY (country_id) REFERENCES crm.countries(country_id);


--
-- Name: student_registration fk_student_reg_country; Type: FK CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.student_registration
    ADD CONSTRAINT fk_student_reg_country FOREIGN KEY (countryid) REFERENCES crm.countries(country_id);


--
-- Name: student_registration fk_student_reg_state; Type: FK CONSTRAINT; Schema: crm; Owner: -
--

ALTER TABLE ONLY crm.student_registration
    ADD CONSTRAINT fk_student_reg_state FOREIGN KEY (stateid) REFERENCES crm.states(state_id);


--
-- Name: roles_assigned employee_roles_assigned_fk_campus; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.roles_assigned
    ADD CONSTRAINT employee_roles_assigned_fk_campus FOREIGN KEY (campus_id) REFERENCES master.campuses(campus_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: roles_assigned employee_roles_assigned_fk_role; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.roles_assigned
    ADD CONSTRAINT employee_roles_assigned_fk_role FOREIGN KEY (roles_role_id) REFERENCES master.roles(role_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: branches fk_branches_program; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.branches
    ADD CONSTRAINT fk_branches_program FOREIGN KEY (program_id) REFERENCES master.programs(program_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: login_otp fk_login_otp_usercode; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.login_otp
    ADD CONSTRAINT fk_login_otp_usercode FOREIGN KEY (usercode) REFERENCES master.login(usercode) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: menus fk_menus_parent; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menus
    ADD CONSTRAINT fk_menus_parent FOREIGN KEY (parent_menu_id) REFERENCES master.menus(menu_id) ON DELETE SET NULL;


--
-- Name: menu_access_roles menu_access_roles_menu_id_fkey; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menu_access_roles
    ADD CONSTRAINT menu_access_roles_menu_id_fkey FOREIGN KEY (menu_id) REFERENCES master.menus(menu_id) ON DELETE CASCADE;


--
-- Name: menu_access_roles menu_access_roles_role_id_fkey; Type: FK CONSTRAINT; Schema: master; Owner: -
--

ALTER TABLE ONLY master.menu_access_roles
    ADD CONSTRAINT menu_access_roles_role_id_fkey FOREIGN KEY (role_id) REFERENCES master.roles(role_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict lx5uSlDX4GCMLg4NrfJInjB9z9diculIjUSjbYS5h3S3u1FJpYniw244pm3IAy3

--
-- PostgreSQL database dump
--

\restrict S3TGUUC7TBZP8c9T3CTqw9isrjzuAIDU8RAzn6QomZxCHCd417YDU1RTBQHrc47

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_11_02_000001_create_communication_logs_table	1
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 4, true);


--
-- PostgreSQL database dump complete
--

\unrestrict S3TGUUC7TBZP8c9T3CTqw9isrjzuAIDU8RAzn6QomZxCHCd417YDU1RTBQHrc47

