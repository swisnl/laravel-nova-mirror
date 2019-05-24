<?php

namespace App;


use Gitonomy\Git\Repository;

class NovaUpdateService
{

    /** @var Repository */
    private $repository;

    public function getRemote()
    {
        return env('NOVA_PRIVATE_REPO_REMOTE', 'git@bitbucket.org:bbrala/laravel-nova');
    }

    /**
     * @return Repository Clones or resets the repository containing the Nova mirror
     */
    public function loadRepository(): Repository
    {
        $repoPath = storage_path('app/nova-repository');
        $remote = $this->getRemote();

        $alreadyCloned = file_exists($repoPath);
        if ($alreadyCloned) {
            $repository = new Repository($repoPath);
            $repository->run('clean', ['-d', '-x', '-f']);
        } else {
            $repository = \Gitonomy\Git\Admin::cloneTo($repoPath, $remote, false);
        }

        $this->setRepository($repository);

        return $repository;
    }

    public function repositoryHasTag(string $tag)
    {
        return $this->repository->getReferences()->hasTag($tag);
    }

    public function getVersion(string $href)
    {
        $parts = explode('/', ltrim(parse_url($href, PHP_URL_PATH), '/'));

        return $parts[1];
    }

    /**
     * @param mixed $repository
     */
    public function setRepository($repository): void
    {
        $this->repository = $repository;
    }

    public function createRelease($version, string $message)
    {
        $this->repository->run('add', ['.']);
        $this->repository->run(
            'commit',
            [
                '-m',
                'Nova release '.str_replace("'", "\\'", $version),
                '-m',
                $message,
            ]
        );
        $this->repository->run('tag', ['v'.$version]);
    }

    public function pushRelease()
    {
        $this->repository->run('push');
        $this->repository->run('push', ['--tags']);
    }
}
