<?php
require_once __DIR__ . '/config/functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
$job = null;

if ($slug) {
    try {
        $stmt = $db->prepare("SELECT * FROM careers WHERE slug = ? AND status = 1");
        $stmt->execute([$slug]);
        $job = $stmt->fetch();
    } catch (Exception $e) {}
    
    if (!$job) {
        header("Location: " . base_url('karir.php'));
        exit();
    }
    
    $page_title = $job['title'] . " — Karir PT. Hastra Karya Persada";
    $page_desc = "Lowongan kerja " . $job['title'] . " di departemen " . $job['department'] . " PT. Hastra Karya Persada.";
} else {
    $page_title = "Karir & Peluang Kerja — PT. Hastra Karya Persada";
    $page_desc = "Bergabunglah bersama tim profesional PT. Hastra Karya Persada. Temukan peluang karir terbaik di bidang konstruksi, pengadaan, dan konsultansi.";
}

// Handle Form Submission for Job Application
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $cover_letter = sanitize($_POST['cover_letter']);
        $career_id = (int)$_POST['career_id'];
        
        // CV File Validation
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['cv']['tmp_name'];
            $file_name = $_FILES['cv']['name'];
            $file_size = $_FILES['cv']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if ($file_ext !== 'pdf') {
                $error_msg = 'Hanya file PDF yang diperbolehkan untuk CV/Resume Anda.';
            } elseif ($file_size > 2 * 1024 * 1024) { // 2MB limit
                $error_msg = 'Ukuran berkas CV Anda melebihi batas maksimal 2MB.';
            } else {
                $new_file_name = 'cv_' . time() . '_' . uniqid() . '.pdf';
                $dest_dir = __DIR__ . '/assets/uploads/cvs/';
                if (!is_dir($dest_dir)) {
                    mkdir($dest_dir, 0755, true);
                }
                $dest_path = $dest_dir . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    try {
                        $stmt = $db->prepare("INSERT INTO career_applications (career_id, name, email, phone, cv_path, cover_letter) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$career_id, $name, $email, $phone, 'assets/uploads/cvs/' . $new_file_name, $cover_letter]);
                        $success_msg = 'Lamaran Anda berhasil terkirim! Tim HRD kami akan meninjau berkas Anda dan menghubungi Anda kembali jika memenuhi syarat.';
                    } catch (Exception $e) {
                        $error_msg = 'Terjadi kesalahan basis data saat memproses lamaran Anda: ' . $e->getMessage();
                    }
                } else {
                    $error_msg = 'Gagal menyimpan berkas CV ke server. Pastikan folder write permissions aktif.';
                }
            }
        } else {
            $error_msg = 'Silakan unggah berkas CV Anda dalam format PDF.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Sinergi Talenta</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;">
                    <?php echo $job ? sanitize($job['title']) : 'Karir &amp; <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Lowongan</em>'; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <?php if ($job): ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url('karir.php'); ?>">Karir</a></li>
                            <li class="breadcrumb-item active"><?php echo sanitize($job['title']); ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active">Karir</li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<?php if ($job): ?>
    <!-- ================= DETAIL VIEW + APPLICATION FORM ================= -->
    <section style="background:var(--white);padding:5rem 0;">
        <div class="container">
            <div class="row g-5">
                <!-- Job details -->
                <div class="col-lg-7 reveal-left">
                    <!-- Status Alerts -->
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert-branded-success mb-4" role="alert">
                            <h5 class="fw-bold"><i class="bi-check-circle-fill me-2"></i>Berhasil!</h5>
                            <p class="mb-0 small"><?php echo $success_msg; ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert-branded-danger mb-4" role="alert">
                            <h5 class="fw-bold"><i class="bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan</h5>
                            <p class="mb-0 small"><?php echo $error_msg; ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="career-meta mb-4">
                        <span style="display:inline-block;padding:.4rem 1.15rem;background:rgba(201,162,39,.1);border:1px solid rgba(201,162,39,.25);color:var(--navy);font-weight:700;font-size:.8rem;border-radius:100px;" class="me-3"><?php echo sanitize($job['job_type']); ?></span>
                        <span class="text-muted"><i class="bi-geo-alt text-gold me-1"></i> <?php echo sanitize($job['location']); ?></span>
                        <span class="text-muted ms-3"><i class="bi-building text-gold me-1"></i> <?php echo sanitize($job['department']); ?></span>
                    </div>

                    <h3 style="font-family:var(--font-head);color:var(--navy);font-size:1.75rem;margin-bottom:1rem;">Deskripsi Pekerjaan</h3>
                    <p class="text-muted leading-relaxed mb-5" style="font-size:1.02rem;">
                        <?php echo nl2br(sanitize($job['description'])); ?>
                    </p>

                    <h3 style="font-family:var(--font-head);color:var(--navy);font-size:1.75rem;margin-bottom:1.5rem;">Persyaratan Jabatan</h3>
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        <?php 
                        $reqs = explode("\n", $job['requirements']);
                        foreach ($reqs as $req):
                            if (trim($req) === '') continue;
                        ?>
                            <div style="display:flex;align-items:flex-start;gap:.75rem;">
                                <i class="bi-check-circle-fill text-gold" style="font-size:1.1rem;margin-top:.1rem;flex-shrink:0;"></i>
                                <span style="font-size:.95rem;color:var(--navy);line-height:1.6;"><?php echo sanitize($req); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Form apply -->
                <div class="col-lg-5 reveal-right">
                    <div class="contact-form-card position-sticky" style="top:100px;">
                        <h3 class="fw-bold text-navy mb-2 h4">Kirim Lamaran Kerja</h3>
                        <p class="small text-muted mb-4">Lengkapi data diri Anda dan unggah berkas CV terbaru (PDF) untuk melamar lowongan ini.</p>
                        
                        <form action="" method="POST" enctype="multipart/form-data" class="form-branded">
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                            <input type="hidden" name="career_id" value="<?php echo $job['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Contoh: Budi Sudarsono">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Contoh: budi@gmail.com">
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">No. Telepon / WhatsApp</label>
                                <input type="text" class="form-control" id="phone" name="phone" required placeholder="Contoh: 081234567890">
                            </div>
                            
                            <div class="mb-3">
                                <label for="cv" class="form-label">Unggah CV / Resume (PDF Max 2MB)</label>
                                <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cover_letter" class="form-label">Surat Pengantar / Catatan</label>
                                <textarea class="form-control" id="cover_letter" name="cover_letter" rows="4" placeholder="Tuliskan alasan mengapa Anda cocok untuk posisi ini..."></textarea>
                            </div>
                            
                            <button type="submit" name="apply_job" class="btn-gold w-100 justify-content-center text-center mt-3">
                                Kirim Lamaran Pekerjaan <i class="bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- ================= LIST VIEW ================= -->
    <section style="background:var(--white);padding:5rem 0;">
        <div class="container">
            <!-- Dynamic Vacancies Load -->
            <?php 
            $vacancies = [];
            try {
                $stmt = $db->query("SELECT * FROM careers WHERE status = 1 ORDER BY id DESC");
                $vacancies = $stmt->fetchAll();
                
                // Auto seed initial data if table is completely empty
                if (count($vacancies) === 0) {
                    $seed_careers = [
                        ['Site Engineer (Civil)', 'site-engineer-civil', 'Konstruksi', 'Jakarta Selatan', 'Full-time', 'Kami sedang mencari Site Engineer yang bertanggung jawab mengawasi jalannya pekerjaan konstruksi sipil gedung bertingkat di lapangan, memastikan keselarasan gambar kerja dengan pelaksanaan.', "Minimal S1 Teknik Sipil dari universitas terkemuka\nPengalaman kerja minimal 3 tahun di posisi yang sama\nMemiliki keahlian mengoperasikan AutoCAD, SAP2000, MS Project\nMemiliki sertifikasi SKA Ahli Muda Teknik Bangunan Gedung lebih disukai\nMampu memimpin tim pekerja lapangan dan berkoordinasi dengan subkontraktor"],
                        ['Procurement Executive', 'procurement-executive', 'Pengadaan', 'Jakarta Pusat', 'Full-time', 'Tugas utama Anda adalah mengelola hubungan dengan vendor supply chain, melakukan negosiasi harga material konstruksi, memantau pengiriman barang logistik, serta memastikan transparansi audit pengadaan barang.', "Minimal S1 Manajemen, Teknik, atau Logistik\nPengalaman kerja minimal 2 tahun di bidang pengadaan konstruksi/industri\nKemampuan negosiasi dan komunikasi yang sangat baik\nMampu bekerja dalam tekanan dan berorientasi pada detail\nMenguasai bahasa Inggris lisan & tulisan"],
                        ['Staf Magang Administrasi Proyek', 'staf-magang-administrasi-proyek', 'Administrasi', 'Jakarta Selatan', 'Magang (Internship)', 'Membantu tim administrasi proyek dalam merapikan dokumen legalitas proyek, menyusun laporan mingguan progres fisik, serta menginput data keuangan operasional proyek.', "Mahasiswa tingkat akhir atau lulusan baru (Fresh Graduate) D3/S1 Administrasi, Manajemen, Akuntansi, atau Teknik\nMampu menggunakan MS Office (Excel, Word) dengan mahir\nMemiliki laptop pribadi dan bersedia magang minimal selama 3 bulan\nJujur, teliti, disiplin, dan mau belajar hal baru"]
                    ];
                    
                    $insert_stmt = $db->prepare("INSERT INTO careers (title, slug, department, location, job_type, description, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    foreach ($seed_careers as $row) {
                        $insert_stmt->execute($row);
                    }
                    $vacancies = $db->query("SELECT * FROM careers WHERE status = 1 ORDER BY id DESC")->fetchAll();
                }
            } catch (Exception $e) {}
            ?>
            <div class="text-center mb-5 reveal">
                <span class="section-tag">Peluang Karir</span>
                <h2 class="section-title">Bergabung Bersama Tim Profesional</h2>
                <span class="section-divider center mb-4"></span>
                <p class="text-muted col-md-8 mx-auto leading-relaxed">Kami mengundang individu-individu bertalenta tinggi, dinamis, dan berdedikasi untuk bersama-sama mengembangkan karir dan ikut andil membangun proyek berskala nasional.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <?php if (count($vacancies) > 0): ?>
                    <?php foreach ($vacancies as $i => $v): ?>
                        <div class="col-lg-10 reveal" style="transition-delay:<?php echo $i * .1; ?>s">
                            <div class="career-card d-flex flex-column flex-md-row align-items-md-center justify-content-between p-4">
                                <div style="display:flex;flex-direction:column;gap:.5rem;">
                                    <div class="career-meta">
                                        <span style="display:inline-block;padding:.2rem .85rem;background:var(--gray-100);color:var(--navy);font-weight:700;font-size:.7rem;border-radius:100px;border:1px solid rgba(11,31,58,.06);"><?php echo sanitize($v['department']); ?></span>
                                    </div>
                                    <h4 class="fw-bold text-navy mb-0 h5"><?php echo sanitize($v['title']); ?></h4>
                                    <div class="career-meta small text-muted" style="display:flex;gap:1.5rem;">
                                        <span><i class="bi-geo-alt"></i> <?php echo sanitize($v['location']); ?></span>
                                        <span><i class="bi-briefcase"></i> <?php echo sanitize($v['job_type']); ?></span>
                                    </div>
                                </div>
                                <div class="mt-3 mt-md-0">
                                    <a href="<?php echo base_url('karir/' . $v['slug']); ?>" class="btn-gold" style="font-size:.82rem;padding:.75rem 1.75rem;">Lamar Pekerjaan <i class="bi-chevron-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi-people text-muted display-1"></i>
                        <p class="mt-3 text-muted">Saat ini belum ada lowongan pekerjaan aktif. Silakan kembali lagi di lain waktu.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
