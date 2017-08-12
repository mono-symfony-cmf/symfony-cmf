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
