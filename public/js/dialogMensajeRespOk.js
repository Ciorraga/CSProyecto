(function() {
    setTimeout( function() {

        // create the notification
        var notification = new NotificationFx({
            message : '<p>Mensaje enviado con éxito</p>',
            layout : 'growl',
            effect : 'scale',
            type : 'error' // notice, warning, error or success
        });

        // show the notification
        notification.show();

    }, 500 );

})();
