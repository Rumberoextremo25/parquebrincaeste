import BannerHero from "@/Components/Hero/BannerHero";
import Layout from "@/Layouts/Layout";
import React from "react";

import ItemList from "./ItemList";
// modal para cancelar los boletos
const Home = ({ eventsFeacture, eventsFree, eventsCarousel }) => {
    console.log(eventsFeacture)
    return (
        <Layout title="Inicio">
            <BannerHero img="/img/home/IMG_9783.jpg"
                title="Reserva tus entradas para la hora que prefieras"
                desc="Emisión de entradas segura y confiable. ¡Su entrada para entretenimiento!"
            />

            <div className="container">

                {/* Sección de Promociones con enlace a otra página */}
                <ItemList title="Promociones" subTitle="" linkPath={route("promotion")} isLink>
                    <div className="flex flex-col items-center bg-violet-100 p-4 rounded-lg shadow-lg">
                        <img
                            src="/img/home/promo-banner.jpg" // Cambia esta ruta a tu imagen
                            alt="Promociones"
                            className="w-full h-auto max-w-lg rounded-lg border-2 border-gray-300 shadow-md transition-transform duration-300 hover:scale-105" // Ajusta la clase según tus necesidades
                        />
                        <p className="mt-2 text-center text-gray-700 font-semibold">
                            No te pierdas nuestras increíbles promociones, ¡aprovecha los mejores precios!
                        </p>
                    </div>
                </ItemList>

                {/* Sección de Brazaletes con lógica de eventos */}
                <ItemList title="Brazaletes" subTitle="" linkPath={route("tienda")}>
                    <div className="flex flex-col items-center bg-violet-100 p-4 rounded-lg shadow-lg">
                        <img
                            src="/img/home/tickets.webp" // Cambia esta ruta a tu imagen
                            alt="Brazaletes"
                            className="w-full h-60 max-w-lg rounded-lg border-2 border-gray-300 shadow-md transition-transform duration-300 hover:scale-105" // Ajusta la clase según tus necesidades
                        />
                        <p className="mt-2 text-center text-gray-700 font-semibold">
                            Descubre nuestra colección única de brazaletes, perfectos para cualquier ocasión.
                        </p>
                    </div>
                </ItemList>

                {/* Sección de Paquetes con enlace a otra página */}
                <ItemList title="Paquetes" linkPath={route("package")} isLink>
                    <div className="flex flex-col items-center bg-violet-100 p-4 rounded-lg shadow-lg">
                        <img
                            src="/img/home/fiestas.jpg" // Cambia esta ruta a tu imagen
                            alt="Paquetes"
                            className="w-full h-60 max-w-lg rounded-lg border-2 border-violet-300 shadow-md transition-transform duration-300 hover:scale-105" // Ajusta la clase según tus necesidades
                        />
                        <p className="mt-2 text-center text-gray-700 font-semibold">
                            Explora nuestros paquetes exclusivos, diseñados para ofrecerte la mejor experiencia.
                        </p>
                    </div>
                </ItemList>
            </div>

        </Layout >
    );
};

export default Home;
