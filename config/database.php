<?php
class Database {
    private $host;
    private $username;
    private $password;
    private $db_name;
    public $conn = null;

    public function __construct() {
        // Otomatis mendeteksi apakah berjalan di komputer lokal (XAMPP) atau di Hostinger
        $is_localhost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8000', '127.0.0.1']);
        
        if ($is_localhost) {
            // === KREDENSIAL LOKAL (XAMPP) ===
            $this->host = "localhost";
            $this->username = "root";
            $this->password = "";
            $this->db_name = "db_hastra_karya";
        } else {
            // === KREDENSIAL HOSTINGER (LIVE) ===
            // Ganti 3 baris di bawah ini sesuai dengan database yang Anda buat di Hostinger!
            $this->host = "localhost"; // Di Hostinger biasanya tetap localhost
            $this->username = "u151221364_hastrakarya";    // CONTOH: ganti dengan Username Database Hostinger Anda
            $this->password = "Hastra_Web_2026!#";   // Password unik dan aman
            $this->db_name = "u151221364_hastrakarya";    // CONTOH: ganti dengan Nama Database Hostinger Anda
        }
    }

    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            // First connect without DB to check/create database
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Select the database
            $this->conn->exec("USE `" . $this->db_name . "`");
            
            // Verify if tables exist, otherwise create them
            $this->checkAndSetupTables();
            
