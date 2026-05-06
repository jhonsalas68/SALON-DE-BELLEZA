@extends('layouts.app')

@section('title', 'Bitácora de Auditoría - Salón de Belleza Anita')

@section('header')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Registro de Auditoría</h1>
        <p class="text-gray-500 font-medium">Historial completo de acciones y seguridad.</p>
    </div>
    <div class="text-right">
        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total: {{ $logs->total() }} registros</span>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('activity_logs.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex-1 w-full">
            <select name="user_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 font-medium">
                <option value="">Todos los usuarios</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->email }} ({{ $user->role->name ?? 'Sin rol' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all">
                Filtrar
            </button>
            @if(request('user_id'))
                <a href="{{ route('activity_logs.index') }}" class="px-6 py-3 rounded-2xl font-bold text-gray-500 hover:bg-gray-100 transition-colors">Limpiar</a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Actor</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Operación</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Detalles de la Acción</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Origen</th>
                    <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-widest">Tiempo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($logs as $log)
                <tr class="hover:bg-indigo-50/30 transition duration-150 group">
                    <td class="px-8 py-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                {{ substr($log->user->email ?? 'S', 0, 1) }}
                            </div>
                            <span class="text-sm font-bold text-gray-800">{{ $log->user->email ?? 'Sistema' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm
                            @if($log->action == 'CREATE' || $log->action == 'CREATE_ROLE') bg-emerald-100 text-emerald-700
                            @elseif($log->action == 'UPDATE' || $log->action == 'UPDATE_ROLE') bg-amber-100 text-amber-700
                            @elseif($log->action == 'DELETE' || $log->action == 'DELETE_ROLE') bg-rose-100 text-rose-700
                            @elseif($log->action == 'LOGIN') bg-indigo-100 text-indigo-700
                            @elseif($log->action == 'LOGOUT') bg-slate-100 text-slate-700
                            @else bg-gray-100 text-gray-600 @endif">
                            <i class="fas
                                @if($log->action == 'CREATE' || $log->action == 'CREATE_ROLE') fa-plus-circle
                                @elseif($log->action == 'UPDATE' || $log->action == 'UPDATE_ROLE') fa-edit
                                @elseif($log->action == 'DELETE' || $log->action == 'DELETE_ROLE') fa-trash
                                @elseif($log->action == 'LOGIN') fa-sign-in-alt
                                @elseif($log->action == 'LOGOUT') fa-sign-out-alt
                                @else fa-info-circle @endif mr-1"></i>
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-sm text-gray-800 font-bold mb-1">{{ $log->description }}</p>
                        @if($log->details)
                            <div class="mt-2">
                                <details class="group">
                                    <summary class="text-[9px] font-black text-indigo-400 uppercase cursor-pointer hover:text-indigo-600 transition list-none">
                                        <i class="fas fa-chevron-right mr-1 group-open:rotate-90 transition-transform"></i> Ver datos específicos
                                    </summary>
                                    <div class="mt-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <pre class="text-[10px] text-gray-500 font-mono overflow-x-auto">@json($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                                    </div>
                                </details>
                            </div>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-[10px] font-bold text-gray-400 uppercase leading-relaxed">
                            <span class="block"><i class="fas fa-network-wired mr-1 text-indigo-300"></i>{{ $log->ip_address }}</span>
                            <span class="block truncate w-32"><i class="fas fa-desktop mr-1 text-indigo-300"></i>{{ $log->user_agent }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-xs text-gray-500 font-bold whitespace-nowrap bg-gray-50 px-3 py-2 rounded-lg border border-gray-100 inline-block">
                            <i class="far fa-calendar-alt mr-1 text-indigo-400"></i> {{ $log->created_at->format('d/m/Y') }}
                            <span class="mx-1 text-gray-300">|</span>
                            <i class="far fa-clock mr-1 text-indigo-400"></i> {{ $log->created_at->format('h:i A') }}
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-8 py-6 border-t border-gray-50">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
