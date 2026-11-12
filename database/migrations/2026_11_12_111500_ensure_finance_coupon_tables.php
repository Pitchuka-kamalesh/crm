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
        DB::statement("CREATE SCHEMA IF NOT EXISTS finance;");

        DB::statement("
            CREATE TABLE IF NOT EXISTS finance.couponinfo (
                id SERIAL PRIMARY KEY,
                couponsno VARCHAR(50) NOT NULL UNIQUE,
                admyear VARCHAR(20) DEFAULT '2026-27',
                campuscode VARCHAR(50) DEFAULT 'VAD',
                coupontype VARCHAR(50) NOT NULL,
                couponname VARCHAR(150),
                applino VARCHAR(50) NULL,
                crmid VARCHAR(50) NULL,
                coupcount INT DEFAULT 1,
                coucharcount INT DEFAULT 11,
                couponcodestart VARCHAR(100),
                currentcouponcode VARCHAR(100),
                discountamount NUMERIC(10,2) NOT NULL DEFAULT 0.00,
                discount NUMERIC(10,2) NOT NULL DEFAULT 0.00,
                coupcurrcount INT DEFAULT 0,
                coupstatus VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
                usercode VARCHAR(50) DEFAULT '03141',
                user_ip VARCHAR(45) DEFAULT '127.0.0.1',
                log_userid VARCHAR(100) DEFAULT '03141',
                log_ipaddress VARCHAR(255) DEFAULT '127.0.0.1',
                log_timestamp TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ");

        // Ensure columns exist if table was already created
        $columnsToEnsure = [
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS campuscode VARCHAR(50) DEFAULT 'VAD';",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS applino VARCHAR(50) NULL;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS crmid VARCHAR(50) NULL;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS coupcount INT DEFAULT 1;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS coucharcount INT DEFAULT 11;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS couponcodestart VARCHAR(100);",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS currentcouponcode VARCHAR(100);",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS discountamount NUMERIC(10,2) NOT NULL DEFAULT 0.00;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS discount NUMERIC(10,2) NOT NULL DEFAULT 0.00;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS coupcurrcount INT DEFAULT 0;",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS coupstatus VARCHAR(20) NOT NULL DEFAULT 'ACTIVE';",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS usercode VARCHAR(50) DEFAULT '03141';",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS user_ip VARCHAR(45) DEFAULT '127.0.0.1';",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS log_userid VARCHAR(100) DEFAULT '03141';",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS log_ipaddress VARCHAR(255) DEFAULT '127.0.0.1';",
            "ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS log_timestamp TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP;",
        ];

        foreach ($columnsToEnsure as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {}
        }

        // Ensure finance.paydetails exists and tracks both default and personal coupon details
        DB::statement("
            CREATE TABLE IF NOT EXISTS finance.paydetails (
                feeid SERIAL PRIMARY KEY,
                applino VARCHAR(50) NOT NULL,
                feepayable NUMERIC(10,2) DEFAULT 1200.00,
                defcoupon VARCHAR(100),
                defcouponamount NUMERIC(10,2) DEFAULT 0.00,
                percoupon VARCHAR(100),
                percouponamount NUMERIC(10,2) DEFAULT 0.00,
                totalcouponamount NUMERIC(10,2) DEFAULT 0.00,
                paidamount NUMERIC(10,2) DEFAULT 0.00,
                amountpaid NUMERIC(10,2) DEFAULT 0.00,
                paytype VARCHAR(50) DEFAULT 'ONLINE',
                paymentstatus VARCHAR(50) DEFAULT 'SUCCESS',
                txnid VARCHAR(100),
                txndatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                txncrmid VARCHAR(50),
                txnpayuid VARCHAR(100),
                txnerror TEXT,
                txnremark TEXT,
                txnstatus VARCHAR(50),
                log_userid VARCHAR(50),
                log_ipaddress VARCHAR(50),
                log_timestamp TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            );
        ");

        $payColumnsToEnsure = [
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS defcoupon VARCHAR(100);",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS defcouponamount NUMERIC(10,2) DEFAULT 0.00;",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS percoupon VARCHAR(100);",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS percouponamount NUMERIC(10,2) DEFAULT 0.00;",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS totalcouponamount NUMERIC(10,2) DEFAULT 0.00;",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS paidamount NUMERIC(10,2) DEFAULT 0.00;",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS amountpaid NUMERIC(10,2) DEFAULT 0.00;",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS paymentstatus VARCHAR(50) DEFAULT 'SUCCESS';",
            "ALTER TABLE finance.paydetails ADD COLUMN IF NOT EXISTS txnid VARCHAR(100);",
        ];

        foreach ($payColumnsToEnsure as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op to preserve financial audit trail
    }
};

