<?php

namespace Febalist\LaravelSupport\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

class Handler extends ExceptionHandler
{
    public function renderForConsole ($output, Exception $e)
    {
        if ($this->whoops()) {
            $whoops = new Whoops;
            $whoops->pushHandler(new PlainTextHandler());
            $whoops->writeToOutput(true);
            $whoops->handleException($e);
        }
        parent::renderForConsole($output, $e);
    }

    protected function convertExceptionToResponse (Exception $e)
    {
        if (!$this->isHttpException($e) && $this->whoops()) {
            $whoops = new Whoops;
            if (request()->expectsJson()) {
                $whoops->pushHandler(new JsonResponseHandler());
            } else {
                $whoops->pushHandler(new PrettyPageHandler());
            }
            return new Response($whoops->handleException($e), 500);
        }
        return parent::convertExceptionToResponse($e);
    }

    protected function whoops ()
    {
        return config('app.debug') && class_exists(Whoops::class);
    }
}

