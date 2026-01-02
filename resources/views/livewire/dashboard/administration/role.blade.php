<div>
    <div class="row mb-3">
        <div class="col-md-2">
            <select class="form-select" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <div class="col-md-4 ms-auto">
            <input
                type="text"
                class="form-control"
                placeholder="Search..."
                wire:model.live="search"
            >
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th width="60">No</th>
                <th>Role</th>
                <th>Location</th>
                <th width="100">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $index => $role)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->location }}</td>
                    <td>-</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        Data tidak ditemukan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $roles->links() }}
    </div>
</div>