<?php

namespace App\Traits;

trait GitMethods
{
    /**
     * @var string $cloneUrl
     */
    private $cloneUrl;

    /**
     * Exits application on invalid clone url.
     *
     * @param string $url
     *
     * @return bool
     */
    public function validCloneUrl(string $url): bool
    {
        if (!preg_match('/http.*\.git/', $url) || filter_var($url, FILTER_VALIDATE_URL)  === false) {
            if ($url !== 'placeholder') {
                $this->consoleError('Invalid clone url given, please check again!');
            }
            return false;
        }
        return true;
    }


    /**
     * Gets clone url.
     *
     * @return void
     */
    public function getCloneUrl()
    {
        if (!$this->gitIsInstalled()) {
            $this->consoleError('Git doesn\'t seems to be installed or isn\'t available on the PATH, please configure git.');
            exit;
        }
        $cloneUrl = $this->option('clone-url') ?: 'placeholder';
        while (! $this->validCloneUrl($cloneUrl)) {
            $cloneUrl = $this->ask('What\'s the repository clone url?');
        }
        $this->cloneUrl = $cloneUrl;
    }


    /**
     * Gets the branch name.
     *
     * @return void
     */
    public function getBranch()
    {
        $this->branch = $this->option('branch') ?: $this->ask('What\'s the branch\'s name?');
    }


    /**
     * @return void
     */
    public function manageVCS()
    {
        exec('git clone ' .  $this->cloneUrl . ' --progress ' . config('app.web-root') . '/' . $this->project . '/');
        if ($this->option('branch')) {
            exec('git --git-dir=' . config('app.web-root') . '/' . $this->project . '/.git/  branch ' . $this->option('branch'));
            exec('git --git-dir=' . config('app.web-root') . '/' . $this->project . '/.git/  checkout ' . $this->option('branch'));
        }
    }


    /**
     * @return bool
     */
    public function gitIsInstalled() :bool
    {
        return strpos(exec('git --version'), 'git version') !== -1;
    }
}
