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
            "\n 127.0.0.1 " . $this->project,
            FILE_APPEND
        );
    }


    /**
     * @return void
     */
    public function startDevelopment(): void
    {
        if (file_exists(config('app.web-root') . '/' . $this->project . '/package.json')) {
            $packagesJson = file_get_contents(config('app.web-root') . '/' . $this->project . '/package.json');
            if (strpos($packagesJson, '"watch":') !== -1) {
                exec('npm --prefix ' . config('app.web-root') . '/' . $this->project . ' run watch');
            }
            if (strpos($packagesJson, '"watch":') === -1 && strpos($packagesJson, '"dev":') == -1 ) {
                exec('npm --prefix ' . config('app.web-root') . '/' . $this->project . ' run dev');
            }
        }
        exec('phpstorm64.exe ' . config('app.web-root') . '/' . $this->project);
        // open in editor
        // open in browser
    }


    /**
     * @return void
     */
    public function copyEnv(): void
    {
        $copied = false;
        if (file_exists(config('app.web-root') . '/' . $this->project . '/.env.example')) {
            copy(
                config('app.web-root') . '/' . $this->project . '/.env.example',
                config('app.web-root') . '/' . $this->project . '/.env'
            );
            $copied = true;
        }
        if (! $copied && file_exists(config('app.web-root') . '/' . $this->project . '/.env.local')) {
            copy(
                config('app.web-root') . '/' . $this->project . '/.env.local',
                config('app.web-root') . '/' . $this->project . '/.env'
            );
        }
    }
}
