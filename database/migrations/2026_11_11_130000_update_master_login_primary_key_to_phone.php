<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            ALTER TABLE master.login_otp DROP CONSTRAINT IF EXISTS fk_login_otp_usercode CASCADE;
            ALTER TABLE master.login DROP CONSTRAINT IF EXISTS login_pkey CASCADE;
            ALTER TABLE master.login ALTER COLUMN phnno SET NOT NULL;
            ALTER TABLE master.login ADD CONSTRAINT login_pkey PRIMARY KEY (phnno);
            CREATE UNIQUE INDEX IF NOT EXISTS login_usercode_unique ON master.login (usercode) WHERE usercode <> '0';
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP INDEX IF EXISTS master.login_usercode_unique;
            ALTER TABLE master.login DROP CONSTRAINT IF EXISTS login_pkey CASCADE;
            ALTER TABLE master.login ADD CONSTRAINT login_pkey PRIMARY KEY (usercode);
            ALTER TABLE master.login_otp ADD CONSTRAINT fk_login_otp_usercode 
                FOREIGN KEY (usercode) REFERENCES master.login (usercode) ON UPDATE CASCADE ON DELETE CASCADE;
        ");
    }
};

