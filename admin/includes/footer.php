        </div><!-- end .admin-content -->
    </main><!-- end .admin-main -->
</div><!-- end .admin-shell -->

<!-- ===== LOGOUT CONFIRMATION MODAL ===== -->
<div id="logoutModal" style="
    display:none;
    position:fixed;inset:0;z-index:99999;
    background:rgba(6,16,31,.65);
    backdrop-filter:blur(8px);
    -webkit-backdrop-filter:blur(8px);
    align-items:center;justify-content:center;
">
    <div style="
        background:#fff;
        border-radius:20px;
        padding:2.5rem 2.25rem;
        max-width:380px;width:90%;
        box-shadow:0 32px 80px rgba(6,16,31,.35);
        border:1px solid rgba(201,162,39,.15);
        text-align:center;
        animation:modalPop .3s cubic-bezier(.34,1.56,.64,1);
        position:relative;
    ">
        <!-- Icon -->
        <div style="
            width:68px;height:68px;border-radius:50%;
            background:linear-gradient(160deg,#0B1F3A,#06101F);
            border:2px solid rgba(201,162,39,.3);
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 1.5rem;
            box-shadow:0 8px 24px rgba(11,31,58,.25);
        ">
            <i class="bi-power" style="font-size:1.75rem;background:linear-gradient(135deg,#BF953F,#FCF6BA 40%,#B38728 70%,#FBF5B7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"></i>
        </div>

        <!-- Text -->
        <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#0B1F3A;margin-bottom:.5rem;font-size:1.2rem;">Konfirmasi Logout</h5>
        <p style="color:#64748B;font-size:.9rem;margin-bottom:2rem;line-height:1.6;">Apakah Anda yakin ingin keluar dari<br><strong style="color:#0B1F3A;">Dashboard Admin?</strong></p>

        <!-- Buttons -->
        <div style="display:flex;gap:.75rem;">
            <button id="logoutCancel" style="
                flex:1;padding:.75rem;border-radius:100px;
                border:1.5px solid rgba(11,31,58,.15);
                background:transparent;color:#0B1F3A;
                font-size:.9rem;font-weight:600;
                cursor:pointer;font-family:'Outfit',sans-serif;
                transition:all .25s;
            " onmouseover="this.style.background='#F4F6FA'" onmouseout="this.style.background='transparent'">
                Batal
            </button>
            <button id="logoutConfirm" onclick="window.location.href='logout.php'" style="
                flex:1;padding:.75rem;border-radius:100px;
                background:linear-gradient(135deg,#BF953F,#FCF6BA 40%,#B38728 70%,#FBF5B7);
                color:#06101F;
                font-size:.9rem;font-weight:700;
                border:none;cursor:pointer;display:flex;
                align-items:center;justify-content:center;gap:.45rem;
                box-shadow:0 4px 14px rgba(201,162,39,.35);
                transition:all .3s;font-family:'Outfit',sans-serif;
            ">
                <i class="bi-box-arrow-right"></i> Ya, Keluar
            </button>
        </div>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity:0; transform:scale(.85) translateY(16px); }
    to   { opacity:1; transform:scale(1)  translateY(0); }
}
</style>

<script>
// Show logout modal on every logout link click
document.querySelectorAll('a[href="logout.php"]').forEach(function(el) {
    el.removeAttribute('onclick');
    el.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutModal').style.display = 'flex';
    });
});
// Cancel button
document.getElementById('logoutCancel').addEventListener('click', function() {
    document.getElementById('logoutModal').style.display = 'none';
});
// Stop click propagation inside the modal card (so inner clicks don't close modal)
document.querySelector('#logoutModal > div').addEventListener('click', function(e) {
    e.stopPropagation();
});
// Close only when clicking the dark backdrop
document.getElementById('logoutModal').addEventListener('click', function() {
    this.style.display = 'none';
});
// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') document.getElementById('logoutModal').style.display = 'none';
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

