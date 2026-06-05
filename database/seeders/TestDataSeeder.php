<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Promotor;
use App\Models\Producto;
use App\Models\Horario;
use App\Models\Servicio;
use App\Models\Promocion;
use App\Models\Cita;
use App\Models\Comision;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Alerta;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar que los roles básicos existan
        if (Role::count() === 0) {
            $this->call(RolePermissionSeeder::class);
        }

        $adminRole = Role::where('slug', 'administrador')->first();
        $stylistRole = Role::where('slug', 'estilista')->first();
        $receptionistRole = Role::where('slug', 'recepcionista')->first();
        $clientRole = Role::where('slug', 'cliente')->first();

        // 2. Crear Usuarios (Estilistas, Clientes, Recepcionista, Admin)
        // Estilistas
        $stylistsData = [
            ['name' => 'Ana Gómez', 'email' => 'ana@salon.com', 'comision_porcentaje' => 15.00],
            ['name' => 'Carlos Pérez', 'email' => 'carlos@salon.com', 'comision_porcentaje' => 20.00],
            ['name' => 'Lucía Fernández', 'email' => 'lucia@salon.com', 'comision_porcentaje' => 10.00],
        ];
        $stylists = [];
        foreach ($stylistsData as $data) {
            $stylists[] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $stylistRole->id,
                    'comision_porcentaje' => $data['comision_porcentaje'],
                    'telefono' => '789456' . rand(10, 99)
                ]
            );
        }

        // Clientes
        $clientsData = [
            ['name' => 'María López', 'email' => 'maria@gmail.com'],
            ['name' => 'Juan Rodriguez', 'email' => 'juan@gmail.com'],
            ['name' => 'Sofía Mercado', 'email' => 'sofia@gmail.com'],
            ['name' => 'Jhon Salas', 'email' => 'jhon@gmail.com'],
            ['name' => 'Valeria Vargas', 'email' => 'valeria@gmail.com'],
        ];
        $clients = [];
        foreach ($clientsData as $data) {
            $clients[] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role_id' => $clientRole->id,
                    'telefono' => '654321' . rand(10, 99)
                ]
            );
        }

        // Recepcionista
        $receptionist = User::updateOrCreate(
            ['email' => 'recepcion@salon.com'],
            [
                'name' => 'Laura Flores',
                'password' => Hash::make('password123'),
                'role_id' => $receptionistRole->id,
                'telefono' => '71234567'
            ]
        );

        // Administrador Principal
        $admin = User::where('email', 'adm@adm.com')->first();
        if ($admin) {
            $admin->update(['name' => 'Administrador Principal']);
        } else {
            $admin = User::create([
                'name' => 'Administrador Principal',
                'email' => 'adm@adm.com',
                'password' => Hash::make('adm123'),
                'role_id' => $adminRole->id
            ]);
        }

        // 3. Crear Promotores (Proveedores)
        $promotoresData = [
            ['nombre' => 'Juan L\'Oreal', 'empresa' => 'L\'Oreal Professional', 'telefono' => '77712345', 'email' => 'juan@loreal.bo'],
            ['nombre' => 'Ana Maria', 'empresa' => 'P&G Beauty Bolivia', 'telefono' => '77754321', 'email' => 'ana@pg.bo'],
            ['nombre' => 'Pedro Gomez', 'empresa' => 'Schwarzkopf Depósito', 'telefono' => '76543210', 'email' => 'pedro@schwarzkopf.bo'],
        ];
        $promotores = [];
        foreach ($promotoresData as $data) {
            $promotores[] = Promotor::updateOrCreate(['nombre' => $data['nombre']], $data);
        }

        // 4. Crear Productos (algunos con stock bajo para generar alertas)
        $productosData = [
            [
                'nombre' => 'Shampoo Sedal Keratina 450ml',
                'precio_compra' => 15.00,
                'precio_venta' => 25.50,
                'stock' => 15,
                'stock_minimo' => 5,
                'codigo' => 'SH-SED-01',
                'fecha_caducidad' => '2027-12-31',
                'promotor_id' => $promotores[0]->id ?? null,
                'imagen' => 'uploads/productos/shampoo_sedal.png'
            ],
            [
                'nombre' => 'Acondicionador Pantene Rulos 400ml',
                'precio_compra' => 18.00,
                'precio_venta' => 28.00,
                'stock' => 3,  // Stock bajo!
                'stock_minimo' => 5,
                'codigo' => 'AC-PAN-02',
                'fecha_caducidad' => '2027-06-30',
                'promotor_id' => $promotores[1]->id ?? null,
                'imagen' => 'uploads/productos/conditioner_pantene.png'
            ],
            [
                'nombre' => 'Tinte de Cabello Koleston Castaño Oscuro',
                'precio_compra' => 25.00,
                'precio_venta' => 45.00,
                'stock' => 20,
                'stock_minimo' => 8,
                'codigo' => 'TI-KOL-03',
                'fecha_caducidad' => '2028-01-15',
                'promotor_id' => $promotores[1]->id ?? null,
                'imagen' => 'uploads/productos/hair_dye_koleston.png'
            ],
            [
                'nombre' => 'Laca de Cabello Schwarzkopf 500ml',
                'precio_compra' => 40.00,
                'precio_venta' => 65.00,
                'stock' => 2,  // Stock bajo!
                'stock_minimo' => 4,
                'codigo' => 'LA-SCH-04',
                'fecha_caducidad' => '2026-10-31',
                'promotor_id' => $promotores[2]->id ?? null,
                'imagen' => 'uploads/productos/hair_spray_schwarzkopf.png'
            ],
            [
                'nombre' => 'Crema para Peinar Elvive Reparación 300ml',
                'precio_compra' => 12.00,
                'precio_venta' => 22.00,
                'stock' => 12,
                'stock_minimo' => 4,
                'codigo' => 'CR-ELV-05',
                'fecha_caducidad' => '2027-05-20',
                'promotor_id' => $promotores[0]->id ?? null,
                'imagen' => 'uploads/productos/styling_cream_elvive.png'
            ],
        ];
        $productos = [];
        foreach ($productosData as $data) {
            $productos[] = Producto::updateOrCreate(['codigo' => $data['codigo']], $data);
        }

        // 5. Crear Horarios para Estilistas y Recepcionista
        foreach ($stylists as $stylist) {
            // Lunes a Viernes: 09:00 a 18:00
            foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia) {
                Horario::updateOrCreate(
                    ['user_id' => $stylist->id, 'dia_semana' => $dia],
                    ['hora_inicio' => '09:00:00', 'hora_fin' => '18:00:00', 'activo' => true]
                );
            }
            // Sábado: 09:00 a 17:00
            Horario::updateOrCreate(
                ['user_id' => $stylist->id, 'dia_semana' => 'Sábado'],
                ['hora_inicio' => '09:00:00', 'hora_fin' => '17:00:00', 'activo' => true]
            );
        }
        foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia) {
            Horario::updateOrCreate(
                ['user_id' => $receptionist->id, 'dia_semana' => $dia],
                ['hora_inicio' => '08:30:00', 'hora_fin' => '18:30:00', 'activo' => true]
            );
        }

        // 6. Crear Servicios
        $serviciosData = [
            ['nombre' => 'Corte de Cabello Dama', 'precio' => 50.00, 'duracion_minutos' => 45, 'activo' => true],
            ['nombre' => 'Corte de Cabello Varón', 'precio' => 30.00, 'duracion_minutos' => 30, 'activo' => true],
            ['nombre' => 'Tinte Completo de Cabello', 'precio' => 150.00, 'duracion_minutos' => 120, 'activo' => true],
            ['nombre' => 'Peinado de Noche / Boda', 'precio' => 120.00, 'duracion_minutos' => 60, 'activo' => true],
            ['nombre' => 'Tratamiento Capilar Hidratante', 'precio' => 80.00, 'duracion_minutos' => 60, 'activo' => true],
        ];
        $servicios = [];
        foreach ($serviciosData as $data) {
            $servicios[] = Servicio::updateOrCreate(['nombre' => $data['nombre']], $data);
        }

        // 7. Crear Promociones
        $promocionesData = [
            [
                'nombre' => 'Descuento de Invierno Corte Dama',
                'descripcion' => '10% de descuento en corte de dama para lucir genial esta temporada.',
                'descuento_porcentaje' => 10.00,
                'fecha_inicio' => now()->subDays(5)->toDateString(),
                'fecha_fin' => now()->addDays(15)->toDateString(),
                'activo' => true,
                'servicio_id' => $servicios[0]->id,
                'producto_id' => null
            ],
            [
                'nombre' => 'Promoción Champú Sedal',
                'descripcion' => '15% de descuento directo en el champú Sedal Keratina.',
                'descuento_porcentaje' => 15.00,
                'fecha_inicio' => now()->subDays(2)->toDateString(),
                'fecha_fin' => now()->addDays(20)->toDateString(),
                'activo' => true,
                'servicio_id' => null,
                'producto_id' => $productos[0]->id
            ],
            [
                'nombre' => 'Tratamiento Hidratante Oferta',
                'descripcion' => '20% de descuento en tratamientos hidratantes de mitad de semana.',
                'descuento_porcentaje' => 20.00,
                'fecha_inicio' => now()->subDays(10)->toDateString(),
                'fecha_fin' => now()->subDays(1)->toDateString(),
                'activo' => false,
                'servicio_id' => $servicios[4]->id,
                'producto_id' => null
            ],
        ];
        foreach ($promocionesData as $data) {
            Promocion::updateOrCreate(['nombre' => $data['nombre']], $data);
        }

        // 8. Crear Citas, Comisiones y Bitácora de Citas
        $citasData = [
            [
                'cliente_id' => $clients[0]->id,
                'estilista_id' => $stylists[0]->id,
                'servicio_id' => $servicios[0]->id,
                'fecha' => now()->subDays(2)->toDateString(),
                'hora' => '10:00:00',
                'estado' => 'completada',
                'notas' => 'Vino por su corte mensual.'
            ],
            [
                'cliente_id' => $clients[1]->id,
                'estilista_id' => $stylists[1]->id,
                'servicio_id' => $servicios[2]->id,
                'fecha' => now()->subDays(1)->toDateString(),
                'hora' => '14:30:00',
                'estado' => 'completada',
                'notas' => 'Coloración permanente.'
            ],
            [
                'cliente_id' => $clients[2]->id,
                'estilista_id' => $stylists[2]->id,
                'servicio_id' => $servicios[1]->id,
                'fecha' => now()->toDateString(),
                'hora' => '11:00:00',
                'estado' => 'confirmada',
                'notas' => 'Corte clásico de caballero.'
            ],
            [
                'cliente_id' => $clients[3]->id,
                'estilista_id' => $stylists[0]->id,
                'servicio_id' => $servicios[3]->id,
                'fecha' => now()->addDays(2)->toDateString(),
                'hora' => '16:00:00',
                'estado' => 'pendiente',
                'notas' => 'Para su fiesta de graduación.'
            ],
            [
                'cliente_id' => $clients[4]->id,
                'estilista_id' => $stylists[1]->id,
                'servicio_id' => $servicios[4]->id,
                'fecha' => now()->subDays(4)->toDateString(),
                'hora' => '09:30:00',
                'estado' => 'completada',
                'notas' => 'Tratamiento reestructurante.'
            ]
        ];

        foreach ($citasData as $cData) {
            // Eliminar si existe para no duplicar en repetidas ejecuciones
            Cita::where('cliente_id', $cData['cliente_id'])
                ->where('fecha', $cData['fecha'])
                ->where('hora', $cData['hora'])
                ->delete();

            $cita = Cita::create($cData);

            if ($cita->estado === 'completada') {
                $precioOriginal = $cita->servicio->precio;
                $promo = Promocion::where('activo', true)
                    ->where('servicio_id', $cita->servicio_id)
                    ->whereDate('fecha_inicio', '<=', $cita->fecha)
                    ->whereDate('fecha_fin', '>=', $cita->fecha)
                    ->first();
                
                $desc = 0;
                if ($promo) {
                    $desc = ($precioOriginal * $promo->descuento_porcentaje) / 100;
                }
                $precioFinal = $precioOriginal - $desc;
                $porcentajeComision = $cita->estilista->comision_porcentaje ?: 15.00;
                $montoComision = ($precioFinal * $porcentajeComision) / 100;

                Comision::updateOrCreate(
                    ['cita_id' => $cita->id],
                    [
                        'estilista_id' => $cita->estilista_id,
                        'monto_servicio' => $precioFinal,
                        'porcentaje_comision' => $porcentajeComision,
                        'monto_comision' => $montoComision,
                        'estado' => rand(0, 1) ? 'pagado' : 'pendiente',
                        'fecha_calculo' => $cita->fecha . ' ' . $cita->hora
                    ]
                );

                ActivityLog::create([
                    'user_id' => $admin->id,
                    'action' => 'UPDATE',
                    'description' => "Servicio realizado registrado para Cita ID: {$cita->id}. Comisión de Bs{$montoComision} calculada para {$cita->estilista->name}.",
                    'details' => json_encode([
                        'cita_id' => $cita->id,
                        'monto_pagado' => $precioFinal,
                        'comision' => $montoComision
                    ]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Seeder'
                ]);
            } else {
                ActivityLog::create([
                    'user_id' => $receptionist->id,
                    'action' => 'CREATE',
                    'description' => "Cita agendada para el cliente ID: {$cita->cliente_id}",
                    'details' => json_encode($cita->toArray()),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Seeder'
                ]);
            }
        }

        // 9. Crear Ventas de Productos
        $ventasData = [
            [
                'cliente_id' => $clients[0]->id,
                'cliente_nombre' => $clients[0]->name,
                'vendedor_id' => $receptionist->id,
                'metodo_pago' => 'efectivo',
                'estado_pago' => 'completado',
                'fecha_venta' => now()->subDays(3),
                'items' => [
                    ['producto_id' => $productos[0]->id, 'cantidad' => 2], // Shampoo Sedal
                    ['producto_id' => $productos[4]->id, 'cantidad' => 1], // Crema Elvive
                ]
            ],
            [
                'cliente_id' => null,
                'cliente_nombre' => 'Oscar Mendez (Cliente Casual)',
                'vendedor_id' => $receptionist->id,
                'metodo_pago' => 'efectivo',
                'estado_pago' => 'completado',
                'fecha_venta' => now()->subDays(1),
                'items' => [
                    ['producto_id' => $productos[2]->id, 'cantidad' => 1], // Tinte Koleston
                ]
            ],
            [
                'cliente_id' => $clients[3]->id,
                'cliente_nombre' => $clients[3]->name,
                'vendedor_id' => $admin->id,
                'metodo_pago' => 'tarjeta',
                'estado_pago' => 'completado',
                'fecha_venta' => now()->subDays(2),
                'items' => [
                    ['producto_id' => $productos[0]->id, 'cantidad' => 1], // Shampoo Sedal
                    ['producto_id' => $productos[3]->id, 'cantidad' => 1], // Laca Schwarzkopf
                ]
            ]
        ];

        foreach ($ventasData as $vData) {
            $venta = new Venta();
            $venta->cliente_id = $vData['cliente_id'];
            $venta->cliente_nombre = $vData['cliente_nombre'];
            $venta->vendedor_id = $vData['vendedor_id'];
            $venta->metodo_pago = $vData['metodo_pago'];
            $venta->estado_pago = $vData['estado_pago'];
            $venta->fecha_venta = $vData['fecha_venta'];
            $venta->subtotal = 0;
            $venta->descuento = 0;
            $venta->total = 0;
            $venta->save();

            $subtotalAcumulado = 0;
            $descuentoAcumulado = 0;

            foreach ($vData['items'] as $item) {
                $prod = Producto::find($item['producto_id']);
                $cant = $item['cantidad'];

                $promo = Promocion::where('activo', true)
                    ->where('producto_id', $prod->id)
                    ->whereDate('fecha_inicio', '<=', $venta->fecha_venta)
                    ->whereDate('fecha_fin', '>=', $venta->fecha_venta)
                    ->first();

                $precioUnitario = $prod->precio_venta;
                $descuentoPorUnidad = 0;
                if ($promo) {
                    $descuentoPorUnidad = ($precioUnitario * $promo->descuento_porcentaje) / 100;
                }

                $subtotalItem = $cant * $precioUnitario;
                $descuentoItem = $cant * $descuentoPorUnidad;
                $totalItem = $subtotalItem - $descuentoItem;

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $prod->id,
                    'cantidad' => $cant,
                    'precio_unitario' => $precioUnitario,
                    'descuento' => $descuentoItem,
                    'subtotal' => $totalItem
                ]);

                $subtotalAcumulado += $subtotalItem;
                $descuentoAcumulado += $descuentoItem;
            }

            $venta->subtotal = $subtotalAcumulado;
            $venta->descuento = $descuentoAcumulado;
            $venta->total = $subtotalAcumulado - $descuentoAcumulado;
            $venta->save();

            ActivityLog::create([
                'user_id' => $venta->vendedor_id,
                'action' => 'CREATE',
                'description' => "Venta registrada ID: {$venta->id}. Total: Bs{$venta->total}",
                'details' => json_encode($venta->load('detalles')->toArray()),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder'
            ]);
        }

        // 10. Crear Alertas de Stock Bajo
        Alerta::query()->delete(); // Limpiar antiguas
        $lowStockProducts = Producto::whereRaw('stock <= stock_minimo')->get();
        foreach ($lowStockProducts as $p) {
            Alerta::create([
                'tipo' => 'stock_bajo',
                'mensaje' => "El producto '{$p->nombre}' ha alcanzado el stock mínimo (Disponible: {$p->stock}, Mínimo: {$p->stock_minimo}).",
                'leido' => false,
                'producto_id' => $p->id,
            ]);
        }
    }
}
