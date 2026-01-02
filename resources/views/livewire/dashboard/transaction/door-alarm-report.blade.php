<div>
    <div class="mb-3 text-end">
        <button
            class="btn btn-outline-danger btn-sm"
            wire:click="exportPdf"
            wire:loading.attr="disabled"
            @disabled($door_alarm_reports->total() === 0)
        >
            <span wire:loading.remove>
                <i class="ph-bold ph-file-pdf"></i> Export PDF
            </span>
            <span wire:loading>
                Generating...
            </span>
        </button>

        <button
            class="btn btn-outline-success btn-sm"
            wire:click="exportExcel"
            wire:loading.attr="disabled"
            @disabled($door_alarm_reports->total() === 0)
        >
            <span wire:loading.remove>
                <i class="ph-bold ph-file-xls"></i> Export Excel
            </span>
            <span wire:loading>
                Generating...
            </span>
        </button>
    </div>  

    <div class="row mb-3">
        <div class="col-md-2">
            <span>Display:</span>
            <select class="form-select" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="col-md-2">
            <span>Location:</span>
            <select class="form-select" wire:model.live="location">
                <option value="">-- All --</option>
                @foreach ($locations as $value)
                    <option value="{{ $value->id }}">{{ $value->description }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <span>Date:</span>
            <div class="input-group">
                <input
                    type="date"
                    class="form-control"
                    wire:model.live="startDate"
                >
                <span class="input-group-text">to</span>
                <input
                    type="date"
                    class="form-control"
                    wire:model.live="endDate"
                >
            </div>
        </div>


        <div class="col-md-3 ms-auto">
            <span>Search:</span>
            <input
                type="text"
                class="form-control"
                wire:model.live="search"
                placeholder="Search..."
            >
        </div>
    </div>

    <div class="table-responsive position-relative">
        <div
            wire:loading.flex
            class="position-absolute top-0 start-0 w-100 h-100
                   bg-white bg-opacity-75
                   justify-content-center align-items-center"
            style="z-index: 10;"
        >
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <table class="table table-striped table-hover table-light mb-0 rounded-table">
            <thead class="table-dark">
                <tr>
                    <th>Date / Time</th>
                    <th>Event Name</th>
                    <th>Door Name</th>
                    <th>Location</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($door_alarm_reports as $value)
                    <tr>
                        <td>{{ $value->ts }}</td>
                        <td>{{ $value->msgdescription }}</td>
                        <td>{{ Str::between($value->hardwaredescription, '[', ']') }}</td>
                        <td>{{ $value->partition?->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <i class="ph-bold ph-x-circle"></i> Data not found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3" wire:loading.class="opacity-50 pe-none">
        {{ $door_alarm_reports->links() }}
    </div>
</div>