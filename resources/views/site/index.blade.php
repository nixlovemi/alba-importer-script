@inject('MainControllerPresenter', 'App\Presenters\MainControllerPresenter')

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('CORE_BODY_CONTENT')
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div>
                                        <h1 class="h4 text-gray-900 mb-4">Alba Script Importer</h1>
                                        <h6>{{ $MainControllerPresenter::getIndexDescription() }}</h6>
                                    </div>

                                    <hr />
                                    @include('layout.alerts')

                                    <div class="row">
                                        <div class="col-12">
                                            {!! $MainControllerPresenter::getIndexTextForSitesToConvert() !!}
                                        </div>
                                    </div>
                                    <hr />

                                    <form class="user" method="POST" action="{{ route('site.doSendFile') }}" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="formFile" class="form-label">{{ $MainControllerPresenter::getIndexFormFileLabel() }}</label>
                                                    <input class="form-control"
                                                        type="file" id="file" name="file" required
                                                        accept="{{ $MainControllerPresenter::getIndexFileAcceptString() }}"
                                                    >
                                                    
                                                    {{-- Display specific error for the file field --}}
                                                    @error('file')
                                                        <div class="invalid-feedback">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <div class="text-right">
                                                <button class="btn btn-primary" type="submit" id="submitBtn">Enviar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection