@extends('layouts.app')

@section('title', 'Create New Task')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb / Back Navigation -->
    <div class="flex items-center space-x-2 text-xs text-slate-400">
        <a href="{{ route('todos.index') }}" class="hover:text-indigo-400 transition">Dashboard</a>
        <span>/</span>
        <span class="text-slate-200">Create Task</span>
    </div>

    <div class="glass-card rounded-2xl p-6 sm:p-8 border border-slate-800/80 shadow-2xl relative overflow-hidden">
        <!-- Header -->
        <div class="border-b border-slate-800/80 pb-5 mb-6">
            <h1 class="text-2xl font-bold text-white tracking-tight">Create New Task</h1>
            <p class="text-slate-400 text-xs mt-1">
                Submitting this form executes the pipeline: <code class="text-indigo-300 font-mono">StoreTodoRequest → TodoController → TodoDTO → TodoService → TodoRepository → Eloquent</code>
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('todos.store') }}" class="space-y-6">
            @csrf

            <!-- Task Title -->
            <div class="space-y-1.5">
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Task Title <span class="text-rose-400">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       autofocus
                       placeholder="e.g. Implement repository pattern in Laravel"
                       class="w-full bg-slate-900/90 border {{ $errors->has('title') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition shadow-inner">
                @error('title')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Task Description -->
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Description <span class="text-slate-500 font-normal lowercase">(optional)</span>
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          placeholder="Provide additional context or requirements..."
                          class="w-full bg-slate-900/90 border {{ $errors->has('description') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition shadow-inner">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Priority Selection -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Priority Level <span class="text-rose-400">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Low Priority -->
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="low" class="peer sr-only" {{ old('priority', 'low') === 'low' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-slate-800 bg-slate-900/60 peer-checked:border-emerald-500 peer-checked:bg-emerald-950/20 peer-checked:text-emerald-300 transition-all flex items-center space-x-3 text-slate-400">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold">Low Priority</span>
                        </div>
                    </label>

                    <!-- Medium Priority -->
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="medium" class="peer sr-only" {{ old('priority', 'medium') === 'medium' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-slate-800 bg-slate-900/60 peer-checked:border-amber-500 peer-checked:bg-amber-950/20 peer-checked:text-amber-300 transition-all flex items-center space-x-3 text-slate-400">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-semibold">Medium Priority</span>
                        </div>
                    </label>

                    <!-- High Priority -->
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="high" class="peer sr-only" {{ old('priority') === 'high' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-slate-800 bg-slate-900/60 peer-checked:border-rose-500 peer-checked:bg-rose-950/20 peer-checked:text-rose-300 transition-all flex items-center space-x-3 text-slate-400">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="text-xs font-semibold">High Priority</span>
                        </div>
                    </label>
                </div>
                @error('priority')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="space-y-1.5">
                <label for="due_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Target Completion Date <span class="text-slate-500 font-normal lowercase">(optional)</span>
                </label>
                <input type="date"
                       id="due_date"
                       name="due_date"
                       value="{{ old('due_date') }}"
                       class="w-full sm:w-64 bg-slate-900/90 border {{ $errors->has('due_date') ? 'border-rose-500' : 'border-slate-800' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition shadow-inner">
                @error('due_date')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end space-x-3">
                <a href="{{ route('todos.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-medium transition">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center space-x-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-medium text-xs shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Save Task</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
