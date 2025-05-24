<?php

namespace Modules\Synktime\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Log;
use Modules\Synktime\Entities\SynkingHistory;

class DepartmentSyncService
{
    /**
     * Sync departments from API data
     *
     * @param array $departments
     * @param int $companyId
     * @param int $userId
     * @return int Number of departments synced
     */
    public function syncDepartments(array $departments, int $companyId, int $userId): int
    {
        $syncCount = 0;
        $departmentMap = [];

        try {
            // First pass: Create departments without parent relationships
            foreach ($departments as $department) {
                // Check if department exists by name
                $existingTeam = Team::where('team_name', $department['name'])->first();

                if (!$existingTeam) {
                    // Create new department
                    $team = new Team();
                    $team->team_name = $department['name'];
                    $team->save();

                    $departmentMap[$department['name']] = $team->id;
                    $syncCount++;
                } else {
                    $departmentMap[$department['name']] = $existingTeam->id;
                }
            }

            // Second pass: Update parent relationships
            foreach ($departments as $department) {
                if (!empty($department['parent_name']) && isset($departmentMap[$department['parent_name']])) {
                    $team = Team::where('team_name', $department['name'])->first();

                    if ($team) {
                        $team->parent_id = $departmentMap[$department['parent_name']];
                        $team->save();
                    }
                }
            }

            // Record history
            $this->recordSyncHistory($companyId, $userId, $syncCount);

            return $syncCount;
        } catch (\Exception $e) {
            Log::error('Department sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 0;
        }
    }

    /**
     * Record sync history
     *
     * @param int $companyId
     * @param int $userId
     * @param int $syncCount
     * @return void
     */
    private function recordSyncHistory(int $companyId, int $userId, int $syncCount): void
    {
        SynkingHistory::create([
            'company_id' => $companyId,
            'created_by' => $userId,
            'updated_by' => $userId,
            'sync_type' => 'department',
            'total_synced' => $syncCount,
            'from_date' => now()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]);
    }
}