            // Return connection with standard options
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");
            
        } catch (PDOException $exception) {
            die("Database Connection Error: " . $exception->getMessage());
        }

        return $this->conn;
    }

    private function checkAndSetupTables() {
        // Simple check if settings table exists
        $tableExists = false;
        try {
            $result = $this->conn->query("SELECT 1 FROM `settings` LIMIT 1");
            $tableExists = true;
        } catch (Exception $e) {
            $tableExists = false;
        }

        if (!$tableExists) {
            // Create tables and seed data
            $sql = "
            CREATE TABLE IF NOT EXISTS `admins` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `last_login` DATETIME DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `settings` (
                `key_name` VARCHAR(50) NOT NULL PRIMARY KEY,
                `key_value` TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `sliders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `subheadline` TEXT NOT NULL,
                `image` VARCHAR(255) NOT NULL,
                `link_text` VARCHAR(100) DEFAULT 'Tentang Perusahaan',
                `link_url` VARCHAR(255) DEFAULT 'tentang.php',
                `status` TINYINT DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `services` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(100) NOT NULL UNIQUE,
                `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-gear',
                `short_description` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `advantages` TEXT NULL,
                `workflow` TEXT NULL,
                `image` VARCHAR(255) NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `portfolio` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(150) NOT NULL,
                `slug` VARCHAR(150) NOT NULL UNIQUE,
                `category` VARCHAR(100) NOT NULL,
                `location` VARCHAR(150) NOT NULL,
                `year` VARCHAR(10) NOT NULL,
                `client` VARCHAR(150) NOT NULL,
                `description` TEXT NOT NULL,
                `image` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `articles` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(200) NOT NULL,
                `slug` VARCHAR(200) NOT NULL UNIQUE,
                `content` LONGTEXT NOT NULL,
                `image` VARCHAR(255) NOT NULL,
                `tags` VARCHAR(255) NULL,
                `author` VARCHAR(100) DEFAULT 'Admin',
                `status` ENUM('draft', 'published') DEFAULT 'draft',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `gallery` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(150) NOT NULL,
                `category` ENUM('kegiatan', 'proyek', 'event') NOT NULL,
                `image` VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `careers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(150) NOT NULL,
                `slug` VARCHAR(150) NOT NULL UNIQUE,
                `department` VARCHAR(100) NOT NULL,
                `location` VARCHAR(100) NOT NULL,
                `job_type` VARCHAR(50) NOT NULL DEFAULT 'Full-time',
                `description` TEXT NOT NULL,
                `requirements` TEXT NOT NULL,
                `status` TINYINT DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `career_applications` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `career_id` INT NOT NULL,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `phone` VARCHAR(20) NOT NULL,
                `cv_path` VARCHAR(255) NOT NULL,
                `cover_letter` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`career_id`) REFERENCES `careers`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `company_profile` (
                `id` INT PRIMARY KEY DEFAULT 1,
                `director_speech` TEXT NOT NULL,
                `profile_text` TEXT NOT NULL,
                `legality` TEXT NOT NULL,
                `structure_img` VARCHAR(255) NULL,
                `business_sectors` TEXT NOT NULL,
                `certificates` TEXT NOT NULL,
                `pdf_path` VARCHAR(255) NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `org_structure` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `position` VARCHAR(150) NOT NULL,
                `parent_id` INT NULL DEFAULT NULL,
                `photo` VARCHAR(255) NULL DEFAULT NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                FOREIGN KEY (`parent_id`) REFERENCES `org_structure`(`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";

            $this->conn->exec($sql);

            // Seed default administrator: username = admin, password = admin123
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO `admins` (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute(['admin', $hashedPassword, 'info@hastrakarya.co.id']);

            // Seed settings
            $settings = [
                'site_name' => 'PT. Hastra Karya Persada',
                'site_tagline' => 'Solusi Profesional dan Terpercaya untuk Kemajuan Bisnis Anda',
                'site_description' => 'PT. Hastra Karya Persada berkomitmen memberikan layanan terbaik dengan standar kualitas tinggi dan profesionalisme yang unggul dalam bidang konstruksi, pengadaan, dan konsultansi.',
                'site_keywords' => 'hastra karya persada, konstruksi jakarta, kontraktor profesional, pembangunan gedung, pdo php, company profile enterprise',
                'email' => 'info@hastrakarya.co.id',
                'phone' => '+62 21 8888 9999',
                'whatsapp' => '6281234567890',
                'address' => 'Gedung Menara Karya Lt. 22, Jl. H.R. Rasuna Said Block X-5, Jakarta Selatan 12950',
                'google_maps' => '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2672079032766!2d106.82869557457788!3d-6.228458793759714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e498c0d959%3A0x7d0ccf8b056be11c!2sMenara%20Karya!5e0!3m2!1sid!2sid!4v1719213456789!5m2!1sid!2sid\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>',
                'social_facebook' => 'https://facebook.com/hastrakarya',
                'social_instagram' => 'https://instagram.com/hastrakarya',
                'social_linkedin' => 'https://linkedin.com/company/hastrakarya',
                'social_twitter' => 'https://twitter.com/hastrakarya',
                'stat_proyek' => '150',
                'stat_mitra' => '80',
                'stat_pengalaman' => '15',
                'stat_kepuasan' => '99'
            ];

            $stmtSetting = $this->conn->prepare("INSERT INTO `settings` (key_name, key_value) VALUES (?, ?)");
            foreach ($settings as $key => $value) {
                $stmtSetting->execute([$key, $value]);
            }

            // Seed initial sliders
            $stmtSlider = $this->conn->prepare("INSERT INTO `sliders` (title, subheadline, image, link_text, link_url, status) VALUES (?, ?, ?, ?, ?, 1)");
            $stmtSlider->execute([
                'Solusi Profesional dan Terpercaya untuk Kemajuan Bisnis Anda',
                'PT. Hastra Karya Persada berkomitmen memberikan layanan terbaik dengan standar kualitas tinggi dan profesionalisme yang unggul.',
                'slider1.jpg',
                'Tentang Perusahaan',
                'tentang.php'
            ]);
            $stmtSlider->execute([
                'Membangun Infrastruktur Masa Depan dengan Kualitas Unggul',
                'Dengan tenaga ahli berpengalaman dan teknologi modern, kami siap mewujudkan proyek skala nasional maupun global.',
                'slider2.jpg',
                'Layanan Kami',
                'layanan.php'
            ]);

            // Seed initial services
            $stmtService = $this->conn->prepare("INSERT INTO `services` (title, slug, icon, short_description, description, advantages, workflow, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtService->execute([
                'Konstruksi & Infrastruktur',
                'konstruksi-dan-infrastruktur',
                'bi-building',
                'Pembangunan gedung, jalan raya, jembatan, dan fasilitas publik dengan standar konstruksi terbaik.',
                'Kami menyediakan layanan konstruksi menyeluruh mulai dari perencanaan, rekayasa, hingga pelaksanaan di lapangan. Mengutamakan keselamatan kerja (K3), efisiensi biaya, dan ketepatan waktu pembangunan.',
                "Tenaga ahli bersertifikat resmi\nPeralatan konstruksi milik sendiri dan modern\nMaterial bersertifikat SNI\nJaminan pemeliharaan pasca konstruksi",
                "1. Studi Kelayakan & Perencanaan Awal\n2. Pembuatan Desain & Rencana Anggaran Biaya (RAB)\n3. Pengurusan Izin Mendirikan Bangunan (IMB)\n4. Pelaksanaan Konstruksi di Lapangan\n5. Serah Terima Proyek & Masa Pemeliharaan",
                'service_konstruksi.jpg'
            ]);
            $stmtService->execute([
                'Pengadaan Barang & Jasa (Procurement)',
                'pengadaan-barang-dan-jasa',
                'bi-cart-check',
                'Penyediaan kebutuhan material proyek, perangkat IT, serta alat berat berkualitas tinggi.',
                'Sebagai mitra pengadaan tepercaya bagi sektor swasta maupun instansi pemerintahan, kami menawarkan rantai pasok yang andal dan transparan guna mendukung operasional bisnis Anda.',
                "Rantai pasok global terpercaya\nHarga sangat kompetitif\nPengiriman tepat waktu dan aman\nLayanan purna jual lengkap",
                "1. Pengajuan Kebutuhan oleh Klien\n2. Negosiasi & Penawaran Harga Terbaik\n3. Pembuatan Kontrak Pengadaan\n4. Pengiriman & Pengujian Kualitas Barang\n5. Serah Terima Barang & Invoice",
                'service_procurement.jpg'
            ]);
            $stmtService->execute([
                'Konsultansi Manajemen Proyek',
                'konsultansi-manajemen-proyek',
                'bi-briefcase',
                'Layanan konsultansi pengawasan, estimasi biaya, dan manajemen proyek konstruksi.',
                'Kami membantu Anda meminimalkan risiko proyek, mengendalikan anggaran, serta memastikan kualitas hasil akhir proyek sesuai dengan spesifikasi yang telah direnakan.',
                "Konsultan berpengalaman lebih dari 15 tahun\nAnalisis risiko mendalam\nLaporan progres berkala yang transparan\nEfisiensi waktu hingga 20%",
                "1. Konsultasi Awal & Audit Rencana Proyek\n2. Pemetaan Risiko & Strategi Mitigasi\n3. Pengawasan Intensif Pelaksanaan Proyek\n4. Pelaporan Progres & Audit Kualitas berkala\n5. Evaluasi Akhir Proyek",
                'service_consultancy.jpg'
            ]);

            // Seed initial portfolio
            $stmtPortfolio = $this->conn->prepare("INSERT INTO `portfolio` (title, slug, category, location, year, client, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtPortfolio->execute([
                'Pembangunan Gedung Perkantoran Menara Hastra',
                'pembangunan-gedung-perkantoran-menara-hastra',
                'Konstruksi',
                'Jakarta Selatan',
                '2024',
                'PT. Hastra Group',
                'Proyek pembangunan gedung bertingkat 20 lantai dengan konsep green building dan struktur tahan gempa.',
                'portfolio1.jpg'
            ]);
            $stmtPortfolio->execute([
                'Pengadaan Alat Berat Proyek Jalan Tol Sumatera',
                'pengadaan-alat-berat-proyek-jalan-tol-sumatera',
                'Pengadaan',
                'Sumatera Selatan',
                '2025',
                'PT. Wijaya Pembangunan Tbk',
                'Penyediaan 15 unit excavator dan crawler crane untuk mendukung percepatan pembangunan jalan tol trans-sumatera.',
                'portfolio2.jpg'
            ]);
            $stmtPortfolio->execute([
                'Manajemen Pengawasan Renovasi Bandara International',
                'manajemen-pengawasan-renovasi-bandara-international',
                'Konsultansi',
                'Tangerang, Banten',
                '2023',
                'Kementerian Perhubungan RI',
                'Konsultan manajemen konstruksi untuk renovasi terminal 2 bandara internasional guna meningkatkan kapasitas penumpang.',
                'portfolio3.jpg'
            ]);

            // Seed company_profile
            $stmtProfile = $this->conn->prepare("INSERT INTO `company_profile` (id, director_speech, profile_text, legality, business_sectors, certificates) VALUES (1, ?, ?, ?, ?, ?)");
            $stmtProfile->execute([
                'Selamat datang di website resmi PT. Hastra Karya Persada. Di era globalisasi dan perkembangan teknologi yang sangat dinamis ini, kami terus berkomitmen menjadi garda terdepan dalam menyediakan solusi konstruksi, pengadaan, dan konsultansi manajemen yang andal. Kepercayaan Anda adalah fondasi utama kami untuk terus berkarya membangun masa depan bangsa.',
                'PT. Hastra Karya Persada berdiri dengan visi menjadi perusahaan konstruksi dan multi-jasa terkemuka di tingkat nasional yang dikenal karena integritas, kualitas, dan ketepatan waktu. Kami didukung oleh tim profesional yang berpengalaman serta berkomitmen tinggi untuk memberikan hasil terbaik di setiap proyek.',
                "Akte Pendirian: No. 45 Tanggal 12 Januari 2020\nSK Kemenkumham: AHU-0012345.AH.01.01.Tahun 2020\nNIB (Nomor Induk Berusaha): 9120001234567\nNPWP: 91.222.333.4-015.000\nSBU (Sertifikat Badan Usaha): Klasifikasi Menengah (M1)",
                "1. Konstruksi Bangunan Sipil (Gedung, Jalan, Jembatan)\n2. Pengadaan Barang Umum dan Peralatan Industri / IT\n3. Jasa Konsultansi Manajemen Proyek & Teknik Sipil",
                "ISO 9001:2015 (Sistem Manajemen Mutu)\nISO 14001:2015 (Sistem Manajemen Lingkungan)\nISO 45001:2018 (Sistem Manajemen K3)\nSertifikasi SMK3 Kementerian Ketenagakerjaan RI"
            ]);

            // Seed org_structure (sesuai gambar struktur organisasi)
            $stmtOrg = $this->conn->prepare("INSERT INTO `org_structure` (id, name, position, parent_id, sort_order) VALUES (?, ?, ?, ?, ?)");
            // Level 1 - Commissioner
            $stmtOrg->execute([1, 'Muhammad Shadra Ali', 'Commissioner', null, 1]);
            // Level 2 - Director
            $stmtOrg->execute([2, 'Muhammad Akib, ST', 'Director', 1, 1]);
            // Level 3 - Managers (parent: Director id=2)
            $stmtOrg->execute([3, 'Ar. Marwan M., S.T., M.Sc., IAI', 'Operations Manager', 2, 1]);
            $stmtOrg->execute([4, 'M. Risnandar, ST', 'Engineering Manager', 2, 2]);
            $stmtOrg->execute([5, 'Mesrawati, A.Md', 'Finance Manager', 2, 3]);
            $stmtOrg->execute([6, 'Patria Muhammad', 'HSE Manager', 2, 4]);
            // Level 4 - Staff (parent: Engineering Manager id=4)
            $stmtOrg->execute([7, 'Ichsan Radjab, ST', 'Mechanical Engineer', 4, 1]);
            $stmtOrg->execute([8, 'Saiful Hayadi, S.ST', 'BIM Engineer', 4, 2]);
            $stmtOrg->execute([9, 'Nila Angelia H., S.Psi', 'Administration & Personnel', 5, 3]);
            // Level 5 - Junior staff (parent: Mechanical id=7 and BIM id=8)
            $stmtOrg->execute([10, 'Ir. Sakaruddin', 'Support Engineer', 7, 1]);
            $stmtOrg->execute([11, 'Nurul Fathana, A.Md.T', 'CAD Drafter', 8, 2]);


            // Seed initial articles
            $stmtArticle = $this->conn->prepare("INSERT INTO `articles` (title, slug, content, image, tags, author, status) VALUES (?, ?, ?, ?, ?, ?, 'published')");
            $stmtArticle->execute([
                'PT. Hastra Karya Persada Raih Sertifikasi ISO 9001:2015 Sistem Manajemen Mutu',
                'pt-hastra-karya-persada-raih-sertifikasi-iso-9001-2015-sistem-manajemen-mutu',
                '<p>PT. Hastra Karya Persada secara resmi telah menerima sertifikasi ISO 9001:2015 untuk Sistem Manajemen Mutu. Pencapaian ini membuktikan komitmen perusahaan dalam menjaga kualitas layanan, efisiensi operasional, dan kepuasan pelanggan pada tingkat tertinggi.</p><p>Sertifikat diserahkan langsung oleh perwakilan badan sertifikasi internasional di kantor pusat PT. Hastra Karya Persada, Jakarta Selatan. Direktur Utama PT. Hastra Karya Persada menyatakan bahwa pencapaian ini adalah hasil kerja keras seluruh tim dalam mengimplementasikan tata kelola perusahaan yang baik dan profesional.</p>',
                'news1.jpg',
                'Sertifikasi,ISO,Prestasi',
                'Direktur Utama'
            ]);
            $stmtArticle->execute([
                'Proyek Pembangunan Gedung Menara Hastra Masuk Tahap Akhir (Topping Off)',
                'proyek-pembangunan-gedung-menara-hastra-masuk-tahap-akhir-topping-off',
                '<p>Proyek pembangunan Gedung Menara Hastra yang berlokasi di kawasan segitiga emas Jakarta Selatan kini telah memasuki tahap topping off. Proyek ini berjalan sesuai dengan target waktu yang ditetapkan dan mengedepankan aspek Zero Accident.</p><p>Gedung Menara Hastra dirancang dengan standar perkantoran premium kelas enterprise dan menggunakan panel surya serta pengolahan limbah air mandiri sebagai wujud kepedulian kami pada kelestarian lingkungan hidup.</p>',
                'news2.jpg',
                'Proyek,Konstruksi,Jakarta',
                'Project Manager'
            ]);
        }
    }
}
?>
