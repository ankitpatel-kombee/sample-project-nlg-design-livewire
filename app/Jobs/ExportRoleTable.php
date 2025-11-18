<?php

namespace App\Jobs;

use App\Helper;
use App\Models\Role;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExportRoleTable implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $index;

    public $itemCountBatching;

    public $file;

    public $filters;

    public $checkboxValues;

    public $search;

    public $extraParam;

    /**
     * Create a new job instance.
     */
    public function __construct($index, $itemCountBatching, $file, $filters, $checkboxValues, $search, $extraParam)
    {
        $this->index = $index;
        $this->itemCountBatching = $itemCountBatching;
        $this->file = $file;
        $this->filters = $filters;
        $this->checkboxValues = $checkboxValues;
        $this->search = $search;
        $this->extraParam = $extraParam;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Extract parameters
        $index = $this->index;
        $itemCountBatching = $this->itemCountBatching;
        $sr_no = $offset = ($index - 1) * $itemCountBatching;
        $file = $this->file;
        $search = $this->search;

        // Initialize query builder
        $query = Role::query();

        $query
            ->select([
                'roles.name',
                DB::raw(
                    '(CASE
                                    WHEN roles.status = "' . config('constants.role.status.key.active') . '" THEN  "' . config('constants.role.status.value.active') . '"
                                    WHEN roles.status = "' . config('constants.role.status.key.inactive') . '" THEN  "' . config('constants.role.status.value.inactive') . '"
                            ELSE " "
                            END) AS status'
                ),
            ]);

        // Apply name filters
        $where_name = $this->filters['input_text']['roles']['name'] ?? null;
        if ($where_name) {
            $query->where('roles.name', 'like', "%$where_name%");
        }

        // Apply status filters
        $where_status = $this->filters['select']['roles']['status'] ?? null;
        if ($where_status) {
            $query->where('roles.status', $where_status);
        }

        // Apply checkbox filter: If role select checkbox then only that result will be exported
        if ($this->checkboxValues) {
            $query->whereIn('roles.id', $this->checkboxValues);
        }

        // Apply search filter
        // if ($search) {
        // $query->where(function ($query) use ($search, $exportableColumns) {
        //  foreach ($exportableColumns as $column) {
        //     $query->orWhere($column, 'like', '%' . $search . '%');
        //  }
        //  });
        //  }

        // Execute query and fetch data
        $query_data = $query
            ->whereNull('roles.deleted_at')
            ->orderByDesc('roles.id')
            ->groupBy('roles.id')
            ->skip($offset)->take($itemCountBatching)->get()->toArray();

        // Convert query result to array
        // $final_data = json_decode(json_encode($query_data), true);

        $final_data = $query_data;

        // Call Helper method to put data into export file
        Helper::putExportData('ExportRoleTable', $final_data, $file, $sr_no);
    }
}
