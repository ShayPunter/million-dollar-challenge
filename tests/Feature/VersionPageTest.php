<?php

use App\Support\DeployedVersion;
use Illuminate\Support\Facades\File;

function fakeGitDir(): string
{
    $dir = sys_get_temp_dir().'/mdg-version-test/git-'.bin2hex(random_bytes(4));
    File::ensureDirectoryExists($dir.'/refs/heads');

    return $dir;
}

afterEach(function () {
    File::deleteDirectory(sys_get_temp_dir().'/mdg-version-test');
});

test('/version answers JSON with no session cookie and no caching', function () {
    $response = $this->get('/version');

    $response->assertOk()
        ->assertJsonStructure(['version', 'commit', 'deployed_at'])
        ->assertHeader('Cache-Control', 'no-store, private');

    expect($response->headers->getCookies())->toBe([]);
});

test('a branch checkout reports the commit from its ref file', function () {
    $dir = fakeGitDir();
    $sha = str_repeat('a1b2c3d4e5', 4);
    File::put($dir.'/HEAD', "ref: refs/heads/main\n");
    File::put($dir.'/refs/heads/main', $sha."\n");
    touch($dir.'/refs/heads/main', 1_794_330_000);

    expect((new DeployedVersion($dir))->toArray())->toBe([
        'version' => 'a1b2c3d',
        'commit' => $sha,
        'deployed_at' => '2026-11-10T17:00:00Z',
    ]);
});

test('a ref that only lives in packed-refs is found', function () {
    $dir = fakeGitDir();
    $sha = str_repeat('0f', 20);
    File::put($dir.'/HEAD', "ref: refs/heads/main\n");
    File::put($dir.'/packed-refs', "# pack-refs with: peeled fully-peeled sorted\n{$sha} refs/heads/main\n");

    expect((new DeployedVersion($dir))->toArray())
        ->version->toBe('0f0f0f0')
        ->commit->toBe($sha);
});

test('a detached HEAD reports its own sha', function () {
    $dir = fakeGitDir();
    $sha = str_repeat('9', 40);
    File::put($dir.'/HEAD', $sha);

    expect((new DeployedVersion($dir))->toArray()['commit'])->toBe($sha);
});

test('no checkout reports unknown', function () {
    expect((new DeployedVersion(sys_get_temp_dir().'/mdg-version-test/missing'))->toArray())->toBe([
        'version' => 'unknown',
        'commit' => 'unknown',
        'deployed_at' => null,
    ]);
});
