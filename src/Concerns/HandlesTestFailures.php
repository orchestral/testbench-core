<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Testing\AssertableJsonString;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionProperty;
use Throwable;

trait HandlesTestFailures
{
    /**
     * This method is called when a test method did not execute successfully.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function onNotSuccessfulTest(Throwable $exception): void
    {
        if (! $exception instanceof ExpectationFailedException || is_null(static::$latestResponse)) {
            parent::onNotSuccessfulTest($exception);
        }

        if ($lastException = static::$latestResponse->exceptions->last()) {
            parent::onNotSuccessfulTest($this->appendExceptionToException($lastException, $exception));

            return;
        }

        if (static::$latestResponse->baseResponse instanceof RedirectResponse) {
            $session = static::$latestResponse->baseResponse->getSession();

            if (! is_null($session) && $session->has('errors')) {
                parent::onNotSuccessfulTest($this->appendErrorsToException($session->get('errors')->all(), $exception));

                return;
            }
        }

        if (static::$latestResponse->baseResponse->headers->get('Content-Type') === 'application/json') {
            $testJson = new AssertableJsonString(static::$latestResponse->getContent());

            if (isset($testJson['errors'])) {
                parent::onNotSuccessfulTest($this->appendErrorsToException($testJson->json(), $exception, true));

                return;
            }
        }

        parent::onNotSuccessfulTest($exception);
    }

    /**
     * Append an exception to the message of another exception.
     *
     * @param  \Throwable  $exceptionToAppend
     * @param  \Throwable  $exception
     * @return \Throwable
     */
    protected function appendExceptionToException($exceptionToAppend, $exception)
    {
        $exceptionMessage = $exceptionToAppend->getMessage();

        $exceptionToAppend = (string) $exceptionToAppend;

        $message = <<<"EOF"
            The following exception occurred during the last request:
            $exceptionToAppend
            ----------------------------------------------------------------------------------
            $exceptionMessage
            EOF;

        return $this->appendMessageToException($message, $exception);
    }

    /**
     * Append errors to an exception message.
     *
     * @param  array  $errors
     * @param  \Throwable  $exception
     * @param  bool  $json
     * @return \Throwable
     */
    protected function appendErrorsToException($errors, $exception, $json = false)
    {
        $errors = $json
            ? json_encode($errors, JSON_PRETTY_PRINT)
            : implode(PHP_EOL, Arr::flatten($errors));

        // JSON error messages may already contain the errors, so we shouldn't duplicate them...
        if (str_contains($exception->getMessage(), $errors)) {
            return $exception;
        }

        $message = <<<"EOF"
            The following errors occurred during the last request:
            $errors
            EOF;

        return $this->appendMessageToException($message, $exception);
    }

    /**
     * Append a message to an exception.
     *
     * @param  string  $message
     * @param  \Throwable  $exception
     * @return \Throwable
     */
    protected function appendMessageToException($message, $exception)
    {
        $property = new ReflectionProperty($exception, 'message');

        $property->setAccessible(true);

        $property->setValue(
            $exception,
            $exception->getMessage().PHP_EOL.PHP_EOL.$message.PHP_EOL
        );

        return $exception;
    }
}
