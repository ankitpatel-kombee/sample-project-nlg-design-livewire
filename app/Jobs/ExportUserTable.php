<?php

namespace App\Jobs;

use App\Helper;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExportUserTable implements ShouldQueue
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
        $query = User::query();

        $query
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->leftJoin('states', 'states.id', '=', 'users.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
            ->select([
                'users.id', 'users.name', 'users.email', 'roles.name as role_name', 'users.dob', 'users.profile', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name',
                DB::raw(
                    '(CASE
                                    WHEN users.gender = "' . config('constants.user.gender.key.female') . '" THEN  "' . config('constants.user.gender.value.female') . '"
                                    WHEN users.gender = "' . config('constants.user.gender.key.male') . '" THEN  "' . config('constants.user.gender.value.male') . '"
                            ELSE " "
                            END) AS gender'
                ),
                DB::raw(
                    '(CASE
                                    WHEN users.status = "' . config('constants.user.status.key.active') . '" THEN  "' . config('constants.user.status.value.active') . '"
                                    WHEN users.status = "' . config('constants.user.status.key.inactive') . '" THEN  "' . config('constants.user.status.value.inactive') . '"
                            ELSE " "
                            END) AS status'
                ),
            ]);

        // Apply name filters
        $where_name = $this->filters['input_text']['users']['name'] ?? null;
        if ($where_name) {
            $query->where('users.name', 'like', "%$where_name%");
        }

        // Apply email filters
        $where_email = $this->filters['input_text']['users']['email'] ?? null;
        if ($where_email) {
            $query->where('users.email', 'like', "%$where_email%");
        }

        // Apply dob filters
        $where_start = $this->filters['datetime']['users']['dob']['start'] ?? null;
        $where_end = $this->filters['datetime']['users']['dob']['end'] ?? null;

        if ($where_start && $where_end) {
            $query->whereBetween('users.dob', [$where_start, $where_end]);
        }

        // Apply country_id filters
        $where_country_id = $this->filters['select']['users']['country_id'] ?? null;
        if ($where_country_id) {
            $query->where('users.country_id', $where_country_id);
        }

        // Apply state_id filters
        $where_state_id = $this->filters['select']['users']['state_id'] ?? null;
        if ($where_state_id) {
            $query->where('users.state_id', $where_state_id);
        }

        // Apply city_id filters
        $where_city_id = $this->filters['select']['users']['city_id'] ?? null;
        if ($where_city_id) {
            $query->where('users.city_id', $where_city_id);
        }

        // Apply gender filters
        $where_gender = $this->filters['select']['users']['gender'] ?? null;
        if ($where_gender) {
            $query->where('users.gender', $where_gender);
        }

        // Apply status filters
        $where_status = $this->filters['select']['users']['status'] ?? null;
        if ($where_status) {
            $query->where('users.status', $where_status);
        }

        // Apply checkbox filter: If user select checkbox then only that result will be exported
        if ($this->checkboxValues) {
            $query->whereIn('users.id', $this->checkboxValues);
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
            ->whereNull('users.deleted_at')
            ->orderByDesc('users.id')
            ->groupBy('users.id')
            ->skip($offset)->take($itemCountBatching)->get()->toArray();

        // Convert query result to array
        // $final_data = json_decode(json_encode($query_data), true);

        $final_data = $query_data;

        // Call Helper method to put data into export file
        Helper::putExportData('ExportUserTable', $final_data, $file, $sr_no);
    }
}
