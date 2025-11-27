<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            // BUKU 1: Keuangan (Dipertahankan)
            [
                'title' => 'The Psychology of Money',
                'author' => 'Morgan Housel',
                'publisher' => 'Harriman House',
                'publication_year' => 2020,
                'category' => 'Money/Investing',
                'stock' => 10,
                'max_loan_days' => 7,
                'daily_fine_rate' => 1000.00,
                'description' => 'Lessons on wealth, greed, and happiness. A timeless guide to thinking better about money.',
            ],
            // BUKU 2: Bisnis (Dipertahankan)
            [
                'title' => 'Company of One',
                'author' => 'Paul Jarvis',
                'publisher' => 'Houghton Mifflin Harcourt',
                'publication_year' => 2019,
                'category' => 'Business',
                'stock' => 5,
                'max_loan_days' => 14,
                'daily_fine_rate' => 500.00,
                'description' => 'Why staying small is the next big thing for business. Focuses on growth optimization over constant scaling.',
            ],
            // BUKU 3: Fiksi Klasik (Dipertahankan)
            [
                'title' => 'The Picture of Dorian Gray',
                'author' => 'Oscar Wilde',
                'publisher' => 'Lippincott’s Monthly Magazine',
                'publication_year' => 1890,
                'category' => 'Classic Fiction',
                'stock' => 15,
                'max_loan_days' => 21,
                'daily_fine_rate' => 250.00,
                'description' => 'A cautionary tale about the pursuit of beauty and pleasure, and the price of eternal youth.',
            ],
            // BUKU 4: Psikologi Baru
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'publisher' => 'Avery',
                'publication_year' => 2018,
                'category' => 'Self-Improvement',
                'stock' => 12,
                'max_loan_days' => 7,
                'daily_fine_rate' => 1000.00,
                'description' => 'An easy and proven way to build good habits & break bad ones. Focuses on tiny changes for remarkable results.',
            ],
            // BUKU 5: Fiksi Fantasi Baru
            [
                'title' => 'Dune',
                'author' => 'Frank Herbert',
                'publisher' => 'Chilton Books',
                'publication_year' => 1965,
                'category' => 'Science Fiction',
                'stock' => 8,
                'max_loan_days' => 14,
                'daily_fine_rate' => 750.00,
                'description' => 'The saga of Paul Atreides, a young man thrust into a conflict for control of the desert planet Arrakis and its vital spice.',
            ],
            // BUKU 6: Sejarah/Biografi Baru
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'publisher' => 'Harper',
                'publication_year' => 2014,
                'category' => 'History',
                'stock' => 7,
                'max_loan_days' => 10,
                'daily_fine_rate' => 800.00,
                'description' => 'A sweeping history of the human race, from its origins to the present day and beyond.',
            ],
            // BUKU 7: Motivasi Baru
            [
                'title' => 'The 7 Habits of Highly Effective People',
                'author' => 'Stephen Covey',
                'publisher' => 'Free Press',
                'publication_year' => 1989,
                'category' => 'Self-Improvement',
                'stock' => 9,
                'max_loan_days' => 7,
                'daily_fine_rate' => 1200.00,
                'description' => 'Timeless principles for solving personal and professional problems.',
            ],
        ];

        foreach ($books as $book) {
            DB::table('books')->insert(array_merge($book, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
