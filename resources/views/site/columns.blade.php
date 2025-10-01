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
                                        <h1 class="h4 text-gray-900 mb-4">Mapear colunas</h1>
                                    </div>

                                    <hr />
                                    @include('layout.alerts')

                                    <div>
                                        {{ $MainControllerPresenter::getColumnsDescription() }}
                                    </div>
                                    <hr />

                                    <form class="user" method="POST" action="{{ route('site.mapAndDownloadFile') }}">
                                        @csrf

                                        <div class="form-row">
                                            <div class="col-12">
                                                @foreach ($fileHandler->getAlbaColumnsForMapping() as $column)
                                                    <div class="form-group">
                                                        <label for="column_{{ $column }}">{{ $column }}</label>
                                                        {!! $fileHandler->getCsvColumnsAsHtmlSelect("columns[{$column}]", "column_{$column}", [], 'form-control', 'Selecione ...') !!}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <div class="text-right">
                                                <a href="{{ url()->previous() }}" class="btn btn-light">Voltar</a>
                                                <button class="btn btn-primary" type="submit" id="submitBtn">Gerar Arquivo Alba</button>
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