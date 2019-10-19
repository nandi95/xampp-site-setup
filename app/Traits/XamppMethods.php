<?php

namespace App\Traits;

/**
 * Trait xamppMethods
 *
 * @package App\Traits
 */
trait XamppMethods
{
    public function updateVHosts()
    {
        $vHost = config('app.xampp-directory') . '/apache/conf/extra/httpd-vhosts.conf';
        $entry = "\n";
        $entry .= file_get_contents(__DIR__ . '\..\entry.stub');
        $entry = str_replace('WEBROOT', config('app.web-root') . '/', $entry);
        $entry = str_replace('PROJECT', $this->project, $entry);

        if (file_exists($vHost)) {
            file_put_contents(
                config('app.xampp-directory') . '/apache/conf/extra/httpd-vhosts.conf',
                $entry,
                FILE_APPEND
            );
        }
    }


    public function restartXampp(): void
    {
        exec(config('app.xampp-directory') . '/apache_stop.bat');
        exec(config('app.xampp-directory') . '/apache_start.bat');
    }
}
