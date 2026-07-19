<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

class CreateInternalOauthClient extends Command
{
    protected $signature = 'auth:client
        {--name=Default API Client : Client name}
        {--confidential : Require a client secret}';

    protected $description = 'Create an OAuth client restricted to the internal authentication grants';

    public function handle(): int
    {
        $secret = $this->option('confidential') ? Str::random(40) : null;

        /** @var Client $client */
        $client = Passport::client()->newQuery()->forceCreate([
            'name'          => (string) $this->option('name'),
            'secret'        => $secret,
            'provider'      => config('auth.guards.api.provider'),
            'redirect_uris' => [],
            'grant_types'   => ['internal', 'internal_refresh_token', 'refresh_token'],
            'revoked'       => false,
        ]);

        $this->components->info('Internal OAuth client created successfully.');
        $this->components->twoColumnDetail('Client ID', (string) $client->getKey());

        if ($client->confidential()) {
            $this->components->twoColumnDetail('Client Secret', (string) $client->plainSecret);
            $this->components->warn('The client secret will not be shown again.');
        }

        return self::SUCCESS;
    }
}
