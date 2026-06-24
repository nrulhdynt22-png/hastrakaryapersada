    <!-- FOOTER -->
    <footer class="footer-section">
        <div class="container">
            <div class="row g-5">
                <!-- Brand -->
                <div class="col-lg-4">
                    <a href="<?php echo base_url(); ?>" style="font-family:var(--font-head);font-size:1.3rem;font-weight:800;color:#fff;text-decoration:none;letter-spacing:.5px;">
                        PT. HASTRA KARYA <span style="background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">PERSADA</span>
                    </a>
                    <p style="font-size:.9rem;line-height:1.8;margin-top:1.25rem;">
                        Penyedia solusi konstruksi, pengadaan, dan konsultansi manajemen proyek terpercaya untuk pembangunan nasional.
                    </p>
                    <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
                        <a href="<?php echo $settings['social_facebook'] ?? '#'; ?>" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);text-decoration:none;transition:all .3s;" onmouseover="this.style.background='rgba(201,162,39,.2)';this.style.borderColor='rgba(201,162,39,.4)';this.style.color='var(--gold)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.6)'"><i class="bi-facebook"></i></a>
                        <a href="<?php echo $settings['social_instagram'] ?? '#'; ?>" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);text-decoration:none;transition:all .3s;" onmouseover="this.style.background='rgba(201,162,39,.2)';this.style.borderColor='rgba(201,162,39,.4)';this.style.color='var(--gold)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.6)'"><i class="bi-instagram"></i></a>
                        <a href="<?php echo $settings['social_linkedin'] ?? '#'; ?>" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);text-decoration:none;transition:all .3s;" onmouseover="this.style.background='rgba(201,162,39,.2)';this.style.borderColor='rgba(201,162,39,.4)';this.style.color='var(--gold)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.6)'"><i class="bi-linkedin"></i></a>
                        <a href="https://wa.me/<?php echo preg_replace('/\D/','',$settings['whatsapp'] ?? ''); ?>" target="_blank" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);text-decoration:none;transition:all .3s;" onmouseover="this.style.background='rgba(201,162,39,.2)';this.style.borderColor='rgba(201,162,39,.4)';this.style.color='var(--gold)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.6)'"><i class="bi-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Nav -->
                <div class="col-lg-2 col-sm-6">
                    <h5>Navigasi</h5>
                    <a href="<?php echo base_url(); ?>">Home</a>
                    <a href="<?php echo base_url('tentang.php'); ?>">Tentang</a>
                    <a href="<?php echo base_url('layanan.php'); ?>">Layanan</a>
                    <a href="<?php echo base_url('hubungi-kami.php'); ?>">Kontak</a>
                </div>

                <!-- Links -->
                <div class="col-lg-2 col-sm-6">
                    <h5>Informasi</h5>
                    <a href="<?php echo base_url('admin/login.php'); ?>">Login</a>
                </div>

                <!-- Contact -->
                <div class="col-lg-4">
                    <h5>Hubungi Kami</h5>
                    <div style="display:flex;flex-direction:column;gap:1rem;margin-top:.25rem;">
                        <div style="display:flex;align-items:flex-start;gap:.75rem;">
                            <i class="bi-geo-alt-fill" style="color:var(--gold);margin-top:.15rem;flex-shrink:0;"></i>
                            <span style="font-size:.88rem;"><?php echo $settings['address'] ?? ''; ?></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <i class="bi-telephone-fill" style="color:var(--gold);flex-shrink:0;"></i>
                            <span style="font-size:.88rem;"><?php echo $settings['phone'] ?? ''; ?></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <i class="bi-envelope-fill" style="color:var(--gold);flex-shrink:0;"></i>
                            <span style="font-size:.88rem;"><?php echo $settings['email'] ?? ''; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <span>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_name'] ?? 'PT. Hastra Karya Persada'; ?>. Hak Cipta Dilindungi.</span>
                <span style="font-size:.78rem;color:rgba(255,255,255,.3);">Dibangun dengan <span style="color:var(--gold);">♥</span> untuk kemajuan Indonesia</span>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <button id="lightbox-close" class="lightbox-close"><i class="bi-x-lg"></i></button>
        <img id="lightbox-img" class="lightbox-img" src="" alt="Preview">
    </div>

    <!-- WhatsApp FAB -->
    <?php if (!empty($settings['whatsapp'])): ?>
    <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $settings['whatsapp']); ?>?text=Halo%20PT.%20Hastra%20Karya%20Persada%2C%20saya%20ingin%20berkonsultasi."
       class="btn-whatsapp" target="_blank" title="Chat via WhatsApp">
        <i class="bi-whatsapp"></i>
    </a>
    <?php endif; ?>

    <!-- Back to top -->
    <a href="#" id="backToTop" class="btn-back-to-top"><i class="bi-chevron-up"></i></a>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Main App JS -->
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
</body>
</html>
