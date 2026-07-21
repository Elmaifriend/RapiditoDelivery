## Que hice
    - Cree una estrategia para asignar repartidores
    - Cree un service para asignar repartidor cuando el restaurante lo marca como listo, pero solo si hay un 
      al menos un repartidor disponible
    - si no hay un repartidor disponible entonces se pone el pedido en "esperando repartidor" y cuando
      algun repartidor se desocupa "jala" automaticamente el siguiente pedido a atender.
    - cree un panel web para que el repartidor pueda gestionar sus pedidos

## Que no hice 
    - aun no he creado un resource para gestionar los repartidores 
    - aun no no he creado el template de wa para los repartidores
    - aun no he borrado la tabla / migracion user_busines
    - aun no agrego el selector de lada +52, +1, etc en /checkout
    - aun no agrego un campo para registrar si hubo algun incidente con el cliete en el modelo order
    - al terminar una entrega orderLifeCycle -> completed, payment status = paid ( si si pago )
    - hacer el cambio en /checkout para que el campo de orderDropOffLocation se use 
      para las "instrucciones de entrega" hay que agregar ese campo
    - En modificar la pagina de checkout para que las special instruccion sean indicaciones para el restaurante por ejemplo sin sal

## Que debo hacer
    - Debo crear un resource para gestionar los repartidores
    - Debo crear un template de WA para los repartidores