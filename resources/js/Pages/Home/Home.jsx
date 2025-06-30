import BannerHero from "../../Components/Hero/BannerHero"; 
import Layout from "../../Layouts/Layout"; 
import React from "react";
import ItemList from "./ItemList"; 

const Home = ({ eventsFeacture, eventsFree, eventsCarousel }) => {
    // console.log(eventsFeacture) // Comentado para limpiar la consola, puedes descomentarlo para depurar

    return (
        <Layout title="Inicio">
            {/* Componente Hero Banner - Se mantiene su uso ya que su diseño es externo */}
            <BannerHero
                img="/img/home/IMG_9783.jpg"
                title="Reserva tus entradas para la hora que prefieras"
                desc="Emisión de entradas segura y confiable. ¡Su entrada para entretenimiento!"
            />

            <div className="container mx-auto p-4 py-12 sm:py-16"> {/* Contenedor principal con más padding vertical */}
                
                {/* Sección de Promociones */}
                <ItemList title="Promociones" subTitle="" linkPath={route("promotion")} isLink>
                    <a href={route("promotion")} className="block"> {/* Envolvente para hacer toda la tarjeta clickeable */}
                        <div className="relative overflow-hidden rounded-xl shadow-2xl transition-transform duration-500 ease-in-out hover:scale-105 group">
                            {/* Imagen de Fondo con efecto de overlay */}
                            <img
                                src="/img/home/IMG_1618.jpg" // Cambia esta ruta a tu imagen
                                alt="Promociones - Brinca Este"
                                className="w-full h-64 object-cover object-center transition-all duration-500 group-hover:filter group-hover:brightness-75"
                                loading="lazy" // Carga perezosa
                                onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/600x400/FFDDC1/808080?text=Promoción"; }} // Fallback
                            />
                            {/* Contenido superpuesto */}
                            <div className="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-70 group-hover:opacity-80 transition-opacity duration-300"></div>
                            <div className="absolute bottom-0 left-0 p-6 text-white">
                                <h3 className="text-3xl sm:text-4xl font-extrabold mb-2 leading-tight drop-shadow-lg">¡Ofertas Exclusivas!</h3>
                                <p className="text-lg drop-shadow-md">
                                    No te pierdas nuestras increíbles promociones, ¡aprovecha los mejores precios!
                                </p>
                                <button className="mt-4 px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-full shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                                    Ver Promociones
                                </button>
                            </div>
                        </div>
                    </a>
                </ItemList>

                {/* Sección de Brazaletes */}
                <ItemList title="Brazaletes" subTitle="" linkPath={route("tienda")} isLink> {/* Añadido isLink para la tarjeta de brazaletes */}
                    <a href={route("tienda")} className="block"> {/* Envolvente para hacer toda la tarjeta clickeable */}
                        <div className="relative overflow-hidden rounded-xl shadow-2xl transition-transform duration-500 ease-in-out hover:scale-105 group">
                            {/* Imagen de Fondo con efecto de overlay */}
                            <img
                                src="/img/home/IMG_1620.jpg" // Cambia esta ruta a tu imagen
                                alt="Brazaletes - Brinca Este"
                                className="w-full h-64 object-cover object-center transition-all duration-500 group-hover:filter group-hover:brightness-75"
                                loading="lazy" // Carga perezosa
                                onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/600x400/D1FFD1/808080?text=Brazaletes"; }} // Fallback
                            />
                            {/* Contenido superpuesto */}
                            <div className="absolute inset-0 bg-gradient-to-t from-purple-800 via-transparent to-transparent opacity-70 group-hover:opacity-80 transition-opacity duration-300"></div>
                            <div className="absolute bottom-0 left-0 p-6 text-white">
                                <h3 className="text-3xl sm:text-4xl font-extrabold mb-2 leading-tight drop-shadow-lg">Estilo y Diversión</h3>
                                <p className="text-lg drop-shadow-md">
                                    Descubre nuestra colección única de brazaletes, perfectos para cualquier ocasión.
                                </p>
                                <button className="mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                                    Explorar Brazaletes
                                </button>
                            </div>
                        </div>
                    </a>
                </ItemList>

                {/* Sección de Paquetes */}
                <ItemList title="Paquetes" subTitle="" linkPath={route("package")} isLink>
                    <a href={route("package")} className="block"> {/* Envolvente para hacer toda la tarjeta clickeable */}
                        <div className="relative overflow-hidden rounded-xl shadow-2xl transition-transform duration-500 ease-in-out hover:scale-105 group">
                            {/* Imagen de Fondo con efecto de overlay */}
                            <img
                                src="/img/home/IMG_1619.jpg" // Cambia esta ruta a tu imagen
                                alt="Paquetes - Brinca Este"
                                className="w-full h-64 object-cover object-center transition-all duration-500 group-hover:filter group-hover:brightness-75"
                                loading="lazy" // Carga perezosa
                                onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/600x400/D1E9FF/808080?text=Paquetes"; }} // Fallback
                            />
                            {/* Contenido superpuesto */}
                            <div className="absolute inset-0 bg-gradient-to-t from-pink-800 via-transparent to-transparent opacity-70 group-hover:opacity-80 transition-opacity duration-300"></div>
                            <div className="absolute bottom-0 left-0 p-6 text-white">
                                <h3 className="text-3xl sm:text-4xl font-extrabold mb-2 leading-tight drop-shadow-lg">Aventuras Completas</h3>
                                <p className="text-lg drop-shadow-md">
                                    Explora nuestros paquetes exclusivos, diseñados para ofrecerte la mejor experiencia.
                                </p>
                                <button className="mt-4 px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-full shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                                    Ver Paquetes
                                </button>
                            </div>
                        </div>
                    </a>
                </ItemList>

            </div>
        </Layout>
    );
};

export default Home;
