<?php

namespace Atlassian\Exceptions;

use Exception;
use Illuminate\Http\Request;

class WebhookException extends Exception
{
    public static function missingSignature(): self
    {
        return new static('The request did not contain a header named `Integrations-Signature`.');
    }

    /**
     * @param array|string $signature
     *
     * @return self
     */
    public static function invalidSignature($signature): self
    {
        return new static("The signature `{$signature}` found in the header named `Integrations-Signature` is invalid. Make sure that the use has configured the webhook field to the value you on the Socrates dashboard.");
    }

    public static function signingSecretNotSet(): self
    {
        return new static('The Integrations webhook signing secret is not set. Make sure that the user has configured the webhook field to the value on the Socrates dashboard.');
    }

    public static function missingType(): self
    {
        return new static('The webhook call did not contain a type. Valid Integrations webhook calls should always contain a type.');
    }

    public static function unrecognizedType(string $type): self
    {
        return new static("The type {$type} is not currently supported.");
    }
}
