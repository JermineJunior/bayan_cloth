<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LogoUploadTest extends TestCase
{
    private $logoDir;

    private $snapshot;

    /**
     * Filenames that were in the logo directory BEFORE this test ran.
     * tearDown only ever deletes files that are NOT in this list, so a real
     * logo uploaded by a user is never removed by the test suite.
     */
    private array $preexisting = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->logoDir = public_path('image/logo');
        $this->snapshot = Setting::first()?->toArray();
        $this->preexisting = array_map(
            fn ($f) => $f->getFilename(),
            File::isDirectory($this->logoDir) ? File::files($this->logoDir) : []
        );

        // Point the setting at a file the controller will never delete, so the
        // replace-logo logic can only ever target files this test creates.
        // Without this, a test upload would delete the real user's uploaded logo.
        Setting::first()->forceFill(['logo' => 'bayan.jpg'])->save();
    }

    protected function tearDown(): void
    {
        if ($this->snapshot) {
            Setting::first()->forceFill($this->snapshot)->save();
        }

        // remove only the files this test created - never anything pre-existing
        foreach (File::files($this->logoDir) as $f) {
            if (! in_array($f->getFilename(), $this->preexisting, true)) {
                unlink($f->getPathname());
            }
        }

        parent::tearDown();
    }

    private function png(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'img').'.png';
        // real 1x1 PNG bytes, so UploadedFile::extension() really resolves to "png"
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function payload(array $overrides = []): array
    {
        $s = Setting::first();

        return array_merge([
            'name' => $s->name,
            'location' => $s->location,
            'email' => $s->email,
            'phone' => $s->phone,
        ], $overrides);
    }

    public function test_settings_page_renders_logo_field_and_preview(): void
    {
        $this->actingAs(User::first())
            ->get('/admin/company/info')
            ->assertOk()
            ->assertSee('شعار المؤسسة')
            ->assertSee('name="logo"', false)
            ->assertSee('id="preview"', false)
            ->assertSee('onchange="previewImage(this)"', false)
            ->assertSee('image/logo/', false);
    }

    public function test_uploading_a_logo_stores_file_and_saves_filename(): void
    {
        $before = count(File::files($this->logoDir));

        $this->actingAs(User::first())
            ->put('/admin/company/info', $this->payload(['logo' => $this->png('my logo.png')]))
            ->assertRedirect();

        $logo = Setting::first()->logo;

        $this->assertMatchesRegularExpression('/^logo_\d+_[A-Za-z0-9]{10}\.png$/', $logo);
        $this->assertFileExists($this->logoDir.'/'.$logo);
        $this->assertSame($before + 1, count(File::files($this->logoDir)));

        // the bare filename must resolve via the 110 asset('image/logo/' . $logo) call sites
        $this->assertFileExists(public_path('image/logo/'.$logo));
    }

    public function test_saving_without_a_file_keeps_the_existing_logo(): void
    {
        $before = Setting::first()->logo;

        $this->actingAs(User::first())
            ->put('/admin/company/info', $this->payload(['name' => 'اسم جديد']))
            ->assertRedirect();

        $this->assertSame('اسم جديد', Setting::first()->name);
        $this->assertSame($before, Setting::first()->logo);
    }

    public function test_replacing_a_logo_deletes_the_previous_upload_but_keeps_bayan_jpg(): void
    {
        $this->actingAs(User::first())
            ->put('/admin/company/info', $this->payload(['logo' => $this->png('a.png')]));
        $first = Setting::first()->logo;

        $this->actingAs(User::first())
            ->put('/admin/company/info', $this->payload(['logo' => $this->png('b.png')]));
        $second = Setting::first()->logo;

        $this->assertNotSame($first, $second);
        $this->assertFileDoesNotExist($this->logoDir.'/'.$first);
        $this->assertFileExists($this->logoDir.'/'.$second);
        // bayan.jpg is hardcoded in sale/pdf.blade.php - must never be deleted
        $this->assertFileExists($this->logoDir.'/bayan.jpg');
    }

    public function test_non_image_upload_is_rejected_with_arabic_error(): void
    {
        $before = Setting::first()->logo;
        $file = UploadedFile::fake()->create('evil.php', 10);

        $this->actingAs(User::first())
            ->put('/admin/company/info', $this->payload(['logo' => $file]))
            ->assertRedirect()
            ->assertSessionHasErrors('logo');

        $this->assertSame($before, Setting::first()->logo);
        $this->assertFileDoesNotExist($this->logoDir.'/evil.php');
    }

    public function test_oversized_upload_over_2mb_is_rejected(): void
    {
        $before = Setting::first()->logo;

        $this->actingAs(User::first())
            ->put('/admin/company/info', $this->payload([
                'logo' => UploadedFile::fake()->create('big.jpg', 3000, 'image/jpeg'),
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('logo');

        $this->assertSame($before, Setting::first()->logo);
    }
}
