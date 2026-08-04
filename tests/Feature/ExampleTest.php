<?php

declare(strict_types=1);

it('serves the Vue application shell', function () {
    $this->withoutVite();

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('<title>Vue is ready</title>', false)
        ->assertSee('<div id="app"></div>', false);
});
