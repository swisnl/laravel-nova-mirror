<?php

namespace Laravel\Nova;

use ReflectionClass;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Nova\Events\ServingNova;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Middleware\RedirectIfAuthenticated;

class Nova
{
    use AuthorizesRequests;

    /**
     * The registered resource names.
     *
     * @var array
     */
    public static $resources = [];

    /**
     * An index of resource names keyed by the model name.
     *
     * @var array
     */
    public static $resourcesByModel = [];

    /**
     * The callback used to create new users via the CLI.
     *
     * @var \Closure
     */
    public static $createUserCallback;

    /**
     * The callback used to gather new user information via the CLI.
     *
     * @var \Closure
     */
    public static $createUserCommandCallback;

    /**
     * The callable that resolves the user's timezone.
     *
     * @var callable
     */
    public static $userTimezoneCallback;

    /**
     * Indicates if Nova is being used to reset passwords.
     *
     * @var bool
     */
    public static $resetsPasswords = false;

    /**
     * All of the registered Nova tools.
     *
     * @var array
     */
    public static $tools = [];

    /**
     * All of the registered Nova cards.
     *
     * @var array
     */
    public static $cards = [];

    /**
     * All of the registered Nova tool scripts.
     *
     * @var array
     */
    public static $scripts = [];

    /**
     * All of the registered Nova tool CSS.
     *
     * @var array
     */
    public static $styles = [];

    /**
     * The variables that should be made available on the Nova JavaScript object.
     *
     * @var array
     */
    public static $jsonVariables = [];

    /**
     * Indicates if Nova should register its migrations.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Get the current Nova version.
     *
     * @return string
     */
    public static function version()
    {
        return '1.0.13';
    }

    /**
     * Get the app name utilized by Nova.
     *
     * @return string
     */
    public static function name()
    {
        return config('nova.name', 'Nova Site');
    }

    /**
     * Get the URI path prefix utilized by Nova.
     *
     * @return string
     */
    public static function path()
    {
        return config('nova.path', '/nova');
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    public static function routes()
    {
        Route::aliasMiddleware('nova.guest', RedirectIfAuthenticated::class);

        return new PendingRouteRegistration;
    }

    /**
     * Register an event listener for the Nova "serving" event.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function serving($callback)
    {
        Event::listen(ServingNova::class, $callback);
    }

    /**
     * Get meta data information about all resources for client side consumption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function resourceInformation(Request $request)
    {
        return collect(static::$resources)->map(function ($resource) use ($request) {
            return [
                'uriKey' => $resource::uriKey(),
                'label' => $resource::label(),
                'singularLabel' => $resource::singularLabel(),
                'authorizedToCreate' => $resource::authorizedToCreate($request),
                'searchable' => $resource::searchable(),
            ];
        })->values()->all();
    }

    /**
     * Get the resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableResources(Request $request)
    {
        return collect(static::$resources)->filter(function ($resource) use ($request) {
            return $resource::authorizedToViewAny($request) &&
                   $resource::availableForNavigation($request);
        })->all();
    }

    /**
     * Get the resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function globallySearchableResources(Request $request)
    {
        return collect(static::availableResources($request))
                    ->filter(function ($resource) {
                        return $resource::$globallySearchable;
                    });
    }

    /**
     * Register the given resources.
     *
     * @param  array  $resources
     * @return static
     */
    public static function resources(array $resources)
    {
        static::$resources = array_merge(static::$resources, $resources);

        return new static;
    }

    /**
     * Register all of the resource classes in the given directory.
     *
     * @param  string  $directory
     * @return void
     */
    public static function resourcesIn($directory)
    {
        $namespace = app()->getNamespace();

        $resources = [];

        foreach ((new Finder)->in($directory)->files() as $resource) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($resource, Resource::class) &&
                ! (new ReflectionClass($resource))->isAbstract()) {
                $resources[] = $resource;
            }
        }

