<?php
// Calculate base path for asset links (handles root vs subfolder pages like /admin/)
$is_admin_dir = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
$base_path = $is_admin_dir ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . " | ITS-BERT" : "ITS-BERT | Intelligent Tutoring System"; ?></title>
  <meta name="description" content="An Intelligent Tutoring System using Bidirectional Encoder Representations from Transformers (BERT) for CS students.">
  
  <!-- Favicon -->
  <link rel="icon" href="<?php echo $base_path; ?>assets/images/ai_tutor_avatar.jpg" type="image/jpeg">

  <!-- Google Fonts (Inter & Poppins) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons & Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Custom Core Stylesheet -->
  <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>
<body class="<?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'landing-page' : ''; ?>">
