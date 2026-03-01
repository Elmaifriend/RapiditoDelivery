<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Delivery App Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F3F4F6; /* Fondo un poco más oscuro para resaltar las tarjetas blancas */
            -webkit-tap-highlight-color: transparent;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-flat {
            background-color: #F3F4F6;
            border: 0;
            outline: none;
            border-radius: 1rem;
            padding: 1rem;
            width: 100%;
            transition: background 0.2s;
        }
        .input-flat:focus {
            background-color: #E5E7EB;
            box-shadow: 0 0 0 2px #EF4444;
        }

        /* Toast notification */
        #toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 50px;
            padding: 16px;
            position: fixed;
            z-index: 100;
            left: 50%;
            bottom: 80px;
            transform: translateX(-50%);
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s, bottom 0.3s;
        }

        #toast.show {
            visibility: visible;
            opacity: 1;
            bottom: 90px;
        }

        /* Ticket style edge for orders */
        .ticket-edge {
            background-image: radial-gradient(circle at 10px 0, transparent 0, transparent 5px, #fff 5px);
            background-size: 20px 10px;
            background-repeat: repeat-x;
            height: 10px;
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 h-screen flex flex-col overflow-hidden">

    <!-- HEADER (Visible en Home y Search) -->
    <header class="bg-white p-4 sticky top-0 z-20 border-b border-gray-100 flex justify-between items-center transition-all duration-300" id="main-header">
        <div class="flex flex-col cursor-pointer" onclick="renderHome()">
            <span class="text-xs text-gray-400 font-medium">Entregar en</span>
            <div class="flex items-center gap-1 text-red-500 font-bold">
                <i class="fas fa-map-marker-alt"></i>
                <span>Casa - Calle 10 #20...</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </div>
        </div>
        <div class="relative cursor-pointer" onclick="renderCart()">
            <div class="bg-gray-100 w-10 h-10 rounded-full flex items-center justify-center text-gray-600">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <!-- Badge estático para la demo -->
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">4</span>
        </div>
    </header>

    <!-- MAIN CONTENT AREA -->
    <main id="app-content" class="flex-1 overflow-y-auto no-scrollbar relative">
        <!-- Contenido dinámico -->
    </main>

    <!-- BOTTOM NAVIGATION -->
    <nav id="bottom-nav" class="bg-white border-t border-gray-100 p-2 flex justify-around items-center z-20 pb-4 md:pb-2">
        <button onclick="renderHome()" class="nav-btn flex flex-col items-center justify-center p-2 text-red-500 w-16" id="nav-home">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Inicio</span>
        </button>
        <button onclick="renderSearch()" class="nav-btn flex flex-col items-center justify-center p-2 text-gray-400 w-16" id="nav-search">
            <i class="fas fa-search text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Buscar</span>
        </button>
        <button onclick="renderCart()" class="nav-btn flex flex-col items-center justify-center p-2 text-gray-400 w-16" id="nav-cart">
            <i class="fas fa-receipt text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Pedidos</span>
        </button>
        <button onclick="renderProfile()" class="nav-btn flex flex-col items-center justify-center p-2 text-gray-400 w-16" id="nav-profile">
            <i class="fas fa-user text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Perfil</span>
        </button>
    </nav>

    <!-- TOAST -->
    <div id="toast">Producto agregado a la demo</div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // --- DATA MOCKUP (Datos visuales para Home y Restaurantes) ---
        const restaurants = [
            {
                id: 1,
                name: "El Tizoncito",
                rating: 4.8,
                time: "20-30 min",
                delivery: "$25.00",
                image: "https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?auto=format&fit=crop&q=80&w=800",
                category: "Tacos",
                banner: "https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&q=80&w=1000",
                menu: [
                    { id: 101, name: "Tacos al Pastor", desc: "3 tacos con piña, cilantro y cebolla.", price: 65, cat: "Tacos" },
                    { id: 102, name: "Gringa de Pastor", desc: "Queso fundido y carne al pastor en harina.", price: 85, cat: "Especialidades" },
                    { id: 103, name: "Agua de Horchata", desc: "1 Litro, receta casera.", price: 35, cat: "Bebidas" }
                ]
            },
            {
                id: 2,
                name: "Burger King",
                rating: 4.5,
                time: "30-45 min",
                delivery: "$30.00",
                image: "https://images.unsplash.com/photo-1571091718767-18b5b1457add?auto=format&fit=crop&q=80&w=800",
                category: "Hamburguesas",
                banner: "https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&q=80&w=1000",
                menu: [
                    { id: 201, name: "Whopper Combo", desc: "Con papas y refresco mediano.", price: 120, cat: "Combos" },
                    { id: 202, name: "Nuggets x10", desc: "Crujientes nuggets de pollo.", price: 80, cat: "Complementos" }
                ]
            },
            {
                id: 3,
                name: "Pizza Maestra",
                rating: 4.2,
                time: "40-50 min",
                delivery: "$20.00",
                image: "https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?auto=format&fit=crop&q=80&w=800",
                category: "Pizza",
                banner: "https://images.unsplash.com/photo-1574071318508-1cdbab80d002?auto=format&fit=crop&q=80&w=1000",
                menu: [
                    { id: 301, name: "Pepperoni Grande", desc: "La clásica de siempre.", price: 180, cat: "Pizzas" }
                ]
            }
        ];

        // --- DOM ELEMENTS ---
        const content = document.getElementById('app-content');
        const mainHeader = document.getElementById('main-header');
        const bottomNav = document.getElementById('bottom-nav');

        // --- HELPERS ---
        const formatCurrency = (amount) => `$${amount.toFixed(2)}`;

        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.innerText = message;
            toast.className = "show";
            setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 2000);
        }

        function updateActiveNav(activeId) {
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('text-red-500');
                btn.classList.add('text-gray-400');
            });
            const active = document.getElementById(activeId);
            if (active) {
                active.classList.remove('text-gray-400');
                active.classList.add('text-red-500');
            }
            // Mostrar/Ocultar bottomNav
            bottomNav.classList.remove('hidden');
        }

        // --- VIEWS ---

        // 1. HOME VIEW
        function renderHome() {
            updateActiveNav('nav-home');
            mainHeader.style.display = 'flex';

            content.innerHTML = `
                <div class="p-4 space-y-6 pb-24 fade-in">

                    <!-- Search Dummy Trigger -->
                    <div onclick="renderSearch()" class="bg-gray-100 rounded-full px-4 py-3 flex items-center text-gray-400 gap-3 border border-gray-200 cursor-pointer">
                        <i class="fas fa-search"></i>
                        <span class="text-sm">¿Qué se te antoja hoy?</span>
                    </div>

                    <!-- Banners -->
                    <div class="overflow-x-auto flex gap-4 no-scrollbar pb-2">
                        <div class="min-w-[85%] h-40 bg-gradient-to-r from-red-500 to-red-600 rounded-3xl p-5 text-white flex flex-col justify-center relative overflow-hidden">
                            <div class="z-10">
                                <h2 class="text-2xl font-bold mb-1">50% OFF</h2>
                                <p class="text-sm opacity-90 mb-3">En tu primer pedido</p>
                                <button class="bg-white text-red-600 px-4 py-2 rounded-full text-xs font-bold w-max">Ver más</button>
                            </div>
                            <i class="fas fa-hamburger absolute -right-4 -bottom-4 text-9xl opacity-20 rotate-12"></i>
                        </div>
                         <div class="min-w-[85%] h-40 bg-gradient-to-r from-orange-400 to-red-400 rounded-3xl p-5 text-white flex flex-col justify-center relative overflow-hidden">
                            <div class="z-10">
                                <h2 class="text-2xl font-bold mb-1">Envíos Gratis</h2>
                                <p class="text-sm opacity-90 mb-3">Todo el fin de semana</p>
                            </div>
                            <i class="fas fa-motorcycle absolute -right-2 -bottom-2 text-8xl opacity-20"></i>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <h3 class="font-bold text-lg mb-4">Categorías</h3>
                        <div class="flex justify-between px-2">
                            ${['Tacos', 'Pizza', 'Burgers', 'Sano', 'Postres'].map(cat => `
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-2xl border border-gray-100 shadow-sm">
                                        ${cat === 'Tacos' ? '🌮' : cat === 'Pizza' ? '🍕' : cat === 'Burgers' ? '🍔' : cat === 'Sano' ? '🥗' : '🍩'}
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">${cat}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Restaurant List -->
                    <div>
                        <h3 class="font-bold text-lg mb-4">Restaurantes cerca</h3>
                        <div class="space-y-6">
                            ${restaurants.map(rest => `
                                <div onclick="renderRestaurant(${rest.id})" class="bg-white rounded-3xl overflow-hidden border border-gray-100 cursor-pointer active:scale-95 transition-transform duration-150">
                                    <div class="h-40 w-full relative">
                                        <img src="${rest.image}" class="w-full h-full object-cover" alt="${rest.name}">
                                        <div class="absolute top-3 right-3 bg-white px-2 py-1 rounded-xl text-xs font-bold flex items-center gap-1 shadow-sm">
                                            <i class="fas fa-clock text-gray-400"></i> ${rest.time}
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-1">
                                            <h4 class="font-bold text-lg text-gray-800">${rest.name}</h4>
                                            <div class="bg-green-100 text-green-700 px-2 py-0.5 rounded-lg text-xs font-bold flex items-center gap-1">
                                                ${rest.rating} <i class="fas fa-star text-[10px]"></i>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                            <span>${rest.category}</span>
                                            <span>•</span>
                                            <span>Envío ${rest.delivery}</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        // 2. SEARCH VIEW (NUEVA - MOCKUP)
        function renderSearch() {
            updateActiveNav('nav-search');
            mainHeader.style.display = 'none'; // Ocultar header principal en búsqueda

            content.innerHTML = `
                <div class="p-4 space-y-6 fade-in min-h-full bg-white">
                    <!-- Fake Search Bar -->
                    <div class="flex gap-3 items-center">
                         <div class="flex-1 bg-gray-100 rounded-2xl px-4 py-3 flex items-center text-gray-800 gap-3 border border-gray-200">
                            <i class="fas fa-search text-gray-400"></i>
                            <input type="text" placeholder="Buscar comida o restaurantes" class="bg-transparent border-none outline-none w-full text-sm font-medium" autofocus>
                        </div>
                    </div>

                    <!-- Recent Searches -->
                    <div>
                        <div class="flex justify-between items-end mb-4">
                            <h3 class="font-bold text-lg">Recientes</h3>
                            <span class="text-xs text-red-500 font-bold">Borrar</span>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <span class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold border border-gray-100 flex items-center gap-2">
                                <i class="far fa-clock text-gray-400"></i> Hamburguesas
                            </span>
                             <span class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold border border-gray-100 flex items-center gap-2">
                                <i class="far fa-clock text-gray-400"></i> Farmacia
                            </span>
                             <span class="px-4 py-2 bg-gray-50 text-gray-600 rounded-xl text-xs font-bold border border-gray-100 flex items-center gap-2">
                                <i class="far fa-clock text-gray-400"></i> KFC
                            </span>
                        </div>
                    </div>

                    <!-- Top Categories -->
                    <div>
                        <h3 class="font-bold text-lg mb-4">Top Categorías</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-24 bg-orange-100 rounded-3xl p-4 relative overflow-hidden flex items-center">
                                <span class="font-bold text-orange-800 relative z-10">Comida<br>Rápida</span>
                                <i class="fas fa-hamburger absolute -right-2 bottom-0 text-6xl text-orange-200"></i>
                            </div>
                            <div class="h-24 bg-green-100 rounded-3xl p-4 relative overflow-hidden flex items-center">
                                <span class="font-bold text-green-800 relative z-10">Saludable<br>& Fit</span>
                                <i class="fas fa-carrot absolute -right-2 bottom-0 text-6xl text-green-200"></i>
                            </div>
                            <div class="h-24 bg-blue-100 rounded-3xl p-4 relative overflow-hidden flex items-center">
                                <span class="font-bold text-blue-800 relative z-10">Bebidas<br>& Licores</span>
                                <i class="fas fa-wine-bottle absolute -right-2 bottom-0 text-6xl text-blue-200"></i>
                            </div>
                            <div class="h-24 bg-pink-100 rounded-3xl p-4 relative overflow-hidden flex items-center">
                                <span class="font-bold text-pink-800 relative z-10">Postres<br>& Dulces</span>
                                <i class="fas fa-ice-cream absolute -right-2 bottom-0 text-6xl text-pink-200"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Trending -->
                    <div>
                        <h3 class="font-bold text-lg mb-4">Tendencias hoy 🔥</h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 py-2 border-b border-gray-50">
                                <span class="text-gray-300 font-bold text-xl">1</span>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">Little Caesars</p>
                                    <p class="text-xs text-gray-400">Pizza • 25-35 min</p>
                                </div>
                                <span class="text-xs bg-red-100 text-red-500 px-2 py-1 rounded-lg font-bold">Promo</span>
                            </div>
                             <div class="flex items-center gap-4 py-2 border-b border-gray-50">
                                <span class="text-gray-300 font-bold text-xl">2</span>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">Starbucks Coffee</p>
                                    <p class="text-xs text-gray-400">Café • 15-25 min</p>
                                </div>
                            </div>
                             <div class="flex items-center gap-4 py-2 border-b border-gray-50">
                                <span class="text-gray-300 font-bold text-xl">3</span>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">Tacos El Califa</p>
                                    <p class="text-xs text-gray-400">Mexicana • 30-40 min</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // 3. PROFILE VIEW (NUEVA - MOCKUP)
        function renderProfile() {
            updateActiveNav('nav-profile');
            mainHeader.style.display = 'none';

            content.innerHTML = `
                <div class="p-4 pb-24 fade-in bg-white min-h-full">
                    <h2 class="text-2xl font-bold mb-6">Mi Perfil</h2>

                    <!-- User Info Card -->
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 bg-gray-200 rounded-full overflow-hidden border-2 border-red-500 p-0.5">
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=200" class="w-full h-full rounded-full object-cover">
                        </div>
                        <div>
                            <h3 class="font-bold text-xl">Juan Pérez</h3>
                            <p class="text-xs text-gray-400">juan.demo@email.com</p>
                            <span class="inline-block mt-1 bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                <i class="fas fa-crown text-[8px] mr-1"></i> Miembro Gold
                            </span>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    <div class="flex gap-4 mb-8">
                        <div class="flex-1 bg-red-50 rounded-2xl p-4 text-center">
                            <span class="block text-2xl font-bold text-red-500">12</span>
                            <span class="text-xs text-gray-500">Pedidos mes</span>
                        </div>
                        <div class="flex-1 bg-gray-50 rounded-2xl p-4 text-center">
                            <span class="block text-2xl font-bold text-gray-800">4.9</span>
                            <span class="text-xs text-gray-500">Tu Calificación</span>
                        </div>
                    </div>

                    <!-- Menu List -->
                    <div class="space-y-2">
                        <!-- Option Item -->
                        <button class="w-full flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl active:bg-gray-50">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <span class="font-medium text-sm text-gray-700">Mis Direcciones</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </button>

                         <button class="w-full flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl active:bg-gray-50">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <span class="font-medium text-sm text-gray-700">Métodos de Pago</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </button>

                         <button class="w-full flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl active:bg-gray-50">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <span class="font-medium text-sm text-gray-700">Notificaciones</span>
                            </div>
                            <div class="bg-red-500 w-2 h-2 rounded-full"></div>
                        </button>

                         <button class="w-full flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl active:bg-gray-50 mt-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <span class="font-medium text-sm text-gray-700">Ayuda y Soporte</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </button>
                    </div>

                    <button class="w-full text-center mt-8 text-sm text-gray-400 font-medium">Cerrar Sesión</button>
                    <p class="text-center text-[10px] text-gray-300 mt-2">Versión Demo 1.0.0</p>
                </div>
            `;
        }

        // 4. CART VIEW (MEJORADO - PEDIDOS SEPARADOS CLARAMENTE)
        function renderCart() {
            updateActiveNav('nav-cart');
            mainHeader.style.display = 'flex'; // Mostrar header en cart

            // Data Breakdown
            const subtotalFood = 320;
            const deliveryTotal = 85; // 25 + 40 + 20
            const serviceFee = 20;
            const totalGeneral = subtotalFood + deliveryTotal + serviceFee;

            content.innerHTML = `
                <div class="p-4 pb-40 fade-in">
                    <!-- Header Cart -->
                    <div class="flex items-center gap-4 mb-2">
                        <button onclick="renderHome()" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div>
                             <h2 class="text-2xl font-bold leading-none">Tu Carrito</h2>
                             <span class="text-xs text-gray-500">3 Pedidos pendientes</span>
                        </div>
                    </div>

                    <div class="bg-yellow-50 text-yellow-700 p-3 rounded-2xl text-xs mb-6 flex gap-2 items-start border border-yellow-100">
                        <i class="fas fa-exclamation-triangle mt-0.5"></i>
                        <span><b>Nota Importante:</b> Tienes productos de 3 restaurantes diferentes. Se realizarán 3 cobros de envío y llegarán 3 repartidores distintos.</span>
                    </div>

                    <!-- ORDER 1 CARD -->
                    <div class="bg-white rounded-3xl mb-6 shadow-sm overflow-hidden">
                        <!-- Header Pedido -->
                        <div class="bg-gray-800 text-white p-3 px-5 flex justify-between items-center">
                            <span class="text-xs font-bold uppercase tracking-wide">Pedido 1 de 3</span>
                            <i class="fas fa-motorcycle text-sm"></i>
                        </div>

                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                                <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden border border-gray-100">
                                    <img src="https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?auto=format&fit=crop&q=80&w=200" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">El Tizoncito</h3>
                                    <p class="text-[10px] text-gray-400">Preparando tu orden...</p>
                                </div>
                            </div>

                            <div class="space-y-4 mb-4">
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-3 items-center">
                                        <div class="bg-gray-50 text-gray-500 w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold border border-gray-100">2x</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Tacos al Pastor</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">$130.00</span>
                                </div>
                            </div>

                            <!-- Costos específicos de este pedido -->
                            <div class="bg-gray-50 rounded-xl p-3 text-xs space-y-1">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal comida</span>
                                    <span>$130.00</span>
                                </div>
                                <div class="flex justify-between text-gray-800 font-bold">
                                    <span>Envío (Repartidor 1)</span>
                                    <span>$25.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ORDER 2 CARD -->
                    <div class="bg-white rounded-3xl mb-6 shadow-sm overflow-hidden">
                        <div class="bg-gray-800 text-white p-3 px-5 flex justify-between items-center">
                            <span class="text-xs font-bold uppercase tracking-wide">Pedido 2 de 3</span>
                             <i class="fas fa-motorcycle text-sm"></i>
                        </div>

                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                                <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden border border-gray-100">
                                    <img src="https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&q=80&w=200" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">Sushi Roll</h3>
                                    <p class="text-[10px] text-gray-400">Preparando tu orden...</p>
                                </div>
                            </div>

                            <div class="space-y-4 mb-4">
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-3 items-center">
                                        <div class="bg-gray-50 text-gray-500 w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold border border-gray-100">1x</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">California Roll</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">$90.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-3 items-center">
                                        <div class="bg-gray-50 text-gray-500 w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold border border-gray-100">1x</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Kushiages</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">$55.00</span>
                                </div>
                            </div>
                             <!-- Costos específicos de este pedido -->
                            <div class="bg-gray-50 rounded-xl p-3 text-xs space-y-1">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal comida</span>
                                    <span>$145.00</span>
                                </div>
                                <div class="flex justify-between text-gray-800 font-bold">
                                    <span>Envío (Repartidor 2)</span>
                                    <span>$40.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ORDER 3 CARD -->
                    <div class="bg-white rounded-3xl mb-6 shadow-sm overflow-hidden">
                        <div class="bg-gray-800 text-white p-3 px-5 flex justify-between items-center">
                            <span class="text-xs font-bold uppercase tracking-wide">Pedido 3 de 3</span>
                             <i class="fas fa-motorcycle text-sm"></i>
                        </div>

                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                                <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden border border-gray-100">
                                    <img src="https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?auto=format&fit=crop&q=80&w=200" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">Pizza Maestra</h3>
                                    <p class="text-[10px] text-gray-400">Preparando tu orden...</p>
                                </div>
                            </div>

                            <div class="space-y-4 mb-4">
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-3 items-center">
                                        <div class="bg-gray-50 text-gray-500 w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold border border-gray-100">1x</div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Refresco 2L</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">$45.00</span>
                                </div>
                            </div>
                             <!-- Costos específicos de este pedido -->
                            <div class="bg-gray-50 rounded-xl p-3 text-xs space-y-1">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal comida</span>
                                    <span>$45.00</span>
                                </div>
                                <div class="flex justify-between text-gray-800 font-bold">
                                    <span>Envío (Repartidor 3)</span>
                                    <span>$20.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary CLEAR BREAKDOWN -->
                    <div class="bg-white rounded-3xl p-5 border border-gray-200 mb-6">
                        <h3 class="font-bold text-lg mb-4">Resumen de Pagos</h3>

                        <div class="flex justify-between mb-2 text-sm text-gray-600">
                            <span>Comida (3 Rest.)</span>
                            <span>${formatCurrency(subtotalFood)}</span>
                        </div>

                        <div class="flex justify-between mb-2 text-sm text-red-500 font-medium bg-red-50 p-2 rounded-lg">
                            <span class="flex items-center gap-2"><i class="fas fa-motorcycle"></i> Envíos (3 repartidores)</span>
                            <span>${formatCurrency(deliveryTotal)}</span>
                        </div>

                         <div class="flex justify-between mb-4 text-sm text-gray-600">
                            <span>Tarifa de servicio</span>
                            <span>${formatCurrency(serviceFee)}</span>
                        </div>

                        <div class="flex justify-between text-2xl font-bold text-gray-900 pt-4 border-t border-gray-100">
                            <span>Total Final</span>
                            <span>${formatCurrency(totalGeneral)}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2 text-center">Incluye impuestos y tarifas de la plataforma.</p>
                    </div>
                </div>

                <!-- Footer Checkout -->
                <div class="absolute bottom-0 left-0 right-0 bg-white p-5 border-t border-gray-100 z-20 rounded-t-3xl shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
                    <button onclick="renderCheckout()" class="w-full bg-gradient-to-r from-gray-800 to-black text-white font-bold py-4 rounded-2xl flex justify-between px-6 items-center active:scale-[0.98] transition-transform">
                        <span>Pagar 3 Pedidos</span>
                        <span>${formatCurrency(totalGeneral)}</span>
                    </button>
                </div>
            `;
        }

        // 5. RESTAURANT VIEW
        function renderRestaurant(id) {
            updateActiveNav('none'); // Ocultar highlight del nav
            bottomNav.classList.add('hidden'); // Ocultar nav en detalle

            const rest = restaurants.find(r => r.id === id);
            const categories = [...new Set(rest.menu.map(m => m.cat))];

            content.innerHTML = `
                <div class="pb-24 fade-in bg-white min-h-full">
                    <!-- Nav Back -->
                    <div class="absolute top-4 left-4 z-10">
                        <button onclick="renderHome()" class="bg-white w-10 h-10 rounded-full flex items-center justify-center text-gray-800 border border-gray-100 shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>

                    <!-- Banner -->
                    <div class="h-56 w-full relative">
                        <img src="${rest.banner}" class="w-full h-full object-cover">
                        <div class="absolute -bottom-10 left-0 right-0 px-5">
                            <div class="bg-white rounded-3xl p-5 border border-gray-100 flex flex-col items-center text-center shadow-sm">
                                <h2 class="text-2xl font-bold mb-1">${rest.name}</h2>
                                <p class="text-xs text-gray-500 mb-2">${rest.category} • ${rest.time}</p>
                                <div class="flex gap-4 text-xs font-medium">
                                    <span class="bg-red-50 text-red-500 px-3 py-1 rounded-full">Envío ${rest.delivery}</span>
                                    <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-full flex items-center gap-1">${rest.rating} <i class="fas fa-star"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-14 px-5">
                        <!-- Menu Items -->
                        <div class="space-y-6 pt-4">
                            ${categories.map(cat => `
                                <div>
                                    <h3 class="font-bold text-lg mb-3 ml-1">${cat}</h3>
                                    <div class="space-y-4">
                                        ${rest.menu.filter(m => m.cat === cat).map(item => `
                                            <div class="flex justify-between items-center bg-white p-2 rounded-2xl border border-gray-100">
                                                <div class="flex-1 pr-4">
                                                    <h4 class="font-medium text-gray-800">${item.name}</h4>
                                                    <p class="text-xs text-gray-400 line-clamp-2 mt-1 mb-2">${item.desc}</p>
                                                    <span class="font-bold text-red-500">${formatCurrency(item.price)}</span>
                                                </div>
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="w-20 h-20 bg-gray-200 rounded-xl overflow-hidden">
                                                        <img src="${rest.image}" class="w-full h-full object-cover opacity-80">
                                                    </div>
                                                    <button onclick="showToast('Agregado a demo')" class="bg-red-100 text-red-600 px-4 py-1.5 rounded-xl text-xs font-bold w-full transition-colors active:bg-red-200">
                                                        Agregar
                                                    </button>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderCheckout() {
            // Static Total Matching the breakdown
            const totalGeneral = 425;

            content.innerHTML = `
                <div class="p-4 pb-24 fade-in">
                    <!-- Header -->
                    <div class="flex items-center gap-4 mb-6">
                        <button onclick="renderCart()" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <h2 class="text-2xl font-bold">Checkout</h2>
                    </div>

                    <!-- Alert for multi-order -->
                    <div class="mb-6 p-4 bg-gray-800 text-white rounded-2xl flex items-center gap-4 shadow-lg">
                        <div class="bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center text-xl">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm">Resumen de transacción</p>
                            <p class="text-xs text-gray-300">Estás pagando por 3 pedidos simultáneos.</p>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="mb-6">
                        <h3 class="font-bold text-lg mb-3">Dirección de entrega</h3>
                        <div class="bg-white p-4 rounded-3xl border border-gray-200 flex items-center gap-4 shadow-sm">
                            <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center text-red-500 text-xl">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">Casa</p>
                                <p class="text-xs text-gray-500">Calle Falsa 123, Ciudad de México</p>
                            </div>
                            <button class="text-red-500 font-bold text-sm">Editar</button>
                        </div>
                         <div class="mt-3">
                             <input type="text" placeholder="Instrucciones para los repartidores..." class="input-flat text-sm bg-white border border-gray-100">
                         </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h3 class="font-bold text-lg mb-3">Método de Pago</h3>

                        <!-- Card Form -->
                        <div class="bg-white p-5 rounded-3xl border border-gray-200 space-y-4 shadow-sm">
                            <div class="flex gap-3 mb-2">
                                <button class="flex-1 bg-red-500 text-white py-2 rounded-xl text-sm font-bold border border-red-500">Tarjeta</button>
                                <button class="flex-1 bg-white text-gray-500 py-2 rounded-xl text-sm font-bold border border-gray-200">Efectivo</button>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Número de Tarjeta</label>
                                <div class="relative">
                                    <input type="text" value="4520 1234 5678 9010" class="input-flat font-mono text-gray-600 bg-gray-50">
                                    <i class="fab fa-cc-visa absolute right-4 top-4 text-gray-400 text-xl"></i>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Fecha Exp.</label>
                                    <input type="text" value="09/28" class="input-flat font-mono text-center text-gray-600 bg-gray-50">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">CVV</label>
                                    <input type="password" value="***" class="input-flat font-mono text-center text-gray-600 bg-gray-50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Break Down Small -->
                    <div class="bg-white rounded-2xl p-4 mb-6 border border-gray-100">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Comida</span>
                            <span>$320.00</span>
                        </div>
                         <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Envíos (x3)</span>
                            <span>$85.00</span>
                        </div>
                         <div class="flex justify-between text-xs text-gray-500 mb-2 border-b border-gray-100 pb-2">
                            <span>Servicio</span>
                            <span>$20.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>Total a pagar</span>
                            <span>${formatCurrency(totalGeneral)}</span>
                        </div>
                    </div>

                    <button class="w-full bg-gradient-to-r from-red-600 to-red-500 text-white font-bold py-4 rounded-2xl flex justify-center items-center gap-2 opacity-90 hover:opacity-100 transition shadow-lg shadow-red-200">
                        <i class="fas fa-lock"></i>
                        <span>Confirmar Pago de 3 Pedidos</span>
                    </button>
                    <p class="text-center text-[10px] text-gray-400 mt-4 pb-4">La transacción es segura y está encriptada.</p>
                </div>
            `;
        }

        // --- INIT ---
        renderHome();

    </script>
</body>
</html>
