<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS crm.telecaller_leads (
                applino         varchar(20)   NOT NULL,
                phoneno         varchar(20)   NOT NULL,
                crmid           varchar(50)   NULL,
                empcode         varchar(255)  NULL,
                leadcount       int8          NOT NULL DEFAULT 0,
                assigndatetime  timestamp     NULL,
                log_userid      varchar(100)  NULL,
                log_ipaddress   varchar(255)  NULL,
                log_timestamp   timestamp     NULL DEFAULT now(),

                CONSTRAINT pk_telecaller_leads PRIMARY KEY (applino, phoneno),
                CONSTRAINT fk_telecaller_leads_student_reg
                    FOREIGN KEY (applino, phoneno)
                    REFERENCES crm.student_registration (applinano, phoneno)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT
            );
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_leads_applino ON crm.telecaller_leads (applino);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_leads_phoneno ON crm.telecaller_leads (phoneno);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_leads_empcode ON crm.telecaller_leads (empcode);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_leads_crmid   ON crm.telecaller_leads (crmid);");

        DB::statement("
            CREATE TABLE IF NOT EXISTS crm.telecaller_leads_tasks (
                leadtaskid         int4          GENERATED ALWAYS AS IDENTITY
                                                 (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
                applino            varchar(20)   NOT NULL,
                phoneno            varchar(20)   NOT NULL,
                crmid              varchar(50)   NULL,
                empcode            varchar(255)  NULL,
                stage_name         varchar(255)  NULL,
                stage_calltime     timestamp     NULL,
                stage_remark       text          NULL,
                stage_callperson   varchar(255)  NULL,
                log_userid         varchar(100)  NULL,
                log_ipaddress      varchar(255)  NULL,
                log_timestamp      timestamp     NULL DEFAULT now(),

                CONSTRAINT fk_tasks_lead
                    FOREIGN KEY (applino, phoneno)
                    REFERENCES crm.telecaller_leads (applino, phoneno)
                    ON UPDATE CASCADE
                    ON DELETE CASCADE
            );
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_tasks_applino  ON crm.telecaller_leads_tasks (applino);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_tasks_phoneno  ON crm.telecaller_leads_tasks (phoneno);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_tasks_crmid    ON crm.telecaller_leads_tasks (crmid);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_telecaller_tasks_empcode  ON crm.telecaller_leads_tasks (empcode);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crm.telecaller_leads_tasks CASCADE;");
        DB::statement("DROP TABLE IF EXISTS crm.telecaller_leads CASCADE;");
    }
};

