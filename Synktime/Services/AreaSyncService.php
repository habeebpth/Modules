<?php

namespace Modules\Synktime\Services;

use App\Models\CompanyAddress;
use Illuminate\Support\Facades\Log;
use Modules\Synktime\Entities\SynkingHistory;

class AreaSyncService
{
    /**
     * Sync areas from API data
     *
     * @param array $areas
     * @param int $companyId
     * @param int $userId
     * @return int Number of areas synced
     */
    public function syncAreas(array $areas, int $companyId, int $userId): int
    {
        $syncCount = 0;

        try {
            foreach ($areas as $area) {
                // Check if office exists by name
                $existingOffice = CompanyAddress::where('company_id', $companyId)
                    ->where('address', $area['area_name'])
                    ->first();

                if (!$existingOffice) {
                    // Create new office
                    $office = new CompanyAddress();
                    $office->company_id = $companyId;
                    $office->address = $area['area_name'];
                    $office->location = $area['area_name']; // Using area name as location too
                    $office->save();
                    $syncCount++;
                }
            }

            // Record history
            $this->recordSyncHistory($companyId, $userId, $syncCount);

            return $syncCount;
        } catch (\Exception $e) {
            Log::error('Area sync failed', [
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
            'sync_type' => 'area',
            'total_synced' => $syncCount,
            'from_date' => now()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]);
    }
}
