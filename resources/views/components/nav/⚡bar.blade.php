<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<nav class="fixed bottom-0 z-50 flex w-full items-center justify-around rounded-t-2xl bg-white p-2 pb-4">

    <livewire:nav.item route="home" icon="bxf bx-home-alt" label="Inicio" />

    <livewire:nav.item route="search" icon="bxf bx-search" label="Buscar" />

    <livewire:nav.item route="cart" icon="bxf bx-basket" label="Pedido" />

    <livewire:nav.item route="profile" icon="bxf bx-user-circle" label="Perfil" />
</nav>
