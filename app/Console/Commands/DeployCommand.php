<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

/**
 * Atualiza o servidor: git pull e, em seguida, dependências, migrations, caches e build.
 *
 * Cada etapa roda em um processo separado, então o que vem depois do pull já usa
 * o código novo. Para na primeira falha. Se houver migrations pendentes, faz
 * backup do banco antes de migrar (e não migra se o backup falhar).
 */
class DeployCommand extends Command
{
    protected $signature = 'deploy
        {--skip-build : Não roda o npm run build}
        {--skip-backup : Não faz backup do banco antes de migrations novas}';

    protected $description = 'git pull + composer/npm (se mudaram) + migrate + caches + build';

    public function handle(): int
    {
        $this->newLine();
        $this->info('Deploy DG Store');

        if (! $this->ensureCleanTree()) {
            return self::FAILURE;
        }

        $before = $this->git('rev-parse HEAD');

        if (! $this->step('Baixando o código (git pull)', ['git', 'pull', '--ff-only'])) {
            $this->error('O pull falhou. Nada mais foi executado.');

            return self::FAILURE;
        }

        $after = $this->git('rev-parse HEAD');
        $changed = $before === $after ? [] : array_filter(explode("\n", $this->git("diff --name-only {$before} {$after}")));

        if ($before === $after) {
            $this->line('  Código já estava atualizado. Seguindo com migrations, caches e build.');
        } else {
            $this->line('  ' . count($changed) . ' arquivo(s) alterado(s):');
            foreach (array_filter(explode("\n", $this->git("log --oneline {$before}..{$after}"))) as $commit) {
                $this->line("    <fg=gray>{$commit}</>");
            }
        }

        $php = PHP_BINARY;
        $artisan = base_path('artisan');

        if (in_array('composer.lock', $changed, true)
            && ! $this->step('Instalando dependências PHP', ['composer', 'install', '--no-dev', '--optimize-autoloader', '--no-interaction'])) {
            return self::FAILURE;
        }

        if (in_array('package-lock.json', $changed, true)
            && ! $this->option('skip-build')
            && ! $this->step('Instalando dependências JS (npm ci)', ['npm', 'ci'])) {
            return self::FAILURE;
        }

        $pending = $this->pendingMigrations();

        if ($pending !== []) {
            $this->newLine();
            $this->line('  Migrations pendentes: ' . implode(', ', $pending));
        }

        if ($pending !== [] && ! $this->option('skip-backup')
            && ! $this->step('Backup do banco (há migrations pendentes)', [$php, $artisan, 'db:backup'])) {
            $this->error('Backup falhou: as migrations NÃO foram rodadas. Use --skip-backup se tiver certeza.');

            return self::FAILURE;
        }

        $steps = [
            ['Rodando migrations', [$php, $artisan, 'migrate', '--force']],
            ['Limpando caches', [$php, $artisan, 'optimize:clear']],
        ];

        if (! $this->option('skip-build')) {
            $steps[] = ['Gerando assets (npm run build)', ['npm', 'run', 'build']];
        }

        $steps[] = ['Recriando caches', [$php, $artisan, 'optimize']];

        foreach ($steps as [$label, $command]) {
            if (! $this->step($label, $command)) {
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('Deploy concluído em ' . now()->format('d/m/Y H:i') . '.');

        return self::SUCCESS;
    }

    /** Recusa o deploy se arquivos versionados foram editados no servidor (o pull poderia conflitar). */
    private function ensureCleanTree(): bool
    {
        $dirty = trim($this->git('status --porcelain --untracked-files=no'));

        if ($dirty === '') {
            return true;
        }

        $this->error('Há arquivos alterados direto no servidor. Resolva antes do deploy:');
        $this->line($dirty);

        return false;
    }

    /** @param list<string> $command */
    private function step(string $label, array $command): bool
    {
        $this->newLine();
        $this->line("<fg=cyan>▸ {$label}</>");

        $result = Process::path(base_path())
            ->timeout(900)
            ->run($command, function (string $type, string $output): void {
                $this->output->write($output);
            });

        if ($result->failed()) {
            $this->error("✗ Falhou: {$label} (código {$result->exitCode()})");

            return false;
        }

        return true;
    }

    /** @return list<string> Migrations em disco que ainda não rodaram no banco. */
    private function pendingMigrations(): array
    {
        $migrator = $this->laravel->make('migrator');

        if (! $migrator->repositoryExists()) {
            return ['(tabela migrations ausente)'];
        }

        $files = array_keys($migrator->getMigrationFiles([database_path('migrations')]));

        return array_values(array_diff($files, $migrator->getRepository()->getRan()));
    }

    private function git(string $args): string
    {
        return trim(Process::path(base_path())->run('git ' . $args)->output());
    }
}
