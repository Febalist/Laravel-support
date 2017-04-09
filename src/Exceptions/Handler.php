<?php

namespace Febalist\LaravelSupport\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

class Handler extends ExceptionHandler
{
    public function renderForConsole($output, Exception $e)
    {
        if ($this->whoops()) {
            $whoops = new Whoops();
            $whoops->pushHandler(new PlainTextHandler());
            $whoops->writeToOutput(true);
            $whoops->handleException($e);
        }
        parent::renderForConsole($output, $e);
    }

    protected function renderHttpException(HttpException $e)
    {
        return $this->convertExceptionToResponse($e);
    }

    protected function convertExceptionToResponse(Exception $e)
    {
        if (!$this->isHttpException($e) && $this->whoops()) {
            $whoops = new Whoops();
            if (request()->expectsJson()) {
                $whoops->pushHandler(new JsonResponseHandler());
            } else {
                $whoops->pushHandler(new PrettyPageHandler());
            }

            return new Response($whoops->handleException($e), 500);
        }

        $status = 500;
        $headers = [];
        $data = ['exception' => $e];
        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();
            $headers = $e->getHeaders();
        }

        if (view()->exists("errors.$status")) {
            return response()->view("errors.$status", $data, $status);
        }

        if (view()->exists("support::errors.$status")) {
            return response()->view("support::errors.$status", $data, $status, $headers);
        }

        return parent::convertExceptionToResponse($e);
    }

    protected function whoops()
    {
        return config('app.debug') && class_exists(Whoops::class);
    }
}
