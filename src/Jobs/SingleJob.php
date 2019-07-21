<?php

namespace Febalist\Laravel\Support\Jobs;

/**
 * @deprecated
 * @see https://packagist.org/packages/febalist/laravel-single
 */
trait SingleJob
{
    public static function dispatchSingle(...$arguments)
    {
        $job = new static(...$arguments);

        $status = $job->getSingleJobStatus();

        if ($status == SingleJobStatus::FREE) {
            $job->setSingleJobStatus(SingleJobStatus::WAITING);
            dispatch($job);
        } elseif ($status == SingleJobStatus::PROCESSING) {
            $job->setSingleJobStatus(SingleJobStatus::RELEASE);
        }
    }

    public function beforeHandle()
    {
        $this->setSingleJobStatus(SingleJobStatus::PROCESSING);
    }

    public function afterHandle()
    {
        if ($this->getSingleJobStatus() == SingleJobStatus::RELEASE) {
            $this->setSingleJobStatus(SingleJobStatus::WAITING);
            $arguments = method_exists($this, 'singleJobArguments') ? $this->singleJobArguments() : [];
            static::dispatch(...$arguments);
        } else {
            $this->setSingleJobStatus(SingleJobStatus::FREE);
        }
    }

    public function getSingleJobStatus()
    {
        return cache($this->getSingleJobCacheKey(), SingleJobStatus::FREE);
    }

    public function setSingleJobStatus($status = null)
    {
        if ($status == SingleJobStatus::FREE) {
            cache()->forget($this->getSingleJobCacheKey());
        } else {
            cache()->put($this->getSingleJobCacheKey(), $status, now()->addHours(6));
        }
    }

    protected function getSingleJobCacheKey()
    {
        $hash = [get_class($this)];

        if (method_exists($this, 'singleJobId')) {
            $hash[] = $this->singleJobId();
        }

        $hash = serialize_hash($hash);

        return "single-job:$hash";
    }
}
