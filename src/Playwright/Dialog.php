<?php

declare(strict_types=1);

namespace Pest\Browser\Playwright;

use Pest\Browser\Playwright\Concerns\InteractsWithPlaywright;

/**
 * @internal
 */
final class Dialog
{
    use InteractsWithPlaywright;

    /**
     * Creates a new dialog instance.
     */
    public function __construct(
        private readonly string $guid,
        private readonly string $type,
        private readonly string $message,
        private readonly string $defaultValue,
    ) {
        //
    }

    /**
     * Returns the dialog's GUID for debugging.
     */
    public function guid(): string
    {
        return $this->guid;
    }

    /**
     * Returns the dialog's message.
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * Returns the dialog's type (alert, confirm, prompt, beforeunload).
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Returns the dialog's default value (for prompt dialogs).
     */
    public function defaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * Accepts the dialog.
     * For prompt dialogs, you can provide text input.
     */
    public function accept(?string $promptText = null): void
    {
        $params = [];
        if ($promptText !== null) {
            $params['promptText'] = $promptText;
        }

        $response = $this->sendMessage('accept', $params);
        $this->processVoidResponse($response);
    }

    /**
     * Dismisses the dialog.
     */
    public function dismiss(): void
    {
        $response = $this->sendMessage('dismiss');
        $this->processVoidResponse($response);
    }

    /**
     * Send a message to the dialog via the channel.
     *
     * @param  array<string, mixed>  $params
     */
    private function sendMessage(string $method, array $params = []): \Generator
    {
        return Client::instance()->execute($this->guid, $method, $params);
    }
}