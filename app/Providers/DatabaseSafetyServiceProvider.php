<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Proteção do banco de dados contra comandos que apagam ou sobrescrevem dados.
 *
 * - migrate:fresh, migrate:refresh, migrate:reset, migrate:rollback e db:wipe
 *   ficam sempre proibidos (DB::prohibitDestructiveCommands).
 * - db:seed e migrate --seed ficam bloqueados: os seeders fazem truncate e
 *   redefinem senhas de usuários. Só podem rodar com
 *   ALLOW_DB_SEED=true no .env E com o banco apontando para localhost.
 * - "artisan test" fica bloqueado (testes com RefreshDatabase apagam o banco).
 */
class DatabaseSafetyServiceProvider extends ServiceProvider
{
    private const ALWAYS_BLOCKED = [
        'db:wipe',
        'migrate:fresh',
        'migrate:refresh',
        'migrate:reset',
        'migrate:rollback',
        'test',
    ];

    private const LOCAL_HOSTS = ['127.0.0.1', 'localhost', '::1'];

    public function boot(): void
    {
        DB::prohibitDestructiveCommands();

        if (! $this->app->runningInConsole()) {
            return;
        }

        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            $command = (string) $event->command;

            if (in_array($command, self::ALWAYS_BLOCKED, true)) {
                $this->block($command, 'este comando pode apagar o banco de dados.');
            }

            $isSeed = $command === 'db:seed'
                || ($command === 'migrate' && $event->input->hasParameterOption(['--seed', '--seeder'], true));

            if ($isSeed && ! $this->seedAllowed()) {
                $this->block(
                    $command,
                    'seeders fazem truncate e redefinem senhas. Só é permitido com ALLOW_DB_SEED=true e banco em localhost.'
                );
            }
        });
    }

    private function seedAllowed(): bool
    {
        $connection = config('database.default');
        $host = (string) config("database.connections.{$connection}.host", '');
        $driver = (string) config("database.connections.{$connection}.driver", '');

        $isLocalDb = $driver === 'sqlite' || in_array($host, self::LOCAL_HOSTS, true);

        return env('ALLOW_DB_SEED') === true && $isLocalDb;
    }

    private function block(string $command, string $reason): never
    {
        throw new RuntimeException("Comando [{$command}] bloqueado por segurança: {$reason}");
    }
}
