<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ── General ──────────────────────────────────────────
            [
                'key'         => 'app_name',
                'value'       => 'TAC Stores',
                'type'        => 'text',
                'group'       => 'general',
                'label'       => 'Application name',
                'description' => 'Displayed in the browser tab and sidebar',
            ],
            [
                'key'         => 'app_tagline',
                'value'       => 'TAC Stores Management System',
                'type'        => 'text',
                'group'       => 'general',
                'label'       => 'Tagline',
                'description' => 'Short description shown on the login page',
            ],
            [
                'key'         => 'currency_code',
                'value'       => 'GHS',
                'type'        => 'text',
                'group'       => 'general',
                'label'       => 'Currency code',
                'description' => 'e.g. GHS, USD, EUR',
            ],
            [
                'key'         => 'currency_symbol',
                'value'       => '₵',
                'type'        => 'text',
                'group'       => 'general',
                'label'       => 'Currency symbol',
                'description' => 'e.g. ₵, $, €',
            ],
            [
                'key'         => 'date_format',
                'value'       => 'd M Y',
                'type'        => 'select',
                'group'       => 'general',
                'label'       => 'Date format',
                'description' => 'How dates are displayed across the system',
            ],
            [
                'key'         => 'timezone',
                'value'       => 'Africa/Accra',
                'type'        => 'select',
                'group'       => 'general',
                'label'       => 'Timezone',
                'description' => 'System timezone for dates and times',
            ],

            // ── Sales ─────────────────────────────────────────────
            [
                'key'         => 'invoice_prefix',
                'value'       => 'TAC-INV',
                'type'        => 'text',
                'group'       => 'sales',
                'label'       => 'Invoice prefix',
                'description' => 'Prefix used for invoice numbers e.g. TAC-INV-000001',
            ],
            [
                'key'         => 'po_prefix',
                'value'       => 'TAC-PO',
                'type'        => 'text',
                'group'       => 'sales',
                'label'       => 'Purchase order prefix',
                'description' => 'Prefix used for PO numbers e.g. TAC-PO-000001',
            ],
            [
                'key'         => 'receipt_footer',
                'value'       => 'God Bless you for coming!',
                'type'        => 'textarea',
                'group'       => 'sales',
                'label'       => 'Receipt footer message',
                'description' => 'Printed at the bottom of every receipt',
            ],
            [
                'key'         => 'enable_credit_sales',
                'value'       => '1',
                'type'        => 'boolean',
                'group'       => 'sales',
                'label'       => 'Allow credit sales',
                'description' => 'Allow sales where the amount paid is less than the total',
            ],
            [
                'key'         => 'low_stock_threshold',
                'value'       => '10',
                'type'        => 'text',
                'group'       => 'sales',
                'label'       => 'Default low stock threshold',
                'description' => 'Default reorder level for new products',
            ],

            // ── Notifications ─────────────────────────────────────
            [
                'key'         => 'notify_low_stock',
                'value'       => '1',
                'type'        => 'boolean',
                'group'       => 'notifications',
                'label'       => 'Low stock notifications',
                'description' => 'Show bell alerts when stock falls below reorder level',
            ],
            [
                'key'         => 'notify_pending_transfers',
                'value'       => '1',
                'type'        => 'boolean',
                'group'       => 'notifications',
                'label'       => 'Pending transfer notifications',
                'description' => 'Show bell alerts for transfers awaiting approval',
            ],
            [
                'key'         => 'notify_pending_returns',
                'value'       => '1',
                'type'        => 'boolean',
                'group'       => 'notifications',
                'label'       => 'Pending return notifications',
                'description' => 'Show bell alerts for returns awaiting approval',
            ],
            [
                'key'         => 'notify_pending_deposits',
                'value'       => '1',
                'type'        => 'boolean',
                'group'       => 'notifications',
                'label'       => 'Pending deposit notifications',
                'description' => 'Show bell alerts for bank deposits awaiting verification',
            ],

            // ── Security ──────────────────────────────────────────
            [
                'key'         => 'session_timeout',
                'value'       => '120',
                'type'        => 'text',
                'group'       => 'security',
                'label'       => 'Session timeout (minutes)',
                'description' => 'Auto-logout after this many minutes of inactivity',
            ],
            [
                'key'         => 'max_login_attempts',
                'value'       => '5',
                'type'        => 'text',
                'group'       => 'security',
                'label'       => 'Max login attempts',
                'description' => 'Lock account after this many failed login attempts',
            ],
            [
                'key'         => 'password_min_length',
                'value'       => '8',
                'type'        => 'text',
                'group'       => 'security',
                'label'       => 'Minimum password length',
                'description' => 'Minimum number of characters required for passwords',
            ],
            [
                'key'         => 'require_strong_password',
                'value'       => '1',
                'type'        => 'boolean',
                'group'       => 'security',
                'label'       => 'Require strong passwords',
                'description' => 'Enforce uppercase, lowercase, numbers in passwords',
            ],

        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully.');
    }
}
