Requirements to up and run

In order to make this project work correctly you need to make sure 
you acomplish the following requirements.

order_confirming_summary
    Hola {{customer_name}}
    Recibimos tu pedido en *{{restaurant_name}}*.
    🧾 Resumen:
    {{order_summary}}
    Subtotal: ${{subtotal}}
    Envío: ${{delivery_fee}}
    Total: *${{total}}*
    Para confirmar tu pedido, haz clic aquí:
    {{confirmation_link}}
    Si no confirmas, no podremos enviarlo 🚚
    
        Named Parameters:
            customer_name
            restaurant_name
            order_summary
            subtotal
            delivery_fee
            total
            confirmation_link


order_confirmed_success
    ✅ ¡Pedido confirmado!
    Gracias {{customer_name}}. Tu orden fue confirmada correctamente y ya la enviamos a *{{restaurant_name}}* para su preparación.
    Te avisaremos cuando cambie el estado 🍽️

        Named Parameters
            customer_name
            restaurant_name

request_precise_location
    Hola {{customer_name}} 👋
    Para entregarte más rápido tu pedido de *{{restaurant_name}}*, compártenos tu ubicación en tiempo real por WhatsApp 📍
    Esto nos ayuda a darte prioridad y evitar retrasos 🚀

        Named Parameters
            customer_name
            restaurant_name


location_confirmed_success
    Gracias {{customer_name}}, ya recibimos tu ubicación correctamente.
    Ahora podremos entregarte sin contratiempos 🚚✨

        Named Parameters
            customer_name


new_order_restaurant_pending
    🔔 Nueva orden recibida
    Cliente: {{customer_name}}
    🧾 Resumen:
    {{order_summary}}
    Total: ${{total}}
    Por favor confirma, rechaza o sugiere cambios aquí:
    {{restaurant_action_link}}

        Named Parameters
            customer_name
            order_summary
            total
            restaurant_action_link


restaurant_accepted_order
    🎉 ¡Buenas noticias!
    *{{restaurant_name}}* aceptó tu pedido.
    Ya están preparando tu orden 🍽️
    Te avisaremos cuando esté lista.

    Named Parameters
        restaurant_name


order_preparing
    👨‍🍳 Tu pedido está en preparación
    *{{restaurant_name}}* ya está preparando tu orden.
    Tiempo estimado: {{estimated_time}} minutos.

    Named Parameters
        restaurant_name
        estimated_time


order_ready_for_pickup
    📦 Pedido listo
    Tu orden de *{{restaurant_name}}* está lista para recoger.
    🧾 Resumen:
    {{order_summary}}
    Sigue tu envío aquí:
    {{tracking_link}}

    Named Parameters
        restaurant_name
        order_summary
        tracking_link

driver_heading_to_restaurant
    🚴 Tu repartidor va en camino
    {{driver_name}} se dirige a *{{restaurant_name}}* para recoger tu pedido.
    Te avisaremos cuando lo tenga.

    Named Parameters
        driver_name
        restaurant_name


order_on_the_way
    🚚 ¡Va en camino!
    {{driver_name}} ya recogió tu pedido y está en camino a tu ubicación 📍
    Tiempo estimado de llegada: {{eta}} minutos.
    Por Favor no lo hagas!
    Sigue tu envío aquí:
    {{tracking_link}}

        Named Parameters
            driver_name
            eta
            tracking_link


restaurant_rejected_order
    😔 Lo sentimos
    *{{restaurant_name}}* no pudo aceptar tu pedido.
    Estamos procesando el reembolso automáticamente.
    Si tienes dudas, contáctanos.

        Named Parameters
            restaurant_name


order_partial_unavailable
    ⚠️ Actualización de tu pedido
    *{{restaurant_name}}* no tiene disponibles algunos productos.
    🧾 Nueva propuesta:
    {{updated_order_summary}}
    Nuevo total: ${{new_total}}
    Confirma los cambios aquí:
    {{confirmation_link}}

        Named Parameters
            restaurant_name
            updated_order_summary
            new_total
            confirmation_link


refund_in_process
💳 Reembolso en proceso
Estamos gestionando el reembolso de tu pedido en *{{restaurant_name}}*.
Monto: ${{amount}}
Recibirás la confirmación pronto.
    Named Parameters
        restaurant_name
        amount

