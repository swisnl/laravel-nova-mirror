<!DOCTYPE html>
<html lang="en" class="h-full font-sans antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1280">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel Nova</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('/nova/app.css') }}">

    <!-- Tool Styles -->
    @foreach(Nova::availableStyles(request()) as $name => $path)
        <link rel="stylesheet" href="/nova-api/styles/{{ $name }}">
    @endforeach
</head>
<body class="min-w-site bg-40 text-black min-h-full">
    <div id="nova">
        <div v-cloak class="flex min-h-screen">
            <!-- Sidebar -->
            <div class="min-h-screen flex-none pt-header min-h-screen w-sidebar bg-grad-sidebar px-6">
                <div class="absolute pin-t pin-l pin-r bg-90 flex items-center w-sidebar h-header px-6 text-white">
                    @include('nova::partials.logo')
                </div>

                @foreach (Nova::availableTools(request()) as $tool)
                    {!! $tool->renderNavigation() !!}
                @endforeach
            </div>

            <!-- Content -->
            <div class="content">
                <div class="flex items-center relative shadow h-header bg-white z-50 px-6">

                    @if (! empty(Config::get('nova.name')))
                        <div class="font-bold text-90 mr-6">
                            {{ Config::get('nova.name') }}
                        </div>
                    @endif

                    @if (count(Nova::globallySearchableResources(request())) > 0)
                        <global-search></global-search>
                    @endif

                    <div class="ml-auto text-80">
                        <dropdown width="200" direction="rtl" active-class="" class="h-9 flex items-center" style="right: 20px">
                            <div slot="default" class="flex items-center" v-pre>
                                <img src="https://secure.gravatar.com/avatar/{{ md5(auth()->user()->email) }}?size=512" class="rounded-full w-8 h-8 mr-3"/>

                                <span class="text-90">
                                    {{ auth()->user()->name }}
                                </span>
                            </div>

                            <div slot="menu">
                                <ul class="list-reset">
                                    <li>
                                        <a href="{{ Laravel\Nova\Nova::path() }}/logout" class="block no-underline text-90 hover:bg-30 p-3">
                                            {{ __('Logout') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </dropdown>
                    </div>
                </div>

                <div data-testid="content" class="px-view py-view mx-auto">
                    @yield('content')

                    <p class="mt-8 text-center text-xs text-80">
                        <a href="http://nova.laravel.com" class="text-primary dim no-underline">Laravel Nova</a>
                        <span class="px-1">&middot;</span>
                        &copy; {{ date('Y') }} Laravel LLC - By Taylor Otwell, David Hemphill, and Steve Schoger.
                        <span class="px-1">&middot;</span>
                        v{{ Laravel\Nova\Nova::version() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.config = JSON.parse('@json(Nova::jsonVariables(request()))');
    </script>

    <!-- Scripts -->
    <script src="/nova/manifest.js"></script>
    <script src="/nova/vendor.js"></script>
    <script src="/nova/app.js"></script>

    <!-- Build Nova Instance -->
    <script>
        const Nova = new CreateNova(config)
    </script>

    <!-- Tool Scripts -->
    @foreach (Nova::availableScripts(request()) as $name => $path)
        @if (starts_with($path, ['http://', 'https://']))
            <script src="{!! $path !!}"></script>
        @else
            <script src="/nova-api/scripts/{{ $name }}"></script>
        @endif
    @endforeach

    <!-- Start Nova -->
    <script>
        Nova.liftOff()
    </script>
</body>
</html>
