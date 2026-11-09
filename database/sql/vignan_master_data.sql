pg_dump: warning: there are circular foreign-key constraints on this table:
pg_dump: detail: menus
pg_dump: hint: You might not be able to restore the dump without using --disable-triggers or temporarily dropping the constraints.
pg_dump: hint: Consider using a full dump instead of a --data-only dump to avoid this problem.
--
-- PostgreSQL database dump
--

\restrict qlydIJUqQdlczRsdLwrRkNuUZtCPu9nLV2vPoQXo260Q6LyaRFsc9EgQKsnfCQr

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
-- Data for Name: programs; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.programs VALUES (1, '1', 'Bachelor of Technology', 'A', 4, 'UG', 'Bachelor of Technology', 'Regular', 'A', 'B.Tech', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (2, '1', 'Master of Technology', 'B', 2, 'PG', 'Master of Technology', 'Regular', 'B', 'M.Tech', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (3, '1', 'Master of Business Administration', 'C', 2, 'PG', 'Master of Business Administration', 'Regular', 'C', 'MBA', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (4, '1', 'Master of Computer Applications', 'D', 2, 'PG', 'Master of Computer Applications', 'Regular', 'D', 'MCA', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (5, '1', 'Diploma', 'E', 3, 'Diploma', 'Diploma', 'Regular', 'E', 'Diploma', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (6, '1', 'PHD', 'G', 3, 'PhD', 'PHD', 'Regular', 'G', 'PHD', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (7, '1', 'BBM', 'H', 3, 'UG', 'BBM', 'Regular', 'H', 'BBM', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (8, '1', 'B.A (Hons.)', 'I', 3, 'UG', 'B.A (Hons.)', 'Regular', 'I', 'B.A (Hons.)', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (9, '1', 'Bachelor of Computer Application', 'J', 3, 'UG', 'Bachelor of Computer Applications', 'Regular', 'J', 'BCA', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (10, '1', 'Bachelor of Business Administration', 'K', 3, 'UG', 'Bachelor of Business Administration', 'Regular', 'K', 'BBA', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (11, '1', 'Bachelor of Science', 'M', 3, 'UG', 'Bachelor of Science', 'Regular', 'M', 'BSC', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (12, '1', 'Bachelor of Pharmacy', 'N', 4, 'UG', 'Bachelor of Pharmacy', 'Regular', 'N', 'B.Pharmacy', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (13, '1', 'Master of Pharmacy', 'P', 2, 'PG', 'Master of Pharmacy', 'Regular', 'P', 'M.Pharm', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (14, '1', 'phd full time', 'P1', 3, 'PhD', 'phd full time', 'Regular', 'P1', 'phd full time', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (15, '1', 'phd part time', 'P2', 4, 'PhD', 'phd part time', 'Regular', 'P2', 'phd part time', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (16, '1', 'Master of Arts', 'Q', 2, 'PG', 'Master of Arts', 'Regular', 'Q', 'M.A', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (17, '1', 'Integrated BBM+MBA', 'R', 5, 'Integrated', 'Integrated BBM+MBA', 'Regular', 'R', 'BBM+MBA', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (18, '1', 'Master of Science', 'S', 2, 'PG', 'Master of Science', 'Regular', 'S', 'M.Sc.', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (19, '1', 'ITEP', 'T', 4, 'UG', 'ITEP', 'Regular', 'T', 'ITEP', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (20, '1', 'B.A (LLB)', 'U', 5, 'Integrated', 'B.A (LLB)', 'Regular', 'U', 'B.A (LLB)', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (21, '1', 'B.B.A (LLB)', 'V', 5, 'Integrated', 'B.B.A (LLB)', 'Regular', 'V', 'B.B.A (LLB)', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (22, '1', 'Bachelor of Science (Hons.)', 'W', 3, 'UG', 'Bachelor of Science (Hons.)', 'Regular', 'W', 'B.Sc. (Hons.)', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (23, '1', 'Doctor of Pharmacy (Pharm.D)', 'X', 6, 'PG', 'Doctor of Pharmacy (Pharm.D)', 'Regular', 'X', 'Pharm.D', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');
INSERT INTO master.programs VALUES (24, '1', 'B.Com', 'Y', 3, 'UG', 'B.Com', 'Regular', 'Y', 'B.Com', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:00.997394');


--
-- Data for Name: student_registrations; Type: TABLE DATA; Schema: admissions; Owner: postgres
--

INSERT INTO admissions.student_registrations VALUES ('20261104642', NULL, '261V36812', 'Sumanth Mani', NULL, NULL, NULL, NULL, '9912996788', NULL, 'R25', 1, 1, 2026, '2026-27', NULL, NULL, NULL, NULL, 'Regular', 'Applied', 0, 'SELF_REGISTER', '127.0.0.1', '2026-11-04 06:14:05');
INSERT INTO admissions.student_registrations VALUES ('20########', 'Jan-26-Fb3JguFEiK', '261A29633', 'hemanth', 'testtd@12.com', '13131-12-13', 'Male', NULL, '7780579012', '7780579012@student.vignan.ac.in', 'R25', 1, 11, 2026, 'JANUARY_2026', '131313131313', '131313131', NULL, NULL, 'Regular', 'Applied', 0, 'SELF_REGISTER', '127.0.0.1', '2026-11-04 12:28:25');
INSERT INTO admissions.student_registrations VALUES ('20261104798', 'Jan-26-Bvq7Simc2m', '261A29134', 'Test SSC Candidate', 'test.student@example.com', NULL, NULL, NULL, '9912996788', 'test.student@example.com', 'R25', 1, 1, 2026, 'JANUARY_2026', NULL, NULL, NULL, NULL, 'Regular', 'Applied', 0, 'SELF_REGISTER', '127.0.0.1', '2026-11-04 15:05:08');
INSERT INTO admissions.student_registrations VALUES ('20261106837', 'Jan-26-p7tZKm5yiP', '261A71518', 'mani', 'test@td.com', '2008-03-20', 'Male', NULL, '7816024445', 'test@td.com', 'R25', 1, 11, 2026, 'JANUARY_2026', '987654321012', 'ABC-2026-90218', NULL, NULL, 'Regular', 'Applied', 0, 'SELF_REGISTER', '127.0.0.1', '2026-11-06 11:30:39');


--
-- Data for Name: countries; Type: TABLE DATA; Schema: crm; Owner: postgres
--

INSERT INTO crm.countries VALUES (1, 'IN', 'India', '+91', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (2, 'CN', 'China', '+86', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (3, 'US', 'United States', '+1', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (4, 'ID', 'Indonesia', '+62', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (5, 'PK', 'Pakistan', '+92', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (6, 'NG', 'Nigeria', '+234', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (7, 'BR', 'Brazil', '+55', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (8, 'BD', 'Bangladesh', '+880', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (9, 'RU', 'Russia', '+7', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (10, 'ET', 'Ethiopia', '+251', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (11, 'MX', 'Mexico', '+52', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (12, 'JP', 'Japan', '+81', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (13, 'EG', 'Egypt', '+20', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (14, 'PH', 'Philippines', '+63', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (15, 'CD', 'DR Congo', '+243', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (16, 'VN', 'Vietnam', '+84', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (17, 'IR', 'Iran', '+98', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (18, 'TR', 'Turkey', '+90', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (19, 'DE', 'Germany', '+49', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (20, 'TZ', 'Tanzania', '+255', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (21, 'TH', 'Thailand', '+66', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (22, 'GB', 'United Kingdom', '+44', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (23, 'FR', 'France', '+33', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (24, 'ZA', 'South Africa', '+27', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (25, 'IT', 'Italy', '+39', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (26, 'KE', 'Kenya', '+254', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (27, 'MM', 'Myanmar', '+95', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (28, 'CO', 'Colombia', '+57', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (29, 'SD', 'Sudan', '+249', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (30, 'UG', 'Uganda', '+256', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (31, 'KR', 'South Korea', '+82', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (32, 'DZ', 'Algeria', '+213', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (33, 'IQ', 'Iraq', '+964', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (34, 'ES', 'Spain', '+34', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (35, 'AR', 'Argentina', '+54', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (36, 'AF', 'Afghanistan', '+93', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (37, 'YE', 'Yemen', '+967', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (38, 'CA', 'Canada', '+1', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (39, 'AO', 'Angola', '+244', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (40, 'UA', 'Ukraine', '+380', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (41, 'MA', 'Morocco', '+212', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (42, 'PL', 'Poland', '+48', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (43, 'UZ', 'Uzbekistan', '+998', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (44, 'MZ', 'Mozambique', '+258', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (45, 'MY', 'Malaysia', '+60', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (46, 'GH', 'Ghana', '+233', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (47, 'SA', 'Saudi Arabia', '+966', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (48, 'PE', 'Peru', '+51', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (49, 'MG', 'Madagascar', '+261', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');
INSERT INTO crm.countries VALUES (50, 'CI', 'Ivory Coast', '+225', 0, 'seed', '2026-11-04 08:57:13.2501', '127.0.0.1');


--
-- Data for Name: states; Type: TABLE DATA; Schema: crm; Owner: postgres
--

INSERT INTO crm.states VALUES (1, 'AP', 'Andhra Pradesh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (2, 'AR', 'Arunachal Pradesh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (3, 'AS', 'Assam', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (4, 'BR', 'Bihar', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (5, 'CG', 'Chhattisgarh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (6, 'GA', 'Goa', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (7, 'GJ', 'Gujarat', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (8, 'HR', 'Haryana', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (9, 'HP', 'Himachal Pradesh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (10, 'JH', 'Jharkhand', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (11, 'KA', 'Karnataka', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (12, 'KL', 'Kerala', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (13, 'MP', 'Madhya Pradesh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (14, 'MH', 'Maharashtra', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (15, 'MN', 'Manipur', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (16, 'ML', 'Meghalaya', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (17, 'MZ', 'Mizoram', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (18, 'NL', 'Nagaland', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (19, 'OD', 'Odisha', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (20, 'PB', 'Punjab', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (21, 'RJ', 'Rajasthan', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (22, 'SK', 'Sikkim', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (23, 'TN', 'Tamil Nadu', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (24, 'TG', 'Telangana', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (25, 'TR', 'Tripura', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (26, 'UP', 'Uttar Pradesh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (27, 'UK', 'Uttarakhand', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (28, 'WB', 'West Bengal', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (29, 'AN', 'Andaman and Nicobar Islands', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (30, 'CH', 'Chandigarh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (31, 'DH', 'Dadra and Nagar Haveli and Daman and Diu', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (32, 'DL', 'Delhi', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (33, 'JK', 'Jammu and Kashmir', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (34, 'LA', 'Ladakh', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (35, 'LD', 'Lakshadweep', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (36, 'PY', 'Puducherry', 1, 0, 'seed', '2026-11-04 09:05:20.301856', '127.0.0.1');
INSERT INTO crm.states VALUES (37, 'CA', 'California', 3, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (38, 'TX', 'Texas', 3, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (39, 'FL', 'Florida', 3, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (40, 'NY', 'New York', 3, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (41, 'PA', 'Pennsylvania', 3, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (42, 'JB', 'Jawa Barat', 4, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (43, 'JI', 'Jawa Timur', 4, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (44, 'JT', 'Jawa Tengah', 4, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (45, 'SU', 'Sumatera Utara', 4, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (46, 'JK', 'DKI Jakarta', 4, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (47, 'PB', 'Punjab', 5, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (48, 'SD', 'Sindh', 5, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (49, 'KP', 'Khyber Pakhtunkhwa', 5, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (50, 'BA', 'Balochistan', 5, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (51, 'IS', 'Islamabad', 5, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (52, 'KN', 'Kano', 6, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (53, 'LA', 'Lagos', 6, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (54, 'KT', 'Katsina', 6, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (55, 'KD', 'Kaduna', 6, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (56, 'BA', 'Bauchi', 6, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (57, 'GD', 'Guangdong', 2, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (58, 'SD', 'Shandong', 2, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (59, 'HE', 'Henan', 2, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (60, 'JS', 'Jiangsu', 2, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');
INSERT INTO crm.states VALUES (61, 'SC', 'Sichuan', 2, 0, 'seed', '2026-11-04 09:07:02.906427', '127.0.0.1');


--
-- Data for Name: district; Type: TABLE DATA; Schema: crm; Owner: postgres
--



--
-- Data for Name: branches; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.branches VALUES (1, 1, '1', 'A-01', 'biotech', 'Bio Technology', 'A01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (2, 1, '1', 'A-02', 'chem', 'Chemical Engg', 'A02', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (3, 1, '1', 'A-03', 'civil', 'Civil Engineering', 'A03', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (4, 1, '1', 'A-04', 'cse', 'Computer Science & Engineering', 'A04', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (5, 1, '1', 'A-05', 'ece', 'Electronics and Communication Engineering', 'A05', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (6, 1, '1', 'A-06', 'eee', 'Electrical and Electronics Engineering', 'A06', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (7, 1, '1', 'A-07', 'it', 'Information technology', 'A07', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (8, 1, '1', 'A-08', 'mech', 'Mechanical Engineering', 'A08', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (9, 1, '1', 'A-10', 'AME', 'Automobile Engineering', 'A10', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (10, 1, '1', 'A-11', 'TT', 'Textile Technology', 'A11', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (11, 1, '1', 'A-12', 'AG', 'Agriculture Engineering', 'A12', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (12, 1, '1', 'A-13', 'ECM', 'Electronics and Computer Engineering', 'A13', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (13, 1, '1', 'A-14', 'BI', 'Bioinformatics', 'A14', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (14, 1, '1', 'A-15', 'FT', 'Food Technology', 'A15', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (15, 1, '1', 'A-16', 'BM', 'Biomedical Engineering', 'A16', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (16, 1, '1', 'A-17', 'PE', 'Petroleum Engineering', 'A17', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (17, 1, '1', 'A-18', 'AIANDML', 'CSE - Artificial Intelligence & Machine Learning', 'A18', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (18, 1, '1', 'A-19', 'CSECS', 'CSE - Cyber Security', 'A19', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (19, 1, '1', 'A-20', 'CSBS', 'Computer Science & Business Systems', 'A20', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (20, 1, '1', 'A-21', 'RA', 'Robotics & Automation', 'A21', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (21, 1, '1', 'A-22', 'TT2', 'Textile Technology', 'A22', 2024, 1, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (22, 1, '1', 'A-23', 'DS', 'Data Science', 'A23', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (23, 1, '1', 'A-24', 'CSEIOT', 'CSE - Internet of Things', 'A24', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (24, 1, '1', 'A-25', 'ECEVLSI', 'ECE - VLSI Design', 'A25', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (25, 1, '1', 'A-26', 'TechText', 'Technical Textiles', 'A26', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:12.028608');
INSERT INTO master.branches VALUES (26, 2, '1', 'B-01', 'biotech', 'Biotechnology and Bioprocess Engineering', 'B01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (27, 2, '1', 'B-02', 'chem', 'Chemical Engineering', 'B02', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (28, 2, '1', 'B-03', 'csp', 'Communication and Signal Processing', 'B03', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (29, 2, '1', 'B-04', 'cse', 'Computer Science & Engineering', 'B04', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (30, 2, '1', 'B-05', 'decs', 'Digital Electronics and Communication System', 'B05', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (31, 2, '1', 'B-06', 'es', 'Embedded Systems', 'B06', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (32, 2, '1', 'B-07', 'ee', 'Energy Engineering', 'B07', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (33, 2, '1', 'B-08', 'it', 'Information Technology', 'B08', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (34, 2, '1', 'B-09', 'md', 'Machine Design', 'B09', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (35, 2, '1', 'B-10', 'ped', 'Power Electronics and Drives', 'B10', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (36, 2, '1', 'B-11', 'VLSI', 'Very Large Scale Integration (VLSI)', 'B11', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (37, 2, '1', 'B-12', 'FT', 'Food Processing Technology', 'B12', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (38, 2, '1', 'B-13', 'ps', 'Power Systems', 'B13', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (39, 2, '1', 'B-14', 'cad', 'CAD / CAM / CAE', 'B14', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (40, 2, '1', 'B-15', 'civil', 'Civil Engineering', 'B15', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (41, 2, '1', 'B-16', 'cnis', 'Computer Networks & Information Security', 'B16', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (42, 2, '1', 'B-17', 'te', 'Thermal Engineering', 'B17', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (43, 2, '1', 'B-18', 'ML', 'Machine Learning', 'B18', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (44, 2, '1', 'B-19', 'dcn', 'Data Communication & Networking', 'B19', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (45, 2, '1', 'B-20', 'pe', 'Petroleum Engineering', 'B20', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (46, 2, '1', 'B-21', 'FM', 'Farm Machinery', 'B21', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (47, 2, '1', 'B-22', 'ipm', 'Image Processing & Machine Vision', 'B22', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (48, 2, '1', 'B-23', 'ds', 'Data Science', 'B23', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (49, 2, '1', 'B-24', 'IOT', 'IOT', 'B24', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (50, 2, '1', 'B-25', 'EVT', 'Electric Vehicle Technology', 'B25', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (51, 2, '1', 'B-26', 'AIANDDS', 'Artificial Intelligence and Data Science', 'B26', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (52, 2, '1', 'B-27', 'SM', 'Smart Manufacturing', 'B27', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (53, 2, '1', 'B-28', 'AEV', 'Autonomous Electric Vehicles', 'B28', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (54, 2, '1', 'B-29', 'SSC', 'Sustainable Smart Construction', 'B29', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:27.492926');
INSERT INTO master.branches VALUES (55, 3, '1', 'C-04', 'FINANCE', 'Finance', 'C04', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (56, 3, '1', 'C-03', 'MR', 'Marketing', 'C03', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (57, 3, '1', 'C-02', 'HR', 'Human Resources', 'C02', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (58, 3, '1', 'C-01', 'mba', 'Master of Business Administration', 'C01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (59, 4, '1', 'D-01', 'mca', 'Master of Computer Applications', 'D01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (60, 5, '1', 'E-08', 'DMECH', 'Mechanical Engineering', 'E08', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (61, 5, '1', 'E-07', 'DAIML', 'Artificial Intelligence & Machine Learning', 'E07', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (62, 5, '1', 'E-06', 'DEEE', 'Electrical & Electronics Engineering', 'E06', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (63, 5, '1', 'E-05', 'DECE', 'Electronics & Communication Engineering', 'E05', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (64, 5, '1', 'E-04', 'DCSE', 'Computer Science & Engineering', 'E04', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (65, 7, '1', 'H-01', 'BBM', 'Bachelor of Business Management', 'H01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (66, 8, '1', 'I-01', 'B.A Hons', 'B.A (Hons.) Political Science', 'I01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (67, 9, '1', 'J-01', 'BCA', 'Bachelor of Computer Applications', 'J01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (68, 10, '1', 'K-01', 'BBA', 'Bachelor of Business Administration', 'K01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (69, 12, '1', 'N-01', 'B.Pharm', 'Bachelor of Pharmacy', 'N01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (70, 13, '1', 'P-01', 'M.Pharm', 'Master of Pharmacy', 'P01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:38.314739');
INSERT INTO master.branches VALUES (71, 6, '1', 'G-01', 'Biotech', 'Biotechnology', 'G01', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (72, 6, '1', 'G-02', 'Chemical', 'Chemical Engineering', 'G02', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (73, 6, '1', 'G-03', 'Civil', 'Civil Engineering', 'G03', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (74, 6, '1', 'G-04', 'CSE', 'Computer Science & Engineering', 'G04', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (75, 6, '1', 'G-05', 'ECE', 'Electronics & Communication Engineering', 'G05', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (76, 6, '1', 'G-06', 'EEE', 'Electrical & Electronics Engineering', 'G06', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (77, 6, '1', 'G-07', 'IT', 'Information Technology', 'G07', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (78, 6, '1', 'G-08', 'Mechanical', 'Mechanical Engineering', 'G08', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (79, 6, '1', 'G-12', 'Management', 'Management Studies', 'G12', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (80, 6, '1', 'G-30', 'Physics', 'Physics', 'G30', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (81, 6, '1', 'G-31', 'Chemistry', 'Chemistry', 'G31', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (82, 6, '1', 'G-32', 'Maths', 'Mathematics', 'G32', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (83, 6, '1', 'G-33', 'English', 'English', 'G33', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');
INSERT INTO master.branches VALUES (84, 6, '1', 'G-35', 'MCA', 'Computer Applications', 'G35', 2024, 0, '02598', '192.168.111.111', '2026-11-01 06:48:46.824392');


--
-- Data for Name: campuses; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.campuses VALUES (1, 'VDM', 'Vignan Main Campus', 'Vignan''s Foundation for Science, Technology & Research', 'Vadlamudi', 'Guntur District', 'Guntur', 'Guntur', 'Andhra Pradesh', 'India', '522213', 16.236000, 80.428000, '+91-863-2344700', 'info@vignan.ac.in', 'https://vignan.ac.in', '1997-01-01', '1997-06-01', 'UGC', 'UGC', '1997-01-01', 'NAAC A+', '2028-12-31', true, 0, '02598', '192.168.111.111', '2026-10-06 16:38:13.153');


--
-- Data for Name: employees; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.employees VALUES ('02598', 'Y.BHANUPRASAD', 'ybp_td@vignan.ac.in', NULL, '9912996788', NULL, NULL, NULL, NULL, NULL, 'PERMANENT', NULL, true, '02598', '192.168.1.10', '2026-10-08 09:42:18.821');
INSERT INTO master.employees VALUES ('03141', 'Kamalesh', 'dean@vignan.ac.in', NULL, '7075624098', NULL, NULL, NULL, NULL, NULL, 'PERMANENT', NULL, true, '02598', '192.168.1.10', '2026-10-08 09:42:18.821');
INSERT INTO master.employees VALUES ('03434', 'Ganesh kumar', 'mskv_mgt@vignan.ac.in', NULL, '9391364210', NULL, NULL, NULL, NULL, NULL, 'PERMANENT', NULL, true, '02598', '192.168.1.10', '2026-10-08 09:42:18.821');


--
-- Data for Name: login; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.login VALUES ('02598', '9912996788', NULL, NULL, 'ybp_td@vignan.ac.in', 'ybp_td@vignan.ac.in', '$2b$12$PLACEHOLDER_HASH_06', '2026-10-14 08:56:00.163', 2, 1, '1', '0', '0', true, '2026-10-14 08:56:18.796', 0, '02598', '192.168', '2026-10-08 16:43:22.041');
INSERT INTO master.login VALUES ('03141', '7075624098', NULL, NULL, 'dean@vignan.ac.in', 'dean@vignan.ac.in', NULL, NULL, 2, NULL, NULL, NULL, NULL, true, NULL, 0, 'Kamalesh', NULL, '2026-11-04 09:26:46.846075');
INSERT INTO master.login VALUES ('03434', '9391364210', NULL, NULL, 'mskv_mgt@vignan.ac.in', 'mskv_mgt@vignan.ac.in', NULL, NULL, 2, NULL, NULL, NULL, NULL, true, NULL, 0, 'Ganesh kumar', NULL, '2026-11-04 09:26:46.846075');
INSERT INTO master.login VALUES ('20261106333', '9440151948', NULL, NULL, 'ypbhanu@gmail.com', 'ypbhanu@gmail.com', '$2y$12$1re8Ww0ybFvYp8ijytyckOMBGTP4YIT/2ZgaWTAaRg2uXy0lym91a', NULL, 1, 5, '1', NULL, NULL, true, NULL, 0, NULL, '127.0.0.1', '2026-11-06 12:43:53');
INSERT INTO master.login VALUES ('20261106837', '7816024445', NULL, NULL, 'test@td.com', 'test@td.com', '$2y$12$DMKg4mwAx00KN41g0cyu/O0vk86.D/RjiC59a7VR0gzu0eHnoLUcO', NULL, 1, 5, '1', NULL, NULL, true, NULL, 0, NULL, '127.0.0.1', '2026-11-06 14:07:46');


--
-- Data for Name: menus; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.menus VALUES (100, 'CRMM0001', 1, 'CRM', 'crm/index', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', NULL);
INSERT INTO master.menus VALUES (101, 'LEADS0001', 2, 'Leads', 'leads/index', 'fa fa-graduation-cap', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', NULL);
INSERT INTO master.menus VALUES (102, 'FINA0001', 3, 'Finance', 'finance/index', 'fa fa-file-text-o', false, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', NULL);
INSERT INTO master.menus VALUES (108, 'KPII0002', 4, 'student KPI', 'kpi/studentkpi', 'fa fa-money', false, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 103);
INSERT INTO master.menus VALUES (103, 'KPII0001', 4, 'KPI', 'kpi/index', 'fa fa-money', false, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', NULL);
INSERT INTO master.menus VALUES (104, 'CRMM0002', 1, 'Student data', 'crm/stddata', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 100);
INSERT INTO master.menus VALUES (105, 'CRMM0003', 1, 'Student search', 'crm/stdsearch', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 100);
INSERT INTO master.menus VALUES (107, 'LEADS0002', 2, 'Allotted Leads', 'leads/allottedleads', 'fa fa-graduation-cap', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 101);
INSERT INTO master.menus VALUES (106, 'FINA0002', 3, 'Paid details', 'finance/paiddetails', 'fa fa-file-text-o', false, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 102);
INSERT INTO master.menus VALUES (109, 'FINA0003', 3, 'Fee Payment', 'step1', 'fa fa-file-text-o', false, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 102);
INSERT INTO master.menus VALUES (110, 'CRMM0004', 1, 'Personal Profile', 'step2', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 100);
INSERT INTO master.menus VALUES (111, 'CRMM0005', 1, 'Academic Info', 'step3', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 100);
INSERT INTO master.menus VALUES (112, 'CRMM0006', 1, 'Application Download', 'step4', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 100);
INSERT INTO master.menus VALUES (113, 'CRMM0007', 1, 'VSAT Result', 'step5', 'fa fa-user-plus', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 100);
INSERT INTO master.menus VALUES (116, 'LEADS0003', 2, 'Leads Allocation', 'leads/leadsallocation', 'fa fa-graduation-cap', true, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', 101);
INSERT INTO master.menus VALUES (117, 'KPII0003', 4, 'SMS Tracker', 'sms/index', 'fa fa-envelope-o', false, 0, 1, '1', '02598', '192.168.111.111', '2026-10-10 08:02:05.388', NULL);


--
-- Data for Name: roles; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.roles VALUES (5, '1', 1, 'Student', 1, '02598', '192.168.111.111', '2024-02-27 10:07:13');
INSERT INTO master.roles VALUES (1, '1', 1, 'Employee', 1, '02598', '192.168.111.111', '2024-02-22 14:11:14');
INSERT INTO master.roles VALUES (3, '1', 1, 'CRM DEAN', 1, '02598', '192.168.111.111', '2024-02-22 14:11:14');
INSERT INTO master.roles VALUES (2, '1', 1, 'CRM Incharge', 1, '02598', '192.168.111.111', '2024-02-22 14:11:14');
INSERT INTO master.roles VALUES (4, '1', 1, 'Tele Caller', 1, '02598', '192.168.111.111', '2024-02-27 10:07:13');


--
-- Data for Name: menu_access_roles; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.menu_access_roles VALUES ('CRMM0001', 100, 1, '1', 1, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('CRMM0002', 104, 1, '1', 2, 2, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('CRMM0003', 105, 1, '1', 3, 2, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('LEADS0001', 101, 1, '1', 4, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('LEADS0002', 107, 1, '1', 5, 2, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('LEADS0003', 116, 1, '1', 6, 2, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('FINA0001', 102, 1, '1', 7, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('FINA0002', 106, 1, '1', 8, 2, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('KPII0001', 103, 1, '1', 9, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('KPII0002', 108, 1, '1', 10, 2, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('KPII0003', 117, 1, '1', 11, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('CRMM0001', 100, 2, '1', 1, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.131559');
INSERT INTO master.menu_access_roles VALUES ('CRMM0002', 104, 2, '1', 2, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.137995');
INSERT INTO master.menu_access_roles VALUES ('CRMM0003', 105, 2, '1', 3, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.139308');
INSERT INTO master.menu_access_roles VALUES ('LEADS0001', 101, 2, '1', 4, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.140713');
INSERT INTO master.menu_access_roles VALUES ('LEADS0003', 116, 2, '1', 5, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.141977');
INSERT INTO master.menu_access_roles VALUES ('FINA0001', 102, 2, '1', 6, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.143081');
INSERT INTO master.menu_access_roles VALUES ('FINA0002', 106, 2, '1', 7, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.144316');
INSERT INTO master.menu_access_roles VALUES ('KPII0001', 103, 2, '1', 8, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.145942');
INSERT INTO master.menu_access_roles VALUES ('KPII0002', 108, 2, '1', 9, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.147261');
INSERT INTO master.menu_access_roles VALUES ('KPII0003', 117, 2, '1', 10, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('CRMM0001', 100, 3, '1', 1, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.131559');
INSERT INTO master.menu_access_roles VALUES ('CRMM0002', 104, 3, '1', 2, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.137995');
INSERT INTO master.menu_access_roles VALUES ('CRMM0003', 105, 3, '1', 3, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.139308');
INSERT INTO master.menu_access_roles VALUES ('LEADS0001', 101, 3, '1', 4, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.140713');
INSERT INTO master.menu_access_roles VALUES ('LEADS0003', 116, 3, '1', 5, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.141977');
INSERT INTO master.menu_access_roles VALUES ('FINA0001', 102, 3, '1', 6, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.143081');
INSERT INTO master.menu_access_roles VALUES ('FINA0002', 106, 3, '1', 7, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.144316');
INSERT INTO master.menu_access_roles VALUES ('KPII0001', 103, 3, '1', 8, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.145942');
INSERT INTO master.menu_access_roles VALUES ('KPII0002', 108, 3, '1', 9, 2, '02598', '192.168.111.111', '2026-11-01 06:02:42.147261');
INSERT INTO master.menu_access_roles VALUES ('KPII0003', 117, 3, '1', 10, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('LEADS0001', 101, 4, '1', 1, 1, '02598', '192.168.111.111', '2026-11-01 06:11:10.298817');
INSERT INTO master.menu_access_roles VALUES ('LEADS0002', 107, 4, '1', 2, 2, '02598', '192.168.111.111', '2026-11-01 06:11:10.298817');
INSERT INTO master.menu_access_roles VALUES ('KPII0003', 117, 4, '1', 3, 1, '02598', '192.168.111.111', '2026-11-09 09:18:00');
INSERT INTO master.menu_access_roles VALUES ('FINA0003', 109, 5, '1', 1, 1, '02598', '192.168.111.111', '2026-11-01 06:02:42.147261');
INSERT INTO master.menu_access_roles VALUES ('CRMM0004', 110, 5, '1', 2, 1, '02598', '192.168.111.111', '2026-11-01 06:11:10.293256');
INSERT INTO master.menu_access_roles VALUES ('CRMM0005', 111, 5, '1', 3, 1, '02598', '192.168.111.111', '2026-11-01 06:11:10.294182');
INSERT INTO master.menu_access_roles VALUES ('CRMM0006', 112, 5, '1', 4, 1, '02598', '192.168.111.111', '2026-11-01 06:11:10.295253');
INSERT INTO master.menu_access_roles VALUES ('CRMM0007', 113, 5, '1', 5, 1, '02598', '192.168.111.111', '2026-11-01 06:11:10.296468');


--
-- Data for Name: roles_assigned; Type: TABLE DATA; Schema: master; Owner: postgres
--

INSERT INTO master.roles_assigned VALUES ('02598', 1, 2, '2026-11-04 16:57:07.074', '2050-11-04 16:57:07.074', NULL, '02598', '2026-11-04 16:57:31.048', '192.168.111.111');
INSERT INTO master.roles_assigned VALUES ('03141', 1, 3, '2026-11-04 16:57:07.074', '2050-11-04 16:57:07.074', NULL, '02598', '2026-11-04 16:57:31.048', '192.168.111.111');
INSERT INTO master.roles_assigned VALUES ('03434', 1, 4, '2026-11-04 16:57:07.074', '2050-11-04 16:57:07.074', NULL, '02598', '2026-11-04 16:57:31.048', '192.168.111.111');


--
-- Name: branches_branch_id_seq; Type: SEQUENCE SET; Schema: master; Owner: postgres
--

SELECT pg_catalog.setval('master.branches_branch_id_seq', 84, true);


--
-- Name: campuses_campus_id_seq; Type: SEQUENCE SET; Schema: master; Owner: postgres
--

SELECT pg_catalog.setval('master.campuses_campus_id_seq', 1, true);


--
-- Name: menus_menu_id_seq; Type: SEQUENCE SET; Schema: master; Owner: postgres
--

SELECT pg_catalog.setval('master.menus_menu_id_seq', 1, true);


--
-- Name: programs_program_id_seq; Type: SEQUENCE SET; Schema: master; Owner: postgres
--

SELECT pg_catalog.setval('master.programs_program_id_seq', 24, true);


--
-- Name: roles_role_id_seq; Type: SEQUENCE SET; Schema: master; Owner: postgres
--

SELECT pg_catalog.setval('master.roles_role_id_seq', 1, false);


ALTER TABLE crm.student_registration DROP CONSTRAINT IF EXISTS pk_student_registration;
ALTER TABLE crm.student_registration ADD CONSTRAINT pk_student_registration_composite PRIMARY KEY (applinano, phoneno);

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
    log_userid VARCHAR(50), 
    log_ipaddress VARCHAR(255),
    log_timestamp TIMESTAMP DEFAULT now(),
    CONSTRAINT fk_student_info_registration 
        FOREIGN KEY (applino, phoneno) 
        REFERENCES crm.student_registration (applinano, phoneno) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS crm.student_academic_details (
    crmid VARCHAR(50), 
    applino VARCHAR(20) NOT NULL,
    phoneno VARCHAR(20) NOT NULL,
    progmid VARCHAR(50),
    branchid INT4,

    tenth_school_name VARCHAR(255),
    tenth_study_board VARCHAR(100),
    tenth_school_city VARCHAR(100),
    tenth_school_district VARCHAR(100),
    tenth_roll_no VARCHAR(50),
    tenth_passed_year INT4,
    tenth_marks_type VARCHAR(50),
    tenth_marks NUMERIC(5,2),
    tenth_cgpa NUMERIC(4,2),

    pu2_college_name VARCHAR(255),
    pu2_study_board VARCHAR(100),
    pu2_college_city VARCHAR(100),
    pu2_college_district VARCHAR(100),
    pu2_roll_no VARCHAR(50),
    pu2_passed_year INT4,
    pu2_marks_type VARCHAR(50),
    pu2_marks NUMERIC(5,2),
    pu2_cgpa NUMERIC(4,2),
    pu2_current_status VARCHAR(100),

    ug_college_name VARCHAR(255),
    ug_university_board VARCHAR(100),
    ug_college_city VARCHAR(100),
    ug_college_district VARCHAR(100),
    ug_roll_no VARCHAR(50),
    ug_passed_year INT4,
    ug_marks_type VARCHAR(50),
    ug_marks NUMERIC(5,2),
    ug_cgpa NUMERIC(4,2),
    ug_current_status VARCHAR(100),

    pg_college_name VARCHAR(255),
    pg_university_board VARCHAR(100),
    pg_college_city VARCHAR(100),
    pg_college_district VARCHAR(100),
    pg_roll_no VARCHAR(50),
    pg_passed_year INT4,
    pg_marks_type VARCHAR(50),
    pg_marks NUMERIC(5,2),
    pg_cgpa NUMERIC(4,2),
    pg_current_status VARCHAR(100),

    log_userid VARCHAR(50),
    log_datetime TIMESTAMP DEFAULT now(),
    log_ipaddress VARCHAR(255),

    CONSTRAINT pk_student_academic PRIMARY KEY (applino, phoneno),
    CONSTRAINT fk_academic_registration 
        FOREIGN KEY (applino, phoneno) 
        REFERENCES crm.student_registration (applinano, phoneno) 
        ON DELETE CASCADE
);

--
-- PostgreSQL database dump complete
--

\unrestrict qlydIJUqQdlczRsdLwrRkNuUZtCPu9nLV2vPoQXo260Q6LyaRFsc9EgQKsnfCQr
