(function() {
    setTimeout( function() {

        // create the notification
        var notification = new NotificationFx({
            message : '<p>Solicitud enviada con éxito</p>',
            layout : 'growl',
            effect : 'scale',
            type : 'error' // notice, warning, error or success
        });

        // show the notification
        notification.show();

    }, 500 );

})();
/**
 * Created by Ciorraga on 02/06/2015.
 */
