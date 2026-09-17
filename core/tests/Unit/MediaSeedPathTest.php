<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MediaSeedPathTest extends TestCase
{
    private string $source;

    protected function setUp(): void
    {
        parent::setUp();
        $this->source = file_get_contents(
            dirname(__DIR__, 2).'/database/seeders/Tenant/MediaSeed.php'
        );
    }

    public function test_media_seed_uses_the_repository_root_asset_topology(): void
    {
        $this->assertStringContainsString(
            "global_assets_path('assets/tenant/seeder-files/all-media')",
            $this->source
        );
        $this->assertStringContainsString(
            "global_assets_path('assets/tenant/uploads/media-uploader/' . tenant()->id)",
            $this->source
        );
        $this->assertStringNotContainsString(
            "base_path('assets/tenant/seeder-files/all-media')",
            $this->source
        );
    }

    public function test_media_bundle_is_validated_and_copied_before_database_rows_are_seeded(): void
    {
        $validation = strpos($this->source, 'if (! is_dir($source_dir) || ! is_readable($source_dir))');
        $copy = strpos($this->source, '$this->recursive_files_copy($source_dir, $destination_dir);');
        $seed = strpos($this->source, '$this->seedMediaUploaderFiles();');

        $this->assertNotFalse($validation);
        $this->assertNotFalse($copy);
        $this->assertNotFalse($seed);
        $this->assertLessThan($copy, $validation);
        $this->assertLessThan($seed, $copy);
    }
}
