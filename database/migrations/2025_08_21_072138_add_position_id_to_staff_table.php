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
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('position_id')->nullable()->after('position');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
        });

        // Create default positions based on existing staff data
        $this->createDefaultPositions();
        
        // Migrate existing position data
        $this->migrateExistingPositions();
        
        // Drop the old position column
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('position')->nullable()->after('phone');
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });

        // Restore old position data
        $this->restoreOldPositions();
    }

    /**
     * Create default positions based on existing staff data
     */
    private function createDefaultPositions(): void
    {
        // Get unique positions from existing staff data
        $existingPositions = DB::table('staff')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position')
            ->filter()
            ->values();

        // Create positions table if it doesn't exist yet
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Insert default positions
        foreach ($existingPositions as $positionTitle) {
            DB::table('positions')->insertOrIgnore([
                'title' => $positionTitle,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add some common positions that might not exist yet
        $commonPositions = [
            'Regional Director',
            'Regional Program Lead',
            'Finance Officer',
            'Admin',
            'Technical Officer Digital System',
            'National Coordinator',
            'Senior Country Representative',
            'Program Officer',
            'Monitoring & Evaluation Officer',
            'Communications Officer',
            'Human Resources Officer',
            'IT Officer',
            'Logistics Officer',
            'Research Officer',
            'Training Coordinator'
        ];

        foreach ($commonPositions as $positionTitle) {
            DB::table('positions')->insertOrIgnore([
                'title' => $positionTitle,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Migrate existing position data to use position_id
     */
    private function migrateExistingPositions(): void
    {
        $staffWithPositions = DB::table('staff')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->get();

        foreach ($staffWithPositions as $staff) {
            $position = DB::table('positions')
                ->where('title', $staff->position)
                ->first();

            if ($position) {
                DB::table('staff')
                    ->where('id', $staff->id)
                    ->update(['position_id' => $position->id]);
            }
        }
    }

    /**
     * Restore old position data (for rollback)
     */
    private function restoreOldPositions(): void
    {
        $staffWithPositionIds = DB::table('staff')
            ->join('positions', 'staff.position_id', '=', 'positions.id')
            ->select('staff.id', 'positions.title')
            ->get();

        foreach ($staffWithPositionIds as $staff) {
            DB::table('staff')
                ->where('id', $staff->id)
                ->update(['position' => $staff->title]);
        }
    }
};
