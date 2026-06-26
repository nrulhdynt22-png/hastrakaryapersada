<?php
// Fallbacks for SEO elements
$meta_title = isset($page_title) ? $page_title . " | " . ($settings['site_name'] ?? 'PT. Hastra Karya Persada') : ($settings['site_name'] ?? 'PT. Hastra Karya Persada') . " - " . ($settings['site_tagline'] ?? 'Solusi Profesional');
$meta_description = isset($page_desc) ? $page_desc : ($settings['site_description'] ?? '');
$meta_keywords = isset($page_keys) ? $page_keys : ($settings['site_keywords'] ?? '');
$meta_og_image = isset($og_image) ? $og_image : base_url('assets/images/logo-share.jpg');
$meta_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!-- SEO Optimization -->
<title><?php echo $meta_title; ?></title>
<link rel="icon" href="<?php echo base_url('assets/img/' . ($settings['site_favicon'] ?? 'favicon.svg')); ?>">
<meta name="description" content="<?php echo $meta_description; ?>">
<meta name="keywords" content="<?php echo $meta_keywords; ?>">
<meta name="author" content="<?php echo $settings['site_name'] ?? 'PT. Hastra Karya Persada'; ?>">
<meta name="robots" content="index, follow">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $meta_url; ?>">
<meta property="og:title" content="<?php echo $meta_title; ?>">
<meta property="og:description" content="<?php echo $meta_description; ?>">
<meta property="og:image" content="<?php echo $meta_og_image; ?>">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo $meta_url; ?>">
<meta property="twitter:title" content="<?php echo $meta_title; ?>">
<meta property="twitter:description" content="<?php echo $meta_description; ?>">
<meta property="twitter:image" content="<?php echo $meta_og_image; ?>">

<!-- Schema.org Organization Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?php echo $settings['site_name'] ?? 'PT. Hastra Karya Persada'; ?>",
  "url": "<?php echo base_url(); ?>",
  "logo": "<?php echo base_url('assets/img/' . ($settings['site_logo'] ?? 'logo.png')); ?>",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "<?php echo $settings['phone'] ?? ''; ?>",
    "contactType": "customer service",
    "email": "<?php echo $settings['email'] ?? ''; ?>",
    "availableLanguage": ["Indonesian", "English"]
  },
  "sameAs": [
    "<?php echo $settings['social_facebook'] ?? ''; ?>",
    "<?php echo $settings['social_instagram'] ?? ''; ?>",
    "<?php echo $settings['social_linkedin'] ?? ''; ?>",
    "<?php echo $settings['social_twitter'] ?? ''; ?>"
  ]
}
</script>
