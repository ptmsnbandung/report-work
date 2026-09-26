<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportOldUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Berikan default username untuk user existing jika belum terisi
        $defaultUsernames = [
            1 => 'admin',
            2 => 'helpdesk',
            3 => 'teknis',
            4 => 'teknis2',
            5 => 'sacs',
        ];

        foreach ($defaultUsernames as $id => $uname) {
            DB::table('users')->where('id', $id)->whereNull('username')->update(['username' => $uname]);
        }

        // 2. Data hasil filter akun NOC dan Teknik dari database lama
        $importedUsers = [
            [
                'id' => 29,
                'name' => 'CHRISTIAN PERDI',
                'username' => 'Chriss',
                'email' => 'chrissferdy12@gmail.com',
                'password' => '$2y$10$hfC63BLfm85Mg3Zgroewyeb.EJbH5Nl6fSjpt.t6NHx8Lqh.wJ3J.',
                'role' => 'teknis',
                'phone' => '087829724169',
            ],
            [
                'id' => 30,
                'name' => 'Ipin Aripin',
                'username' => 'Ipin Aripin',
                'email' => 'ipinaripin219@gmail.com',
                'password' => '$2y$10$m.WeRH0yLl3JP/.CgLwiKOHeRyUKbeXEruIATeL3mW/UFSXIek2vO',
                'role' => 'teknis',
                'phone' => '089684193712',
            ],
            [
                'id' => 31,
                'name' => 'HARRY SETIONO',
                'username' => 'harry estes',
                'email' => 'harryestes@msn.net.id',
                'password' => '$2y$10$3fuefKm9O0banRyZTTLZ5eTXIFhDz5OViQfzlZ9qp5h27cPMUnNQ.',
                'role' => 'helpdesk',
                'phone' => '089630595394',
            ],
            [
                'id' => 33,
                'name' => 'DUDI DURAHMAN',
                'username' => 'Dudi Durahman',
                'email' => 'dudidurahman@msn.net.id',
                'password' => '$2y$10$K6gc6tojrusYJd9XbNQRiObJJok1kVg26SDIpGzzlRzgqJVL8Krnm',
                'role' => 'teknis',
                'phone' => '089655394872',
            ],
            [
                'id' => 34,
                'name' => 'PADILAH M NUR',
                'username' => 'Padilah M Nur',
                'email' => 'padilmuhammad53116@gamil.com',
                'password' => '$2y$10$h1QD0Aaka.M.1FPwISDeuuos6sqoynQermGzXH2wSPinJc1Nbm9VO',
                'role' => 'teknis',
                'phone' => '083190225326',
            ],
            [
                'id' => 37,
                'name' => 'RICKY SAHARA PUTRA',
                'username' => 'rickysp',
                'email' => 'rickyputraa03@gmail.com',
                'password' => '$2y$10$3C0TzHKsJKDShNtjwZfx1.O4bmpRNHFET06LIXLtLr.iBF.3zTjRi',
                'role' => 'helpdesk',
                'phone' => '085721183196',
            ],
            [
                'id' => 39,
                'name' => 'DENI HAMDANI',
                'username' => 'Deni hamdani',
                'email' => 'denihamdeni1974@gmail.com',
                'password' => '$2y$10$r3jZaGOBLzKujeXlYGuUzeH.P2k97mBsJrlAKIELkGwXvp/r4kAWG',
                'role' => 'teknis',
                'phone' => '083113147404',
            ],
            [
                'id' => 42,
                'name' => 'IYAN SOFIAN',
                'username' => 'Iyan Sofian',
                'email' => 'jeunuchian@gmail.com',
                'password' => '$2y$10$I8P6vwI6aI1BQuAtWbX5IuwXz.KDg9JfGHcFEfyStKEBR9cZ5K1vC',
                'role' => 'teknis',
                'phone' => '081285347696',
            ],
            [
                'id' => 44,
                'name' => 'BAGUS JOKO PRIYONO',
                'username' => 'Bagus Joko',
                'email' => 'bagusjoko@msn.net.id',
                'password' => '$2y$10$nYO10GUWKb1KIRCsITDwQOXp4QZBr5p0EO6EJQOCIhqazNNM5OwF6',
                'role' => 'admin',
                'phone' => null,
            ],
            [
                'id' => 45,
                'name' => 'DODI SODIKIN',
                'username' => 'Dodi Sodikin',
                'email' => 'dodisodikin@msn.net.id',
                'password' => '$2y$10$FLwPsWdEMxpqVAwRKBn6J.D1l84eWDxMLK509O51yMhYiNUW9Aasi',
                'role' => 'teknis',
                'phone' => '089652270447',
            ],
            [
                'id' => 47,
                'name' => 'ENCU SAMSUDIN',
                'username' => 'Encu samsudin',
                'email' => 'encu.samsudin.2525@gmail.com',
                'password' => '$2y$10$sUwP77WxSw7UVu56DWSDh.ilSJVhG5WpsnUYPUw7BOVls4K0T3T0m',
                'role' => 'teknis',
                'phone' => '082115051760',
            ],
            [
                'id' => 48,
                'name' => 'DIKA IBNU',
                'username' => 'Dika ibnu',
                'email' => 'dikabmx5@gmail.com',
                'password' => '$2y$10$IysdXlYPskmaW.yEQyBP5O2cKl.yQ.FVyZKhOYT90w7/Lapg/s0di',
                'role' => 'teknis',
                'phone' => '082115051760',
            ],
            [
                'id' => 49,
                'name' => 'KELVIN SULTAN',
                'username' => 'Kelvin sultan',
                'email' => 'kelvinsultan3@gmail.com',
                'password' => '$2y$10$bJh52VC0DveQ2p4kQNxfvORLB3SjBy2F4SZt6p/uUm0Th4qMzWcsm',
                'role' => 'helpdesk',
                'phone' => '089529951898',
            ],
            [
                'id' => 51,
                'name' => 'MUHAMAD RAFI RAMDANI',
                'username' => 'mrafiramdhani',
                'email' => 'rafiahmat272018@gmail.com',
                'password' => '$2y$10$Bfsx6eePt6oXHvVatKLfjOUsw381ntMExRKkS1lEXlUfl3qR8ILCy',
                'role' => 'helpdesk',
                'phone' => '082117138236',
            ],
            [
                'id' => 52,
                'name' => 'LEVANDRI AHMAD F AJMAL',
                'username' => 'Levandri Ajmal',
                'email' => 'levandriajmal@gmail.com',
                'password' => '$2y$10$KkYPDKvcPBIyEZkXWqWcUu/yTFf6G.l8LwIm1uhrqFXgOC3OBDDc6',
                'role' => 'admin',
                'phone' => null,
            ],
            [
                'id' => 55,
                'name' => 'RYAN SEPTIADI',
                'username' => 'Ryan Gondel',
                'email' => 'ryangondel@msn.net.id',
                'password' => '$2y$10$MiD.mgVdgZcTbE3ps0b9S.hsi9ROOQtaIbDTKoGrvsqqf2xqUUR1m',
                'role' => 'teknis',
                'phone' => '083831228235',
            ],
            [
                'id' => 56,
                'name' => 'DEDE RAHMAN',
                'username' => 'Dede Rahman',
                'email' => 'bettafisht21@gmail.com',
                'password' => '$2y$10$./csFGy.SXd5dznPYrHpeeFw3d2NbJ/0JXJeZXmkQNLY0pS1OG1/K',
                'role' => 'teknis',
                'phone' => '081320222935',
            ],
            [
                'id' => 59,
                'name' => 'BUDI SAMSUDIN',
                'username' => 'Budi samsudin',
                'email' => 'budisamsudin321@gmail.com',
                'password' => '$2y$10$dY6gKrgAT5MupPw/cA5HLOgqivBpj9OTtRugPJPNNg5hKRvW1s44a',
                'role' => 'teknis',
                'phone' => '08977547887',
            ],
            [
                'id' => 60,
                'name' => 'LEVANDRI AHMAD F AJMAL',
                'username' => 'Ajmal',
                'email' => 'ajmal@msn.net.id',
                'password' => '$2y$10$t7S0VtAHK/rI39RQA0djg.NXV7wM71ubW1O0I0DrhgDV5VkHfqpay',
                'role' => 'helpdesk',
                'phone' => '082216491002',
            ],
            [
                'id' => 61,
                'name' => 'BAGUS JOKO PRIYONO',
                'username' => 'Bagus',
                'email' => 'bagus@msn.net.id',
                'password' => '$2y$10$6ccQEtJWXdFc9TiR0eTzQ.6MhCad620yV2GCdTbnPQ9mPfzRpsVQ.',
                'role' => 'helpdesk',
                'phone' => '089656952045',
            ],
            [
                'id' => 62,
                'name' => 'IPIN ARIPIN',
                'username' => 'Ipin',
                'email' => 'ipin@msn.net.id',
                'password' => '$2y$10$gBKiSEDJ5AQa65eJLBKGMeYFdtjyuYOfbtftucaGCvjvB8R9L7KGq',
                'role' => 'teknis',
                'phone' => '089684193712',
            ],
            [
                'id' => 64,
                'name' => 'SANDY WAHYUDY',
                'username' => 'Sandy Wahyudi',
                'email' => 'sandywahyudi@msn.net.id',
                'password' => '$2y$10$jt0LadlDPjekIn.BB8Z.sOGNV1nHDMuyFeH7TxTXMD1jBujHZ7I0G',
                'role' => 'teknis',
                'phone' => '089665719400',
            ],
            [
                'id' => 65,
                'name' => 'DANDI ALRIZKI',
                'username' => 'Dandi Alrizki',
                'email' => 'dandialrizki@msn.net.id',
                'password' => '$2y$10$64JF9mEHOKoUBVME3oHr3e0Xkmn1Wbg1GQPrnHDomdQdn6hKl/pJ2',
                'role' => 'teknis',
                'phone' => '088971352212',
            ],
            [
                'id' => 66,
                'name' => 'FENDRADIKA',
                'username' => 'Fendradika',
                'email' => 'fendradika@msn.net.id',
                'password' => '$2y$10$TDnpjaVqNI7t3vTk4sYKO.FcneOE6wYumGkN/rbJuriKIo17Ie3hy',
                'role' => 'teknis',
                'phone' => '087735081997',
            ],
            [
                'id' => 67,
                'name' => 'REZA APRIANT',
                'username' => 'Reza Apriant',
                'email' => 'rezaapriant@msn.net.id',
                'password' => '$2y$10$JUwxGrp0PeMa/ogDz1k40uurwx/XnCL0k5LM31SXdvPtzCsyZwJFm',
                'role' => 'teknis',
                'phone' => '083155750975',
            ],
            [
                'id' => 68,
                'name' => 'ABDUL GHANI',
                'username' => 'Abdul Ghani',
                'email' => 'abdulghani@msn.net.id',
                'password' => '$2y$10$lXl3KzTiC76jVN2Iqk42Ceyaat15Aweyps6eaFy2ZTIcHsASSTV7W',
                'role' => 'teknis',
                'phone' => '085624213955',
            ],
            [
                'id' => 74,
                'name' => 'Iwan',
                'username' => 'Iwan',
                'email' => 'iwan@msn.net.id',
                'password' => '$2y$10$B5RD/MG51Y9.r2gkWgMg7e7NtAWCcY6/IVxUV1kcjQ1t6WXyzB3lC',
                'role' => 'helpdesk',
                'phone' => null,
            ],
            [
                'id' => 81,
                'name' => 'MUHAMMAD HASYA NUR RASHIF',
                'username' => 'Nur Rashif',
                'email' => 'nurrashif@msn.net.id',
                'password' => '$2y$10$ztYIxWJPIfwc3v1AzqLAs.QGDUMhvdIdiOoEd6S0viH4kfSOH9wSe',
                'role' => 'helpdesk',
                'phone' => null,
            ],
        ];

        $now = now();
        foreach ($importedUsers as $u) {
            DB::table('users')->updateOrInsert(
                ['id' => $u['id']],
                [
                    'name' => $u['name'],
                    'username' => $u['username'],
                    'email' => $u['email'],
                    'password' => $u['password'],
                    'role' => $u['role'],
                    'phone' => $u['phone'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
