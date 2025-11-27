<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan AdminSeeder tidak bentrok, kita hapus user Admin bawaan jika ada
        // atau kita pastikan UserSeeder dipanggil setelah AdminSeeder jika AdminSeeder membuat user 'admin@mail.com'

        // --- ADMIN (PENTING: Untuk Akses Penuh) ---
        // Jika Anda sudah punya user ini dari AdminSeeder, ini akan membuat user kedua.
        // Jika AdminSeeder sudah berjalan, Anda bisa mengomentari baris ini.
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        // --- PEGAWAI (Untuk Proses Loan & Pengembalian) ---
        User::create([
            'name' => 'Budi Pegawai',
            'email' => 'pegawai@mail.com',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        // --- MAHASISWA AKTIF (Normal Loan) ---
        User::create([
            'name' => 'Citra Mahasiswa Aktif',
            'email' => 'citra@mail.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        // --- MAHASISWA DIBLOKIR (Testing Denda) ---
        // Status is_blocked = true, harus diblokir saat mencoba pinjam
        User::create([
            'name' => 'Dinda Mahasiswa Diblokir',
            'email' => 'dinda@mail.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_blocked' => true,
            'email_verified_at' => now(),
        ]);

        // --- MAHASISWA BARU (Testing Reservasi) ---
        User::create([
            'name' => 'Eko Mahasiswa Baru',
            'email' => 'eko@mail.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'is_blocked' => false,
            'email_verified_at' => now(),
        ]);

        // Buat 10 Mahasiswa lainnya menggunakan factory
        User::factory(10)->create(['role' => 'mahasiswa']);
    }
}