        static::resources(
            collect($resources)->sort()->all()
        );
    }

    /**
     * Get the resource class name for a given key.
     *
     * @param  string  $key
     * @return string
     */
    public static function resourceForKey($key)
    {
        return collect(static::$resources)->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }

    /**
     * Get a new resource instance with the given model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Laravel\Nova\Resource
     */
    public static function newResourceFromModel($model)
    {
        $resource = static::resourceForModel($model);

        return new $resource($model);
    }

    /**
     * Get the resource class name for a given model class.
     *
     * @param  object|string  $class
     * @return string
     */
    public static function resourceForModel($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (isset(static::$resourcesByModel[$class])) {
            return static::$resourcesByModel[$class];
        }

        $resource = collect(static::$resources)->first(function ($value) use ($class) {
            return $value::$model === $class;
        });

        return static::$resourcesByModel[$class] = $resource;
    }

    /**
     * Get a resource instance for a given key.
     *
     * @param  string  $key
     * @return \Laravel\Nova\Resource|null
     */
    public static function resourceInstanceForKey($key)
    {
        if ($resource = static::resourceForKey($key)) {
            return new $resource($resource::newModel());
        }
    }

    /**
     * Get a fresh model instance for the resource with the given key.
     *
     * @param  string  $key
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function modelInstanceForKey($key)
    {
        $resource = static::resourceForKey($key);

        return $resource ? $resource::newModel() : null;
    }

    /**
     * Get the available dashboard cards for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public static function availableDashboardCards(NovaRequest $request)
    {
        return collect(static::$cards)->filter->authorize($request)->values();
    }

    /**
     * Create a new user instance.
     *
     * @param  \Illuminate\Console\Command
     * @return mixed
     */
    public static function createUser($command)
    {
        if (! static::$createUserCallback) {
            static::createUserUsing();
        }

        return call_user_func(
            static::$createUserCallback,
            ...call_user_func(static::$createUserCommandCallback, $command)
        );
    }

    /**
     * Register the callbacks used to create a new user via the CLI.
     *
     * @param  \Closure  $createUserCommandCallback
     * @param  \Closure  $createUserCallback
     * @return static
     */
    public static function createUserUsing($createUserCommandCallback = null, $createUserCallback = null)
    {
        if (! $createUserCallback) {
            $createUserCallback = $createUserCommandCallback;
            $createUserCommandCallback = null;
        }

        static::$createUserCommandCallback = $createUserCommandCallback ??
                  static::defaultCreateUserCommandCallback();

        static::$createUserCallback = $createUserCallback ??
                  static::defaultCreateUserCallback();

        return new static;
    }

    /**
     * Get the default callback used for the create user command.
     *
     * @return \Closure
     */
    protected static function defaultCreateUserCommandCallback()
    {
        return function ($command) {
            return [
                $command->ask('Name'),
                $command->ask('Username / Email Address'),
                $command->secret('Password'),
            ];
        };
    }

    /**
     * Get the default callback used for creating new Nova users.
     *
     * @return \Closure
     */
    protected static function defaultCreateUserCallback()
    {
        return function ($name, $email, $password) {
            $model = config('auth.providers.users.model');

            return tap((new $model)->forceFill([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]))->save();
        };
    }

    /**
     * Set the callable that resolves the user's preferred timezone.
     *
     * @param  callable  $userTimezoneCallback
     * @return static
     */
    public static function userTimezone($userTimezoneCallback)
    {
        static::$userTimezoneCallback = $userTimezoneCallback;

        return new static;
    }

    /**
     * Resolve the user's preferred timezone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public static function resolveUserTimezone(Request $request)
    {
        if (static::$userTimezoneCallback) {
            return call_user_func(static::$userTimezoneCallback, $request);
        }

        return null;
    }

    /**
     * Register new tools with Nova.
     *
     * @param  array  $tools
     * @return static
     */
    public static function tools(array $tools)
    {
        static::$tools = array_merge(
            static::$tools,
            $tools
        );

        return new static;
    }

    /**
     * Get the tools registered with Nova.
     *
     * @return array
     */
    public static function registeredTools()
    {
        return static::$tools;
    }

    /**
     * Boot the available Nova tools.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public static function bootTools(Request $request)
    {
        collect(static::availableTools($request))->each->boot();
    }

    /**
     * Get the tools registered with Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableTools(Request $request)
    {
        return collect(static::$tools)->filter->authorize($request)->all();
    }

    /**
     * Register new dashboard cards with Nova.
     *
     * @param  array  $cards
     * @return static
     */
    public static function cards(array $cards)
    {
        static::$cards = array_merge(
            static::$cards,
            $cards
        );

        return new static;
    }

    /**
     * Get the cards registered with Nova.
     *
     * @return array
     */
    public static function registeredCards()
    {
        return static::$cards;
    }

    /**
     * Get the cards registered with Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableCards(Request $request)
    {
        return collect(static::$cards)->filter->authorize($request)->all();
    }

    /**
     * Get all of the additional scripts that should be registered.
     *
     * @return array
     */
    public static function allScripts()
    {
        return static::$scripts;
    }

    /**
     * Get all of the available scripts that should be registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableScripts(Request $request)
    {
        return static::$scripts;
    }

    /**
     * Get all of the additional stylesheets that should be registered.
     *
     * @return array
     */
    public static function allStyles()
    {
        return static::$styles;
    }

    /**
     * Get all of the available stylesheets that should be registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function availableStyles(Request $request)
    {
        return static::$styles;
    }

    /**
     * Register the given script file with Nova.
     *
     * @param  string  $name
     * @param  string  $path
     * @return static
     */
    public static function script($name, $path)
    {
        static::$scripts[$name] = $path;

        return new static;
    }

    /**
     * Register the given remote script file with Nova.
     *
     * @param  string  $path
     * @return static
     */
    public static function remoteScript($path)
    {
        static::$scripts[md5($path)] = $path;

        return new static;
    }

    /**
     * Register the given CSS file with Nova.
     *
     * @param  string  $name
     * @param  string  $path
     * @return static
     */
    public static function style($name, $path)
    {
        static::$styles[$name] = $path;

        return new static;
    }

    /**
     * Get the JSON variables that should be provided to the global Nova JavaScript object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function jsonVariables(Request $request)
    {
        return collect(static::$jsonVariables)->map(function ($variable) use ($request) {
            return is_callable($variable) ? $variable($request) : $variable;
        })->all();
    }

    /**
     * Provide additional variables to the global Nova JavaScript object.
     *
     * @param  array  $variables
     * @return static
     */
    public static function provideToScript(array $variables)
    {
        if (empty(static::$jsonVariables)) {
            static::$jsonVariables = [
                'base' => static::path(),
                'userId' => Auth::id() ?? null,
            ];
        }

        static::$jsonVariables = array_merge(static::$jsonVariables, $variables);

        return new static;
    }

    /**
     * Configure Nova to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Humanize the given value into a proper name.
     *
     * @param  string  $value
     * @return string
     */
    public static function humanize($value)
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        } else {
            return Str::title(Str::snake($value, ' '));
        }
    }

    /**
     * Dynamically proxy static method calls.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     */
    public static function __callStatic($method, $parameters)
    {
        if (! property_exists(get_called_class(), $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return static::${$method};
    }
}
