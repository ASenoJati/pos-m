<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel yang modelnya memakai trait SoftDeletes tetapi kolomnya
     * belum pernah dibuat oleh migrasi mana pun.
     *
     * @var array<int, string>
     */
    private array $tables = ['items', 'customers', 'checkouts'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropSoftDeletes();
                });
            }
        }
    }
};
