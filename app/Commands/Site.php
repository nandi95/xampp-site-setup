<?php

namespace App\Commands;

use App\Traits\BaseMethods;
use App\Traits\GitMethods;
use App\Traits\xamppMethods;
use Carbon\Carbon;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * Class Site
 *
 * @package App\Commands
 */
class Site extends Command
{
    use BaseMethods,
        GitMethods,
        XamppMethods;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site {domain?} {--clone-url=} {--branch=} {--domain-extension=.co.uk} {--silent}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sets up basic configuration for local development.';

    /**
     * @var string $project
     */
    private $project;

    /**
     * @var string $domain
     */
    private $domain;

    /**
     * @var Carbon $startTime
     */
    private $startTime;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->gatherInfo();
        $this->commandInfo('Setting up for: "' . $this->project . '"');
        $this->startTime = now();
        $this->info('Cloning repository 1/5');
        $this->manageVCS();
        $this->info('Installing NPM packages 2/5');
        $this->runNPM();
        $this->info('Installing Composer packages 3/5');
        $this->runComposer();
        $this->info('Updating configurations 4/5');
        $this->line(' - Configuring .env file');
        $this->copyEnv();
        $this->line(' - Updating httpd-vhosts.conf');
        $this->updateVHosts();
        if ($this->inAdministratorMode()) {
            $this->line(' - Updating hosts file');
            $this->updateHosts();
        }
        $this->info('Restarting xampp 5/5');
        $this->restartXampp();
        $this->line('You may need to do additional steps as outlined in the repository\'s README');
        $this->commandInfo('Finished running command for: "' . $this->project . '" in: ' . gmdate('H:i:s', $this->startTime->diffInSeconds(now())));
        $this->line('site\'s ready at: http://' . $this->project);
    }


    public function gatherInfo()
    {
        $this->getCloneUrl();
        $default = explode('/', $this->cloneUrl);
        $default = str_replace('.git', '', end($default));
        $this->project  = config('app.domain-prefix');
        $this->project .= strtolower($this->argument('domain') ? $this->argument('domain') : $this->ask('What\'s the domain name?', $default));
        $this->project .= $this->option('domain-extension');
        if (! file_exists(config('app.web-root') . '/' . $this->project)) {
            mkdir(config('app.web-root') . '/' . $this->project);
        }
        if (count(scandir(config('app.web-root') . '/' . $this->project)) > 2) {
            $this->consoleError('Folder\'s not empty, please select another domain name or empty the folder: ' . config('app.web-root') . '/' . $this->project);
            exit;
        }
    }
}
//todo - create a log file in the folder so if interrupted the script can pick up where it left off
