<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Nexo de Ideas'; ?></title>
    <link rel="stylesheet" href="/PROYECTO_BLOG/public/recursos/css/estilos.css">
</head>
<body>
    <?php include VIEWS_PATH . '/components/header.php'; ?>
    
    <main class="contenedor">
        <?php echo $contenido ?? ''; ?>
    </main>
    
    <?php include VIEWS_PATH . '/components/footer.php'; ?>
    
    <script src="/PROYECTO_BLOG/public/recursos/js/app.js"></script>
</body>
</html>
