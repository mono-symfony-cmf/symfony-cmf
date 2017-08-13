<?php

/**
 * Checks whether a given branch name exists in the source git.
 *
 * @param $branchName
 *
 * @return bool
 */
function branchExistsOnSource($branchName)
{
    exec(sprintf('git branch --list %s | wc -l', $branchName), $output, $exitCode);
    if (1 > count($output) || 1 != array_shift($output)) {
        if ($exitCode) {
            exit($exitCode);
        }

        return false;
    }

    return true;
}

/**
 * Simple lookup if the project name is in the list of given projects.
 *
 * @param $remoteName string
 * @param $projects []
 *
 * * @return string
 */
function getProjectDirectory($remoteName, $projects)
{
    if (!isset($projects[$remoteName])) {
        exit(sprintf('Remote %s does not exist in the configured list of projects.', $remoteName));
    }

    return $projects[$remoteName];
}

/**
 * By checking the remote repository this method tests if the remote for this project is reachable.
 *
 * @param $remoteName
 *
 * @return bool
 */
function remoteExists($remoteName)
{
    exec(sprintf('git ls-remote %s | wc -l', $remoteName), $output, $exitCode);
    if (1 > count($output) || 0 == array_shift($output)) {
        if ($exitCode) {
            exit($exitCode);
        }

        return false;
    }

    return true;
}

/**
 * Checks out the branch on source.
 *
 * @param $branchName
 * @param bool $createFromIfNotExist
 *
 * @return bool
 */
function checkout($branchName, $createFromIfNotExist = false)
{
    if ($branchName === currentBranch()) {
        echo "Current branch is correct. Lets Stay.\n";

        return true;
    }

    if ($createFromIfNotExist && !branchExistsOnSource($branchName)) {
        if ($createFromIfNotExist !== $createFromIfNotExist) {
            execute(sprintf('git checkout %s', $createFromIfNotExist));
        }

        return execute(sprintf('git checkout -b %s', $branchName));
    }

    return execute(sprintf('git checkout %s', $branchName));
}

/**
 * @return bool|string
 */
function currentBranch()
{
    return exec('git rev-parse --abbrev-ref HEAD');
}

function subtree($method, $prefix, $remote, $branch)
{
    $message = sprintf('%s %s/%s in directory %s.', ucfirst($method), $remote, $branch, $prefix);

    $command = sprintf(
        'git subtree %s --prefix=%s %s %s -m "%s"',
        $method,
        $prefix,
        $remote,
        $branch,
        $message
    );

    return execute($command);
}

function execute($command)
{
    if (DRY_RUN) {
        echo $command.PHP_EOL;

        return;
    }
    echo "Execute Command: '$command'\n";

    if (!system($command, $exitCode)) {
        if ($exitCode) {
            echo "Unable execute command: $command\n ";
        }

        return false;
    }

    return true;
}
