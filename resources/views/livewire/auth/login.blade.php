<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="card p-4 ps-5 pe-5">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo.png') }}" class="rounded" width="270" alt="logo">
                    </div>
                </div>
                @if ($errors->has('alert'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ $errors->first('alert') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                    <form wire:submit.prevent="login">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-bold ph-user"></i>
                            </span>
                            <input type="text" class="form-control border-left-0 p-2" placeholder="Username" wire:model="username">
                        </div>
                        @error('username')<div class="form-text text-danger">{{ $message }}</div> @enderror
                        <div class="input-group mt-3">
                            <span class="input-group-text">
                                <i class="ph-bold ph-lock"></i>
                            </span>
                            <input type="password" class="form-control border-left-0 p-2" placeholder="Password" wire:model="password">
                        </div>
                        @error('password')<div class="form-text text-danger">{{ $message }}</div> @enderror
                        <div class="input-group mb-3 mt-3">
                            <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                                <span wire:loading.remove>Login</span>
                                <span wire:loading>Loading...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>