@extends('layouts.layout')

@section('title', 'Получить грузы по API')

@section('content')
    <div class="text-center">
            <div id="form_block">
                @if(isset($_GET['requestType']))
                    <h1 style="color: whitesmoke; margin-left: 500px; margin-top: 50px; margin-bottom: 50px">Выберите действие с API:</h1>
                    <form id="request-type-form" action="{{route('initialDataShower')}}">
                    @csrf
                    <div>
                        <p></p>
                        <span>Выберите API для запроса:</span>
                        <div class="apiSettings">
                            <p></p>
                            <span class="static-text">https://api.cargo.tech/v1/</span>
                            <input id="api" name="api" value="cargos">
                            <p></p>
                        @if($_GET['requestType'] === 'all')
                                <button class="btn btn-primary" type="submit">Запросить все грузы</button>
                            @endif
                        </div>
                    </div>

                    @if($_GET['requestType'] === 'byId')
                        <p></p>
                        <div class="requestByIdSettings">
                            <p>Выберите ID для запроса:</p>
                            <input id="itemId" name="itemId" class="masked-input input" value="0">
                            <p></p>
                            <button class="btn btn-primary" type="submit">Получить груз по ID</button>
                        </div>
                    @endif
                    @if($_GET['requestType'] === 'byPageCount')
                        <p></p>
                        <div class="requestByPageCountSettings">
                        <p>Выберите сколько первых страниц запросить:</p>
                        <input id="pageCount" name="pageCount" value="5">
                        <p></p>
                        <button class="btn btn-primary" type="submit">Получить первые страницы</button>
                   </div>

                    @endif
                </form>
                @else
                    <h1 style="color: whitesmoke; margin-left: 500px; margin-top: 50px; margin-bottom: 50px">Выберите действие с API:</h1>
                    <form action="{{route('chooseRequestType')}}" method="post">
                        @csrf
                        <select id="requestTypeSelector" name="requestTypeSelector" class="form-control on-trigger-tooltip default_cursor_cs">
                            <option value="all">Получить все грузы</option>
                            <option value="byId">Получить груз по ID</option>
                            <option value="byPageCount">Получить первые страницы грузов</option>
                        </select>
                        <p></p>
                        <button class="btn btn-primary" type="submit">Выбрать</button>
                    </form>
                @endif
            </div>
            <div id="load" class="load"></div>
    </div>

    <script>
        const inputElement = document.getElementById('api');
        const loadElement = document.getElementById('load');
        loadElement.style.display = 'none';

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('request-type-form');
            const contentElement = document.getElementById('form_block');

            form.addEventListener('submit', function(event) {
                console.log('a')
                contentElement.style.display = 'none';
                loadElement.style.display = 'block';
            });
        });

        inputElement.addEventListener('input', function() {
            this.style.width = (this.value.length - 0.5) + 'ch';
        });
    </script>
    </div>
@endsection
