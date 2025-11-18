<?php

namespace App\Livewire\User;

use App\Helper;
use App\Jobs\ExportUserTable;
use App\Models\User;
use App\Traits\RefreshDataTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Throwable;

final class Table extends PowerGridComponent
{
    use RefreshDataTable;

    public bool $deferLoading = true; // default false

    public string $tableName;

    public string $loadingComponent = 'components.powergrid-loading';

    public string $sortField = 'users.id';

    public string $sortDirection = 'desc';

    // Custom per page
    public int $perPage;

    // Custom per page values
    public array $perPageValues;

    public $currentUser;

    public function __construct()
    {
        if (! Gate::allows('view-user')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->tableName = __('messages.user.listing.tableName');
        $this->perPage = config('constants.webPerPage');
        $this->perPageValues = config('constants.webPerPageValues');
    }

    public function exportData()
    {
        try {
            // Define export parameters
            $exportClass = ExportUserTable::class;
            $headingColumn = 'Id,Name,Email,Role,Dob,Profile,Country,State,City,Gender,Status';
            $batchName = 'Export User Table';
            $downloadPrefixFileName = 'UserReports_';
            $extraParam = [];

            // Run export job and handle result
            $result = Helper::runExportJob($this->total, $this->filters, $this->checkboxValues, $this->search, $headingColumn, $downloadPrefixFileName, $exportClass, $batchName, $extraParam);
            if (! $result['status']) {
                // Dispatch error alert if export fails
                $this->dispatch('alert', type: 'error', message: $result['message']);

                return false;
            }

            // Dispatch event to show export progress
            $this->dispatch('showExportProgressEvent', json_encode($result['data']))->to('common-code');
        } catch (Throwable $e) {
            // Log and dispatch error alert if exception occurs
            logger()->error('App\Livewire\UserTable: exportData: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);
            session()->flash('error', __('messages.user.messages.common_error_message'));

            return false;
        }
    }

    public function header(): array
    {
        $headerArray = [];

        if (Gate::allows('add-user')) {
            $headerArray[] = Button::add('add-user')
                ->slot('    <a href="/user/create" title="Add New User" data-testid="add_new" class="flex items-center justify-center" wire:navigate>
        <svg class="h-5 w-5 text-pg-white-500 dark:text-pg-white-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
    </a>')
                ->class(
                    'flex rounded-md ring-1 transition focus:ring-2
                        dark:text-white text-white
                        bg-black hover:bg-gray-800
                        border-0 py-2 px-3
                        focus:outline-none
                        sm:text-sm sm:leading-6
                        w-11 h-9 inline-flex items-center justify-center ml-1
                        focus:ring-black focus:ring-offset-1'
                );
        }

        if (Gate::allows('export-user')) {
            $headerArray[] = Button::add('export-data')
                ->slot('
                    <a href="javascript:void(0);" title="Export User" data-testid="export_button" wire:click="exportData" class="flex items-center justify-center" wire:loading.attr="disabled">
                        <svg class="h-5 w-5 text-white dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                ')
                ->class('
                    flex rounded-md ring-1 transition focus:ring-2
                    text-white bg-green-600 hover:bg-green-700
                    border-0 py-2 px-3
                    focus:outline-none
                    sm:text-sm sm:leading-6
                    w-11 h-9 inline-flex items-center justify-center ml-1
                    focus:ring-green-600 focus:ring-offset-1
                ');
        }

        if (Gate::allows('bulkDelete-user')) {
            $headerArray[] = Button::add('bulk-delete')
                ->slot('<div x-show="$wire.checkboxValues && $wire.checkboxValues.length > 0" x-transition>
                <div class="flex items-center justify-center 
                    cursor-pointer
                    focus:ring-red-600
                    flex rounded-md ring-1 transition focus:ring-2
                    text-white ring-red-700
                    bg-red-600 hover:bg-red-700
                    border-0 py-2 px-3
                    focus:outline-none
                    sm:text-sm sm:leading-6
                    w-11 h-9 inline-flex items-center justify-center ml-1"
                    data-testid="bulk_delete_button"
                    wire:click="bulkDelete"
                    title="Bulk Delete Users">
                    <svg class="h-5 w-5 text-pg-white-500 dark:text-pg-white-300"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </div>
            </div>
            ');
        }

        return $headerArray;
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [

            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage($this->perPage, $this->perPageValues)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        // Main query
        return User::query()
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->leftJoin('states', 'states.id', '=', 'users.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
            ->leftJoin('user_product', 'user_product.user_id', '=', 'users.id')
            ->leftJoin('products', 'products.id', '=', 'user_product.product_id')
            ->select([
                'users.id', 'users.name', 'users.email', 'users.password', 'roles.name as role_name', 'users.description', 'users.dob', 'users.profile', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name',
                DB::raw(
                    '(CASE
                                        WHEN users.gender = "' . config('constants.user.gender.key.female') . '" THEN  "' . config('constants.user.gender.value.female') . '"
                                        WHEN users.gender = "' . config('constants.user.gender.key.male') . '" THEN  "' . config('constants.user.gender.value.male') . '"
                                ELSE " "
                                END) AS gender'
                ), DB::raw('GROUP_CONCAT(DISTINCT products.name) AS products_name'),
                DB::raw(
                    '(CASE
                                        WHEN users.status = "' . config('constants.user.status.key.active') . '" THEN  "' . config('constants.user.status.value.active') . '"
                                        WHEN users.status = "' . config('constants.user.status.key.inactive') . '" THEN  "' . config('constants.user.status.value.inactive') . '"
                                ELSE " "
                                END) AS status'
                ), 'users.email_verified_at', 'users.remember_token', 'users.locale',
            ])->groupBy('users.id');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('dob_formatted', fn ($row) => Carbon::parse($row->dob)->format(config('constants.default_date_format')))
            ->add('created_at_formatted', fn ($row) => Carbon::parse($row->created_at)->format(config('constants.default_datetime_format')));
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.user.listing.id'), 'id')->sortable(),

            Column::make(__('messages.user.listing.name'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.user.listing.email'), 'email')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.user.listing.roles'), 'role_name')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.user.listing.dob'), 'dob_formatted', 'dob')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.user.listing.gender'), 'gender')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.user.listing.status'), 'status')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.created_date'), 'created_at_formatted', 'created_at'),
            Column::action(__('messages.user.listing.actions')),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name', 'users.name')->operators(['contains']),
            Filter::inputText('email', 'users.email')->operators(['contains']),

            Filter::datepicker('dob'),
            Filter::select('gender', 'gender')
                ->dataSource(User::gender())
                ->optionLabel('label')
                ->optionValue('key'),
            Filter::select('status', 'status')
                ->dataSource(User::status())
                ->optionLabel('label')
                ->optionValue('key'),
            Filter::datetimepicker('created_at'),
        ];
    }

    #[On('edit')]
    /**
     * edit
     *
     * @param mixed $rowId
     */
    public function edit($id)
    {
        return $this->redirect('user/' . $id . '/edit', navigate: true); // redirect to edit component
    }

    public function actions(User $row): array
    {
        $actions = [];

        if (Gate::allows('show-user')) {
            $actions[] = Button::add('view')
                ->slot('<div title="' . __('messages.tooltip.view') . '" class="flex items-center justify-center" data-testid="view_button">' . view('components.flux.icon.eye', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-green-600 hover:text-green-800'])])->render() . '</div>')
                ->class('border border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatchTo('user.show', 'show-user-info', ['id' => $row->id]);
        }

        if (Gate::allows('edit-user')) {
            $actions[] = Button::add('edit')
                ->slot('<div title="Edit" class="flex items-center justify-center" data-testid="edit_button">' . view('components.flux.icon.pencil', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-blue-600 hover:text-blue-800'])])->render() . '</div>')
                ->class('border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatch('edit', ['id' => $row->id]);
        }

        if (Gate::allows('delete-user')) {
            $actions[] = Button::add('delete-user')
                ->slot('<div title="' . __('messages.tooltip.click_delete') . '" class="flex items-center justify-center" data-testid="delete_button">' . view('components.flux.icon.trash', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-red-600 hover:text-red-800'])])->render() . '</div>')
                ->class('border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatchTo('user.delete', 'delete-confirmation', ['ids' => [$row->id], 'tableName' => $this->tableName]);
        }

        return $actions;
    }

    /**
     * actionRules
     *
     * @param mixed $row
     */
    public function actionRules($row): array
    {
        return [];
    }

    /**
     * handlePageChange
     */
    public function handlePageChange()
    {
        $this->checkboxAll = false;
        $this->checkboxValues = [];
    }

    #[On('deSelectCheckBoxEvent')]
    public function deSelectCheckBox(): bool
    {
        $this->checkboxAll = false;
        $this->checkboxValues = [];

        return true;
    }

    public function bulkDelete(): void
    {
        try {
            // Clear any existing error message
            if (! empty($this->checkboxValues)) {
                // Dispatch to the delete component
                $this->dispatch('bulk-delete-confirmation', [
                    'ids' => $this->checkboxValues,
                    'tableName' => $this->tableName,
                ]);
            } else {
                // Show flash message using Livewire event
                session()->flash('error', __('messages.bulk_delete.no_users_selected'));
            }
        } catch (Throwable $e) {
            // Defer logging to run after response
            defer(function () use ($e) {
                logger()->error('App\Livewire\User\Table: bulkDelete: Throwable', [
                    'Message' => $e->getMessage(),
                    'TraceAsString' => $e->getTraceAsString(),
                ]);
            });
            session()->flash('error', __('messages.bulk_delete.failed'));
        }
    }
}
