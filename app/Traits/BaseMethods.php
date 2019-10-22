<?php

namespace App\Traits;

trait BaseMethods
{
    /**
     * @param $line
     *
     * @return void
     */
    public function commandInfo($line): void
    {
        $this->output->writeln('<bg=green;fg=white;options=bold>' . $line . '</>');
    }


    /**
     * @param $line
     *
     * @return void
     */
    public function consoleError($line): void
    {
        $this->output->writeln("<fg=black;bg=red> Error: </> \n <fg=red>" . $line . '</>');
    }


    /**
     * @return void
     */
    public function runNPM(): void
    {
        if ($this->NPMIsInstalled() && file_exists(config('app.web-root') . '/' . $this->project . '/package.json')) {
            exec('npm --prefix ' . config('app.web-root') . '/' . $this->project . ' install ' . config('app.web-root') . '/' . $this->project);
        }
    }


    /**
     * @return bool
     */
    public function NPMIsInstalled(): bool
    {
        // This tests more like if it's a semver rather than npm
        // filter for numeric values and check if the resulting array has a the length of 3
        return count(array_filter(explode('.', exec('npm --version')), function ($item) {
                return is_numeric($item);
            })) === 3;
    }


    /**
     * @return void
     */
    public function runComposer(): void
    {
        if ($this->ComposerIsInstalled() && file_exists(config('app.web-root') . '/' . $this->project . '/composer.json')) {
            exec('composer install --working-dir=' . config('app.web-root') . '/' . $this->project);
        }
    }


    /**
     * @return bool
     */
    public function ComposerIsInstalled(): bool
    {
        return strpos(exec('composer --version'), 'Composer version') !== -1;
    }


    /**
     * @return void
     */
    public function updateHosts(): void
    {
        file_put_contents(
            'C:\Windows\System32\drivers\etc\hosts',
            "\n127.0.0.1 " . $this->project,
            FILE_APPEND
        );
    }


    /**
     * @return void
     */
    public function copyEnv(): void
    {
        $lines = null;
        if (file_exists(config('app.web-root') . '/' . $this->project . '/.env.example')) {
            $lines = $this->parseEnv(config('app.web-root') . '/' . $this->project . '/.env.example');
        } elseif (file_exists(config('app.web-root') . '/' . $this->project . '/.env.local')) {
            $lines = $this->parseEnv(config('app.web-root') . '/' . $this->project . '/.env.local');
        }

        if ($lines) {
            $this->createEnv($lines);
        }
    }


    /**
     * Returns a key value array representation of the .env
     * @param string $path
     *
     * @return array
     */
    private function parseEnv(string $path): array
    {
        $env = file_get_contents($path);
        $envInline = trim(preg_replace('/\s\s+/', '&', $env));
        $lines = [];
        parse_str($envInline, $lines);
        return $lines;
    }


    private function createEnv(array $lines): void
    {
        $keyType = explode('_', array_key_first($lines))[0];
        $env = '';
        $needAppKey = false;
        foreach ($lines as $key => $value) {
            if (strpos($key, $keyType) === false) {
                $env .= "\r\n\n";
            }
            if ($key === 'APP_KEY' && empty($value)) {
                $needAppKey = true;
            }
            if ($key === 'APP_NAME' && empty($value)) {
                $value = ucfirst($this->domain);
            }
            if ($key === 'APP_URL') {
                $value = $this->project;
            }
            $env .= $key . '=' . $value . "\n";
            $keyType = explode('_', $key)[0];
        }
        file_put_contents(config('app.web-root') . '/' . $this->project . '/.env', $env);
        if ($needAppKey) {
            $workingPath = getcwd();
            chdir(config('app.web-root') . '/' . $this->project);
            exec('php artisan key:generate');
            chdir($workingPath);
        }
    }


    public function inAdministratorMode(): bool
    {
        $output = null;
        $systemCode = null;
        exec('net session', $output, $systemCode);
        return $systemCode === 0;
    }
}
