<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\Kasir;
use App\Models\Owner;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Database\Seeders\BarangSeeder;
use Database\Seeders\PelangganSeeder;
use Database\Seeders\TransaksiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PosIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_accounts_can_log_in_and_roles_are_enforced(): void
    {
        $owner = Owner::create([
            'nama' => 'Pemilik',
            'username' => 'pemilik',
            'password' => 'rahasia',
            'no_telp' => '081234567890',
        ]);
        $this->assertTrue(Hash::check('rahasia', $owner->fresh()->password));

        $this->post(route('login.store'), [
            'username' => 'pemilik',
            'password' => 'rahasia',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('transaksi.create'))->assertForbidden();
        $this->get(route('barang.index'))->assertForbidden();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_manage_products_and_customers(): void
    {
        $admin = Admin::create([
            'nama' => 'Admin',
            'username' => 'admin-test',
            'password' => 'rahasia',
            'no_telp' => '081234567890',
        ]);
        $session = ['pos_role' => 'admin', 'pos_user_id' => $admin->id_admin];

        $this->withSession($session)->get(route('barang.create'))->assertOk();
        $this->withSession($session)->post(route('barang.store'), [
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'TEST-01',
            'nama_barang' => 'Barang Uji',
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'stok' => 5,
        ])->assertRedirect(route('barang.index'));
        $barang = Barang::where('kode_barang', 'TEST-01')->firstOrFail();
        $this->withSession($session)->get(route('barang.edit', $barang))->assertOk();

        $this->withSession($session)->put(route('barang.update', $barang), [
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'TEST-01',
            'nama_barang' => 'Barang Diubah',
            'harga_beli' => 1100,
            'harga_jual' => 1600,
            'stok' => 7,
        ])->assertRedirect(route('barang.index'));

        $this->withSession($session)->get(route('pelanggan.create'))->assertOk();
        $this->withSession($session)->post(route('pelanggan.store'), [
            'nama' => 'Pelanggan Uji',
            'no_telp' => '081111111111',
        ])->assertRedirect(route('pelanggan.index'));
        $pelanggan = Pelanggan::where('nama', 'Pelanggan Uji')->firstOrFail();

        $this->withSession($session)->get(route('pelanggan.search', ['q' => '081111']))
            ->assertOk()
            ->assertJsonFragment(['id_pelanggan' => $pelanggan->id_pelanggan]);
        $kasir = Kasir::create(['nama' => 'Kasir', 'username' => 'kasir-search', 'password' => 'secret']);
        $this->withSession(['pos_role' => 'kasir', 'pos_user_id' => $kasir->id_kasir])
            ->get(route('pelanggan.search', ['q' => '081111']))
            ->assertOk();

        $this->assertSame('Barang Diubah', $barang->fresh()->nama_barang);

        $this->withSession($session)->delete(route('barang.destroy', $barang))
            ->assertRedirect(route('barang.index'));
        $this->withSession($session)->delete(route('pelanggan.destroy', $pelanggan))
            ->assertRedirect(route('pelanggan.index'));
        $this->assertDatabaseMissing('Barang', ['id_barang' => $barang->id_barang]);
        $this->assertDatabaseMissing('Pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
    }

    public function test_transaction_saves_details_and_decrements_stock_atomically(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'username' => 'admin-tx', 'password' => 'secret']);
        $kasir = Kasir::create(['nama' => 'Kasir', 'username' => 'kasir-tx', 'password' => 'secret']);
        $owner = Owner::create(['nama' => 'Owner', 'username' => 'owner-tx', 'password' => 'secret']);
        $pelanggan = Pelanggan::create(['nama' => 'Pelanggan']);
        $barang = Barang::create([
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'TX-01',
            'nama_barang' => 'Kopi',
            'harga_beli' => 2000,
            'harga_jual' => 3500,
            'stok' => 10,
        ]);
        $session = ['pos_role' => 'kasir', 'pos_user_id' => $kasir->id_kasir];

        $this->withSession($session)->get(route('transaksi.create'))->assertOk()->assertSee('Buat transaksi');
        $this->withSession($session)->post(route('transaksi.store'), [
            'id_kasir' => 999999,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'uang_bayar' => 10000,
            'items' => [['id_barang' => $barang->id_barang, 'jumlah' => 2]],
        ])->assertRedirect();

        $transaksi = Transaksi::with('detailTransaksis')->firstOrFail();
        $this->assertSame($kasir->id_kasir, $transaksi->id_kasir);
        $this->assertSame($pelanggan->id_pelanggan, $transaksi->id_pelanggan);
        $this->assertSame('7000.00', $transaksi->total_harga);
        $this->assertSame('3000.00', $transaksi->kembalian);
        $this->assertSame(8, $barang->fresh()->stok);
        $this->assertSame(1, $transaksi->detailTransaksis->count());
        $this->assertSame('7000.00', $transaksi->detailTransaksis->first()->subtotal);

        $this->withSession($session)->get(route('transaksi.show', $transaksi))->assertOk();
        $this->withSession(['pos_role' => 'owner', 'pos_user_id' => $owner->id_owner])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp7.000')
            ->assertSee('Rp3.000')
            ->assertSee('Kopi');
    }

    public function test_barang_and_pelanggan_used_by_transactions_cannot_be_deleted(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'username' => 'admin-delete', 'password' => 'secret']);
        $kasir = Kasir::create(['nama' => 'Kasir', 'username' => 'kasir-delete', 'password' => 'secret']);
        $pelanggan = Pelanggan::create(['nama' => 'Pelanggan terkait']);
        $barang = Barang::create([
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'LOCK-01',
            'nama_barang' => 'Barang terkait',
            'harga_beli' => 10,
            'harga_jual' => 20,
            'stok' => 1,
        ]);
        $transaksi = Transaksi::create([
            'tanggal_transaksi' => now(),
            'id_kasir' => $kasir->id_kasir,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'total_harga' => 20,
            'uang_bayar' => 20,
            'kembalian' => 0,
        ]);
        $transaksi->detailTransaksis()->create([
            'id_barang' => $barang->id_barang,
            'jumlah' => 1,
            'harga_satuan' => 20,
            'harga_beli_satuan' => 10,
            'subtotal' => 20,
        ]);

        $this->withSession(['pos_role' => 'admin', 'pos_user_id' => $admin->id_admin])
            ->delete(route('barang.destroy', $barang))
            ->assertSessionHasErrors('barang');
        $this->withSession(['pos_role' => 'admin', 'pos_user_id' => $admin->id_admin])
            ->delete(route('pelanggan.destroy', $pelanggan))
            ->assertSessionHasErrors('pelanggan');

        $this->assertDatabaseHas('Barang', ['id_barang' => $barang->id_barang]);
        $this->assertDatabaseHas('Pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
    }

    public function test_transaction_rejects_insufficient_payment_without_changing_stock(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'username' => 'admin-short', 'password' => 'secret']);
        $kasir = Kasir::create(['nama' => 'Kasir', 'username' => 'kasir-short', 'password' => 'secret']);
        $barang = Barang::create([
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'TX-02',
            'nama_barang' => 'Air',
            'harga_beli' => 1000,
            'harga_jual' => 2500,
            'stok' => 4,
        ]);

        $this->withSession(['pos_role' => 'kasir', 'pos_user_id' => $kasir->id_kasir])
            ->from(route('transaksi.create'))
            ->post(route('transaksi.store'), [
                'id_kasir' => $kasir->id_kasir,
                'uang_bayar' => 2000,
                'items' => [['id_barang' => $barang->id_barang, 'jumlah' => 1]],
            ])
            ->assertRedirect(route('transaksi.create'))
            ->assertSessionHasErrors('uang_bayar');

        $this->assertSame(4, $barang->fresh()->stok);
        $this->assertSame(0, Transaksi::count());
        $this->assertSame(0, DetailTransaksi::count());
    }

    public function test_transaction_rejects_insufficient_stock_without_creating_records(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'username' => 'admin-stock', 'password' => 'secret']);
        $kasir = Kasir::create(['nama' => 'Kasir', 'username' => 'kasir-stock', 'password' => 'secret']);
        $barang = Barang::create([
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'TX-03',
            'nama_barang' => 'Stok terbatas',
            'harga_beli' => 1000,
            'harga_jual' => 2500,
            'stok' => 1,
        ]);

        $this->withSession(['pos_role' => 'kasir', 'pos_user_id' => $kasir->id_kasir])
            ->from(route('transaksi.create'))
            ->post(route('transaksi.store'), [
                'id_kasir' => $kasir->id_kasir,
                'uang_bayar' => 10000,
                'items' => [['id_barang' => $barang->id_barang, 'jumlah' => 2]],
            ])
            ->assertRedirect(route('transaksi.create'))
            ->assertSessionHasErrors('items');

        $this->assertSame(1, $barang->fresh()->stok);
        $this->assertSame(0, Transaksi::count());
        $this->assertSame(0, DetailTransaksi::count());
    }

    public function test_customer_seeder_reaches_target_and_is_idempotent(): void
    {
        $seeder = new PelangganSeeder;
        $seeder->run();
        $this->assertSame(100_000, Pelanggan::count());

        $seeder->run();
        $this->assertSame(100_000, Pelanggan::count());
    }

    public function test_barang_seeder_does_not_reset_existing_stock(): void
    {
        $admin = Admin::create(['nama' => 'Admin', 'username' => 'admin', 'password' => 'secret']);
        Barang::create([
            'id_admin' => $admin->id_admin,
            'kode_barang' => 'SKU-001',
            'nama_barang' => 'Stok yang sudah diubah',
            'harga_beli' => 2000,
            'harga_jual' => 3500,
            'stok' => 9,
        ]);

        $this->seed(BarangSeeder::class);

        $this->assertSame(9, Barang::where('kode_barang', 'SKU-001')->value('stok'));
        $this->assertSame('Stok yang sudah diubah', Barang::where('kode_barang', 'SKU-001')->value('nama_barang'));
    }

    public function test_demo_transactions_create_consistent_history_and_seed_idempotently(): void
    {
        foreach (range(1, 3) as $number) {
            Kasir::create([
                'nama' => "Kasir {$number}",
                'username' => "kasir-demo-{$number}",
                'password' => 'secret',
            ]);
        }

        foreach (range(1, 20) as $number) {
            Pelanggan::create(['nama' => "Pelanggan {$number}"]);
        }

        $admin = Admin::create([
            'nama' => 'Admin demo',
            'username' => 'admin-demo',
            'password' => 'secret',
        ]);

        foreach (range(1, 20) as $number) {
            Barang::create([
                'id_admin' => $admin->id_admin,
                'kode_barang' => sprintf('DEMO-SKU-%03d', $number),
                'nama_barang' => "Barang {$number}",
                'harga_beli' => 1000 + $number,
                'harga_jual' => 2000 + $number,
                'stok' => 100,
            ]);
        }

        $seeder = new TransaksiSeeder;
        $seeder->run();

        $this->assertSame(100_000, Transaksi::count());
        $this->assertGreaterThan(100_000, DetailTransaksi::count());
        $this->assertSame(100_000, Transaksi::whereNotNull('kode_transaksi')->distinct('kode_transaksi')->count());
        $this->assertSame(0, Transaksi::whereNull('id_kasir')->orWhereNull('id_pelanggan')->count());
        $this->assertSame(
            0,
            DB::table('Transaksi as transaksi')
                ->leftJoin('Detail_Transaksi as detail', 'detail.id_transaksi', '=', 'transaksi.id_transaksi')
                ->whereNotNull('transaksi.kode_transaksi')
                ->selectRaw('transaksi.id_transaksi')
                ->groupBy('transaksi.id_transaksi', 'transaksi.total_harga')
                ->havingRaw('COUNT(detail.id_detail) = 0 OR SUM(detail.subtotal) != transaksi.total_harga')
                ->get()
                ->count(),
        );

        DB::table('Transaksi')
            ->where('kode_transaksi', 'DEMO-000001')
            ->update(['id_pelanggan' => null]);

        $seeder->run();
        $this->assertSame(100_000, Transaksi::count());
        $this->assertSame(0, Transaksi::whereNull('id_pelanggan')->count());
    }
}
