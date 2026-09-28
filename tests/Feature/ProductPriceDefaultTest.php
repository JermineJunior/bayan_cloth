<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * A product must be creatable without entering prices: blank prices are
 * normalised to 0 for every unit row (including the base unit) and stored as
 * 0 rather than null.
 */
class ProductPriceDefaultTest extends TestCase
{
    /** @var array<string, array<int, array>> */
    private array $snapshot = [];

    protected function setUp(): void
    {
        parent::setUp();

        // The store() action runs its own DB::commit(), so a wrapping
        // transaction cannot be used to undo it. Snapshot instead.
        foreach (['products', 'product_units', 'product_stocks'] as $table) {
            $this->snapshot[$table] = DB::table($table)->get()->map(fn ($r) => (array) $r)->all();
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->snapshot as $table => $rows) {
            DB::table($table)->delete();
            foreach ($rows as $row) {
                DB::table($table)->insert($row);
            }
        }

        parent::tearDown();
    }

    private function payload(array $units): array
    {
        return [
            'barcode' => 'T'.random_int(100000, 999999),
            'name' => 'منتج '.uniqid(),
            'category_id' => DB::table('categories')->min('id'),
            'reorder_level' => 5,
            'unit_reorder_level' => $units[0]['unit_id'],
            'units' => $units,
        ];
    }

    private function asAdmin(): User
    {
        $user = User::first();
        $this->actingAs($user);

        return $user;
    }

    public function test_blank_prices_are_stored_as_zero(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $response = $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'price' => '', 'price_purchase' => '', 'conversion_factor' => 1],
        ]));

        $response->assertRedirect(route('product.index'));
        $this->assertSame(0, (int) DB::table('product_units')->latest('id')->value('price'));
        $this->assertSame(0, (int) DB::table('product_units')->latest('id')->value('price_purchase'));
    }

    public function test_missing_price_keys_are_stored_as_zero(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        // price / price_purchase absent entirely from the posted rows
        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'conversion_factor' => 1],
        ]))->assertRedirect(route('product.index'));

        $row = DB::table('product_units')->latest('id')->first();
        $this->assertSame(0, (int) $row->price);
        $this->assertSame(0, (int) $row->price_purchase);
    }

    public function test_every_unit_row_defaults_to_zero_including_the_base_row(): void
    {
        $this->asAdmin();

        $unitIds = DB::table('units')->pluck('id')->take(2)->all();
        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitIds[0], 'price' => '', 'price_purchase' => '', 'conversion_factor' => 1],
            ['unit_id' => $unitIds[1], 'conversion_factor' => 6],
        ]))->assertRedirect(route('product.index'));

        $productId = DB::table('product_units')->latest('id')->value('product_id');
        $rows = DB::table('product_units')->where('product_id', $productId)->get();

        $this->assertCount(2, $rows);
        foreach ($rows as $row) {
            $this->assertSame(0, (int) $row->price, 'row '.$row->id.' price');
            $this->assertSame(0, (int) $row->price_purchase, 'row '.$row->id.' price_purchase');
            $this->assertNotNull($row->price);
        }
    }

    public function test_explicit_zero_prices_are_kept(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'price' => 0, 'price_purchase' => 0, 'conversion_factor' => 1],
        ]))->assertRedirect(route('product.index'));

        $this->assertSame(0, (int) DB::table('product_units')->latest('id')->value('price'));
    }

    public function test_real_prices_are_not_altered(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'price' => '12.50', 'price_purchase' => '7.25', 'conversion_factor' => 1],
        ]))->assertRedirect(route('product.index'));

        $row = DB::table('product_units')->latest('id')->first();
        $this->assertSame('12.50', $row->price);
        $this->assertSame('7.25', $row->price_purchase);
    }

    public function test_negative_price_is_rejected(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $before = DB::table('product_units')->count();

        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'price' => -5, 'price_purchase' => 0, 'conversion_factor' => 1],
        ]))->assertSessionHasErrors('units.0.price');

        $this->assertSame($before, DB::table('product_units')->count());
    }

    public function test_non_numeric_price_is_rejected(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'price' => 'abc', 'price_purchase' => 0, 'conversion_factor' => 1],
        ]))->assertSessionHasErrors('units.0.price');
    }

    public function test_create_view_defaults_both_price_inputs_to_zero(): void
    {
        $this->asAdmin();

        $html = $this->get(route('product.create'))->assertOk()->getContent();

        // every rendered price input (base row + the JS row template) starts at 0
        preg_match_all('/<input[^>]*name="units\[[^\]]*\]\[(price|price_purchase)\]"[^>]*>/', $html, $m);

        $this->assertCount(4, $m[0], 'base row (2) + dynamic row template (2)');
        foreach ($m[0] as $tag) {
            $this->assertMatchesRegularExpression('/\svalue="0"/', $tag, $tag);
            $this->assertStringNotContainsString('required', $tag, $tag);
        }
    }

    public function test_create_view_repopulates_old_prices_after_a_validation_error(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $this->from(route('product.create'))
            ->post(route('product.store'), [
                'name' => '',                                   // force an error
                'category_id' => DB::table('categories')->min('id'),
                'units' => [['unit_id' => $unitId, 'price' => '3.75', 'price_purchase' => '', 'conversion_factor' => 1]],
            ]);

        $html = $this->get(route('product.create'))->assertOk()->getContent();
        $this->assertStringContainsString('value="3.75"', $html, 'submitted price is restored');
    }

    public function test_updating_a_unit_with_a_blank_price_saves_zero(): void
    {
        $this->asAdmin();

        $unitId = DB::table('units')->min('id');
        $this->post(route('product.store'), $this->payload([
            ['unit_id' => $unitId, 'price' => 10, 'price_purchase' => 4, 'conversion_factor' => 1],
        ]))->assertRedirect(route('product.index'));

        $productId = DB::table('product_units')->latest('id')->value('product_id');

        $this->put(route('product.update', $productId), [
            'barcode' => 'T'.random_int(100000, 999999),
            'name' => 'منتج '.uniqid(),
            'category_id' => DB::table('categories')->min('id'),
            'reorder_level' => 5,
            'unit_reorder_level' => $unitId,
            'units' => [
                ['unit_id' => $unitId, 'price' => '', 'price_purchase' => '', 'conversion_factor' => 1],
            ],
        ])->assertRedirect(route('product.index'));

        $row = DB::table('product_units')->where('product_id', $productId)->first();
        $this->assertSame(0, (int) $row->price);
        $this->assertSame(0, (int) $row->price_purchase);
    }
}
