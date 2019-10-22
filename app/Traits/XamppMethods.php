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
        $command = config('app.xampp-directory') . '/apache\bin\httpd.exe';
        if (file_exists('C:\xampp\apache\logs\httpd.pid')) {
            //todo
            // doesn't fully restarts, if it did it would require
            // the console to stay open for the process
            $command .= ' -k restart';
        }
        exec($command);
    }
}
