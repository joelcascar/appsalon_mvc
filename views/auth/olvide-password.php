<h1 class="nombre-pagina">Olvide Password</h1>
<p class="descripcion-pagina">Reestablece tu password escribiendo tu email a continuación</p>
<?php include __DIR__ . "/../templates/alertas.php"; ?>
<form action="/index.php/olvide" method="POST" class="formulario">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Tu email">
    </div>
    <input type="submit" class="boton" value="Reestablecer contraseña">
</form>
<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/index.php/crear-cuenta">¿Aún no tienes cuenta? Crea una</a>
</div>