<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-10">

        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePassword">

            <div class="mb-3">
                <label class="form-label fw-semibold">Current Password</label>
                <input
                    type="password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    wire:model.defer="current_password"
                >
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    wire:model.defer="password"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input
                    type="password"
                    class="form-control"
                    wire:model.defer="password_confirmation"
                >
            </div>

            <div class="d-flex justify-content-end">
                <button
                    type="submit"
                    class="btn btn-danger"
                    wire:loading.attr="disabled"
                    wire:target="updatePassword"
                >
                    <span wire:loading.remove wire:target="updatePassword">
                        Update Password
                    </span>
                    <span wire:loading wire:target="updatePassword">
                        Processing...
                    </span>
                </button>
            </div>

        </form>

    </div>
</div>