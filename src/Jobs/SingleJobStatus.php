<?php

namespace Febalist\Laravel\Support\Jobs;

class SingleJobStatus
{
    const FREE = 0;
    const WAITING = 1;
    const PROCESSING = 2;
    const RELEASE = 3;
}
