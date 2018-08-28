<?php

namespace Tests\Browser;

use App\NovaFilesHelper;
use App\NovaUpdateService;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NovaUpdaterTest extends DuskTestCase
{
    /**
     * @test
     */
    public function updateNovaMirror()
    {
        $updateService = new NovaUpdateService();
        $repository = $updateService->loadRepository();

        fwrite(STDOUT, '>>> Logging into nova.laravel.com'.PHP_EOL);

        $this->browse(
            function (Browser $browser) use ($updateService, $repository) {
                $browser->visit('https://nova.laravel.com/releases')
                    ->type('email', env('NOVA_DOWNLOAD_EMAIL'))
                    ->type('password', env('NOVA_DOWNLOAD_PASSWORD'))
                    ->press('LOGIN');
                $elements = array_reverse($browser->elements('a[href^="https://nova.laravel.com/releases/"]'));

                fwrite(STDOUT, '>>> Logged into nova.laravel.com, scanning download links'.PHP_EOL);

                foreach ($elements as $element) {
                    $releaseTag = $updateService->getReleaseTag($element->getAttribute('href'));

                    if ($updateService->repositoryHasTag($releaseTag)) {
                        fwrite(STDOUT, '>>> Skipping existing release'.PHP_EOL);
                        continue;
                    }

                    $releasePath = 'downloaded-releases/'.$releaseTag;
                    if (Storage::disk('local')->exists('downloaded-releases/'.$releaseTag) === false) {
                        fwrite(STDOUT, '>>> Downloading new release'.PHP_EOL);
                        $url = $browser->driver->getCommandExecutor()->getAddressOfRemoteServer();
                        $uri = '/session/'.$browser->driver->getSessionID().'/chromium/send_command';
                        $body = [
                            'cmd' => 'Page.setDownloadBehavior',
                            'params' => ['behavior' => 'allow', 'downloadPath' => storage_path('app/'.$releasePath)],
                        ];
                        (new \GuzzleHttp\Client())->post($url.$uri, ['body' => json_encode($body)]);
                        $browser->visit($element->getAttribute('href'));
                    }

                    $filesHelper = new NovaFilesHelper();
                    $releaseFile = $filesHelper->waitForReleaseDownload($releaseTag);
                    $version = $updateService->getVersionFromFilePath($releaseFile);

                    if ($updateService->repositoryHasTag($version)) {
                        fwrite(STDOUT, '>>> Skipping existing version ('.$version.')'.PHP_EOL);
                        continue;
                    }

                    fwrite(STDOUT, '>>> Updating repository'.PHP_EOL);
                    $filesHelper->updateRepositoryFiles($releaseFile, $releasePath);
                    fwrite(STDOUT, '>>> Committing and tagging new release ('.$version.')'.PHP_EOL);
                    $updateService->createRelease($version, $releaseTag);

                    if(env('NOVA_ENABLE_PUSH', true) === false) {
                        fwrite(STDOUT, '>>> Pushing changes to remote is disabled, skipping'.PHP_EOL);
                        continue;
                    }

                    fwrite(STDOUT, '>>> Pushing changes to remote'.PHP_EOL);
                    $updateService->pushRelease();

                }

                fwrite(STDOUT, '>>> Done'.PHP_EOL);
            }
        );


        $this->assertTrue(true);
    }
}
