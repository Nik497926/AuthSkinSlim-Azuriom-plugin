@extends('admin.layouts.admin')

@section('title', trans('auth-skin-slim::admin.title'))

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ trans('auth-skin-slim::admin.title') }}</h5>
        </div>
        <div class="card-body">
            @if(! $skinApiEnabled)
                <div class="alert alert-warning mb-4">
                    {{ trans('auth-skin-slim::admin.skin_api_disabled') }}
                </div>
            @endif

            <p class="text-muted">{{ trans('auth-skin-slim::admin.intro') }}</p>

            <form method="POST" action="{{ route('auth-skin-slim.admin.settings.update') }}">
                @csrf

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="relaxSkinApi"
                           name="relax_skin_api_dimensions" value="1"
                           @checked(old('relax_skin_api_dimensions', $relaxSkinApiDimensions))>
                    <label class="form-check-label" for="relaxSkinApi">
                        {{ trans('auth-skin-slim::admin.relax_label') }}
                    </label>
                </div>
                <p class="small text-muted">{{ trans('auth-skin-slim::admin.relax_help') }}</p>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
