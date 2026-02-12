<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché de permisos de la aplicación
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definir Permisos por Módulo (Basado en tus migraciones y rutas)
        $permissions = [
            // --- Módulo: Usuarios (Administración del Sistema) ---
            [
                'name' => 'users.index',
                'description' => 'Ver lista de usuarios y empleados',
                'module' => 'Usuarios'
            ],
            [
                'name' => 'users.create',
                'description' => 'Registrar nuevos usuarios',
                'module' => 'Usuarios'
            ],
            [
                'name' => 'users.edit',
                'description' => 'Editar usuarios y asignar roles',
                'module' => 'Usuarios'
            ],
            [
                'name' => 'users.delete',
                'description' => 'Eliminar o desactivar usuarios',
                'module' => 'Usuarios'
            ],
            [
                'name' => 'users.status',
                'description' => 'Cambiar estatus de usuarios (activar/desactivar)',
                'module' => 'Usuarios'
            ],

            // --- Módulo: Citas (Appointments) ---
            [
                'name' => 'appointments.index',
                'description' => 'Ver calendario y lista de citas',
                'module' => 'Citas'
            ],
            [
                'name' => 'appointments.manage',
                'description' => 'Agendar, reagendar o cambiar estatus de citas', // Cubre edit/update
                'module' => 'Citas'
            ],
            [
                'name' => 'appointments.delete',
                'description' => 'Cancelar o eliminar citas del historial',
                'module' => 'Citas'
            ],

            // --- Módulo: Punto de Venta (POS) y Órdenes ---
            [
                'name' => 'pos.access',
                'description' => 'Acceder al módulo de Punto de Venta',
                'module' => 'Ventas'
            ],
            [
                'name' => 'orders.index',
                'description' => 'Ver historial de ventas y órdenes',
                'module' => 'Ventas'
            ],
            // La creación de órdenes suele ir implícita con el acceso al POS, pero lo definimos por si acaso
            [
                'name' => 'orders.create',
                'description' => 'Registrar nuevas ventas',
                'module' => 'Ventas'
            ],

            // --- Módulo: Inventario y Productos ---
            [
                'name' => 'products.index',
                'description' => 'Ver catálogo de productos e inventario',
                'module' => 'Inventario'
            ],
            [
                'name' => 'products.create',
                'description' => 'Crear nuevos productos',
                'module' => 'Inventario'
            ],
            [
                'name' => 'products.edit',
                'description' => 'Editar precios y detalles de productos',
                'module' => 'Inventario'
            ],
            [
                'name' => 'products.delete',
                'description' => 'Eliminar productos',
                'module' => 'Inventario'
            ],

            // --- Módulo: Clientes (CRM) ---
            [
                'name' => 'clients.index',
                'description' => 'Ver directorio de clientes',
                'module' => 'Clientes'
            ],
            [
                'name' => 'clients.create',
                'description' => 'Registrar nuevos clientes',
                'module' => 'Clientes'
            ],
            [
                'name' => 'clients.edit',
                'description' => 'Editar información de clientes',
                'module' => 'Clientes'
            ],
            [
                'name' => 'clients.delete',
                'description' => 'Eliminar clientes',
                'module' => 'Clientes'
            ],

            // --- Módulo: CMS (Gestión de la Landing Page) ---
            [
                'name' => 'cms.access',
                'description' => 'Acceder al panel de gestión de contenido',
                'module' => 'CMS'
            ],
            [
                'name' => 'portfolio.manage',
                'description' => 'Crear, editar y eliminar items del portafolio',
                'module' => 'CMS'
            ],
            [
                'name' => 'services.manage',
                'description' => 'Administrar paquetes de servicios y precios',
                'module' => 'CMS'
            ],

            // --- Módulo: Mensajes de Contacto ---
            [
                'name' => 'messages.index',
                'description' => 'Ver buzón de mensajes de contacto',
                'module' => 'Mensajes'
            ],
            [
                'name' => 'messages.delete',
                'description' => 'Eliminar mensajes procesados',
                'module' => 'Mensajes'
            ],

            // --- Módulo: Reportes ---
            [
                'name' => 'reports.view',
                'description' => 'Ver reportes financieros y de ventas',
                'module' => 'Reportes'
            ],
            [
                'name' => 'reports.generate',
                'description' => 'Generar y exportar reportes',
                'module' => 'Reportes'
            ],

            // --- Módulo: Configuraciones ---
            [
                'name' => 'roles.index',
                'description' => 'Ver roles y permisos existentes',
                'module' => 'Configuraciones'
            ],
            [
                'name' => 'roles.manage',
                'description' => 'Crear, editar y eliminar roles',
                'module' => 'Configuraciones'
            ],
            [
                'name' => 'settings.general',
                'description' => 'Acceder a configuraciones generales del sistema',
                'module' => 'Configuraciones'
            ],
        ];

        // 3. Insertar Permisos en Base de Datos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'module' => $permission['module'],
                    'guard_name' => 'web'
                ]
            );
        }

        // 4. Crear Roles y Asignar Permisos

        // --- Role: Super Admin ---
        // Tiene acceso absoluto a todo.
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super admin']);
        $roleSuperAdmin->syncPermissions(Permission::all());

        // --- Role: Vendedor ---
        // Enfocado en atención al cliente, ventas (POS) y citas.
        $roleVendedor = Role::firstOrCreate(['name' => 'Vendedor']);
        $roleVendedor->syncPermissions([
            // Ventas y POS
            'pos.access', 'orders.index', 'orders.create',
            // Citas
            'appointments.index', 'appointments.manage',
            // Clientes (CRM)
            'clients.index', 'clients.create', 'clients.edit',
            // Inventario (Solo ver para consultar precios/stock)
            'products.index',
            // Mensajes (Para dar seguimiento a leads)
            'messages.index',
        ]);

        // --- Role: Diseñador ---
        // Enfocado en el contenido visual (Portafolio) y ver trabajos pendientes.
        $roleDisenador = Role::firstOrCreate(['name' => 'Diseñador']);
        $roleDisenador->syncPermissions([
            // CMS y Portafolio (Su área principal)
            'cms.access', 'portfolio.manage',
            // Citas (Para ver su agenda de escaneos o entregas)
            'appointments.index',
            // Productos (Ver catálogo de servicios/precios)
            'products.index', 'services.manage', // Quizás ayude a definir servicios técnicos
            // Mensajes (Soporte técnico o dudas de diseño)
            'messages.index',
        ]);
    }
}