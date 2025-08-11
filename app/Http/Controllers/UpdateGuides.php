<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class UpdateGuides extends Controller
{
    public function __construct()
    {
        $this->middleware(['demo'])->only(['runUpdatedCommand']);
    }

    public function index()
    {
        return view('backend.update_guides');
    }

    public function runUpdatedCommand()
    {
        try {
            // Step 1: Run migration
            Artisan::call('migrate', ['--force' => true]);

            // Step 2: Clear caches
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');

            // Step 3: Composer install
            if (function_exists('exec')) {
                $output = null;
                $returnCode = null;
                exec('composer install --no-dev --optimize-autoloader 2>&1', $output, $returnCode);

                if ($returnCode !== 0) {
                    Log::error('Composer install failed.', [
                        'output' => $output,
                        'return_code' => $returnCode,
                    ]);

                    return redirect('admin/update/guides')
                        ->with('fail', 'Composer install failed. Please check the logs for more details.');
                }
            } else {
                Log::warning('exec() is disabled. Composer install was skipped.');
            }

            // Step 4: DB inserts
            DB::beginTransaction();

            DB::statement("INSERT INTO `settings` (`id`, `event`, `details`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) 
                VALUES 
                (120, 'cookie_content', '{\"alert_title\":\"We value your privacy!\",\"alert_content\":\"We use cookies to improve your experience, deliver personalized content and ads, and analyze our traffic. By continuing to browse our site, you agree to our use of cookies.\",\"page_title\":\"Cookie Policy\",\"page_url\":\"https:\\/\\/latestnews365.bdtask-demo.com\\/privacy-policy\",\"cookie_duration\":\"10\"}', NULL, 1, NULL, '2025-07-26 10:53:35', NULL)
                ON DUPLICATE KEY UPDATE 
                `details` = VALUES(`details`), `updated_by` = VALUES(`updated_by`), `updated_at` = VALUES(`updated_at`);
            ");

            $menuItems = [
                [
                    'id' => 68,
                    'uuid' => '',
                    'parentmenu_id' => null,
                    'lable' => 0,
                    'menu_name' => 'Auto Post Settings',
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => '2025-07-22 12:47:33',
                    'updated_at' => '2025-07-22 12:47:33',
                    'deleted_at' => null,
                ],
                [
                    'id' => 69,
                    'uuid' => '',
                    'parentmenu_id' => 68,
                    'lable' => 0,
                    'menu_name' => 'Social Media',
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => '2025-07-22 12:47:33',
                    'updated_at' => '2025-07-22 12:47:33',
                    'deleted_at' => null,
                ],
                [
                    'id' => 70,
                    'uuid' => '',
                    'parentmenu_id' => 31,
                    'lable' => 0,
                    'menu_name' => 'Cookie Content',
                    'created_by' => null,
                    'updated_by' => null,
                    'created_at' => '2025-07-26 08:33:16',
                    'updated_at' => '2025-07-26 08:33:16',
                    'deleted_at' => null,
                ],
            ];
            foreach ($menuItems as $item) {
                DB::table('per_menus')->updateOrInsert(
                    ['id' => $item['id']],
                    $item
                );
            }

            $permissions = [
                [
                    'id' => 260,
                    'name' => 'create_auto_post_settings',
                    'guard_name' => 'web',
                    'per_menu_id' => 68,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 261,
                    'name' => 'read_auto_post_settings',
                    'guard_name' => 'web',
                    'per_menu_id' => 68,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 262,
                    'name' => 'update_auto_post_settings',
                    'guard_name' => 'web',
                    'per_menu_id' => 68,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 263,
                    'name' => 'delete_auto_post_settings',
                    'guard_name' => 'web',
                    'per_menu_id' => 68,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 264,
                    'name' => 'create_auto_posting_media',
                    'guard_name' => 'web',
                    'per_menu_id' => 69,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 265,
                    'name' => 'read_auto_posting_media',
                    'guard_name' => 'web',
                    'per_menu_id' => 69,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 266,
                    'name' => 'update_auto_posting_media',
                    'guard_name' => 'web',
                    'per_menu_id' => 69,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 267,
                    'name' => 'delete_auto_posting_media',
                    'guard_name' => 'web',
                    'per_menu_id' => 69,
                    'created_at' => '2025-07-22 12:49:46',
                    'updated_at' => '2025-07-22 12:49:46',
                ],
                [
                    'id' => 268,
                    'name' => 'create_cookie_content',
                    'guard_name' => 'web',
                    'per_menu_id' => 70,
                    'created_at' => '2025-07-26 08:33:16',
                    'updated_at' => '2025-07-26 08:33:16',
                ],
                [
                    'id' => 269,
                    'name' => 'read_cookie_content',
                    'guard_name' => 'web',
                    'per_menu_id' => 70,
                    'created_at' => '2025-07-26 08:33:16',
                    'updated_at' => '2025-07-26 08:33:16',
                ],
                [
                    'id' => 270,
                    'name' => 'update_cookie_content',
                    'guard_name' => 'web',
                    'per_menu_id' => 70,
                    'created_at' => '2025-07-26 08:33:16',
                    'updated_at' => '2025-07-26 08:33:16',
                ],
                [
                    'id' => 271,
                    'name' => 'delete_cookie_content',
                    'guard_name' => 'web',
                    'per_menu_id' => 70,
                    'created_at' => '2025-07-26 08:33:16',
                    'updated_at' => '2025-07-26 08:33:16',
                ],
            ];
            foreach ($permissions as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['id' => $permission['id']],
                    $permission
                );
            }

            DB::commit();

            return redirect('admin/update/guides')->with('success', 'Update completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect('admin/update/guides')
                ->with('fail', 'Update failed!');
        }
    }
}
