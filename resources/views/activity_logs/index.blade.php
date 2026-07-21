@extends('layouts.app')

@section('title', 'Bitácora de Auditoría - Salón de Belleza Anita')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Registro de Auditoría</h1>
        <p class="text-gray-500 font-medium">Historial completo de acciones y seguridad.</p>
    </div>
    <div class="flex items-center gap-3">
        <details class="relative inline-block text-left group">
            <summary class="bg-stone-800 hover:bg-stone-900 text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition-all flex items-center gap-2 text-xs cursor-pointer list-none select-none">
                <i class="fas fa-file-export text-amber-400"></i>
                <span>Exportar Bitácora</span>
                <i class="fas fa-chevron-down text-xs ml-1 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 p-1">
                <a href="{{ route('reports.export', ['modulo' => 'activity-logs', 'format' => 'excel']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50 rounded-xl transition-colors">
                    <i class="fas fa-file-excel text-emerald-500 text-sm"></i>
                    <span>Exportar Excel (.csv)</span>
                </a>
                <a href="{{ route('reports.export', ['modulo' => 'activity-logs', 'format' => 'pdf']) }}" class="flex items-center space-x-2 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50 rounded-xl transition-colors">
                    <i class="fas fa-file-pdf text-rose-500 text-sm"></i>
                    <span>Reporte PDF / Imprimir</span>
                </a>
            </div>
        </details>
        <div class="text-right">
            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total: {{ $logs->total() }} registros</span>
        </div>
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
                            @php
                                $detailsArray = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                                $ignoredKeys = ['stripe_session_id', 'password', 'remember_token', 'token', 'api_token', 'password_confirmation'];
                            @endphp
                            @if(is_array($detailsArray) && !empty($detailsArray))
                                <div class="mt-3">
                                    <details class="group bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm transition-all duration-300">
                                        <summary class="flex justify-between items-center p-2.5 cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors list-none">
                                            <span class="text-[10px] font-bold text-indigo-600 flex items-center gap-2">
                                                <i class="fas fa-list-ul"></i> Ver datos específicos
                                            </span>
                                            <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform duration-300 text-[10px]"></i>
                                        </summary>
                                        
                                        <div class="p-3 bg-white border-t border-gray-100">
                                            @if(isset($detailsArray['old']) && isset($detailsArray['new']))
                                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
                                                    <div>
                                                        <h4 class="text-[9px] font-black text-rose-500 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                                            <i class="fas fa-minus-circle"></i> Antes
                                                        </h4>
                                                        <div class="space-y-1">
                                                            @foreach($detailsArray['old'] as $key => $value)
                                                                @if(!in_array($key, $ignoredKeys) && !is_array($value) && (!isset($detailsArray['new'][$key]) || $detailsArray['new'][$key] != $value))
                                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-rose-50/50 p-1.5 rounded-md border border-rose-100">
                                                                        <span class="text-[8px] font-bold text-rose-400 uppercase truncate pr-2">{{ str_replace('_', ' ', $key) }}</span>
                                                                        <span class="text-[10px] font-semibold text-rose-700 break-words text-left sm:text-right">{{ $value === null ? 'Nulo' : (is_bool($value) ? ($value ? 'Sí' : 'No') : $value) }}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                                            <i class="fas fa-plus-circle"></i> Después
                                                        </h4>
                                                        <div class="space-y-1">
                                                            @foreach($detailsArray['new'] as $key => $value)
                                                                @if(!in_array($key, $ignoredKeys) && !is_array($value) && (!isset($detailsArray['old'][$key]) || $detailsArray['old'][$key] != $value))
                                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-emerald-50/50 p-1.5 rounded-md border border-emerald-100">
                                                                        <span class="text-[8px] font-bold text-emerald-400 uppercase truncate pr-2">{{ str_replace('_', ' ', $key) }}</span>
                                                                        <span class="text-[10px] font-semibold text-emerald-700 break-words text-left sm:text-right">{{ $value === null ? 'Nulo' : (is_bool($value) ? ($value ? 'Sí' : 'No') : $value) }}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                    @foreach($detailsArray as $key => $value)
                                                        @if(!in_array($key, $ignoredKeys) && !is_array($value) && !is_object($value))
                                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50 p-1.5 rounded-md border border-gray-100">
                                                                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider mb-0.5 sm:mb-0 truncate pr-2">{{ str_replace('_', ' ', $key) }}</span>
                                                                <span class="text-[10px] font-semibold text-gray-800 break-words text-left sm:text-right">{{ $value === null ? 'Nulo' : (is_bool($value) ? ($value ? 'Sí' : 'No') : $value) }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @foreach($detailsArray as $key => $value)
                                                    @if(!in_array($key, $ignoredKeys) && (is_array($value) || is_object($value)))
                                                        @php
                                                            $nestedArray = is_object($value) ? (array) $value : $value;
                                                        @endphp
                                                        <div class="mt-3 border-t border-gray-150 pt-3">
                                                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-wider mb-2 block">{{ str_replace('_', ' ', $key) }}</span>
                                                            
                                                            @if(!empty($nestedArray))
                                                                @if(!\Illuminate\Support\Arr::isAssoc($nestedArray))
                                                                    <div class="overflow-x-auto border border-gray-150 rounded-xl shadow-sm">
                                                                        <table class="w-full text-left text-[10px]">
                                                                            <thead>
                                                                                <tr class="bg-gray-50 border-b border-gray-150">
                                                                                    @php
                                                                                        $firstItem = is_object($nestedArray[0]) ? (array) $nestedArray[0] : $nestedArray[0];
                                                                                        $validKeys = array_filter(array_keys($firstItem), function($k) {
                                                                                            return !in_array($k, ['id', 'venta_id', 'cita_id', 'created_at', 'updated_at', 'deleted_at']);
                                                                                        });
                                                                                    @endphp
                                                                                    @foreach($validKeys as $itemKey)
                                                                                        <th class="px-3 py-2 font-bold text-gray-500 uppercase tracking-wider">
                                                                                            {{ str_replace('_', ' ', $itemKey) }}
                                                                                        </th>
                                                                                    @endforeach
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody class="divide-y divide-gray-100">
                                                                                @foreach($nestedArray as $item)
                                                                                    @php
                                                                                        $itemArray = is_object($item) ? (array) $item : $item;
                                                                                    @endphp
                                                                                    <tr class="hover:bg-indigo-50/20 transition-colors">
                                                                                        @foreach($validKeys as $itemKey)
                                                                                            <td class="px-3 py-2 text-gray-700 font-semibold">
                                                                                                @php
                                                                                                    $itemVal = $itemArray[$itemKey] ?? null;
                                                                                                @endphp
                                                                                                @if(is_array($itemVal) || is_object($itemVal))
                                                                                                    @php
                                                                                                        $subVal = is_object($itemVal) ? (array) $itemVal : $itemVal;
                                                                                                    @endphp
                                                                                                    {{ $subVal['nombre'] ?? $subVal['name'] ?? '...' }}
                                                                                                @else
                                                                                                    @if($itemKey === 'producto_id')
                                                                                                        @php
                                                                                                            $prod = \App\Models\Producto::find($itemVal);
                                                                                                        @endphp
                                                                                                        {{ $prod ? $prod->nombre : "Producto #$itemVal" }}
                                                                                                    @elseif($itemKey === 'servicio_id')
                                                                                                        @php
                                                                                                            $serv = \App\Models\Servicio::find($itemVal);
                                                                                                        @endphp
                                                                                                        {{ $serv ? $serv->nombre : "Servicio #$itemVal" }}
                                                                                                    @else
                                                                                                        {{ $itemVal === null ? 'Nulo' : (is_bool($itemVal) ? ($itemVal ? 'Sí' : 'No') : $itemVal) }}
                                                                                                    @endif
                                                                                                @endif
                                                                                            </td>
                                                                                        @endforeach
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                                        @foreach($nestedArray as $subKey => $subValue)
                                                                            @if(!is_array($subValue) && !is_object($subValue))
                                                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50/50 p-1.5 rounded-md border border-gray-100">
                                                                                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider mb-0.5 sm:mb-0 truncate pr-2">{{ str_replace('_', ' ', $subKey) }}</span>
                                                                                    <span class="text-[10px] font-semibold text-gray-800 break-words text-left sm:text-right">{{ $subValue === null ? 'Nulo' : (is_bool($subValue) ? ($subValue ? 'Sí' : 'No') : $subValue) }}</span>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <span class="text-[10px] font-semibold text-gray-400 italic">Vacío</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </details>
                                </div>
                            @endif
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
