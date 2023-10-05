@section('header')
    <p></p>
    <header class="text-body-secondary text-center default_cursor_cs">
        <nav>
            <ul class="list-inline">
                <li class="list-inline-item"><a class="btn btn-primary" href="{{ route('menu') }}">Main</a></li>
                <li class="list-inline-item"><a class="btn btn-primary" href="{{ route('getCargosApi') }}">Get Cargos API</a></li>
                <li class="list-inline-item"><a class="btn btn-primary" href="{{ route('cargosUpdater') }}">Cargos Updater</a></li>
            </ul>
        </nav>
    </header>
