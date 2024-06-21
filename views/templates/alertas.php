<?php
foreach($alertas as $p => $mensajes){
    foreach($mensajes as $mensaje){
?>
    <div class="alerta <?php echo $p; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php
    }
}
?>