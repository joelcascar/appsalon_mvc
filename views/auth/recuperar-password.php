<h1 class="nombre-pagina">Recuperar Contraseña</h1>
<p class="descripcion-pagina">Coloca tu nueva contraseña a continuación</p>
<?php include __DIR__ . "/../templates/alertas.php"; ?>
<?php if($error) return; ?>
<form method="POST" class="formulario">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Tu nueva contraseña">
    </div>
    <input type="submit" class="boton" value="Guardar nueva contraseña">
</form>
<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Inicia Sesión</a>
    <a href="/index.php/crear-cuenta">¿Aún no tienes cuenta? Crea una</a>
</div>