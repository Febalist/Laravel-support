<?php

namespace Febalist\Laravel\Support\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * @mixin Command
 * @deprecated
 * @see https://github.com/febalist/laravel-progress
 */
trait Progress
{
    protected $progressBar;

    public function progressStart($max = 0)
    {
        $this->progressMax($max);
        $this->getProgressBar()->start();
    }

    public function progressAdvance($step = 1)
    {
        $this->getProgressBar()->advance($step);
    }

    public function progressFinish()
    {
        $this->getProgressBar()->finish();
        $this->bar = null;
    }

    public function progressMessage($message = null)
    {
        $this->getProgressBar()->setMessage($message ? " ($message)" : '');
    }

    public function progressMax($max)
    {
        if ($max instanceof Collection) {
            $max = $max->count();
        } elseif (is_array($max)) {
            $max = count($max);
        }

        $this->getProgressBar()->setFormat(" %current% [%bar%] %elapsed:6s%%message%\n");
        $this->getProgressBar()->setMaxSteps($max);
        if ($max) {
            $length = strlen($max);
            $this->getProgressBar()->setFormat(" %current:{$length}s%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%%message%\n");
        }
    }

    protected function getProgressBar()
    {
        if (!$this->progressBar) {
            $this->progressBar = $this->output->createProgressBar();
            $this->progressMax(0);
            $this->progressMessage();
        }

        return $this->progressBar;
    }
}
