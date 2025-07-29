<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'ads',
            'branches',
            'brands',
            'cancelled_orders',
            'categories',
            'chart_of_accounts',
            'colors',
            'company_details',
            'contacts',
            'contact_emails',
            'coupons',
            'coupon_usages',
            'delivery_charges',
            'equity_holders',
            'faq_questions',
            'groups',
            'mail_contents',
            'orders',
            'order_details',
            'order_returns',
            'payment_gateways',
            'products',
            'product_colors',
            'product_models',
            'product_prices',
            'product_reviews',
            'product_sizes',
            'product_types',
            'purchases',
            'purchase_histories',
            'purchase_history_logs',
            'purchase_returns',
            'related_products',
            'roles',
            'section_statuses',
            'shipments',
            'shipment_details',
            'shippings',
            'shipping_costs',
            'sizes',
            'sliders',
            'stocks',
            'stock_histories',
            'stock_transfer_requests',
            'sub_categories',
            'suppliers',
            'system_loses',
            'transactions',
            'types',
            'units',
            'users',
            'warehouses',
        ];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
                }

                if (!Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->string('deleted_by')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'ads',
            'branches',
            'brands',
            'cancelled_orders',
            'categories',
            'chart_of_accounts',
            'colors',
            'company_details',
            'contacts',
            'contact_emails',
            'coupons',
            'coupon_usages',
            'delivery_charges',
            'equity_holders',
            'faq_questions',
            'groups',
            'mail_contents',
            'orders',
            'order_details',
            'order_returns',
            'payment_gateways',
            'products',
            'product_colors',
            'product_models',
            'product_prices',
            'product_reviews',
            'product_sizes',
            'product_types',
            'purchases',
            'purchase_histories',
            'purchase_history_logs',
            'purchase_returns',
            'related_products',
            'roles',
            'section_statuses',
            'shipments',
            'shipment_details',
            'shippings',
            'shipping_costs',
            'sizes',
            'sliders',
            'stocks',
            'stock_histories',
            'stock_transfer_requests',
            'sub_categories',
            'suppliers',
            'system_loses',
            'transactions',
            'types',
            'units',
            'users',
            'warehouses',
        ];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->dropSoftDeletes();
                }

                if (Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->dropColumn('deleted_by');
                }
            });
        }
    }
};
