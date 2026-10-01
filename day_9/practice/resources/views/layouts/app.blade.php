<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TodoArchitect') - Multi-Tier Todo Application</title>
    <meta name="description" content="Production-grade Todo Application built on a strict multi-tier architecture: HTTP -> Route -> Middleware -> Form Request -> Controller -> DTO -> Service -> Repository Interface -> Service Container -> Repository Implementation -> Eloquent -> Database">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">
    <!-- Ambient Background Glows -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[450px] bg-gradient-to-tr from-indigo-600/15 via-purple-600/10 to-cyan-500/10 blur-[130px] rounded-full"></div>
        <div class="absolute top-1/2 -right-40 w-[600px] h-[600px] bg-indigo-900/10 blur-[150px] rounded-full"></div>
        <div class="absolute bottom-10 -left-40 w-[600px] h-[600px] bg-purple-900/10 blur-[150px] rounded-full"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-xl transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('todos.index') }}" class="group flex items-center space-x-3 text-white">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-cyan-400 p-[1px] shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 transition-all duration-300">
                            <div class="w-full h-full bg-slate-900 rounded-[11px] flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-indigo-200 bg-clip-text text-transparent">TodoArchitect</span>
                                <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Clean Arch</span>
                            </div>
                            <p class="text-xs text-slate-400 hidden sm:block">docs.md 12-Tier Pipeline</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links & Action Buttons -->
                <nav class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('todos.index') }}"
                       class="px-3.5 py-2 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('todos.index') ? 'bg-indigo-600/15 text-indigo-300 border border-indigo-500/30 shadow-inner' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('todos.create') }}"
                       class="inline-flex items-center space-x-1.5 px-3.5 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>New Task</span>
                    </a>

                    <!-- Architecture Flow Modal Trigger -->
                    <button type="button"
                            onclick="document.getElementById('archModal').classList.remove('hidden')"
                            class="hidden md:inline-flex items-center space-x-1.5 px-3 py-2 text-xs font-medium rounded-lg text-slate-400 hover:text-indigo-300 hover:bg-slate-800/50 border border-slate-700/60 transition-all">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>Architecture Flow</span>
                    </button>

                    <!-- API JSON Trigger -->
                    <a href="{{ url('/api/todos') }}" target="_blank"
                       title="View raw JSON output from API controller"
                       class="hidden lg:inline-flex items-center space-x-1 px-2.5 py-2 text-xs font-mono text-cyan-400 hover:text-cyan-300 bg-cyan-950/40 hover:bg-cyan-900/40 border border-cyan-800/50 rounded-lg transition-colors">
                        <span>API JSON</span>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Flash Messages (Toasts) -->
    @if (session('success'))
        <div id="flashNotice" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 z-30 relative animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 shadow-lg shadow-emerald-950/50 backdrop-blur-md">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-emerald-100">Success</p>
                        <p class="text-xs text-emerald-300">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('flashNotice').remove()" class="text-emerald-400 hover:text-white p-1 rounded-lg hover:bg-emerald-900/50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div id="flashError" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 z-30 relative">
            <div class="flex items-start justify-between p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-200 shadow-lg shadow-rose-950/50 backdrop-blur-md">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-400 flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-rose-100">Validation Error</p>
                        <ul class="text-xs text-rose-300 list-disc list-inside mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('flashError').remove()" class="text-rose-400 hover:text-white p-1 rounded-lg hover:bg-rose-900/50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Main Content Container -->
    <main class="flex-grow z-10 relative py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        @yield('content')
    </main>

    <!-- Architecture Modal -->
    <div id="archModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl max-w-3xl w-full p-6 sm:p-8 border border-slate-700/80 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Full Architecture Trace (docs.md)</h3>
                        <p class="text-xs text-slate-400">Exact 12-tier pipeline implemented across the application</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('archModal').classList.add('hidden')" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Flow Visualizer -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs font-mono">
                <div class="p-3 rounded-lg bg-slate-900/80 border border-indigo-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-[10px]">1</span>
                    <div>
                        <strong class="text-indigo-300">HTTP Request</strong>
                        <p class="text-slate-400 font-sans text-[11px]">Browser or API sends GET, POST, PUT, DELETE.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-indigo-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-[10px]">2</span>
                    <div>
                        <strong class="text-indigo-300">Route</strong>
                        <p class="text-slate-400 font-sans text-[11px]">routes/web.php & routes/api.php match endpoint.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-purple-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-[10px]">3</span>
                    <div>
                        <strong class="text-purple-300">Middleware</strong>
                        <p class="text-slate-400 font-sans text-[11px]">TodoActivityMiddleware context & sanitize.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-purple-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-[10px]">4</span>
                    <div>
                        <strong class="text-purple-300">Form Request</strong>
                        <p class="text-slate-400 font-sans text-[11px]">StoreTodoRequest / UpdateTodoRequest validates payload.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-cyan-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[10px]">5</span>
                    <div>
                        <strong class="text-cyan-300">Controller</strong>
                        <p class="text-slate-400 font-sans text-[11px]">TodoController coordinates and handles responses.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-cyan-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[10px]">6</span>
                    <div>
                        <strong class="text-cyan-300">DTO</strong>
                        <p class="text-slate-400 font-sans text-[11px]">TodoDTO strongly typed transport object.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-emerald-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">7</span>
                    <div>
                        <strong class="text-emerald-300">Service</strong>
                        <p class="text-slate-400 font-sans text-[11px]">TodoService business logic & operations.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-emerald-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">8</span>
                    <div>
                        <strong class="text-emerald-300">Repository Interface</strong>
                        <p class="text-slate-400 font-sans text-[11px]">TodoRepositoryInterface contract boundary.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-amber-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-[10px]">9</span>
                    <div>
                        <strong class="text-amber-300">Service Container</strong>
                        <p class="text-slate-400 font-sans text-[11px]">AppServiceProvider binds contract to implementation.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-amber-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-[10px]">10</span>
                    <div>
                        <strong class="text-amber-300">Repository Impl</strong>
                        <p class="text-slate-400 font-sans text-[11px]">TodoRepository database queries & transactions.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-rose-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-[10px]">11</span>
                    <div>
                        <strong class="text-rose-300">Eloquent</strong>
                        <p class="text-slate-400 font-sans text-[11px]">Todo model, scopes, casts, fillable attributes.</p>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-slate-900/80 border border-rose-500/30 flex items-start space-x-2">
                    <span class="w-5 h-5 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-[10px]">12</span>
                    <div>
                        <strong class="text-rose-300">Database</strong>
                        <p class="text-slate-400 font-sans text-[11px]">SQLite database persistence layer.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" onclick="document.getElementById('archModal').classList.add('hidden')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition">
                    Close Diagram
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-900 bg-slate-950/90 py-6 text-center text-xs text-slate-500 z-10 relative">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Multi-Tier Architecture Live Demo</span>
            </div>
            <p>Built strictly following <code class="text-indigo-400 font-mono bg-slate-900 px-1.5 py-0.5 rounded border border-slate-800">docs.md</code> architecture specifications</p>
            <div class="flex items-center space-x-4">
                <a href="{{ route('todos.index') }}" class="hover:text-indigo-400 transition">Tasks</a>
                <a href="{{ route('todos.create') }}" class="hover:text-indigo-400 transition">Create</a>
                <a href="{{ url('/api/todos') }}" class="hover:text-indigo-400 transition font-mono">/api/todos</a>
            </div>
        </div>
    </footer>
</body>
</html>
