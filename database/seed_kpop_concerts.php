<?php
/**
 * EVENTIX - Seeder Data 12 Konser Korea & Kategori Tiket
 * Diimpor otomatis langsung dari dokumen referensi panduan panel admin.
 */

require_once __DIR__ . '/../config/database.php';

$concerts = [
    [
        'nama_event'    => 'SEVENTEEN [RIGHT HERE] World Tour in Jakarta',
        'pengisi_acara' => 'SEVENTEEN',
        'tanggal_event' => '2026-12-12',
        'jam_event'     => '18:30:00',
        'genre_musik'   => 'K-Pop / Performance',
        'venue'         => 'Jakarta International Stadium (JIS), Jakarta Utara',
        'deskripsi'     => 'Konser tur dunia SEVENTEEN kembali menyapa CARAT Indonesia dengan panggung megah berskala stadion. Open gate dimulai pukul 15:00 WIB, wajib membawa e-ticket dan kartu identitas resmi saat penukaran wristband.',
        'poster'        => 'seventeen.jpg',
        'tickets'       => [
            [
                'kategori'       => 'PINK Soundcheck',
                'harga'          => 3800000,
                'kuota_tersedia' => 200,
                'deskripsi'      => 'Standing area paling dekat panggung, akses soundcheck session, lanyard & photocard eksklusif.'
            ],
            [
                'kategori'       => 'CAT 1 (Festival)',
                'harga'          => 2900000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Area festival berdiri dekat panggung utama dengan queue number.'
            ],
            [
                'kategori'       => 'CAT 2 (Lower Tribune)',
                'harga'          => 2200000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi bernomor di tribun bawah dengan pandangan langsung ke panggung.'
            ],
            [
                'kategori'       => 'CAT 3 (Upper Tribune)',
                'harga'          => 1600000,
                'kuota_tersedia' => 600,
                'deskripsi'      => 'Kursi bernomor di tribun atas dengan layar LED pendamping.'
            ]
        ]
    ],
    [
        'nama_event'    => 'IU H.E.R. World Tour Concert in Jakarta',
        'pengisi_acara' => 'IU',
        'tanggal_event' => '2026-12-19',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / Ballad',
        'venue'         => 'ICE BSD City Hall 5-6, Tangerang',
        'deskripsi'     => 'Pertunjukan solo spesial dari IU yang membawakan deretan lagu hits dan album terbaru bersama live band penuh. Seluruh kategori bersifat numbered seating, penukaran tiket dibuka mulai pukul 11:00 WIB.',
        'poster'        => 'iu.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Seated',
                'harga'          => 2900000,
                'kuota_tersedia' => 250,
                'deskripsi'      => 'Barisan kursi depan panggung, souvenir seat cushion & photocard spesial.'
            ],
            [
                'kategori'       => 'CAT 1',
                'harga'          => 2400000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi bernomor area tengah hall dengan visibilitas optimal.'
            ],
            [
                'kategori'       => 'CAT 2',
                'harga'          => 1800000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Kursi bernomor area sayap kanan dan kiri hall.'
            ],
            [
                'kategori'       => 'CAT 3',
                'harga'          => 1100000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Kursi bernomor area belakang dengan akses penuh ke layar utama.'
            ]
        ]
    ],
    [
        'nama_event'    => 'DAY6 3rd World Tour <FOREVER YOUNG> Jakarta',
        'pengisi_acara' => 'DAY6',
        'tanggal_event' => '2027-01-09',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Rock / Pop Rock',
        'venue'         => 'Stadion Madya Gelora Bung Karno, Senayan, Jakarta',
        'deskripsi'     => 'Band rock asal Korea Selatan DAY6 hadir membawakan energi live performance penuh untuk My Day Indonesia. Dilarang membawa kamera profesional, tripod, dan makanan dari luar area stadion.',
        'poster'        => 'day6.jpg',
        'tickets'       => [
            [
                'kategori'       => 'PINK Soundcheck',
                'harga'          => 3400000,
                'kuota_tersedia' => 200,
                'deskripsi'      => 'Akses soundcheck, area standing terdepan, dan exclusive merchandise lanyard.'
            ],
            [
                'kategori'       => 'BLUE Festival',
                'harga'          => 2500000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Area festival berdiri di tengah lapangan dekat extended stage.'
            ],
            [
                'kategori'       => 'YELLOW Tribune',
                'harga'          => 1500000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi tribun bernomor menghadap langsung ke panggung utama.'
            ],
            [
                'kategori'       => 'GREEN Tribune',
                'harga'          => 850000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Kursi tribun sisi kanan dan kiri dengan harga paling terjangkau.'
            ]
        ]
    ],
    [
        'nama_event'    => 'BLACKPINK World Tour [BORN PINK] Jakarta',
        'pengisi_acara' => 'BLACKPINK (Jisoo, Jennie, Rosé, Lisa)',
        'tanggal_event' => '2027-01-23',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / Hip-Hop',
        'venue'         => 'Stadion Utama Gelora Bung Karno (SUGBK), Jakarta',
        'deskripsi'     => 'Konser stadion berskala internasional dari BLACKPINK dengan produksi panggung, koreografi, dan tata kembang api spektakuler. Catatan: seluruh kuota diisi 0 untuk contoh tampilan status Habis di katalog.',
        'poster'        => 'blackpink.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Soundcheck',
                'harga'          => 3800000,
                'kuota_tersedia' => 0,
                'deskripsi'      => 'Kursi terdepan dekat panggung utama, akses soundcheck, jalur masuk khusus, dan laminate eksklusif.'
            ],
            [
                'kategori'       => 'Platinum Seated',
                'harga'          => 3400000,
                'kuota_tersedia' => 0,
                'deskripsi'      => 'Kursi lapangan bernomor tepat di belakang area VIP dengan jarak pandang dekat.'
            ],
            [
                'kategori'       => 'CAT 1',
                'harga'          => 2900000,
                'kuota_tersedia' => 0,
                'deskripsi'      => 'Area tribun bawah sisi barat dan timur stadion dengan kursi bernomor.'
            ],
            [
                'kategori'       => 'CAT 4 (Upper)',
                'harga'          => 1350000,
                'kuota_tersedia' => 0,
                'deskripsi'      => 'Area tribun atas stadion dengan pandangan panorama panggung dan lautan lightstick.'
            ]
        ]
    ],
    [
        'nama_event'    => 'wave to earth – 0.03 World Tour in Jakarta',
        'pengisi_acara' => 'wave to earth',
        'tanggal_event' => '2027-02-06',
        'jam_event'     => '20:00:00',
        'genre_musik'   => 'K-Indie / Lo-Fi',
        'venue'         => 'Istora Senayan, Jakarta Pusat',
        'deskripsi'     => 'Trio indie rock dan lo-fi asal Seoul kembali membawa suasana hangat dan intim lewat tur 0.03 World Tour. Pintu masuk dibuka pukul 17:30 WIB dengan area merchandise resmi di selasar Istora.',
        'poster'        => 'wave_to_earth.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Meet & Greet',
                'harga'          => 2400000,
                'kuota_tersedia' => 150,
                'deskripsi'      => 'Early entry ke area festival, sesi foto grup 1:6, poster bertanda tangan, dan VIP lanyard.'
            ],
            [
                'kategori'       => 'CAT 1 (Standing)',
                'harga'          => 1250000,
                'kuota_tersedia' => 600,
                'deskripsi'      => 'Area berdiri lantai utama tepat di depan panggung dengan audio akustik jernih.'
            ],
            [
                'kategori'       => 'CAT 2 (Seated)',
                'harga'          => 950000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi tribun tengah menghadap langsung ke panggung.'
            ],
            [
                'kategori'       => 'CAT 3 (Seated)',
                'harga'          => 650000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Kursi tribun bagian atas dengan suasana konser yang nyaman.'
            ]
        ]
    ],
    [
        'nama_event'    => 'aespa LIVE TOUR – SYNK : PARALLEL LINE Jakarta',
        'pengisi_acara' => 'aespa (Karina, Giselle, Winter, Ningning)',
        'tanggal_event' => '2027-02-20',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / Hyperpop',
        'venue'         => 'Beach City International Stadium, Ancol, Jakarta',
        'deskripsi'     => 'Tur dunia aespa yang memadukan konsep futuristik, visual LED imersif, serta penampilan solo dari keempat member. Tiket sudah termasuk akses masuk kawasan Ancol pada hari acara.',
        'poster'        => 'aespa.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Soundcheck',
                'harga'          => 3000000,
                'kuota_tersedia' => 250,
                'deskripsi'      => 'Akses soundcheck eksklusif, area standing terdekat panggung, dan VIP gift set.'
            ],
            [
                'kategori'       => 'CAT 1 (Standing)',
                'harga'          => 2750000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Area berdiri di sekitar extended stage dengan nomor antrean masuk.'
            ],
            [
                'kategori'       => 'CAT 2 (Seated)',
                'harga'          => 2550000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi tribun bernomor di lantai 2 dengan sudut pandang lurus ke panggung.'
            ],
            [
                'kategori'       => 'CAT 4 (Upper Seated)',
                'harga'          => 1300000,
                'kuota_tersedia' => 550,
                'deskripsi'      => 'Kursi tribun lantai atas dengan visibilitas layar utama penuh.'
            ]
        ]
    ],
    [
        'nama_event'    => 'NCT DREAM – THE DREAM SHOW 3 in Jakarta',
        'pengisi_acara' => 'NCT DREAM',
        'tanggal_event' => '2027-03-06',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / Dance Pop',
        'venue'         => 'Stadion Utama Gelora Bung Karno (SUGBK), Jakarta',
        'deskripsi'     => 'Konser stadion akbar NCT DREAM bertajuk DREAM( )SCAPE yang menampilkan setlist energik, panggung bergerak, dan interaksi langsung bersama NCTzen Indonesia.',
        'poster'        => 'nct_dream.jpg',
        'tickets'       => [
            [
                'kategori'       => 'DREAM A/B Soundcheck',
                'harga'          => 3350000,
                'kuota_tersedia' => 300,
                'deskripsi'      => 'Area standing paling depan, akses soundcheck, jalur prioritas, dan lanyard eksklusif.'
            ],
            [
                'kategori'       => 'DREAM C (Standing)',
                'harga'          => 2950000,
                'kuota_tersedia' => 600,
                'deskripsi'      => 'Area festival lapangan mengelilingi panggung tengah stadion.'
            ],
            [
                'kategori'       => 'CAT 2 (Lower Seated)',
                'harga'          => 2550000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Kursi tribun bawah bernomor dengan kenyamanan penuh dan pandangan jelas.'
            ],
            [
                'kategori'       => 'CAT 6 (Upper Seated)',
                'harga'          => 1050000,
                'kuota_tersedia' => 700,
                'deskripsi'      => 'Kursi tribun atas bernomor dengan harga terjangkau.'
            ]
        ]
    ],
    [
        'nama_event'    => 'THE DREAM REBORN World Tour in Jakarta',
        'pengisi_acara' => 'DPR IAN, DPR CREAM, DPR ARTIC',
        'tanggal_event' => '2027-03-20',
        'jam_event'     => '19:30:00',
        'genre_musik'   => 'K-R&B / Alternative',
        'venue'         => 'Beach City International Stadium, Ancol, Jakarta',
        'deskripsi'     => 'Kolektif musik independen Dream Perfect Regime menghadirkan malam penuh eksplorasi visual sinematik, R&B, dan rock alternatif bersama DPR IAN, DPR CREAM, dan DPR ARTIC.',
        'poster'        => 'dpr.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Meet & Greet',
                'harga'          => 3800000,
                'kuota_tersedia' => 150,
                'deskripsi'      => 'Early entry, sesi foto grup bersama DPR, akses Q&A singkat, dan exclusive merch pack.'
            ],
            [
                'kategori'       => 'Early Entry Festival',
                'harga'          => 2150000,
                'kuota_tersedia' => 350,
                'deskripsi'      => 'Area berdiri lantai utama dengan akses masuk lebih awal sebelum penonton reguler.'
            ],
            [
                'kategori'       => 'General Standing',
                'harga'          => 1550000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Area berdiri reguler di lantai utama menghadap panggung.'
            ],
            [
                'kategori'       => 'Seated Tribune',
                'harga'          => 950000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Kursi tribun bernomor dengan tata suara jernih.'
            ]
        ]
    ],
    [
        'nama_event'    => "TWICE 5TH WORLD TOUR 'READY TO BE' in Jakarta",
        'pengisi_acara' => 'TWICE',
        'tanggal_event' => '2027-04-10',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / Pop Dance',
        'venue'         => 'Jakarta International Stadium (JIS), Jakarta Utara',
        'deskripsi'     => 'Sembilan member TWICE menyapa ONCE Indonesia dalam konser stadion megah lengkap dengan live band dan panggung solo tiap member. Shuttle bus resmi tersedia dari area parkir JIExpo.',
        'poster'        => 'twice.jpg',
        'tickets'       => [
            [
                'kategori'       => 'PINK Soundcheck',
                'harga'          => 3600000,
                'kuota_tersedia' => 250,
                'deskripsi'      => 'Kursi lapangan terdepan, akses soundcheck session, dan souvenir lanyard eksklusif.'
            ],
            [
                'kategori'       => 'BLUE Seated',
                'harga'          => 3300000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi bernomor di area lapangan tepat di samping panggung utama.'
            ],
            [
                'kategori'       => 'GREEN Tribune',
                'harga'          => 2200000,
                'kuota_tersedia' => 500,
                'deskripsi'      => 'Kursi tribun lantai 1 menghadap panggung utama secara langsung.'
            ],
            [
                'kategori'       => 'GRAY Tribune',
                'harga'          => 1200000,
                'kuota_tersedia' => 650,
                'deskripsi'      => 'Kursi tribun tingkat atas dengan visibilitas keseluruhan stadion.'
            ]
        ]
    ],
    [
        'nama_event'    => 'Stray Kids World Tour <dominATE> Jakarta',
        'pengisi_acara' => 'Stray Kids',
        'tanggal_event' => '2027-04-24',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / Rap Rock',
        'venue'         => 'Indonesia Arena Gelora Bung Karno, Jakarta Pusat',
        'deskripsi'     => 'Stray Kids membawa energi panggung eksplosif dengan aransemen live band keras dan koreografi intens untuk STAY Indonesia di arena indoor berkapasitas besar.',
        'poster'        => 'stray_kids.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Soundcheck',
                'harga'          => 3600000,
                'kuota_tersedia' => 200,
                'deskripsi'      => 'Area standing terdekat panggung, sesi soundcheck, lanyard VIP, dan jalur antrean khusus.'
            ],
            [
                'kategori'       => 'CAT 1 (Standing)',
                'harga'          => 2900000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Area festival berdiri tepat di sekitar panggung utama dan catwalk.'
            ],
            [
                'kategori'       => 'CAT 3 (Seated)',
                'harga'          => 2200000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi tribun bawah bernomor dengan sudut pandang optimal ke seluruh arena.'
            ],
            [
                'kategori'       => 'CAT 5 (Upper Seated)',
                'harga'          => 1200000,
                'kuota_tersedia' => 550,
                'deskripsi'      => 'Kursi tribun atas bernomor di dalam arena ber-AC penuh.'
            ]
        ]
    ],
    [
        'nama_event'    => 'TAEYEON CONCERT – The ODD Of LOVE in Jakarta',
        'pengisi_acara' => 'TAEYEON',
        'tanggal_event' => '2027-05-08',
        'jam_event'     => '18:30:00',
        'genre_musik'   => 'K-Pop / Vocal',
        'venue'         => 'ICE BSD City Hall 5-6, Tangerang',
        'deskripsi'     => 'Pertunjukan vokal memukau dari solois legendaris TAEYEON bersama full live band, menampilkan lagu-lagu balada dan pop ikonik dalam tata akustik hall terbaik.',
        'poster'        => 'taeyeon.jpg',
        'tickets'       => [
            [
                'kategori'       => 'CAT 1 (Standing)',
                'harga'          => 2900000,
                'kuota_tersedia' => 300,
                'deskripsi'      => 'Area berdiri paling dekat panggung dengan nomor antrean masuk.'
            ],
            [
                'kategori'       => 'CAT 2 (Seated)',
                'harga'          => 2700000,
                'kuota_tersedia' => 350,
                'deskripsi'      => 'Kursi bernomor area depan tengah hall untuk menikmati kualitas vokal secara intim.'
            ],
            [
                'kategori'       => 'CAT 4 (Seated)',
                'harga'          => 1900000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Kursi bernomor area sayap kanan dan kiri hall.'
            ],
            [
                'kategori'       => 'CAT 5 (Seated)',
                'harga'          => 1100000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Kursi bernomor area belakang hall dengan dukungan layar LED besar.'
            ]
        ]
    ],
    [
        'nama_event'    => 'BAEKHYUN ASIA TOUR [Lonsdaleite] in Jakarta',
        'pengisi_acara' => 'BAEKHYUN',
        'tanggal_event' => '2027-05-22',
        'jam_event'     => '19:00:00',
        'genre_musik'   => 'K-Pop / R&B',
        'venue'         => 'ICE BSD City Hall 5, Tangerang',
        'deskripsi'     => 'Tur solo Asia perdana BAEKHYUN yang menyuguhkan perpaduan vokal R&B halus, koreografi tajam, dan sesi interaksi hangat bersama EXO-L Indonesia.',
        'poster'        => 'baekhyun.jpg',
        'tickets'       => [
            [
                'kategori'       => 'VIP Soundcheck',
                'harga'          => 3100000,
                'kuota_tersedia' => 200,
                'deskripsi'      => 'Akses soundcheck, sesi send-off terbatas, area standing terdepan, dan photocard eksklusif.'
            ],
            [
                'kategori'       => 'CAT 1 (Standing)',
                'harga'          => 2600000,
                'kuota_tersedia' => 400,
                'deskripsi'      => 'Area berdiri dekat extended stage dengan nomor antrean.'
            ],
            [
                'kategori'       => 'CAT 2 (Seated)',
                'harga'          => 2200000,
                'kuota_tersedia' => 350,
                'deskripsi'      => 'Kursi bernomor area tribun tengah menghadap langsung ke panggung.'
            ],
            [
                'kategori'       => 'CAT 3 (Seated)',
                'harga'          => 1600000,
                'kuota_tersedia' => 450,
                'deskripsi'      => 'Kursi bernomor area belakang dengan pandangan jelas ke panggung dan layar utama.'
            ]
        ]
    ]
];

