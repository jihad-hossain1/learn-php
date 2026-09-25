@extends('layouts.app')

@section('title', 'Task: ' . $todo->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-slate-400">
        <a href="{{ route('todos.index') }}" class="hover:text-indigo-400 transition">Dashboard</a>
        <span>/</span>
        <span class="text-slate-200">Task #{{ $todo->id }}</span>
    </div>

    <!-- Task Card -->
    <div class="glass-card rounded-2xl p-6 sm:p-8 border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 border-b border-slate-800/80 pb-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <!-- Priority Badge -->
                    @if ($todo->priority === 'high')
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            High Priority
                        </span>
                    @elseif ($todo->priority === 'medium')
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Medium Priority
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Low Priority
                        </span>
                    @endif

                    <!-- Status Badge -->
                    @if ($todo->is_completed)
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Completed
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/20 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            In Progress
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight {{ $todo->is_completed ? 'line-through text-slate-400' : '' }}">
                    {{ $todo->title }}
                </h1>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center space-x-2 flex-shrink-0">
                <!-- Toggle -->
                <form method="POST" action="{{ route('todos.toggle', $todo->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-3.5 py-2 rounded-xl text-xs font-medium border transition-all {{ $todo->is_completed ? 'bg-slate-900 border-slate-700 text-slate-300 hover:text-white' : 'bg-emerald-950/40 border-emerald-500/40 text-emerald-300 hover:bg-emerald-900/40' }}">
                        {{ $todo->is_completed ? 'Mark as Incomplete' : 'Mark Completed' }}
                    </button>
                </form>

                <!-- Edit -->
                <a href="{{ route('todos.edit', $todo->id) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-medium bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition">
                    Edit
                </a>

                <!-- Delete -->
                <form method="POST" action="{{ route('todos.destroy', $todo->id) }}" onsubmit="return confirm('Delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 border border-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Description Body -->
        <div class="py-6 border-b border-slate-800/80">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Description</h3>
            @if ($todo->description)
                <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">{{ $todo->description }}</p>
            @else
                <p class="text-sm text-slate-500 italic">No description provided for this task.</p>
            @endif
        </div>

        <!-- Metadata Grid -->
        <div class="py-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-slate-500">Target Due Date</p>
                <p class="font-semibold text-slate-200 mt-1">
                    {{ $todo->due_date ? $todo->due_date->format('F d, Y') : 'None specified' }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Created At</p>
                <p class="font-semibold text-slate-200 mt-1">
                    {{ $todo->created_at->format('M d, Y · H:i') }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Last Modified</p>
                <p class="font-semibold text-slate-200 mt-1">
                    {{ $todo->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>

        <!-- Live Architecture Inspection Box -->
        <div class="mt-4 p-4 rounded-xl bg-slate-900/90 border border-indigo-500/20 text-xs">
            <div class="flex items-center space-x-2 text-indigo-400 font-semibold mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Architecture Execution Pipeline</span>
            </div>
            <p class="text-slate-400 leading-relaxed">
                Retrieved via <code class="text-indigo-300 font-mono">Route::get('/todos/{id}')</code> →
                <code class="text-indigo-300 font-mono">TodoActivityMiddleware</code> →
                <code class="text-indigo-300 font-mono">TodoController::show()</code> →
                <code class="text-indigo-300 font-mono">TodoService::getTodo()</code> →
                <code class="text-indigo-300 font-mono">TodoRepositoryInterface (bound to TodoRepository)</code> →
                <code class="text-indigo-300 font-mono">Todo::findOrFail()</code> →
                <code class="text-indigo-300 font-mono">SQLite DB</code>.
            </p>
        </div>
    </div>
</div>
@endsection
