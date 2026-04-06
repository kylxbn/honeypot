<?php

namespace App\Console\Commands;

use App\Models\CanaryToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateCanaries extends Command
{
    protected $signature   = 'honeypot:generate-canaries {--force : Regenerate all tokens, replacing existing ones}';
    protected $description = 'Generate canary tokens and embed them in fake file content';

    /**
     * Each entry defines one canary token.
     * trap_source matches what gets embedded; label describes where it appears.
     */
    private const CANARIES = [
        [
            'trap_source' => 'env-app-url',
            'label'       => '.env → APP_URL',
            'description' => 'Triggered if someone follows the APP_URL found in the fake .env file.',
        ],
        [
            'trap_source' => 'env-webhook',
            'label'       => '.env → WEBHOOK_SECRET_URL',
            'description' => 'Triggered if someone pings the webhook URL found in the fake .env file.',
        ],
        [
            'trap_source' => 'sql-dump-avatar',
            'label'       => 'SQL dump → avatar_url',
            'description' => 'Triggered if someone fetches an avatar URL from the SQL user dump.',
        ],
        [
            'trap_source' => 'api-avatar',
            'label'       => 'API /users → avatar_url',
            'description' => 'Triggered if someone fetches an avatar URL from the /api/v1/users response.',
        ],
        [
            'trap_source' => 'admin-panel-logo',
            'label'       => 'Admin panel → logo image',
            'description' => 'Triggered if someone loads the fake admin panel and the browser requests the logo.',
        ],
        [
            'trap_source' => 'wp-admin-logo',
            'label'       => 'wp-admin → site icon',
            'description' => 'Triggered if someone browses the fake wp-admin and loads the site icon.',
        ],
        [
            'trap_source' => 'aws-key-test',
            'label'       => '.env → fake AWS endpoint',
            'description' => 'Triggered if someone tests the fake AWS key against this endpoint.',
        ],
        [
            'trap_source' => 'backup-sql-logo',
            'label'       => 'SQL dump → company_logo_url',
            'description' => 'Triggered if someone fetches the company logo URL found in the SQL settings table.',
        ],
    ];

    public function handle(): int
    {
        $force = $this->option('force');

        foreach (self::CANARIES as $def) {
            $existing = CanaryToken::where('trap_source', $def['trap_source'])->first();

            if ($existing && !$force) {
                $this->line("  <fg=yellow>skip</>  {$def['trap_source']} — already exists (use --force to regenerate)");
                continue;
            }

            if ($existing) {
                $existing->delete();
            }

            $token = CanaryToken::create([
                'token'       => Str::random(40),
                'label'       => $def['label'],
                'trap_source' => $def['trap_source'],
                'description' => $def['description'],
            ]);

            $this->line("  <fg=green>created</>  {$def['trap_source']}  →  " . $token->url());
        }

        $this->newLine();
        $this->info('Canary tokens ready. Embed them in fake content by running the app — they are read from the DB dynamically.');

        return Command::SUCCESS;
    }
}
