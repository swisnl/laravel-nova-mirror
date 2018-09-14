<?php
/**
 * Created by PhpStorm.
 * User: bjorn
 * Date: 26-8-2018
 * Time: 13:17
 */

namespace App;


use Illuminate\Support\Facades\Storage;

class NovaFilesHelper
{
    public function waitForReleaseDownload(string $releaseTag): string
    {
        $filesystem = Storage::disk('local');
        $i = 0;
        $directory = 'downloaded-releases/'.$releaseTag;
        $releaseFiles = $filesystem->files($directory);
        while (count($releaseFiles) === 0) {
            sleep(1);
            $releaseFiles = $filesystem->files($directory);

            if ($i++ > 5) {
                throw new \RuntimeException('Waiting for release file in `downloaded-releases` timed out.');
            }
        }

        $releaseFile = str_replace('.crdownload', '', array_pop($releaseFiles));

        $i = 0;
        clearstatcache();
        $zipPath = storage_path('app/'.$releaseFile);
        while (file_exists($zipPath) === false) {
            sleep(1);
            clearstatcache(false, $zipPath);

            if ($i++ > 5) {
                throw new \RuntimeException('Waiting for existence of zip-file timed out.');
            }
        }

        $i = 0;
        while (filemtime($zipPath) > time() - 2) {
            sleep(1);
            clearstatcache(false, $zipPath);

            if ($i++ > 5) {
                throw new \RuntimeException('Waiting for end of writes timed out.');
            }
        }

        return $releaseFile;
    }


    /**
     * @param $releaseFile string Zipped nova release file
     * @param $releasePath string Path to extract nova files to
     */
    public function updateRepositoryFiles($releaseFile, $releasePath)
    {
        $filesystem = Storage::disk('local');
        $files = $filesystem->files('nova-repository');
        $filesystem->delete($files);

        $directories = $filesystem->directories('nova-repository');
        foreach ($directories as $directory) {
            if ($directory === 'nova-repository/.git') {
                continue;
            }

            $filesystem->deleteDirectory($directory);
        }

        $zip = new \ZipArchive();
        $zip->open(storage_path('app/'.$releaseFile));

        $novaReleaseDirectory = $zip->getNameIndex(0);
        $zip->extractTo(storage_path('app/'.$releasePath));
        $zip->close();

        $novaFiles = $filesystem->files($releasePath.'/'.$novaReleaseDirectory);
        $novaDirectories = $filesystem->directories($releasePath.'/'.$novaReleaseDirectory);

        foreach ($novaFiles as $file) {
            $filesystem->copy($file, 'nova-repository/'.pathinfo($file, PATHINFO_BASENAME));
        }
        foreach ($novaDirectories as $directory) {
            $filesystem->move($directory, 'nova-repository/'.pathinfo($directory, PATHINFO_BASENAME));
        }
    }

}