try {
    $pdo->beginTransaction();

    // Pastikan tidak ada duplikasi jika konser sudah ada dengan nama yang sama
    $stmtCheck = $pdo->prepare("SELECT id_event FROM event WHERE nama_event = :nama LIMIT 1");
    $stmtInsertEvent = $pdo->prepare("INSERT INTO event (venue, nama_event, tanggal_event, jam_event, pengisi_acara, deskripsi, poster) 
                                      VALUES (:venue, :nama, :tanggal, :jam, :pengisi, :deskripsi, :poster)");
    $stmtInsertKonser = $pdo->prepare("INSERT INTO konser (id_event, daftar_lagu, genre_musik) 
                                       VALUES (:id_event, :daftar_lagu, :genre_musik)");
    $stmtInsertTiket = $pdo->prepare("INSERT INTO tiket (id_event, kategori, deskripsi, kuota_tersedia, harga) 
                                     VALUES (:id_event, :kategori, :deskripsi, :kuota, :harga)");

    $insertedConcerts = 0;
    $insertedTickets = 0;

    foreach ($concerts as $idx => $c) {
        $stmtCheck->execute([':nama' => $c['nama_event']]);
        $existingId = $stmtCheck->fetchColumn();

        if ($existingId) {
            $idEvent = (int)$existingId;
            // Update data event jika sudah ada
            $stmtUpdateEvent = $pdo->prepare("UPDATE event SET venue = :venue, tanggal_event = :tanggal, jam_event = :jam, 
                                             pengisi_acara = :pengisi, deskripsi = :deskripsi, poster = :poster 
                                             WHERE id_event = :id");
            $stmtUpdateEvent->execute([
                ':venue'     => $c['venue'],
                ':tanggal'   => $c['tanggal_event'],
                ':jam'       => $c['jam_event'],
                ':pengisi'   => $c['pengisi_acara'],
                ':deskripsi' => $c['deskripsi'],
                ':poster'    => $c['poster'],
                ':id'        => $idEvent
            ]);

            // Update konser
            $stmtUpdateKonser = $pdo->prepare("INSERT INTO konser (id_event, daftar_lagu, genre_musik) 
                                              VALUES (:id_event, '', :genre) 
                                              ON DUPLICATE KEY UPDATE genre_musik = :genre2");
            $stmtUpdateKonser->execute([
                ':id_event' => $idEvent,
                ':genre'    => $c['genre_musik'],
                ':genre2'   => $c['genre_musik']
            ]);

            echo "[UPDATE] Konser ({$idEvent}): {$c['nama_event']}\n";
        } else {
            // Insert event baru
            $stmtInsertEvent->execute([
                ':venue'     => $c['venue'],
                ':nama'      => $c['nama_event'],
                ':tanggal'   => $c['tanggal_event'],
                ':jam'       => $c['jam_event'],
                ':pengisi'   => $c['pengisi_acara'],
                ':deskripsi' => $c['deskripsi'],
                ':poster'    => $c['poster']
            ]);
            $idEvent = (int)$pdo->lastInsertId();

            // Insert relasi konser
            $stmtInsertKonser->execute([
                ':id_event'     => $idEvent,
                ':daftar_lagu'  => '',
                ':genre_musik'  => $c['genre_musik']
            ]);

            $insertedConcerts++;
            echo "[INSERT] Konser ({$idEvent}): {$c['nama_event']}\n";
        }

        // Hapus tiket lama untuk event ini agar tidak dobel bila re-run
        $pdo->prepare("DELETE FROM tiket WHERE id_event = :id_event")->execute([':id_event' => $idEvent]);

        // Insert kategori tiket
        foreach ($c['tickets'] as $t) {
            $stmtInsertTiket->execute([
                ':id_event'  => $idEvent,
                ':kategori'  => $t['kategori'],
                ':deskripsi' => $t['deskripsi'],
                ':kuota'     => $t['kuota_tersedia'],
                ':harga'     => $t['harga']
            ]);
            $insertedTickets++;
        }
        echo "   -> " . count($c['tickets']) . " Kategori tiket berhasil disimpan.\n";
    }

    $pdo->commit();
    echo "\n=== SEEDING BERHASIL ===\n";
    echo "Total Konser Disimpan: " . count($concerts) . "\n";
    echo "Total Tiket Disimpan: " . $insertedTickets . "\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR SEEDING: " . $e->getMessage() . "\n";
    exit(1);
}
