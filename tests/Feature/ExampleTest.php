<?php

declare(strict_types=1);

it('redirects guests to the login page', function (): void {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
