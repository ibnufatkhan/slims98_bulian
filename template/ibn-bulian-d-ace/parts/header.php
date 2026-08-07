<!--
# Ibn Bulian D'ACE Template (Etran visual system)
# SLiMS OPAC header
-->
<?php
$request_uri = urlencode(strip_tags(urldecode($_SERVER['REQUEST_URI'])));
?>
<!DOCTYPE html>
<html lang="<?php echo substr($sysconf['default_lang'] ?? 'id', 0, 2); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0"/>
    <meta http-equiv="Expires" content="Sat, 26 Jul 1997 05:00:00 GMT"/>
    <?php echo $metadata; ?>
    <?php if (isset($_GET['p']) && ($_GET['p'] == 'show_detail')): ?>
        <meta name="description" content="<?php echo substr($notes, 0, 152) . '...'; ?>">
        <meta name="keywords" content="<?php echo $subject; ?>">
    <?php else: ?>
        <meta name="description" content="<?php echo $page_title; ?>">
        <meta name="keywords" content="<?php echo $sysconf['library_subname']; ?>">
    <?php endif; ?>
    <meta name="generator" content="<?php echo SENAYAN_VERSION ?>">
    <meta name="theme-color" content="#3a4a1c">

    <meta property="og:locale" content="<?php echo str_replace('-', '_', $sysconf['default_lang']); ?>"/>
    <meta property="og:type" content="book"/>
    <meta property="og:title" content="<?php echo $page_title; ?>"/>
    <?php if (isset($_GET['p']) && ($_GET['p'] == 'show_detail')): ?>
        <meta property="og:description" content="<?php echo substr($notes, 0, 152) . '...'; ?>"/>
    <?php else: ?>
        <meta property="og:description" content="<?php echo $sysconf['library_subname']; ?>"/>
    <?php endif; ?>
    <meta property="og:url" content="//<?php echo $_SERVER["SERVER_NAME"] . $request_uri; ?>"/>
    <meta property="og:site_name" content="<?php echo $sysconf['library_name']; ?>"/>

    <link rel="stylesheet" href="<?php echo assets('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets('plugin/font-awesome/css/fontawesome-all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets('css/tailwind.min.css'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo assets('one/vendor/aos/aos.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets('plugin/vegas/vegas.min.css'); ?>">
    <link href="<?php echo JWB; ?>toastr/toastr.min.css?<?php echo date('this') ?>" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="<?= JWB; ?>ckeditor5/ckeditor5.css">
    <link rel="stylesheet" href="<?= JWB; ?>colorbox/colorbox.css">
    <link rel="stylesheet" href="<?= JWB; ?>ion.rangeSlider/css/ion.rangeSlider.min.css">
    <link rel="stylesheet" href="<?php echo assets('css/flag-icon.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo assets('css/style.css?v=' . date('Ymd-his')); ?>">
    <link rel="stylesheet" href="<?php echo assets('css/etran.css?v=' . date('Ymd-his')); ?>">

    <?php
    $icon = SWB . 'webicon.ico';
    if (isset($sysconf['webicon']) && !empty($sysconf['webicon']) && $imagesDisk->isExists($path = 'default/' . $sysconf['webicon'])) {
        $icon = SWB . 'lib/minigalnano/createthumb.php?filename=images/' . $path . '&width=130';
    }
    ?>
    <link rel="shortcut icon" href="<?= $icon ?>" type="image/x-icon"/>

    <script src="<?php echo assets('js/vue.min.js'); ?>"></script>
    <script src="<?php echo assets('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo assets('js/masonry.pkgd.min.js'); ?>"></script>
    <script src="<?php echo assets('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo assets('plugin/vegas/vegas.min.js'); ?>"></script>
    <script src="<?php echo JWB; ?>toastr/toastr.min.js"></script>
    <script src="<?php echo JWB; ?>colorbox/jquery.colorbox-min.js"></script>
    <script src="<?php echo JWB . v('gui.js'); ?>"></script>
    <script src="<?php echo JWB; ?>fancywebsocket.js"></script>
    <script src="<?php echo JWB; ?>ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <?php if (isset($js)) { echo $js; } ?>
</head>
<body class="etran-body bg-grey-lightest">
<?php include __DIR__ . '/_navbar.php'; ?>
