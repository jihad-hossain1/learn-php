@extends('layouts.app')

@section('title', 'Tasks Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>Task Dashboard</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    {{ $statistics['total'] }} {{ Str::plural('Task', $statistics['total']) }}
                </span>
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Full-stack Todo application orchestrated through clean architecture layers.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('todos.create') }}"
               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-600 text-white font-medium text-sm shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create New Task</span>
            </a>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tasks -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Tasks</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $statistics['total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 text-xs text-slate-400">
                Managed through <code class="text-indigo-300 font-mono">TodoRepository</code>
            </div>
        </div>

        <!-- In Progress -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-amber-400 uppercase tracking-wider">Pending</p>
                    <p class="text-2xl font-bold text-amber-300 mt-1">{{ $statistics['pending'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 text-xs text-slate-400">
                Actionable backlog items
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-400 uppercase tracking-wider">Completed</p>
                    <p class="text-2xl font-bold text-emerald-300 mt-1">{{ $statistics['completed'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/60 text-xs text-slate-400">
                Successfully processed
            </div>
        </div>

        <!-- Completion Rate Progress -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-cyan-400 uppercase tracking-wider">Completion Rate</p>
                    <p class="text-2xl font-bold text-cyan-300 mt-1">{{ $statistics['completion_rate'] }}%</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="w-full bg-slate-800 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-500 to-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $statistics['completion_rate'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="glass-card rounded-2xl p-4 border border-slate-800/80 shadow-xl">
        <form method="GET" action="{{ route('todos.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Status Tabs -->
            <div class="flex items-center space-x-1 bg-slate-900/80 p-1 rounded-xl border border-slate-800">
                <a href="{{ route('todos.index', array_merge($filters, ['status' => null])) }}"
                   class="px-3.5 py-1.5 text-xs font-medium rounded-lg transition-all {{ empty($filters['status']) ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                    All ({{ $statistics['total'] }})
                </a>
                <a href="{{ route('todos.index', array_merge($filters, ['status' => 'active'])) }}"
                   class="px-3.5 py-1.5 text-xs font-medium rounded-lg transition-all {{ ($filters['status'] ?? '') === 'active' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                    Active ({{ $statistics['pending'] }})
                </a>
                <a href="{{ route('todos.index', array_merge($filters, ['status' => 'completed'])) }}"
                   class="px-3.5 py-1.5 text-xs font-medium rounded-lg transition-all {{ ($filters['status'] ?? '') === 'completed' ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">
                    Completed ({{ $statistics['completed'] }})
                </a>
            </div>

            <!-- Priority & Search Controls -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                <!-- Priority Selector -->
                <select name="priority" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                    <option value="">All Priorities</option>
                    <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High Priority</option>
                    <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                    <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low Priority</option>
                </select>

                <!-- Search Input -->
                <div class="relative flex-grow sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Search tasks..."
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    @if (! empty($filters['search']))
                        <a href="{{ route('todos.index', array_merge($filters, ['search' => null])) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>

                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium rounded-xl border border-slate-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Todo List Grid -->
    @if ($todos->isEmpty())
        <div class="glass-card rounded-2xl p-12 text-center border border-slate-800/80 shadow-xl max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white">No tasks found</h3>
            <p class="text-slate-400 text-sm mt-1 max-w-sm mx-auto">
                @if (! empty($filters['search']) || ! empty($filters['status']) || ! empty($filters['priority']))
                    No tasks match your active filters. Try clearing your search parameters.
                @else
                    Get started by creating your first task through the multi-tier architecture pipeline.
                @endif
            </p>
            <div class="mt-6">
                @if (! empty($filters['search']) || ! empty($filters['status']) || ! empty($filters['priority']))
                    <a href="{{ route('todos.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium transition">
                        Clear All Filters
                    </a>
                @else
                    <a href="{{ route('todos.create') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Create First Task</span>
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($todos as $todo)
                <div class="glass-card glass-card-hover rounded-2xl p-4 sm:p-5 border border-slate-800/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all">
                    <!-- Left: Checkbox + Title + Description -->
                    <div class="flex items-start space-x-4 flex-grow min-w-0">
                        <!-- Toggle Form -->
                        <form method="POST" action="{{ route('todos.toggle', $todo->id) }}" class="mt-0.5 flex-shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    title="{{ $todo->is_completed ? 'Mark as incomplete' : 'Mark as complete' }}"
                                    class="w-6 h-6 rounded-lg border flex items-center justify-center transition-all duration-200 {{ $todo->is_completed ? 'bg-emerald-500 border-emerald-400 text-white shadow-md shadow-emerald-500/30' : 'border-slate-600 hover:border-indigo-400 bg-slate-900/60' }}">
                                @if ($todo->is_completed)
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <!-- Content -->
                        <div class="min-w-0 flex-grow">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('todos.show', $todo->id) }}"
                                   class="font-semibold text-base transition-colors hover:text-indigo-400 {{ $todo->is_completed ? 'line-through text-slate-400' : 'text-white' }}">
                                    {{ $todo->title }}
                                </a>

                                <!-- Priority Tag -->
                                @if ($todo->priority === 'high')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        High
                                    </span>
                                @elseif ($todo->priority === 'medium')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Medium
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Low
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if ($todo->is_completed)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Done
                                    </span>
                                @endif
                            </div>

                            @if ($todo->description)
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                                    {{ $todo->description }}
                                </p>
                            @endif

                            <div class="flex items-center gap-4 mt-2 text-[11px] text-slate-500">
                                @if ($todo->due_date)
                                    <span class="inline-flex items-center gap-1 text-slate-400">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Due: {{ $todo->due_date->format('M d, Y') }}
                                    </span>
                                @endif
                                <span>Created {{ $todo->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center space-x-2 sm:self-center ml-auto sm:ml-0 flex-shrink-0">
                        <a href="{{ route('todos.show', $todo->id) }}"
                           title="View details"
                           class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <a href="{{ route('todos.edit', $todo->id) }}"
                           title="Edit task"
                           class="p-2 rounded-lg text-slate-400 hover:text-indigo-400 hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <form method="POST" action="{{ route('todos.destroy', $todo->id) }}" onsubmit="return confirm('Are you sure you want to remove this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    title="Delete task"
                                    class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pipeline Architecture Showcase Banner -->
    <div class="glass-card rounded-2xl p-6 border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Architecture Flow (docs.md)</h3>
            </div>
            <span class="text-xs text-slate-400 font-mono">12 Distinct Stages</span>
        </div>

        <div class="overflow-x-auto pb-2">
            <div class="flex items-center space-x-2 min-w-max text-[11px] font-mono">
                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">1. HTTP</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">2. Route</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/30">3. Middleware</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/30">4. Form Request</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">5. Controller</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">6. DTO</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">7. Service</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">8. Repo Interface</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30">9. Container</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30">10. Repo Impl</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30">11. Eloquent</span>
                <span class="text-slate-600">→</span>
                <span class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30">12. Database</span>
            </div>
        </div>
    </div>
</div>
@endsection