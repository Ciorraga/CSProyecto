(function() {
    setTimeout( function() {

        // create the notification
        var notification = new NotificationFx({
            message : '<p>Usuario creado con éxito!<br/>!Prueba a ingresar!</p>',
            layout : 'growl',
            effect : 'scale',
            type : 'error' // notice, warning, error or success
        });

        // show the notification
        notification.show();

    }, 500 );

})();
