<?php

namespace App\Livewire\Brand;

use App\Helper;
use App\Jobs\ExportBrandTable;
use App\Models\Brand;
use App\Traits\RefreshDataTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
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

    public string $sortField = 'brands.id';

    public string $sortDirection = 'desc';

    // Custom per page
    public int $perPage;

    // Custom per page values
    public array $perPageValues;

    public $currentUser;

    public function __construct()
    {
        if (! Gate::allows('view-brand')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->tableName = __('messages.brand.listing.tableName');
        $this->perPage = config('constants.webPerPage');
        $this->perPageValues = config('constants.webPerPageValues');
    }

    public function exportData()
    {
        try {
            // Define export parameters
            $exportClass = ExportBrandTable::class;
            $headingColumn = 'Id,Name,Remark,Bob,StartDate,StartTime';
            $batchName = 'Export Brand Table';
            $downloadPrefixFileName = 'BrandReports_';
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
            logger()->error('App\Livewire\BrandTable: exportData: Throwable', ['Message' => $e->getMessage(), 'TraceAsString' => $e->getTraceAsString()]);
            session()->flash('error', __('messages.brand.messages.common_error_message'));

            return false;
        }
    }

    public function header(): array
    {
        $headerArray = [];

        if (Gate::allows('add-brand')) {
            $headerArray[] = Button::add('add-brand')
                ->slot('    <a href="/brand/create" title="Add New Brand" data-testid="add_new" class="flex items-center justify-center" wire:navigate>
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

        if (Gate::allows('export-brand')) {
            $headerArray[] = Button::add('export-data')
                ->slot('
                    <a href="javascript:void(0);" title="Export Brand" data-testid="export_button" wire:click="exportData" class="flex items-center justify-center" wire:loading.attr="disabled">
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

        if (Gate::allows('bulkDelete-brand')) {
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
                    title="Bulk Delete Brands">
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
        return Brand::query()

            ->select([
                'brands.id', 'brands.name', 'brands.remark', 'brands.bob', 'brands.start_date', 'brands.start_time',
            ]);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('bob_formatted', fn ($row) => Carbon::parse($row->bob)->format(config('constants.default_datetime_format')))
            ->add('start_date_formatted', fn ($row) => Carbon::parse($row->start_date)->format(config('constants.default_date_format')))
            ->add('start_time_formatted', fn ($row) => Carbon::parse($row->start_time)->format(config('constants.default_time_format')))
            ->add('created_at_formatted', fn ($row) => Carbon::parse($row->created_at)->format(config('constants.default_datetime_format')));
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.brand.listing.id'), 'id')->sortable(),

            Column::make(__('messages.brand.listing.name'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.remark'), 'remark')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.bob'), 'bob_formatted', 'bob')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.start_date'), 'start_date_formatted', 'start_date')
                ->sortable()
                ->searchable(),

            Column::make(__('messages.brand.listing.start_time'), 'start_time_formatted', 'start_time')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.created_date'), 'created_at_formatted', 'created_at'),
            Column::action(__('messages.brand.listing.actions')),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name', 'brands.name')->operators(['contains']),
            Filter::inputText('remark', 'brands.remark')->operators(['contains']),
            Filter::datetimepicker('bob'),
            Filter::datepicker('start_date'),

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
        return $this->redirect('brand/' . $id . '/edit', navigate: true); // redirect to edit component
    }

    public function actions(Brand $row): array
    {
        $actions = [];

        if (Gate::allows('show-brand')) {
            $actions[] = Button::add('view')
                ->slot('<div title="' . __('messages.tooltip.view') . '" class="flex items-center justify-center" data-testid="view_button">' . view('components.flux.icon.eye', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-green-600 hover:text-green-800'])])->render() . '</div>')
                ->class('border border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatchTo('brand.show', 'show-brand-info', ['id' => $row->id]);
        }

        if (Gate::allows('edit-brand')) {
            $actions[] = Button::add('edit')
                ->slot('<div title="Edit" class="flex items-center justify-center" data-testid="edit_button">' . view('components.flux.icon.pencil', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-blue-600 hover:text-blue-800'])])->render() . '</div>')
                ->class('border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatch('edit', ['id' => $row->id]);
        }

        if (Gate::allows('delete-brand')) {
            $actions[] = Button::add('delete-brand')
                ->slot('<div title="' . __('messages.tooltip.click_delete') . '" class="flex items-center justify-center" data-testid="delete_button">' . view('components.flux.icon.trash', ['variant' => 'micro', 'attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'text-red-600 hover:text-red-800'])])->render() . '</div>')
                ->class('border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 py-2 px-2 rounded text-sm cursor-pointer hover:cursor-pointer')
                ->dispatchTo('brand.delete', 'delete-confirmation', ['ids' => [$row->id], 'tableName' => $this->tableName]);
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
